<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/permissions.php';

if (!isset($_SESSION['id'])) {
	echo json_encode(array("status"=> "failed", "message"=> "Session expired, please login again"));
	exit;
}

if (!hasModulePermission('Stock Management', 'Stock Take', ['cancelled'])) {
	echo json_encode(array("status"=> "failed", "message"=> "You do not have permission to cancel this record"));
	exit;
}

$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$cancelReason = isset($_POST['cancelReason']) ? trim($_POST['cancelReason']) : '';

if ($id === '' || $cancelReason === '') {
	echo json_encode(array("status"=> "failed", "message"=> "Please fill in the cancel reason"));
	exit;
}

$username = $_SESSION["username"];
$cancel = '1';

if ($stmt = $db->prepare("UPDATE Bitumen SET status=?, cancel_reason=?, modified_by=? WHERE id=? AND status='0'")) {
	$stmt->bind_param('ssss', $cancel, $cancelReason, $username, $id);

	if (!$stmt->execute()) {
		echo json_encode(array("status"=> "failed", "message"=> $stmt->error));
	}
	else if ($stmt->affected_rows == 0) {
		echo json_encode(array("status"=> "failed", "message"=> "Record not found or already cancelled"));
	}
	else {
		echo json_encode(array("status"=> "success", "message"=> "Cancelled"));
	}

	$stmt->close();
}
else {
	echo json_encode(array("status"=> "failed", "message"=> "Something went wrong"));
}

$db->close();
?>
