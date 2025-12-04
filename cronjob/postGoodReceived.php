<?php
require_once __DIR__ . '/../php/db_connect2.php';
require_once __DIR__ . '/../php/requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();

$yesterday = new DateTime('yesterday');
$startDate = $yesterday->format('Y-m-d 00:00:00');
$endDate   = $yesterday->format('Y-m-d 23:59:59');

$searchQuery = " and tare_weight1_date >= '".$startDate."'";
$searchQuery .= " and tare_weight1_date <= '".$endDate."'";

$sql = "select * from Weight WHERE transaction_status = 'Purchase' AND is_complete = 'Y' AND  is_cancel <> 'Y' AND synced='N'".$searchQuery;

if ($stmt2 = $db->prepare($sql)){
    if($stmt2->execute()){
        $result = $stmt2->get_result();
        $groupedData = [];

        while ($row = $result->fetch_assoc()) {
            $poNumber = $row["purchase_order"]; // your DB column for PO_NUMBER
            $orderNo = $row['purchase_order'];
            $raw_mat_code = $row['raw_mat_code'];
            $plantCode = $row['plant_code'];
        
            // If this PO_NUMBER is not yet in the grouped array, create it
            if (!isset($groupedData[$poNumber])) {
                $groupedData[$poNumber] = [
                    "PO_NUMBER" => $poNumber,
                    "items"     => []
                ];
            }
            
            $unitPrice = 0;
            $uom = '';
            $qty = '';
            $amt = 0;
            
            if ($select_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no=? AND raw_mat_code=? AND deleted='0'")) {
                $select_stmt->bind_param('ss', $poNumber, $row['raw_mat_code']);
                $select_stmt->execute();
                $result2 = $select_stmt->get_result();
                
                if ($row3 = $result2->fetch_assoc()) { 
                    $uom = searchUnitById($row3['converted_unit'], $db);
                    $rawMatId = searchRawMatIdByCode($row3['raw_mat_code'], $db);
                    $unitPrice = $row3['unit_price'];

                    if ($update_stmt = $db->prepare("SELECT * FROM Raw_Mat_UOM WHERE raw_mat_id=? AND unit_id='2' AND status='0'")) {
                        $update_stmt->bind_param('s', $rawMatId);
                        $update_stmt->execute();
                        $result3 = $update_stmt->get_result();
                        
                        if ($row4 = $result3->fetch_assoc()) {
                            $qty = $row['supplier_weight'] * $row4['rate'];
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
                "DOCNOEX"     => $orderNo,
                "REMARK1"     => $row["delivery_no"] ?? '',
                "QTY"         => $qty,
                "UOM"         => $uom,
                "PROJECT"     => $row['plant_code'],
                "LOCATION"    => $row['plant_code'],
                "UNITPRICE"   => round($unitPrice, 2),
                "AMOUNT"      => round($amt, 2),
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
        $pythonUrl = "https://sturgeon-still-falcon.ngrok-free.app/goods_receive";
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
                        $oldReportMode = mysqli_report(MYSQLI_REPORT_OFF);
                        $alive = ($db && @$db->ping());
                        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                        
                        if (!$alive) {
                            if ($db) { @$db->close(); }
                            require 'db_connect.php';
                        }

                        /*foreach ($poGroup["items"] as $transactionId) {
                            $stmtUpdateWeight = $db->prepare("UPDATE weight SET synced = 'Y' WHERE transaction_id = ?");
                            $stmtUpdateWeight->bind_param('s', $transactionId);
                            $stmtUpdateWeight->execute();
                            $stmtUpdateWeight->close();
                        }*/
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

        $oldReportMode = mysqli_report(MYSQLI_REPORT_OFF);
        $alive = ($db && @$db->ping());
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        if (!$alive) {
            if ($db) { @$db->close(); }
            require 'db_connect.php';
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
else {
    $services = 'PostGoodReceived';
    $requests = json_encode(["Error"=>"Something Wrong"]);

    $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
    $stmtL->bind_param('ss', $services, $requests);
    $stmtL->execute();
    $invid = $stmtL->insert_id;
    $response = json_encode(
        array(
            "status" => "failed",
            "message" => "Invalid data received from API"
        )
    );
    $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
    $stmtU->bind_param('ss', $response, $invid);
    $stmtU->execute();
    $db->close();
}
?>