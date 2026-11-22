<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(array("status" => "failed", "message" => "Session expired, please login again"));
    exit;
}

require_once 'requires/permissions.php';

if (!hasModulePermission('Master Data', 'Plant', ['edit'])) {
    echo json_encode(array("status" => "failed", "message" => "You do not have permission to update serial no"));
    exit;
}

$username = $_SESSION["username"];
$plantId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)));
$stockTakeBatch = filter_input(INPUT_POST, 'stockTakeBatch', FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)));
$stockTakeDrum = filter_input(INPUT_POST, 'stockTakeDrum', FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)));

if (!$plantId || !$stockTakeBatch || !$stockTakeDrum) {
    echo json_encode(array("status" => "failed", "message" => "Serial no must be a whole number of 1 or more"));
    exit;
}

if ($update_stmt = $db->prepare("UPDATE Plant SET stock_take_batch=?, stock_take_drum=?, modified_by=? WHERE id=?")) {
    $update_stmt->bind_param('iisi', $stockTakeBatch, $stockTakeDrum, $username, $plantId);

    if (!$update_stmt->execute()) {
        echo json_encode(array("status" => "failed", "message" => $update_stmt->error));
    } else {
        echo json_encode(array("status" => "success", "message" => "Serial No Updated Successfully!!"));
    }

    $update_stmt->close();
} else {
    echo json_encode(array("status" => "failed", "message" => "Something went wrong"));
}

$db->close();
?>
