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
$today = date('ym');

// Processing form data when form is submitted
if (isset($_POST['transactionStatus'], $_POST['weightType'], $_POST['transactionDate'], $_POST['grossIncoming'], $_POST['grossIncomingDate']
, $_POST['nettWeight'], $_POST['manualWeight'], $_POST['plantCode'])) {
    $isCancel = 'N';
    $isComplete = 'N';
    $isApproved = 'Y';

    if (empty($_POST["id"])) {
        $weightId = null;
    } else {
        $weightId = trim($_POST["id"]);
    }

    if (empty($_POST["plantCode"])) {
        $plantCode = null;
    } else {
        $plantCode = trim($_POST["plantCode"]);
    }

    if (empty($_POST["plant"])) {
        $plant = null;
    } else {
        $plant = trim($_POST["plant"]);
    }

    if (empty($_POST["transactionId"])) {
        $status = $_POST['transactionStatus'];

		if($update_stmt2 = $db->prepare("SELECT * FROM status WHERE status=?")){
			$update_stmt2->bind_param('s', $status);

			if (! $update_stmt2->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong when pulling status"
                    )
                ); 
            }
            else{
                $result2 = $update_stmt2->get_result();
				$id = '1';
				$transactionId = $plantCode.'/'.$today.'-';

				if ($row2 = $result2->fetch_assoc()) {
					$id = $row2['misc_id'];
					$transactionId .= $row2['prefix'];
				} 

				if ($update_stmt = $db->prepare("SELECT * FROM miscellaneous WHERE id=?")) {
					$update_stmt->bind_param('s', $id);
					
					// Execute the prepared query.
					if (! $update_stmt->execute()) {
						echo json_encode(
							array(
								"status" => "failed",
								"message" => "Something went wrong"
							)); 
					}
					else{
						$result = $update_stmt->get_result();
						$message = array();
						
						if ($row = $result->fetch_assoc()) {
							$charSize = strlen($row['value']);
							$misValue = $row['value'];
		
							for($i=0; $i<(4-(int)$charSize); $i++){
								$transactionId.='0';  // S0000
							}
					
							$transactionId .= $misValue;  //S00009
                        }
                    }
                }
            }
		}
    } 
    else {
        $transactionId = trim($_POST["transactionId"]);
    }

    if (empty($_POST["transactionStatus"])) {
        $transactionStatus = null;
    } else {
        $transactionStatus = trim($_POST["transactionStatus"]);
    }

    if (empty($_POST["weightType"])) {
        $weightType = 'Normal';
    } else {
        $weightType = trim($_POST["weightType"]);
    }

    if (empty($_POST["transactionDate"])) {
        $transactionDate = null;
    } else {
        $transactionDate = DateTime::createFromFormat('d-m-Y', $_POST["transactionDate"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["supplierWeight"])) {
        $supplierWeight = null;
    } else {
        $supplierWeight = trim($_POST["supplierWeight"]);
    }

    if (empty($_POST["orderWeight"])) {
        $orderWeight = null;
    } else {
        $orderWeight = trim($_POST["orderWeight"]);
    }

    if (empty($_POST["grossIncoming"])) {
        $grossIncoming = 0;
    } else {
        $grossIncoming = trim($_POST["grossIncoming"]);
    }

    if (empty($_POST["grossIncomingDate"])) {
        $grossIncomingDate = null;
    } 
    else {
        $grossIncomingDate = trim(str_replace(["AM", "PM"], "", $_POST["grossIncomingDate"]));
        $grossIncomingDate = DateTime::createFromFormat('d/m/Y H:i:s', $grossIncomingDate)->format('Y-m-d H:i:s');
    }

    if (empty($_POST["tareOutgoing"])) {
        $tareOutgoing = 0;
    } else {
        $tareOutgoing = trim($_POST["tareOutgoing"]);
    }

    if (empty($_POST["tareOutgoingDate"])) {
        $tareOutgoingDate = null;
    } else {
        $tareOutgoingDate = trim(str_replace(["AM", "PM"], "", $_POST["tareOutgoingDate"]));
        $tareOutgoingDate = DateTime::createFromFormat('d/m/Y H:i:s', $tareOutgoingDate)->format('Y-m-d H:i:s');
    }

    if (empty($_POST["nettWeight"])) {
        $nettWeight = 0;
    } else {
        $nettWeight = trim($_POST["nettWeight"]);
    }

    if (empty($_POST["manualWeight"])) {
        $manualWeight = null;
    } else {
        $manualWeight = trim($_POST["manualWeight"]);
    }

    if (empty($_POST["manualPrice"])) {
        $manualPrice = null;
    } else {
        $manualPrice = trim($_POST["manualPrice"]);
    }

    if (empty($_POST["weighbridge"])) {
        $weighbridge = 'Weigh1';
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
        $reduceWeight = '0';
    } else {
        $reduceWeight = trim($_POST["reduceWeight"]);
    }

    if (empty($_POST["remarks"])) {
        $otherRemarks = null;
    } else {
        $otherRemarks = trim($_POST["remarks"]);
    }

    if (empty($_POST["driverName"])) {
        $driverName = null;
    } else {
        $driverName = trim($_POST["driverName"]);
    }

    if (empty($_POST["driverCode"])) {
        $driverCode = null;
    } else {
        $driverCode = trim($_POST["driverCode"]);
    }

    if (empty($_POST["driverPhone"])) {
        $driverPhone = null;
    } else {
        $driverPhone = trim($_POST["driverPhone"]);
    }

    if (empty($_POST["driverICNo"])) {
        $driverICNo = null;
    } else {
        $driverICNo = trim($_POST["driverICNo"]);
    }

    if (empty($_POST["estimateLoading"])) {
        $estimateLoading = '0';
    } else {
        $estimateLoading = trim($_POST["estimateLoading"]);
    }

    if (empty($_POST["totalPrice"])) {
        $totalPrice = '0';
    } else {
        $totalPrice = trim($_POST["totalPrice"]);
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
        $grossIncomingDate2 = DateTime::createFromFormat('d-m-Y', $_POST["grossIncomingDate2"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["tareOutgoing2"])) {
        $tareOutgoing2 = null;
    } else {
        $tareOutgoing2 = trim($_POST["tareOutgoing2"]);
    }

    if (empty($_POST["tareOutgoingDate2"])) {
        $tareOutgoingDate2 = null;
    } else {
        $tareOutgoingDate2 = DateTime::createFromFormat('d-m-Y', $_POST["tareOutgoingDate2"])->format('Y-m-d H:i:s');
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
        $finalWeight = '0';
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

    if($weightType == 'Normal' && ($grossIncoming != null && $tareOutgoing != null)){
        $isComplete = 'Y';
    }
    else if($weightType == 'Container' && ($grossIncoming != null && $tareOutgoing != null && $grossIncoming2 != null && $tareOutgoing2 != null)){
        $isComplete = 'Y';
    }
    else{
        $isComplete = 'N';
    }

    if(isset($_POST['status']) && $_POST['status'] != null && $_POST['status'] != ''){
        if($_POST['status'] == 'pending'){
            $isComplete = 'N';
            $isApproved = 'N';
        }
    }

    if(isset($_POST['bypassReason']) && $_POST['bypassReason'] != null && $_POST['bypassReason'] != ''){
        $approved_reason = $_POST['bypassReason'];
    }
    else{
        $approved_reason = null;
    }

    /*if($_POST['grossIncomingDate'] != null && $_POST['grossIncomingDate'] != ''){
        // $inDate = new DateTime($_POST['grossIncomingDate']);
        // $inCDateTime = date_format($inDate,"Y-m-d H:i:s");
        $pStatus = "Pending";
    }

    if($_POST['tareOutgoingDate'] != null && $_POST['tareOutgoingDate'] != ''){
        // $outDate = new DateTime($_POST['tareOutgoingDate']);
        // $outGDateTime = date_format($outDate,"Y-m-d H:i:s");
        $pStatus = "Complete";
    }*/

    if(! empty($weightId)){
        // $sql = "UPDATE Customer SET company_reg_no=?, name=?, address_line_1=?, address_line_2=?, address_line_3=?, phone_no=?, fax_no=?, created_by=?, modified_by=? WHERE customer_code=?";
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Weight SET transaction_id=?, transaction_status=?, weight_type=?, transaction_date=?, lorry_plate_no1=?, lorry_plate_no2=?, supplier_weight=?, order_weight=?, customer_code=?, customer_name=?, supplier_code=?, supplier_name=?,
        product_code=?, product_name=?, container_no=?, invoice_no=?, purchase_order=?, delivery_no=?, transporter_code=?, transporter=?, destination_code=?, destination=?, remarks=?, gross_weight1=?, gross_weight1_date=?, tare_weight1=?, tare_weight1_date=?, nett_weight1=?,
        gross_weight2=?, gross_weight2_date=?, tare_weight2=?, tare_weight2_date=?, nett_weight2=?, reduce_weight=?, final_weight=?, weight_different=?, is_complete=?, is_cancel=?, manual_weight=?, manual_price=?, indicator_id=?, weighbridge_id=?, created_by=?, modified_by=?, indicator_id_2=?, 
        product_description=?, total_price=?, is_approved=?, approved_reason=?, plant_code=?, plant_name=?, driver_code=?, driver_name=?, driver_ic=?, driver_phone=?, estimate_loading=? WHERE id=?"))
        {
            $update_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $orderWeight, $customerCode, $customerName,
            $supplierCode, $supplierName, $productCode, $productName, $containerNo, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
            $grossIncoming, $grossIncomingDate, $tareOutgoing, $tareOutgoingDate, $nettWeight, $grossIncoming2, $grossIncomingDate2, $tareOutgoing2, $tareOutgoingDate2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
            $isComplete, $isCancel, $manualWeight, $manualPrice, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $totalPrice, $isApproved, $approved_reason, $plantCode, $plant, $driverCode, $driverName, $driverICNo, $driverPhone, $estimateLoading, $weightId);

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

                # Weight_Product 
                $no = isset($_POST['no']) ? $_POST['no']: [];
                $weightProductId = isset($_POST['weightProductId']) ? $_POST['weightProductId']: [];
                $productPartCode =  isset($_POST['productPartCode']) ? $_POST['productPartCode']: [];
                $products =isset($_POST['products']) ? $_POST['products']: [];
                $productPercentage = isset($_POST['productPercentage']) ? $_POST['productPercentage']: [];
                $productItemWeight = isset($_POST['productItemWeight']) ? $_POST['productItemWeight']: [];
                $productUnitPrice = isset($_POST['productUnitPrice']) ? $_POST['productUnitPrice']: [];
                $productTotalPrice = isset($_POST['productTotalPrice']) ? $_POST['productTotalPrice']: [];

                if(isset($no) && $no != null && count($no) > 0){ 
                    for ($i=0; $i < count($no); $i++) { 
                        if(isset($weightProductId[$i]) && $weightProductId[$i] > 0){
                            if ($product_stmt = $db->prepare("UPDATE Weight_Product SET weight_id=?, product_code=?, product_name=?, percentage=?, item_weight=?, unit_price=?, total_price=? WHERE id=?")){
                                $product_stmt->bind_param('ssssssss', $weightId, $productPartCode[$i], $products[$i], $productPercentage[$i], $productItemWeight[$i], $productUnitPrice[$i], $productTotalPrice[$i], $weightProductId[$i]);
                                $product_stmt->execute();
                            }
                        }
                        else{
                            if ($product_stmt = $db->prepare("INSERT INTO Weight_Product (weight_id, product_code, product_name, percentage, item_weight, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)")){
                                $product_stmt->bind_param('sssssss', $weightId, $productPartCode[$i], $products[$i], $productPercentage[$i], $productItemWeight[$i], $productUnitPrice[$i], $productTotalPrice[$i]);
                                $product_stmt->execute();
                            }
                        }
                    }

                    $product_stmt->close();
                }

                $update_stmt->close();
                $db->close();

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!",
                        "id"=>$weightId
                    )
                );
            }
            
        }
    }
    else{
        $action = "1";

        if ($insert_stmt = $db->prepare("INSERT INTO Weight (transaction_id, transaction_status, weight_type, transaction_date, lorry_plate_no1, lorry_plate_no2, supplier_weight, order_weight, customer_code, customer_name, supplier_code, supplier_name,
        product_code, product_name, container_no, invoice_no, purchase_order, delivery_no, transporter_code, transporter, destination_code, destination, remarks, gross_weight1, gross_weight1_date, tare_weight1, tare_weight1_date, nett_weight1,
        gross_weight2, gross_weight2_date, tare_weight2, tare_weight2_date, nett_weight2, reduce_weight, final_weight, weight_different, is_complete, is_cancel, manual_weight, manual_price, indicator_id, weighbridge_id, created_by, modified_by, indicator_id_2, 
        product_description, total_price, is_approved, approved_reason, driver_code, driver_name, driver_ic, estimate_loading, plant_code, plant_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssssssssssssss', $transactionId, $transactionStatus, $weightType, $transactionDate, $vehiclePlateNo1, $vehiclePlateNo2, $supplierWeight, $orderWeight, $customerCode, $customerName,
            $supplierCode, $supplierName, $productCode, $productName, $containerNo, $invoiceNo, $purchaseOrder, $deliveryNo, $transporterCode, $transporter, $destinationCode, $destination, $otherRemarks,
            $grossIncoming, $grossIncomingDate, $tareOutgoing, $tareOutgoingDate, $nettWeight, $grossIncoming2, $grossIncomingDate2, $tareOutgoing2, $tareOutgoingDate2, $nettWeight2, $reduceWeight, $finalWeight, $weightDifference,
            $isComplete, $isCancel, $manualWeight, $manualPrice, $indicatorId, $weighbridge, $username, $username, $indicatorId2, $productDescription, $totalPrice, $isApproved, $approved_reason, $driverCode, $driverName, $driverICNo, $estimateLoading, $plantCode, $plant);

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
                $misValue++;
                $weightId = $insert_stmt->insert_id;
                
                ///insert miscellaneous
                if ($update_stmt = $db->prepare("UPDATE miscellaneous SET value=? WHERE id=?")){
                    $update_stmt->bind_param('ss', $misValue, $id);
                    
                    // Execute the prepared query.
                    if (! $update_stmt->execute()){
        
                        echo json_encode(
                            array(
                                "status"=> "failed", 
                                "message"=> $update_stmt->error
                            )
                        );
                    } 
                    else{
                        $update_stmt->close();
                        //$db->close();

                        # Insert into Weight_Product 
                        $no = isset($_POST['no']) ? $_POST['no']: [];
                        $productPartCode =  isset($_POST['productPartCode']) ? $_POST['productPartCode']: [];
                        $products = isset($_POST['products']) ? $_POST['products']: [];
                        $productPercentage = isset($_POST['productPercentage']) ? $_POST['productPercentage']: [];
                        $productItemWeight = isset($_POST['productItemWeight']) ? $_POST['productItemWeight']: [];
                        $productUnitPrice = isset($_POST['productUnitPrice']) ? $_POST['productUnitPrice']: [];
                        $productTotalPrice = isset($_POST['productTotalPrice']) ? $_POST['productTotalPrice']: [];
        
                        if(isset($no) && $no != null && count($no) > 0){
                            for ($i=0; $i < count($no); $i++) { 
                                if ($product_stmt = $db->prepare("INSERT INTO Weight_Product (weight_id, product_code, product_name, percentage, item_weight, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)")){
                                    $product_stmt->bind_param('sssssss', $weightId, $productPartCode[$i], $products[$i], $productPercentage[$i], $productItemWeight[$i], $productUnitPrice[$i], $productTotalPrice[$i]);
                                    $product_stmt->execute();
                                }
                            }

                            $product_stmt->close();
                        }
                        
                        echo json_encode(
                            array(
                                "status"=> "success", 
                                "message"=> "Added Successfully!!" ,
                                "id"=>$weightId
                            )
                        );
                    }
                } 
                else{
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $update_stmt->error
                        )
                    );
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