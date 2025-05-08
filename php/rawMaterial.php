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

    if (empty($_POST["productPrice"])) {
        $productPrice = '0.00';
    } else {
        $productPrice = trim($_POST["productPrice"]);
    }

    if (empty($_POST["varianceType"])) {
        $varianceType = null;
    } else {
        $varianceType = trim($_POST["varianceType"]);
    }

    if (empty($_POST["high"])) {
        $high = null;
    } else {
        $high = trim($_POST["high"]);
    }

    if (empty($_POST["low"])) {
        $low = null;
    } else {
        $low = trim($_POST["low"]);
    }

    if (empty($_POST["type"])) {
        $type = null;
    } else {
        $type = trim($_POST["type"]);
    }

    if (empty($_POST["basicUom"])) {
        $basicUom = null;
    } else {
        $basicUom = trim($_POST["basicUom"]);
    }

    if(! empty($productId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Raw_Mat SET raw_mat_code=?, name=?, price=?, description=?, variance=?, high=?, low=?, basic_uom=?, type=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('ssssssssssss', $productCode, $productName, $productPrice, $description, $varianceType, $high, $low, $basicUom, $type, $username, $username, $productId);

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
                # Raw_Mat_UOM
                if (isset($_POST['uomNo'])){
                    $uomNo = $_POST['uomNo'];
                    $uomId = $_POST['uomId'];
                    $uom =  $_POST['uom'];
                    $rate = $_POST['rate'];
                    $deleteStatus = 1;
                    if(isset($uomNo) && $uomNo != null && count($uomNo) > 0){
                        # Delete all existing product uom records tied to the product id then reinsert
                        if ($delete_stmt = $db->prepare("UPDATE Raw_Mat_UOM SET status=? WHERE raw_mat_id=?")){
                            $delete_stmt->bind_param('ss', $deleteStatus, $productId);
    
                            // Execute the prepared query.
                            if (! $delete_stmt->execute()) {
                                echo json_encode(
                                    array(
                                        "status"=> "failed", 
                                        "message"=> $delete_stmt->error
                                    )
                                );
                            }
                            else{

                                foreach ($uomNo as $key => $no) {
                                    if ($product_stmt = $db->prepare("INSERT INTO Raw_Mat_UOM (raw_mat_id, unit_id, rate) VALUES (?, ?, ?)")){
                                        $product_stmt->bind_param('sss', $productId, $uom[$key], $rate[$key]);
                                        $product_stmt->execute();
                                    }
                                }
                                $product_stmt->close();
                            }
                        } 
                    }
                }

                if ($insert_stmt = $db->prepare("INSERT INTO Raw_Mat_Log (raw_mat_id, raw_mat_code, name, price, description, variance, high, low, basic_uom, type, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('ssssssssssss', $productId, $productCode, $productName, $productPrice, $description, $varianceType, $high, $low, $basicUom, $type, $action, $username);
        
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
        if ($insert_stmt = $db->prepare("INSERT INTO Raw_Mat (raw_mat_code, name, price, description, variance, high, low, basic_uom, type, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssss', $productCode, $productName,  $productPrice, $description, $varianceType, $high, $low, $basicUom, $type, $username, $username);

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

                # Raw_Mat_UOM
                if (isset($_POST['uomNo'])){
                    $uomNo = $_POST['uomNo'];
                    $uomId = $_POST['uomId'];
                    $uom =  $_POST['uom'];
                    $rate = $_POST['rate'];
                    $deleteStatus = 1;
                    if(isset($uomNo) && $uomNo != null && count($uomNo) > 0){
                        # Delete all existing product uom records tied to the product id then reinsert
                        if ($delete_stmt = $db->prepare("UPDATE Raw_Mat_UOM SET status=? WHERE raw_mat_id=?")){
                            $delete_stmt->bind_param('ss', $deleteStatus, $productId);
    
                            // Execute the prepared query.
                            if (! $delete_stmt->execute()) {
                                echo json_encode(
                                    array(
                                        "status"=> "failed", 
                                        "message"=> $delete_stmt->error
                                    )
                                );
                            }
                            else{

                                foreach ($uomNo as $key => $no) {
                                    if ($product_stmt = $db->prepare("INSERT INTO Raw_Mat_UOM (raw_mat_id, unit_id, rate) VALUES (?, ?, ?)")){
                                        $product_stmt->bind_param('sss', $productId, $uom[$key], $rate[$key]);
                                        $product_stmt->execute();
                                    }
                                }
                                $product_stmt->close();
                            }
                        } 
                    }
                }
                
                $sel = mysqli_query($db,"select count(*) as allcount from Product");
                $records = mysqli_fetch_assoc($sel);
                $totalRecords = $records['allcount'];

                if ($insert_log = $db->prepare("INSERT INTO Raw_Mat_Log (raw_mat_id, raw_mat_code, name, price, description, variance, high, low, basic_uom, type, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_log->bind_param('ssssssssssss', $totalRecords, $productCode, $productName, $productPrice, $description, $varianceType, $high, $low, $basicUom,  $type, $action, $username);
        
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