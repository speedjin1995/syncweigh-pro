<?php
session_start();
require_once "db_connect.php";
require_once "requires/lookup.php";

if(!isset($_SESSION['id'])){
    echo json_encode(array("status" => "failed", "message" => "Session expired"));
    exit;
}

$id = null;
if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
}
else if(isset($_POST['plantId'], $_POST['batchDrum'], $_POST['type'])){
    // Stock take: find setup by plant + batch/drum + type
    $plantId = filter_input(INPUT_POST, 'plantId', FILTER_SANITIZE_STRING);
    $batchDrum = filter_input(INPUT_POST, 'batchDrum', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

    if ($find_stmt = $db->prepare("SELECT id FROM Calculations WHERE plant_id=? AND batch_drum=? AND type=? AND deleted=0 LIMIT 1")) {
        $find_stmt->bind_param('sss', $plantId, $batchDrum, $type);
        if (!$find_stmt->execute()) {
            $find_stmt->close();
            echo json_encode(array("status" => "failed", "message" => "Something went wrong"));
            exit;
        }
        $found = $find_stmt->get_result()->fetch_assoc();
        $find_stmt->close();

        if (!$found) {
            echo json_encode(array("status" => "success", "message" => array("values" => array())));
            exit;
        }
        $id = $found['id'];
    }
    else{
        echo json_encode(array("status" => "failed", "message" => "Something went wrong"));
        exit;
    }
}

if($id !== null){
    if ($update_stmt = $db->prepare("SELECT * FROM Calculations WHERE id=?")) {
        $update_stmt->bind_param('s', $id);

        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                ));
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();

            while ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['type'] = $row['type'];
                $message['plant_id'] = $row['plant_id'];
                $message['batch_drum'] = $row['batch_drum'];
            }

            // retrieve products
            $values = array();
            $count = 0;

            if ($value_stmt = $db->prepare("SELECT * FROM Calculation_Value WHERE calculation_id = ? AND deleted = '0' ORDER BY id ASC")) {
                $value_stmt->bind_param('s', $id);
                if ($value_stmt->execute()) {
                    $empRecords = $value_stmt->get_result();

                    while($row2 = $empRecords->fetch_assoc()) {
                        $values[] = array(
                            "no" => $count,
                            "id" => $row2['id'],
                            "calculation_id" => $row2['calculation_id'],
                            "level" => $row2['level'],
                            "volume" => $row2['volume'],
                            "temperature" => $row2['temperature'],
                            "sg" => $row2['sg'],
                        );

                        $count++;
                    }
                }
                $value_stmt->close();
            }

            $message['values'] = $values;

            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));
        }
        $update_stmt->close();
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            ));
}
?>
