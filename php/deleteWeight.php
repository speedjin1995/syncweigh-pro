<?php
require_once 'db_connect.php';

session_start();

$username = $_SESSION["username"];

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$del = "1";
	$cancel = "Y";
	$action = "";
	$type = '';
	$cancelReason = '';

	if(isset($_POST['action']) && $_POST['action']!=null && $_POST['action']!=""){
		$action = $_POST['action'];
	}

	if(isset($_POST['type']) && $_POST['type']!=null && $_POST['type']!=""){
		$type = $_POST['type'];
	}

	if(isset($_POST['cancelReason']) && $_POST['cancelReason']!=null && $_POST['cancelReason']!=""){
		$cancelReason = $_POST['cancelReason'];
	}

	if ($action == 'Cancel'){
		if ($type == 'MULTI'){
			if(is_array($_POST['userID'])){
				$ids = implode(",", $_POST['userID']);
			}else{
				$ids = $_POST['userID'];
			}

			if ($stmt2 = $db->prepare("UPDATE Weight SET is_cancel=?, cancel_reason=? WHERE id IN ($ids)")) {
				$stmt2->bind_param('ss', $cancel, $cancelReason);
				
				if($stmt2->execute()){
		
					$stmt2->close();
					echo json_encode(
						array(
							"status"=> "success", 
							"message"=> "Deleted"
						)
					);
		
					// $stmt2->close();
					$db->close();
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
		}else{
			if ($stmt2 = $db->prepare("UPDATE Weight SET is_cancel=?, cancel_reason=? WHERE id=?")) {
				$stmt2->bind_param('sss', $cancel, $cancelReason, $id);
				
				if($stmt2->execute()){
					$stmt2->close();
					echo json_encode(
						array(
							"status"=> "success", 
							"message"=> "Deleted"
						)
					);
		
					// $stmt2->close();
					$db->close();
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
	}else{
		if ($type == 'MULTI'){
			if(is_array($_POST['userID'])){
				$ids = implode(",", $_POST['userID']);
			}else{
				$ids = $_POST['userID'];
			}

			if ($stmt2 = $db->prepare("UPDATE Weight SET status=? WHERE id IN ($ids)")) {
				$stmt2->bind_param('s', $del);
				
				if($stmt2->execute()){
		
					$stmt2->close();
					echo json_encode(
						array(
							"status"=> "success", 
							"message"=> "Deleted"
						)
					);
		
					// $stmt2->close();
					$db->close();
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
		}else{
			if ($stmt2 = $db->prepare("UPDATE Weight SET status=? WHERE id=?")) {
				$stmt2->bind_param('ss', $del, $id);
				
				if($stmt2->execute()){
					$stmt2->close();
					echo json_encode(
						array(
							"status"=> "success", 
							"message"=> "Deleted"
						)
					);
		
					// $stmt2->close();
					$db->close();
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
