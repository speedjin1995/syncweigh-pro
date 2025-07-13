<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
include 'phpqrcode/qrlib.php';
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

if(isset($_POST['userID'], $_POST["file"])){
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if (empty($_POST["prePrint"])) {
        $prePrintStatus = 'N';
    } else {
        $prePrintStatus = trim($_POST["prePrint"]);
    }

    if ($select_stmt = $db->prepare("SELECT * FROM Weight WHERE id=?")) {
        $select_stmt->bind_param('s', $id);

        $compname = 'BLACKTOP LANCHANG SDN BHD';
        $compaddress = '37, Jalan Perusahaan Amari,';
        $compaddress2 = 'Amari Business Park,';
        $compaddress3 = '68100 Batu Caves, Selangor Darul Ehsan';
        $compphone = '+603-6096 0383';
        $compiemail = 'lowct@eastrock.com.my';
        $compiwebsite = 'www.eastrock.com.my';


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
                $plantCode = $row['plant_code'];
                $productCode = $row['product_code'];
                $productName = $row['product_name'];
                $transportCode = $row['transporter_code'];
                $transportName = $row['transporter'];
                $destinationCode = $row['destination_code'];
                $destinationName = $row['destination'];
                $projectCode = $row['site_code'];
                $projectName = $row['site_name'];
                $loadingChitNo = $row['transaction_id'];
                $lorryNo = $row['lorry_plate_no1'];
                $poNo = $row['purchase_order'];
                $weightType = $row['weight_type'];
                $doNo = $row['delivery_no'];
                $exDel = $row['ex_del'] === 'EX' ? 'E' : 'D';
                $complete = $row['is_complete'];
                $grossWeightDate = new DateTime($row['gross_weight1_date']);
                $formattedGrossWeightDate = $grossWeightDate->format('H:i A');
                $tareWeightDate =  new DateTime($row['tare_weight1_date']);
                $formattedTareWeightDate = $tareWeightDate->format('H:i A');
                $grossWeight = number_format($row['gross_weight1']);
                $tareWeight = number_format($row['tare_weight1']);
                $nettWeight = number_format($row['nett_weight1']);
                $grossWeight2 = number_format($row['gross_weight2']);
                $tareWeight2 = number_format($row['tare_weight2']);
                $nettWeight2 = number_format($row['nett_weight2']);
                $supplierWeight =  number_format($row['supplier_weight']);
                $weightDifference = number_format($row['weight_different']);
                $sysdate = date("d-m-Y");
                $weightBy = searchNamebyId($row['created_by'], $db);
                $createDate = new DateTime($row['created_date']);
                $formattedCreateDate = $createDate->format('d-m-Y');
                $transDate = new DateTime($row['tare_weight1_date']);
                $transDateOnly = $transDate->format('d-m-Y');
                //$transDateOnly = date('Y-m-d', strtotime($transDate));
                $remarks = $row['remarks'];
                $plantCode = $row['plant_code'];
                $message = '';

                $queryPlantAddr = "SELECT *  FROM Plant WHERE plant_code='$plantCode'";
                
                if ($plant_stmt_addr = $db->prepare($queryPlantAddr)) {
                    // Execute the prepared query.
                    $plant_stmt_addr->execute();
                    $resultAddr = $plant_stmt_addr->get_result();
                       
                    if ($rowAddr = $resultAddr->fetch_assoc()) {
                        $compaddress = $rowAddr['address_line_1'];
                        $compaddress2 = $rowAddr['address_line_2'];
                        $compaddress3 = $rowAddr['address_line_3'];
                        $compphone = $rowAddr['phone_no'];
                    }
                }
                
                if($type == 'Sales' && $complete == 'Y'){
                    if($row['delivery_no'] == null || $row['delivery_no'] == ''){
                        $deliverOrderNo = $plantCode.'/DO';
                        $queryPlant = "SELECT do_no as curcount FROM Plant WHERE plant_code='$plantCode'";
        
                        if ($plant_stmt = $db->prepare($queryPlant)) {
                            // Execute the prepared query.
                            $plant_stmt->execute();
                            $result2 = $plant_stmt->get_result();
                            
                            if ($row2 = $result2->fetch_assoc()) {
                                $charSize = strlen($row2['curcount']);
                                $misValue = $row2['curcount'];
            
                                for($i=0; $i<(5-(int)$charSize); $i++){
                                    $deliverOrderNo.='0';  // S0000
                                }
                        
                                $deliverOrderNo .= $misValue;  //S00009

                                // Update back to Plant
                                $misValue++;
                                $queryPlantU = "UPDATE Plant SET do_no=? WHERE plant_code='$plantCode'";
                                
                                if ($update_plant_stmt = $db->prepare($queryPlantU)){
                                    $update_plant_stmt->bind_param('s', $misValue);
                                    $update_plant_stmt->execute();
                                    $update_plant_stmt->close();
                                } 

                                // Update back to Weight
                                $queryWeightU = "UPDATE Weight SET delivery_no=? WHERE id='$id'";
                                
                                if ($update_weight_stmt = $db->prepare($queryWeightU)){
                                    $update_weight_stmt->bind_param('s', $deliverOrderNo);
                                    $update_weight_stmt->execute();
                                    $update_weight_stmt->close();
                                } 
                            }

                            $plant_stmt->close();
                        }
                    }
                    else{
                        $deliverOrderNo = $row['delivery_no'];
                    }
                }
                else{
                    $deliverOrderNo = $row['delivery_no'];
                }
                
                if($type == 'Sales' || $type == 'Local'){
                    $customerCode = $row['customer_code'];
                    $customerName = $row['customer_name'];
                }
                else{
                    $customerCode = $row['supplier_code'];
                    $customerName = $row['supplier_name'];
                    $productCode = $row['raw_mat_code'];
                    $productName = $row['raw_mat_name'];
                }

                if($complete == 'N'){
                    // Put your loading chit
                    $message = '<html>
                            <head>
                                <!-- Bootstrap CSS -->
                                <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
                                <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
                                <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
                                <link href="assets/css/custom.min.css" id="app-style" rel="stylesheet" type="text/css" />

                                <style>
                                    @page {
                                        size: A5 landscape;
                                        margin: 10mm;
                                    }

                                    .custom-hr {
                                        border-top: 1px solid #000;        /* Remove the default border */
                                        height: 1px;         /* Define the thickness */
                                        margin: 0;           /* Reset margins */
                                    }

                                    .body_1 p span{
                                        margin-right: 15px;
                                    }

                                    .body_3 {
                                        border-top: 1px dashed black;
                                        text-align: center;
                                    }

                                    .signature {
                                        padding-top: 20px; 
                                        padding-left: 80px; 
                                        padding-right: 80px;
                                    }

                                    .spacer {
                                        margin: 16px 0; /* Adjust the spacing as needed */
                                        height: 19.5px;   /* Set a height to create space */
                                    }
                                </style>

                            </head>

                            <body>
                                <div class="container-full">
                                    <div class="header row">
                                        <h2 style="color: black; font-weight: bold;">BLACKTOP LANCHANG SDN BHD</h2>
                                        <div class="col-7" style="text-align: left;">
                                            <p style="font-size: 11px; margin-bottom: 3px;">(1373003-H) <br> '.$compaddress.' <br>'.$compaddress2.'<br>'.$compaddress3.'<br>TEL: '.$compphone.'</p>
                                        </div>
                                        <div class="col-5 align-self-end">
                                            <h3 style="font-weight: bold; margin-bottom: 0px;">LOADING CHIT</h3>
                                        </div>
                                    </div>

                                    <div class="row mb-2 mt-2" style="border-top: 1px solid black;">
                                        <div class="col-8 body_1 mt-2">
                                            <p>WEIGHING DATE<span style="margin-left: 25px;">:</span>'.$transDateOnly.'</p>
                                            <p>CUSTOMER<span style="margin-left: 55px;">:</span>'.$customerCode. ' ' . $customerName .'</p>
                                            <p>VEHICLE NO.<span style="margin-left: 48px;">:</span>'.$lorryNo.'</p>
                                            <p>PRODUCT<span style="margin-left: 62px;">:</span>'.$productCode. ' ' . $productName .'</p>
                                            <p>PLANT NO.<span style="margin-left: 59px;">:</span></p>
                                            <p>WEIGHT IN<span style="margin-left: 57px;">:</span>'.($weightType == 'Normal' ? $grossWeight : ((float)$grossWeight + (float)$grossWeight2)).' KG</p>
                                        </div>
                                        <div class="col-4 body_1 mt-2">
                                            <p>LOADING CHIT NO.<span style="margin-left: 10px;">:</span>'.$loadingChitNo.'</p>
                                            <p class="spacer"></p>
                                            <p class="spacer"></p>
                                            <p>TIME IN<span style="margin-left: 90px;">:</span>'.$formattedGrossWeightDate.'</p>
                                        </div>
                                    </div>

                                    <div class="row" style="padding-top: 40px;">
                                        <div class="col-6 signature">
                                            <div class="body_3">
                                                WEIGHED BY <br>'.$weightBy.'
                                            </div>
                                        </div>
                                        <div class="col-6 signature">
                                            <div class="body_3">
                                                CHECKED BY
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </body></html>';
                }
                else{
                    if($type == 'Sales'){
                        if ($prePrintStatus == 'N'){
                            $message = '<html>
                                            <head>
                                                <!-- Bootstrap CSS -->
                                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
                                                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" type="text/css" />
                                                <link href="https://your-cdn-link-to-app.min.css" rel="stylesheet" type="text/css" />
                                                <link href="https://your-cdn-link-to-custom.min.css" rel="stylesheet" type="text/css" />
                        
                                                <style>
                                                    @page {
                                                        size: A5 landscape;
                                                        margin: 10px;
                                                    }
                        
                                                    .custom-hr {
                                                        border-top: 1px solid #000;        /* Remove the default border */
                                                        height: 1px;         /* Define the thickness */
                                                        margin: 0;           /* Reset margins */
                                                    }
                                                </style>
                                            </head>
                        
                                            <body>
                                                <div class="container-full">
                                                    <br>
                                                    <div class="header mb-3">
                                                        <div class="row col-12">
                                                            <div class="col-10">
                                                                <div class="col-12" style="font-size: 17px; font-weight: bold;margin-left:10px">
                                                                    BLACKTOP LANCHANG SDN BHD<span style="font-size: 12px; margin-left: 5px">198501006021 (138463-T)</span>
                                                                </div>
                                                                <div class="col-12" style="font-size: 12px">
                                                                    <span style="margin-left:10px">Office</span><span style="margin-left:25px">:&nbsp;37, Jalan Perusahaan Amari, Amari Business Park, 68100 Batu Caves, Selangor Darul Ehsan</span>
                                                                </div>
                                                                <div class="col-12" style="font-size: 12px">
                                                                    <span style="margin-left:45px">Tel&nbsp;&nbsp;:&nbsp;&nbsp; +603-6096 0383</span>
                                                                    <span style="margin-left:10px">Email&nbsp;&nbsp;:&nbsp;&nbsp; lowct@eastrock.com.my</span>
                                                                    <span style="margin-left:10px">Website&nbsp;&nbsp;:&nbsp;&nbsp; www.eastrock.com.my</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-2">
                                                                <img src="assets/images/eastrock_logo.jpg" alt="East Rock Logo" width="100%" style="margin-left:20px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-7" style="margin-top:50px">
                                                            <table class="table">
                                                                <tbody>
                                                                    <tr>
                                                                        <td width="25%" style="border: 0px solid black;">
                                                                            <div class="row">
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>CUSTOMER</b></div>
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>PROJECT</b></div>
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>PRODUCT</b></div>
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>DELIVERED TO</b></div>
                                                                                <div class="col-12" style="height: 25px;"></div>
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>DELIVERED BY</b></div>
                                                                            </div>
                                                                        </td>
                                                                        <td colspan="2" width="75%" style="border: 1px solid black;">
                                                                            <div class="row" style="margin-left: 1px">
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'. $customerCode . ' ' . $customerName .'</div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'.$projectCode. ' ' . $projectName .'</div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'. $productCode . ' ' . $productName .'</div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'. $destinationCode . ' ' . $destinationName .'</div>
                                                                                <div class="col-12" style="height: 15px;"></div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'. $transportCode . ' ' . $transportName .'</div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="font-size: 9px;">
                                                                        <td width="31%" style="border: 0px solid black; margin-bottom:0px;">
                                                                            <div style="margin-top:60px">
                                                                                <hr class="custom-hr mb-1">
                                                                                <div class="text-center" style="font-size: 11px;">Stamped And Signed</div>
                                                                            </div>
                                                                        </td>
                                                                        <td width="31%" style="border: 0px solid black; padding-bottom:0px; ">
                                                                            <div style="margin-top:60px;">
                                                                                <hr class="custom-hr mb-1">
                                                                                <div class="text-center" style="font-size: 11px;">Lorry Driver</div>
                                                                            </div>
                                                                        </td>
                                                                        <td width="38%" style="border: 1px solid black;">
                                                                            <div class="row">
                                                                                <div class="col-12 mb-4">
                                                                                    <span style="font-size: 12px;"><b>Waiting Hours:</b></span>
                                                                                    <span style="margin-left: 10px; font-size: 12px;"></span>
                                                                                </div>
                                                                                <div class="col-12 mb-3">
                                                                                    <span style="font-size: 12px;"><b>From:</b></span>
                                                                                    <span style="margin-left: 10px; font-size: 12px;"></span>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <span style="font-size: 12px;"><b>To:</b></span>
                                                                                    <span style="margin-left: 10px; font-size: 12px;"></span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="3" style="border:0;padding-top:15px;">
                                                                            <span style="font-size: 12px">REMARK: '.$remarks.'</span>  
                                                                        </td>
                                                                    </tr>
                                                                </tbody>                
                                                            </table>
                                                        </div>
                                                        <div class="col-4">
                                                            <table class="table">
                                                                <tbody style="font-size: 11px">
                                                                    <tr style="border: 1px solid black;">
                                                                        <td colspan="2">
                                                                            <div class="row" >
                                                                                <div class="col-12 mb-2">
                                                                                    <span style="font-size: 13px;"><b>Date</b></span><span style="margin-left: 70px"><b>:</b></span>
                                                                                    <span style="margin-left: 8px;font-size: 13px;">'.$transDateOnly.'</span>
                                                                                </div>
                                                                                <div class="col-12 mb-2">
                                                                                    <span style="font-size: 13px;"><b>Loading Chit No</b></span><span style="margin-left: 20px"><b>:</b></span>
                                                                                    <span style="margin-left: 8px;font-size: 13px;">'.$loadingChitNo.'</span>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <span style="font-size: 13px;"><b>Delivery Order No</b></span><span style="margin-left: 10px"><b>:</b></span>
                                                                                    <span style="margin-left: 8px;font-size: 13px;">'.$deliverOrderNo.' ('.$exDel.')</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="border: 1px solid black;">
                                                                        <td colspan="2">
                                                                            <div class="row">
                                                                                <div class="col-12 mb-2">
                                                                                    <span style="font-size: 13px;"><b>Lorry No</b></span><span style="margin-left: 15px"><b>:</b></span>
                                                                                    <span style="margin-left: 8px;font-size: 13px;">'.$lorryNo.'</span>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <span style="font-size: 13px;"><b>P/O No</b></span><span style="margin-left: 20px"><b>:</b></span>
                                                                                    <span style="margin-left: 8px;font-size: 13px;">'.$poNo.'</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="border: 1px solid black;">
                                                                        <td style="border: 1px solid black; text-align: center;" width="50%"><b>Time</b></td>
                                                                        <td style="border: 1px solid black; text-align: center;" width="50%"><b>Weight (MT)</b></td>
                                                                    </tr>
                                                                    <tr style="border: 1px solid black; height: 50px;">
                                                                        <td style="border: 1px solid black; text-align: center;" width="50%">
                                                                            <span style="font-size: 13px;">'.$formattedGrossWeightDate.'</span>
                                                                            <br>
                                                                            <span style="font-size: 13px;">'.$formattedTareWeightDate.'</span>
                                                                        </td>
                                                                        <td style="border: 1px solid black; text-align: center;" width="50%">
                                                                            <span style="font-size: 13px;">'.$grossWeight.'</span>
                                                                            <br>
                                                                            <span style="font-size: 13px;">'.$tareWeight.'</span>
                                                                            <hr style="width:30%; margin-left: auto; margin-right: auto; margin-top: 5px;">
                                                                            <div style="margin-top: -10px;font-size: 13px;">'.$nettWeight.'</div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2" style="border: 0px solid black; padding-bottom: 40px;font-size: 13px;">
                                                                            <div class="row">
                                                                                <div class="col-12">
                                                                                    <span><b>Weighted by :</b></span>
                                                                                    <span style="margin-left: 15px">'.$weightBy.'</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2" style="border: 0px solid black; text-align: right; padding-top:30px;">
                                                                            <div class="row">
                                                                                <div class="col-12">
                                                                                    <span><b style="font-size: 13px">No : '.$loadingChitNo.'</b><b style="font-size: 20px; color: red;"></b></span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody> 
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </body>
                                        </html>';
                        }
                        else{
                            $message = '<html>
                                            <head>
                                                <!-- Bootstrap CSS -->
                                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
                                                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" type="text/css" />
                                                <link href="https://your-cdn-link-to-app.min.css" rel="stylesheet" type="text/css" />
                                                <link href="https://your-cdn-link-to-custom.min.css" rel="stylesheet" type="text/css" />
                        
                                                <style>
                                                    @page {
                                                        size: A5 landscape;
                                                        margin: 10px;
                                                    }
                        
                                                    .custom-hr {
                                                        border-top: 1px solid #000;        /* Remove the default border */
                                                        height: 1px;         /* Define the thickness */
                                                        margin: 0;           /* Reset margins */
                                                    }

                                                    .hideElement {
                                                        visibility: hidden;
                                                    }

                                                </style>
                                            </head>
                        
                                            <body>
                                                <div class="container-full">
                                                    <br>
                                                    <div class="header mb-3 hideElement">
                                                        <div class="row col-12">
                                                            <div class="col-10">
                                                                <div class="col-12" style="font-size: 17px; font-weight: bold;margin-left:10px">
                                                                    BLACKTOP LANCHANG SDN BHD<span style="font-size: 12px; margin-left: 5px">198501006021 (138463-T)</span>
                                                                </div>
                                                                <div class="col-12" style="font-size: 12px">
                                                                    <span style="margin-left:10px">Office</span><span style="margin-left:25px">:&nbsp;37, Jalan Perusahaan Amari, Amari Business Park, 68100 Batu Caves, Selangor Darul Ehsan</span>
                                                                </div>
                                                                <div class="col-12" style="font-size: 12px">
                                                                    <span style="margin-left:45px">Tel&nbsp;&nbsp;:&nbsp;&nbsp; +603-6096 0383</span>
                                                                    <span style="margin-left:10px">Email&nbsp;&nbsp;:&nbsp;&nbsp; lowct@eastrock.com.my</span>
                                                                    <span style="margin-left:10px">Website&nbsp;&nbsp;:&nbsp;&nbsp; www.eastrock.com.my</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-2">
                                                                <img src="assets/images/eastrock_logo.jpg" alt="East Rock Logo" width="100%" style="margin-left:20px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-7">
                                                            <table class="table" style="margin-top:60px">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="hideElement" width="10%" style="border: 0;">
                                                                            <div class="row">
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>CUSTOMER</b></div>
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>PROJECT</b></div>
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>PRODUCT</b></div>
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>DELIVERED TO</b></div>
                                                                                <div class="col-12" style="height: 25px;"></div>
                                                                                <div class="col-12" style="height: 25px;font-size: 14px;"><b>DELIVERED BY</b></div>
                                                                            </div>
                                                                        </td>
                                                                        <td colspan="2" width="90%" style="border: 0;">
                                                                            <div class="row" style="margin-left: -40px">
                                                                                <div class="col-12" style="height: 25px;"></div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'. $customerCode . ' ' . $customerName .'</div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'.$projectCode. ' ' . $projectName .'</div>
                                                                                <div class="col-12" style="height: 5px;"></div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'. $productCode . ' ' . $productName .'</div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'. $destinationCode . ' ' . $destinationName .'</div>
                                                                                <div class="col-12" style="height: 10px;"></div>
                                                                                <div class="col-12 p-0" style="height: 25px;font-size: 14px;">'. $transportCode . ' ' . $transportName .'</div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="font-size: 9px;">
                                                                        <td width="31%" class="hideElement" style="border: 0; margin-bottom:0px;">
                                                                            <div style="margin-top:60px">
                                                                                <hr class="custom-hr mb-1">
                                                                                <div class="text-center" style="font-size: 11px;">Stamped And Signed</div>
                                                                            </div>
                                                                        </td>
                                                                        <td width="31%" class="hideElement" style="border: 0; padding-bottom:0px; ">
                                                                            <div style="margin-top:60px;">
                                                                                <hr class="custom-hr mb-1">
                                                                                <div class="text-center" style="font-size: 11px;">Lorry Driver</div>
                                                                            </div>
                                                                        </td>
                                                                        <td width="38%" style="border: 0;">
                                                                            <div class="row">
                                                                                <div class="col-12 mb-4">
                                                                                    <span class="hideElement" style="font-size: 12px;"><b>Waiting Hours:</b></span>
                                                                                    <span style="margin-left: 10px; font-size: 12px;"></span>
                                                                                </div>
                                                                                <div class="col-12 mb-3">
                                                                                    <span class="hideElement" style="font-size: 12px;"><b>From:</b></span>
                                                                                    <span style="margin-left: 10px; font-size: 12px;"></span>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <span class="hideElement" style="font-size: 12px;"><b>To:</b></span>
                                                                                    <span style="margin-left: 10px; font-size: 12px;"></span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="3" style="border:0;padding-top:30px;">
                                                                            <span style="font-size: 12px">REMARK: </span><span style="font-size: 12px">'.$remarks.'</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>                
                                                            </table>
                                                        </div>
                                                        <div class="col-1">
                                                        </div>
                                                        <div class="col-4">
                                                            <table class="table" style="margin-top:-10px">
                                                                <tbody style="font-size: 11px">
                                                                    <tr style="border: 0;">
                                                                        <td colspan="2" style="border:0;">
                                                                            <div class="row">
                                                                                <div class="col-12 mb-2">
                                                                                    <span class="hideElement" style="font-size: 13px;"><b>Date</b></span><span class="hideElement" style="margin-left: 70px"><b>:</b></span>
                                                                                    <span style="margin-left: -30px;font-size: 13px;">'.$transDateOnly.'</span>
                                                                                </div>
                                                                                <div class="col-12 mb-2">
                                                                                    <span class="hideElement" style="font-size: 13px;"><b>Loading Chit No</b></span><span class="hideElement" style="margin-left: 8px"><b>:</b></span>
                                                                                    <span style="margin-left: -35px;font-size: 13px;">'.$loadingChitNo.'</span>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <span class="hideElement" style="font-size: 13px;"><b>Delivery Order No</b></span><span class="hideElement" style="margin-left: 5px"><b>:</b></span>
                                                                                    <span style="margin-left: -45px;font-size: 13px;">'.$deliverOrderNo.' ('.$exDel.')</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="border: 0;">
                                                                        <td colspan="2" style="border:0;">
                                                                            <div class="row" style="border:0;">
                                                                                <br><div class="col-12 mb-2">
                                                                                    <span class="hideElement" style="font-size: 13px;"><b>Lorry No</b></span><span class="hideElement" style="margin-left: 15px"><b>:</b></span>
                                                                                    <span style="margin-left: -25px;font-size: 13px;">'.$lorryNo.'</span>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <span class="hideElement" style="font-size: 13px;"><b>P/O No</b></span><span class="hideElement" style="margin-left: 20px"><b>:</b></span>
                                                                                    <span style="margin-left: -20px;font-size: 13px;">'.$poNo.'</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="border: 0">
                                                                        <td class="hideElement" style="border: 0; text-align: center;" width="50%"><b>Time</b></td>
                                                                        <td class="hideElement" style="border: 0; text-align: center;" width="50%"><b>Weight (MT)</b></td>
                                                                    </tr>
                                                                    <tr style="border: 0; height: 65px;">
                                                                        <td style="border: 0; text-align: center;" width="50%">
                                                                            <br><span style="margin-left: -60px;font-size: 13px;">'.$formattedGrossWeightDate.'</span>
                                                                            <br>
                                                                            <span style="margin-left: -60px;font-size: 13px;">'.$formattedTareWeightDate.'</span>
                                                                        </td>
                                                                        <td style="border: 0; text-align: center;" width="50%">
                                                                            <br><span style="font-size: 13px;">'.($weightType == 'Normal' ? $grossWeight : ((float)$grossWeight + (float)$grossWeight2)).'</span>
                                                                            <br>
                                                                            <span style="font-size: 13px;">'.($weightType == 'Normal' ? $tareWeight : ((float)$tareWeight + (float)$tareWeight2)).'</span>
                                                                            <hr style="width:30%; margin-left: auto; margin-right: auto; margin-top: 5px;">
                                                                            <div style="margin-top: -10px;font-size: 13px;">'.($weightType == 'Normal' ? $nettWeight : ((float)$nettWeight + (float)$nettWeight2)).'</div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2" style="border: 0; padding-bottom: 60px;font-size: 13px;">
                                                                            <br><br><div class="row">
                                                                                <div class="col-12">
                                                                                    <span class="hideElement"><b>Weighted by :</b></span>
                                                                                    <span style="margin-left: -35px">'.$weightBy.'</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody> 
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </body>
                                        </html>';
                        }
                        
                        $select_stmt->close();
                    }
                    elseif ($type == 'Purchase'){ 
                        $message = '
                            <html>
                                <head>
                                    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                                    <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />
                                    <style>
                                        @page {
                                            size: A5 landscape;
                                            margin: 10mm;
                                        }

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
                                            padding-left: 30px; 
                                            padding-right: 30px;
                                        }
                                    </style>

                                </head>

                                <body>
                                    <div class="container-full">
                                        <div class="header">
                                            <div style="text-align: center;">
                                                <h3 class="mb-0 fw-bold text-dark">BLACKTOP LANCHANG SDN BHD</h3>
                                                <p style="font-size: 10px; margin-bottom: 3px;">(1373003-H) <br> '.$compaddress.' <br>'.$compaddress2.'<br>'.$compaddress3.'<br>TEL: '.$compphone.'</p>
                                                <h4 class="pb-2 fw-bold text-dark"><span style="border-bottom: 1px solid black">PURCHASE WEIGHING TICKET</span></h4>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-8 body_1">
                                                <p>WEIGHING DATE<span style="margin-left: 44.5px; margin-right:10px;">:</span>'.$transDateOnly.'</p>
                                                <p>VEHICLE NO.<span style="margin-left: 66.5px; margin-right:10px;">:</span>'.$lorryNo.'</p>
                                                <p>TRANSPORTER CODE<span style="margin-left: 10px; margin-right:10px;">:</span>'.$transportCode . '<span style="margin-left:59px">' .$transportName.'</span></p>
                                                <p>SUPPLIER CODE<span style="margin-left: 43.5px; margin-right:10px;">:</span>'.$customerCode. '<span style="margin-left:52px">' .$customerName.'</span></p>
                                                <p>PRODUCT CODE<span style="margin-left: 43.5px; margin-right:10px;">:</span>'.$productCode. '<span style="margin-left:57.5px">' .$productName.'</span></p>
                                                <p>DESTINATION CODE<span style="margin-left: 21px; margin-right:10px;">:</span>'.$destinationCode. '<span style="margin-left:60px">' .$destinationName.'</span></p>
                                                <p>P/O NO.<span style="margin-left: 99px; margin-right:10px;">:</span>'.$poNo.' D/O No. : '.$doNo.'</p>
                                                <p>REMARKS<span style="margin-left: 83.5px; margin-right:10px;">:</span>'.$remarks.'</p>
                                            </div>
                                            <div class="col-4 body_1">
                                                <p>TICKET NO.<span style="margin-left: 25.5px; margin-right:5px;">:</span><b>'.$loadingChitNo.'</b></p>
                                                <p>SUPPLIER WT<span style="margin-left: 10px; margin-right:5px;">:</span>'.$supplierWeight.'</p>
                                                <p>WEIGHT DIFF<span style="margin-left: 14px; margin-right:6px;">:</span>'.$weightDifference.'</p>
                                            </div>
                                        </div>

                                        <div class="row body2">
                                            <div class="col-6 body_2 mt-2 mb-2">
                                                <p>WEIGHT IN <span style="margin-left: 26px;">(KG)</span><span>:</span>'.($weightType == 'Normal' ? $grossWeight : number_format((float)$row['gross_weight1'] + (float)$row['gross_weight2'])).'</p>
                                                <p>WEIGHT OUT<span style="margin-left: 15px;">(KG)</span><span>:</span>'.($weightType == 'Normal' ? $tareWeight : number_format((float)$row['tare_weight1'] + (float)$row['tare_weight2'])).'</p>
                                                <p>NET WEIGHT<span style="margin-left: 16px;">(KG)</span><span>:</span>'.($weightType == 'Normal' ? $nettWeight : number_format((float)$row['nett_weight1'] + (float)$row['nett_weight2'])).'</p>
                                            </div>
                                            <div class="col-6 body_2 mt-2 mb-2">
                                                <p>TIME IN<span style="margin-left: 26px;">:</span>'.$formattedGrossWeightDate.'</p>
                                                <p>TIME OUT<span style="margin-left: 11.5px;">:</span>'.$formattedTareWeightDate.'</p>
                                            </div>
                                        </div>

                                        <div class="row pt-4">
                                            <div class="col-4 signature">
                                                <div class="body_3">
                                                    WEIGHED BY '.strtoupper($weightBy).'
                                                </div>
                                            </div>
                                            <div class="col-4 signature">
                                                <div class="body_3">
                                                    LORRY DRIVER
                                                </div>
                                            </div>
                                            <div class="col-4 signature">
                                                <div class="body_3">
                                                    RECEIVED BY
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </body>
                            </html>
                        ';

                        $select_stmt->close();
                    }
                    else{
                        // Do your Local slips here
                        $message = '
                            <html>
                            <head>
                                <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
                                <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
                                <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
                                <link href="assets/css/custom.min.css" id="app-style" rel="stylesheet" type="text/css" />

                                <style>
                                    @page {
                                        size: A5 landscape;
                                        margin: 10mm;
                                    }

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
                                            <h2 style="color: black; font-weight: bold;">BLACKTOP LANCHANG SDN BHD</h2>
                                            <p style="font-size: 11px; margin-bottom: 3px;">(1373003-H) <br> '.$compaddress.' <br>'.$compaddress2.'<br>'.$compaddress3.'<br>TEL: '.$compphone.'</p>
                                            <h3 class="pb-2" style="color: black; font-weight: bold;"><span style="border-bottom: 1px solid black">PUBLIC WEIGHING</span></h3>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-8 body_1">
                                            <p>DATE<span style="margin-left: 100px;">:</span>'.$transDateOnly.'</p>
                                            <p>VEHICLE NO.<span style="margin-left: 55.5px;">:</span>'.$lorryNo.'</p>
                                            <p>SUPPLIER NAME<span style="margin-left: 37px;">:</span>'.$customerName.'</p>
                                            <p>PRODUCT NAME<span style="margin-left: 30.5px;">:</span>'.$productName.'</p>
                                            <p>SERVICE CHARGES<span style="margin-left: 16px;">:</span>RM </p>
                                            <p>REMARKS<span style="margin-left: 72.5px;">:</span>'.$remarks.'</p>
                                        </div>
                                        <div class="col-4 body_1">
                                            <p>TICKET NO.<span style="margin-left: 20px;">:</span><b>'.$loadingChitNo.'</b></p>
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