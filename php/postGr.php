<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
$config = include(dirname(__DIR__, 2) . '/sql_config.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['username'];
$companyKey = $_SESSION['company'] ?? null;
$type = '';

if($_POST['type'] != null && $_POST['type'] != ''){
    $type = $_POST['type'];
}

## Search 
$searchQuery = "";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d H:i:00');
  $searchQuery = " and tare_weight1_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d H:i:59');
	$searchQuery .= " and tare_weight1_date <= '".$toDateTime."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and transaction_status = '".$_POST['status']."'";
}

if($_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
	$searchQuery .= " and supplier_code = '".$_POST['supplier']."'";
}

if($_POST['rawMat'] != null && $_POST['rawMat'] != '' && $_POST['rawMat'] != '-'){
	$searchQuery .= " and raw_mat_code = '".$_POST['rawMat']."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_POST['plant']."'";
}

if($_POST['purchaseOrder'] != null && $_POST['purchaseOrder'] != '' && $_POST['purchaseOrder'] != '-'){
	$searchQuery .= " and purchase_order = '".$_POST['purchaseOrder']."'";
}

if (!$companyKey || !isset($config[$companyKey])) {
    echo json_encode([
        "status" => "failed",
        "message" => "Invalid company session"
    ]);
    exit;
}

if ($type == "MULTI"){
    if(is_array($_POST['userID'])){
        $ids = implode(",", $_POST['userID']);
    }else{
        $ids = $_POST['userID'];
    }

    if ($stmt2 = $db->prepare("SELECT * FROM Weight WHERE id IN ($ids)")) {
        if($stmt2->execute()){
            $result = $stmt2->get_result();

            while ($row = $result->fetch_assoc()) {
                $poNumber = $row["purchase_order"]; // your DB column for PO_NUMBER
            
                // If this PO_NUMBER is not yet in the grouped array, create it
                if (!isset($groupedData[$poNumber])) {
                    $groupedData[$poNumber] = [
                        "PO_NUMBER" => $poNumber,
                        "items"     => []
                    ];
                }
                
                $uom = '';
                $qty = '';
                $amt = '';
                
                if ($select_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND raw_mat_code=? AND deleted='0'")) {
                    $select_stmt->bind_param('ss', $poNumber, $row2['raw_mat_code']);
                    $select_stmt->execute();
                    $result = $select_stmt->get_result();
                    if ($row3 = $result->fetch_assoc()) { 
                        $uom = searchUnitById($row3['converted_unit'], $db);
                        $rawMatId = searchRawMatIdByCode($row3['raw_mat_code'], $db);
                        $unitPrice = $row3['unit_price'];

                        if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id='2' AND status='0'")) {
                            $update_stmt->bind_param('s', $rawMatId);
                            $update_stmt->execute();
                            $result2 = $update_stmt->get_result();
                            if ($row4 = $result2->fetch_assoc()) {
                                $qty = $row2['nett_weight1'] * $row4['rate'];
                                $amt = $qty * $unitPrice;
                            }
                            $update_stmt->close();
                        }
                    }
                    $select_stmt->close();
                }
            
                // Add item to this PO_NUMBER's items
                $groupedData[$poNumber]["items"][] = [
                    "DOCREF2"     => $row["transaction_id"],
                    "DOCDATE"     => substr($row["tare_weight1_date"], 0, 10),
                    "DESCRIPTION2"=> $row["lorry_plate_no1"],
                    "CODE"        => $row["supplier_code"] ?? "300-C0001", // hardcoded or dynamic if needed
                    "COMPANYNAME" => $row["supplier_name"],
                    "ITEMCODE"    => $row["raw_mat_code"],
                    "DESCRIPTION" => $row["raw_mat_name"],
                    "REMARK2"     => $row["destination"],
                    "SHIPPER"     => $row["transporter_code"] ?? "T01",
                    "DOCREF1"     => ($row["ex_del"] == 'EX' ? 'E' : 'D'),
                    "DOCNOEX"     => "-",
                    "REMARK1"     => $row["delivery_no"],
                    "QTY"         => $qty,
                    "UOM"         => $uom,
                    "PROJECT"     => $row['plant_code'],
                    "LOCATION"    => $row['plant_code'],
                    //"UNITPRICE"   => round($unitPrice, 2),
                    //"AMOUNT"      => round($amt, 2),
                    "PO_NUMBER"   => $poNumber
                ];
            }
            
            $stmt2->close();
            
            // Convert associative grouping to indexed array
            $finalData = array_values($groupedData);
            
            // JSON encode
            $services = 'PostGoodReceived';
            $jsonPayload = json_encode($finalData, JSON_UNESCAPED_UNICODE);
            
            // Insert request into Api_Log
            $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
            $stmtL->bind_param('ss', $services, $jsonPayload);
            $stmtL->execute();
            $logId = $stmtL->insert_id;
            
            // POST to Python
            $pythonUrl = rtrim($config[$companyKey], '/') . "/goods_receive";
            $ch = curl_init($pythonUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            
            // Decode API response (JSON string to array)
            $apiResponse = json_decode($response, true);
            
            // Prepare loggable response JSON
            if ($httpCode === 200 && isset($apiResponse["status"]) && $apiResponse["status"] === "success") {
                foreach ($apiResponse["results"] as $poGroup) {
                    if (isset($poGroup["status"]) && $poGroup["status"] === "success") {
                        if (!empty($poGroup["items"]) && is_array($poGroup["items"])) {
                            foreach ($poGroup["items"] as $transactionId) {
                                $stmtUpdateWeight = $db->prepare("UPDATE weight SET synced = 'Y' WHERE transaction_id = ?");
                                $stmtUpdateWeight->bind_param('s', $transactionId);
                                $stmtUpdateWeight->execute();
                                $stmtUpdateWeight->close();
                            }
                        }
                    }
                }
            
                $responseToLog = json_encode([
                    "status" => "success",
                    "message" => "Post Successfully",
                    "posted" => $apiResponse["results"]
                ]);
            } else {
                $responseToLog = json_encode([
                    "status" => "failed",
                    "http_code" => $httpCode,
                    "error" => $err,
                    "response" => $response
                ]);
            }

            
            // Update the same Api_Log record with the response
            $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
            $stmtU->bind_param('ss', $responseToLog, $logId);
            $stmtU->execute();
            $stmtU->close();

            
            $db->close();
            echo $responseToLog;
        } else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $stmt2->error
                )
            );
        }
    } 
    else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Something's wrong"
            )
        );
    }
}else{
    $sql = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery." group by purchase_order";
    if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
        $username = implode("', '", $_SESSION["plant"]);
        $sql = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' and plant_code IN ('$username')".$searchQuery." group by purchase_order";
    }

    if ($stmt2 = $db->prepare($sql)){
        if($stmt2->execute()){
            $result = $stmt2->get_result();
            $groupedData = [];

            while ($row = $result->fetch_assoc()) {
                $poNumber = $row["purchase_order"]; // your DB column for PO_NUMBER
            
                // If this PO_NUMBER is not yet in the grouped array, create it
                if (!isset($groupedData[$poNumber])) {
                    $groupedData[$poNumber] = [
                        "PO_NUMBER" => $poNumber,
                        "items"     => []
                    ];
                }
                
                $uom = '';
                $qty = '';
                $amt = '';
                
                if ($select_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND raw_mat_code=? AND deleted='0'")) {
                    $select_stmt->bind_param('ss', $poNumber, $row2['raw_mat_code']);
                    $select_stmt->execute();
                    $result = $select_stmt->get_result();
                    if ($row3 = $result->fetch_assoc()) { 
                        $uom = searchUnitById($row3['converted_unit'], $db);
                        $rawMatId = searchRawMatIdByCode($row3['raw_mat_code'], $db);
                        $unitPrice = $row3['unit_price'];

                        if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id='2' AND status='0'")) {
                            $update_stmt->bind_param('s', $rawMatId);
                            $update_stmt->execute();
                            $result2 = $update_stmt->get_result();
                            if ($row4 = $result2->fetch_assoc()) {
                                $qty = $row2['nett_weight1'] * $row4['rate'];
                                $amt = $qty * $unitPrice;
                            }
                            $update_stmt->close();
                        }
                    }
                    $select_stmt->close();
                }
            
                // Add item to this PO_NUMBER's items
                $groupedData[$poNumber]["items"][] = [
                    "DOCREF2"     => $row["transaction_id"],
                    "DOCDATE"     => substr($row["tare_weight1_date"], 0, 10),
                    "DESCRIPTION2"=> $row["lorry_plate_no1"],
                    "CODE"        => $row["supplier_code"] ?? "300-C0001", // hardcoded or dynamic if needed
                    "COMPANYNAME" => $row["supplier_name"],
                    "ITEMCODE"    => $row["raw_mat_code"],
                    "DESCRIPTION" => $row["raw_mat_name"],
                    "REMARK2"     => $row["destination"],
                    "SHIPPER"     => $row["transporter_code"] ?? "T01",
                    "DOCREF1"     => ($row["ex_del"] == 'EX' ? 'E' : 'D'),
                    "DOCNOEX"     => "-",
                    "REMARK1"     => $row["delivery_no"],
                    "QTY"         => $qty,
                    "UOM"         => $uom,
                    "PROJECT"     => $row['plant_code'],
                    "LOCATION"    => $row['plant_code'],
                    //"UNITPRICE"   => round($unitPrice, 2),
                    //"AMOUNT"      => round($amt, 2),
                    "PO_NUMBER"   => $poNumber
                ];
            }
            
            $stmt2->close();
            
            // Convert associative grouping to indexed array
            $finalData = array_values($groupedData);
            
            // JSON encode
            $services = 'PostGoodReceived';
            $jsonPayload = json_encode($finalData, JSON_UNESCAPED_UNICODE);
            
            // Insert request into Api_Log
            $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
            $stmtL->bind_param('ss', $services, $jsonPayload);
            $stmtL->execute();
            $logId = $stmtL->insert_id;
            
            // POST to Python
            $pythonUrl = rtrim($config[$companyKey], '/') . "/goods_receive";
            $ch = curl_init($pythonUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            
            // Decode API response (JSON string to array)
            $apiResponse = json_decode($response, true);
            
            // Prepare loggable response JSON
            if ($httpCode === 200 && isset($apiResponse["status"]) && $apiResponse["status"] === "success") {
                foreach ($apiResponse["results"] as $poGroup) {
                    if (isset($poGroup["status"]) && $poGroup["status"] === "success") {
                        if (!empty($poGroup["items"]) && is_array($poGroup["items"])) {
                            foreach ($poGroup["items"] as $transactionId) {
                                $stmtUpdateWeight = $db->prepare("UPDATE weight SET synced = 'Y' WHERE transaction_id = ?");
                                $stmtUpdateWeight->bind_param('s', $transactionId);
                                $stmtUpdateWeight->execute();
                                $stmtUpdateWeight->close();
                            }
                        }
                    }
                }
            
                $responseToLog = json_encode([
                    "status" => "success",
                    "message" => "Post Successfully",
                    "posted" => $apiResponse["results"]
                ]);
            } else {
                $responseToLog = json_encode([
                    "status" => "failed",
                    "http_code" => $httpCode,
                    "error" => $err,
                    "response" => $response
                ]);
            }

            
            // Update the same Api_Log record with the response
            $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
            $stmtU->bind_param('ss', $responseToLog, $logId);
            $stmtU->execute();
            $stmtU->close();

            
            $db->close();
            echo $responseToLog;
        } else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $stmt2->error
                )
            );
        }
    }
    else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Something's wrong"
            )
        );
    }
}
?>
