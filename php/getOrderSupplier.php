<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['code'], $_POST['type'])){
	$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

    $final_weight = [];
    $customerName = '';
    $productName = '';
    $order_supplier_weight = 0;
    $previousRecordsTag = true;
    $count = 1;

    $empQuery = "SELECT * FROM Weight WHERE status = '0' AND purchase_order = '$code' AND transaction_status = '$type' ORDER BY id ASC"; 
    $empRecords = mysqli_query($db, $empQuery);
    if (mysqli_num_rows($empRecords) > 0) { // Check if records exist
        while ($row = mysqli_fetch_assoc($empRecords)) {
            if ($count == 1) {
                $customerName = $row['customer_name'];
                $productName = $row['product_name'];
                if ($type == 'Purchase') {
                    $order_supplier_weight = $row['supplier_weight'] ?? 0;
                } else {
                    $order_supplier_weight = $row['order_weight'] ?? 0;
                }
            }
            $final_weight[] = !empty($row['final_weight']) ? $row['final_weight'] : 0;
        }
    } else {
        // No records found
        $previousRecordsTag = false;
    }

    // prevRecordTag
    $finalWeight = array_sum($final_weight);
    $message['customer_name'] = $customerName;
    $message['product_name'] = $productName;
    $message['order_supplier_weight'] = $order_supplier_weight;
    $message['final_weight'] = $finalWeight;
    $message['previousRecordsTag'] = $previousRecordsTag;

    echo json_encode(
        array(
            "status" => "success",
            "message" => $message
        )
    );
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>