<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['userID'];

// Data format: { "Sheet1": [{row}, {row}], "Sheet2": [{row}, {row}], ... }
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $errorArray = [];
    // Get Bitumen 60/70 Raw Mat Id
    $bitumenRawMatDetail = searchRawMatByCode('BTBI001', $db);
    $rawMatId = $bitumenRawMatDetail['id'];
    $rawMatCode = $bitumenRawMatDetail['raw_mat_code'];

    // Process BDM - Bukit Damar (Batch)
    if (isset($data['BUKIT - BP BOM']) && !empty($data['BUKIT - BP BOM'])){
        $plantId = searchPlantIdByName('Bukit Damar', $db);
        foreach ($data['BUKIT - BP BOM'] as $row){
            $productCode = $row['Item Code'];
            if (isset($productCode) && !empty($productCode) && $productCode != 'false'){
                if ((float) $row['BTBI001'] <= 0) {
                    continue;
                }

                $bitumenMT = (float) $row['BTBI001'];
                $bitumenKG = (float) $row['BTBI001'] * 1000;
                $productId = searchProductIdByCode($productCode, $db);

                // Query Product_RawMat to find existing
                $productRawMatQuery = $db->prepare("SELECT * FROM Product_RawMat WHERE product_id = ? AND raw_mat_id = ? AND plant_id = ? AND batch_drum = 'Batch' AND status = 0");
                $productRawMatQuery->bind_param("sss", $productId, $rawMatId, $plantId);
                $productRawMatQuery->execute();
                $productRawMatResult = $productRawMatQuery->get_result();
                if ($productRawMatResult->num_rows == 0){
                    // Insert new record
                    $productRawMatInsert = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum) VALUES (?, ?, ?, ?, 6, ?, ?, 'Batch')");
                    $productRawMatInsert->bind_param("ssssss", $productId, $rawMatId, $rawMatCode, $bitumenMT, $bitumenKG, $plantId);
                    if (!$productRawMatInsert->execute()) {
                        array_push($errorArray, "Failed to insert BDM - BP BOM data for Item Code: " . $productCode);
                    }
                    $productRawMatInsert->close();
                } else {
                    $row = $productRawMatResult->fetch_assoc();
                    $productRawMatId = $row['id'];

                    // Update existing record
                    $productRawMatUpdate = $db->prepare("UPDATE Product_RawMat SET raw_mat_basic_uom=?, raw_mat_weight=? WHERE id=?");
                    $productRawMatUpdate->bind_param("sss", $bitumenMT, $bitumenKG, $productRawMatId);
                    if (!$productRawMatUpdate->execute()) {
                        array_push($errorArray, "Failed to update BDM - BP BOM data for Item Code: " . $productCode);
                    }
                    $productRawMatUpdate->close();
                }
            }else{
                continue;
            }
        }
    }

    // Process BDM - Bukit Damar (Drum)
    // if (isset($data['BUKIT - DP BOM']) && !empty($data['BUKIT - DP BOM'])){
    //     foreach ($data['BUKIT - DP BOM'] as $row){
            
    //     }
    // }

    // // Process GMB - Gambang (Default - Drum)
    // if (isset($data['GAMBANG - BOM']) && !empty($data['GAMBANG - BOM'])){
    //     foreach ($data['GAMBANG - BOM'] as $row){
            
    //     }
    // }

    // // Process GEB - Gebeng (Default - Batch)
    // if (isset($data['GEBENG - BOM']) && !empty($data['GEBENG - BOM'])){
    //     foreach ($data['GEBENG - BOM'] as $row){
            
    //     }
    // }

    $db->close();

    if (!empty($errorArray)) {
        echo json_encode(
            array(
                "status" => "error",
                "message" => $errorArray
            )
        );
    } else {
        echo json_encode(
            array(
                "status" => "success",
                "message" => "BOM List uploaded successfully!"
            )
        );
    }
} else {
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "No data found in the uploaded file"
        )
    );
}
?>
