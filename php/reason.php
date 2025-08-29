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
if (isset($_POST['reason'])) {

    if (empty($_POST["id"])) {
        $reasonId = null;
    } else {
        $reasonId = trim($_POST["id"]);
    }

    if (empty($_POST["reason"])) {
        $reason = null;
    } else {
        $reason = trim($_POST["reason"]);
    }

    if(! empty($reasonId))
    {
        if ($update_stmt = $db->prepare("UPDATE Reasons SET reason=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sss', $reason, $username, $reasonId);

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
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }

            $update_stmt->close();
        }
    }
    else
    {
        if ($insert_stmt = $db->prepare("INSERT INTO Reasons (reason, created_by) VALUES (?, ?)")) {
            $insert_stmt->bind_param('ss', $reason, $username);

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