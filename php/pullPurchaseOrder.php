<?php
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();
$uid = $_SESSION['username'];

$startDate = '6/1/2025'; // Hardcoded or dynamic as needed
//$endDate = date('n/j/Y'); // Today’s date in m/d/Y format, e.g., 4/29/2025
$endDate = '6/30/2025';

$url = "https://sturgeon-still-falcon.ngrok-free.app/purchase_orders?start_date=$startDate&end_date=$endDate";


$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPGET, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 60);
curl_setopt($curl, CURLOPT_VERBOSE, true);

$response = curl_exec($curl);

if ($response === false) {
    echo json_encode([
        "status" => "failed",
        "message" => curl_error($curl)
    ]);
    exit;
}

curl_close($curl);

// Decode the JSON
$data = json_decode($response, true);

if (!empty($data['data'])) {
    require_once 'db_connect.php';
    $agents = $data['data'];
    
    foreach ($agents as $rows) {
        $OrderDate = (isset($rows['DOCDATE']) && !empty($rows['DOCDATE']) && $rows['DOCDATE'] !== '' && $rows['DOCDATE'] !== null) ? DateTime::createFromFormat('Y-m-d', excelSerialToDate($rows['DOCDATE']))->format('Y-m-d H:i:s') : '';
        $PONumber = (isset($rows['DOCNO']) && !empty($rows['DOCNO']) && $rows['DOCNO'] !== '' && $rows['DOCNO'] !== null) ? trim($rows['DOCNO']) : '';
        $SupplierCode = (isset($rows['CODE']) && !empty($rows['CODE']) && $rows['CODE'] !== '' && $rows['CODE'] !== null) ? trim($rows['CODE']) : '';
        // $SupplierName = (isset($rows['COMPANYNAME']) && !empty($rows['COMPANYNAME']) && $rows['COMPANYNAME'] !== '' && $rows['COMPANYNAME'] !== null) ? trim($rows['COMPANYNAME']) : '';
        $SupplierName = '';
        if (!empty($SupplierCode)) {
            $SupplierName = searchSupplierByCode($SupplierCode, $db);
        }
        $TransporterCode = (isset($rows['SHIPPER']) && !empty($rows['SHIPPER']) && $rows['SHIPPER'] !== '' && $rows['SHIPPER'] !== null) ? trim($rows['SHIPPER']) : '';
        $TransporterName = '';
        if (!empty($TransporterCode)) {
            $TransporterName = searchTransporterNameByCode($TransporterCode, $db);
        }
        $AgentCode = (isset($rows['AGENT']) && !empty($rows['AGENT']) && $rows['AGENT'] !== '' && $rows['AGENT'] !== null) ? trim($rows['AGENT']) : '';
        $AgentName = '';
        if (!empty($AgentCode)) {
            $AgentName = searchAgentNameByCode($AgentCode, $db);
        }
        $RawMaterialCode = (isset($rows['ITEMCODE']) && !empty($rows['ITEMCODE']) && $rows['ITEMCODE'] !== '' && $rows['ITEMCODE'] !== null) ? trim($rows['ITEMCODE']) : '';
        // $RawMaterialName = (isset($rows['DESCRIPTION']) && !empty($rows['DESCRIPTION']) && $rows['DESCRIPTION'] !== '' && $rows['DESCRIPTION'] !== null) ? trim($rows['DESCRIPTION']) : '';
        $RawMaterialName = '';
        if (!empty($RawMaterialCode)) {
            $RawMaterialName = searchRawNameByCode($RawMaterialCode, $db);
        }
        $VehNumber = (isset($rows['DESCRIPTION2']) && !empty($rows['DESCRIPTION2']) && $rows['DESCRIPTION2'] !== '' && $rows['DESCRIPTION2'] !== null) ? trim($rows['DESCRIPTION2']) : '';
        $Remarks = !empty($rows['REMARK1']) ? trim($rows['REMARK1']) : '';
        $DestinationName =  (isset($rows['REMARK2']) && !empty($rows['REMARK2']) && $rows['REMARK2'] !== '' && $rows['REMARK2'] !== null) ? trim($rows['REMARK2']) : '';
        $DestinationCode = '';
        if(!empty($DestinationName)){
            $DestinationCode = searchDestinationCodeByName($DestinationName, $db);
        }
        $ExOrDel = (isset($rows['DOCREF1']) && !empty($rows['DOCREF1']) && $rows['DOCREF1'] !== '' && $rows['DOCREF1'] !== null) ? trim($rows['DOCREF1']) : '';
        $ConvertedSupplierQuantity = (isset($rows['QTY']) && !empty($rows['QTY']) && $rows['QTY'] !== '' && $rows['QTY'] !== null) ? trim($rows['QTY']) : '';
        $ConvertedBalance = (isset($rows['QTY']) && !empty($rows['QTY']) && $rows['QTY'] !== '' && $rows['QTY'] !== null) ? trim($rows['QTY']) : '';
        $ConvertedUnitId = (isset($rows['UOM']) && !empty($rows['UOM']) && $rows['UOM'] !== '' && $rows['UOM'] !== null) ? searchUnitIdByCode(trim($rows['UOM']), $db) : '';
        $PlantCode = (isset($rows['PROJECT']) && !empty($rows['PROJECT']) && $rows['PROJECT'] !== '' && $rows['PROJECT'] !== null) ? trim($rows['PROJECT']) : '';
        $PlantName = '';
        if (!empty($PlantCode)) {
            $PlantName = searchPlantNameByCode($PlantCode, $db);
        }
        $UnitPrice = (isset($rows['UNITPRICE']) && !empty($rows['UNITPRICE']) && $rows['UNITPRICE'] !== '' && $rows['UNITPRICE'] !== null) ? (float) trim($rows['UNITPRICE']) : '';
        $status = 'Open';
        $actionId = 1;

        # Supplier Checking & Processing
        if($SupplierCode != null && $SupplierCode != ''){
            $supplierQuery = "SELECT * FROM Supplier WHERE supplier_code = '$SupplierCode'";
            $supplierDetail = mysqli_query($db, $supplierQuery);
            $supplierRow = mysqli_fetch_assoc($supplierDetail);
            
            if(empty($supplierRow)){
                $errMsg = "Supplier: ".$SupplierCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }

        # Transporter Checking & Processing
        if($TransporterCode != null && $TransporterCode != ''){
            $transporterQuery = "SELECT * FROM Transporter WHERE transporter_code = '$TransporterCode'";
            $transporterDetail = mysqli_query($db, $transporterQuery);
            $transporterSite = mysqli_fetch_assoc($transporterDetail);
            
            if(empty($transporterSite)){
                $errMsg = "Transporter: ".$TransporterCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }

        # Agent Checking & Processing
        if($AgentCode != null && $AgentCode != ''){
            $agentQuery = "SELECT * FROM Agents WHERE agent_code = '$AgentCode'";
            $agentDetail = mysqli_query($db, $agentQuery);
            $agentRow = mysqli_fetch_assoc($agentDetail);
            
            if(empty($agentRow)){
                $errMsg = "Sales Representative: ".$AgentCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }
        
        # Vehicle Checking & Processing
        if($VehNumber != null && $VehNumber != ''){
            $vehQuery = "SELECT * FROM Vehicle WHERE veh_number = '$VehNumber'";
            $vehDetail = mysqli_query($db, $vehQuery);
            $vehRow = mysqli_fetch_assoc($vehDetail);
            
            if(empty($vehRow)){
                $errMsg = "Vehicle: ".$VehNumber." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }
        
        # Destination Checking & Processing
        if($DestinationCode != null && $DestinationCode != ''){
            $destinationQuery = "SELECT * FROM Destination WHERE destination_code = '$DestinationCode'";
            $destinationDetail = mysqli_query($db, $destinationQuery);
            $destinationRow = mysqli_fetch_assoc($destinationDetail);
            
            if(empty($destinationRow)){
                $errMsg = "Destination: ".$DestinationCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }

        # Plant Checking & Processing
        if($PlantCode != null && $PlantCode != ''){
            $plantQuery = "SELECT * FROM Plant WHERE plant_code = '$PlantCode'";
            $plantDetail = mysqli_query($db, $plantQuery);
            $plantRow = mysqli_fetch_assoc($plantDetail);
            
            if(empty($plantRow)){
                $errMsg = "Plant: ".$PlantCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }

        # Raw Material Checking & Processing
        $rawMatId = '';
        if($RawMaterialCode != null && $RawMaterialCode != ''){
            $rawMatQuery = "SELECT * FROM Raw_Mat WHERE raw_mat_code = '$RawMaterialCode'";
            $rawMatDetail = mysqli_query($db, $rawMatQuery);
            $rawMatRow = mysqli_fetch_assoc($rawMatDetail);
            
            if(empty($rawMatRow)){
                $errMsg = "Raw Material: ".$RawMaterialCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
            else{
                $rawMatId = $rawMatRow['id'];
            }
        }

        //Checking to pull rate in raw mat
        $SupplierQuantity = 0;
        if (isset($rawMatId) && !empty($rawMatId)){
            $rawMatUomQuery = "SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id = '$rawMatId' AND unit_id = '2' AND status = '0'";
            $rawMatUomDetail = mysqli_query($db, $rawMatUomQuery);
            $rawMatUomRow = mysqli_fetch_assoc($rawMatUomDetail);

            if (empty($rawMatUomRow)){
                $errMsg = "Raw Material UOM for raw material code: ".$ProductCode." and UOM: KG doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
            else{                
                $SupplierQuantity = $ConvertedSupplierQuantity / $rawMatUomRow['rate'];
            }
        }

        # Checking for existing PO No.
        if($PONumber != null && $PONumber != ''){
            $poQuery = "SELECT COUNT(*) AS count FROM Purchase_Order WHERE po_no = '$PONumber' AND raw_mat_code = '$RawMaterialCode' AND deleted = '0'";
            $poDetail = mysqli_query($db, $poQuery);
            $poRow = mysqli_fetch_assoc($poDetail);
            $poCount = (int) $poRow['count'];
            
            if($poCount < 1){
                $TotalPrice = 0;
                if (isset($UnitPrice) && !empty($UnitPrice) && isset($SupplierQuantity) && !empty($SupplierQuantity)){
                    if ($unit == 'MT'){
                        $supplierQtyMt = $SupplierQuantity/1000;
                    }
                    $TotalPrice = $UnitPrice * $supplierQtyMt;
                }

                $system = 'SYSTEM';

                if ($insert_stmt = $db->prepare("INSERT INTO Purchase_Order (company_code, company_name, supplier_code, supplier_name, order_date, po_no, agent_code, agent_name, destination_code, destination_name, raw_mat_code, raw_mat_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, converted_order_qty, converted_balance, converted_unit, order_quantity, balance, unit_price, total_price, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('sssssssssssssssssssssssssssss', $CompanyCode, $CompanyName, $SupplierCode, $SupplierName, $OrderDate, $PONumber, $AgentCode, $AgentName, $DestinationCode, $DestinationName, $RawMaterialCode, $RawMaterialName, $PlantCode, $PlantName, $TransporterCode, $TransporterName, $VehNumber, $ExOrDel, $ConvertedSupplierQuantity, $ConvertedBalance, $ConvertedUnitId, $SupplierQuantity, $SupplierQuantity, $UnitPrice, $TotalPrice, $Remarks, $status, $system, $system);
                    $insert_stmt->execute();
                    $insert_stmt->close(); 
                }
            }else{
                $errMsg = "Purchase order for P/O No: ".$PONumber." + Raw Material: ".$RawMaterialName." already exist.";
                $errorSoProductArray[] = $errMsg;
            }
        }
    }

    $db->close();

    if (!empty($errorSoProductArray)){
        echo json_encode(
            array(
                "status"=> "error", 
                "message"=> $errorSoProductArray 
            )
        );
    }else{
        echo json_encode(
            array(
                "status"=> "success", 
                "message"=> "Added Successfully!!"
            )
        );
    }
} 
else {
    echo json_encode([
        "status" => "failed",
        "message" => "Invalid data received from API"
    ]);
}
?>