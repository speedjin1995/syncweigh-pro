<?php

require_once 'db_connect.php';

$searchQuery = "";
$groupByFields = array();

function rearrangeList($weightDetails) {
    // global $mapOfHouses, $mapOfWeights, $totalSGross, $totalSCrate, $totalSReduce, $totalSNet, $totalSBirds, $totalSCages, $totalAGross, $totalACrate, $totalAReduce, $totalANet, $totalABirds, $totalACages, $totalGross, $totalCrate, $totalReduce, $totalNet, $totalCrates, $totalBirds, $totalMaleBirds, $totalMaleCages, $totalFemaleBirds, $totalFemaleCages, $totalMixedBirds, $totalMixedCages;
    global $groupByFields, $mapOfWeights;

    $result = array();

    $groupby = array(
        "customer_supplier_code" => "Customer/Supplier",
        "product_code" => "Product",
        "lorry_plate_no1" => "Vehicle",
        "destination_code" => "Destination",
        "transporter_code" => "Transporter"
    );

    foreach ($weightDetails as $row) {
        $current = &$result;

        // Loop through the group fields
        foreach ($groupByFields as $field) {
            $key = $row[$field];
            if (!isset($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }

        // Push the full row at the deepest level
        $current[] = $row;
    }

    return $result;
}

if(isset($_POST['fromDate']) && $_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
    $formatted_date = $date->format('Y-m-d 00:00:00');

    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_date >= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.transaction_date >= '".$formatted_date."'";
    }
}

if(isset($_POST['toDate']) && $_POST['toDate'] != null && $_POST['toDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
    $formatted_date = $date->format('Y-m-d 23:59:59');

    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_date <= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.transaction_date <= '".$formatted_date."'";
    }
}

if(isset($_POST['status']) && $_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_status = '".$_POST['status']."'";
    }
    else{
        $searchQuery .= " and count.transaction_status = '".$_POST['status']."'";
    }	
}

if(isset($_POST['customer']) && $_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_code = '".$_POST['customer']."'";
    }
    else{
        $searchQuery .= " and count.customer_code = '".$_POST['customer']."'";
    }
}

if(isset($_POST['vehicle']) && $_POST['vehicle'] != null && $_POST['vehicle'] != '' && $_POST['vehicle'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.lorry_plate_no1 = '".$_POST['vehicle']."'";
    }
    else{
        $searchQuery .= " and count.lorry_plate_no1 = '".$_POST['vehicle']."'";
    }
}

if(isset($_POST['weighingType']) && $_POST['weighingType'] != null && $_POST['weighingType'] != '' && $_POST['weighingType'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.weight_type like '%".$_POST['weighingType']."%'";
    }
    else{
        $searchQuery .= " and count.weight_type like '%".$_POST['weighingType']."%'";
    }
}

if(isset($_POST['product']) && $_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.product_code = '".$_POST['product']."'";
    }
    else{
        $searchQuery .= " and count.product_code = '".$_POST['product']."'";
    }
}

if(isset($_POST['groupOne']) && $_POST['groupOne'] != null && $_POST['groupOne'] != '' && $_POST['groupOne'] != '-'){
    $groupByFields[] = $_POST['groupOne'];
}

if(isset($_POST['groupTwo']) && $_POST['groupTwo'] != null && $_POST['groupTwo'] != '' && $_POST['groupTwo'] != '-'){
    $groupByFields[] = $_POST['groupTwo'];
}

if(isset($_POST['groupThree']) && $_POST['groupThree'] != null && $_POST['groupThree'] != '' && $_POST['groupThree'] != '-'){
    $groupByFields[] = $_POST['groupThree'];
}

if(isset($_POST["file"])){
    if($_POST["file"] == 'weight'){
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        if ($select_stmt = $db->prepare("select * from Weight WHERE Weight.is_cancel = 'N'".$searchQuery)) {
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
                
                $message = '<html>
    <head>
        <style>
            @page {
                size: A4 landscape;
                margin: 5mm;
            }

            @media print {
                .details td {
                    border: 0;
                    padding-top: 0;
                    padding-bottom: 0;
                }

                .section-break {
                    page-break-before: always;
                }
            } 

            table {
                border-collapse: collapse;
                width: 100%;
            }

            thead {
                border-top: 2px solid black;
                border-bottom: 2px solid black;
            }

            #text-end {
                text-align: right;
            }
                    
            // table {
            //     width: 100%;
            //     border-collapse: collapse;
                
            // } 
            
            // .table th, .table td {
            //     padding: 0.70rem;
            //     vertical-align: top;
            //     border-top: 1px solid #dee2e6;
                
            // } 
            
            // .table-bordered {
            //     border: 1px solid #000000;
                
            // } 
            
            // .table-bordered th, .table-bordered td {
            //     border: 1px solid #000000;
            //     font-family: sans-serif;
            //     font-size: 12px;
                
            // } 
            
            // .row {
            //     display: flex;
            //     flex-wrap: wrap;
            //     margin-top: 20px;
            //     margin-right: -15px;
            //     margin-left: -15px;
                
            // } 
            
            // .col-md-4{
            //     position: relative;
            //     width: 33.333333%;
            // }
        </style>
    </head>
    <body>';

    while ($row = $result->fetch_assoc()) {
        $message .= '<div class="container-full content">
        <div class="row">
            <div class="table-responsive">
                <table class="table" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="border: 2px solid black;">
                        <tr class="text-center" style="border-top: 1px solid black;">
                            <th rowspan="2" class="text-start">Serial No.</th>
                            <th rowspan="2">Part Code</th>
                            <th rowspan="2" colspan="3">Products Description</th>
                            <th rowspan="2">Percentage (%)</th>
                            <th rowspan="2">Item Weight (kg)</th>
                            <th rowspan="2">Unit Price (RM)</th>
                            <th rowspan="2">Total Price (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" style="border:0; padding-bottom: 0;">
                                <div class="fw-bold">
                                    <span>';

                                    if($row['transaction_status'] == 'Sales') {
                                        $name = 'Customer';
                                        $value = $row['customer_name'];
                                    } else {
                                        $name = 'Supplier';
                                        $value = $row['supplier_name'];
                                    }
                                        
                                    $message .= $name.' <span>:</span> '.$value.
                                        '<br>
                                        Transporter <span>:</span> '.$row['transporter'].
                                        '<br>
                                        Destination <span>:</span> '.$row['destination'].
                                        '<br>
                                        Vehicle Plate No. <span>:</span> '.$row['lorry_plate_no1'].
                                        '<br>
                                        Driver Name <span>:</span> '.$row['driver_name'].
                                        '<br>
                                        Driver I/C No <span>:</span> '.$row['driver_ic'].
                                        '<br>
                                        Driver Contact No <span>:</span> '.$row['driver_phone'].
                                    '</span>
                                </div>
                            </td>
                            <td colspan="3" style="border:0; padding-bottom: 0;">
                                <div class="fw-bold">
                                    <span>
                                        Transaction ID <span>:</span> '.$row['transaction_id'].
                                        '<br>
                                        Weight Type <span>:</span> '.$row['weight_type'].
                                        '<br>
                                        Transaction Status <span>:</span> '.$row['transaction_status'].
                                        '<br>
                                        Transaction Date <span>:</span> '.$row['transaction_date'].
                                        '<br>
                                        Purchase Order <span>:</span> '.$row['purchase_order'].
                                        '<br>
                                        Invoice No <span>:</span> '.$row['invoice_no'].
                                        '<br>
                                        Delivery No <span>:</span> '.$row['delivery_no'].
                                    '</span>
                                </div>
                            </td>
                            <td colspan="3" style="border:0; padding-bottom: 0;">
                                <div class="fw-bold">
                                    <span>
                                        Incoming Weight (kg) <span>:</span> '.number_format($row['gross_weight1'], 2, '.', ',').
                                        '<br>
                                        Outgoing Weight (kg) <span>:</span> '.number_format($row['tare_weight1'], 2, '.', ',').
                                        '<br>
                                        Nett Weight <span>:</span> '.number_format($row['nett_weight1'], 2, '.', ',').
                                        '<br>
                                        Overall Reduce Weight <span>:</span> '.number_format($row['reduce_weight'], 2, '.', ',').
                                        '<br>
                                        Final Weight <span>:</span> '.number_format($row['final_weight'], 2, '.', ',').
                                        '<br>
                                    </span>
                                </div>
                            </td>
                        </tr>';

            if ($select_stmt2 = $db->prepare("select * FROM Weight_Product WHERE weight_id = ?")) {
                $select_stmt2->bind_param('s', $row['id']);
                // Execute the prepared query.
                if (! $select_stmt2->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result2 = $select_stmt2->get_result();

                    $sub_total = 0;

                    while ($row2 = $result2->fetch_assoc()) {

                        $sub_total += $row2['total_price'];

                        $message .= '<tr class="details">
                            <td>'.$row2['id'].'</td>
                            <td>'.$row2['product_code'].'</td>
                            <td colspan="3">'.$row2['product_name'].'</td>
                            <td class="text-end">'.$row2['percentage'].'</td>
                            <td class="text-end">'.number_format($row2['item_weight'], 2, '.', ',').'</td>
                            <td class="text-end">'.number_format($row2['unit_price'], 2, '.', ',').'</td>
                            <td class="text-end">'.number_format($row2['total_price'], 2, '.', ',').'</td>
                        </tr>';
                    }

                    $message .= '<tr class="details fw-bold">
                            <td colspan="6">Sub Total Price (RM)</td>
                            <td colspan="2"></td>
                            <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.number_format($sub_total, 2, '.', ',').'</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>';

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

    }

            $message .= '</body>
                    </html>';

//         $message = '<table style="width:100%; border:1px solid black;"><thead>
//             <tr>
//                 <th style="border:1px solid black;font-size: 11px;">TRANSACTION <br>ID</th>
//                 <th style="border:1px solid black;font-size: 11px;">TRANSACTION <br>STATUS</th>
//                 <th style="border:1px solid black;font-size: 11px;">WEIGHT <br>TYPE</th>
//                 <th style="border:1px solid black;font-size: 11px;">LORRY <br>NO.</th>
//                 <th style="border:1px solid black;font-size: 11px;">CUSTOMER</th>
//                 <th style="border:1px solid black;font-size: 11px;">SUPPLIER</th>
//                 <th style="border:1px solid black;font-size: 11px;">PRODUCT</th>
//                 <th style="border:1px solid black;font-size: 11px;">PO NO.</th>
//                 <th style="border:1px solid black;font-size: 11px;">DO NO.</th>
//                 <th style="border:1px solid black;font-size: 11px;">GROSS</th>
//                 <th style="border:1px solid black;font-size: 11px;">TARE</th>
//                 <th style="border:1px solid black;font-size: 11px;">NET</th>
//             </tr></thead><tbody>';
            
//             $totalGross = 0;
//             $totalTare = 0;
//             $totalNet = 0;

//             while ($row = $result->fetch_assoc()) {
//                 $message .= '<tr>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['transaction_id'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['transaction_status'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['weight_type'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['lorry_plate_no1'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['customer_name'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['supplier_name'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['product_name'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['purchase_order'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['delivery_no'].'</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['gross_weight1'].' kg</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['tare_weight1'].' kg</td>
//                     <td style="border:1px solid black;font-size: 10px;">'.$row['nett_weight1'].' kg</td>
//                 </tr>';
                
//                 $totalGross += (float)$row['gross_weight1'];
//                 $totalTare += (float)$row['tare_weight1'];
//                 $totalNet += (float)$row['nett_weight1'];
//             }
            
//             $message .= '</tbody><tfoot><tr>
//                 <th style="border:1px solid black;font-size: 11px;" colspan="9">Total</th>
//                 <th style="border:1px solid black;font-size: 11px;">'.$totalGross.' kg</th>
//                 <th style="border:1px solid black;font-size: 11px;">'.$totalTare.' kg</th>
//                 <th style="border:1px solid black;font-size: 11px;">'.$totalNet.' kg</th>
//             </tr>';
            
//         $message .= '</table>
//     </body>
// </html>';


                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    )
                );
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
    /*else{
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
    } */
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