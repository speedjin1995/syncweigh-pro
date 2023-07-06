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
if (isset($_POST['productCode'])) {

    if (empty($_POST["id"])) {
        $productId = null;
    } else {
        $productId = trim($_POST["id"]);
    }

    if (empty($_POST["productCode"])) {
        $productCode = null;
    } else {
        $productCode = trim($_POST["productCode"]);
    }

    if (empty($_POST["description"])) {
        $description = null;
    } else {
        $description = trim($_POST["description"]);
    }

    if (empty($_POST["productName"])) {
        $productName = null;
    } else {
        $productName = trim($_POST["productName"]);
    }
    
    if(! empty($productId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Product SET name=?, description=? , created_by=?, modified_by=? WHERE product_code=?")) 
        {
            $update_stmt->bind_param('sssss', $productName, $description, $username, $username, $productCode);

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
    else
    {
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Product (product_code, name, description, created_by, modified_by) VALUES (?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssss', $productCode, $productName, $description, $username, $username);

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
                $insert_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
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