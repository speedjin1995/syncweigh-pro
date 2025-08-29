<?php
session_start();
require_once 'db_connect.php';

$username = $_SESSION["username"];

if(isset($_POST['id'], $_POST['cancelId'], $_POST['cancelReason'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
	$cancelId = filter_input(INPUT_POST, 'cancelId', FILTER_SANITIZE_STRING);
	$cancelReason = filter_input(INPUT_POST, 'cancelReason', FILTER_SANITIZE_STRING);
	//$del = "1";
	$cancel = "Y";
	$action = "3";

	if ($stmt2 = $db->prepare("UPDATE Weight SET is_complete=?, is_cancel=?, cancel_id=?, cancelled_reason=? WHERE id=?")) {
		$stmt2->bind_param('sssss', $cancel, $cancel, $cancelId, $cancelReason, $id);
		
		if($stmt2->execute()){
			// if ($insert_stmt = $db->prepare("INSERT INTO Supplier_Log (supplier_id, action_id, action_by) VALUES (?, ?, ?)")) {
			// 	$insert_stmt->bind_param('sss', $id, $action, $username);
	
			// 	// Execute the prepared query.
			// 	if (! $insert_stmt->execute()) {
			// 		echo json_encode(
			// 		    array(
			// 		        "status"=> "failed", 
			// 		        "message"=> $insert_stmt->error
			// 		    )
			// 		);
			// 	}
			// 	else{
					// $insert_stmt->close();
					// echo json_encode(
					// 	array(
					// 		"status"=> "success", 
					// 		"message"=> "Deleted"
					// 	)
					// );
			// 	}
			// }

			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Deleted"
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

	$stmt2->close();
	$db->close();
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
