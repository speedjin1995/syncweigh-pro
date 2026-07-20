<?php
session_start();
require_once "db_connect.php";
require_once "requires/lookup.php";

if(isset($_POST['declarationDate'], $_POST['plantId'], $_POST['batchDrum'])){
    $declarationDate = filter_input(INPUT_POST, 'declarationDate', FILTER_SANITIZE_STRING);
    $formattedDate = DateTime::createFromFormat('d-m-Y H:i', $declarationDate)->format('Y-m-d H:i:s');
    $plantId = filter_input(INPUT_POST, 'plantId', FILTER_SANITIZE_STRING);
    $batchDrum = filter_input(INPUT_POST, 'batchDrum', FILTER_SANITIZE_STRING);

    if ($stmt = $db->prepare("SELECT * FROM Bitumen WHERE plant_id=? AND batch_drum=? AND declaration_datetime<? AND ((JSON_EXTRACT(diesel, '$.totalDiesel') IS NOT NULL AND JSON_EXTRACT(diesel, '$.totalDiesel') > 0) OR (JSON_EXTRACT(lfo, '$.totalLfo') IS NOT NULL AND JSON_EXTRACT(lfo, '$.totalLfo') > 0)) ORDER BY declaration_datetime DESC LIMIT 1")) {
        $stmt->bind_param('sss', $plantId, $batchDrum, $formattedDate);
        
        // Execute the prepared query.
        if (! $stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $stmt->get_result();
            $message = array();
            
            while ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['declaration_datetime'] = $row['declaration_datetime'];

                ## lfo Processing ##
                $lfo = json_decode($row['lfo'], true);
                
                $message['previous_lfo'] = $lfo['totalLfoLitre'] ?? 0;

                ## diesel Processing ##
                $diesel = json_decode($row['diesel'], true);
                $message['previous_diesel'] = $diesel['totalDiesel'] ?? 0;
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
    }
    else{
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Something went wrong"
                )); 
    }

    $stmt->close();
    $db->close();
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>