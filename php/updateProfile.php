<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$id = $_SESSION['id'];
}

if(isset($_POST['userName'], $_POST['userEmail'])){
	$name = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_STRING);
	$username = filter_input(INPUT_POST, 'userEmail', FILTER_SANITIZE_STRING);

	require_once 'requires/translation.php';
	$language = isset($_POST['language']) ? $_POST['language'] : 'en';
	if (!array_key_exists($language, TRANSLATION_LANGUAGES)) {
		$language = 'en';
	}

	// modified_by/modified_date feed the Users_Log trigger.
	if ($stmtLang = $db->prepare("UPDATE Users SET language=?, modified_by=?, modified_date=NOW() WHERE id=?")) {
		$stmtLang->bind_param('ssi', $language, $_SESSION['username'], $_SESSION['id']);
		if ($stmtLang->execute()) {
			$_SESSION['language'] = $language;
		}
		$stmtLang->close();
	}

	if ($stmt2 = $db->prepare("UPDATE Users SET username=?, useremail=? WHERE employee_code=?")) {
		$stmt2->bind_param('sss', $name, $username, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
			echo '<script type="text/javascript">alert("Your Email / Username is updated successfully!");</script>'; 
			header("location: ../myProfile.php");
		} else{
			echo '<script type="text/javascript">alert("Failed to update profile!");</script>'; 
			header("location: ../myProfile.php");
		}
	} 
	else{
		echo '<script type="text/javascript">alert("Failed to prepare statements!");</script>'; 
		header("location: ../myProfile.php");
	}
} 
else{
	echo '<script type="text/javascript">alert("Please fill in all fields!");</script>'; 
	header("location: ../myProfile.php");
}
?>
