<?php
session_start();
require_once "db_connect.php";

if (isset($_POST['userID'], $_POST['type'], $_POST['plant'], $_POST['prodRawMatCode'])) {
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $plant = filter_input(INPUT_POST, 'plant', FILTER_SANITIZE_STRING);
    $prodRawMatCode = filter_input(INPUT_POST, 'prodRawMatCode', FILTER_SANITIZE_STRING);
    $convertedOrderQty = filter_input(INPUT_POST, 'convertedOrderQty', FILTER_SANITIZE_STRING);
    $unitId = filter_input(INPUT_POST, 'unitId', FILTER_SANITIZE_STRING);
    $productId = filter_input(INPUT_POST, 'productId', FILTER_SANITIZE_STRING);
    $currentBalance = 0;
    $status = '0';
    $unit = '2';

    if ($type == 'SO') {
        /*
         * ============================================================
         * 1. GET CONVERSION RATE TO KG
         * ============================================================
         */
        $rate = 1;

        if ($conversion_stmt = $db->prepare("SELECT rate FROM Product_UOM WHERE product_id = ? AND unit_id = ? AND status = ? LIMIT 1 ")) {
            $conversion_stmt->bind_param('sss', $productId, $unit, $status);

            if ($conversion_stmt->execute()) {
                $conversion_result = $conversion_stmt->get_result();

                if ($conversion_result->num_rows > 0) {
                    $conversionRow = $conversion_result->fetch_assoc();
                    $rate = (float)$conversionRow['rate'];
                }
            }

            $conversion_stmt->close();
        }

        /*
         * ============================================================
         * 2. CONVERT ORDER QUANTITY TO KG
         * ============================================================
         */
        $orderQty = (float)$convertedOrderQty;
        $orderQtyKg = $orderQty / $rate;

        /*
         * ============================================================
         * 3. SUM ALL WEIGHED WEIGHT
         * ============================================================
         */
        $totalWeightKg = 0;

        if ($weighing_stmt = $db->prepare("SELECT COALESCE(SUM(nett_weight1), 0) AS total_weight FROM Weight
            WHERE purchase_order = ? AND product_code = ? AND status = '0' AND is_complete = 'Y' AND is_cancel = 'N'")) {

            $weighing_stmt->bind_param('ss', $id, $prodRawMatCode);

            if ($weighing_stmt->execute()) {
                $weighing_result = $weighing_stmt->get_result();

                if ($weighing_row = $weighing_result->fetch_assoc()) {
                    $totalWeightKg = (float)$weighing_row['total_weight'];
                }
            }

            $weighing_stmt->close();
        }


        /*
         * ============================================================
         * 4. CALCULATE REMAINING BALANCE IN KG
         * ============================================================
         */
        $currentBalance = $orderQtyKg - $totalWeightKg;

        /*
         * ============================================================
         * 5. CONVERT BALANCE BACK TO ORIGINAL UOM
         * ============================================================
         */
        $convertedBalance = 0;

        if ($rate > 0) {
            $convertedBalance = $currentBalance * $rate;
        }

        /*
         * ============================================================
         * 6. RETURN RESULT
         * ============================================================
         */
        $message = array(
            'balance' => $currentBalance,
            'converted_balance' => $convertedBalance,
            'order_quantity' => $orderQtyKg,
            'converted_order_qty' => $orderQty,
            'total_weight' => $totalWeightKg,
            'rate' => $rate
        );


        echo json_encode(
            array(
                "status" => "success",
                "message" => $message
            )
        );

    } 
    else if($type == 'PO') {
        if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order_Log WHERE po_no=? AND plant_code=? AND raw_mat_code=?  ORDER BY id DESC LIMIT 1")) {
            $update_stmt->bind_param('sss', $id, $plant, $prodRawMatCode);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong"
                    )); 
            }
            else{
                $result = $update_stmt->get_result();
                $message = array();
                
                if ($result->num_rows > 0){
                    $row = $result->fetch_assoc();
                    $message['id'] = $row['id'];
                    $message['balance'] = $row['balance'];
                    $message['converted_balance'] = $row['converted_balance'];
                    $message['order_quantity'] = $row['order_quantity'];
                    $message['converted_order_qty'] = $row['converted_order_qty'];
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));   
            }
        }
    }
    else {

        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Invalid type"
            )
        );
    }

} else {

    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
        )
    );
}
?>