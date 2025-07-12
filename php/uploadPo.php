<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) { 
    # Company Details
    $CompanyId = '';
    $CompanyCode = '';
    $CompanyName = '';

    $companyQuery = "SELECT * FROM Company";
    $companyDetail = mysqli_query($db, $companyQuery);
    $companyRow = mysqli_fetch_assoc($companyDetail);

    if (!empty($companyRow)) {
        $CompanyId = $companyRow['id'];
        $CompanyCode = $companyRow['company_code'];
        $CompanyName = $companyRow['name'];
    }

    $errorSoProductArray = [];
    foreach ($data as $rows) {
        // Extract and sanitize input data
        $OrderDate = (isset($rows['DOCDATE']) && trim($rows['DOCDATE']) !== '') ? DateTime::createFromFormat('Y-m-d', excelSerialToDate($rows['DOCDATE']))->format('Y-m-d H:i:s') : '';
        $PONumber = (isset($rows['DOCNO']) && trim($rows['DOCNO']) !== '') ? trim($rows['DOCNO']) : '';
        $SupplierCode = (isset($rows['CODE']) && trim($rows['CODE']) !== '') ? trim($rows['CODE']) : '';
        $TransporterCode = (isset($rows['SHIPPER']) && trim($rows['SHIPPER']) !== '') ? trim($rows['SHIPPER']) : '';
        $AgentCode = (isset($rows['AGENT']) && trim($rows['AGENT']) !== '') ? trim($rows['AGENT']) : '';
        $RawMaterialCode = (isset($rows['ITEMCODE']) && trim($rows['ITEMCODE']) !== '') ? trim($rows['ITEMCODE']) : '';
        $VehNumber = (isset($rows['DESCRIPTION2']) && trim($rows['DESCRIPTION2']) !== '') ? trim($rows['DESCRIPTION2']) : '';
        $Remarks = (isset($rows['DOCREF4']) && trim($rows['DOCREF4']) !== '') ? trim($rows['DOCREF4']) : '';
        $DestinationName = (isset($rows['REMARK2']) && trim($rows['REMARK2']) !== '') ? trim($rows['REMARK2']) : '';
        $ExOrDel = (isset($rows['DOCREF1']) && trim($rows['DOCREF1']) !== '') ? trim($rows['DOCREF1']) : '';
        $ConvertedSupplierQuantity = (isset($rows['QTY']) && trim($rows['QTY']) !== '') ? trim($rows['QTY']) : '';
        $ConvertedBalance = $ConvertedSupplierQuantity;
        $ConvertedUnitId = (isset($rows['UOM']) && trim($rows['UOM']) !== '') ? searchUnitIdByCode(trim($rows['UOM']), $db) : '';
        $PlantCode = (isset($rows['PROJECT']) && trim($rows['PROJECT']) !== '') ? trim($rows['PROJECT']) : '';
        $UnitPrice = (isset($rows['UNITPRICE']) && trim($rows['UNITPRICE']) !== '') ? (float) trim($rows['UNITPRICE']) : '';
        $status = 'Open';
        $actionId = 1;

        // Lookup Supplier
        $SupplierId = '';
        $SupplierName = '';
        if ($SupplierCode !== null && trim($SupplierCode) !== '') {
            $supplierData = searchSupplierByCode($SupplierCode, $db);
            if (empty($supplierData)) {
                $errorSoProductArray[] = "Supplier: $SupplierCode doesn't exist in master data.";
                continue;
            }
            $SupplierId = $supplierData['id'];
            $SupplierName = $supplierData['name'];
        } else {
            $errorSoProductArray[] = "Supplier: $SupplierCode doesn't exist in master data.";
            continue;
        }

        // Lookup Transporter
        $TransporterId = '';
        $TransporterName = '';
        if ($TransporterCode !== null && trim($TransporterCode) !== '') {
            $transporterData = searchTransporterNameByCode($TransporterCode, $db);
            if (empty($transporterData)) {
                $errorSoProductArray[] = "Transporter: $TransporterCode doesn't exist in master data.";
                continue;
            }
            $TransporterId = $transporterData['id'];
            $TransporterName = $transporterData['name'];
        } else {
            $errorSoProductArray[] = "Transporter: $TransporterCode doesn't exist in master data.";
            continue;
        }

        // Lookup Agent
        $AgentId = '';
        $AgentName = '';
        if ($AgentCode !== null && trim($AgentCode) !== '') {
            $agentData = searchAgentNameByCode($AgentCode, $db);
            if (empty($agentData)) {
                $errorSoProductArray[] = "Sales Representative: $AgentCode doesn't exist in master data.";
                continue;
            }
            $AgentId = $agentData['id'];
            $AgentName = $agentData['name'];
        }

        // Vehicle - Insert if missing
        if($VehNumber !== null && $VehNumber !== ''){
            $vehQuery = "SELECT * FROM Vehicle WHERE veh_number = '$VehNumber' AND status = '0'";
            $vehDetail = mysqli_query($db, $vehQuery);
            $vehRow = mysqli_fetch_assoc($vehDetail);
            
            if(empty($vehRow)){
                $vehSupplierCode = NULL;
                $vehSupplierName = NULL;
                $vehTransporterCode = NULL;
                $vehTransporterName = NULL;

                if ($ExOrDel == 'E'){
                    $vehTransporterCode = $TransporterCode;
                    $vehTransporterName = $TransporterName;
                }else{
                    $vehSupplierCode = $SupplierCode;
                    $vehSupplierName = $SupplierName;
                }

                if($insert_veh = $db->prepare("INSERT INTO Vehicle (veh_number, transporter_code, transporter_name, customer_code, customer_name, ex_del, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_veh->bind_param('ssssssss', $VehNumber, $vehTransporterCode, $vehTransporterName, $vehSupplierCode, $vehSupplierName, $ExOrQuarry, $uid, $uid);
                    $insert_veh->execute();
                    $vehId = $insert_veh->insert_id; // Get the inserted vehicle ID
                    $insert_veh->close();
                    
                    if ($insert_veh_log = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, transporter_code, transporter_name, customer_code, customer_name, ex_del, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_veh_log->bind_param('sssssssss', $vehId, $VehNumber, $vehTransporterCode, $vehTransporterName, $vehSupplierCode, $vehSupplierName, $ExOrQuarry, $actionId, $uid);
                        $insert_veh_log->execute();
                        $insert_veh_log->close();
                    }    
                }

                // $errMsg = "Vehicle: ".$VehNumber." doesn't exist in master data.";
                // $errorSoProductArray[] = $errMsg;
                // continue;
            }
        }
        
        // Lookup Destination, Insert if missing
        $DestinationId = '';
        $DestinationCode = '';
        if ($DestinationName !== null && trim($DestinationName) !== '') {
            $destinationData = searchDestinationCodeByName($DestinationName, $db);
            if (empty($destinationData)) {
                // Generate new code
                $code = 'destination';
                $firstChar = strtoupper(substr($DestinationName, 0, 1));
                $update_stmt2 = $db->prepare("SELECT * FROM miscellaneous WHERE code=? AND name=?");
                $update_stmt2->bind_param('ss', $code, $firstChar);
                if ($update_stmt2->execute()) {
                    $result2 = $update_stmt2->get_result();
                    $DestinationCode = $firstChar."-";
                    if ($row2 = $result2->fetch_assoc()) {
                        $charSize = strlen($row2['value']);
                        $misValue = $row2['value'];
                        for($i=0; $i<(5-(int)$charSize); $i++){
                            $DestinationCode.='0';
                        }
                        $DestinationCode .= $misValue;
                        $misValue++;
                    }
                }
                $update_stmt2->close();

                // Insert destination and log
                if($insert_destination = $db->prepare("INSERT INTO Destination (destination_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_destination->bind_param('ssss', $DestinationCode, $DestinationName, $uid, $uid);
                    $insert_destination->execute();
                    $destinationId = $insert_destination->insert_id;
                    $insert_destination->close();
                    if ($insert_destination_log = $db->prepare("INSERT INTO Destination_Log (destination_id, destination_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_destination_log->bind_param('sssss', $destinationId, $DestinationCode, $DestinationName, $actionId, $uid);
                        $insert_destination_log->execute();
                        $insert_destination_log->close();
                    }
                }
            } else {
                $DestinationId = $destinationData['id'];
                $DestinationCode = $destinationData['destination_code'];
            }
        }

        // Lookup Plant
        $PlantId = '';
        $PlantName = '';
        if ($PlantCode !== null && trim($PlantCode) !== '') {
            $plantData = searchPlantNameByCode($PlantCode, $db);
            if (empty($plantData)) {
                $errorSoProductArray[] = "Plant: $PlantCode doesn't exist in master data.";
                continue;
            }
            $PlantId = $plantData['id'];
            $PlantName = $plantData['name'];
        } else {
            $errorSoProductArray[] = "Plant: $PlantCode doesn't exist in master data.";
            continue;
        }

        // Lookup Raw Material
        $RawMaterialId = '';
        $RawMaterialName = '';
        if ($RawMaterialCode !== null && trim($RawMaterialCode) !== '') {
            $rawMatData = searchRawNameByCode($RawMaterialCode, $db);
            if (empty($rawMatData)) {
                $errorSoProductArray[] = "Raw Material: $RawMaterialCode doesn't exist in master data.";
                continue;
            }
            $RawMaterialId = $rawMatData['id'];
            $RawMaterialName = $rawMatData['name'];
        } else {
            $errorSoProductArray[] = "Raw Material: $RawMaterialCode doesn't exist in master data.";
            continue;
        }

        // Raw Material UOM
        $SupplierQuantity = 0;
        if ($RawMaterialId !== null && trim($RawMaterialId) !== '') {
            $rawMatUomQuery = "SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id = '$RawMaterialId' AND unit_id = '2' AND status = '0'";
            $rawMatUomDetail = mysqli_query($db, $rawMatUomQuery);
            $rawMatUomRow = mysqli_fetch_assoc($rawMatUomDetail);
            if (empty($rawMatUomRow)) {
                $errorSoProductArray[] = "Raw Material UOM for raw material code: $RawMaterialCode and UOM: KG doesn't exist in master data.";
                continue;
            }
            $SupplierQuantity = $ConvertedSupplierQuantity / $rawMatUomRow['rate'];
        }

        // PO Duplication check
        if ($PONumber !== null && trim($PONumber) !== '') {
            $poQuery = "SELECT COUNT(*) AS count FROM Purchase_Order WHERE po_no = '$PONumber' AND raw_mat_code = '$RawMaterialCode' AND deleted = '0'";
            $poDetail = mysqli_query($db, $poQuery);
            $poRow = mysqli_fetch_assoc($poDetail);
            $poCount = (int) $poRow['count'];
            if ($poCount < 1) {
                $TotalPrice = 0;
                if (!empty($UnitPrice) && !empty($SupplierQuantity)) {
                    $supplierQtyMt = $SupplierQuantity/1000;
                    $TotalPrice = $UnitPrice * $supplierQtyMt;
                }
                $system = 'SYSTEM';
                if ($insert_stmt = $db->prepare("INSERT INTO Purchase_Order (company_id, company_code, company_name, supplier_id, supplier_code, supplier_name, order_date, po_no, agent_id, agent_code, agent_name, destination_id, destination_code, destination_name, raw_mat_id, raw_mat_code, raw_mat_name, plant_id, plant_code, plant_name, transporter_id, transporter_code, transporter_name, veh_number, exquarry_or_delivered, converted_order_qty, converted_balance, converted_unit,order_quantity, balance, unit_price, total_price, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssssssssssssssssssssssssssssssss', 
                        $CompanyId, $CompanyCode, $CompanyName,
                        $SupplierId, $SupplierCode, $SupplierName,
                        $OrderDate, $PONumber,
                        $AgentId, $AgentCode, $AgentName,
                        $DestinationId, $DestinationCode, $DestinationName,
                        $RawMaterialId, $RawMaterialCode, $RawMaterialName,
                        $PlantId, $PlantCode, $PlantName,
                        $TransporterId, $TransporterCode, $TransporterName,
                        $VehNumber, $ExOrDel,
                        $ConvertedSupplierQuantity, $ConvertedBalance, $ConvertedUnitId,
                        $SupplierQuantity, $SupplierQuantity,
                        $UnitPrice, $TotalPrice,
                        $Remarks, $status, $system, $system
                    );
                    $insert_stmt->execute();
                    $insert_stmt->close();
                }
            } else {
                $errorSoProductArray[] = "Purchase order for P/O No: $PONumber + Raw Material: $RawMaterialName already exist.";
            }
        }
    }

    $db->close();

    if (!empty($errorSoProductArray)){
        echo json_encode([
            "status"=> "error", 
            "message"=> $errorSoProductArray
        ]);
    } else {
        echo json_encode([
            "status"=> "success", 
            "message"=> "Added Successfully!!"
        ]);
    }
} else {
    echo json_encode([
        "status"=> "failed", 
        "message"=> "Please fill in all the fields"
    ]);     
}
?>