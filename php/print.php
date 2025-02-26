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
                $type = $row['transaction_status'];
                $customerCode = '';
                $customerName = '';
                $productCode = $row['product_code'];
                $productName = $row['product_name'];
                $transportCode = $row['transporter_code'];
                $transportName = $row['transporter'];
                $destinationCode = $row['destination_code'];
                $destinationName = $row['destination'];
                $loadingChitNo = $row['transaction_id'];
                $deliverOrderNo = $row['delivery_no'];
                $lorryNo = $row['lorry_plate_no1'];
                $poNo = $row['purchase_order'];
                $complete = $row['is_complete'];
                $grossWeightDate = new DateTime($row['gross_weight1_date']);
                $formattedGrossWeightDate = $grossWeightDate->format('H:i');
                $tareWeightDate =  new DateTime($row['tare_weight1_date']);
                $formattedTareWeightDate = $tareWeightDate->format('H:i');
                $grossWeight = number_format($row['gross_weight1'] / 1000, 3);
                $tareWeight = number_format($row['tare_weight1'] / 1000, 3);
                $nettWeight = number_format($row['nett_weight1'] / 1000, 3);
                $sysdate = date("d-m-Y");
                $weightBy = $row['created_by'];
                $createDate = new DateTime($row['created_date']);
                $formattedCreateDate = $createDate->format('d-m-Y');
                $remarks = $row['remarks'];
                $message = '';
                
                if($type == 'Sales'){
                    $customerCode = $row['customer_code'];
                    $customerName = $row['customer_name'];
                }
                else{
                    $customerCode = $row['supplier_code'];
                    $customerName = $row['supplier_name'];
                }

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
                    
                    if($row['transaction_status'] == 'Sales'){
                        $cid = $row['customer_code'];
                    
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
                    }
                    else{
                        $cid = $row['supplier_code'];
                    
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
                    
                    $message = '<html>
    <head>
        <style>
            @media print {
                @page {
                    margin-left: 0.5in;
                    margin-right: 0.5in;
                    margin-top: 0.1in;
                    margin-bottom: 0.1in;
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
        <table style="width:100%">
            <tr>
                <td style="width: 60%;">
                    <p>
                        <span style="font-weight: bold;font-size: 16px;">'.$compname.'</span><br><br>
                        <span style="font-size: 12px;">'.$compaddress.'</span><br>
                        <span style="font-size: 12px;">'.$compaddress2.'</span><br>
                        <span style="font-size: 12px;">'.$compaddress3.'</span><br>
                        <span style="font-size: 12px;">TEL: '.$compphone.' / FAX: '.$compiemail.'</span>
                    </p>
                </td>
                <td>
                    <p>
                        <span style="font-weight: bold;font-size: 12px;">Transaction Date. : '.$row['transaction_date'].'</span><br>
                        <span style="font-weight: bold;font-size: 12px;">Transaction No. &nbsp;&nbsp;&nbsp;: '.$row['transaction_id'].'</span><br>
                        <span style="font-size: 12px;">Transaction Status: '.$row['transaction_status'].'</span><br>';
                        
                    if($row['manual_weight'] == 'true'){
                        $message .= '<span style="font-size: 12px;">Weight Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Manual Weighing</span><br>';
                    }
                    else{
                        $message .= '<span style="font-size: 12px;">Weight Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Auto Weighing</span><br>';
                    }
                    
                    $message .= '<span style="font-size: 12px;">Invoice No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['invoice_no'] ?? '').'</span><br>
                        <span style="font-size: 12px;">Delivery No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['delivery_no'] ?? '').'</span><br>
                        <span style="font-size: 12px;">Purchase No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['purchase_order'] ?? '').'</span><br>
                        <span style="font-size: 12px;">Container No. &nbsp;&nbsp;&nbsp;&nbsp;: '.($row['container_no'] ?? '').'</span>
                    </p>
                </td>
            </tr>
        </table>
        <hr>
        <table style="width:100%">
        <tr>
            <td style="width: 40%;">
                <p>
                    <span style="font-weight: bold;font-size: 16px;">'.$customer.'</span><br>
                </p>
            </td>
            <td style="width: 20%;">
                <p>&nbsp;</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span style="font-size: 12px;">'.$customerA.'</span><br>
                    <span style="font-size: 12px;">'.$customerA2.'</span><br>
                    <span style="font-size: 12px;">'.$customerA3.'</span><br>
                    <span style="font-size: 12px;">TEL: '.$customerP.'/ FAX: '.$customerE.'</span>
                </p>
            </td>
            <td style="width: 20%;"></td>
            <td>
                <p>
                    <span style="font-size: 12px;">Weight Date & Time : '.($row['gross_weight1_date'] ?? '').'</span><br>
                    <span style="font-size: 12px;">User Weight &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$row['created_by'].'</span><br>
                </p>
                <table style="width:100%; border:1px solid black;">
                    <tr>';
                if($row['transaction_status'] == 'Sales'){
                    $message .= '<th colspan="2" style="border:1px solid black; font-size: 14px;">Order Weight</th>
                    <th colspan="2" style="border:1px solid black; font-size: 14px;">Variance Weight</th>
                    <th style="border:1px solid black; font-size: 14px;">Variance</th>';
                }
                else{
                    $message .= '<th colspan="2" style="border:1px solid black; font-size: 14px;">Supply Weight</th>
                    <th colspan="2" style="border:1px solid black; font-size: 14px;">Variance Weight</th>
                    <th style="border:1px solid black; font-size: 14px;">Variance</th>';
                }

                if($row['transaction_status'] == 'Sales'){
                    $final = $row['final_weight'];
                    $trueWeight = $row['order_weight'] ?? '0';
                    $different = 0;

                    if ($variance == 'W') {
                        if ($low !== null && ((float)$final < (float)$trueWeight - (float)$low || (float)$final > (float)$trueWeight + (float)$low)) {
                            $different = (float)$final < (float)$trueWeight - (float)$low;
                        } elseif ($high !== null && ((float)$final < (float)$trueWeight - (float)$high || (float)$final > (float)$trueWeight + (float)$high)) {
                            $different = (float)$final < (float)$trueWeight - (float)$high;
                        }
                    } 
                    elseif ($variance == 'P') {
                        if ($low !== null && ((float)$final < (float)$trueWeight * (1 - (float)$low / 100) || (float)$final > (float)$trueWeight * (1 + (float)$low / 100))) {
                            $different = (float)$final - (float)$trueWeight * (1 - (float)$low / 100);
                        } elseif ($high !== null && ((float)$final < (float)$trueWeight * (1 - (float)$high / 100) || (float)$final > (float)$trueWeight * (1 + (float)$high / 100))) {
                            $different = (float)$final - (float)$trueWeight * (1 - (float)$high / 100);
                        }
                    }

                    $message .= '</tr>
                    <tr>
                        <td style="border:1px solid black;">'.($row['order_weight'] ?? '').'</td>
                        <td style="border:1px solid black;">kg</td>
                        <td style="border:1px solid black;">'.($row['weight_different'] ?? '').'</td>
                        <td style="border:1px solid black;">kg</td>
                        <td style="border:1px solid black;">'.$different.' '.($variance == "W" ? 'kg' : '%').'</td>
                    </tr>';
                }
                else{
                    $final = $row['final_weight'];
                    $trueWeight = $row['supplier_weight'] ?? '0';
                    $different = 0;

                    if ($variance == 'W') {
                        if ($low !== null && ((float)$final < (float)$trueWeight - (float)$low || (float)$final > (float)$trueWeight + (float)$low)) {
                            $different = (float)$final < (float)$trueWeight - (float)$low;
                        } elseif ($high !== null && ((float)$final < (float)$trueWeight - (float)$high || (float)$final > (float)$trueWeight + (float)$high)) {
                            $different = (float)$final < (float)$trueWeight - (float)$high;
                        }
                    } 
                    elseif ($variance == 'P') {
                        if ($low !== null && ((float)$final < (float)$trueWeight * (1 - (float)$low / 100) || (float)$final > (float)$trueWeight * (1 + (float)$low / 100))) {
                            $different = (float)$final - (float)$trueWeight * (1 - (float)$low / 100);
                        } elseif ($high !== null && ((float)$final < (float)$trueWeight * (1 - (float)$high / 100) || (float)$final > (float)$trueWeight * (1 + (float)$high / 100))) {
                            $different = (float)$final - (float)$trueWeight * (1 - (float)$high / 100);
                        }
                    }

                    $message .= '</tr>
                    <tr>
                        <td style="border:1px solid black;">'.($row['supplier_weight'] ?? '').'</td>
                        <td style="border:1px solid black;">kg</td>
                        <td style="border:1px solid black;">'.($row['weight_different'] ?? '').'</td>
                        <td style="border:1px solid black;">kg</td>
                        <td style="border:1px solid black;">'.$different.' '.($variance == "W" ? 'kg' : '%').'</td>
                    </tr>';
                }
                        
                $message .= '</table>
            </td>
        </tr>
        </table><br>
        <table style="width:100%; border:1px solid black;">
            <tr>
                <th style="border:1px solid black;font-size: 14px;">Vehicle No.</th>
                <th style="border:1px solid black;font-size: 14px;">Product Name</th>
                <th style="border:1px solid black;font-size: 14px;">Unit Price</th>
                <th colspan="2" style="border:1px solid black;font-size: 14px;">Total Weight</th>
                <th style="border:1px solid black;font-size: 14px;">Total Price</th>
            </tr>
            <tr>
                <td style="border:1px solid black;font-size: 14px;">'.$row['lorry_plate_no1'].'</td>
                <td style="border:1px solid black;font-size: 14px;">'.$row['product_name'].'</td>
                <td style="border:1px solid black;font-size: 14px;">RM '.$price.'</td>
                <td style="border:1px solid black;font-size: 14px;">'.$row['nett_weight1'].'</td>
                <td style="border:1px solid black;font-size: 14px;">kg</td>
                <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.number_format(((float)$price * (float)$row['nett_weight1']), 2, '.', '').'</td>
            </tr>';

            if($row['weight_type'] == 'Container'){
                $message .= '<tr>
                    <td style="border:1px solid black;font-size: 14px;">'.$row['lorry_plate_no2'].'</td>
                    <td style="border:1px solid black;font-size: 14px;">'.$row['product_name'].'</td>
                    <td style="border:1px solid black;font-size: 14px;">RM '.$price.'</td>
                    <td style="border:1px solid black;font-size: 14px;">'.$row['nett_weight2'].'</td>
                    <td style="border:1px solid black;font-size: 14px;">kg</td>
                    <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.number_format(((float)$price * (float)$row['nett_weight2']), 2, '.', '').'</td>
                </tr>';
            }

            $message .= '<tr>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">Subtotal</td>
                <td style="border:1px solid black;font-size: 14px;">'.$row['final_weight'].'</td>
                <td style="border:1px solid black;font-size: 14px;">kg</td>
                <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['sub_total'].'</td>
            </tr>
            <tr>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">SST</td>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="2"></td>
                <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['sst'].'</td>
            </tr>
            <tr>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">Total Price</td>
                <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="2"></td>
                <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['total_price'].'</td>
            </tr>
        </table>
        <p>
            <span style="font-size: 12px;font-weight: bold;">Remark: </span>
            <span style="font-size: 12px;">'.$row['remarks'].'</span>
        </p>
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

                                    .custom-hr {
                                        border-top: 1px solid #000;        /* Remove the default border */
                                        height: 1px;         /* Define the thickness */
                                        margin: 0;           /* Reset margins */
                                    }

                                    .body_1 p {
                                        margin-bottom: 0px;
                                    }

                                    .body_1 p span{
                                        margin-right: 15px;
                                    }

                                    .body_2 p {
                                        margin-bottom: 0px;
                                    }

                                    .body_2 p span{
                                        margin-right: 15px;
                                    }

                                    .body2 {
                                        border-top: 1px dashed black;
                                        border-bottom: 1px dashed black;
                                    }

                                    .body_3 {
                                        border-top: 1px dashed black;
                                        text-align: center;
                                        padding-top: 5px;
                                    }

                                    .signature {
                                        padding-top: 30px; 
                                        padding-left: 80px; 
                                        padding-right: 80px;
                                    }
                                </style>

                            </head>

                            <body>
                                <div class="container-full">
                                    <div class="header">
                                        <div style="text-align: center;">
                                            <h2 style="color: black; font-weight: bold;">EAST ROCK MARKETING SDN BHD</h2>
                                            <p style="font-size: 11px; margin-bottom: 3px;">(1373003-H) <br> LOT PT 758, JALAN PADANG GAJAH <br>BATU 16, TAMBAK JAWA<br>45800 JERAM, KUALA SELANGOR,<br>SELANGOR D.E.<br>TEL: 013-969 7663, 012-9536128</p>
                                            <h3 class="pb-2" style="color: black; font-weight: bold;"><span style="border-bottom: 1px solid black">PUBLIC WEIGHING</span></h3>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-8 body_1">
                                            <p>DATE<span style="margin-left: 100px;">:</span>'.$formattedCreateDate.'</p>
                                            <p>VEHICLE NO.<span style="margin-left: 55px;">:</span>'.$lorryNo.'</p>
                                            <p>CUSTOMER NAME<span style="margin-left: 21px;">:</span>'.$customerName.'</p>
                                            <p>PRODUCT NAME<span style="margin-left: 30px;">:</span>'.$productName.'</p>
                                            <p>SERVICE CHARGES<span style="margin-left: 15px;">:</span>RM </p>
                                            <p>REMARKS<span style="margin-left: 71px;">:</span>'.$remarks.'</p>
                                        </div>
                                        <div class="col-4 body_1">
                                            <p>TICKET NO.<span style="margin-left: 20px;">:</span><b>'.str_replace('P', '', str_replace('S', '', $loadingChitNo)).'</b></p>
                                        </div>
                                    </div>

                                    <div class="row body2">
                                        <div class="col-6 body_2 mt-2 mb-2">
                                            <p>WEIGHT IN <span style="margin-left: 25px;">(KG)</span><span>:</span>'.$grossWeight.'</p>
                                            <p>WEIGHT OUT<span style="margin-left: 15px;">(KG)</span><span>:</span>'.$tareWeight.'</p>
                                            <p>NET WEIGHT<span style="margin-left: 18px;">(KG)</span><span>:</span>'.$nettWeight.'</p>
                                        </div>
                                        <div class="col-6 body_2 mt-2 mb-2">
                                            <p>TIME IN<span style="margin-left: 25px;">:</span>'.$formattedGrossWeightDate.'</p>
                                            <p>TIME OUT<span style="margin-left: 11px;">:</span>'.$formattedTareWeightDate.'</p>
                                        </div>
                                    </div>

                                    <div class="row pt-5">
                                        <div class="col-6 signature">
                                            <div class="body_3">
                                                WEIGHED BY '.$weightBy.'
                                            </div>
                                        </div>
                                        <div class="col-6 signature">
                                            <div class="body_3">
                                                LORRY DRIVER
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </body></html>
                        ';
                        $select_stmt->close();
                    }
                }
                
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
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}

?>