<?php
session_start();
require_once "db_connect.php";
require_once "requires/functions.php";

if(isset($_POST['rawMatCode'], $_POST['diameter'], $_POST['length'], $_POST['height'])){
    $rawMatCode = filter_input(INPUT_POST, 'rawMatCode', FILTER_SANITIZE_STRING);
    $diameter = filter_input(INPUT_POST, 'diameter', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $length = filter_input(INPUT_POST, 'length', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $height = filter_input(INPUT_POST, 'height', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if ($diameter <= 0 || $length <= 0 || $height < 0) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Invalid dimensions"
            ));
        exit;
    }
    
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