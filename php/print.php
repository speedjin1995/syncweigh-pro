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
                        <p>'.$compaddress.' '.$compaddress2.' '.$compaddress3.'</p>
                        <p>Tel: +6'.$compphone.'</p>
                        <hr>
                        
                        <div class="container">
                            <table class="info-table left-section">';


                        if ($row['transaction_status'] == 'Purchase'){
                            $message .= '<tr><td class="label">Supplier</td><td>:</td></td><td class="value">'.$customer.'</td></tr>';
                        }else{
                            $message .= '<tr><td class="label">Customer</td><td>:</td></td><td class="value">'.$customer.'</td></tr>';
                        }
                        
                    


                        $message .= '<tr><td class="label">Address</td><td>:</td><td class="value">'.$customerA.'<br> '.$customerA2.'<br> '.$customerA3.'</td></tr>
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
                    foreach($weighProducts as $row2) {
                        $message .=  '<tr>
                                            <td class="left-align">'.$rowCount. ' '.$row2['product_name'].'</td>
                                            <td>'.$row2['percentage'].'</td>
                                            <td>'.$row2['item_weight'].'</td>
                                            <td class="right-align">'.($row2['unit_price'] ?? '-').'</td>
                                            <td class="right-align">'.($row2['total_price'] ?? '-').'</td>
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