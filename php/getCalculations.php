<?php
session_start();
require_once "db_connect.php";
require_once "requires/lookup.php";

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Calculations WHERE id=?")) {
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
                $message['type'] = $row['type'];
                $message['plant_id'] = $row['plant_id'];
                $message['batch_drum'] = $row['batch_drum'];
            } 

            // retrieve products
            $empQuery = "SELECT * FROM Calculation_Value WHERE calculation_id = $id AND deleted = '0' ORDER BY id ASC";
            $empRecords = mysqli_query($db, $empQuery);
            $values = array();
            $count = 0;

            while($row2 = mysqli_fetch_assoc($empRecords)) {
                $values[] = array(
                    "no" => $count,
                    "id" => $row2['id'],
                    "calculation_id" => $row2['calculation_id'],
                    "level" => $row2['level'],
                    "volume" => $row2['volume'],
                    "temperature" => $row2['temperature'],
                    "sg" => $row2['sg'],
                );
                
                $count++;
            }

            $message['values'] = $values;

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