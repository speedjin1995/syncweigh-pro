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
if (isset($_POST['transactionId'], $_POST['transactionStatus'], $_POST['weightType'], $_POST['transactionDate'], $_POST['supplierWeight'], $_POST['grossIncoming'], $_POST['grossIncomingDate']
, $_POST['tareOutgoing'], $_POST['tareOutgoingDate'], $_POST['nettWeight'], $_POST['manualWeight'], $_POST['weighbridge'], $_POST['indicatorId'])) {

    if (empty($_POST["id"])) {
        $weightId = null;
    } else {
        $weightId = trim($_POST["id"]);
    }

    if (empty($_POST["transactionId"])) {
        $transactionId = null;
    } else {
        $transactionId = trim($_POST["transactionId"]);
    }

    if (empty($_POST["transactionStatus"])) {
        $transactionStatus = null;
    } else {
        $transactionStatus = trim($_POST["transactionStatus"]);
    }

    if (empty($_POST["weightType"])) {
        $weightType = null;
    } else {
        $weightType = trim($_POST["weightType"]);
    }

    if (empty($_POST["transactionDate"])) {
        $transactionDate = null;
    } else {
        $transactionDate = trim($_POST["transactionDate"]);
    }

    if (empty($_POST["supplierWeight"])) {
        $supplierWeight = null;
    } else {
        $supplierWeight = trim($_POST["supplierWeight"]);
    }

    if (empty($_POST["grossIncoming"])) {
        $grossIncoming = null;
    } else {
        $grossIncoming = trim($_POST["grossIncoming"]);
    }

    if (empty($_POST["grossIncomingDate"])) {
        $grossIncomingDate = null;
    } else {
        $grossIncomingDate = trim($_POST["grossIncomingDate"]);
    }

    if (empty($_POST["tareOutgoing"])) {
        $tareOutgoing = null;
    } else {
        $tareOutgoing = trim($_POST["tareOutgoing"]);
    }

    if (empty($_POST["tareOutgoingDate"])) {
        $tareOutgoingDate = null;
    } else {
        $tareOutgoingDate = trim($_POST["tareOutgoingDate"]);
    }

    if (empty($_POST["nettWeight"])) {
        $nettWeight = null;
    } else {
        $nettWeight = trim($_POST["nettWeight"]);
    }

    if (empty($_POST["manualWeight"])) {
        $manualWeight = null;
    } else {
        $manualWeight = trim($_POST["manualWeight"]);
    }

    if (empty($_POST["weighbridge"])) {
        $weighbridge = null;
    } else {
        $weighbridge = trim($_POST["weighbridge"]);
    }

    if (empty($_POST["indicatorId"])) {
        $indicatorId = null;
    } else {
        $indicatorId = trim($_POST["indicatorId"]);
    }

    if (empty($_POST["invoiceNo"])) {
        $invoiceNo = null;
    } else {
        $invoiceNo = trim($_POST["invoiceNo"]);
    }

    if (empty($_POST["deliveryNo"])) {
        $deliveryNo = null;
    } else {
        $deliveryNo = trim($_POST["deliveryNo"]);
    }

    if (empty($_POST["purchaseOrder"])) {
        $purchaseOrder = null;
    } else {
        $purchaseOrder = trim($_POST["purchaseOrder"]);
    }

    if (empty($_POST["containerNo"])) {
        $containerNo = null;
    } else {
        $containerNo = trim($_POST["containerNo"]);
    }

    if (empty($_POST["customerName"])) {
        $customerName = null;
    } else {
        $customerName = trim($_POST["customerName"]);
    }

    if (empty($_POST["productName"])) {
        $productName = null;
    } else {
        $productName = trim($_POST["productName"]);
    }

    if (empty($_POST["transporter"])) {
        $transporter = null;
    } else {
        $transporter = trim($_POST["transporter"]);
    }

    if (empty($_POST["weightDifference"])) {
        $weightDifference = null;
    } else {
        $weightDifference = trim($_POST["weightDifference"]);
    }
    
    if (empty($_POST["destination"])) {
        $destination = null;
    } else {
        $destination = trim($_POST["destination"]);
    }

    if (empty($_POST["reduceWeight"])) {
        $reduceWeight = null;
    } else {
        $reduceWeight = trim($_POST["reduceWeight"]);
    }

    if (empty($_POST["totalPrice"])) {
        $totalPrice = null;
    } else {
        $totalPrice = trim($_POST["totalPrice"]);
    }

    if (empty($_POST["otherRemarks"])) {
        $otherRemarks = null;
    } else {
        $otherRemarks = trim($_POST["otherRemarks"]);
    }

    // container
    if (empty($_POST["vehiclePlateNo2"])) {
        $vehiclePlateNo2 = null;
    } else {
        $vehiclePlateNo2 = trim($_POST["vehiclePlateNo2"]);
    }

    if (empty($_POST["grossIncoming2"])) {
        $grossIncoming2 = null;
    } else {
        $grossIncoming2 = trim($_POST["grossIncoming2"]);
    }

    if (empty($_POST["grossIncomingDate2"])) {
        $grossIncomingDate2 = null;
    } else {
        $grossIncomingDate2 = trim($_POST["grossIncomingDate2"]);
    }

    if (empty($_POST["tareOutgoing2"])) {
        $tareOutgoing2 = null;
    } else {
        $tareOutgoing2 = trim($_POST["tareOutgoing2"]);
    }

    if (empty($_POST["tareOutgoingDate2"])) {
        $tareOutgoingDate2 = null;
    } else {
        $tareOutgoingDate2 = trim($_POST["tareOutgoingDate2"]);
    }

    if (empty($_POST["nettWeight2"])) {
        $nettWeight2 = null;
    } else {
        $nettWeight2 = trim($_POST["nettWeight2"]);
    }
    
    if (empty($_POST["customerCode"])) {
        $customerCode = null;
    } else {
        $customerCode = trim($_POST["customerCode"]);
    }

    if (empty($_POST["supplierCode"])) {
        $supplierCode = null;
    } else {
        $supplierCode = trim($_POST["supplierCode"]);
    }

    if (empty($_POST["supplierName"])) {
        $supplierName = null;
    } else {
        $supplierName = trim($_POST["supplierName"]);
    }

    if (empty($_POST["productCode"])) {
        $productCode = null;
    } else {
        $productCode = trim($_POST["productCode"]);
    }

    if (empty($_POST["destinationCode"])) {
        $destinationCode = null;
    } else {
        $destinationCode = trim($_POST["destinationCode"]);
    }

    if (empty($_POST["transporterCode"])) {
        $transporterCode = null;
    } else {
        $transporterCode = trim($_POST["transporterCode"]);
    }

    if (empty($_POST["finalWeight"])) {
        $finalWeight = null;
    } else {
        $finalWeight = trim($_POST["finalWeight"]);
    }

    if (empty($_POST["indicatorId2"])) {
        $indicatorId2 = null;
    } else {
        $indicatorId2 = trim($_POST["indicatorId2"]);
    }

    if (empty($_POST["productDescription"])) {
        $productDescription = null;
    } else {
        $productDescription = trim($_POST["productDescription"]);
    }

    if (empty($_POST["vehiclePlateNo1"])) {
        $vehiclePlateNo1 = null;
    } else {
        $vehiclePlateNo1 = trim($_POST["vehiclePlateNo1"]);
    }

    if(filter_has_var(INPUT_POST,'manualVehicle')) {
        $vehiclePlateNo1 = trim($_POST["vehicleNoTxt"]);
    }
    


    if(! empty($weightId))
    {
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Weight SET transaction_id=?, transaction_status=?, weight_type=?, transaction_date=?, lorry_plate_no1=?, lorry_plate_no2=?, supplier_weight=?, customer_code=?, customer_name=?, supplier_code=?, supplier_name=?,
        product_code=?, product_name=?, container_no=?, invoice_no=?, purchase_order=?, delivery_no=?, transporter_code=?, transporter=?, destination_code=?, destination=?, remarks=?, gross_weight1=?, gross_weight1_date=?, tare_weight1=?, tare_weight1_date=?, nett_weight1=?,
        gross_weight2=?, gross_weight2_date=?, tare_weight2=?, tare_weight2_date=?, nett_weight2=?, reduce_weight=?, final_weight=?, weight_different=?, is_complete=?, is_cancel=?, manual_weight=?, indicator_id=?, weighbridge_id=?, created_by=?, modified_by=?, indicator_id_2=?, product_description=?
         WHERE id=?"))
        {
            $update_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $customerCode, $customerName,
            $supplierCode, $supplierName, $productCode, $productName, $containerNo, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
            $grossIncoming, $grossIncomingDate, $tareOutgoing, $tareOutgoingDate, $nettWeight, $grossIncoming2, $grossIncomingDate2, $tareOutgoing2, $tareOutgoingDate2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
            $pStatus, $pStatus, $manualWeight, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $weightId);

            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else
            {
                // if ($insert_stmt = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, vehicle_weight, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                //     $insert_stmt->bind_param('sssss', $vehicleId, $vehicleNo, $vehicleWeight, $action, $username);
        
                //     // Execute the prepared query.
                //     if (! $insert_stmt->execute()) {
                //         // echo json_encode(
                //         //     array(
                //         //         "status"=> "failed", 
                //         //         "message"=> $insert_stmt->error
                //         //     )
                //         // );
                //     }
                //     else{
                //         $insert_stmt->close();
                        
                //         // echo json_encode(
                //         //     array(
                //         //         "status"=> "success", 
                //         //         "message"=> "Added Successfully!!" 
                //         //     )
                //         // );
                //     }

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

        if($_POST['grossIncomingDate'] != null && $_POST['grossIncomingDate'] != ''){
            // $inDate = new DateTime($_POST['grossIncomingDate']);
            // $inCDateTime = date_format($inDate,"Y-m-d H:i:s");
            $pStatus = "Pending";
        }
    
        if($_POST['tareOutgoingDate'] != null && $_POST['tareOutgoingDate'] != ''){
            // $outDate = new DateTime($_POST['tareOutgoingDate']);
            // $outGDateTime = date_format($outDate,"Y-m-d H:i:s");
            $pStatus = "Complete";
        }

        if ($insert_stmt = $db->prepare("INSERT INTO Weight (transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, customer_code, customer_name, supplier_code, supplier_name,
        product_code, product_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
        gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, manual_weight, indicator_id, weighbridge_id, created_by, modified_by, indicator_id_2, product_description) VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $customerCode, $customerName,
            $supplierCode, $supplierName, $productCode, $productName, $containerNo, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
            $grossIncoming, $grossIncomingDate, $tareOutgoing, $tareOutgoingDate, $nettWeight, $grossIncoming2, $grossIncomingDate2, $tareOutgoing2, $tareOutgoingDate2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
            $pStatus, $pStatus, $manualWeight, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription);

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

                // $sel = mysqli_query($db,"select count(*) as allcount from Vehicle");
                // $records = mysqli_fetch_assoc($sel);
                // $totalRecords = $records['allcount'];

                // if ($insert_log = $db->prepare("INSERT INTO Vehicle_Log (vehicle_id, veh_number, vehicle_weight, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                //     $insert_log->bind_param('sssss', $totalRecords, $vehicleNo, $vehicleWeight, $action, $username);
        
                //     // Execute the prepared query.
                //     if (! $insert_log->execute()) {
                //         // echo json_encode(
                //         //     array(
                //         //         "status"=> "failed", 
                //         //         "message"=> $insert_stmt->error
                //         //     )
                //         // );
                //     }
                //     else{
                //         $insert_log->close();
                //         // echo json_encode(
                //         //     array(
                //         //         "status"=> "success", 
                //         //         "message"=> "Added Successfully!!" 
                //         //     )
                //         // );
                //     }
                // }

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