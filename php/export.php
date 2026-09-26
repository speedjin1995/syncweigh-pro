<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/permissions.php';

// Load the database configuration file 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 

function getRequestValue($key, $default = '') {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

function getRequestList($key) {
    if (!isset($_GET[$key])) {
        return array();
    }

    $value = $_GET[$key];

    if (is_array($value)) {
        return array_values(array_filter($value, function($item) {
            return $item !== '' && $item !== '-';
        }));
    }

    if ($value === '' || $value === '-') {
        return array();
    }

    return array_values(array_filter(explode(',', $value), function($item) {
        return trim($item) !== '' && trim($item) !== '-';
    }));
}

function appendInFilter(&$searchQuery, $db, $column, $values) {
    if (count($values) === 0) {
        return;
    }

    $escapedValues = array_map(function($value) use ($db) {
        return "'" . mysqli_real_escape_string($db, trim($value)) . "'";
    }, $values);

    $searchQuery .= " AND ".$column." IN (" . implode(',', $escapedValues) . ")";
}

function appendEqualFilter(&$searchQuery, $db, $column, $value) {
    if ($value !== null && $value !== '' && $value !== '-') {
        $searchQuery .= " and ".$column." = '".mysqli_real_escape_string($db, $value)."'";
    }
}

function appendLikeFilter(&$searchQuery, $db, $column, $value) {
    if ($value !== null && $value !== '' && $value !== '-') {
        $searchQuery .= " and ".$column." like '%".mysqli_real_escape_string($db, $value)."%'";
    }
}

function parseReportDate($value, $isEndDate = false) {
    if ($value === null || $value === '') {
        return null;
    }

    $formats = array('d-m-Y H:i', 'Y-m-d H:i', 'Y-m-d');

    foreach ($formats as $format) {
        $dateTime = DateTime::createFromFormat($format, $value);

        if ($dateTime instanceof DateTime) {
            if ($format === 'Y-m-d') {
                $dateTime->setTime($isEndDate ? 23 : 0, $isEndDate ? 59 : 0, $isEndDate ? 59 : 0);
            } elseif ($isEndDate) {
                $dateTime->setTime((int)$dateTime->format('H'), (int)$dateTime->format('i'), 59);
            } else {
                $dateTime->setTime((int)$dateTime->format('H'), (int)$dateTime->format('i'), 0);
            }

            return $dateTime->format('Y-m-d H:i:s');
        }
    }

    return null;
}
 
// Excel file name for download 
if(getRequestValue("file") == 'weight'){
    $fileName = "Weight-data_" . date('Y-m-d') . ".xls";
}else{
    $fileName = "Count-data_" . date('Y-m-d') . ".xls";
}

## Search 
$searchQuery = "";
$reportStatus = getRequestValue('status', 'Sales');

$fromDate = parseReportDate(getRequestValue('fromDate'), false);
if($fromDate !== null){
    if(getRequestValue("file") == 'weight'){
        $searchQuery .= " and Weight.tare_weight1_date >= '".$fromDate."'";
    }
    else{
        $searchQuery .= " and count.tare_weight1_date >= '".$fromDate."'";
    }
}

$toDate = parseReportDate(getRequestValue('toDate'), true);
if($toDate !== null){
    if(getRequestValue("file") == 'weight'){
        $searchQuery .= " and Weight.tare_weight1_date <= '".$toDate."'";
    }
    else{
        $searchQuery .= " and count.tare_weight1_date <= '".$toDate."'";
    }
}

if(getRequestValue('status') != null && getRequestValue('status') != '' && getRequestValue('status') != '-'){
    if(getRequestValue("file") == 'weight'){
        appendEqualFilter($searchQuery, $db, 'Weight.transaction_status', getRequestValue('status'));
    }
    else{
        appendEqualFilter($searchQuery, $db, 'count.transaction_status', getRequestValue('status'));
    }

    if (getRequestValue('status') == 'Local'){
        $reportStatus = 'Public';
    }else{
        $reportStatus = getRequestValue('status');
    }
}

if(getRequestValue("file") == 'weight'){
    appendInFilter($searchQuery, $db, 'Weight.customer_code', getRequestList('customer'));
} else {
    appendInFilter($searchQuery, $db, 'count.customer_code', getRequestList('customer'));
}

if(getRequestValue("file") == 'weight'){
    appendInFilter($searchQuery, $db, 'Weight.supplier_code', getRequestList('supplier'));
} else {
    appendInFilter($searchQuery, $db, 'count.supplier_code', getRequestList('supplier'));
}

if(getRequestValue("file") == 'weight'){
    appendLikeFilter($searchQuery, $db, 'Weight.lorry_plate_no1', getRequestValue('vehicle'));
} else {
    appendLikeFilter($searchQuery, $db, 'count.lorry_plate_no1', getRequestValue('vehicle'));
}

if(getRequestValue("file") == 'weight'){
    appendLikeFilter($searchQuery, $db, 'Weight.weight_type', getRequestValue('weighingType'));
} else {
    appendLikeFilter($searchQuery, $db, 'count.weight_type', getRequestValue('weighingType'));
}

if(getRequestValue("file") == 'weight'){
    appendEqualFilter($searchQuery, $db, 'Weight.customer_type', getRequestValue('customerType'));
} else {
    appendEqualFilter($searchQuery, $db, 'count.customer_type', getRequestValue('customerType'));
}

if(getRequestValue("file") == 'weight'){
    appendInFilter($searchQuery, $db, 'Weight.product_code', getRequestList('product'));
} else {
    appendInFilter($searchQuery, $db, 'count.product_code', getRequestList('product'));
}

$rawMatList = getRequestList('rawMat');
if (count($rawMatList) === 0) {
    $rawMatList = getRequestList('rawMaterial');
}

if(getRequestValue("file") == 'weight'){
    appendInFilter($searchQuery, $db, 'Weight.raw_mat_code', $rawMatList);
} else {
    appendInFilter($searchQuery, $db, 'count.raw_mat_code', $rawMatList);
}

if(getRequestValue('destination') != null && getRequestValue('destination') != '' && getRequestValue('destination') != '-'){
    if(getRequestValue("file") == 'weight'){
        appendEqualFilter($searchQuery, $db, 'Weight.destination', getRequestValue('destination'));
    }
    else{
        appendEqualFilter($searchQuery, $db, 'count.destination', getRequestValue('destination'));
    }
}

if(getRequestValue('plant') != null && getRequestValue('plant') != '' && getRequestValue('plant') != '-'){
    if(getRequestValue("file") == 'weight'){
        appendEqualFilter($searchQuery, $db, 'Weight.plant_code', getRequestValue('plant'));
    }
    else{
        appendEqualFilter($searchQuery, $db, 'count.plant_code', getRequestValue('plant'));
    }
}else{
    if (!hasModulePermission('Report', $reportStatus, ['view_all_plants'])){
        $username = implode("', '", $_SESSION["plant"]);
        $searchQuery .= " and Weight.plant_code IN ('$username')";
    }
}

if(getRequestValue('purchaseOrder') != null && getRequestValue('purchaseOrder') != '' && getRequestValue('purchaseOrder') != '-'){
    if(getRequestValue("file") == 'weight'){
        appendEqualFilter($searchQuery, $db, 'Weight.purchase_order', getRequestValue('purchaseOrder'));
    }
    else{
        appendEqualFilter($searchQuery, $db, 'count.purchaseNo', getRequestValue('purchaseOrder'));
    }
}

if(getRequestValue('soNo') != null && getRequestValue('soNo') != '' && getRequestValue('soNo') != '-'){
    appendEqualFilter($searchQuery, $db, 'Weight.purchase_order', getRequestValue('soNo'));
}

if(getRequestValue('batchDrum') != null && getRequestValue('batchDrum') != '' && getRequestValue('batchDrum') != '-'){
    if(getRequestValue("file") == 'weight'){
        appendEqualFilter($searchQuery, $db, 'Weight.batch_drum', getRequestValue('batchDrum'));
    }
    else{
        appendEqualFilter($searchQuery, $db, 'count.batch_drum', getRequestValue('batchDrum'));
    }
}

$isMulti = '';
if(getRequestValue('isMulti') != null && getRequestValue('isMulti') != '' && getRequestValue('isMulti') != '-'){
    $isMulti = getRequestValue('isMulti');
}

// Column names 
if (!hasModulePermission('Report', $reportStatus, ['include_price'])){
    if ($reportStatus == 'Sales'){
        $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'TIN NO.', 'ID NO.', 'ID TYPE', 'CUSTOMER TYPE', 'CUSTOMER CODE', 'CUSTOMER NAME', 
            'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 
            'DELIVERED BY', 'EX-QUARRY / DELIVERED', 'BATCH/DRUM', 'PO NO.', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
            ($reportStatus == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'VARIANCE (MT)', 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 
            'PLANT CODE', 'PLANT NAME', 'WEIGHTED BY', 'REMARKS'); 
    }
    else{
        $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'CUSTOMER TYPE', 'CUSTOMER CODE', 'CUSTOMER NAME', 
            'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 
            'DELIVERED BY', 'EX-QUARRY / DELIVERED', 'BATCH/DRUM', 'PO NO.', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
            ($reportStatus == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'VARIANCE (MT)', 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
            'PLANT NAME', 'WEIGHTED BY', 'REMARKS'); 
    }
}
else{
    if ($reportStatus == 'Sales'){
        $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'TIN NO.', 'ID NO.', 'ID TYPE','CUSTOMER TYPE', 'CUSTOMER CODE', 'CUSTOMER NAME', 
            'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 
            'DELIVERED BY', 'EX-QUARRY / DELIVERED', 'BATCH/DRUM', 'PO NO.', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
            ($reportStatus == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'VARIANCE (MT)', 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
            'PLANT NAME', 'UNIT PRICE (RM)', 'TOTAL PRICE (RM)', 'WEIGHTED BY', 'REMARKS'); 
    }else{
        $fields = array('TRANSACTION ID', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'CUSTOMER TYPE', 'CUSTOMER CODE', 'CUSTOMER NAME', 
            'SUPPLIER CODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'DESTINATION CODE', 'TO DESTINATION', 'TRANSPORTER CODE', 
            'DELIVERED BY', 'EX-QUARRY / DELIVERED', 'BATCH/DRUM', 'PO NO.', 'DO NO.', 'GROSS WEIGHT (MT)', 'TARE WEIGHT (MT)', 'NET WEIGHT (MT)', 
            ($reportStatus == 'Sales' ? 'ORDER WEIGHT (MT)' : 'SUPPLIER WEIGHT (MT)'), 'VARIANCE (MT)', 'IN TIME', 'OUT TIME', 'MANUAL', 'CANCELLED', 'PLANT CODE', 
            'PLANT NAME', 'UNIT PRICE (RM)', 'TOTAL PRICE (RM)', 'WEIGHTED BY', 'REMARKS'); 
    }
}


// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";

// Fetch records from database
if($_GET["file"] == 'weight'){
    if ($isMulti == 'Y'){
        $id = $_GET['id']; 
        $sql = "select * from Weight WHERE id IN ($id)";
    }else{
        $sql = "select * from Weight WHERE Weight.is_cancel = 'N'".$searchQuery;
    }

    $query = $db->query($sql);
}
else{
    $query = $db->query("select count.id, count.serialNo, vehicles.veh_number, lots.lots_no, count.batchNo, count.invoiceNo, count.deliveryNo, 
    count.purchaseNo, customers.customer_name, products.product_name, packages.packages, count.unitWeight, count.tare, count.totalWeight, 
    count.actualWeight, count.currentWeight, units.units, count.moq, count.dateTime, count.unitPrice, count.totalPrice,count.totalPCS, 
    count.remark, count.deleted, status.status from count, vehicles, packages, lots, customers, products, units, status WHERE 
    count.vehicleNo = vehicles.id AND count.package = packages.id AND count.lotNo = lots.id AND count.customer = customers.id AND 
    count.productName = products.id AND status.id=count.status AND units.id=count.unit ".$searchQuery."");
}

if($query->num_rows > 0){ 
    // Output each row of the data 
    while($row = $query->fetch_assoc()){
        $lineData = []; // Ensure it starts as an empty array each iteration

        if($_GET["file"] == 'weight'){
            $exDel = '';
            
            if ($row['ex_del'] == 'EX'){
                $exDel = 'E';
            }else{
                $exDel = 'D';
            }
            
            if (!hasModulePermission('Report', $reportStatus, ['include_price'])){
                $lineData = array_merge(
                    array($row['transaction_id'], $row['weight_type'], $row['transaction_date'], $row['lorry_plate_no1']),
                    ($row['transaction_status'] == 'Sales' ? array($row['tin_no'], $row['id_no'], $row['id_type']) : array()),
                    array($row['customer_type'], $row['customer_code'], $row['customer_name'], $row['supplier_code'], $row['supplier_name'], 
                    ($row['transaction_status'] == 'Sales' ? $row['product_code'] : $row['raw_mat_code']), 
                    ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']), 
                    $row['product_description'], $row['destination_code'], $row['destination'], $row['transporter_code'], 
                    $row['transporter'], $exDel, $row['batch_drum'], $row['purchase_order'], $row['delivery_no'], 
                    number_format((float)$row['gross_weight1'] / 1000, 2, '.', ''), number_format((float)$row['tare_weight1'] / 1000, 2, '.', ''), 
                    number_format((float)$row['nett_weight1'] / 1000, 2, '.', ''), 
                    ($row['transaction_status'] == 'Sales' ? number_format((float)$row['order_weight'] / 1000, 2, '.', '') : number_format((float)$row['supplier_weight'] / 1000, 2, '.', '')), 
                    number_format((float)$row['weight_different'] / 1000, 2, '.', ''), $row['gross_weight1_date'], $row['tare_weight1_date'], 
                    $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $row['created_by'], $row['remarks'])
                );
            }
            else{
                $unitPrice = 0.0;
                $totalPrice = 0.0;

                if($row['purchase_order'] != null && $row['purchase_order'] != '' && $row['purchase_order'] != '-'){
                    if($row['transaction_status'] == 'Sales'){
                        $stmt = $db->prepare("SELECT * FROM Sales_Order WHERE order_no = ? AND product_code = ? AND plant_code = ?");
                        $stmt->bind_param('sss', $row['purchase_order'], $row['product_code'], $row['plant_code']);
                        $stmt->execute();
                        $query2 = $stmt->get_result();

                        if($row2 = $query2->fetch_assoc()){ 
                            $unitPrice = $row2['unit_price'];
                            $totalPrice = (float)$unitPrice * ((float)$row['nett_weight1'] / 1000);
                            $totalPrice = number_format($totalPrice, 2, '.', '');
                        }
                        $stmt->close();
                    }
                    else{
                        $stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE po_no = ? AND raw_mat_code = ? AND plant_code = ?");
                        $stmt->bind_param('sss', $row['purchase_order'], $row['raw_mat_code'], $row['plant_code']);
                        $stmt->execute();
                        $query2 = $stmt->get_result();

                        if($row2 = $query2->fetch_assoc()){ 
                            $unitPrice = $row2['unit_price'];
                            $totalPrice = (float)$unitPrice * ((float)$row['nett_weight1'] / 1000);
                            $totalPrice = number_format($totalPrice, 2, '.', '');
                        }
                        $stmt->close();
                    }
                }

                $lineData = array_merge(
                    array($row['transaction_id'], $row['weight_type'], $row['transaction_date'], $row['lorry_plate_no1']),
                    ($row['transaction_status'] == 'Sales' ? array($row['tin_no'], $row['id_no'], $row['id_type']) : array()),
                    array($row['customer_type'], $row['customer_code'], $row['customer_name'], $row['supplier_code'], $row['supplier_name'], 
                    ($row['transaction_status'] == 'Sales' ? $row['product_code'] : $row['raw_mat_code']), 
                    ($row['transaction_status'] == 'Sales' ? $row['product_name'] : $row['raw_mat_name']), 
                    $row['product_description'], $row['destination_code'], $row['destination'], $row['transporter_code'], 
                    $row['transporter'], $exDel, $row['batch_drum'], $row['purchase_order'], $row['delivery_no'], 
                    number_format((float)$row['gross_weight1'] / 1000, 2, '.', ''), number_format((float)$row['tare_weight1'] / 1000, 2, '.', ''), 
                    number_format((float)$row['nett_weight1'] / 1000, 2, '.', ''), 
                    ($row['transaction_status'] == 'Sales' ? number_format((float)$row['order_weight'] / 1000, 2, '.', '') : number_format((float)$row['supplier_weight'] / 1000, 2, '.', '')), 
                    number_format((float)$row['weight_different'] / 1000, 2, '.', ''), $row['gross_weight1_date'], $row['tare_weight1_date'], 
                    $row['manual_weight'], $row['is_cancel'], $row['plant_code'], $row['plant_name'], $unitPrice, $totalPrice, $row['created_by'], $row['remarks'])
                );
            }
        }
        else{
            $lineData = array($row['serialNo'], $row['product_name'], $row['units'], $row['unitWeight'], $row['tare'], $row['currentWeight'], $row['actualWeight'],
            $row['totalPCS'], $row['moq'], $row['unitPrice'], $row['totalPrice'], $row['veh_number'], $row['lots_no'], $row['batchNo'], $row['invoiceNo']
            , $row['deliveryNo'], $row['purchaseNo'], $row['customer_name'], $row['packages'], $row['dateTime'], $row['remark'], $row['status'], $deleted);
        }

        # Added checking to fix duplicated issue
        if (!empty($lineData)) {
            array_walk($lineData, 'filterData'); 
            $excelData .= implode("\t", array_values($lineData)) . "\n"; 
        }
    } 

    $db->close();
}else{ 
    $excelData .= 'No records found...'. "\n"; 
}
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData;
 
exit;
?>
