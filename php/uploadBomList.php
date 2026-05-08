<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['userID'];
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $errorArray = [];
    try {
        // KG Unit Id
        $kgUnitId = searchUnitIdByCode('KG', $db);

        // Plant config
        $bukitDamarPlant = searchPlantByName('Bukit Damar', $db);
        $gambangPlant = searchPlantByName('Gambang', $db);
        $gebengPlant = searchPlantByName('Gebeng', $db);

        $sheets = [
            'BUKIT - BP BOM' => [$bukitDamarPlant['id'], 'Batch', 'BDM - BP BOM'],
            'BUKIT - DP BOM' => [$bukitDamarPlant['id'], 'Drum', 'BDM - DP BOM'],
            'GAMBANG - BOM'  => [$gambangPlant['id'], $gambangPlant['default_type'], 'GAMBANG - BOM'],
            'GEBENG - BOM'   => [$gebengPlant['id'], $gebengPlant['default_type'], 'GEBENG - BOM'],
        ];

        // Skip columns that are not raw material codes
        $skipKeys = ['Item Code', 'Description', 'Lime', 'Rycle Filler (either Lime)'];

        // Cache raw mat lookups to avoid repeated DB calls
        $rawMatCache = [];

        // Prepare statements once
        $selectStmt = $db->prepare("SELECT id FROM Product_RawMat WHERE product_id = ? AND raw_mat_id = ? AND plant_id = ? AND batch_drum = ? AND status = 0");
        $insertStmt = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $updateStmt = $db->prepare("UPDATE Product_RawMat SET raw_mat_basic_uom=?, raw_mat_weight=? WHERE id=?");

        foreach ($sheets as $sheetName => [$plantId, $batchDrum, $errorLabel]) {
            if (empty($data[$sheetName])) {
                continue;
            }

            // Delete from Stock_Take_List records for plant_id, batch_drum first
            if($deleteStockTakeList = $db->prepare("DELETE FROM Stock_Take_List WHERE plant_id = ? AND batch_drum = ?"))
            {
                $deleteStockTakeList->bind_param('ss', $plantId, $batchDrum);
                $deleteStockTakeList->execute();
                $deleteStockTakeList->close();
            }

            $sortCount = 1;
            foreach ($data[$sheetName] as $row) {
                $productName = $row['Description'] ?? null;
                if (empty($productName) || $productName === 'false') {
                    continue;
                }

                $productCode = $row['Item Code'] ?? null;
                if (empty($productCode) || $productCode === 'false' || $productCode === 'Item Code') {
                    continue;
                }

                $product = searchProductWithBasicUomByName($productName, $db);
                if (empty($product)) {
                    $errorArray[] = "Product not found for Description: {$productName} in {$errorLabel}";
                    continue;
                } 

                $productId = $product['id'];
                $productBasicUom = $product['unit'];
                $productKgRate = 0.001; // Default to MT -> KG conversion

                // Insert Into Stock_Take_List
                if($insertStockTakeList = $db->prepare("INSERT INTO Stock_Take_List (plant_id, product_id, batch_drum, sort) VALUES (?, ?, ?, ?)")){
                    $insertStockTakeList->bind_param('ssss', $plantId, $productId, $batchDrum, $sortCount);
                    $insertStockTakeList->execute();
                    $insertStockTakeList->close();
                    $sortCount++;
                }

                // Search Product KG conversion rate
                if ($productUomStmt = $db->prepare("SELECT * FROM Product_UOM WHERE product_id = ? AND unit_id = ? AND status = 0")) {
                    $productUomStmt->bind_param('ss', $productId, $kgUnitId);
                    $productUomStmt->execute();
                    $uomResult = $productUomStmt->get_result();
                    if ($uomRow = $uomResult->fetch_assoc()) {
                        $productKgRate = $uomRow['rate'];
                    }
                    $productUomStmt->close();
                }

                // Loop through all columns — each non-skip key is a raw material name
                foreach ($row as $rawMatName => $value) {
                    if (in_array($rawMatName, $skipKeys) || $rawMatName === '' || strpos($rawMatName, '__EMPTY') === 0) {
                        continue;
                    }

                    if ((float) $value <= 0) {
                        continue;
                    }

                    // Lookup raw mat details (cached)
                    if (!isset($rawMatCache[$rawMatName])) {
                        $rawMatDetail = searchRawMatByName($rawMatName, $db);
                        if (empty($rawMatDetail)) {
                            $errorArray[] = "Raw material not found: {$rawMatName}";
                        }
                        $rawMatCache[$rawMatName] = $rawMatDetail;
                    }
                    $rawMatDetail = $rawMatCache[$rawMatName];
                    if (empty($rawMatDetail)) {
                        continue;
                    }

                    $rawMatId = $rawMatDetail['id'];
                    $rawMatCode = $rawMatDetail['raw_mat_code'];
                    $valueKG = (float) $value / (float) $productKgRate;

                    $selectStmt->bind_param("ssss", $productId, $rawMatId, $plantId, $batchDrum);
                    $selectStmt->execute();
                    $result = $selectStmt->get_result();

                    if ($result->num_rows == 0) {
                        $insertStmt->bind_param("ssssssss", $productId, $rawMatId, $rawMatCode, $value, $kgUnitId, $valueKG, $plantId, $batchDrum);
                        if (!$insertStmt->execute()) {
                            $errorArray[] = "Failed to insert {$errorLabel} data for Item Code: {$productCode}, Raw Mat: {$rawMatCode}";
                        }
                    } else {
                        $existing = $result->fetch_assoc();
                        $existingId = $existing['id'];
                        $updateStmt->bind_param("sss", $value, $valueKG, $existingId);
                        if (!$updateStmt->execute()) {
                            $errorArray[] = "Failed to update {$errorLabel} data for Item Code: {$productCode}, Raw Mat: {$rawMatCode}";
                        }
                    }
                }
            }
        }

        $selectStmt->close();
        $insertStmt->close();
        $updateStmt->close();
        $db->close();

        if (!empty($errorArray)) {
            echo json_encode(["status" => "error", "message" => $errorArray]);
        } else {
            echo json_encode(["status" => "success", "message" => "BOM List uploaded successfully!"]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "failed", "message" => "No data found in the uploaded file"]);
}
?>
