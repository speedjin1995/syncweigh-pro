<?php
session_start();
require_once "db_connect.php";
require_once "requires/permissions.php";

function formatInventoryNumber($value) {
    $formatted = number_format((float)$value, 3, '.', '');
    $formatted = rtrim(rtrim($formatted, '0'), '.');
    return $formatted === '' ? '0' : $formatted;
}

if(!isset($_POST['userID'])){
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
        )
    );
    exit;
}

$id = trim($_POST['userID']);
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
        Inventory.*,
        Raw_Mat.raw_mat_code,
        Raw_Mat.name,
        (
            CAST(Inventory.raw_mat_weight AS DECIMAL(18,3)) +
            COALESCE(Inventory_Adjustment_Sum.total_weight_adjustment, 0)
        ) AS adjusted_raw_mat_weight,
        (
            CAST(Inventory.raw_mat_count AS DECIMAL(18,3)) +
            COALESCE(Inventory_Adjustment_Sum.total_drum_adjustment, 0)
        ) AS adjusted_raw_mat_count
    FROM Inventory
    INNER JOIN Raw_Mat ON Inventory.raw_mat_id = Raw_Mat.id
    LEFT JOIN (
        SELECT
            inventory_id,
            SUM(weight_adjustment) AS total_weight_adjustment,
            SUM(drum_adjustment) AS total_drum_adjustment
        FROM inventory_adjustment
        WHERE status = '0'
        GROUP BY inventory_id
    ) Inventory_Adjustment_Sum ON Inventory_Adjustment_Sum.inventory_id = Inventory.id
    WHERE Inventory.status = '0'
    AND Inventory.id = ?
    ".$plantQuery."
";

if ($update_stmt = $db->prepare($sql)) {
    $update_stmt->bind_param('s', $id);

    if (!$update_stmt->execute()) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Something went wrong"
            )
        );
        exit;
    }

    $result = $update_stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $message = array(
            "id" => $row['id'],
            "raw_mat_id" => $row['raw_mat_id'],
            "raw_mat_code" => $row['raw_mat_code'],
            "name" => $row['name'],
            "raw_mat_basic_uom" => $row['raw_mat_basic_uom'],
            "raw_mat_weight" => formatInventoryNumber($row['adjusted_raw_mat_weight']),
            "raw_mat_count" => formatInventoryNumber($row['adjusted_raw_mat_count'])
        );

        echo json_encode(
            array(
                "status" => "success",
                "message" => $message
            )
        );
    } else {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Inventory not found"
            )
        );
    }

    $update_stmt->close();
} else {
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Something went wrong"
        )
    );
}
?>
