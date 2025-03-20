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
                    $datetime = explode(' ', date('d/m/Y h:i:s A', strtotime($row['transaction_date'])), 2);
                    list($date, $time) = $datetime;
                    $inDate = date('h:i:s A', strtotime(explode(' ', $row['gross_weight1_date'])[1]));
                    $outDate = date('h:i:s A', strtotime(explode(' ', $row['tare_weight1_date'])[1]));
                    
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

                    $wid = $row['id'];
                    if ($select_stmt2 = $db->prepare("SELECT * FROM Weight_Product WHERE weight_id=?")) {
                        $select_stmt2->bind_param('s', $wid);
            
                        // Execute the prepared query.
                        if (! $select_stmt2->execute()) {
                            echo json_encode(
                                array(
                                    "status" => "failed",
                                    "message" => "Something went wrong"
                                )); 
                        }
                        else{
                            $result = $select_stmt2->get_result();
                            $weighProducts = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array

                            if(empty($weighProducts)) {
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

                    $message = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Weighing Slip</title>
                        <style>
                            @media print{ 
                                 @page {
                                    size: a5 landscape;
                                    margin-left: 0.15in;
                                    margin-right: 0.15in;
                                    margin-top: 0.1in;
                                    margin-bottom: 0.1in;
                                }
                                /* Ensure the body content is contained within the page size */
                                body {
                                    zoom: 67%; 
                                    width: 100%; /* Full width of the A5 landscape */
                                    height: 100%; /* Full height of the A5 landscape */
                                    margin: 0;
                                    padding: 0;
                                    box-sizing: border-box; /* Ensure padding and borders are accounted for */
                                }
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    /* border: 1px solid black; */
                                }
                                th, td {
                                    border: 1px solid black;
                                    padding: 5px;
                                    /* margin: 0; */
                                    text-align: center;
                                    vertical-align: top;
                                    border-collapse: collapse;
                                }
                                td:nth-child(1), td:nth-child(2) {
                                    text-align: left;
                                }
                                .left-align {
                                text-align: left;
                                }
                                .right-align {
                                text-align: right;
                                }
                                .right-align td {
                                text-align: right;
                                }
                                .no-border {
                                    border: none;
                                }
                                .container {
                                    display: flex;
                                    justify-content: space-between;
                                }
                                .weight table, .product table {
                                    width: 100%;
                                    border-collapse: collapse;
                                }     
                                .weight td, .product td, .weight tr, .product tr {
                                    border-collapse: collapse;
                                }         
                                .weight, .product {
                                    padding: 0;
                                    margin: 0;
                                    vertical-align: top;
                                }
                                .left-section {
                                    width: 40%;
                                    text-align: left;
                                }
                                .right-section {
                                    width: 40%;
                                    text-align: left;
                                }
                                .computer-section {
                                    width: 40%;
                                    text-align: center;
                                    float: right;
                                }
                                .info-table {
                                    width: 100%;
                                    border: none;
                                }
                                .info-table td {
                                    padding: 4px 4px;
                                    border: none;
                                }
                                .info-table .label {
                                    font-weight: bold;
                                    text-align: left;
                                    white-space: nowrap;
                                }
                                .info-table .value {
                                    text-align: left;
                                    padding-left: 10px;
                                }
                                .info-table td:first-child {
                                    width: 50px;
                                }
                                .signature-section {
                                    display: flex;
                                    justify-content: space-between;
                                    margin-top: 10px;
                                }
                                .signature-box {
                                    width: 100%;
                                    text-align: center;
                                }
                                .dotted-line {
                                    border-bottom: 1px dotted black;
                                    width: 100%;
                                    display: inline-block;
                                }
                                .driver-sign {
                                    text-align: center; 
                                }
                                hr {
                                    border: 2px solid black; /* Adjust thickness and color */
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <h2>'.$compname.'</h2>
                        <p>'.$compaddress.' '.$compaddress2.' '$compaddress3.'</p>
                        <p>Tel: +6'.$compphone.'</p>
                        <hr>
                        
                        <div class="container">
                            <table class="info-table left-section">
                                <tr><td class="label">Customer</td><td>:</td></td><td class="value">'.$customer.'</td></tr>
                                <tr><td class="label">Address</td><td>:</td><td class="value">'.$customerA.'<br> '.$customerA2.'<br> '.$customerA3.'</td></tr>
                                <tr><td class="label">Contact</td><td>:</td><td class="value">Tel: +6'.$customerP.' | Fax: +6'.$customerE.'</td></tr>
                            </table>
                            <table class="info-table right-section">
                                <tr><td class="label">Weight Status</td><td>:</td><td class="value">'.$row['transaction_status'].'</td></tr>
                                <tr><td class="label">Weight No</td><td>:</td><td class="value">'.$row['transaction_id'].'</td></tr>
                                <tr><td class="label">Weight Date</td><td>:</td><td class="value">'.$date.'</td></tr>
                                <tr><td class="label">Weight Time</td><td>:</td><td class="value">'.$time.'</td></tr>
                                <tr><td class="label">Vehicle Plate</td><td>:</td><td class="value">'.$row['lorry_plate_no1'].'</td></tr>
                            </table>
                        </div>
                        <br>
                        <table style="border: none;">
                            <tr>
                                <td class="weight">
                                    <table style="border: none;">
                                        <tr>
                                            <th>Time</th>
                                            <th colspan="2">Weight (kg)</th>
                                        </tr> 
                                        <tr>
                                            <td>'.$inDate.'</td>
                                            <td>IN</td>
                                            <td class="right-align">'.($row['gross_weight1'] ?? '0').'</td>
                                        </tr>
                                        <tr>
                                            <td>'.$outDate.'</td>
                                            <td>OUT</td>
                                            <td class="right-align">'.($row['tare_weight1'] ?? '0').'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Total Weight (kg)</td>
                                            <td class="right-align">'.($row['final_weight'] ?? '0').'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Deduct (kg)</td>
                                            <td class="right-align">'.($row['reduce_weight'] ?? '0').'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Net Weight (kg)</td>
                                            <td class="right-align"'.($row['nett_weight1'] ?? '0').'</td>
                                        </tr>
                                        <!-- <tr>
                                            <td colspan="3"></td>
                                        </tr> -->
                                    </table>
                                </td>
                                <td class="product">
                                    <table style="border: none;">
                                        <tr>
                                            <th>Product Description</th>
                                            <th>(%)</th>
                                            <th>Weight (kg)</th>
                                            <th>U/Price (RM)</th>
                                            <th>Total Price (RM)</th>
                                        </tr>';

                    $rowCount = 1;
                    foreach($weighProducts as $row) {
                        $message .=  '<tr>
                                            <td class="left-align">'.$rowCount. ' '.$row['product_name'].'</td>
                                            <td>'.$row['percentage'].'</td>
                                            <td>'.$row['item_weight'].'</td>
                                            <td class="right-align">'.($row['unit_price'] ?? '-').'</td>
                                            <td class="right-align">'.($row['total_price'] ?? '-').'</td>
                                        </tr>';
                        $rowCount++;
                    }

                    if ($select_stmt3 = $db->prepare("SELECT SUM(total_price) AS total_amount FROM Weight_Product WHERE weight_id=?")) {
                        $select_stmt3->bind_param('s', $wid);
                        
                        // Execute the prepared query.
                        if ($select_stmt3->execute()) {
                            $result = $select_stmt3->get_result();
                            
                            if ($row3 = $result->fetch_assoc()) {
                                $totalAmount = $row3['total_amount'];
                            }
                        }
                    }

                                        $message .= '<tr>
                                            <td colspan="3" class="no-border"></td>
                                            <td>Total Amount</td>
                                            <td class="right-align">'.$totalAmount.'</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <p><strong>Remark: '.$row['remarks'].'</strong></p>
                        <br><br><br><br>
                        <div class="signature-section">
                            <div class="left-section">
                                <p class="dotted-line"></p>
                                <p class="driver-sign">Driver Sign</p>
                                <table class="info-table">
                                    <tr><td class="label">Name</td><td>:</td></td><td class="value">'.$customer.'</td></tr>
                                    <tr><td class="label">I/C</td><td>:</td><td class="value">'.$customerR.'</td></tr>
                                </table>
                            </div>
                            <div class="computer-section">
                                <p>THIS WEIGHING SLIP IS COMPUTER GENERATED AND</p>
                                <p>REQUIRES NO SIGNATURE & CHOP</p>
                                <p>Authorised by: '.$compname.'</p>
                            </div>
                        </div>
                    </body>
                    </html>';
                    
//                     $message = '<html>
//     <head>
//         <style>
//             @media print {
//                 @page {
//                     margin-left: 0.5in;
//                     margin-right: 0.5in;
//                     margin-top: 0.1in;
//                     margin-bottom: 0.1in;
//                 }
                
//             } 
                    
//             table {
//                 width: 100%;
//                 border-collapse: collapse;
                
//             } 
            
//             .table th, .table td {
//                 padding: 0.70rem;
//                 vertical-align: top;
//                 border-top: 1px solid #dee2e6;
                
//             } 
            
//             .table-bordered {
//                 border: 1px solid #000000;
                
//             } 
            
//             .table-bordered th, .table-bordered td {
//                 border: 1px solid #000000;
//                 font-family: sans-serif;
//                 font-size: 12px;
                
//             } 
            
//             .row {
//                 display: flex;
//                 flex-wrap: wrap;
//                 margin-top: 20px;
//                 margin-right: -15px;
//                 margin-left: -15px;
                
//             } 
            
//             .col-md-4{
//                 position: relative;
//                 width: 33.333333%;
//             }
//         </style>
//     </head>
//     <body>
//         <table style="width:100%">
//             <tr>
//                 <td style="width: 60%;">
//                     <p>
//                         <span style="font-weight: bold;font-size: 16px;">'.$compname.'</span><br><br>
//                         <span style="font-size: 12px;">'.$compaddress.'</span><br>
//                         <span style="font-size: 12px;">'.$compaddress2.'</span><br>
//                         <span style="font-size: 12px;">'.$compaddress3.'</span><br>
//                         <span style="font-size: 12px;">TEL: '.$compphone.' / FAX: '.$compiemail.'</span>
//                     </p>
//                 </td>
//                 <td>
//                     <p>
//                         <span style="font-weight: bold;font-size: 12px;">Transaction Date. : '.$row['transaction_date'].'</span><br>
//                         <span style="font-weight: bold;font-size: 12px;">Transaction No. &nbsp;&nbsp;&nbsp;: '.$row['transaction_id'].'</span><br>
//                         <span style="font-size: 12px;">Transaction Status: '.$row['transaction_status'].'</span><br>';
                        
//                     if($row['manual_weight'] == 'true'){
//                         $message .= '<span style="font-size: 12px;">Weight Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Manual Weighing</span><br>';
//                     }
//                     else{
//                         $message .= '<span style="font-size: 12px;">Weight Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Auto Weighing</span><br>';
//                     }
                    
//                     $message .= '<span style="font-size: 12px;">Invoice No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['invoice_no'] ?? '').'</span><br>
//                         <span style="font-size: 12px;">Delivery No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['delivery_no'] ?? '').'</span><br>
//                         <span style="font-size: 12px;">Purchase No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.($row['purchase_order'] ?? '').'</span><br>
//                         <span style="font-size: 12px;">Container No. &nbsp;&nbsp;&nbsp;&nbsp;: '.($row['container_no'] ?? '').'</span>
//                     </p>
//                 </td>
//             </tr>
//         </table>
//         <hr>
//         <table style="width:100%">
//         <tr>
//             <td style="width: 40%;">
//                 <p>
//                     <span style="font-weight: bold;font-size: 16px;">'.$customer.'</span><br>
//                 </p>
//             </td>
//             <td style="width: 20%;">
//                 <p>&nbsp;</p>
//             </td>
//         </tr>
//         <tr>
//             <td>
//                 <p>
//                     <span style="font-size: 12px;">'.$customerA.'</span><br>
//                     <span style="font-size: 12px;">'.$customerA2.'</span><br>
//                     <span style="font-size: 12px;">'.$customerA3.'</span><br>
//                     <span style="font-size: 12px;">TEL: '.$customerP.'/ FAX: '.$customerE.'</span>
//                 </p>
//             </td>
//             <td style="width: 20%;"></td>
//             <td>
//                 <p>
//                     <span style="font-size: 12px;">Weight Date & Time : '.($row['gross_weight1_date'] ?? '').'</span><br>
//                     <span style="font-size: 12px;">User Weight &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$row['created_by'].'</span><br>
//                 </p>
//                 <table style="width:100%; border:1px solid black;">
//                     <tr>';
//                 if($row['transaction_status'] == 'Sales'){
//                     $message .= '<th colspan="2" style="border:1px solid black; font-size: 14px;">Order Weight</th>
//                     <th colspan="2" style="border:1px solid black; font-size: 14px;">Variance Weight</th>
//                     <th style="border:1px solid black; font-size: 14px;">Variance</th>';
//                 }
//                 else{
//                     $message .= '<th colspan="2" style="border:1px solid black; font-size: 14px;">Supply Weight</th>
//                     <th colspan="2" style="border:1px solid black; font-size: 14px;">Variance Weight</th>
//                     <th style="border:1px solid black; font-size: 14px;">Variance</th>';
//                 }

//                 if($row['transaction_status'] == 'Sales'){
//                     $final = $row['final_weight'];
//                     $trueWeight = $row['order_weight'] ?? '0';
//                     $different = 0;

//                     if ($variance == 'W') {
//                         if ($low !== null && ((float)$final < (float)$trueWeight - (float)$low || (float)$final > (float)$trueWeight + (float)$low)) {
//                             $different = (float)$final < (float)$trueWeight - (float)$low;
//                         } elseif ($high !== null && ((float)$final < (float)$trueWeight - (float)$high || (float)$final > (float)$trueWeight + (float)$high)) {
//                             $different = (float)$final < (float)$trueWeight - (float)$high;
//                         }
//                     } 
//                     elseif ($variance == 'P') {
//                         if ($low !== null && ((float)$final < (float)$trueWeight * (1 - (float)$low / 100) || (float)$final > (float)$trueWeight * (1 + (float)$low / 100))) {
//                             $different = (float)$final - (float)$trueWeight * (1 - (float)$low / 100);
//                         } elseif ($high !== null && ((float)$final < (float)$trueWeight * (1 - (float)$high / 100) || (float)$final > (float)$trueWeight * (1 + (float)$high / 100))) {
//                             $different = (float)$final - (float)$trueWeight * (1 - (float)$high / 100);
//                         }
//                     }

//                     $message .= '</tr>
//                     <tr>
//                         <td style="border:1px solid black;">'.($row['order_weight'] ?? '').'</td>
//                         <td style="border:1px solid black;">kg</td>
//                         <td style="border:1px solid black;">'.($row['weight_different'] ?? '').'</td>
//                         <td style="border:1px solid black;">kg</td>
//                         <td style="border:1px solid black;">'.$different.' '.($variance == "W" ? 'kg' : '%').'</td>
//                     </tr>';
//                 }
//                 else{
//                     $final = $row['final_weight'];
//                     $trueWeight = $row['supplier_weight'] ?? '0';
//                     $different = 0;

//                     if ($variance == 'W') {
//                         if ($low !== null && ((float)$final < (float)$trueWeight - (float)$low || (float)$final > (float)$trueWeight + (float)$low)) {
//                             $different = (float)$final < (float)$trueWeight - (float)$low;
//                         } elseif ($high !== null && ((float)$final < (float)$trueWeight - (float)$high || (float)$final > (float)$trueWeight + (float)$high)) {
//                             $different = (float)$final < (float)$trueWeight - (float)$high;
//                         }
//                     } 
//                     elseif ($variance == 'P') {
//                         if ($low !== null && ((float)$final < (float)$trueWeight * (1 - (float)$low / 100) || (float)$final > (float)$trueWeight * (1 + (float)$low / 100))) {
//                             $different = (float)$final - (float)$trueWeight * (1 - (float)$low / 100);
//                         } elseif ($high !== null && ((float)$final < (float)$trueWeight * (1 - (float)$high / 100) || (float)$final > (float)$trueWeight * (1 + (float)$high / 100))) {
//                             $different = (float)$final - (float)$trueWeight * (1 - (float)$high / 100);
//                         }
//                     }

//                     $message .= '</tr>
//                     <tr>
//                         <td style="border:1px solid black;">'.($row['supplier_weight'] ?? '').'</td>
//                         <td style="border:1px solid black;">kg</td>
//                         <td style="border:1px solid black;">'.($row['weight_different'] ?? '').'</td>
//                         <td style="border:1px solid black;">kg</td>
//                         <td style="border:1px solid black;">'.$different.' '.($variance == "W" ? 'kg' : '%').'</td>
//                     </tr>';
//                 }
                        
//                 $message .= '</table>
//             </td>
//         </tr>
//         </table><br>
//         <table style="width:100%; border:1px solid black;">
//             <tr>
//                 <th style="border:1px solid black;font-size: 14px;">Vehicle No.</th>
//                 <th style="border:1px solid black;font-size: 14px;">Product Name</th>
//                 <th style="border:1px solid black;font-size: 14px;">Unit Price</th>
//                 <th colspan="2" style="border:1px solid black;font-size: 14px;">Total Weight</th>
//                 <th style="border:1px solid black;font-size: 14px;">Total Price</th>
//             </tr>
//             <tr>
//                 <td style="border:1px solid black;font-size: 14px;">'.$row['lorry_plate_no1'].'</td>
//                 <td style="border:1px solid black;font-size: 14px;">'.$row['product_name'].'</td>
//                 <td style="border:1px solid black;font-size: 14px;">RM '.$price.'</td>
//                 <td style="border:1px solid black;font-size: 14px;">'.$row['nett_weight1'].'</td>
//                 <td style="border:1px solid black;font-size: 14px;">kg</td>
//                 <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.number_format(((float)$price * (float)$row['nett_weight1']), 2, '.', '').'</td>
//             </tr>';

//             if($row['weight_type'] == 'Container'){
//                 $message .= '<tr>
//                     <td style="border:1px solid black;font-size: 14px;">'.$row['lorry_plate_no2'].'</td>
//                     <td style="border:1px solid black;font-size: 14px;">'.$row['product_name'].'</td>
//                     <td style="border:1px solid black;font-size: 14px;">RM '.$price.'</td>
//                     <td style="border:1px solid black;font-size: 14px;">'.$row['nett_weight2'].'</td>
//                     <td style="border:1px solid black;font-size: 14px;">kg</td>
//                     <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.number_format(((float)$price * (float)$row['nett_weight2']), 2, '.', '').'</td>
//                 </tr>';
//             }

//             $message .= '<tr>
//                 <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">Subtotal</td>
//                 <td style="border:1px solid black;font-size: 14px;">'.$row['final_weight'].'</td>
//                 <td style="border:1px solid black;font-size: 14px;">kg</td>
//                 <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['sub_total'].'</td>
//             </tr>
//             <tr>
//                 <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">SST</td>
//                 <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="2"></td>
//                 <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['sst'].'</td>
//             </tr>
//             <tr>
//                 <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="3">Total Price</td>
//                 <td style="border:1px solid black;font-size: 14px;text-align:right;" colspan="2"></td>
//                 <td style="border:1px solid black;font-weight: bold;font-size: 14px;">RM '.$row['total_price'].'</td>
//             </tr>
//         </table>
//         <p>
//             <span style="font-size: 12px;font-weight: bold;">Remark: </span>
//             <span style="font-size: 12px;">'.$row['remarks'].'</span>
//         </p>
//     </body>
// </html>';
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