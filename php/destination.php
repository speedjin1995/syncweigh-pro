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
if (isset($_POST['destinationName'])) {

    if (empty($_POST["id"])) {
        $destinationId = null;
    } else {
        $destinationId = trim($_POST["id"]);
    }

    if (empty($_POST["destinationName"])) {
        $destinationName = null;
    } else {
        $destinationName = trim($_POST["destinationName"]);
    }

    $misValue='';
    if (empty($_POST["destinationCode"])) {
        $destinationCode = null;
        $code = 'destination';
        $firstChar = substr($destinationName, 0, 1);
        if (ctype_alpha($firstChar)) { //Check if letter is alphabet 
            $firstChar = strtoupper($firstChar);
        }

        // Auto gen destination code
        if($update_stmt2 = $db->prepare("SELECT * FROM miscellaneous WHERE code=? AND name=?")){
			$update_stmt2->bind_param('ss', $code, $firstChar);

			if (! $update_stmt2->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong when generating destination code"
                    )
                ); 
            }
            else{
                $result2 = $update_stmt2->get_result();
                $destinationCode = $firstChar."-";
				if ($row2 = $result2->fetch_assoc()) {
                    $charSize = strlen($row2['value']);
                    $misValue = $row2['value'];

                    for($i=0; $i<(5-(int)$charSize); $i++){
                        $destinationCode.='0';  // S0000
                    }
            
                    $destinationCode .= $misValue;  //S00009

                    $misValue++;
				}
            }
		}
    } else {
        $destinationCode = trim($_POST["destinationCode"]);
    }

    if (empty($_POST["description"])) {
        $description = null;
    } else {
        $description = trim($_POST["description"]);
    }
    
    if(! empty($destinationId))
    {
        $action = "2";
        // 1. Get old destination_code
        $oldCode = null;
        if ($stmt = $db->prepare("SELECT destination_code FROM Destination WHERE id = ?")) {
            $stmt->bind_param("s", $destinationId);
            $stmt->execute();
            $stmt->bind_result($oldCode);
            $stmt->fetch();
            $stmt->close();
        }
        
        if ($update_stmt = $db->prepare("UPDATE Destination SET destination_code=?, name=?, description=? , created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssssss', $destinationCode, $destinationName, $description, $username, $username, $destinationId);

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
            
            // 2. If code changed → update related table (example: Weight)
            if ($oldCode !== null && $oldCode !== $destinationCode) {
                if ($weight_stmt = $db->prepare("UPDATE Weight SET destination_code=? WHERE destination_code=?")) {
                    $weight_stmt->bind_param("ss", $destinationCode, $oldCode);
                    if (!$weight_stmt->execute()) {
                        throw new Exception($weight_stmt->error);
                    }
                    $weight_stmt->close();
                }
            }

            $update_stmt->close();
            $db->close();
        }
    }
    else
    {
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Destination (destination_code, name, description, created_by, modified_by) VALUES (?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssss', $destinationCode, $destinationName, $description, $username, $username);

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
                // Update Miscellaneous 
                if (!empty($misValue)){
                    if ($update_miscellaneous = $db->prepare("UPDATE miscellaneous SET value=? WHERE code=? AND name=?")) {
                        $update_miscellaneous->bind_param('sss', $misValue, $code, $firstChar);
    
                        if (! $update_miscellaneous->execute()) {
                            echo json_encode(
                                array(
                                    "status"=> "failed", 
                                    "message"=> $update_miscellaneous->error
                                )
                            );
                        }else{
                            echo json_encode(
                                array(
                                    "status"=> "success", 
                                    "message"=> "Added Successfully!!" 
                                )
                            );
                        }     
                        
                        $update_miscellaneous->close();
                    }
                }
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