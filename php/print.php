<?php

require_once 'db_connect.php';
include 'phpqrcode/qrlib.php';
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

if(isset($_POST['userID'], $_POST["file"])){
    $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

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

                if($complete == 'N'){
                    // Put your loading chit
                }
                else{
                    if($type == 'Sales'){
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
                                            <div class="col-12" style="font-size: 18px; font-weight: bold;margin-left:10px">
                                                BLACKTOP LANCHANG SDN BHD<span style="font-size: 12px; margin-left: 5px">198501006021 (138463-T)</span>
                                            </div>
                                            <div class="col-12" style="font-size: 13px">
                                                <span style="margin-left:10px">Office</span><span style="margin-left:39px">:&nbsp;&nbsp; 37, Jalan Perusahaan Amari, Amari Business Park, 68100 Batu Caves, Selangor Darul Ehsan</span>
                                            </div>
                                            <div class="col-12" style="font-size: 13px">
                                                <span style="margin-left:50px">Tel&nbsp;&nbsp;:&nbsp;&nbsp; +603-6096 0383</span>
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
                                    <div class="col-7" style="margin-top:60px">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td width="25%" style="border: 0px solid black;">
                                                        <div class="row">
                                                            <div class="col-12 mt-2" style="height: 25px;font-size: 14px;"><b>CUSTOMER</b></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"><b>PROJECT</b></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"><b>PRODUCT</b></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"><b>DELIVERED TO</b></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"><b>DELIVERED BY</b></div>
                                                        </div>
                                                    </td>
                                                    <td colspan="2" width="75%" style="border: 1px solid black;">
                                                        <div class="row" style="margin-left: 5px">
                                                            <div class="col-12 mt-2" style="height: 25px;font-size: 14px;">'. $customerCode . ' ' . $customerName .'</div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;"></div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;">'. $productCode . ' ' . $productName .'</div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;">'. $destinationCode . ' ' . $destinationName .'</div>
                                                            <div class="col-12" style="height: 25px;font-size: 14px;">'. $transportCode . ' ' . $transportName .'</div>
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
                                                                <span style="font-size: 14px;"><b>Date</b></span><span style="margin-left: 78px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$sysdate.'</span>
                                                            </div>
                                                            <div class="col-12 mb-2">
                                                                <span style="font-size: 14px;"><b>Loading Chit No</b></span><span style="margin-left: 29px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$loadingChitNo.'</span>
                                                            </div>
                                                            <div class="col-12">
                                                                <span style="font-size: 14px;"><b>Delivery Order No</b></span><span style="margin-left: 20px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$deliverOrderNo.'</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="border: 1px solid black;">
                                                    <td colspan="2">
                                                        <div class="row">
                                                            <div class="col-12 mb-2">
                                                                <span style="font-size: 14px;"><b>Lorry No</b></span><span style="margin-left: 22px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$lorryNo.'</span>
                                                            </div>
                                                            <div class="col-12">
                                                                <span style="font-size: 14px;"><b>P/O No</b></span><span style="margin-left: 27px"><b>:</b></span>
                                                                <span style="margin-left: 10px;font-size: 14px;">'.$poNo.'</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="border: 1px solid black;">
                                                    <td style="border: 1px solid black; text-align: center;" width="50%"><b>Time</b></td>
                                                    <td style="border: 1px solid black; text-align: center;" width="50%"><b>Weight (MT)</b></td>
                                                </tr>
                                                <tr style="border: 1px solid black; height: 70px;">
                                                    <td style="border: 1px solid black; text-align: center;" width="50%">
                                                        <span style="font-size: 14px;">'.$formattedGrossWeightDate.'</span>
                                                        <br>
                                                        <span style="font-size: 14px;">'.$formattedTareWeightDate.'</span>
                                                    </td>
                                                    <td style="border: 1px solid black; text-align: center;" width="50%">
                                                        <span style="font-size: 14px;">'.$grossWeight.'</span>
                                                        <br>
                                                        <span style="font-size: 14px;">'.$tareWeight.'</span>
                                                        <hr style="width:30%; margin-left: auto; margin-right: auto; margin-top: 5px;">
                                                        <div style="margin-top: -10px;font-size: 14px;">'.$nettWeight.'</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="border: 0px solid black; padding-bottom: 45px;font-size: 14px;">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span><b>Weighted by :</b></span>
                                                                <span style="margin-left: 15px">'.$weightBy.'</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="border: 0px solid black; text-align: right;">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <span><b style="font-size: 15px">No : '.str_replace('P', '', str_replace('S', '', $loadingChitNo)).'</b><b style="font-size: 25px; color: red;"></b></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody> 
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </body></html>';
    
                        $select_stmt->close();
                    }
                    else{
                        // Do your puchase slips here
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
                                                WEIGHED BY VANI
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