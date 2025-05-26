<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $type = '';

    if (isset($_POST['type']) && $_POST['type'] != ''){
        $type = $_POST['type'];
    }

    if ($type == 'pullCustomer'){
        if ($update_stmt = $db->prepare("SELECT * FROM Vehicle WHERE veh_number=? AND status='0'")) {
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
                    $message['veh_number'] = $row['veh_number'];
                    $message['vehicle_weight'] = $row['vehicle_weight'];
                    $message['customer_code'] = $row['customer_code'];
                    $message['customer_name'] = $row['customer_name'];
                    $message['supplier_code'] = $row['supplier_code'];
                    $message['supplier_name'] = $row['supplier_name'];
                    $message['plant'] = $row['plant'];
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));   
            }
        }
    }else{
        if ($update_stmt = $db->prepare("SELECT * FROM Vehicle WHERE id=?")) {
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
                    $message['veh_number'] = $row['veh_number'];
                    $message['vehicle_weight'] = $row['vehicle_weight'];
                    $message['customer_code'] = $row['customer_code'];
                    $message['customer_name'] = $row['customer_name'];
                    $message['supplier_code'] = $row['supplier_code'];
                    $message['supplier_name'] = $row['supplier_name'];
                    $message['plant'] = $row['plant'];
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));   
            }
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