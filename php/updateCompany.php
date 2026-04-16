<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}

if(isset($_POST['companyRegNo'], $_POST['companyName'], $_POST['companyAddress'], $_POST['companyPhone'])){
	$companyRegNo = filter_input(INPUT_POST, 'companyRegNo', FILTER_SANITIZE_STRING);
	$companyName = filter_input(INPUT_POST, 'companyName', FILTER_SANITIZE_STRING);
	$companyAddress = filter_input(INPUT_POST, 'companyAddress', FILTER_SANITIZE_STRING);
	$companyPhone = filter_input(INPUT_POST, 'companyPhone', FILTER_SANITIZE_STRING);
	$companyAddress2 = null;
	$companyAddress3 = null;
	$companyFax = null;
	$sopLink = '';
	$hardwareSetupLink = '';
	$helpLink = '';
	$today = date("Y-m-d H:i:s");
	$id = '1';
	$action = '2';

	if($_POST['companyAddress2'] != null && $_POST['companyAddress2'] != ""){
		$companyAddress2 = filter_input(INPUT_POST, 'companyAddress2', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['companyAddress3'] != null && $_POST['companyAddress3'] != ""){
		$companyAddress3 = filter_input(INPUT_POST, 'companyAddress3', FILTER_SANITIZE_STRING);
	}

	if($_POST['companyFax'] != null && $_POST['companyFax'] != ""){
		$companyFax = filter_input(INPUT_POST, 'companyFax', FILTER_SANITIZE_STRING);
	}

	if($_POST['sopLink'] != null && $_POST['sopLink'] != ""){
		$sopLink = filter_input(INPUT_POST, 'sopLink', FILTER_SANITIZE_URL);
	}
	
	if($_POST['hardwareSetupLink'] != null && $_POST['hardwareSetupLink'] != ""){
		$hardwareSetupLink = filter_input(INPUT_POST, 'hardwareSetupLink', FILTER_SANITIZE_URL);
	}
	
	if($_POST['helpLink'] != null && $_POST['helpLink'] != ""){
		$helpLink = filter_input(INPUT_POST, 'helpLink', FILTER_SANITIZE_URL);
	}
	
	$linksArray = array(
		'sop_link' => $sopLink,
		'hardware_setup_link' => $hardwareSetupLink,
		'help_link' => $helpLink
	);
	$companyLinks = json_encode($linksArray);
	
	if ($stmt2 = $db->prepare("UPDATE Company SET company_reg_no=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, name=?, links=?, modified_date=?, modified_by=? WHERE id=?")) {
		$stmt2->bind_param('sssssssssss', $companyRegNo, $companyAddress, $companyAddress2, $companyAddress3, $companyPhone, $companyFax, $companyName, $companyLinks, $today, $username, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();

			echo '<script type="text/javascript">alert("Your company profile is updated successfully!");</script>'; 
			header("location: ../companyProfile.php");
		} 
		else{
			echo '<script type="text/javascript">alert("Failed due to '.$stmt2->error.'");</script>'; 
			header("location: ../companyProfile.php");
		}
	} 
	else{
		echo '<script type="text/javascript">alert("Something went wrong!");</script>'; 
		header("location: ../companyProfile.php");
	}
} 
else{
	echo '<script type="text/javascript">alert("Please fill in all fields!");</script>'; 
	header("location: ../companyProfile.php");
}
?>
