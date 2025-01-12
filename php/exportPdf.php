<?php

require_once 'db_connect.php';

$searchQuery = "";

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
        <table style="width:100%; border:1px solid black;"><thead>
            <tr>
                <th style="border:1px solid black;font-size: 11px;">TRANSACTION <br>ID</th>
                <th style="border:1px solid black;font-size: 11px;">TRANSACTION <br>STATUS</th>
                <th style="border:1px solid black;font-size: 11px;">WEIGHT <br>TYPE</th>
                <th style="border:1px solid black;font-size: 11px;">LORRY <br>NO.</th>
                <th style="border:1px solid black;font-size: 11px;">CUSTOMER</th>
                <th style="border:1px solid black;font-size: 11px;">SUPPLIER</th>
                <th style="border:1px solid black;font-size: 11px;">PRODUCT</th>
                <th style="border:1px solid black;font-size: 11px;">PO NO.</th>
                <th style="border:1px solid black;font-size: 11px;">DO NO.</th>
                <th style="border:1px solid black;font-size: 11px;">GROSS</th>
                <th style="border:1px solid black;font-size: 11px;">TARE</th>
                <th style="border:1px solid black;font-size: 11px;">NET</th>
            </tr></thead><tbody>';
            
            $totalGross = 0;
            $totalTare = 0;
            $totalNet = 0;

            while ($row = $result->fetch_assoc()) {
                $message .= '<tr>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['transaction_id'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['transaction_status'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['weight_type'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['lorry_plate_no1'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['customer_name'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['supplier_name'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['product_name'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['purchase_order'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['delivery_no'].'</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['gross_weight1'].' kg</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['tare_weight1'].' kg</td>
                    <td style="border:1px solid black;font-size: 10px;">'.$row['nett_weight1'].' kg</td>
                </tr>';
                
                $totalGross += (float)$row['gross_weight1'];
                $totalTare += (float)$row['tare_weight1'];
                $totalNet += (float)$row['nett_weight1'];
            }
            
            $message .= '</tbody><tfoot><tr>
                <th style="border:1px solid black;font-size: 11px;" colspan="9">Total</th>
                <th style="border:1px solid black;font-size: 11px;">'.$totalGross.' kg</th>
                <th style="border:1px solid black;font-size: 11px;">'.$totalTare.' kg</th>
                <th style="border:1px solid black;font-size: 11px;">'.$totalNet.' kg</th>
            </tr>';
            
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