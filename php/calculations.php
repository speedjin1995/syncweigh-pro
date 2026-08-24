<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];

// Processing form data when form is submitted
if (isset($_POST['type'], $_POST['plant'], $_POST['batchDrum'])) {
    if (empty($_POST["id"])) {
        $calculationId = null;
    } else {
        $calculationId = trim($_POST["id"]);
    }

    if (empty($_POST["plant"])) {
        $plantId = null;
    } else {
        $plantId = trim($_POST["plant"]);
    }

    if (empty($_POST["batchDrum"])) {
        $batchDrum = null;
    } else {
        $batchDrum = trim($_POST["batchDrum"]);
    }

    if (empty($_POST["type"])) {
        $type = null;
    } else {
        $type = trim($_POST["type"]);
    }
    
    if(! empty($calculationId))
    {
        if ($update_stmt = $db->prepare("UPDATE Calculations SET plant_id=?, batch_drum=?, type=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssss', $plantId, $batchDrum, $type, $username, $calculationId);

            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                # Calculation Values Update
                $deleteStatus = 1;
                if ($delete_stmt = $db->prepare("UPDATE Calculation_Value SET deleted=? WHERE calculation_id=?")){
                    $delete_stmt->bind_param('ss', $deleteStatus, $calculationId);
                    if (!$delete_stmt->execute()) {
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $delete_stmt->error
                            )
                        );
                        exit;
                    }else{
                         # Insert new values
                        if ($type == 'BITULEVEL' && isset($_POST['levelNo'])) {
                            $levelMm = $_POST['levelMm'];
                            $volume = $_POST['volume'];
                            $stmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, `level`, volume) VALUES (?, ?, ?)");
                            foreach ($_POST['levelNo'] as $key => $levelNo) {
                                $stmt->bind_param('sss', $calculationId, $levelMm[$key], $volume[$key]);
                                $stmt->execute();
                            }
                            $stmt->close();
                        } elseif ($type == 'BITUSG' && isset($_POST['sgNo'])) {
                            $temperature = $_POST['temperature'];
                            $sg = $_POST['sg'];
                            $stmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, temperature, sg) VALUES (?, ?, ?)");
                            foreach ($_POST['sgNo'] as $key => $sgNo) {
                                $stmt->bind_param('sss', $calculationId, $temperature[$key], $sg[$key]);
                                $stmt->execute();
                            }
                            $stmt->close();
                        } elseif ($type == 'LFOLOOKUP' && isset($_POST['lfoLookupNo'])) {
                            $lfoLookupDepth = $_POST['lfoLookupDepth'];
                            $lfoLookupLitre = $_POST['lfoLookupLitre'];
                            $stmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, `level`, volume) VALUES (?, ?, ?)");
                            foreach ($_POST['lfoLookupNo'] as $key => $lfoLookupNo) {
                                $stmt->bind_param('sss', $calculationId, $lfoLookupDepth[$key], $lfoLookupLitre[$key]);
                                $stmt->execute();
                            }
                            $stmt->close();
                        } elseif ($type == 'DIESELLOOKUP' && isset($_POST['dieselLookupNo'])) {
                            $dieselLookupDepth = $_POST['dieselLookupDepth'];
                            $dieselLookupLitre = $_POST['dieselLookupLitre'];
                            $stmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, `level`, volume) VALUES (?, ?, ?)");
                            foreach ($_POST['dieselLookupNo'] as $key => $dieselLookupNo) {
                                $stmt->bind_param('sss', $calculationId, $dieselLookupDepth[$key], $dieselLookupLitre[$key]);
                                $stmt->execute();
                            }
                            $stmt->close();
                        }
                    }
                    $delete_stmt->close();
                }

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }

            $update_stmt->close();
            $db->close();
        }
    }
    else
    {
        if ($insert_stmt = $db->prepare("INSERT INTO Calculations (plant_id, batch_drum, type, created_by) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $plantId, $batchDrum, $type, $username);

            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                $calculationId = $insert_stmt->insert_id;

                # Calculation Values Insertion
                if ($type == 'BITULEVEL') {
                    if(isset($_POST['levelNo'])){
                        $no = $_POST['levelNo'];
                        $levelMm = $_POST['levelMm'];
                        $volume = $_POST['volume'];

                        if(isset($no) && $no != null && count($no) > 0){
                            foreach ($no as $key => $levelNo) {
                                if ($calculation_value_stmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, `level`, volume) VALUES (?, ?, ?)")){
                                    $calculation_value_stmt->bind_param('sss', $calculationId, $levelMm[$key], $volume[$key]);
                                    $calculation_value_stmt->execute();
                                    $calculation_value_stmt->close();
                                }
                            }
                        }
                    }
                }elseif($type == 'BITUSG'){
                    if (isset($_POST['sgNo'])) {
                        $no = $_POST['sgNo'];
                        $temperature = $_POST['temperature'];
                        $sg = $_POST['sg'];

                        if(isset($no) && $no != null && count($no) > 0){
                            foreach ($no as $key => $sgNo) {
                                if ($calculation_value_stmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, temperature, sg) VALUES (?, ?, ?)")){
                                    $calculation_value_stmt->bind_param('sss', $calculationId, $temperature[$key], $sg[$key]);
                                    $calculation_value_stmt->execute();
                                    $calculation_value_stmt->close();
                                }
                            }
                        }
                    }
                }elseif($type == 'LFOLOOKUP'){
                    if (isset($_POST['lfoLookupNo'])) {
                        $no = $_POST['lfoLookupNo'];
                        $lfoLookupDepth = $_POST['lfoLookupDepth'];
                        $lfoLookupLitre = $_POST['lfoLookupLitre'];

                        if(isset($no) && $no != null && count($no) > 0){
                            foreach ($no as $key => $lookupNo) {
                                if ($calculation_value_stmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, level, volume) VALUES (?, ?, ?)")){
                                    $calculation_value_stmt->bind_param('sss', $calculationId, $lfoLookupDepth[$key], $lfoLookupLitre[$key]);
                                    $calculation_value_stmt->execute();
                                    $calculation_value_stmt->close();
                                }
                            }
                        }
                    }
                }elseif($type == 'DIESELLOOKUP'){
                    if (isset($_POST['dieselLookupNo'])) {
                        $no = $_POST['dieselLookupNo'];
                        $dieselLookupDepth = $_POST['dieselLookupDepth'];
                        $dieselLookupLitre = $_POST['dieselLookupLitre'];

                        if(isset($no) && $no != null && count($no) > 0){
                            foreach ($no as $key => $lookupNo) {
                                if ($calculation_value_stmt = $db->prepare("INSERT INTO Calculation_Value (calculation_id, level, volume) VALUES (?, ?, ?)")){
                                    $calculation_value_stmt->bind_param('sss', $calculationId, $dieselLookupDepth[$key], $dieselLookupLitre[$key]);
                                    $calculation_value_stmt->execute();
                                    $calculation_value_stmt->close();
                                }
                            }
                        }
                    }
                }

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }

            $insert_stmt->close();
            $db->close();
        }
    }
    
}
else
{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>