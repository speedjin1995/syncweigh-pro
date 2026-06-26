<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

    if (empty($_POST["description"])) {
        $description = null;
    } else {
        $description = trim($_POST["description"]);
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

    if (empty($_POST["basicUom"])) {
        $basicUom = null;
    } else {
        $basicUom = trim($_POST["basicUom"]);
    } 

    if (empty($_POST["type"])) {
        $type = null;
    } else {
        $type = trim($_POST["type"]);
    } 

    if(! empty($productId))
    {
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Product SET product_code=?, name=?, price=?, description=?, variance=?, high=?, low=?, basic_uom=?, type=?, created_by=?, modified_by=? WHERE id=?")) 
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
                # Product_RawMat 
                if (isset($_POST['no'])){
                    $no = $_POST['no'];
                    $productRawMatCode = $_POST['productRawMatCode'];
                    $rawMats =  $_POST['rawMats'];
                    $rawMatBasicUom = $_POST['rawMatBasicUom'];
                    $rawMatBasicUomUnitId = $_POST['rawMatBasicUomUnitId'];
                    $rawMatWeight = $_POST['rawMatWeight'];
                    $plant = $_POST['plant'];
                    // $plantCode = $_POST['plantCode'];
                    // $plantName = $_POST['plantName'];
                    $batchDrum = $_POST['batchDrum'];
                    $deleteStatus = 1;
                    if(isset($no) && $no != null && count($no) > 0){
                        // Fetch all Bitumen raw mat IDs once
                        $bitumenRawMatIds = [];
                        $placeholders = implode(',', array_fill(0, count($rawMats), '?'));
                        $types = str_repeat('s', count($rawMats));
                        if ($bm_stmt = $db->prepare("SELECT id FROM Raw_Mat WHERE id IN ($placeholders) AND type = 'Bitumen'")) {
                            $bm_stmt->bind_param($types, ...array_values($rawMats));
                            $bm_stmt->execute();
                            $bm_result = $bm_stmt->get_result();
                            while ($bm_row = $bm_result->fetch_assoc()) {
                                $bitumenRawMatIds[$bm_row['id']] = true;
                            }
                            $bm_stmt->close();
                        }

                        $stl_stmt = $db->prepare("INSERT INTO Stock_Take_List (plant_id, product_id, batch_drum, sort) SELECT ?, ?, ?, ? FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Stock_Take_List WHERE plant_id = ? AND product_id = ? AND batch_drum = ?)");
                        $sort = 1;

                        # Delete all existing product rawmat records tied to the product id then reinsert
                        if ($delete_stmt = $db->prepare("UPDATE Product_RawMat SET status=? WHERE product_id=?")){
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
                                foreach ($no as $key => $rawMatNo) {
                                    if ($product_stmt = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")){
                                        $product_stmt->bind_param('ssssssss', $productId, $rawMats[$key], $productRawMatCode[$key], $rawMatBasicUom[$key], $rawMatBasicUomUnitId[$key], $rawMatWeight[$key], $plant[$key], $batchDrum[$key]);
                                        $product_stmt->execute();
                                    }

                                    if (isset($bitumenRawMatIds[$rawMats[$key]]) && $stl_stmt) {
                                        $stl_stmt->bind_param('sssssss', $plant[$key], $productId, $batchDrum[$key], $sort, $plant[$key], $productId, $batchDrum[$key]);
                                        $stl_stmt->execute();
                                        $sort++;
                                    }
                                }

                                if ($stl_stmt){
                                    $stl_stmt->close();
                                }
                            }
                        } 
                    }
                }

                # Product_UOM
                if (isset($_POST['uomNo'])){
                    $uomNo = $_POST['uomNo'];
                    $uomId = $_POST['uomId'];
                    $uom =  $_POST['uom'];
                    $rate = $_POST['rate'];
                    $deleteStatus = 1;
                    if(isset($uomNo) && $uomNo != null && count($uomNo) > 0){
                        # Delete all existing product uom records tied to the product id then reinsert
                        if ($delete_stmt = $db->prepare("UPDATE Product_UOM SET status=? WHERE product_id=?")){
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
                                    if ($product_stmt = $db->prepare("INSERT INTO Product_UOM (product_id, unit_id, rate) VALUES (?, ?, ?)")){
                                        $product_stmt->bind_param('sss', $productId, $uom[$key], $rate[$key]);
                                        $product_stmt->execute();
                                    }
                                }
                                $product_stmt->close();
                            }
                        } 
                    }
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
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Product (product_code, name, price, description, variance, high, low, basic_uom, type, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
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
                $productId = $insert_stmt->insert_id;

                # Product_RawMat 
                if(isset($_POST['no'])){
                    $no = $_POST['no'];
                    $productRawMatCode = $_POST['productRawMatCode'];
                    $rawMats =  $_POST['rawMats'];
                    $rawMatBasicUom = $_POST['rawMatBasicUom'];
                    $rawMatBasicUomUnitId = $_POST['rawMatBasicUomUnitId'];
                    $rawMatWeight = $_POST['rawMatWeight'];
                    $plant = $_POST['plant'];
                    // $plantCode = $_POST['plantCode'];
                    // $plantName = $_POST['plantName'];
                    $batchDrum = $_POST['batchDrum'];

                    if(isset($no) && $no != null && count($no) > 0){
                        // Fetch all Bitumen raw mat IDs once
                        $bitumenRawMatIds = [];
                        $placeholders = implode(',', array_fill(0, count($rawMats), '?'));
                        $types = str_repeat('s', count($rawMats));
                        if ($bm_stmt = $db->prepare("SELECT id FROM Raw_Mat WHERE id IN ($placeholders) AND type = 'Bitumen'")) {
                            $bm_stmt->bind_param($types, ...array_values($rawMats));
                            $bm_stmt->execute();
                            $bm_result = $bm_stmt->get_result();
                            while ($bm_row = $bm_result->fetch_assoc()) {
                                $bitumenRawMatIds[$bm_row['id']] = true;
                            }
                            $bm_stmt->close();
                        }

                        $stl_stmt = $db->prepare("INSERT INTO Stock_Take_List (plant_id, product_id, batch_drum, sort) SELECT ?, ?, ?, ? FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Stock_Take_List WHERE plant_id = ? AND product_id = ? AND batch_drum = ?)");
                        $sort = 1;

                        foreach ($no as $key => $rawMatNo) {
                            if ($product_stmt = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")){
                                $product_stmt->bind_param('ssssssss', $productId, $rawMats[$key], $productRawMatCode[$key], $rawMatBasicUom[$key], $rawMatBasicUomUnitId[$key], $rawMatWeight[$key], $plant[$key], $batchDrum[$key]);
                                $product_stmt->execute();
                                $product_stmt->close();
                            }

                            if (isset($bitumenRawMatIds[$rawMats[$key]]) && $stl_stmt) {
                                $stl_stmt->bind_param('sssssss', $plant[$key], $productId, $batchDrum[$key], $sort, $plant[$key], $productId, $batchDrum[$key]);
                                $stl_stmt->execute();
                                $sort++;
                            }
                        }

                        if ($stl_stmt){
                            $stl_stmt->close();
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