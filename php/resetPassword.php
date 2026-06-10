<?php
session_start();
require_once "db_connect.php";

if(!isset($_SESSION['username'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['userID'])){
    $name = $_SESSION["username"];
    $userId = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $password = "123456";
    $param_password = password_hash($password, PASSWORD_DEFAULT);
    $param_token = bin2hex(random_bytes(50));
    $param_modified_by = $name;

    if ($update_stmt = $db->prepare("UPDATE Users SET password=?, token=?, modified_by=? WHERE id=?")) {
        $update_stmt->bind_param('ssss', $param_password, $param_token, $param_modified_by, $userId);

        if (!$update_stmt->execute()) {
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
                    "message"=> "Password reset to 123456 successfully!" 
                )
            );
        }

        $update_stmt->close();
        $db->close();
    }
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>