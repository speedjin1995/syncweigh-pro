<?php
session_start();
require_once "db_connect.php";
require_once "requires/functions.php";

if(isset($_POST['rawMatCode'], $_POST['diameter'], $_POST['length'], $_POST['height'], $_POST['plantId'], $_POST['batchDrum'])){
    $rawMatCode = filter_input(INPUT_POST, 'rawMatCode', FILTER_SANITIZE_STRING);
    $diameter = filter_input(INPUT_POST, 'diameter', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $length = filter_input(INPUT_POST, 'length', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $height = filter_input(INPUT_POST, 'height', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $plantId = filter_input(INPUT_POST, 'plantId', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $batchDrum = filter_input(INPUT_POST, 'batchDrum', FILTER_SANITIZE_STRING);

    // Query to get raw material details
    $sql = "SELECT * FROM Raw_Mat rm LEFT JOIN Raw_Mat_UOM rmu ON rm.id = rmu.raw_mat_id WHERE rm.raw_mat_code = ? AND rm.status = 0 AND rmu.unit_id = 2";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $rawMatCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $rawMat = $result->fetch_assoc();
    $rate = $rawMat['rate'];
    $rawMatId = $rawMat['id'];
    $stmt->close();

    // Lookup to calculation table first to get preset values before we calculate
    if($rawMatCode == 'LFFO001'){
        $type = 'LFOLOOKUP';
    }else if ($rawMatCode == 'DIE001'){
        $type = 'DIESELLOOKUP';
    }

    if ($calculation_stmt = $db->prepare("SELECT cv.level, cv.volume FROM Calculations c JOIN Calculation_Value cv ON cv.calculation_id = c.id WHERE c.type = ? AND c.plant_id = ? AND c.batch_drum = ? AND cv.level = ? AND c.deleted = 0 AND cv.deleted=0")){
        $calculation_stmt->bind_param("ssss", $type, $plantId, $batchDrum, $height);
        if (!$calculation_stmt->execute()) {
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $calculation_stmt->error
                ));

            $calculation_stmt->close();
            exit;
        }else{
            $result = $calculation_stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $volumeLitres = floatval($row['volume']);
                    $volumeKg = $volumeLitres / floatval($rate);
                    $volumeMt = $volumeKg / 1000;

                    $message = array();
                    $message['volumeLitres'] = $volumeLitres;
                    $message['volumeKg'] = $volumeKg;
                    $message['volumeMt'] = $volumeMt;
                }
            }else{
                if ($diameter <= 0 || $length <= 0 || $height < 0) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Invalid dimensions"
                        ));
                    exit;
                }
                
                $volumeLitres = calculateLFOVolumeLitres($diameter, $length, $height);
                $volumeKg = (float) $volumeLitres / $rate;
                $volumeMt = $volumeKg / 1000;

                $message = array();
                $message['diameter'] = $diameter;
                $message['length'] = $length;
                $message['height'] = $height;
                $message['volumeLitres'] = $volumeLitres;
                $message['volumeKg'] = $volumeKg;
                $message['volumeMt'] = $volumeMt;
            }
        }
    }
    
    echo json_encode(
        array(
            "status" => "success",
            "message" => $message
        ));
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
        ));
}
?>