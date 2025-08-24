<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
// // Load the database configuration file 
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
 
## Search 
$searchQuery = "";

if(!empty($_GET['fromDate'])){
    $fromDateTime = (new DateTime($_GET['fromDate']))->format("Y-m-d 00:00:00");
    $searchQuery .= " AND tare_weight1_date >= '".$fromDateTime."'";
}

if(!empty($_GET['toDate'])){
    $toDateTime = (new DateTime($_GET['toDate']))->format("Y-m-d 23:59:59");
    $searchQuery .= " AND tare_weight1_date <= '".$toDateTime."'";
}

$plantId = !empty($_GET['plant']) ? $_GET['plant'] : null;
if($plantId){
    $plantCode = searchPlantCodeById($plantId, $db);
    $plantName = searchPlantNameById($plantId, $db);
    $searchQuery .= " AND plant_code = '".$plantCode."'";
}

// Excel file name for download 
$fileName = $plantName . "-data_" . date('Y-m-d') . ".xls";

// Products to track (columns in table)
$fields = [
    'Tonnage','BITUMEN 60/70','BITUMEN PG76','CRMB','CMB','LMB','LEMB','% Bit Usage','Actual Bit Usage',
    'Bit %','Plant Control Bit %','QUARRY DUST','10MM AGGREGATE', '14MM AGGREGATE', 
    '20MM AGGREGATE', '28MM AGGREGATE', '40MM AGGREGATE','OPC','Lime'
];

// Raw materials (rows in table)
$rawMats = [
    'AC14', 'AC28', 'ACW20', 'ACW14', 'ACB20', 'ACB28', 'AC10', 
    'BMB28', '20MM', 'BMB20', 'SFM14', '3/8 WC', 'SMA20', 'DBM40', 
    'FMA20', 'CMA', 'CRGGA', 'AC14 Latex', 'AC14 MR6', 'ACW20 MR6', 
    'SMA20 Adv', 'Dust mix', 'AC14RPF', 'ACW20 PLUS', 'SMA20 PLUS', 
    'BMW14'
];

// Initialize $data array
$data = [];
foreach ($rawMats as $rawMat) {
    $data[$rawMat] = array_fill_keys($fields, 0);
}

// Track total sold weight of each product
$products = [];
$productToTrack = ['BITUMEN 60/70', 'BITUMEN PG76', 'QUARRY DUST', '10MM AGGREGATE', '14MM AGGREGATE', 
                    '20MM AGGREGATE', '28MM AGGREGATE', '40MM AGGREGATE'];
foreach ($productToTrack as $p) {
    $products[$p] = 0;
}

// Fetch product weights
$empQuery = "SELECT product_name, final_weight 
             FROM Weight 
             WHERE is_complete='Y' AND is_cancel<>'Y' AND status='0' 
             AND transaction_status IN ('Sales','Local') $searchQuery 
             ORDER BY tare_weight1_date ASC";
$empRecords = mysqli_query($db, $empQuery);

while($row = mysqli_fetch_assoc($empRecords)) {
    $productName = strtoupper($row['product_name']);
    $weight = isset($row['final_weight']) ? (float)$row['final_weight'] : 0;
    if(isset($products[$productName])) {
        $products[$productName] += $weight;
    }
}

// Initialize totals array
$totals = array_fill_keys($fields, 0);

// Process raw material usage per product
foreach ($products as $productName => $totalWeightMt) {
    if ($totalWeightMt <= 0) continue;

    $productId = searchProductIdByName($productName, $db);
    if ($prod_rawmat_stmt = $db->prepare("SELECT * FROM Product_RawMat WHERE product_id = ? AND plant_id = ? AND status = '0'")){
        $prod_rawmat_stmt->bind_param("ss", $productId, $_GET['plant']);
        $prod_rawmat_stmt->execute();
        $prod_rawmat_result = $prod_rawmat_stmt->get_result();

        while ($prod_rawmat_row = $prod_rawmat_result->fetch_assoc()) {
            $rawMatName = searchRawNameByCode($prod_rawmat_row['raw_mat_code'], $db);

            // Calculate usage of each raw material needed for the product
            $rawMatWeightNeeded = ($totalWeightMt/1000)* (float) $prod_rawmat_row['raw_mat_weight'];
            $rawMatWeightNeededMt = $rawMatWeightNeeded / 1000; // Convert to tonnes
            $data[$rawMatName]['Tonnage'] += $rawMatWeightNeededMt;
            $data[$rawMatName][$productName] += $rawMatWeightNeededMt;

            // Update totals
            $totals['Tonnage'] += $rawMatWeightNeededMt;
            $totals[$productName] += $rawMatWeightNeededMt;
        }

        $prod_rawmat_stmt->close();
    }
}

foreach ($data as $key => $value) {
    $data[$key]['% Bit Usage'] = $value['BITUMEN 60/70'] > 0 ? ($value['BITUMEN 60/70'] / $totals['BITUMEN 60/70']) * 100 : 0;
}

$data['Total'] = $totals;

// Round all numeric values
foreach($data as &$row){
    foreach($row as &$val){
        if(is_numeric($val)) $val = round($val, 2);
    }
}

// Convert associative array to indexed array for DataTables
$tableData = [];
foreach ($data as $productName => $productData) {
    $row = $productData;
    $row['Product'] = $productName;
    $tableData[] = $row;
}

// Get the last record and update it with $totals values
$lastIndex = count($tableData) - 1;
if ($tableData[$lastIndex]['Product'] === 'Total') {
    // Override the Total row with actual totals from $totals array
    foreach ($totals as $field => $value) {
        if (isset($tableData[$lastIndex][$field])) {
            $tableData[$lastIndex][$field] = round($value, 2);
        }
    }
}

// Column names 
$fields = array('Date', 'Product', 'Tonnage', '60/70', 'PG76', 'CRMB', 'CMB', 'LMB', 'LEMB', 
    '% bit usage', 'Actual bit usage', 'bit %', 'plant control bit %', 'Q.Dust', '10mm', '14mm', 
    '20mm', '28mm', '40mm', 'OPC', 'Lime'); 

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";

$currentDate = !empty($_GET['fromDate']) ? $_GET['fromDate'] : date('Y-m-d');
$monthYear = date('F Y', strtotime($currentDate));

foreach ($tableData as $index => $row) {
    $lineData = [];

    $dateToShow = ($index === 0) ? $monthYear : ''; // only show month/year for the first row

    // Prepare line data
    $lineData = array(
        $dateToShow, $row['Product'], $row['Tonnage'], $row['BITUMEN 60/70'], $row['BITUMEN PG76'], $row['CRMB'],
        $row['CMB'], $row['LMB'], $row['LEMB'], $row['% Bit Usage'], $row['Actual Bit Usage'],
        $row['Bit %'], $row['Plant Control Bit %'], $row['QUARRY DUST'],
        $row['10MM AGGREGATE'], $row['14MM AGGREGATE'], $row['20MM AGGREGATE'], $row['28MM AGGREGATE'],
        $row['40MM AGGREGATE'], $row['OPC'], $row['Lime']
    );

    # Added checking to fix duplicated issue
    if (!empty($lineData)) {
        array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
    }
}
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData;
 
exit;
?>