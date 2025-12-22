<?php
session_start();
require_once "db_connect.php";
require_once "requires/lookup.php";

if(isset($_POST['plantId'], $_POST['batchDrum'])){
    $plantId = filter_input(INPUT_POST, 'plantId', FILTER_SANITIZE_STRING);
    $batchDrum = filter_input(INPUT_POST, 'batchDrum', FILTER_SANITIZE_STRING);
    $deleted = 0;

    if ($update_stmt = $db->prepare("SELECT * FROM Assets WHERE plant_id=? AND batch_drum=? AND deleted=?")) {
        $update_stmt->bind_param('sss', $plantId, $batchDrum, $deleted);
        
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
                $message[] = [
                    'type' => $row['type'],
                    'name' => $row['name']
                ];
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