<?php
session_start();
require_once "db_connect.php";
require_once "requires/functions.php";

if(isset($_POST['plantId'], $_POST['batchDrum'], $_POST['level'], $_POST['temp'])){
    $plantId = filter_input(INPUT_POST, 'plantId', FILTER_SANITIZE_STRING);
    $batchDrum = filter_input(INPUT_POST, 'batchDrum', FILTER_SANITIZE_STRING);
    $level = filter_input(INPUT_POST, 'level', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $temp = filter_input(INPUT_POST, 'temp', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $calculationIdSG = getCalculationId($db, $plantId, $batchDrum, 'BITUSG');
    $calculationIdLevel = getCalculationId($db, $plantId, $batchDrum, 'BITULEVEL');
    
    if (!$calculationIdSG || !$calculationIdLevel) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Invalid tank configuration"
            ));
        exit;
    }

    $volume = getVolumeFromLevel($db, $calculationIdLevel, $level);
    $sg = getSG($db, $calculationIdSG, $temp);
    $tcf = getTCF($db, $calculationIdSG, $temp);

    if ($volume === null || $sg === null) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Calculation data missing"
            ));
        exit;
    }

    $observedMT = getTonnes($volume, $sg);
    $correctedMT = $observedMT * $tcf;

    $message = array();
    $message['weight'] = $correctedMT;
    $message['volume'] = $volume;
    $message['sg'] = $sg;
    $message['tcf'] = $tcf;
    $message['observedMT'] = $observedMT;
    
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
