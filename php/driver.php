<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];

// Processing form data when form is submitted
if (isset($_POST['driverCode'])) {

    if (empty($_POST["id"])) {
        $driverId = null;
    } else {
        $driverId = trim($_POST["id"]);
    }

    if (empty($_POST["driverCode"])) {
        $driverCode = null;
    } else {
        $driverCode = trim($_POST["driverCode"]);
    }

    if (empty($_POST["driverIC"])) {
        $driverIC = null;
    } else {
        $driverIC = trim($_POST["driverIC"]);
    }

    if (empty($_POST["driverName"])) {
        $driverName = null;
    } else {
        $driverName = trim($_POST["driverName"]);
    }
    
    if(! empty($driverId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Driver SET driver_code=?, driver_name=?, driver_ic=? , modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssss', $driverCode, $driverName, $driverIC, $username, $driverId);

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
                if ($insert_stmt = $db->prepare("INSERT INTO Driver_Log (driver_id, driver_code, driver_name, driver_ic, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssss', $driverId, $driverCode, $driverName, $driverIC, $action, $username);
        
                    // Execute the prepared query.
                    if (! $insert_stmt->execute()) {
                        // echo json_encode(
                        //     array(
                        //         "status"=> "failed", 
                        //         "message"=> $insert_stmt->error
                        //     )
                        // );
                    }
                    else{
                        $insert_stmt->close();
                        
                        // echo json_encode(
                        //     array(
                        //         "status"=> "success", 
                        //         "message"=> "Added Successfully!!" 
                        //     )
                        // );
                    }

                    $update_stmt->close();
                    $db->close();

                    echo json_encode(
                        array(
                            "status"=> "success", 
                            "message"=> "Updated Successfully!!" 
                        )
                    );
                }
            }
        }
    }
    else
    {
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Driver (driver_code, driver_name, driver_ic, created_by, modified_by) VALUES (?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssss', $driverCode, $driverName, $driverIC, $username, $username);

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

                $sel = mysqli_query($db,"SELECT id FROM Driver ORDER BY id DESC LIMIT 1");
                $records = mysqli_fetch_assoc($sel);
                $driverIdAudit = $records['id'];

                if ($insert_log = $db->prepare("INSERT INTO Driver_Log (driver_id, driver_code, driver_name, driver_ic, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssss', $driverIdAudit, $driverCode, $driverName, $driverIC, $action, $username);
        
                    // Execute the prepared query.
                    if (! $insert_log->execute()) {
                        // echo json_encode(
                        //     array(
                        //         "status"=> "failed", 
                        //         "message"=> $insert_stmt->error
                        //     )
                        // );
                    }
                    else{
                        $insert_log->close();
                        // echo json_encode(
                        //     array(
                        //         "status"=> "success", 
                        //         "message"=> "Added Successfully!!" 
                        //     )
                        // );
                    }
                }

                $insert_stmt->close();
                $db->close();
            }
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