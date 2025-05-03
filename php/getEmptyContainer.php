<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM Weight_Container WHERE container_no=? AND status = '0' AND is_complete = 'Y' AND is_cancel = 'N'")) {
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
                $message['container_no'] = $row['container_no'];
                $message['gross_weight1'] = $row['gross_weight1'];
                $message['gross_weight1_date'] = date("d/m/Y h:i:s A", strtotime($row['gross_weight1_date']));
                $message['tare_weight1'] = $row['tare_weight1'];
                $message['tare_weight1_date'] = date("d/m/Y h:i:s A", strtotime($row['tare_weight1_date']));
                $message['lorry_plate_no1'] = $row['lorry_plate_no1'];
                $message['nett_weight1'] = $row['nett_weight1'];
                $message['seal_no'] = $row['seal_no'];

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