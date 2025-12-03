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
        $records = [];

        while ($row = $result->fetch_assoc()) {
            $orderNo = $row['purchase_order'];
            $productCode = $row['product_code'];
            $plantCode = $row['plant_code'];
            $customerCode = $row['customer_code'];

            $soNo = '';
            $uom = '';
            $qty = 0;
            $amt = 0;
            $unitPrice = 0;

            // Get product ID & basic UOM
            $productId = searchProductIdByCode($productCode, $db);
            $uom = searchProductBasicUomByCode($productCode, $db);

            // Convert weight to UOM-based quantity
            if ($update_stmt = $db->prepare("SELECT * FROM Product_UOM WHERE product_id=? AND unit_id='2' AND status='0'")) {
                $update_stmt->bind_param('s', $productId);
                $update_stmt->execute();
                $result2 = $update_stmt->get_result();
                if ($row4 = $result2->fetch_assoc()) {
                    $qty = $row['nett_weight1'] * $row4['rate'];
                }
                $update_stmt->close();
            }

            // Get unit price and SO if available
            if ($orderNo === '-' || empty($orderNo)) {
                $unitPrice = $row['unit_price'];
            } else {
                if ($select_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no=? AND product_code=? AND customer_code=? AND deleted='0'")) {
                    $select_stmt->bind_param('sss', $orderNo, $productCode, $customerCode);
                    $select_stmt->execute();
                    $result3 = $select_stmt->get_result();
                    if ($row3 = $result3->fetch_assoc()) {
                        $unitPrice = $row3['unit_price'] ?? 0;
                        $soNo = $row3['so_no'];
                    }
                    $select_stmt->close();
                }
            }

            $amt = $qty * $unitPrice;

            $records[] = [
                "DOCREF2"     => $row["transaction_id"],
                "DOCDATE"     => substr($row["tare_weight1_date"], 0, 10),
                "DESCRIPTION2"=> $row["lorry_plate_no1"],
                "CODE"        => $row["customer_code"] ?? "300-C0001", // hardcoded or dynamic if needed
                "COMPANYNAME" => $row["customer_name"],
                "ITEMCODE"    => $productCode,
                "DESCRIPTION" => $row["product_name"],
                "REMARK2"     => $row["destination"] ?? "-",
                "SHIPPER"     => $row["transporter_code"] ?? "T01",
                "DOCREF1"     => ($row["ex_del"] == 'EX' ? 'E' : 'D'),
                "DOCNOEX"     => $orderNo,
                "REMARK1"     => $row["delivery_no"],
                "QTY"         => round($qty, 3),
                "UOM"         => $uom,
                "PROJECT"     => $row['plant_code'],
                "LOCATION"    => $row['plant_code'],
                "UNITPRICE"   => round($unitPrice, 2),
                "AMOUNT"      => round($amt, 2),
                "SO_NUMBER"   => $soNo
            ];
        }

        $stmt2->close();
        
        if(isset($records) && count($records)>0){
            $services = 'PostGoodReceived';
            $requests = json_encode($records);
            
            // Insert request into Api_Log
            $stmtL = $db->prepare("INSERT INTO Api_Log (services, request) VALUES (?, ?)");
            $stmtL->bind_param('ss', $services, $requests);
            $stmtL->execute();
            $logId = $stmtL->insert_id;
            
            // Send to API
            $url = "https://sturgeon-still-falcon.ngrok-free.app/delivery_order";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($records));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            
            // Decode API response (JSON string to array)
            $responseData = json_decode($response, true);
            
            // Prepare loggable response JSON
            if ($httpCode === 200 && isset($responseData["status"]) && $responseData["status"] === "success") {
                // Loop through each result item
                foreach ($responseData["results"] as $item) {
                    if (isset($item["status"]) && $item["status"] === "success") {
                        $docref2 = $item["docref2"];
                        
                        $oldReportMode = mysqli_report(MYSQLI_REPORT_OFF);
                        $alive = ($db && @$db->ping());
                        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                        
                        if (!$alive) {
                            if ($db) { @$db->close(); }
                            require 'db_connect.php';
                        }
                    }
                }
                
                $responseToLog = json_encode([
                    "status" => "success", 
                    "message" => "Post Successfully",
                    "posted" => $responseData["results"]
                ]);
            } 
            else {
                $responseToLog = json_encode([
                    "status" => "failed",
                    "message" => $responseData["message"] ?? 'Failed to insert',
                ]);
            }
            
            $oldReportMode = mysqli_report(MYSQLI_REPORT_OFF);
            $alive = ($db && @$db->ping());
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            if (!$alive) {
                if ($db) { @$db->close(); }
                require 'db_connect.php';
            }
        }
        else{
            $responseToLog = json_encode([
                "status" => "failed", 
                "message" => "No record founds"
            ]);
        }
        
        // Update the same Api_Log record with the response
        $stmtU = $db->prepare("UPDATE Api_Log SET response = ? WHERE id = ?");
        $stmtU->bind_param('ss', $responseToLog, $logId);
        $stmtU->execute();
        $stmtU->close();
        $db->close();
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