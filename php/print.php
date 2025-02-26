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

if(isset($_POST['userID'], $_POST["file"], $_POST['type'])){
    $stmt = $db->prepare("SELECT * FROM Company WHERE id=?");
    $stmt->bind_param('s', $compids);
    $stmt->execute();
    $result1 = $stmt->get_result();
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);        

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

                    $gross_weight1_date = '';
                    $grossWeight1Date = '';
                    $grossWeight1DateTime = '';
                    $tare_weight1_date = '';
                    $tareWeight1Date = '';
                    $tareWeight1DateTime = '';

                    if (isset($row['gross_weight1_date'])){
                        $gross_weight1_date = $row['gross_weight1_date'];
                        $grossWeight1Date = DateTime::createFromFormat('Y-m-d H:i:s', $gross_weight1_date)->format('d/m/Y');  
                        $grossWeight1DateTime = DateTime::createFromFormat('Y-m-d H:i:s', $gross_weight1_date)->format('d/m/Y - H:i:sa');  
                    }

                    if (isset($row['tare_weight1_date'])){
                        $tare_weight1_date = $row['tare_weight1_date'];
                        $tareWeight1Date = DateTime::createFromFormat('Y-m-d H:i:s', $tare_weight1_date)->format('d/m/Y');  
                        $tareWeight1DateTime = DateTime::createFromFormat('Y-m-d H:i:s', $tare_weight1_date)->format('d/m/Y - H:i:sa');  
                    }
                    
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

                    # Query for Weight_Product
                    if ($type == 'Dual Bins'){
                        $count = 1;
                        $productName1 = '';
                        $productName2 = '';
                        $bin1Weight = '';
                        $bin2Weight = '';
                        $bin1StartDt = '';
                        $bin2StartDt = '';
                        $bin1EndDt = '';
                        $bin2EndDt = '';

                        $query = "SELECT * FROM Weight_Product JOIN Product ON Weight_Product.product_id = Product.id WHERE Weight_Product.weight_id=$id ORDER BY Weight_Product.id ASC";
                        $empRecords = mysqli_query($db, $query);
                        while($row4 = mysqli_fetch_assoc($empRecords)) {
                            if ($count == 1){
                                $productName1 = $row4['name'];
                                $bin1Weight = $row4['order_weight'];
                                if (isset($row4['start_date'])){
                                    $bin1StartDt = DateTime::createFromFormat('Y-m-d H:i:s', $row4['start_date'])->format('d/m/Y H:i:sa');  
                                }

                                if (isset($row4['end_date'])){
                                    $bin1EndDt = DateTime::createFromFormat('Y-m-d H:i:s', $row4['end_date'])->format('d/m/Y H:i:sa');  
                                }
                            }elseif ($count == 2){
                                $productName2 = $row4['name'];
                                $bin2Weight = $row4['order_weight'];
                                if (isset($row4['start_date'])){
                                    $bin2StartDt = DateTime::createFromFormat('Y-m-d H:i:s', $row4['start_date'])->format('d/m/Y H:i:sa');  
                                }

                                if (isset($row4['end_date'])){
                                    $bin2EndDt = DateTime::createFromFormat('Y-m-d H:i:s', $row4['end_date'])->format('d/m/Y H:i:sa');  
                                }                            
                            }

                            $count++;
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
                    
                    if ($type == 'Dual Bins'){
                        $message = '<html>
                                        <head>
                                            <style>
                                                @page {
                                                    margin-left: 0.5in;
                                                    margin-right: 0.5in;
                                                    margin-top: 0.1in;
                                                    margin-bottom: 0.1in;
                                                }

                                                .vehRow {
                                                    border: 1px dashed black;
                                                }

                                                .vehRow td {
                                                    border: 1px dashed black;
                                                }
                                            </style>

                                        </head>

                                        <body>
                                            <table style="width:100%; margin-top:10px">
                                                <tr>
                                                    <td style="width: 70%;">
                                                        <p style="margin-bottom: 0">
                                                            <span style="font-weight: bold;font-size: 18px;">'.$compname.'</span><br>
                                                            <span style="font-size: 12px;">'.$compaddress.' '.$compaddress2.'</span><br>
                                                            <span style="font-size: 12px;">'.$compaddress3.'</span><br>
                                                            <span style="font-size: 12px;">TEL: '.$compphone.'</span>
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p style="margin-bottom: 0;font-size: 12px;">
                                                            <span style="font-weight: bold;">Transaction ID</span><span style="margin-left:14px">:</span><span style="margin-left:10px">'.$row['transaction_id'].'</span><br>
                                                            <span>Weight Type</span><span style="margin-left:30.5px">:</span><span style="margin-left:10px">'.$row['weight_type'].'</span><br>
                                                            <span>Weight Status</span><span style="margin-left:24.5px">:</span><span style="margin-left:10px">'.$row['transaction_status'].'</span><br>
                                                            <span>Weight By</span><span style="margin-left:39.5px">:</span><span style="margin-left:10px">'.$row['created_by'].'</span>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <hr style="margin:0">
                                            <table style="width:100%">
                                                <tr>
                                                    <td style="width: 70%;">
                                                        <p style="margin-bottom: 0;font-size: 12px;">
                                                            <span>Customer Name</span><span style="margin-left:15px">:</span><span style="margin-left:10px">'.$customer.'</span><br>
                                                            <span>Product Name</span><span style="margin-left:24px">:</span><span style="margin-left:10px">'.$product.'</span><br>
                                                            <span>Transporter</span><span style="margin-left:37.3px">:</span><span style="margin-left:10px">'.$row['transporter'].'</span><br>
                                                            <span>Description</span><span style="margin-left:36.5px">:</span><span style="margin-left:10px">'.$row['destination'].'</span>
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p style="margin-bottom: 0;font-size: 12px;">
                                                            <span>Date / Time</span><span style="margin-left:33.5px">:</span><span style="margin-left:10px">'.$grossWeight1Date.'</span><br>
                                                            <span>Purchase No</span><span style="margin-left:29px">:</span><span style="margin-left:10px">'.($row['purchase_order'] ?? '').'</span><br>
                                                            <span>Invoice No</span><span style="margin-left:36.5px">:</span><span style="margin-left:10px">'.($row['invoice_no'] ?? '').'</span><br>
                                                            <span>Delivery No</span><span style="margin-left:31px">:</span><span style="margin-left:10px">'.($row['delivery_no'] ?? '').'</span><br>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <hr style="margin:0">
                                            <table style="width:100%; font-size:12px; text-align: center; ">
                                                <tr style="font-weight: 600;">
                                                    <td style="padding-top:10px; padding-bottom:10px;">Vehicle No</td>
                                                    <td>Item Description</td>
                                                    <td colspan="2">Status/Date/Time</td>
                                                    <td>Weight (kg)</td>
                                                </tr>
                                                <tr class="vehRow">
                                                    <td>'.$row['lorry_plate_no1'].'</td>
                                                    <td>'.$product.'</td>
                                                    <td>IN</td>
                                                    <td>'.$grossWeight1DateTime.'</td>
                                                    <td>'.$row['gross_weight1'].' (kg)</td>
                                                </tr>
                                                <tr class="vehRow">
                                                    <td></td>
                                                    <td></td>
                                                    <td>OUT</td>
                                                    <td>'.$tareWeight1DateTime.'</td>
                                                    <td>'.$row['tare_weight1'].' (kg)</td>
                                                </tr>
                                                <tr class="vehRow">
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan="2" style="font-weight: 600;">Total Weight (kg)</td>
                                                    <td><span style="font-weight: 600; border-bottom: 1px solid black">'.$row['nett_weight1'].' </span>(kg)</td>
                                                </tr>
                                                <tr class="vehRow">
                                                    <td>BIN 1</td>
                                                    <td>'.$productName1.'</td>
                                                    <td colspan="2">'.$bin1StartDt.' - '.$bin1EndDt.'</td>
                                                    <td>'.$bin1Weight.' (kg)</td>
                                                </tr>
                                                <tr class="vehRow">
                                                    <td>BIN 2</td>
                                                    <td>'.$productName2.'</td>
                                                    <td colspan="2">'.$bin2StartDt.' - '.$bin2EndDt.'</td>
                                                    <td>'.$bin2Weight.' (kg)</td>
                                                </tr>
                                                <tr class="vehRow">
                                                    <td style="font-weight: 600;">Remark:</td>
                                                    <td></td>
                                                    <td colspan="2" style="font-weight: 600;">Sub Total Net Weight</td>
                                                    <td><span style="font-weight: 600; border-bottom: 1px solid black">'.(float) $row['nett_weight1'] + (float) $bin1Weight + (float) $bin2Weight.' </span>(kg)</td>
                                                </tr>
                                            </table>

                                            <table style="width:100%; margin-top: 150px; font-size: 12px;">
                                                <tr>
                                                    <td width="50%" style="text-align: center;">
                                                        <div style="width: 250px; border-top: 1px dashed black; text-align: center; display: inline-block;">
                                                            <span>( Received Lorry )</span>
                                                        </div>
                                                    </td>
                                                    <td width="50%" style="text-align: center;">
                                                        <div style="width: 250px; border-top: 1px dashed black; text-align: center; display: inline-block;">
                                                            <span>( Weigh by : Eeven Kho )</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                            
                                        </body>

                                        </html>';
                    }else{
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