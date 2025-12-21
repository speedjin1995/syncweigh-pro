<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    $fromDate = DateTime::createFromFormat('d-m-Y H:i', $_POST['fromDate']);
    $fromDateTime = $fromDate->format('Y-m-d H:i:00');
    $searchQuery = " AND tare_weight1_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
    $toDate = DateTime::createFromFormat('d-m-Y H:i', $_POST['toDate']);
    $toDateTime = $toDate->format('Y-m-d H:i:59');
    $searchQuery .= " AND tare_weight1_date <= '".$toDateTime."'";
}

if ($_POST['transactionStatus'] != null && $_POST['transactionStatus'] != '' && $_POST['transactionStatus'] != '-'){
    $searchQuery .= " AND transaction_status = '".$_POST['transactionStatus']."'";
}

if ($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
    $searchQuery .= " AND plant_code = '".$_POST['plant']."'";
}

// Split query based on transaction status
if($_POST['transactionStatus'] == 'Purchase') {
    $query = "SELECT 
        batch_drum,
        raw_mat_name as product_name,
        lorry_plate_no1 as vehicle_no,
        nett_weight1,
        tare_weight1_date,
        is_complete,
        is_cancel
        FROM Weight 
        WHERE status = '0' AND raw_mat_name IS NOT NULL".$searchQuery."
        ORDER BY batch_drum, raw_mat_name, tare_weight1_date";
} else {
    $query = "SELECT 
        batch_drum,
        product_name,
        lorry_plate_no1 as vehicle_no,
        nett_weight1,
        tare_weight1_date,
        is_complete,
        is_cancel
        FROM Weight 
        WHERE status = '0' AND product_name IS NOT NULL".$searchQuery."
        ORDER BY batch_drum, product_name, tare_weight1_date";
}

$records = mysqli_query($db, $query);

// Generate text report in MT format
$plantName = searchPlantNameByCode($_POST['plant'], $db);
if (!empty($plantName)) {
    $plantName = strtoupper($plantName);
} else {
    $plantName = "ALL PLANTS";
}

$reportTitle = $plantName . ' ' . strtoupper($_POST['transactionStatus'])." REPORT";
$text = $reportTitle."\n";
$dateRange = '';
if($_POST['fromDate'] != null && $_POST['fromDate'] != '' && $_POST['toDate'] != null && $_POST['toDate'] != '') {
    $dateRange = $_POST['fromDate'].' -> '.$_POST['toDate'];
} else {
    $dateRange = date('d/m/Y');
}
$text .= $dateRange."\n\n";

$currentBatchDrum = '';
$currentProduct = '';
$productData = [];
$vehicleCounter = 1;

while($row = mysqli_fetch_assoc($records)) {
    $key = $row['batch_drum'].'_'.$row['product_name'];
    
    if (!isset($productData[$key])) {
        $productData[$key] = [
            'batch_drum' => $row['batch_drum'],
            'product_name' => $row['product_name'],
            'completed' => [],
            'pending' => [],
            'cancelled' => []
        ];
    }
    
    $weight = floatval($row['nett_weight1']/1000);
    $time = date('Hi A', strtotime($row['tare_weight1_date']));
    $vehicleInfo = ['vehicle' => $row['vehicle_no'], 'weight' => $weight, 'time' => $time];
    
    if ($row['is_complete'] == 'Y' && $row['is_cancel'] == 'N') {
        $productData[$key]['completed'][] = $vehicleInfo;
    } elseif ($row['is_complete'] == 'N' && $row['is_cancel'] == 'N') {
        $productData[$key]['pending'][] = $vehicleInfo;
    } elseif ($row['is_cancel'] == 'Y') {
        $productData[$key]['cancelled'][] = $vehicleInfo;
    }
}

foreach($productData as $data) {
    if($currentBatchDrum != $data['batch_drum']) {
        if($currentBatchDrum != '') $text .= "\n";
        $text .= $data['batch_drum'].":\n";
        $currentBatchDrum = $data['batch_drum'];
    }
    
    $text .= $data['product_name']."\n";
    
    // Completed
    $completedTotal = array_sum(array_column($data['completed'], 'weight'));
    $text .= "Completed: ".number_format($completedTotal, 2)." MT\n";
    $counter = 1;
    foreach($data['completed'] as $vehicle) {
        $text .= $counter.". ".$vehicle['vehicle'].' '.number_format($vehicle['weight'], 2).' MT '.$vehicle['time']."\n";
        $counter++;
    }
    
    // Pending
    $pendingTotal = array_sum(array_column($data['pending'], 'weight'));
    $text .= "\nPending: ".number_format($pendingTotal, 2)." MT\n";
    $counter = 1;
    foreach($data['pending'] as $vehicle) {
        $text .= $counter.". ".$vehicle['vehicle'].' '.number_format($vehicle['weight'], 2).' MT '.$vehicle['time']."\n";
        $counter++;
    }
    
    // Cancelled
    $cancelledTotal = array_sum(array_column($data['cancelled'], 'weight'));
    $text .= "\nCancelled: ".number_format($cancelledTotal, 2)." MT\n";
    $counter = 1;
    foreach($data['cancelled'] as $vehicle) {
        $text .= $counter.". ".$vehicle['vehicle'].' '.number_format($vehicle['weight'], 2).' MT '.$vehicle['time']."\n";
        $counter++;
    }
    
    $text .= "\n";
}

echo json_encode([
    "status" => "success",
    "message" => $text
]);
?>