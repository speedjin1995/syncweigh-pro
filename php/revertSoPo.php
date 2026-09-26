<?php
session_start();
require_once 'db_connect.php';

$username = $_SESSION["username"];

if(isset($_POST['userID'], $_POST['type'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	$status = "Open";

	if ($type == 'Sales'){
		if ($check_stmt = $db->prepare("SELECT status, converted_balance FROM Sales_Order WHERE id=? AND deleted='0'")) {
			$check_stmt->bind_param('s', $id);
			$check_stmt->execute();
			$result = $check_stmt->get_result();
			$order = $result->fetch_assoc();
			$check_stmt->close();

			if (!$order) {
				echo json_encode(
					array(
						"status"=> "failed",
						"message"=> "Sales Order not found"
					)
				);
				$db->close();
				exit;
			}

			if (($order['status'] == 'Close' || $order['status'] == 'Closed') && (float)$order['converted_balance'] < -20.0) {
				echo json_encode(
					array(
						"status"=> "failed",
						"message"=> "Balance exceeded threshold -20MT"
					)
				);
				$db->close();
				exit;
			}
		} else {
			echo json_encode(
				array(
					"status"=> "failed",
					"message"=> "Somethings wrong"
				)
			);
			$db->close();
			exit;
		}

		$sql = "UPDATE Sales_Order SET status=?, modified_by=? WHERE id=?";
	}else{
		$sql = "UPDATE Purchase_Order SET status=?, modified_by=? WHERE id=?";
	}

	if ($stmt2 = $db->prepare($sql)) {
		$stmt2->bind_param('sss', $status, $username, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Open"
				)
			);
		} else{
		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> $stmt2->error
    	        )
    	    );
		}
	} 
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Somethings wrong"
	        )
	    );
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>
