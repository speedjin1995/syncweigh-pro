<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    $fromDate = new DateTime($_POST['fromDate']);
    $fromDateTime = date_format($fromDate,"Y-m-d 00:00:00");
    $searchQuery = " and tare_weight1_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
    $toDate = new DateTime($_POST['toDate']);
    $toDateTime = date_format($toDate,"Y-m-d 23:59:59");
    $searchQuery .= " and tare_weight1_date <= '".$toDateTime."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '')
{
    $plant = searchPlantCodeById($_POST['plant'], $db);
    $searchQuery .= " and plant_code = '".$plant."'";
}

// Filter by product
$productionList = [
    'BITUMEN 60/70'
];

// , 'BITUMEN PG76', 'QUARRY DUST', '10MM AGGREGATE', '14MM AGGREGATE', '20MM AGGREGATE', '28MM AGGREGATE',
//     '40MM AGGREGATE'

// Build array
$products = [];
foreach ($productionList as $product) {
    $products[$product] = 0;
}

// Build search query for products
$upperCodes = array_map(function($code) {
    return "'" . strtoupper($code) . "'";
}, $productionList);
$productWhereClause = implode(',', $upperCodes);
$searchQuery .= " and UPPER(product_name) IN ($productWhereClause)";

// Build response data structure
$productNames = [
    'AC14', 'AC28', 'ACW20', 'ACW14', 'ACB20', 'ACB28', 'AC10', 
    'BMB28', '20MM', 'BMB20', 'SFM14', '3/8 WC', 'SMA20', 'DBM40', 
    'FMA20', 'CMA', 'CRGGA', 'AC14 Latex', 'AC14 MR6', 'ACW20 MR6', 
    'SMA20 Adv', 'Dust mix', 'AC14RPF', 'ACW20 PLUS', 'SMA20 PLUS', 
    'BMW14'
];

// For stock used array with product name as key
$fields = [
    'Tonnage','60/70','PG76','CRMB','CMB','LMB','LEMB','% Bit Usage','Actual Bit Usage',
    'Bit %','Plant Control Bit %','% Bit Usage 2','Q.Dust','10mm','14mm','20mm','28mm','40mm',
    'OPC','Lime'
];

$data = [];
foreach ($productNames as $product) {
    $data[$product] = [];
    foreach ($fields as $field) {
        $data[$product][$field] = 0;
    }
}

## Fetch records
$empQuery = "select * from Weight WHERE is_complete = 'Y' AND  is_cancel <> 'Y' AND status = '0' AND transaction_status IN ('Sales', 'Local')".$searchQuery." order by tare_weight1_date asc";
$empRecords = mysqli_query($db, $empQuery);

while($row = mysqli_fetch_assoc($empRecords)) {
    $productName = strtoupper($row['product_name']);
    if (array_key_exists($productName, $products)) {
        $products[$productName] += (float) $row['final_weight'] ?? 0;
    }
}

// Process data to add into the response data structure


## Response
$response = array(
  "aaData" => $data,
);

echo json_encode($response);

?>