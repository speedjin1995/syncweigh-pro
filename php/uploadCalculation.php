<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $uploadType = $data['uploadType'];
    $rowsData = $data['data'];

    // Validate template fields based on upload type
    $requiredFields = [];
    $templateName = '';
    
    if ($uploadType == 'LEVEL') {
        $requiredFields = ['Plant', 'BatchDrum', 'Levelmm', 'Volumem'];
        $templateName = 'Bitumen_Level_Template';
    } elseif ($uploadType == 'SG') {
        $requiredFields = ['Plant', 'BatchDrum', 'TemperatureC', 'SG'];
        $templateName = 'Bitumen_SG_Template';
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
        if ($uploadType == 'LEVEL') {
            $type = 'BITULEVEL';
            $Plant = isset($row['Plant']) && !empty($row['Plant']) ? searchPlantIdByName($row['Plant'], $db) : '';
            if (empty($Plant)) {
                $errors[] = "Plant not found in row " . ($index + 1) . ": " . $row['Plant'];
                continue;
            }

            $BatchDrum = !empty($row['BatchDrum']) ? trim($row['BatchDrum']) : '';
            if (empty($BatchDrum)) {
                $errors[] = "Batch/Drum cannot be blank in row " . ($index + 1);
                continue;
            }
            // Query to find existing calculation
            if (empty($calculationId)){
                if ($calculation_stmt = $db->prepare("SELECT * FROM Calculations WHERE plant_id=? AND batch_drum=? AND type = ? AND deleted='0'")) {
                    $calculation_stmt->bind_param('sss', $Plant, $BatchDrum, $type);
            
                    // Execute the prepared query.
                    if ($calculation_stmt->execute()) {
                        $result = $calculation_stmt->get_result();
                        
                        while ($calcRow = $result->fetch_assoc()) {
                            $calculationId = $calcRow['id'];
                        }
                    }
                    $calculation_stmt->close();
                }
                
                // Create new calculation if not found
                if (empty($calculationId)) {
                    if ($insert_stmt = $db->prepare("INSERT INTO Calculations (type, plant_id, batch_drum, created_by) VALUES (?, ?, ?, ?)")) {
                        $insert_stmt->bind_param('ssss', $type, $Plant, $BatchDrum, $uid);
                        $insert_stmt->execute();
                        $calculationId = $insert_stmt->insert_id;
                        $insert_stmt->close();
                    }
                }
            }

            $Levelmm = isset($row['Levelmm']) && $row['Levelmm'] !== '' ? ($row['Levelmm'] == '0' ? '0' : trim($row['Levelmm'])) : '';
            $Volumem = isset($row['Volumem']) && $row['Volumem'] !== '' ? ($row['Volumem'] == '0' ? '0' : $row['Volumem']) : '';
            
            if (!empty($calculationId) && $Levelmm !== '' && $Volumem !== '') {
                if ($insertValueStmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, `level`, volume) VALUES (?, ?, ?)")) {
                    $insertValueStmt->bind_param('sss', $calculationId, $Levelmm, $Volumem);
                    $insertValueStmt->execute();
                    $insertValueStmt->close();
                }
            }
        }elseif ($uploadType == 'SG') {
            $type = 'BITUSG';
            $Plant = isset($row['Plant']) && !empty($row['Plant']) ? searchPlantIdByName($row['Plant'], $db) : '';
            if (empty($Plant)) {
                $errors[] = "Plant not found in row " . ($index + 1) . ": " . $row['Plant'];
                continue;
            }

            $BatchDrum = !empty($row['BatchDrum']) ? trim($row['BatchDrum']) : '';
            if (empty($BatchDrum)) {
                $errors[] = "Batch/Drum cannot be blank in row " . ($index + 1);
                continue;
            }
            // Query to find existing calculation
            if (empty($calculationId)){
                if ($calculation_stmt = $db->prepare("SELECT * FROM Calculations WHERE plant_id=? AND batch_drum=? AND type = ? AND deleted='0'")) {
                    $calculation_stmt->bind_param('sss', $Plant, $BatchDrum, $type);
            
                    // Execute the prepared query.
                    if ($calculation_stmt->execute()) {
                        $result = $calculation_stmt->get_result();
                        
                        while ($calcRow = $result->fetch_assoc()) {
                            $calculationId = $calcRow['id'];
                        }
                    }
                    $calculation_stmt->close();
                }
                
                // Create new calculation if not found
                if (empty($calculationId)) {
                    if ($insert_stmt = $db->prepare("INSERT INTO Calculations (type, plant_id, batch_drum, created_by) VALUES (?, ?, ?, ?)")) {
                        $insert_stmt->bind_param('ssss', $type, $Plant, $BatchDrum, $uid);
                        $insert_stmt->execute();
                        $calculationId = $insert_stmt->insert_id;
                        $insert_stmt->close();
                    }
                }
            }
            $TemperatureC = isset($row['TemperatureC']) && $row['TemperatureC'] !== '' ? ($row['TemperatureC'] == '0' ? '0' : trim($row['TemperatureC'])) : '';
            $SG = isset($row['SG']) && $row['SG'] !== '' ? ($row['SG'] == '0' ? '0' : trim($row['SG'])) : '';
            
            if (!empty($calculationId) && $TemperatureC !== '' && $SG !== '') {
                if ($insertValueStmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, temperature, sg) VALUES (?, ?, ?)")) {
                    $insertValueStmt->bind_param('sss', $calculationId, $TemperatureC, $SG);
                    $insertValueStmt->execute();
                    $insertValueStmt->close();
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
