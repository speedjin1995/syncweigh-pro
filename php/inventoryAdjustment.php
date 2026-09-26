<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/permissions.php';

function failResponse($message) {
    echo json_encode(
        array(
            "status" => "failed",
            "message" => $message
        )
    );
    exit;
}

function readDecimalField($fieldName) {
    if (!isset($_POST[$fieldName]) || trim($_POST[$fieldName]) === '') {
        return 0.0;
    }

    $value = trim($_POST[$fieldName]);
    return is_numeric($value) ? (float)$value : null;
}

if(!isset($_SESSION['id'])){
    failResponse('Session expired');
}

if(!hasModulePermission('Stock Management', 'Inventory', ['edit'])){
    failResponse('No permission to adjust inventory');
}

if(!isset($_POST['id'])){
    failResponse('Missing Attribute');
}

$inventoryId = trim($_POST['id']);
$weightAdjustment = readDecimalField('weightAdjustment');
$drumAdjustment = readDecimalField('drumAdjustment');
$remarks = isset($_POST['adjustmentRemarks']) ? trim($_POST['adjustmentRemarks']) : null;
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : 'SYSTEM';

if($weightAdjustment === null || $drumAdjustment === null){
    failResponse('Please key in valid adjustment values');
}

if(abs($weightAdjustment) < 0.000001 && abs($drumAdjustment) < 0.000001){
    failResponse('Please key in Weight Adjustment or Drum Adjustment');
}

$plantQuery = "";

if (!hasModulePermission('Stock Management', 'Inventory', ['view_all_plants'])){
    $plantCodes = isset($_SESSION["plant"]) && is_array($_SESSION["plant"]) ? $_SESSION["plant"] : array();
    $escapedPlants = array_map(function($plantCode) use ($db) {
        return mysqli_real_escape_string($db, $plantCode);
    }, $plantCodes);

    if (count($escapedPlants) === 0) {
        failResponse('No plant permission');
    }

    $plantQuery = " AND Inventory.plant_code IN ('".implode("', '", $escapedPlants)."')";
}

$db->begin_transaction();

try {
    $inventorySql = "
        SELECT
            Inventory.*,
            Raw_Mat.raw_mat_code,
            Raw_Mat.name
        FROM Inventory
        INNER JOIN Raw_Mat ON Inventory.raw_mat_id = Raw_Mat.id
        WHERE Inventory.id = ?
        AND Inventory.status = '0'
        ".$plantQuery."
        FOR UPDATE
    ";

    if(!$inventoryStmt = $db->prepare($inventorySql)){
        throw new Exception($db->error);
    }

    $inventoryStmt->bind_param('s', $inventoryId);

    if(!$inventoryStmt->execute()){
        throw new Exception($inventoryStmt->error);
    }

    $inventoryResult = $inventoryStmt->get_result();
    $inventory = $inventoryResult->fetch_assoc();
    $inventoryStmt->close();

    if(!$inventory){
        throw new Exception('Inventory not found');
    }

    if(!$sumStmt = $db->prepare("SELECT COALESCE(SUM(weight_adjustment), 0) AS total_weight_adjustment, COALESCE(SUM(drum_adjustment), 0) AS total_drum_adjustment FROM inventory_adjustment WHERE inventory_id = ? AND status = '0'")){
        throw new Exception($db->error);
    }

    $sumStmt->bind_param('s', $inventoryId);

    if(!$sumStmt->execute()){
        throw new Exception($sumStmt->error);
    }

    $sumResult = $sumStmt->get_result();
    $sumRow = $sumResult->fetch_assoc();
    $sumStmt->close();

    $currentWeight = (float)$inventory['raw_mat_weight'] + (float)$sumRow['total_weight_adjustment'];
    $currentDrum = (float)$inventory['raw_mat_count'] + (float)$sumRow['total_drum_adjustment'];
    $newWeight = $currentWeight + $weightAdjustment;
    $newDrum = $currentDrum + $drumAdjustment;

    if($newWeight < -0.000001 || $newDrum < -0.000001){
        throw new Exception('Total inventory cannot be negative');
    }

    if(!$insertStmt = $db->prepare("INSERT INTO inventory_adjustment (inventory_id, raw_mat_id, raw_mat_code, raw_mat_name, weight_adjustment, drum_adjustment, plant_id, plant_code, remarks, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
        throw new Exception($db->error);
    }

    $rawMatId = $inventory['raw_mat_id'];
    $rawMatCode = $inventory['raw_mat_code'];
    $rawMatName = $inventory['name'];
    $plantId = $inventory['plant_id'];
    $plantCode = $inventory['plant_code'];

    $insertStmt->bind_param(
        'ssssddsssss',
        $inventoryId,
        $rawMatId,
        $rawMatCode,
        $rawMatName,
        $weightAdjustment,
        $drumAdjustment,
        $plantId,
        $plantCode,
        $remarks,
        $username,
        $username
    );

    if(!$insertStmt->execute()){
        throw new Exception($insertStmt->error);
    }

    $insertStmt->close();
    $db->commit();
    $db->close();

    echo json_encode(
        array(
            "status"=> "success",
            "message"=> "Adjusted Successfully!!"
        )
    );
} catch (Exception $e) {
    $db->rollback();

    echo json_encode(
        array(
            "status"=> "failed",
            "message"=> $e->getMessage()
        )
    );
}
?>
