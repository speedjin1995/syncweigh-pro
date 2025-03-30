<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Sales_Order WHERE id=?")) {
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
                $message['company_code'] = $row['company_code'];
                $message['customer_code'] = $row['customer_code'];
                $message['site_code'] = $row['site_code'];
                $message['order_date'] = $row['order_date'];
                $message['order_no'] = $row['order_no'];
                $message['so_no'] = $row['so_no'];
                $message['agent_code'] = $row['agent_code'];
                $message['destination_code'] = $row['destination_code'];
                $message['product_code'] = $row['product_code'];
                $message['plant_code'] = $row['plant_code'];
                $message['transporter_code'] = $row['transporter_code'];
                $message['veh_number'] = $row['veh_number'];
                $message['exquarry_or_delivered'] = $row['exquarry_or_delivered'];
                $message['order_quantity'] = $row['order_quantity'];
                $message['remarks'] = $row['remarks'];
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