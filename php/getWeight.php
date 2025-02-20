<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Weight WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
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
            
            while ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['transaction_id'] = $row['transaction_id'];
                $message['transaction_status'] = $row['transaction_status'];
                $message['weight_type'] = $row['weight_type'];
                $message['transaction_date'] = $row['transaction_date'];
                $message['lorry_plate_no1'] = $row['lorry_plate_no1'];
                $message['lorry_plate_no2'] = $row['lorry_plate_no2'];
                $message['supplier_weight'] = $row['supplier_weight'];
                $message['order_weight'] = $row['order_weight'];
                $message['customer_code'] = $row['customer_code'];
                $message['customer_name'] = $row['customer_name'];
                $message['supplier_code'] = $row['supplier_code'];
                $message['supplier_name'] = $row['supplier_name'];
                $message['product_code'] = $row['product_code'];
                $message['product_name'] = $row['product_name'];
                $message['container_no'] = $row['container_no'];
                $message['invoice_no'] = $row['invoice_no'];
                $message['purchase_order'] = $row['purchase_order'];
                $message['delivery_no'] = $row['delivery_no'];
                $message['transporter_code'] = $row['transporter_code'];
                $message['transporter'] = $row['transporter'];
                $message['destination_code'] = $row['destination_code'];
                $message['destination'] = $row['destination'];
                $message['remarks'] = $row['remarks'];
                $message['gross_weight1'] = $row['gross_weight1'];
                $message['gross_weight1_date'] = $row['gross_weight1_date'];
                $message['tare_weight1'] = $row['tare_weight1'];
                $message['tare_weight1_date'] = $row['tare_weight1_date'];
                $message['nett_weight1'] = $row['nett_weight1'];
                $message['gross_weight2'] = $row['gross_weight2'];
                $message['gross_weight2_date'] = $row['gross_weight2_date'];
                $message['tare_weight2'] = $row['tare_weight2'];
                $message['tare_weight2_date'] = $row['tare_weight2_date'];
                $message['nett_weight2'] = $row['nett_weight2'];
                $message['reduce_weight'] = $row['reduce_weight'];
                $message['final_weight'] = $row['final_weight'];
                $message['weight_different'] = $row['weight_different'];
                $message['is_complete'] = $row['is_complete'];
                $message['is_cancel'] = $row['is_cancel'];
                $message['manual_weight'] = $row['manual_weight'];
                $message['indicator_id'] = $row['indicator_id'];
                $message['weighbridge_id'] = $row['weighbridge_id'];
                $message['created_date'] = $row['created_date'];
                $message['created_by'] = $row['created_by'];
                $message['modified_date'] = $row['modified_date'];
                $message['modified_by'] = $row['modified_by'];
                $message['indicator_id_2'] = $row['indicator_id_2'];
                $message['product_description'] = $row['product_description'];
                $message['sub_total'] = $row['sub_total'];
                $message['sst'] = $row['sst'];
                $message['total_price'] = $row['total_price'];
                $message['final_weight'] = $row['final_weight'];

                if ($update_stmt2 = $db->prepare("SELECT * FROM Vehicle WHERE veh_number=?")) {
                    $update_stmt2->bind_param('s', $row['lorry_plate_no1']);
                    $update_stmt2->execute();
                    $result2 = $update_stmt2->get_result();
                    
                    if ($row2 = $result2->fetch_assoc()) {
                        $message['vehicleNoTxt'] = null; // Replace "123" with the actual value if needed
                    } 
                    else {
                        $message['vehicleNoTxt'] = $row['lorry_plate_no1']; // Debugging line
                    }
                } 
                else {
                    // Log error if the statement couldn't be prepared
                    $message['vehicleNoTxt'] = $db->error;
                }
            
                // Check and retrieve vehicle details for lorry_plate_no2
                if ($update_stmt3 = $db->prepare("SELECT * FROM Vehicle WHERE veh_number=?")) {
                    $update_stmt3->bind_param('s', $row['lorry_plate_no2']);
                    $update_stmt3->execute();
                    $result3 = $update_stmt3->get_result();
                    
                    if ($row3 = $result3->fetch_assoc()) {
                        $message['vehicleNoTxt2'] = null; // Replace "123" with the actual value if needed
                    } 
                    else {
                        $message['vehicleNoTxt2'] = $row['lorry_plate_no2']; // Debugging line
                    }
                } 
                else {
                    // Log error if the statement couldn't be prepared
                    $message['vehicleNoTxt2'] = $db->error;
                }

                // retrieve products
                $empQuery = "SELECT * FROM Weight_Product WHERE weight_id = $id ORDER BY id ASC";
                $empRecords = mysqli_query($db, $empQuery);
                $products = array();
                $productCount = 1;

                while($row4 = mysqli_fetch_assoc($empRecords)) {
                    $products[] = array(
                        "no" => $productCount,
                        "id" => $row4['id'],
                        "product_id" => $row4['product_id'],
                        "order_weight" => $row4['order_weight'],
                        "bin_name" => $row4['bin_name'],
                        "actual_weight" => $row4['actual_weight'],
                        "start_date" => !empty($row4['start_date']) ? DateTime::createFromFormat('Y-m-d H:i:s', $row4['start_date'])->format('d/m/Y H:i') : null,
                        "end_date" => !empty($row4['end_date']) ? DateTime::createFromFormat('Y-m-d H:i:s', $row4['end_date'])->format('d/m/Y H:i') : null,
                        "variance" => $row4['variance']
                    );
                    $productCount++;
                }

                $message['products'] = $products;
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>