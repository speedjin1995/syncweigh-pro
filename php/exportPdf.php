<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';

$searchQuery = "";
$groupByFields = array();

// function rearrangeList($weightDetails) {
//     // global $mapOfHouses, $mapOfWeights, $totalSGross, $totalSCrate, $totalSReduce, $totalSNet, $totalSBirds, $totalSCages, $totalAGross, $totalACrate, $totalAReduce, $totalANet, $totalABirds, $totalACages, $totalGross, $totalCrate, $totalReduce, $totalNet, $totalCrates, $totalBirds, $totalMaleBirds, $totalMaleCages, $totalFemaleBirds, $totalFemaleCages, $totalMixedBirds, $totalMixedCages;
//     global $groupByFields, $mapOfWeights;

//     $result = array();

//     $groupby = array(
//         "customer_supplier_code" => "Customer/Supplier",
//         "product_code" => "Product",
//         "lorry_plate_no1" => "Vehicle",
//         "destination_code" => "Destination",
//         "transporter_code" => "Transporter"
//     );

//     foreach ($weightDetails as $row) {
//         $current = &$result;

//         // Loop through the group fields
//         foreach ($groupByFields as $field) {
//             $key = $row[$field];
//             if (!isset($current[$key])) {
//                 $current[$key] = [];
//             }
//             $current = &$current[$key];
//         }

//         // Push the full row at the deepest level
//         $current[] = $row;
//     }

//     return $result;
// }

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

if(isset($_POST['plant']) && $_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
    if($_POST["file"] == 'weight'){
        $searchQuery .= " and Weight.plant_code = '".$_POST['plant']."'";
    }
    else{
        $searchQuery .= " and count.plant_code = '".$_POST['plant']."'";
    }
}

// if(isset($_POST['groupOne']) && $_POST['groupOne'] != null && $_POST['groupOne'] != '' && $_POST['groupOne'] != '-'){
//     $groupByFields[] = $_POST['groupOne'];
// }

// if(isset($_POST['groupTwo']) && $_POST['groupTwo'] != null && $_POST['groupTwo'] != '' && $_POST['groupTwo'] != '-'){
//     $groupByFields[] = $_POST['groupTwo'];
// }

// if(isset($_POST['groupThree']) && $_POST['groupThree'] != null && $_POST['groupThree'] != '' && $_POST['groupThree'] != '-'){
//     $groupByFields[] = $_POST['groupThree'];
// }

if(isset($_POST['groupOne']) && $_POST['groupOne'] != null && $_POST['groupOne'] != '' && $_POST['groupOne'] != '-'){
    if ($_POST['status'] == 'Purchase'){
        $group1 = 'supplier_code';
    }else{
        $group1 = $_POST['groupOne'];
    }
}

if(isset($_POST['groupTwo']) && $_POST['groupTwo'] != null && $_POST['groupTwo'] != '' && $_POST['groupTwo'] != '-'){
    $group2 = $_POST['groupTwo'];
}

$group3 = null;
if(isset($_POST['groupThree']) && $_POST['groupThree'] != null && $_POST['groupThree'] != '' && $_POST['groupThree'] != '-'){
    $group3 = $_POST['groupThree'];
}

$type = '';
if(isset($_POST['type']) && $_POST['type'] != null && $_POST['type'] != '' && $_POST['type'] != '-'){
    $type = $_POST['type'];
}

function getWeightProductLookup(string $ids, $db): array {
    $lookup = [];
    $sql = "SELECT weight_id, product_code, product_name FROM Weight_Product WHERE weight_id IN ($ids)";
    $result = $db->query($sql);

    while ($row = $result->fetch_assoc()) {
        $lookup[$row['weight_id']] = [
            'product_code' => $row['product_code'],
            'product_name' => $row['product_name'],
        ];
    }

    return $lookup;
}

function rearrangeList(array $records, array $filteredGroupKeys, array $productLookup): array {
    $grouped = [];

    $filteredGroupKeys = array_filter($filteredGroupKeys, fn($key) => $key !== 'product_code');

    foreach ($records as $record) {
        $ref = &$grouped;

        // Inject product_code from lookup via weight_id
        $weightId = $record['id'] ?? null;

        if ($weightId && isset($productLookup[$weightId])) {
            $record['product_code'] = $productLookup[$weightId]['product_code'];
            $record['product_name'] = $productLookup[$weightId]['product_name'];
        } else {
            $record['product_code'] = 'UNKNOWN';
            $record['product_name'] = 'UNKNOWN';
        }

        // Add grouping levels based on provided groupKeys
        foreach ($filteredGroupKeys as $key) {
            if (empty($record[$key])) {
                continue; // skip empty group value
            }

            $keyValue = $record[$key];

            if (!isset($ref[$keyValue])) {
                $ref[$keyValue] = [];
            }

            $ref = &$ref[$keyValue];
        }

        // Step 2: Group by product_code
        $productKey = $record['product_code'];
        if (!isset($ref[$productKey])) {
            $ref[$productKey] = [];
        }
        $ref = &$ref[$productKey];

        // Always group by transaction_date (formatting optional)
        $dateKey = convertDatetimeToDate($record['transaction_date']) ?? 'UNKNOWN_DATE';

        if (!isset($ref[$dateKey])) {
            $ref[$dateKey] = [];
        }

        $ref[$dateKey][] = $record;

        unset($ref); // break reference
    }

    return $grouped;
}

function addToHeaderGroup(&$headerGroup, $groupKey, $value) {
    if (!isset($headerGroup[$groupKey])) {
        $headerGroup[$groupKey] = [];
    }

    if (!in_array($value, $headerGroup[$groupKey])) {
        $headerGroup[$groupKey][] = $value;
    }
}

function callLookup($group, $groupValue, $db){
    $value = '';
    switch ($group) {
        case 'Customer':
            $value = searchCustomerByCode($groupValue, $db);
            break;
        case 'Supplier':
            $value = searchSupplierByCode($groupValue, $db);
            break;
        case 'Product':
            $value = searchProductNameByCode($groupValue, $db);
            break;
        case 'Raw Material':
            $value = searchRawNameByCode($groupValue, $db);
            break;
        case 'Vehicle':
            $value = $groupValue;
            break;
        case 'Destination':
            $value = searchDestinationNameByCode($groupValue, $db);
            break;
        case 'Transporter':
            $value = searchTransporterNameByCode($groupValue, $db);
            break;
        case 'Plant':
            $value = searchPlantNameByCode($groupValue, $db);
            break;
    }
    
    return $value;
}

if(isset($_POST["file"])){
    if($_POST["file"] == 'weight'){
        //i remove this because both(billboard and weight) also call this print page.
        //AND weight.pStatus = 'Pending'

        if ($type == 'summary'){
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
                                            
                                        </style>
                                    </head>
                                    <body>
                                        <div class="container-full content">
                                            <div class="row">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead style="border: 2px solid black;">
                                                            <tr class="text-center" style="border-top: 1px solid black;">
                                                                <th class="text-start">Product Code</th>
                                                                <th>Product Name</th>
                                                                <th>Products Description</th>
                                                                <th>Total Item Weight (kg)</th>
                                                                <th>Total Unit Price (RM)</th>
                                                                <th>Total Price (RM)</th>
                                                            </tr>
                                                        </thead>
                                    ';
                    $count = 0;
                    $weightIds = '';
                    while ($row = $result->fetch_assoc()) {
                        $weightIds .= $row['id'] . ','; // assuming your column name is 'id'
                        $count++;
                    }

                    $weightIds = rtrim($weightIds, ',');
                    if ($count > 0){
                        if ($select_stmt2 = $db->prepare("select product_code, product_name, SUM(item_weight) AS total_weight, SUM(unit_price) AS total_unit_price, SUM(total_price) AS total_price from Weight_Product WHERE weight_id IN ($weightIds) GROUP BY product_code, product_name ORDER BY product_name")) {
                            
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

                                while ($row2 = $result2->fetch_assoc()) { 
                                    $productCode = $row2['product_code'] ?? '';
                                    $productName = $row2['product_name'] ?? '';
                                    $productDesc = searchProductDescByCode($row2['product_code'], $db) ?? '';
                                    $totalWeight = $row2['total_weight'] ?? '0';
                                    $totalUnitPrice = number_format($row2['total_unit_price'] ?? 0, 2);
                                    $totalPrice = number_format($row2['total_price'] ?? 0, 2);
                                    
                                    $message .= '
                                        <tbody>
                                            <tr>
                                                <td>'.$productCode.'</td>
                                                <td>'.$productName.'</td>
                                                <td>'.$productDesc.'</td>
                                                <td>'.$totalWeight.'</td>
                                                <td>'.$totalUnitPrice.'</td>
                                                <td>'.$totalPrice.'</td>
                                            </tr>
                                        </tbody>
                                    ';
                                }

                                $message .= "
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </body>
                                    </html>";

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
                                    "message" => "Something Went Wrong"
                                )
                            );
                        }
                    }else{
                        echo json_encode(
                            array(
                                "status" => "error",
                                "message" => "No record found"
                            )
                        );
                    }
                    
                }
            }
            else{
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something Went Wrong"
                    )
                );
            }
        }
        elseif ($type == 'group'){
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
                    $weightData = [];
                    $id = [];
                    while ($row = $result->fetch_assoc()) {
                        // Replace all null values with ''
                        $cleanedRow = array_map(function($value) {
                            return $value === null ? '' : $value;
                        }, $row);
    
                        $weightData[] = $cleanedRow;
                        $id[] = $row['id'];
                    }
                    
                    if (count($id) > 0){
                        $id = implode(',', $id);
                    }
    
                    $groupList = [
                        $group1, $group2, $group3            
                    ];
    
                    // Clean groupKeys to remove any empty values
                    $filteredGroupKeys = array_filter($groupList, fn($key) => !empty($key));
                    $groupBy = "";
                    $groupOrder = [];
                    if (empty($filteredGroupKeys)){
                        $groupBy = 'Date';
                    }else{
                        foreach ($filteredGroupKeys as $group) {
                            if ($group == 'customer_code'){
                                $groupBy .= '/Customer';
                                $groupOrder[] = 'Customer';
                            }elseif ($group == 'supplier_code') {
                                $groupBy .= '/Supplier';
                                $groupOrder[] = 'Supplier';
                            }elseif($group == 'product_code'){
                                $groupBy .= '/Product';
                                $groupOrder[] = 'Product';
                            }
                        }
                    }
                    $groupBy = ltrim($groupBy, '/');

                    $weightProductData = getWeightProductLookup($id, $db); 
    
                    $processedData = rearrangeList($weightData, $filteredGroupKeys, $weightProductData);
    
                    ################################################## Header Processing ##################################################
                    $headerGrouping = [];
                    
                    if ($_POST['status'] == 'Purchase'){
                        $defaultGroups = ['Supplier', 'Product', 'Vehicle', 'Destination', 'Transporter', 'Plant']; // Default group keys
                    }else{
                        $defaultGroups = ['Customer', 'Product', 'Vehicle', 'Destination', 'Transporter', 'Plant']; // Default group keys
                    }
                    // Initialize $headerGrouping with empty arrays
                    foreach ($defaultGroups as $group) {
                        $headerGroup[$group] = [];
                    }
    
                    if (count($groupOrder) > 0){
                        foreach ($processedData as $grp1 => $grp1Data) {
                            # Group 1 Header Processing
                            if (in_array($groupOrder[0], $defaultGroups)) {
                                addToHeaderGroup($headerGroup, $groupOrder[0], $grp1);
                            }
        
                            if(count($groupOrder) > 1 && !empty($grp1Data)){
                                foreach ($grp1Data as $grp2 => $grp2Data) {
                                    # Group 2 Header Processing
                                    if (in_array($groupOrder[1], $defaultGroups)) {
                                        addToHeaderGroup($headerGroup, $groupOrder[1], $grp2);
                                    }
        
                                    if(count($groupOrder) > 2 && !empty($grp2Data)){
                                        foreach ($grp2Data as $grp3 => $grp3Data) {
                                            # Group 3 Header Processing
                                            if (in_array($groupOrder[2], $defaultGroups)) {
                                                addToHeaderGroup($headerGroup, $groupOrder[2], $grp3);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    ################################################## Row Processing ##################################################
                    $groupCount = count($filteredGroupKeys)+1; // Add 1 for date group
                    $compiledRowData = '';
                    if($groupCount == 1){ 
                        $grpNettWeight = 0;
                        $totalNettWeight = 0;
                        $grpTotalCount = 0;
    
                        foreach ($processedData as $date => $grpData) { 
                            $rowData = '
                                <tr>
                                    <td colspan="17" style="border:0; padding-bottom: 0;">
                                        <div class="fw-bold">
                                            <span>
                                                Date <span>:</span> '.$date.'
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            ';
    
                            foreach ($grpData as $data){ 
                                $grpNettWeight += $data['nett_weight1']/1000; 
                                if ($data['ex_del'] == 'EX'){
                                    $exDel = 'E';
                                }else{
                                    $exDel = 'D';
                                }
    
                                $rowData .= '<tr class="details">
                                    <td>'.$data['transaction_id'].'</td>
                                    <td>'.$data['transporter_code'].'</td>
                                    <td>'.$data['lorry_plate_no1'].'</td>
                                    <td>'.$data['agent_code'].'</td>
                                    <td>'.date("d/m/Y", strtotime($data['transaction_date'])).'</td>
                                    <td width="10%">'.$data['purchase_order'].'</td>
                                    <td class="text-end">'.date("H:i", strtotime($data['gross_weight1_date'])).'</td>
                                    <td class="text-end">'.date("H:i", strtotime($data['tare_weight1_date'])).'</td>
                                    <td class="text-end">'.($data['gross_weight1']/1000).'</td>
                                    <td class="text-end">'.($data['tare_weight1']/1000).'</td>
                                    <td class="text-end">'.($data['nett_weight1']/1000).'</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">0.00</td>
                                    <td class="text-end">0.00</td>
                                    <td>'.$exDel.'</td>
                                    <td>'.searchNamebyId($data['created_by'], $db).'</td>
                                </tr>';                
                            }
    
                            $rowData .= '
                                <tr class="details fw-bold">
                                    <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">Date Total : '.$date.'</td>
                                    <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.count($grpData).'</td>
                                    <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grpNettWeight.'</td>
                                    <td></td>
                                    <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                </tr>
                                <tr style="height: 18.5px;"></tr>
                            ';
                            
                            // Append this row block to full HTML
                            $compiledRowData .= $rowData;
    
                            $grpTotalCount += count($grpData);
                            $totalNettWeight += $grpNettWeight;
                        }
    
                        $compiledRowData .= '
                            <tr class="details fw-bold">
                                <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">Company Total : </td>
                                <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grpTotalCount.'</td>
                                <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$totalNettWeight.'</td>
                                <td></td>
                                <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                            </tr>
                            <tr style="height: 18.5px;"></tr>         
                        ';
                    }elseif ($groupCount == 2) { 
                        $grpTotalCount = 0;
                        $dateNettWeight = 0;
                        $totalNettWeight = 0;
                        foreach ($processedData as $grp1 => $grp1Data) { 
                            $rowData = '
                                <tr>
                                    <td colspan="17" style="border:0; padding-bottom: 0;">
                                        <div class="fw-bold">
                                            <span>
                                                '.$groupOrder[0].' <span>:</span> '.$grp1.' &nbsp;&nbsp;&nbsp;&nbsp; '.callLookup($groupOrder[0], $grp1, $db).'
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            '; 
                            $grp1Records = 0;
                            $grp1NettWeight = 0;
                            foreach ($grp1Data as $date => $dateData){
                                $rowData .= '
                                    <tr>
                                        <td colspan="17" style="border:0; padding-bottom: 0;">
                                            <div class="fw-bold">
                                                <span>
                                                    Date <span>:</span> '.$date.'
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                '; 
    
                                foreach ($dateData as $data){
                                    $dateNettWeight += $data['nett_weight1']/1000;
                                    if ($data['ex_del'] == 'EX'){
                                        $exDel = 'E';
                                    }else{
                                        $exDel = 'D';
                                    }
    
                                    $rowData .= '<tr class="details">
                                        <td>'.$data['transaction_id'].'</td>
                                        <td>'.$data['transporter_code'].'</td>
                                        <td>'.$data['lorry_plate_no1'].'</td>
                                        <td>'.$data['agent_code'].'</td>
                                        <td>'.date("d/m/Y", strtotime($data['transaction_date'])).'</td>
                                        <td width="10%">'.$data['purchase_order'].'</td>
                                        <td class="text-end">'.date("H:i", strtotime($data['gross_weight1_date'])).'</td>
                                        <td class="text-end">'.date("H:i", strtotime($data['tare_weight1_date'])).'</td>
                                        <td class="text-end">'.($data['gross_weight1']/1000).'</td>
                                        <td class="text-end">'.($data['tare_weight1']/1000).'</td>
                                        <td class="text-end">'.($data['nett_weight1']/1000).'</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td>'.$exDel.'</td>
                                        <td>'.searchNamebyId($data['created_by'], $db).'</td>
                                    </tr>';                
                                }
    
                                $rowData .= '
                                    <tr class="details fw-bold">
                                        <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">Date Total : '.$date.'</td>
                                        <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.count($dateData).'</td>
                                        <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$dateNettWeight.'</td>
                                        <td></td>
                                        <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                        <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                        <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    </tr>
                                    <tr style="height: 18.5px;"></tr>      
                                ';
    
                                $grp1Records += count($dateData);
                                $grp1NettWeight += $dateNettWeight;
                            }
    
                            $rowData .= '
                                <tr class="details fw-bold">
                                    <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$groupOrder[0].' Total : '.callLookup($groupOrder[0], $grp1, $db).'</td>
                                    <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp1Records.'</td>
                                    <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp1NettWeight.'</td>
                                    <td></td>
                                    <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                </tr>
                                <tr style="height: 18.5px;"></tr>   
                            ';
                            
                            // Append this row block to full HTML
                            $compiledRowData .= $rowData;
    
                            $grpTotalCount += $grp1Records;
                            $totalNettWeight += $grp1NettWeight;
                        }
    
                        $compiledRowData .= '
                            <tr class="details fw-bold">
                                <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">Company Total : </td>
                                <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grpTotalCount.'</td>
                                <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$totalNettWeight.'</td>
                                <td></td>
                                <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                            </tr>
                        ';
                    }elseif ($groupCount == 3) {
                        $companyCount = 0;
                        $companyNettWeight = 0;
                        $grp2Count = [];
                        $grp2NettWeight = [];
                        
                        foreach ($processedData as $grp1 => $grp1Data){
                            $rowData = '
                                <tr>
                                    <td colspan="17" style="border:0; padding-bottom: 0;">
                                        <div class="fw-bold">
                                            <span>
                                                '.$groupOrder[0].' <span>:</span> '.$grp1.' &nbsp;&nbsp;&nbsp;&nbsp; '.callLookup($groupOrder[0], $grp1, $db).'
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            '; 
    
                            $grp1Count = 0;
                            $grp1NettWeight = 0;
                            foreach ($grp1Data as $grp2 => $grp2Data){
                                $rowData .= '
                                    <tr>
                                        <td colspan="17" style="border:0; padding-top: 0; padding-bottom: 0;">
                                            <div class="fw-bold">
                                                <span>
                                                    '.$groupOrder[1].' <span>:</span> '.$grp2.' &nbsp;&nbsp;&nbsp;&nbsp; '.callLookup($groupOrder[1], $grp2, $db).'
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                '; 
    
                                $grp2Count[$grp2] = 0;
                                $grp2NettWeight[$grp2] = 0;
    
                                foreach ($grp2Data as $grp3 => $grp3Data){ 
                                    $grp2Count[$grp2] += count($grp3Data);
    
                                    $rowData .= '
                                        <tr>
                                            <td colspan="17" style="border:0; padding-top: 0; padding-bottom: 0;">
                                                <div class="fw-bold">
                                                    <span>
                                                    Date <span>:</span> '.$grp3.'
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    '; 
    
                                    $dateNettWeight = 0;
                                    foreach ($grp3Data as $data){ 
                                        $grp1Count++;
                                        $dateNettWeight += $data['nett_weight1']/1000; 
        
                                        $rowData .= '<tr class="details">
                                            <td>'.$data['transaction_id'].'</td>
                                            <td>'.$data['transporter_code'].'</td>
                                            <td>'.$data['lorry_plate_no1'].'</td>
                                            <td>'.date("d/m/Y", strtotime($data['transaction_date'])).'</td>
                                            <td width="10%">'.$data['purchase_order'].'</td>
                                            <td class="text-end">'.date("H:i", strtotime($data['gross_weight1_date'])).'</td>
                                            <td class="text-end">'.date("H:i", strtotime($data['tare_weight1_date'])).'</td>
                                            <td class="text-end">'.($data['gross_weight1']/1000).'</td>
                                            <td class="text-end">'.($data['tare_weight1']/1000).'</td>
                                            <td class="text-end">'.($data['nett_weight1']/1000).'</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                            <td>'.searchNamebyId($data['created_by'], $db).'</td>
                                        </tr>';                
                                    }
    
                                    $rowData .= '
                                        <tr class="details fw-bold">
                                            <td colspan="5" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">Date Total : '.$grp3.'</td>
                                            <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.count($grp3Data).'</td>
                                            <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$dateNettWeight.'</td>
                                            <td></td>
                                            <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                            <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                            <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                        </tr>
                                        <tr style="height: 18.5px;"></tr>      
                                    ';
    
                                    $grp2NettWeight[$grp2] += $dateNettWeight;
                                    $grp1NettWeight += $dateNettWeight;
    
                                }
    
                                $rowData .= '
                                    <tr class="details fw-bold">
                                        <td colspan="5" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$groupOrder[1].' Total : '.callLookup($groupOrder[1], $grp2, $db).'</td>
                                        <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp2Count[$grp2].'</td>
                                        <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp2NettWeight[$grp2].'</td>
                                        <td></td>
                                        <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                        <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                        <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    </tr>
                                    <tr style="height: 18.5px;"></tr>
                                ';
                            }
    
                            $rowData .= '
                                <tr class="details fw-bold">
                                    <td colspan="5" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$groupOrder[0].' Total : '.callLookup($groupOrder[0], $grp1, $db).'</td>
                                    <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp1Count.'</td>
                                    <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp1NettWeight.'</td>
                                    <td></td>
                                    <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                </tr>
                                <tr style="height: 18.5px;"></tr>   
                            ';
    
                            $companyCount += $grp1Count;
                            $companyNettWeight += $grp1NettWeight;
    
                            $compiledRowData .= $rowData;
                        }
    
                        // $compiledRowData .= '
                        //     <tr class="details fw-bold">
                        //         <td colspan="5" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">Company Total : </td>
                        //         <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$companyCount.'</td>
                        //         <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$companyNettWeight.'</td>
                        //         <td></td>
                        //         <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                        //         <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                        //         <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                        //     </tr>
                        // ';
                    }elseif($groupCount == 4){
                        $companyCount = 0;
                        $companyNettWeight = 0;
    
                        foreach ($processedData as $grp1 => $grp1Data) {
                            $rowData = '
                                <tr>
                                    <td colspan="17" style="border:0; padding-bottom: 0;">
                                        <div class="fw-bold">
                                            <span>
                                                '.$groupOrder[0].' <span>:</span> '.$grp1.' &nbsp;&nbsp;&nbsp;&nbsp; '.callLookup($groupOrder[0], $grp1, $db).'
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            '; 
                        
                            $grp1Count = 0;
                            $grp1TotalNettWeight = 0;
                        
                            foreach ($grp1Data as $grp2 => $grp2Data) {
                                $rowData .= '
                                    <tr>
                                        <td colspan="17" style="border:0; padding-top: 0; padding-bottom: 0;">
                                            <div class="fw-bold">
                                                <span>
                                                    '.$groupOrder[1].' <span>:</span> '.$grp2.' &nbsp;&nbsp;&nbsp;&nbsp; '.callLookup($groupOrder[1], $grp2, $db).'
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                '; 
                        
                                $grp2Count = 0;
                                $grp2TotalNettWeight = 0;
                        
                                foreach ($grp2Data as $grp3 => $grp3Data) { 
                                    $rowData .= '
                                        <tr>
                                            <td colspan="17" style="border:0; padding-top: 0; padding-bottom: 0;">
                                                <div class="fw-bold">
                                                    <span>
                                                    '.$groupOrder[2].' <span>:</span> '.$grp3.' &nbsp;&nbsp;&nbsp;&nbsp; '.callLookup($groupOrder[2], $grp3, $db).'
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    '; 
                        
                                    $grp3Count = 0;
                                    $grp3TotalNettWeight = 0;
                        
                                    foreach ($grp3Data as $grp4 => $grp4Data) {
                                        $rowData .= '
                                            <tr>
                                                <td colspan="17" style="border:0; padding-top: 0; padding-bottom: 0;">
                                                    <div class="fw-bold">
                                                        <span>
                                                        Date <span>:</span> '.$grp4.'
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        ';
                        
                                        $dateNettWeight = 0;
                                        $grp4Count = 0;
                        
                                        foreach ($grp4Data as $data) {
                                            $dateNettWeight += $data['nett_weight1'] / 1000;
                                            $grp4Count++;
                                            if ($data['ex_del'] == 'EX') {
                                                $exDel = 'E';
                                            } else {
                                                $exDel = 'D';
                                            }
                        
                                            $rowData .= '<tr class="details">
                                                <td>'.$data['transaction_id'].'</td>
                                                <td>'.$data['transporter_code'].'</td>
                                                <td>'.$data['lorry_plate_no1'].'</td>
                                                <td>'.$data['agent_code'].'</td>
                                                <td>'.date("d/m/Y", strtotime($data['transaction_date'])).'</td>
                                                <td width="10%">'.$data['purchase_order'].'</td>
                                                <td class="text-end">'.date("H:i", strtotime($data['gross_weight1_date'])).'</td>
                                                <td class="text-end">'.date("H:i", strtotime($data['tare_weight1_date'])).'</td>
                                                <td class="text-end">'.($data['gross_weight1'] / 1000).'</td>
                                                <td class="text-end">'.($data['tare_weight1'] / 1000).'</td>
                                                <td class="text-end">'.($data['nett_weight1'] / 1000).'</td>
                                                <td class="text-end">0.00</td>
                                                <td class="text-end">0.00</td>
                                                <td class="text-end">0.00</td>
                                                <td class="text-end">0.00</td>
                                                <td class="text-end">0.00</td>
                                                <td>'.$exDel.'</td>
                                                <td>'.searchNamebyId($data['created_by'], $db).'</td>
                                            </tr>';                
                                        }
                        
                                        $rowData .= '
                                            <tr class="details fw-bold">
                                                <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">Date Total : '.$grp4.'</td>
                                                <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp4Count.'</td>
                                                <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$dateNettWeight.'</td>
                                                <td></td>
                                                <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                                <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                                <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                            </tr>
                                            <tr style="height: 18.5px;"></tr>      
                                        ';
                        
                                        $grp3TotalNettWeight += $dateNettWeight;
                                        $grp3Count += $grp4Count;
                                    }
                        
                                    $rowData .= '
                                        <tr class="details fw-bold">
                                            <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$groupOrder[2].' Total : '.callLookup($groupOrder[2], $grp3, $db).'</td>
                                            <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp3Count.'</td>
                                            <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp3TotalNettWeight.'</td>
                                            <td></td>
                                            <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                            <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                            <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                        </tr>
                                        <tr style="height: 18.5px;"></tr>
                                    ';
                        
                                    $grp2TotalNettWeight += $grp3TotalNettWeight;
                                    $grp2Count += $grp3Count;
                                }
                        
                                $rowData .= '
                                    <tr class="details fw-bold">
                                        <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$groupOrder[1].' Total : '.callLookup($groupOrder[1], $grp2, $db).'</td>
                                        <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp2Count.'</td>
                                        <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp2TotalNettWeight.'</td>
                                        <td></td>
                                        <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                        <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                        <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    </tr>
                                    <tr style="height: 18.5px;"></tr>   
                                ';
                        
                                $grp1TotalNettWeight += $grp2TotalNettWeight;
                                $grp1Count += $grp2Count;
                            }
                        
                            $rowData .= '
                                <tr class="details fw-bold">
                                    <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$groupOrder[0].' Total : '.callLookup($groupOrder[0], $grp1, $db).'</td>
                                    <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp1Count.'</td>
                                    <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$grp1TotalNettWeight.'</td>
                                    <td></td>
                                    <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                    <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                </tr>
                                <tr style="height: 18.5px;"></tr>   
                            ';
                            
                            $companyNettWeight += $grp1TotalNettWeight;
                            $companyCount += $grp1Count;
                            $compiledRowData .= $rowData;
                        }
    
                        $compiledRowData .= '
                            <tr class="details fw-bold">
                                <td colspan="6" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">Company Total : </td>
                                <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$companyCount.'</td>
                                <td colspan="3" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">'.$companyNettWeight.'</td>
                                <td></td>
                                <td colspan="2" class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                                <td class="text-end" style="border-top: 1px dashed black; border-bottom: 1px dashed black;">0.00</td>
                            </tr>
                        ';
                    }
    
                    $message = '
                        <html>
                            <head>
                                <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
                                <link rel="stylesheet" href="assets/css/custom.min.css" type="text/css" media="all" />
    
                                <style>
                                    @page {
                                        size: A4 landscape;
                                        margin: 5mm;
                                    }
    
                                    @media print {
                                        header {
                                            top: 0;
                                            width: 100%;
                                            background: white;
                                            z-index: 1000; /* High z-index to make sure header stays on top */
                                        } 
    
                                        .details td {
                                            border: 0;
                                            padding-top: 0;
                                            padding-bottom: 0;
                                        }
    
                                        .section-break {
                                            page-break-before: always;
                                        }
                                    }
                                </style>
                            </head>
    
                            <body>
                                <header>
                                    <div class="row">
                                        <div class="d-flex justify-content-center">
                                            <h5 class="fw-bold">EAST ROCK MARKETING SDN. BHD.</h5>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <p>Sales Weighing Summary Report By '.$groupBy.'</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <p>
                                            Start Date : '.$fromDate.' Last Date : '.$toDate.'
                                            <br>
                                            Start Customer / Last Customer : '.reset($headerGroup['Customer']).' / '.end($headerGroup['Customer']).'
                                            <br>
                                            Start Product / Last Product : '.reset($headerGroup['Product']).' / '.end($headerGroup['Product']).'
                                        </p>
                                    </div>
                                </header>
                                <div class="container-full content">
                                    <div class="row">
                                        <div>
                                            <table class="table" style="font-size: 12px">
                                                <thead style="border-bottom: 1px solid black;">
                                                    <tr class="text-center" style="border-top: 1px solid black;">
                                                        <th rowspan="2" class="text-start">Serial No.</th>
                                                        <th rowspan="2">Transport</th>
                                                        <th rowspan="2">Vehicle No.</th>
                                                        <th rowspan="2">Date</th>
                                                        <th rowspan="2">P/O No</th>
                                                        <th colspan="2" class="pb-0 pt-0" style="border-bottom: none;">Time</th>
                                                        <th colspan="3" class="pt-0 pb-0" style="border-bottom: none;">Weight (MT)</th>
                                                        <th rowspan="2">Price <br>/Ton</th>
                                                        <th rowspan="2">Trans Rate</th>
                                                        <th rowspan="2">Ex_GST <br>(RM)</th>
                                                        <th rowspan="2">GST 0% <br>(RM)</th>
                                                        <th rowspan="2">Amount <br>(RM)</th>
                                                        <th rowspan="2"></th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>In</th>
                                                        <th>Out</th>
                                                        <th>In</th>
                                                        <th>Out</th>
                                                        <th>Net</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                '.$compiledRowData.'
                                                </tbody>
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
                                            
                                            /*.row {
                                                display: flex;
                                                flex-wrap: wrap;
                                                margin-top: 20px;
                                                margin-right: -15px;
                                                margin-left: -15px;  
                                            } 
                                            
                                            .col-md-4{
                                                position: relative;
                                                width: 33.333333%;
                                            }*/
                                        </style>
                                    </head>
                                    <body>';

                                    while ($row = $result->fetch_assoc()) {
                                        $message .= '<div class="container-full content">
                                        <div class="row">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
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
                                                    $count = 0;
                                                    $sub_total = 0;

                                                    while ($row2 = $result2->fetch_assoc()) {
                                                        $sub_total += $row2['total_price'];
                                                        $count++;

                                                        $message .= '<tr class="details">
                                                            <td>'.$count.'</td>
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
                        "message" => "Something Went Wrong"
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