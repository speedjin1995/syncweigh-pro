<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $format = 'MODAL';

    if (isset($_POST['format']) && $_POST['format'] != ''){
        $format = $_POST['format'];
    }

    if ($update_stmt = $db->prepare("SELECT * FROM Weight WHERE id=?")) {
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
            
            while ($row = $result->fetch_assoc()) {
                if ($format == 'EXPANDABLE'){
                    $message['id'] = $row['id'];
                    $message['name'] = '';
                    $message['tin_no'] = '';
                    $message['address_line_1'] = '';
                    $message['address_line_2'] = '';
                    $message['address_line_3'] = '';
                    $message['phone_no'] = '';
                    $message['fax_no'] = '';
                    $message['cust_supp_tag'] = 'N'; // Tag to see if customer or supplier is selected

                    if ($row['transaction_status'] == 'Purchase'){
                        if ($customer_stmt = $db->prepare("SELECT * FROM Supplier WHERE supplier_code=? AND status = '0'")) {
                            $customer_stmt->bind_param('s', $row['supplier_code']);
                            $customer_stmt->execute();
                            $customer_result = $customer_stmt->get_result();
                            
                            if ($row2 = $customer_result->fetch_assoc()) {
                                $message['name'] = $row2['name'];
                                $message['tin_no'] = $row2['tin_no'];
                                $message['address_line_1'] = $row2['address_line_1'];
                                $message['address_line_2'] = $row2['address_line_2'];
                                $message['address_line_3'] = $row2['address_line_3'];
                                $message['phone_no'] = $row2['phone_no'];
                                $message['fax_no'] = $row2['fax_no'];
                                $message['cust_supp_tag'] = 'Y';
                            } 
                        }
                    }else{
                        if ($customer_stmt = $db->prepare("SELECT * FROM Customer WHERE customer_code=? AND status = '0'")) {
                            $customer_stmt->bind_param('s', $row['customer_code']);
                            $customer_stmt->execute();
                            $customer_result = $customer_stmt->get_result();
                            
                            if ($row2 = $customer_result->fetch_assoc()) {
                                $message['name'] = $row2['name'];
                                $message['tin_no'] = $row2['tin_no'];
                                $message['address_line_1'] = $row2['address_line_1'];
                                $message['address_line_2'] = $row2['address_line_2'];
                                $message['address_line_3'] = $row2['address_line_3'];
                                $message['phone_no'] = $row2['phone_no'];
                                $message['fax_no'] = $row2['fax_no'];
                                $message['cust_supp_tag'] = 'Y';
                            }
                        } 
                    }
                    $message['transporter'] = $row['transporter'] ?? '';
                    $message['driver_name'] = $row['driver_name'] ?? '';
                    $message['driver_ic'] = $row['driver_ic'] ?? '';
                    $message['driver_phone'] = $row['driver_phone'] ?? '';
                    $message['lorry_plate_no1'] = $row['lorry_plate_no1'] ?? '';
                    $message['transaction_id'] = $row['transaction_id'] ?? '';
                    $message['transaction_status'] = $row['transaction_status'] ?? '';
                    $message['invoice_no'] = $row['invoice_no'] ?? '';
                    $message['delivery_no'] = $row['delivery_no'] ?? '';
                    $message['purchase_order'] = $row['purchase_order'] ?? '';
                    $message['gross_weight1_date'] = date("d/m/Y - h:i:sa", strtotime($row['gross_weight1_date']));
                    $message['tare_weight1_date'] = date("d/m/Y - h:i:sa", strtotime($row['tare_weight1_date']));
                    $message['gross_weight1'] = $row['gross_weight1'] ?? '';
                    $message['tare_weight1'] = $row['tare_weight1'] ?? '';
                    $message['nett_weight1'] = $row['nett_weight1'] ?? '';
                    $message['reduce_weight'] = $row['reduce_weight'] ?? '';
                    $message['final_weight'] = $row['final_weight'] ?? '';
                    $message['plant_name'] = $row['plant_name'] ?? '';
                    $message['remarks'] = $row['remarks'] ?? '';
                    $message['modified_by'] = $row['modified_by'] ?? '';

                    # Values for Weight Product
                    $productCode = null;
                    $productName = null;
                    $productPercentage = 0;
                    $productItemWeight = 0;
                    $productUnitPrice = 0;
                    $productTotalPrice = 0;

                    if($row['id'] != null && $row['id'] != ''){
                        $weightId = $row['id'];
                        $weightProductQuery = "SELECT * FROM Weight_Product WHERE weight_id = $weightId AND deleted='0'";
                        $weightProductDetail = mysqli_query($db, $weightProductQuery);

                        $weightProduct = array();
                        while($weightProductRow = mysqli_fetch_assoc($weightProductDetail)) {
                            if(!empty($weightProductRow)){
                                $weightProduct[] = array(
                                    'product_code' => $weightProductRow['product_code'],
                                    'product_name' => $weightProductRow['product_name'],
                                    'percentage' => $weightProductRow['percentage'],
                                    'item_weight' => $weightProductRow['item_weight'],
                                    'unit_price' => $weightProductRow['unit_price'],
                                    'total_price' => $weightProductRow['total_price']
                                );
                            }
                        }

                        $message['weight_product'] = $weightProduct;
                    }
                }
                else{
                    $message['id'] = $row['id'];
                    $message['transaction_id'] = $row['transaction_id'];
                    $message['transaction_status'] = $row['transaction_status'];
                    $message['weight_type'] = $row['weight_type'];
                    $message['transaction_date'] = $row['transaction_date'];
                    $message['lorry_plate_no1'] = $row['lorry_plate_no1'];
                    $message['lorry_plate_no2'] = $row['lorry_plate_no2'];
                    $message['supplier_weight'] = $row['supplier_weight'];
                    $message['order_weight'] = $row['order_weight'];
                    $message['customer_code'] = $row['customer_code'];
                    $message['customer_name'] = $row['customer_name'];
                    $message['driver_code'] = $row['driver_code'];
                    $message['driver_name'] = $row['driver_name'];
                    $message['driver_ic'] = $row['driver_ic'];
                    $message['driver_phone'] = $row['driver_phone'];
                    $message['supplier_code'] = $row['supplier_code'];
                    $message['supplier_name'] = $row['supplier_name'];
                    $message['product_code'] = $row['product_code'];
                    $message['product_name'] = $row['product_name'];
                    $message['container_no'] = $row['container_no'];
                    $message['invoice_no'] = $row['invoice_no'];
                    $message['purchase_order'] = $row['purchase_order'];
                    $message['delivery_no'] = $row['delivery_no'];
                    $message['transporter_code'] = $row['transporter_code'];
                    $message['transporter'] = $row['transporter'];
                    $message['destination_code'] = $row['destination_code'];
                    $message['destination'] = $row['destination'];
                    $message['remarks'] = $row['remarks'];
                    $message['gross_weight1'] = $row['gross_weight1'];
                    $message['gross_weight1_date'] = $row['gross_weight1_date'];
                    $message['tare_weight1'] = $row['tare_weight1'];
                    $message['tare_weight1_date'] = $row['tare_weight1_date'];
                    $message['nett_weight1'] = $row['nett_weight1'];
                    $message['gross_weight2'] = $row['gross_weight2'];
                    $message['gross_weight2_date'] = $row['gross_weight2_date'];
                    $message['tare_weight2'] = $row['tare_weight2'];
                    $message['tare_weight2_date'] = $row['tare_weight2_date'];
                    $message['nett_weight2'] = $row['nett_weight2'];
                    $message['reduce_weight'] = $row['reduce_weight'];
                    $message['final_weight'] = $row['final_weight'];
                    $message['weight_different'] = $row['weight_different'];
                    $message['estimate_loading'] = $row['estimate_loading'];
                    $message['is_complete'] = $row['is_complete'];
                    $message['is_cancel'] = $row['is_cancel'];
                    $message['manual_weight'] = $row['manual_weight'];
                    $message['manual_price'] = $row['manual_price'];
                    $message['indicator_id'] = $row['indicator_id'];
                    $message['weighbridge_id'] = $row['weighbridge_id'];
                    $message['created_date'] = $row['created_date'];
                    $message['created_by'] = $row['created_by'];
                    $message['modified_date'] = $row['modified_date'];
                    $message['modified_by'] = $row['modified_by'];
                    $message['indicator_id_2'] = $row['indicator_id_2'];
                    $message['product_description'] = $row['product_description'];
                    $message['sub_total'] = $row['sub_total'];
                    $message['sst'] = $row['sst'];
                    $message['total_price'] = $row['total_price'];
                    $message['final_weight'] = $row['final_weight'];
                    $message['driver_code'] = $row['driver_code'];
                    $message['driver_name'] = $row['driver_name'];
                    $message['driver_ic'] = $row['driver_ic'];
                    $message['estimate_loading'] = $row['estimate_loading'];
                    $message['plant_code'] = $row['plant_code'];
                    $message['plant_name'] = $row['plant_name'];
    
                    if ($update_stmt2 = $db->prepare("SELECT * FROM Vehicle WHERE veh_number=? AND status='0'")) {
                        $update_stmt2->bind_param('s', $row['lorry_plate_no1']);
                        $update_stmt2->execute();
                        $result2 = $update_stmt2->get_result();
                        
                        if ($row2 = $result2->fetch_assoc()) {
                            $message['vehicleNoTxt'] = null; // Replace "123" with the actual value if needed
                        } 
                        else {
                            $message['vehicleNoTxt'] = $row['lorry_plate_no1']; // Debugging line
                        }
                    } 
                    else {
                        // Log error if the statement couldn't be prepared
                        $message['vehicleNoTxt'] = $db->error;
                    }
                
                    // Check and retrieve vehicle details for lorry_plate_no2
                    if ($update_stmt3 = $db->prepare("SELECT * FROM Vehicle WHERE veh_number=? AND status='0'")) {
                        $update_stmt3->bind_param('s', $row['lorry_plate_no2']);
                        $update_stmt3->execute();
                        $result3 = $update_stmt3->get_result();
                        
                        if ($row3 = $result3->fetch_assoc()) {
                            $message['vehicleNoTxt2'] = null; // Replace "123" with the actual value if needed
                        } 
                        else {
                            $message['vehicleNoTxt2'] = $row['lorry_plate_no2']; // Debugging line
                        }
                    } 
                    else {
                        // Log error if the statement couldn't be prepared
                        $message['vehicleNoTxt2'] = $db->error;
                    }
    
                    // retrieve products
                    $empQuery = "SELECT * FROM Weight_Product WHERE weight_id = $id AND deleted = '0' ORDER BY id ASC";
                    $empRecords = mysqli_query($db, $empQuery);
                    $products = array();
                    $productCount = 1;
    
                    while($row4 = mysqli_fetch_assoc($empRecords)) {
                        $products[] = array(
                            "no" => $productCount,
                            "id" => $row4['id'],
                            "weight_id" => $row4['weight_id'],
                            "product_code" => $row4['product_code'],
                            "product_name" => $row4['product_name'],
                            "percentage" => $row4['percentage'],
                            "item_weight" => $row4['item_weight'],
                            "unit_price" => $row4['unit_price'],
                            "total_price" => $row4['total_price']
                        );
                        $productCount++;
                    }
    
                    $message['products'] = $products;
                }
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>