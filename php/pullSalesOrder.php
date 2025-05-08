<?php
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();
$uid = $_SESSION['username'];

$startDate = '5/7/2025'; // Hardcoded or dynamic as needed
$endDate = date('n/j/Y'); // Today’s date in m/d/Y format, e.g., 4/29/2025
//$endDate = '6/30/2025';

$url = "https://sturgeon-still-falcon.ngrok-free.app/sales_orders?start_date=$endDate&end_date=$endDate";

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
        $OrderDate = (isset($rows['DOCDATE']) && !empty($rows['DOCDATE']) && $rows['DOCDATE'] !== '' && $rows['DOCDATE'] !== null) ? DateTime::createFromFormat('d/m/Y', $rows['DOCDATE'])->format('Y-m-d H:i:s') : '';
        $SONumber = (isset($rows['DOCNO']) && !empty($rows['DOCNO']) && $rows['DOCNO'] !== '' && $rows['DOCNO'] !== null) ? trim($rows['DOCNO']) : '';
        $OrderNumber = (isset($rows['DOCNOEX']) && !empty($rows['DOCNOEX']) && $rows['DOCNOEX'] !== '' && $rows['DOCNOEX'] !== null) ? trim($rows['DOCNOEX']) : '';
        $CustomerCode = (isset($rows['CODE']) && !empty($rows['CODE']) && $rows['CODE'] !== '' && $rows['CODE'] !== null) ? trim($rows['CODE']) : '';
        $CustomerName = '';
        if (!empty($CustomerCode)){
            $CustomerName = searchCustomerByCode($CustomerCode, $db);
        }
        // $CustomerName = (isset($rows['COMPANYNAME']) && !empty($rows['COMPANYNAME']) && $rows['COMPANYNAME'] !== '' && $rows['COMPANYNAME'] !== null) ? trim($rows['COMPANYNAME']) : '';
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
        $ProductCode = (isset($rows['ITEMCODE']) && !empty($rows['ITEMCODE']) && $rows['ITEMCODE'] !== '' && $rows['ITEMCODE'] !== null) ? trim($rows['ITEMCODE']) : '';
        // $ProductName = (isset($rows['DESCRIPTION']) && !empty($rows['DESCRIPTION']) && $rows['DESCRIPTION'] !== '' && $rows['DESCRIPTION'] !== null) ? trim($rows['DESCRIPTION']) : '';
        $ProductName = '';
        if (!empty($ProductCode)) {
            $ProductName = searchProductNameByCode($ProductCode, $db);
        }
        $VehNumber = (isset($rows['DESCRIPTION2']) && !empty($rows['DESCRIPTION2']) && $rows['DESCRIPTION2'] !== '' && $rows['DESCRIPTION2'] !== null) ? trim($rows['DESCRIPTION2']) : '';
        $Remarks = !empty($rows['REMARKS']) ? trim($rows['REMARKS']) : '';
        $DestinationName =  (isset($rows['REMARK2']) && !empty($rows['REMARK2']) && $rows['REMARK2'] !== '' && $rows['REMARK2'] !== null) ? trim($rows['REMARK2']) : '';
        $DestinationCode = '';
        if(!empty($DestinationName)){
            $DestinationCode = searchDestinationCodeByName($DestinationName, $db);
        }
        $ExOrQuarry = (isset($rows['DOCREF1']) && !empty($rows['DOCREF1']) && $rows['DOCREF1'] !== '' && $rows['DOCREF1'] !== null) ? trim($rows['DOCREF1']) : '';
        $OrderQuantity = (isset($rows['QTY']) && !empty($rows['QTY']) && $rows['QTY'] !== '' && $rows['QTY'] !== null) ? trim($rows['QTY']) : '';
        $unit = (isset($rows['UOM']) && !empty($rows['UOM']) && $rows['UOM'] !== '' && $rows['UOM'] !== null) ? trim($rows['UOM']) : '';
        if ($unit == 'MT'){
            $OrderQuantity = (float)$OrderQuantity * 1000;
        }
        $PlantCode = (isset($rows['PROJECT']) && !empty($rows['PROJECT']) && $rows['PROJECT'] !== '' && $rows['PROJECT'] !== null) ? trim($rows['PROJECT']) : '';
        $PlantName = '';
        if (!empty($PlantCode)) {
            $PlantName = searchPlantNameByCode($PlantCode, $db);
        }
        $UnitPrice = (isset($rows['UNITPRICE']) && !empty($rows['UNITPRICE']) && $rows['UNITPRICE'] !== '' && $rows['UNITPRICE'] !== null) ? (float) trim($rows['UNITPRICE']) : '';
        $status = 'Open';
        $actionId = 1;

        # Customer Checking & Processing
        if($CustomerCode != null && $CustomerCode != ''){
            $customerQuery = "SELECT * FROM Customer WHERE customer_code = '$CustomerCode'";
            $customerDetail = mysqli_query($db, $customerQuery);
            $customerRow = mysqli_fetch_assoc($customerDetail);
            
            if(empty($customerRow)){
                $errMsg = "Customer: ".$CustomerCode." doesn't exist in master data.";
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

        # Product Checking & Processing
        if($ProductCode != null && $ProductCode != ''){
            $productQuery = "SELECT * FROM Product WHERE product_code = '$ProductCode'";
            $productDetail = mysqli_query($db, $productQuery);
            $productRow = mysqli_fetch_assoc($productDetail);
            
            if(empty($productRow)){
                $errMsg = "Product: ".$ProductCode." doesn't exist in master data.";
                $errorSoProductArray[] = $errMsg;
                continue;
            }
        }

        # Checking for existing Order No.
        if($OrderNumber != null && $OrderNumber != ''){
            $soQuery = "SELECT COUNT(*) AS count FROM Sales_Order WHERE order_no = '$OrderNumber' AND product_code = '$ProductCode' AND deleted = '0'";
            $soDetail = mysqli_query($db, $soQuery);
            $soRow = mysqli_fetch_assoc($soDetail);
            $soCount = (int) $soRow['count'];
            
            if($soCount < 1){
                $TotalPrice = 0;
                if (isset($UnitPrice) && !empty($UnitPrice) && isset($OrderQuantity) && !empty($OrderQuantity)){
                    if ($unit == 'MT'){
                        $orderQtyMt = $OrderQuantity/1000;
                    }
                    $TotalPrice = $UnitPrice * $orderQtyMt;
                }
                
                $system = 'SYSTEM';

                if ($insert_stmt = $db->prepare("INSERT INTO Sales_Order (company_code, company_name, customer_code, customer_name, order_date, order_no, so_no, agent_code, agent_name, destination_code, destination_name, product_code, product_name, plant_code, plant_name, transporter_code, transporter_name, veh_number, exquarry_or_delivered, converted_order_qty, converted_balance, converted_unit, order_quantity, balance, unit_price, total_price, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssssssssssssssssssssssssss', $CompanyCode, $CompanyName, $CustomerCode, $CustomerName, $OrderDate, $OrderNumber, $SONumber, $AgentCode, $AgentName, $DestinationCode, $DestinationName, $ProductCode, $ProductName, $PlantCode, $PlantName, $TransporterCode, $TransporterName, $VehNumber, $ExOrQuarry, $rows['QTY'], $rows['QTY'], $unit, $OrderQuantity, $OrderQuantity, $UnitPrice, $TotalPrice, $Remarks, $status, $system, $system);
                    $insert_stmt->execute();
                    $insert_stmt->close(); 
                }
            }
            else{
                $errMsg = "Sales order for Customer P/O No: ".$OrderNumber." + Product: ".$ProductName." already exist.";
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