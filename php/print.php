<?php
require_once 'db_connect.php';
include 'phpqrcode/qrlib.php';

$compids = '1';
$compname = 'SYNCTRONIX TECHNOLOGY (M) SDN BHD';
$compreg = '123456789-X';
$compaddress = 'No.34, Jalan Bagan 1,';
$compaddress2 = 'Taman Bagan,';
$compaddress3 = '13400 Butterworth. Penang. Malaysia.';
$compphone = '6043325822';
$compiemail = 'admin@synctronix.com.my';
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

// Format Weight String
function formatWeight($weight){
    if ($weight != 0){
        $formatted = number_format(ltrim($weight, '0'), 2, '.', ',');
        $formatted = preg_replace('/\.00$/', '', $formatted);    
    }else{
        $formatted = $weight;
    }

    return $formatted;
}

if(isset($_POST['userID'], $_POST["file"])){
    $stmt = $db->prepare("SELECT * FROM Company WHERE id=?");
    $stmt->bind_param('s', $compids);
    $stmt->execute();
    $result1 = $stmt->get_result();
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
            
    if ($row = $result1->fetch_assoc()) {
        $compname = $row['name'];
        $compreg = $row['company_reg_no'];
        $compaddress = $row['address_line_1'];
        $compaddress2 = $row['address_line_2'];
        $compaddress3 = $row['address_line_3'];
        $compphone = $row['phone_no'];
        $compiemail = $row['fax_no'];
    }

    if($_POST["file"] == 'weight'){
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        if ($select_stmt = $db->prepare("SELECT * FROM Weight WHERE id=?")) {
            $select_stmt->bind_param('s', $id);

            // Execute the prepared query.
            if (! $select_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong"
                    )); 
            }
            else{
                $result = $select_stmt->get_result();
                    
                if ($row = $result->fetch_assoc()) {
                    $customer = '';
                    $customerR = '';
                    $customerP = '';
                    $customerA = '';
                    $customerA2 = '';
                    $customerA3 = '';
                    $customerE = '';

                    $product = '';
                    $price = '';
                    $variance = '';
                    $high = '';
                    $low = '';

                    $transactionDate = date("d/m/Y", strtotime($row['transaction_date']));
                    $grossWeightTime = date("d/m/Y - H:i:s", strtotime($row['gross_weight1_date']));
                    $tareWeightTime = date("d/m/Y - H:i:s", strtotime($row['tare_weight1_date']));

                    $orderSuppWeight = 0;
                    $weightDifference = $row['weight_different'];
                    $finalWeight = $row['final_weight'];

                    $grossWeightTime2 = $row['gross_weight2_date'] != null ? date("d/m/Y - H:i:s", strtotime($row['gross_weight2_date'])) : "";
                    $tareWeightTime2 = $row['tare_weight2_date'] != null ? date("d/m/Y - H:i:s", strtotime($row['tare_weight2_date'])) : "";
                    
                    if($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local'){
                        $cid = $row['supplier_code'];
                        $orderSuppWeight = floatval($row['supplier_weight']);

                        if ($update_stmt = $db->prepare("SELECT * FROM Supplier WHERE supplier_code=?")) {
                            $update_stmt->bind_param('s', $cid);
                            
                            // Execute the prepared query.
                            if ($update_stmt->execute()) {
                                $result2 = $update_stmt->get_result();
                                
                                if ($row2 = $result2->fetch_assoc()) {
                                    $customer = $row2['name'];
                                    $customerR = $row2['company_reg_no'] ?? '';
                                    $customerP = $row2['phone_no'] ?? '-';
                                    $customerA = $row2['address_line_1'];
                                    $customerA2 = $row2['address_line_2'];
                                    $customerA3 = $row2['address_line_3'];
                                    $customerE = $row2['fax_no'] ?? '-';
                                }
                            }
                        }

                        $pid = $row['raw_mat_code'];
                    
                        if ($update_stmt2 = $db->prepare("SELECT * FROM Raw_Mat WHERE raw_mat_code=?")) {
                            $update_stmt2->bind_param('s', $pid);
                            
                            // Execute the prepared query.
                            if ($update_stmt2->execute()) {
                                $result3 = $update_stmt2->get_result();
                                
                                if ($row3 = $result3->fetch_assoc()) {
                                    $product = $row3['name'];
                                    $variance = $row3['variance'] ?? '';
                                    $high = $row3['high'] ?? '0';
                                    $low = $row3['low'] ?? '0';
                                    $price = $row3['price'] ??  '0.00';
                                }
                            }
                        }
                    }
                    else{
                        $cid = $row['customer_code'];
                        $orderSuppWeight = floatval($row['order_weight']);
                    
                        if ($update_stmt = $db->prepare("SELECT * FROM Customer WHERE customer_code=?")) {
                            $update_stmt->bind_param('s', $cid);
                            
                            // Execute the prepared query.
                            if ($update_stmt->execute()) {
                                $result2 = $update_stmt->get_result();
                                
                                if ($row2 = $result2->fetch_assoc()) {
                                    $customer = $row2['name'];
                                    $customerR = $row2['company_reg_no'] ?? '';
                                    $customerP = $row2['phone_no'] ?? '-';
                                    $customerA = $row2['address_line_1'];
                                    $customerA2 = $row2['address_line_2'];
                                    $customerA3 = $row2['address_line_3'];
                                    $customerE = $row2['fax_no'] ?? '-';
                                }
                            }
                        }

                        $pid = $row['product_code'];
                    
                        if ($update_stmt2 = $db->prepare("SELECT * FROM Product WHERE product_code=?")) {
                            $update_stmt2->bind_param('s', $pid);
                            
                            // Execute the prepared query.
                            if ($update_stmt2->execute()) {
                                $result3 = $update_stmt2->get_result();
                                
                                if ($row3 = $result3->fetch_assoc()) {
                                    $product = $row3['name'];
                                    $variance = $row3['variance'] ?? '';
                                    $high = $row3['high'] ?? '0';
                                    $low = $row3['low'] ?? '0';
                                    $price = $row3['price'] ??  '0.00';
                                }
                            }
                        }
                    }

                    /*$text = "https://speedjin.com/synctronix/qr.php?id=".$id."&compid=".$compids;
  
                    // $path variable store the location where to 
                    // store image and $file creates directory name
                    // of the QR code file by using 'uniqid'
                    // uniqid creates unique id based on microtime
                    $path = 'images/';
                    $file = $path.uniqid().".png";
                      
                    // $ecc stores error correction capability('L')
                    $ecc = 'L';
                    $pixel_Size = 10;
                    $frame_Size = 10;
                      
                    // Generates QR Code and Stores it in directory given
                    QRcode::png($text, $file, $ecc, $pixel_Size, $frame_size);*/
                    
                    $message = '
                    <html>
                        <head>
                            <style>
                                @media print {
                                    @page {
                                        size: A5 landscape;
                                        margin-left: 0in;
                                        margin-right: 0in;
                                        margin-top: 0.1in;
                                        margin-bottom: 0.1in;
                                        padding-left: 0.2in;
                                        padding-right: 0.2in;
                                    }
                                    
                                } 
                                        
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    
                                } 
                                
                                .table th, .table td {
                                    padding: 0.70rem;
                                    vertical-align: top;
                                    border-top: 1px solid #dee2e6;
                                    
                                } 
                                
                                .table-bordered {
                                    border: 1px solid #000000;
                                    
                                } 
                                
                                .table-bordered th, .table-bordered td {
                                    border: 1px solid #000000;
                                    font-family: sans-serif;
                                    font-size: 12px;
                                    
                                } 

                                .table-border {
                                    border: 1px solid #000000;
                                }
                                
                                .row {
                                    display: flex;
                                    flex-wrap: wrap;
                                    margin-top: 20px;
                                    margin-right: -15px;
                                    margin-left: -15px;
                                    
                                } 
                                
                                .col-md-4{
                                    position: relative;
                                    width: 33.333333%;
                                }
                            </style>
                        </head>
                        <body>
                            <table style="width:100%;">
                                <tr>
                                    <td style="width: 70%;">
                                        <p style="font-size: 14px;">
                                            <span style="font-weight: bold;font-size: 16px;">'.$compname.'</span><br><br>
                                            <span> Reg No.: '.$compreg.'</span><br>
                                            <span style="font-size: 12px;">'.$compaddress.'</span><br>
                                            <span style="font-size: 12px;">'.$compaddress2.'</span><br>
                                            <span style="font-size: 12px;">'.$compaddress3.'</span><br>
                                            <span style="font-size: 12px;">TEL: '.$compphone.' / FAX: '.$compiemail.'</span>
                                        </p>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <span style="font-size: 24px; font-weight: bold; display: inline-block; margin-bottom: 6px;">'. $row['transaction_status'] . ' Slip' . '</span><br>
                                        <table style="border:none; font-size:14px;">
                                            <tr><td>Transaction No</td><td style="padding: 0 6px;">:</td><td><b>'.$row['transaction_id'].'</b></td></tr>
                                            <tr><td>Date</td><td style="padding: 0 6px;">:</td><td>'.$transactionDate.'</td></tr>
                                            <tr><td>DO No</td><td style="padding: 0 6px;">:</td><td>'.$row['delivery_no'].'</td></tr>
                                            <tr><td>PO No</td><td style="padding: 0 6px;">:</td><td>'.$row['purchase_order'].'</td></tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr style="visibility:hidden;">
                                    <td style="font-size: 3px;">Placeholder for empty space</td>
                                </tr>
                                <tr style="border-top: 1px solid black;">
                                    <td style="vertical-align: top;">
                                        <table style="border:none; font-size:14px; margin-top:5px;">
                                            <tr><td>Customer</td><td style="padding: 0 6px;">:</td><td><b>'.$customer.'</b></td></tr>
                                            <tr><td>Transporter</td><td style="padding: 0 6px;">:</td><td>'.$row['transporter'].'</td></tr>
                                            <tr><td>Destination</td><td style="padding: 0 6px;">:</td><td>'.$row['destination'].'</td></tr>
                                        </table>';
                                        $message .= '
                                    </td>
                                    <td style="vertical-align: top;">
                                        <table style="border:none; font-size:14px; margin-top:5px; width:auto;">
                                            <tr><td>Order Weight</td><td style="padding: 0 6px;">:</td><td><b>'.$row['order_weight'].'</b></td></tr>
                                            <tr><td>Weight Variance</td><td style="padding: 0 6px;">:</td><td>'.$row['weight_different'].'</td></tr>
                                            <tr><td>Container No</td><td style="padding: 0 6px;">:</td><td>'.$row['container_no'].'</td></tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>';

                            if ($row['weight_type'] == 'Container') {
                                $message .= '
                                    <table style="width:100%; border:0px solid black; margin-top: 10px;">
                                        <tr style="font-size: 14px;text-align: center;">
                                            <th style="border:1px solid black;">Incoming Date/Time</th>
                                            <th style="border:1px solid black;">Outgoing Date/Time</th>
                                            <th colspan="2" style="border:1px solid black;">Prime Mover No. & Weight (kg)</th>
                                            <th style="border:1px solid black;">Tare (kg)</th>
                                            <th style="border:1px solid black;">Nett (kg)</th>
                                        </tr>
                                        <tr style="font-size: 14px;text-align: center;">
                                            <td style="border:1px solid black;">'.$grossWeightTime.'</td>
                                            <td style="border:1px solid black;">'.$tareWeightTime.'</td>
                                            <td style="border:1px solid black;">'.$row['lorry_plate_no1'].'</td>
                                            <td style="border:1px solid black;">'.formatWeight($row['gross_weight1']).'</td>
                                            <td style="border:1px solid black;">'.formatWeight($row['tare_weight1']).' kg</td>
                                            <td style="border:1px solid black;">'.formatWeight($row['nett_weight1']).' kg</td>
                                        </tr>
                                        <tr style="font-size: 14px;text-align: center;">
                                            <td style="border:1px solid black;">'.$grossWeightTime2.'</td>
                                            <td style="border:1px solid black;">'.$tareWeightTime2.'</td>
                                            <td style="border:1px solid black;">'.$row['lorry_plate_no2'].'</td>
                                            <td style="border:1px solid black;">'.formatWeight($row['gross_weight2']).'</td>
                                            <td style="border:1px solid black;">'.formatWeight($row['tare_weight2']).' kg</td>
                                            <td style="border:1px solid black;">'.formatWeight($row['nett_weight2']).' kg</td>
                                        </tr>
                                        <tr style="font-size: 14px;text-align: center;">
                                            <td colspan="4" style="text-align: left;"><b>Remarks &nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;</b> <span style="margin-left: 10px">'.$row['remarks'].'</span></td>
                                            <td style="border:1px solid black;">Final Weight</td>
                                            <td style="border:1px solid black;">'.formatWeight(abs((int)$row['nett_weight1'] - (int)$row['nett_weight2'])).' kg</td>
                                        </tr>
                                        <tr style="font-size: 14px;text-align: center;">
                                            <td colspan="4" style="text-align: left;"></td>
                                            <td style="border:1px solid black;">Reduce Weight (kg)</td>
                                            <td style="border:1px solid black;">'.($row['reduce_weight_type'] == '%' ? $row['reduce_weight_input'].'% <br> ('.formatWeight($row['reduce_weight']).' kg)' : formatWeight($row['reduce_weight']).' kg').'</td>
                                        </tr>
                                        <tr style="font-size: 14px;text-align: center;font-weight:bold;">
                                            <td colspan="4" style="text-align: left;"></td>
                                            <td style="border:1px solid black;">Nett Weight</td>
                                            <td style="border:1px solid black;">'.formatWeight((int) $row['final_weight'] - (int) $row['reduce_weight']).' kg</td>
                                        </tr>
                                    </table>
                                ';
                            } else {
                                $message .= '<br>
                                    <table style="width:100%; border:0px solid black; margin-top: -10px;">
                                        <tr>
                                            <th style="border:1px solid black;font-size: 16px;text-align: center;" width="15%">Vehicle No</th>
                                            <th colspan="2" style="border:1px solid black;font-size: 16px;text-align: center;" width="30%">Product Description</th>
                                            <th style="border-top: 1px solid black; border-right: none" width="5%"></th>
                                            <th style="border-top: 1px solid black; border-right: none" width="24%">Date Time</th>
                                            <th style="border-top: 1px solid black; border-left: none;font-size: 16px;text-align: center;" width="6%"></th>
                                            <th colspan="2" style="border:1px solid black;font-size: 16px;text-align: center;" width="20%">Weight (kg)</th>
                                        </tr>
                                        <tr style="font-size: 16px;text-align: center;">
                                            <td rowspan="2" style="border:1px solid black;">'.$row['lorry_plate_no1'].'</td>';

                                            if ($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local'){
                                                $message .= '<td rowspan="2" colspan="2" style="border:1px solid black;">'.$row['raw_mat_name'].'</td>';
                                            }else{
                                                $message .= '<td rowspan="2" colspan="2" style="border:1px solid black;">'.$row['product_name'].'</td>';
                                            }

                                        $message .= '    
                                            <td style="border:1px solid black; font-weight: bold;">In</td>
                                            <td colspan="2" style="border:1px solid black;">'.$grossWeightTime.'</td>
                                            <td style="border:1px solid black;">'.formatWeight($row['gross_weight1']).' kg</td>
                                        </tr>
                                        <tr style="font-size: 16px;text-align: center;">
                                            <td style="border:1px solid black; font-weight: bold;">Out</td>
                                            <td colspan="2" style="border:1px solid black;">'.$tareWeightTime.'</td>
                                            <td style="border:1px solid black;">'.formatWeight($row['tare_weight1']).' kg</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Remarks &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; <span style="margin-left: 10px">'.$row['remarks'].'</span></td>
                                            <td colspan="3" style="border:1px solid black;font-size: 16px;text-align: center;">Reduce Weight (kg)</td>
                                            <td style="border:1px solid black;font-size: 16px;text-align: center;">('.formatWeight($row['reduce_weight']).' kg)</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan="3" style="border:1px solid black;font-size: 16px;font-weight:bold;text-align: center;">Nett Weight</td>
                                            <td style="border:1px solid black;font-size: 16px;font-weight:bold;text-align: center;">'.formatWeight($row['final_weight']).' kg</td>
                                        </tr>
                                    </table>
                                    <br>
                                ';
                            }

                            $message .='
                        </body>
                    </html>';
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
                else{
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => 'Unable to read data'
                        )
                    );
                }
                
                
            }
        }
        else{
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something Goes Wrong"
                ));
        }
    }
    else{
        $empQuery = "select count.id, count.serialNo, vehicles.veh_number, lots.lots_no, count.batchNo, count.invoiceNo, count.deliveryNo, 
        count.purchaseNo, customers.customer_name, products.product_name, packages.packages, count.unitWeight, count.tare, count.totalWeight, 
        count.actualWeight, count.currentWeight, units.units, count.moq, count.dateTime, count.unitPrice, count.totalPrice,count.totalPCS, 
        count.remark, status.status from count, vehicles, packages, lots, customers, products, units, status WHERE 
        count.vehicleNo = vehicles.id AND count.package = packages.id AND count.lotNo = lots.id AND count.customer = customers.id AND 
        count.productName = products.id AND status.id=count.status AND units.id=count.unit AND count.deleted = '0' AND count.id=?";

        if ($select_stmt = $db->prepare($empQuery)) {
            $select_stmt->bind_param('s', $id);

            // Execute the prepared query.
            if (! $select_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong"
                    )); 
            }
            else{
                $result = $select_stmt->get_result();
                

                if ($row = $result->fetch_assoc()) {
                    $message = '<html>
                    <head>
                        <title>Html to PDF</title>
                    </head>
                    <body>
                        <h3>'.$compname.'</h3>
                        <p>No.34, Jalan Bagan 1, <br>Taman Bagan, 13400 Butterworth.<br> Penang. Malaysia.</p>
                        <p>TEL: 6043325822 | EMAIL: admin@synctronix.com.my</p><hr>
                        <table style="width:100%">
                        <tr>
                            <td>
                                <h4>CUSTOMER NAME: '.$row['customer_name'].'</h4>
                            </td>
                            <td>
                                <h4>SERIAL NO: '.$row['serialNo'].'</h4>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>No.34, Jalan Bagan 1, <br>Taman Bagan, <br>13400 Butterworth. Penang. Malaysia.</p>
                            </td>
                            <td>
                                <h4>Status: '.$row['status'].'</h4>
                                <p>Date: 23/03/2022<br>Delivery No: '.$row['deliveryNo'].'</p>
                            </td>
                        </tr>
                        </table>
                        <table style="width:100%; border:1px solid black;">
                        <tr>
                            <th style="border:1px solid black;">Vehicle No.</th>
                            <th style="border:1px solid black;">Product Name</th>
                            <th style="border:1px solid black;">Date & Time</th>
                            <th style="border:1px solid black;">Weight</th>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;">'.$row['veh_number'].'</td>
                            <td style="border:1px solid black;">'.$row['product_name'].'</td>
                            <td style="border:1px solid black;">'.$row['dateTime'].'</td>
                            <td style="border:1px solid black;">'.$row['unitWeight'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Tare Weight</td>
                            <td style="border:1px solid black;">'.$row['tare'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Net Weight</td>
                            <td style="border:1px solid black;">'.$row['actualWeight'].' '.$row['units'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">M.O.Q</td>
                            <td style="border:1px solid black;">'.$row['moq'].'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid black;">Total Weight</td>
                            <td style="border:1px solid black;">'.$row['totalWeight'].' '.$row['units'].'</td>
                        </tr>
                        </table>
                        <p>Remark: '.$row['remark'].'</p>
                    </body>
                </html>';
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));
            }
        }
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