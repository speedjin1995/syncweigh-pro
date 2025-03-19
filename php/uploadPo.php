<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) { 
    $CompanyCode = '';
    $CompanyName = '';

    $companyQuery = "SELECT * FROM Company";
    $companyDetail = mysqli_query($db, $companyQuery);
    $companyRow = mysqli_fetch_assoc($companyDetail);

    if (!empty($companyRow)) {
        $CompanyCode = $companyRow['company_code'];
        $CompanyName = $companyRow['name'];
    }

    foreach ($data as $rows) {
        $OrderDate = (isset($rows['DocDate']) && !empty($rows['DocDate']) && $rows['DocDate'] !== '' && $rows['DocDate'] !== null) ? DateTime::createFromFormat('Y-m-d', excelSerialToDate($rows['DocDate']))->format('Y-m-d H:i:s') : '';
        $PONumber = (isset($rows['DocNo']) && !empty($rows['DocNo']) && $rows['DocNo'] !== '' && $rows['DocNo'] !== null) ? trim($rows['DocNo']) : '';
        $SupplierCode = (isset($rows['Code']) && !empty($rows['Code']) && $rows['Code'] !== '' && $rows['Code'] !== null) ? trim($rows['Code']) : '';
        $SupplierName = (isset($rows['CompanyName']) && !empty($rows['CompanyName']) && $rows['CompanyName'] !== '' && $rows['CompanyName'] !== null) ? trim($rows['CompanyName']) : '';
        $PlantCode = (isset($rows['PROJECT']) && !empty($rows['PROJECT']) && $rows['PROJECT'] !== '' && $rows['PROJECT'] !== null) ? trim($rows['PROJECT']) : '';
        $PlantName = '';
        if (!empty($PlantCode)) {
            $PlantName = searchPlantNameByCode($PlantCode, $db);
        }
        $RawMaterialCode = (isset($rows['ItemCode']) && !empty($rows['ItemCode']) && $rows['ItemCode'] !== '' && $rows['ItemCode'] !== null) ? trim($rows['ItemCode']) : '';
        $RawMaterialName = (isset($rows['DESCRIPTION']) && !empty($rows['DESCRIPTION']) && $rows['DESCRIPTION'] !== '' && $rows['DESCRIPTION'] !== null) ? trim($rows['DESCRIPTION']) : '';
        $SupplierQuantity = (isset($rows['Qty']) && !empty($rows['Qty']) && $rows['Qty'] !== '' && $rows['Qty'] !== null) ? trim($rows['Qty']) : '';
        $unit = (isset($rows['UOM']) && !empty($rows['UOM']) && $rows['UOM'] !== '' && $rows['UOM'] !== null) ? trim($rows['UOM']) : '';
        if ($unit == 'MT'){
            $SupplierQuantity = $SupplierQuantity * 1000;
        }
        $Remarks = !empty($rows['REMARK1']) ? trim($rows['REMARK1']) : '';
        $status = 'Open';
        $actionId = 1;

        # Company Checking & Processing
        // if($CompanyCode != null && $CompanyCode != ''){
        //     $companyQuery = "SELECT * FROM Company WHERE company_code = '$CompanyCode'";
        //     $companyDetail = mysqli_query($db, $companyQuery);
        //     $companyRow = mysqli_fetch_assoc($companyDetail);
            
        //     if(empty($companyRow)){
        //         if($insert_company = $db->prepare("INSERT INTO Company (company_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
        //             $insert_company->bind_param('ssss', $CompanyCode, $CompanyName, $uid, $uid);
        //             $insert_company->execute();
        //             $companyId = $insert_company->insert_id; // Get the inserted company ID
        //             $insert_company->close();
                    
        //             if ($insert_company_log = $db->prepare("INSERT INTO Company_Log (company_id, company_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
        //                 $insert_company_log->bind_param('sssss', $companyId, $CompanyCode, $CompanyName, $actionId, $uid);
        //                 $insert_company_log->execute();
        //                 $insert_company_log->close();
        //             }    
        //         }
        //     }
        // }

        # Supplier Checking & Processing
        if($SupplierCode != null && $SupplierCode != ''){
            $supplierQuery = "SELECT * FROM Supplier WHERE supplier_code = '$SupplierCode'";
            $supplierDetail = mysqli_query($db, $supplierQuery);
            $supplierRow = mysqli_fetch_assoc($supplierDetail);
            
            if(empty($supplierRow)){
                if($insert_supplier = $db->prepare("INSERT INTO Supplier (supplier_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_supplier->bind_param('ssss', $SupplierCode, $SupplierName, $uid, $uid);
                    $insert_supplier->execute();
                    $supplierId = $insert_supplier->insert_id; // Get the inserted supplier ID
                    $insert_supplier->close();
                    
                    if ($insert_supplier_log = $db->prepare("INSERT INTO Supplier_Log (supplier_id, supplier_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_supplier_log->bind_param('sssss', $supplierId, $SupplierCode, $SupplierName, $actionId, $uid);
                        $insert_supplier_log->execute();
                        $insert_supplier_log->close();
                    }    
                }
            }
        }

        # Site Checking & Processing
        // if($SiteCode != null && $SiteCode != ''){
        //     $siteQuery = "SELECT * FROM Site WHERE site_code = '$SiteCode'";
        //     $siteDetail = mysqli_query($db, $siteQuery);
        //     $siteRow = mysqli_fetch_assoc($siteDetail);
            
        //     if(empty($siteRow)){
        //         if($insert_site = $db->prepare("INSERT INTO Site (site_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
        //             $insert_site->bind_param('ssss', $SiteCode, $SiteName, $uid, $uid);
        //             $insert_site->execute();
        //             $siteId = $insert_site->insert_id; // Get the inserted site ID
        //             $insert_site->close();
                    
        //             if ($insert_site_log = $db->prepare("INSERT INTO Site_Log (site_id, site_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
        //                 $insert_site_log->bind_param('sssss', $siteId, $SiteCode, $SiteName, $actionId, $uid);
        //                 $insert_site_log->execute();
        //                 $insert_site_log->close();
        //             }    
        //         }
        //     }
        // }

        # Agent Checking & Processing
        // if($SalesrepCode != null && $SalesrepCode != ''){
        //     $agentQuery = "SELECT * FROM Agents WHERE agent_code = '$SalesrepCode'";
        //     $agentDetail = mysqli_query($db, $agentQuery);
        //     $agentRow = mysqli_fetch_assoc($agentDetail);
            
        //     if(empty($agentRow)){
        //         if($insert_agent = $db->prepare("INSERT INTO Agents (agent_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
        //             $insert_agent->bind_param('ssss', $SalesrepCode, $SalesrepName, $uid, $uid);
        //             $insert_agent->execute();
        //             $agentId = $insert_agent->insert_id; // Get the inserted agent ID
        //             $insert_agent->close();
                    
        //             if ($insert_agent_log = $db->prepare("INSERT INTO Agents_Log (agent_id, agent_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
        //                 $insert_agent_log->bind_param('sssss', $agentId, $SalesrepCode, $SalesrepName, $actionId, $uid);
        //                 $insert_agent_log->execute();
        //                 $insert_agent_log->close();
        //             }    
        //         }
        //     }
        // }
        
        # Destination Checking & Processing
        // if($DestinationCode != null && $DestinationCode != ''){
        //     $destinationQuery = "SELECT * FROM Destination WHERE destination_code = '$DestinationCode'";
        //     $destinationDetail = mysqli_query($db, $destinationQuery);
        //     $destinationRow = mysqli_fetch_assoc($destinationDetail);
            
        //     if(empty($destinationRow)){
        //         if($insert_destination = $db->prepare("INSERT INTO Destination (destination_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
        //             $insert_destination->bind_param('ssss', $DestinationCode, $DestinationName, $uid, $uid);
        //             $insert_destination->execute();
        //             $destinationId = $insert_destination->insert_id; // Get the inserted destination ID
        //             $insert_destination->close();
                    
        //             if ($insert_destination_log = $db->prepare("INSERT INTO Destination_Log (destination_id, destination_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
        //                 $insert_destination_log->bind_param('sssss', $destinationId, $DestinationCode, $DestinationName, $actionId, $uid);
        //                 $insert_destination_log->execute();
        //                 $insert_destination_log->close();
        //             }    
        //         }
        //     }
        // }

        # Plant Checking & Processing
        if($PlantCode != null && $PlantCode != ''){
            $plantQuery = "SELECT * FROM Plant WHERE plant_code = '$PlantCode'";
            $plantDetail = mysqli_query($db, $plantQuery);
            $plantRow = mysqli_fetch_assoc($plantDetail);
            
            if(empty($plantRow)){
                if($insert_plant = $db->prepare("INSERT INTO Plant (plant_code, created_by, modified_by) VALUES (?, ?, ?)")) {
                    $insert_plant->bind_param('sss', $PlantCode, $uid, $uid);
                    $insert_plant->execute();
                    $plantId = $insert_plant->insert_id; // Get the inserted plant ID
                    $insert_plant->close();
                    
                    if ($insert_plant_log = $db->prepare("INSERT INTO Plant_Log (plant_id, plant_code, action_id, action_by) VALUES (?, ?, ?, ?)")) {
                        $insert_plant_log->bind_param('ssss', $plantId, $PlantCode, $actionId, $uid);
                        $insert_plant_log->execute();
                        $insert_plant_log->close();
                    }    
                }
            }
        }

        # Raw Material Checking & Processing
        if($RawMaterialCode != null && $RawMaterialCode != ''){
            $rawMatQuery = "SELECT * FROM Raw_Mat WHERE raw_mat_code = '$RawMaterialCode'";
            $rawMatDetail = mysqli_query($db, $rawMatQuery);
            $rawMatRow = mysqli_fetch_assoc($rawMatDetail);
            
            if(empty($rawMatRow)){
                if($insert_raw_mat = $db->prepare("INSERT INTO Raw_Mat (raw_mat_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_raw_mat->bind_param('ssss', $RawMaterialCode, $RawMaterialName, $uid, $uid);
                    $insert_raw_mat->execute();
                    $rawMatId = $insert_raw_mat->insert_id; // Get the inserted destination ID
                    $insert_raw_mat->close();
                    
                    if ($insert_raw_mat_log = $db->prepare("INSERT INTO Raw_Mat_Log (raw_mat_id, raw_mat_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_raw_mat_log->bind_param('sssss', $rawMatId, $RawMaterialCode, $RawMaterialName, $actionId, $uid);
                        $insert_raw_mat_log->execute();
                        $insert_raw_mat_log->close();
                    }    
                }
            }
        }

        # Checking for existing PO No.
        if($PONumber != null && $PONumber != ''){
            $poQuery = "SELECT * FROM Purchase_Order WHERE po_no = '$PONumber' AND deleted = '0'";
            $poDetail = mysqli_query($db, $poQuery);
            $poRow = mysqli_fetch_assoc($poDetail);

            if(empty($poRow)){
                // if ($insert_stmt = $db->prepare("INSERT INTO Purchase_Order (company_code, company_name, supplier_code, supplier_name, site_code, site_name, order_date, order_no, po_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, raw_mat_code, raw_mat_name, order_load, order_quantity, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                //     $insert_stmt->bind_param('sssssssssssssssssssssss', $CompanyCode, $CompanyName, $SupplierCode, $SupplierName, $SiteCode, $SiteName, $OrderDate, $OrderNumber, $PONumber, $DeliveryDate, $SalesrepCode, $SalesrepName, $DestinationCode, $DestinationName, $DeliverToName, $RawMaterialCode, $RawMaterialName, $SupplierLoad, $SupplierQuantity,$Remarks, $status, $uid, $uid);
                //     $insert_stmt->execute();
                //     $insert_stmt->close(); 
                // }

                if ($insert_stmt = $db->prepare("INSERT INTO Purchase_Order (company_code, company_name, supplier_code, supplier_name, order_date, po_no, raw_mat_code, raw_mat_name, plant_code, plant_name, order_quantity, balance, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssssssssssss', $CompanyCode, $CompanyName, $SupplierCode, $SupplierName, $OrderDate, $PONumber, $RawMaterialCode, $RawMaterialName, $PlantCode, $PlantName, $SupplierQuantity, $SupplierQuantity, $Remarks, $status, $uid, $uid);
                    $insert_stmt->execute();
                    $insert_stmt->close(); 
                }
            }
        }
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
