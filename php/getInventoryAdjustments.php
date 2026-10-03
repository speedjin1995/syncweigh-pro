<?php
session_start();
require_once "db_connect.php";
require_once "requires/permissions.php";

function formatInventoryNumber($value) {
    $formatted = number_format((float)$value, 3, '.', '');
    $formatted = rtrim(rtrim($formatted, '0'), '.');
    return $formatted === '' ? '0' : $formatted;
}

if(!isset($_POST['inventoryId'])){
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
        )
    );
    exit;
}

if(!hasModulePermission('Stock Management', 'Inventory', ['view', 'edit'])){
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "No permission"
        )
    );
    exit;
}

$inventoryId = trim($_POST['inventoryId']);
$plantQuery = "";

if (!hasModulePermission('Stock Management', 'Inventory', ['view_all_plants'])){
    $plantCodes = isset($_SESSION["plant"]) && is_array($_SESSION["plant"]) ? $_SESSION["plant"] : array();
    $escapedPlants = array_map(function($plantCode) use ($db) {
        return mysqli_real_escape_string($db, $plantCode);
    }, $plantCodes);

    if (count($escapedPlants) === 0) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "No plant permission"
            )
        );
        exit;
    }

    $plantQuery = " AND Inventory.plant_code IN ('".implode("', '", $escapedPlants)."')";
}

$sql = "
    SELECT
        inventory_adjustment.weight_adjustment,
        inventory_adjustment.drum_adjustment,
        inventory_adjustment.remarks,
        inventory_adjustment.created_by,
        inventory_adjustment.created_date
    FROM inventory_adjustment
    INNER JOIN Inventory ON Inventory.id = inventory_adjustment.inventory_id
    WHERE inventory_adjustment.inventory_id = ?
    AND inventory_adjustment.status = '0'
    AND Inventory.status = '0'
    ".$plantQuery."
    ORDER BY inventory_adjustment.created_date DESC, inventory_adjustment.id DESC
";

if ($select_stmt = $db->prepare($sql)) {
    $select_stmt->bind_param('s', $inventoryId);

    if (!$select_stmt->execute()) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Something went wrong"
            )
        );
        exit;
    }

    $result = $select_stmt->get_result();
    $message = array();

    while ($row = $result->fetch_assoc()) {
        $message[] = array(
            "weight_adjustment" => formatInventoryNumber($row['weight_adjustment']),
            "drum_adjustment" => formatInventoryNumber($row['drum_adjustment']),
            "remarks" => $row['remarks'] ?? '',
            "created_by" => $row['created_by'] ?? '',
            "created_date" => $row['created_date'] ?? ''
        );
    }

    echo json_encode(
        array(
            "status" => "success",
            "message" => $message
        )
    );

    $select_stmt->close();
} else {
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Something went wrong"
        )
    );
}
?>
