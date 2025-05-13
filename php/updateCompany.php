<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}

if(isset($_POST['newRegNo'], $_POST['companyName'], $_POST['companyAddress'], $_POST['companyPhone'])){
	$newRegNo = filter_input(INPUT_POST, 'newRegNo', FILTER_SANITIZE_STRING);
	$companyName = filter_input(INPUT_POST, 'companyName', FILTER_SANITIZE_STRING);
	$companyAddress = filter_input(INPUT_POST, 'companyAddress', FILTER_SANITIZE_STRING);
	$companyPhone = filter_input(INPUT_POST, 'companyPhone', FILTER_SANITIZE_STRING);
	$companyRegNo = null;
	$companyAddress2 = null;
	$companyAddress3 = null;
	$companyFax = null;
	$companyMobileNo = null;
	$companyTinNo = null;
	$today = date("Y-m-d H:i:s");
	$id = '1';
	$action = '2';

	if($_POST['companyRegNo'] != null && $_POST['companyRegNo'] != ""){
		$companyRegNo = filter_input(INPUT_POST, 'companyRegNo', FILTER_SANITIZE_STRING);
	}

	if($_POST['companyAddress2'] != null && $_POST['companyAddress2'] != ""){
		$companyAddress2 = filter_input(INPUT_POST, 'companyAddress2', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['companyAddress3'] != null && $_POST['companyAddress3'] != ""){
		$companyAddress3 = filter_input(INPUT_POST, 'companyAddress3', FILTER_SANITIZE_STRING);
	}

	if($_POST['companyFax'] != null && $_POST['companyFax'] != ""){
		$companyFax = filter_input(INPUT_POST, 'companyFax', FILTER_SANITIZE_STRING);
	}

	if($_POST['companyMobileNo'] != null && $_POST['companyMobileNo'] != ""){
		$companyMobileNo = filter_input(INPUT_POST, 'companyMobileNo', FILTER_SANITIZE_STRING);
	}

	if($_POST['companyTinNo'] != null && $_POST['companyTinNo'] != ""){
		$companyTinNo = filter_input(INPUT_POST, 'companyTinNo', FILTER_SANITIZE_STRING);
	}

	if ($stmt2 = $db->prepare("UPDATE Company SET company_reg_no=?, new_reg_no=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, mobile_no=?, tin_no=?, name=?, modified_date=?, modified_by=? WHERE id=?")) {
		$stmt2->bind_param('sssssssssssss', $companyRegNo, $newRegNo, $companyAddress, $companyAddress2, $companyAddress3, $companyPhone, $companyFax, $companyMobileNo, $companyTinNo, $companyName, $today, $username, $id);
		
		if($stmt2->execute()){
			$stmt2->close();

			if ($log_insert_stmt = $db->prepare("INSERT INTO Company_Log (company_id, company_reg_no, new_reg_no, name, address_line_1, address_line_2, address_line_3, phone_no, fax_no, mobile_no, tin_no, action_id, action_by, event_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
				$log_insert_stmt->bind_param('ssssssssssssss', $id, $companyRegNo, $newRegNo, $companyName, $companyAddress, $companyAddress2, $companyAddress3, $companyPhone, $companyFax, $companyMobileNo, $companyTinNo, $action, $username, $today);
			

				if (! $log_insert_stmt->execute()) {
					echo '<script type="text/javascript">alert("Failed due to '.$log_insert_stmt->error.'");</script>'; 
					header("location: ../companyProfile.php");
				}
				else{
					$log_insert_stmt->close();
					$db->close();

					echo '<script type="text/javascript">alert("Your company profile is updated successfully!");</script>'; 
					header("location: ../companyProfile.php");
				}
			}
			else{
				echo '<script type="text/javascript">alert("Something went wrong when insert log!");</script>'; 
				header("location: ../companyProfile.php");
			}
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
