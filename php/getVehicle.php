<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    # Enhancement to pull Vehicle by veh_number
    if (empty($_POST["type"])) {
        $type = null;
    } else {
        $type = trim($_POST["type"]);
    }

    if (!empty($type) && $type == 'lookup'){
        if ($update_stmt = $db->prepare("SELECT * FROM Vehicle WHERE veh_number=?")) {
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
                    $message['transporter_name'] = $row['transporter_name'];
                    $message['transporter_code'] = $row['transporter_code'];
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
                    $message['transporter_name'] = $row['transporter_name'];
                    $message['transporter_code'] = $row['transporter_code'];
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