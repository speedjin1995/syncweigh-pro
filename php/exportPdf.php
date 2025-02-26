<?php

require_once 'db_connect.php';
session_start();

$searchQuery = "";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = $_SESSION["plant"];
    $searchQuery = "and plant_code='$username'";
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
        if($_POST['status'] == 'Sales'){
            $searchQuery .= " and Weight.transaction_status = '".$_POST['status']."'";
        }
        else{
            $searchQuery .= " and Weight.transaction_status IN ('Purchase', 'Local')";
        }
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
        <table style="width:100%;">
            <thead>
                <tr>
                    <th style="font-size: 9px;">TRANSACTION <br>ID</th>
                    <th style="font-size: 9px;">TRANSACTION <br>DATE</th>
                    <th style="font-size: 9px;">TRANSACTION <br>STATUS</th>
                    <th style="font-size: 9px;">WEIGHT <br>TYPE</th>
                    <th style="font-size: 9px;">LORRY <br>NO.</th>';
                    
                if($_POST['status'] == 'Sales'){
                    $message .= '<th style="font-size: 9px;">CUSTOMER <br>CODE</th>';
                    $message .= '<th style="font-size: 9px;">CUSTOMER</th>';
                }
                else{
                    $message .= '<th style="font-size: 9px;">SUPPLIER <br>CODE</th>';
                    $message .= '<th style="font-size: 9px;">SUPPLIER</th>';
                }
                    
                    $message .= '<th style="font-size: 9px;">PRODUCT <br>CODE</th>
                    <th style="font-size: 9px;">PRODUCT</th>
                    <th style="font-size: 9px;">DESTINATION <br>CODE</th>
                    <th style="font-size: 9px;">DESTINATION</th>
                    <th style="font-size: 9px;">PO NO.</th>
                    <th style="font-size: 9px;">DO NO.</th>
                    <th style="font-size: 9px;">GROSS</th>
                    <th style="font-size: 9px;">TARE</th>
                    <th style="font-size: 9px;">NET</th>
                    <th style="font-size: 9px;">IN TIME</th>
                    <th style="font-size: 9px;">OUT TIME</th>
                </tr>
            </thead>
            <tbody>';

            // Initialize the grouped data array
            $groupedData = [];
            
            // Fetch data and group by product_name
            while ($row = $result->fetch_assoc()) {
                $productName = $row['product_name'];
            
                if (!isset($groupedData[$productName])) {
                    $groupedData[$productName] = [];
                }
            
                $groupedData[$productName][] = $row;
            }
            
            // Initialize total values
            $grandTotalGross = 0;
            $grandTotalTare = 0;
            $grandTotalNet = 0;

            // Generate table grouped by product
            foreach ($groupedData as $product => $rows) {
                $message .= '<tr>
                    <td colspan="14" style="font-size: 9px;">. </td>
                </tr>
                <tr>
                    <td colspan="14" style="font-size: 9px;">. </td>
                </tr>';
            
                $totalGross = 0;
                $totalTare = 0;
                $totalNet = 0;
            
                foreach ($rows as $row) {
                    $grossWeightDate = new DateTime($row['gross_weight1_date']);
                    $formattedGrossWeightDate = $grossWeightDate->format('H:i');
                    $tareWeightDate =  new DateTime($row['tare_weight1_date']);
                    $formattedTareWeightDate = $tareWeightDate->format('H:i');
                    $transactionDate =  new DateTime($row['transaction_date']);
                    $formattedtransactionDate = $transactionDate->format('d/m/Y');
                    
                    $message .= '<tr>
                        <td style="font-size: 8px;">' . $row['transaction_id'] . '</td>
                        <td style="font-size: 8px;">' . $formattedtransactionDate . '</td>
                        <td style="font-size: 8px;">' . $row['transaction_status'] . '</td>
                        <td style="font-size: 8px;">' . $row['weight_type'] . '</td>
                        <td style="font-size: 8px;">' . $row['lorry_plate_no1'] . '</td>';
                        
                        if($_POST['status'] == 'Sales'){
                            $message .= '<td style="font-size: 8px;">' . $row['customer_code'] . '</td>';
                            $message .= '<td style="font-size: 8px;">' . $row['customer_name'] . '</td>';
                        }
                        else{
                            $message .= '<td style="font-size: 8px;">' . $row['supplier_code'] . '</td>';
                            $message .= '<td style="font-size: 8px;">' . $row['supplier_name'] . '</td>';
                        }
                        
                        
                        $message .= '<td style="font-size: 8px;">' . $row['product_code'] . '</td>
                        <td style="font-size: 8px;">' . $row['product_name'] . '</td>
                        <td style="font-size: 8px;">' . $row['destination_code'] . '</td>
                        <td style="font-size: 8px;">' . $row['destination'] . '</td>
                        <td style="font-size: 8px;">' . $row['purchase_order'] . '</td>
                        <td style="font-size: 8px;">' . $row['delivery_no'] . '</td>
                        <td style="font-size: 8px;">' . $row['gross_weight1'] . ' kg</td>
                        <td style="font-size: 8px;">' . $row['tare_weight1'] . ' kg</td>
                        <td style="font-size: 8px;">' . $row['nett_weight1'] . ' kg</td>
                        <td style="font-size: 8px;">' . $formattedGrossWeightDate . '</td>
                        <td style="font-size: 8px;">' . $formattedTareWeightDate . '</td>
                    </tr>';
            
                    // Calculate subtotals
                    $totalGross += (float)$row['gross_weight1'];
                    $totalTare += (float)$row['tare_weight1'];
                    $totalNet += (float)$row['nett_weight1'];
                }
            
                // Add product-wise subtotal
                $message .= '<tr>
                    <th style="font-size: 10px;" colspan="13">Subtotal (' . $product . ')</th>
                    <th style="border:1px solid black;font-size: 9px;">' . $totalGross . ' kg</th>
                    <th style="border:1px solid black;font-size: 9px;">' . $totalTare . ' kg</th>
                    <th style="border:1px solid black;font-size: 9px;">' . $totalNet . ' kg</th>
                </tr>';
            
                // Add to grand total
                $grandTotalGross += $totalGross;
                $grandTotalTare += $totalTare;
                $grandTotalNet += $totalNet;
            }
            
            $message .= '</tbody>
                <tfoot>
                    <tr>
                        <th style="font-size: 10px;" colspan="13">Grand Total</th>
                        <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.$grandTotalGross.' kg</th>
                        <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.$grandTotalTare.' kg</th>
                        <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.$grandTotalNet.' kg</th>
                    </tr>
                </tfoot>';
            $message .= '</tbody>';
            
        $message .= '</table>
    </body>
</html>';


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