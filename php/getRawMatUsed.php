<?php
session_start();
require_once "db_connect.php";

if(isset($_POST['id'], $_POST['plant'], $_POST['batchDrum'])){
    $id = $_POST['id'];
    $plant = $_POST['plant'];
    $batchDrum = $_POST['batchDrum'];

    if ($update_stmt = $db->prepare("SELECT * FROM Product_RawMat WHERE product_id=? AND plant_id=? AND batch_drum=? AND status='0'")) {
        $update_stmt->bind_param('sss', $id, $plant, $batchDrum);

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
                    'raw_mat_id' => $row['raw_mat_id'],
                    'raw_mat_basic_uom' => $row['raw_mat_basic_uom'],
                    'raw_mat_weight' => $row['raw_mat_weight']
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