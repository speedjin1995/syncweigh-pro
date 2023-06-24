<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$id = $_SESSION['id'];
}

if(isset($_POST['oldPassword'], $_POST['newPassword'], $_POST['confirmPassword'])){
	$oldPassword = filter_input(INPUT_POST, 'oldPassword', FILTER_SANITIZE_STRING);
	$newPassword = filter_input(INPUT_POST, 'newPassword', FILTER_SANITIZE_STRING);
	$confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING);
	
	$stmt = $db->prepare("SELECT * from Users where employee_code = ?");
	$stmt->bind_param('s', $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if(($row = $result->fetch_assoc()) !== null){
		if (password_verify($oldPassword, $row['password'])){
			$param_password = password_hash($newPassword, PASSWORD_DEFAULT); // Creates a password hash
			$stmt2 = $db->prepare("UPDATE Users SET password = ? WHERE employee_code = ?");
			$stmt2->bind_param('ss', $param_password, $id);
			
			if($stmt2->execute()){
    			$stmt2->close();
    			$db->close();

				echo '<script type="text/javascript">alert("Update successfully!");</script>'; 
				header("location: ../ChangePassword.php");
    		} else{
    		    echo '<script type="text/javascript">alert("FAiled to update due to "'.$stmt2->error.');</script>'; 
				header("location: ../ChangePassword.php");
    		}
		} else{
		    echo '<script type="text/javascript">alert("Old password is not matched");</script>'; 
			header("location: ../ChangePassword.php");
		}
	} else{
	    echo '<script type="text/javascript">alert("Data retrieve failed");</script>'; 
		header("location: ../ChangePassword.php");
	}
} else{
    echo '<script type="text/javascript">alert("Please fill in all the fields");</script>'; 
	header("location: ../ChangePassword.php");
}
?>
