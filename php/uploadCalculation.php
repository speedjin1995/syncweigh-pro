<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['username'];

// Update existing active Calculation_Value row with the same key, otherwise insert new row.
// $keyCol / $valCol are hardcoded by callers, never request input.
function upsertCalculationValue($db, $calculationId, $keyCol, $keyVal, $valCol, $val) {
    $existingValueId = '';
    if ($checkValueStmt = $db->prepare("SELECT id FROM Calculation_Value WHERE calculation_id=? AND `$keyCol`=? AND deleted='0' LIMIT 1")) {
        $checkValueStmt->bind_param('ss', $calculationId, $keyVal);
        if ($checkValueStmt->execute()) {
            $checkResult = $checkValueStmt->get_result();
            if ($valueRow = $checkResult->fetch_assoc()) {
                $existingValueId = $valueRow['id'];
            }
        }
        $checkValueStmt->close();
    }

    if (!empty($existingValueId)) {
        if ($updateValueStmt = $db->prepare("UPDATE Calculation_Value SET `$valCol`=? WHERE id=?")) {
            $updateValueStmt->bind_param('ss', $val, $existingValueId);
            $updateValueStmt->execute();
            $updateValueStmt->close();
        }
    } else {
        if ($insertValueStmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, `$keyCol`, `$valCol`) VALUES (?, ?, ?)")) {
            $insertValueStmt->bind_param('sss', $calculationId, $keyVal, $val);
            $insertValueStmt->execute();
            $insertValueStmt->close();
        }
    }
}

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $uploadType = $data['uploadType'];
    $rowsData = $data['data'];

    // Validate template fields based on upload type
    $requiredFields = [];
    $templateName = '';//var_dump($data);exit;
    
    if ($uploadType == 'LEVEL') {
        $requiredFields = ['Plant', 'BatchDrum', 'Levelmm', 'Volumem'];
        $templateName = 'Bitumen_Level_Template';
    } elseif ($uploadType == 'SG') {
        $requiredFields = ['Plant', 'BatchDrum', 'TemperatureC', 'SG'];
        $templateName = 'Bitumen_SG_Template';
    } elseif ($uploadType == 'BITULOOKUP') {
        $requiredFields = ['Plant', 'BatchDrum', 'HeightCM', 'WeightMT'];
        $templateName = 'Bitumen_Lookup_Template';
    } elseif ($uploadType == 'LFOLOOKUP') {
        $requiredFields = ['Plant', 'BatchDrum', 'Depthm', 'Litrel'];
        $templateName = 'LFO_Lookup_Template';
    } elseif ($uploadType == 'DIESELLOOKUP') {
        $requiredFields = ['Plant', 'BatchDrum', 'Depthm', 'Litrel'];
        $templateName = 'Diesel_Lookup_Template';
    }

    // Check if first row has required fields
    if (!empty($rowsData) && !empty($requiredFields)) {
        $firstRow = $rowsData[0];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $firstRow)) {
                echo json_encode([
                    "status" => "error",
                    "message" => ["Wrong template uploaded. Please use the correct $templateName."]
                ]);
                exit;
            }
        }
    }

    $errors = [];
    $calculationId = null;
    foreach ($rowsData as $index => $row) {
        // Map uploadType to calculation type
        $typeMap = ['LEVEL' => 'BITULEVEL', 'SG' => 'BITUSG', 'BITULOOKUP' => 'BITULOOKUP', 'LFOLOOKUP' => 'LFOLOOKUP', 'DIESELLOOKUP' => 'DIESELLOOKUP'];
        $type = $typeMap[$uploadType] ?? null;

        if ($type) {
            // Shared: Plant validation
            $Plant = isset($row['Plant']) && !empty($row['Plant']) ? searchPlantIdByName($row['Plant'], $db) : '';
            if (empty($Plant)) {
                $errors[] = "Plant not found in row " . ($index + 1) . ": " . $row['Plant'];
                continue;
            }

            // Shared: BatchDrum validation
            $BatchDrum = !empty($row['BatchDrum']) ? trim($row['BatchDrum']) : '';
            if (empty($BatchDrum)) {
                $errors[] = "Batch/Drum cannot be blank in row " . ($index + 1);
                continue;
            }

            // Shared: find or create calculation
            if (empty($calculationId)) {
                if ($calculation_stmt = $db->prepare("SELECT * FROM Calculations WHERE plant_id=? AND batch_drum=? AND type=? AND deleted='0'")) {
                    $calculation_stmt->bind_param('sss', $Plant, $BatchDrum, $type);
                    if ($calculation_stmt->execute()) {
                        $result = $calculation_stmt->get_result();
                        while ($calcRow = $result->fetch_assoc()) {
                            $calculationId = $calcRow['id'];
                        }
                    }
                    $calculation_stmt->close();
                }

                if (empty($calculationId)) {
                    if ($insert_stmt = $db->prepare("INSERT INTO Calculations (type, plant_id, batch_drum, created_by) VALUES (?, ?, ?, ?)")) {
                        $insert_stmt->bind_param('ssss', $type, $Plant, $BatchDrum, $uid);
                        $insert_stmt->execute();
                        $calculationId = $insert_stmt->insert_id;
                        $insert_stmt->close();
                    }
                }
            }

            // Type-specific insert
            if (!empty($calculationId)) {
                // Set old calculation deleted to 1
                if ($uploadType == 'LEVEL') {
                    $Levelmm = isset($row['Levelmm']) && $row['Levelmm'] !== '' ? ($row['Levelmm'] == '0' ? '0' : trim($row['Levelmm'])) : '';
                    $Volumem = isset($row['Volumem']) && $row['Volumem'] !== '' ? ($row['Volumem'] == '0' ? '0' : trim($row['Volumem'])) : '';
                    if ($Levelmm !== '' && $Volumem !== '') {
                        upsertCalculationValue($db, $calculationId, 'level', $Levelmm, 'volume', $Volumem);
                    }
                } elseif ($uploadType == 'SG') {
                    $TemperatureC = isset($row['TemperatureC']) && $row['TemperatureC'] !== '' ? ($row['TemperatureC'] == '0' ? '0' : trim($row['TemperatureC'])) : '';
                    $SG = isset($row['SG']) && $row['SG'] !== '' ? ($row['SG'] == '0' ? '0' : trim($row['SG'])) : '';
                    if ($TemperatureC !== '' && $SG !== '') {
                        upsertCalculationValue($db, $calculationId, 'temperature', $TemperatureC, 'sg', $SG);
                    }
                } elseif ($uploadType == 'BITULOOKUP') {
                    $HeightCM = isset($row['HeightCM']) && $row['HeightCM'] !== '' ? ($row['HeightCM'] == '0' ? '0' : trim($row['HeightCM'])) : '';
                    $WeightMT = isset($row['WeightMT']) && $row['WeightMT'] !== '' ? ($row['WeightMT'] == '0' ? '0' : trim($row['WeightMT'])) : '';
                    if ($HeightCM !== '' && $WeightMT !== '') {
                        upsertCalculationValue($db, $calculationId, 'level', $HeightCM, 'volume', $WeightMT);
                    }
                } elseif ($uploadType == 'LFOLOOKUP') {
                    $Depthm = isset($row['Depthm']) && $row['Depthm'] !== '' ? ($row['Depthm'] == '0' ? '0' : trim($row['Depthm'])) : '';
                    $Litrel = isset($row['Litrel']) && $row['Litrel'] !== '' ? ($row['Litrel'] == '0' ? '0' : trim($row['Litrel'])) : '';
                    if ($Depthm !== '' && $Litrel !== '') {
                        upsertCalculationValue($db, $calculationId, 'level', $Depthm, 'volume', $Litrel);
                    }
                } elseif ($uploadType == 'DIESELLOOKUP') {
                    $Depthm = isset($row['Depthm']) && $row['Depthm'] !== '' ? ($row['Depthm'] == '0' ? '0' : trim($row['Depthm'])) : '';
                    $Litrel = isset($row['Litrel']) && $row['Litrel'] !== '' ? ($row['Litrel'] == '0' ? '0' : trim($row['Litrel'])) : '';
                    if ($Depthm !== '' && $Litrel !== '') {
                        upsertCalculationValue($db, $calculationId, 'level', $Depthm, 'volume', $Litrel);
                    }
                }
            }
        }
    }

    if (!empty($errors)) {
        echo json_encode([
            "status" => "error",
            "message" => $errors
        ]);
        exit;
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
