<?php
## Database configuration
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Search 
$searchQuery = "";

if(!empty($_POST['fromDate'])){
    $fromDateTime = (new DateTime($_POST['fromDate']))->format("Y-m-d 00:00:00");
    $searchQuery .= " AND tare_weight1_date >= '".$fromDateTime."'";
}

if(!empty($_POST['toDate'])){
    $toDateTime = (new DateTime($_POST['toDate']))->format("Y-m-d 23:59:59");
    $searchQuery .= " AND tare_weight1_date <= '".$toDateTime."'";
}

$plantId = !empty($_POST['plant']) ? $_POST['plant'] : null;
if($plantId){
    $plantCode = searchPlantCodeById($plantId, $db);
    $searchQuery .= " AND plant_code = '".$plantCode."'";
}

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
        $prod_rawmat_stmt->bind_param("ss", $productId, $_POST['plant']);
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

// Return response
$response = [
    // "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    // "recordsTotal" => count($tableData),
    // "recordsFiltered" => count($tableData),
    "data" => $tableData
];
echo json_encode($response);
?>
