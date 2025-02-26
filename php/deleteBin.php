<?php
require_once 'db_connect.php';

session_start();

$username = $_SESSION["username"];

if(isset($_POST['userID'], $_POST['code'], $_POST['name'], $_POST['desc'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$desc = filter_input(INPUT_POST, 'desc', FILTER_SANITIZE_STRING);
	$del = "1";
	$action = "3";

	
	if ($stmt2 = $db->prepare("UPDATE Bin SET status=? WHERE id=?")) {
		$stmt2->bind_param('ss', $del , $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();

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
