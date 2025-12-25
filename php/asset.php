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
if (isset($_POST['assetType'], $_POST['assetName'], $_POST['plant'], $_POST['batchDrum'])) {
    if (empty($_POST["id"])) {
        $assetId = null;
    } else {
        $assetId = trim($_POST["id"]);
    }

    if (empty($_POST["assetType"])) {
        $assetType = null;
    } else {
        $assetType = trim($_POST["assetType"]);
    }

    if (empty($_POST["assetName"])) {
        $assetName = null;
    } else {
        $assetName = trim($_POST["assetName"]);
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

    if (empty($_POST["diameter"])) {
        $diameter = null;
    } else {
        $diameter = trim($_POST["diameter"]);
    }

    if (empty($_POST["height"])) {
        $height = null;
    } else {
        $height = trim($_POST["height"]);
    }

    if (empty($_POST["length"])) {
        $length = null;
    } else {
        $length = trim($_POST["length"]);
    }

    if(!empty($assetId))
    {
        if ($update_stmt = $db->prepare("UPDATE Assets SET type=?, name=?, plant_id=?, batch_drum=?, diameter=?, height=?, length=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssssssss', $assetType, $assetName, $plantId, $batchDrum, $diameter, $height, $length, $username, $assetId);

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
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!",
                    )
                );
            }

            $update_stmt->close();
        }
    }
    else
    {
        if ($insert_stmt = $db->prepare("INSERT INTO Assets (type, name, plant_id, batch_drum, diameter, height, length, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssss', $assetType, $assetName, $plantId, $batchDrum, $diameter, $height, $length, $username);

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

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }

            $insert_stmt->close();
        }
    }

    $db->close();
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