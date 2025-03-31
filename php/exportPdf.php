<?php

require_once 'db_connect.php';
session_start();

$searchQuery = "";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $searchQuery = "and plant_code IN ('$username')";
}

if(isset($_POST['fromDate']) && $_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
    $formatted_date = $date->format('Y-m-d 00:00:00');
    $fromDate = $date->format('d/m/Y');

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
    $toDate = $date->format('d/m/Y');

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

if(isset($_POST['supplier']) && $_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.supplier_code = '".$_POST['supplier']."'";
    }
    else{
        $searchQuery .= " and count.supplier_code = '".$_POST['supplier']."'";
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

if(isset($_POST['customerType']) && $_POST['customerType'] != null && $_POST['customerType'] != '' && $_POST['customerType'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_type like '%".$_POST['customerType']."%'";
    }
    else{
        $searchQuery .= " and count.customer_type like '%".$_POST['customerType']."%'";
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

if(isset($_POST['rawMat']) && $_POST['rawMat'] != null && $_POST['rawMat'] != '' && $_POST['rawMat'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.raw_mat_code = '".$_POST['rawMat']."'";
    }
    else{
        $searchQuery .= " and count.raw_mat_code = '".$_POST['rawMat']."'";
    }
}

if(isset($_POST['destination']) && $_POST['destination'] != null && $_POST['destination'] != '' && $_POST['destination'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.destination = '".$_POST['destination']."'";
    }
    else{
        $searchQuery .= " and count.destination = '".$_POST['destination']."'";
    }
}

if(isset($_POST['plant']) && $_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.plant_code = '".$_POST['plant']."'";
    }
    else{
        $searchQuery .= " and count.plant_code = '".$_POST['plant']."'";
    }
}

if(isset($_POST["file"])){
    if($_POST["file"] == 'weight'){
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        if ($_POST['reportType'] == 'SUMMARY') {
            if ($select_stmt = $db->prepare("SELECT DATE(transaction_date) AS transaction_date,SUM(nett_weight1) AS product_weight,SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight,COUNT(*) AS total_records FROM Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery." GROUP BY DATE(transaction_date) ORDER BY DATE(transaction_date) ASC")){

                if (!$select_stmt->execute()){
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }else{
                    $result = $select_stmt->get_result();

                    $totalRecords = 0;
                    $totalProductWeight = 0;
                    $totalTransportWeight = 0;

                    $message = '
                        <html>
                            <head>
                                <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                                <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />

                                <style>
                                    @page {
                                        size: A4 landscape;
                                        margin: 10mm;
                                    }
                                </style>
                            </head>

                            <body>
                                <div class="container-full">
                                    <div class="header">
                                        <div class="row">
                                            <div class="d-flex justify-content-center">
                                                <h5 class="fw-bold">EAST ROCK MARKETING SDN. BHD.</h5>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <p>Sales Weighing Summary Report By Date</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <p>
                                                Start Date : '.$fromDate.' Last Date : '.$toDate.'
                                                <br>
                                                Start/Last Company : ERMSB / ERMSB
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <p>
                                                Quarry And Prefix Product
                                                <br>
                                                Start/Last Customer Type: /IN 
                                                <br>
                                                Start/Last Site : BEN/BEN - Weighing Only
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead style="border-bottom: 1px solid black;">
                                                    <tr class="text-center" style="border-top: 1px solid black;">
                                                        <th rowspan="2" width="15%" class="text-start">Date</th>
                                                        <th rowspan="2">Total Loads</th>
                                                        <th rowspan="2">Product Weight (MT)</th>
                                                        <th rowspan="2">Transport Weight (MT)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total Amount (RM)</th>
                                                        <th colspan="3" style="border-bottom: none;">Total Ex-GST (RM)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total GST 0% (RM)</th>
                                                        <th rowspan="2">Total Amount (RM)</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Total</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                                while ($row = $result->fetch_assoc()) {
                                                    $transactionDate = date("d-m-Y", strtotime($row['transaction_date']));
                                                    $productWeight = number_format($row['product_weight']/1000, 2);
                                                    $transportWeight = number_format($row['transport_weight']/1000, 2);

                                                    $totalRecords += $row['total_records'];
                                                    $totalProductWeight += $row['product_weight']/1000;
                                                    $totalTransportWeight += $row['transport_weight']/1000;

                                                    $message .= '<tr>
                                                            <td>'.$transactionDate.'</td>
                                                            <td>'.$row['total_records'].'</td>
                                                            <td>'.$productWeight.'</td>
                                                            <td>'.$transportWeight.'</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                        </tr>';
                                                }
                                                
                                                $message .= '</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">Company Total:</td>
                                                        <td>'.$totalRecords.'</td>
                                                        <td>'.number_format($totalProductWeight, 2).'</td>
                                                        <td>'.number_format($totalTransportWeight, 2).'</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </body>
                        </html>
                    ';

                    echo json_encode(
                        array(
                            "status" => "success",
                            "query" => "SELECT DATE(transaction_date) AS transaction_date,SUM(nett_weight1) AS product_weight,SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight,COUNT(*) AS total_records FROM Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery." GROUP BY DATE(transaction_date) ORDER BY DATE(transaction_date) ASC",
                            "message" => $message
                        )
                    );
                }
            }else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
        else if ($_POST['reportType'] == 'PRODUCT'){
            if ($select_stmt = $db->prepare("SELECT * FROM ( SELECT product_code AS code, SUM(nett_weight1) AS product_weight, SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight, COUNT(*) AS total_records FROM Weight WHERE TRIM(product_code) IS NOT NULL AND  is_cancel <> 'Y'".$searchQuery." GROUP BY product_code UNION ALL SELECT raw_mat_code AS code, SUM(nett_weight1) AS product_weight, SUM(CASE WHEN ex_del = 'DEL' THEN nett_weight1 ELSE 0 END) AS transport_weight, COUNT(*) AS total_records FROM Weight WHERE TRIM(raw_mat_code) IS NOT NULL".$searchQuery." GROUP BY raw_mat_code ) AS combined_results ORDER BY code")){

                if (!$select_stmt->execute()){
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }else{
                    $result = $select_stmt->get_result();

                    $totalRecords = 0;
                    $totalProductWeight = 0;
                    $totalTransportWeight = 0;

                    $message = '
                        <html>
                            <head>
                                <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                                <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />

                                <style>
                                    @page {
                                        size: A4 landscape;
                                        margin: 10mm;
                                    }
                                </style>
                            </head>

                            <body>
                                <div class="container-full">
                                    <div class="header">
                                        <div class="row">
                                            <div class="d-flex justify-content-center">
                                                <h5 class="fw-bold">EAST ROCK MARKETING SDN. BHD.</h5>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <p>Sales Weighing Summary Report By Product</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <p>
                                                Start Date : '.$fromDate.' Last Date : '.$toDate.'
                                                <br>
                                                Start/Last Company : ERMSB / ERMSB
                                                Start Product / Last Product : / QD
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <p>
                                                Quarry And Prefix Product
                                                <br>
                                                Start/Last Customer Type: /IN 
                                                <br>
                                                Start/Last Site : BEN/BEN - Weighing Only
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead style="border-bottom: 1px solid black;">
                                                    <tr class="text-center" style="border-top: 1px solid black;">
                                                        <th rowspan="2" class="text-start">Product</th>
                                                        <th rowspan="2">Total Loads</th>
                                                        <th rowspan="2">Product Weight (MT)</th>
                                                        <th rowspan="2">Transport Weight (MT)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total Amount (RM)</th>
                                                        <th colspan="3" style="border-bottom: none;">Total Ex-GST (RM)</th>
                                                        <th colspan="2" style="border-bottom: none;">Total GST 0% (RM)</th>
                                                        <th rowspan="2">Average Selling Price</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                        <th>Total</th>
                                                        <th>Product</th>
                                                        <th>Transport</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                                while ($row = $result->fetch_assoc()) {
                                                    $product = $row['code'];
                                                    $productWeight = number_format($row['product_weight']/1000, 2);
                                                    $transportWeight = number_format($row['transport_weight']/1000, 2);

                                                    $totalRecords += $row['total_records'];
                                                    $totalProductWeight += $row['product_weight']/1000;
                                                    $totalTransportWeight += $row['transport_weight']/1000;

                                                    $message .= '<tr>
                                                            <td>'.$product.'</td>
                                                            <td>'.$row['total_records'].'</td>
                                                            <td>'.$productWeight.'</td>
                                                            <td>'.$transportWeight.'</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                            <td>0.00</td>
                                                        </tr>';
                                                }
                                                
                                                $message .= '</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">Company Total:</td>
                                                        <td>'.$totalRecords.'</td>
                                                        <td>'.number_format($totalProductWeight, 2).'</td>
                                                        <td>'.number_format($totalTransportWeight, 2).'</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                        <td>0.00</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </body>
                        </html>
                    ';

                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        )
                    );
                }
            }else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Goes Wrong"
                    ));
            }
        }
        else if ($_POST['reportType'] == 'S&PC'){
            if ($select_stmt = $db->prepare("select * from Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery.' ORDER BY tare_weight1_date')) {
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
                                                <th style="font-size: 9px;">EX-QUARRY / DELIVERED</th>
                                                <th style="font-size: 9px;">PO NO.</th>
                                                <th style="font-size: 9px;">DO NO.</th>
                                                <th style="font-size: 9px;">INCOMING <br>(MT)</th>
                                                <th style="font-size: 9px;">OUTGOING <br>(MT)</th>
                                                <th style="font-size: 9px;">NET <br>(MT)</th>
                                                <th style="font-size: 9px;">IN TIME</th>
                                                <th style="font-size: 9px;">OUT TIME</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
    
                                        // Initialize the grouped data array
                                        $groupedData = [];
                                        
                                        // Fetch data and group by product_name
                                        while ($row = $result->fetch_assoc()) {
                                            $productName = ($row['transaction_status'] == 'Sales' ? $row['customer_name'] : $row['supplier_name']);
                                        
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
                                                $exDel = '';
                                                
                                                if ($row['ex_del'] == 'EX'){
                                                    $exDel = 'E';
                                                }else{
                                                    $exDel = 'D';
                                                }
                                                
                                                
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
                                                    
                                                    
                                                    $message .= '<td style="font-size: 8px;">' . ($row['transaction_status'] == 'Sales' ? $row['product_code'] : $row['raw_mat_code']) . '</td>
                                                    <td style="font-size: 8px;">' . ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                    <td style="font-size: 8px;">' . $row['destination_code'] . '</td>
                                                    <td style="font-size: 8px;">' . $row['destination'] . '</td>
                                                    <td style="font-size: 8px;">' . $exDel . '</td>
                                                    <td style="font-size: 8px;">' . $row['purchase_order'] . '</td>
                                                    <td style="font-size: 8px;">' . $row['delivery_no'] . '</td>
                                                    <td style="font-size: 8px;">' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                    <td style="font-size: 8px;">' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                    <td style="font-size: 8px;">' . number_format($row['nett_weight1']/1000, 2) . '</td>
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
                                                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalGross /1000, 2). '</th>
                                                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalTare/1000, 2) . '</th>
                                                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalNet/1000, 2) . '</th>
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
                                                    <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.number_format($grandTotalGross/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.number_format($grandTotalTare/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.number_format($grandTotalNet/1000, 2).'</th>
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
        else{
            if ($select_stmt = $db->prepare("select * from Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery.' ORDER BY tare_weight1_date')) {
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
                                                <th style="font-size: 9px;">EX-QUARRY / DELIVERED</th>
                                                <th style="font-size: 9px;">PO NO.</th>
                                                <th style="font-size: 9px;">DO NO.</th>
                                                <th style="font-size: 9px;">INCOMING <br>(MT)</th>
                                                <th style="font-size: 9px;">OUTGOING <br>(MT)</th>
                                                <th style="font-size: 9px;">NET <br>(MT)</th>
                                                <th style="font-size: 9px;">IN TIME</th>
                                                <th style="font-size: 9px;">OUT TIME</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
    
                                        // Initialize the grouped data array
                                        $groupedData = [];
                                        
                                        // Fetch data and group by product_name
                                        while ($row = $result->fetch_assoc()) {
                                            $productName = ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']);
                                        
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
                                                $exDel = '';
                                                
                                                if ($row['ex_del'] == 'EX'){
                                                    $exDel = 'E';
                                                }else{
                                                    $exDel = 'D';
                                                }
                                                
                                                
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
                                                    
                                                    
                                                    $message .= '<td style="font-size: 8px;">' . ($row['transaction_status'] == 'Sales' ? $row['product_code'] : $row['raw_mat_code']) . '</td>
                                                    <td style="font-size: 8px;">' . ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']) . '</td>
                                                    <td style="font-size: 8px;">' . $row['destination_code'] . '</td>
                                                    <td style="font-size: 8px;">' . $row['destination'] . '</td>
                                                    <td style="font-size: 8px;">' . $exDel . '</td>
                                                    <td style="font-size: 8px;">' . $row['purchase_order'] . '</td>
                                                    <td style="font-size: 8px;">' . $row['delivery_no'] . '</td>
                                                    <td style="font-size: 8px;">' . number_format($row['gross_weight1']/1000, 2) . '</td>
                                                    <td style="font-size: 8px;">' . number_format($row['tare_weight1']/1000, 2) . '</td>
                                                    <td style="font-size: 8px;">' . number_format($row['nett_weight1']/1000, 2) . '</td>
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
                                                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalGross /1000, 2). '</th>
                                                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalTare/1000, 2) . '</th>
                                                <th style="border:1px solid black;font-size: 9px;">' . number_format($totalNet/1000, 2) . '</th>
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
                                                    <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.number_format($grandTotalGross/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.number_format($grandTotalTare/1000, 2).'</th>
                                                    <th style="border:1px solid black;font-size: 9px;border:1px solid black;">'.number_format($grandTotalNet/1000, 2).'</th>
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