<?php
session_start();
require_once 'db_connect.php';

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
        SUM(CASE WHEN is_complete = 'Y' AND is_cancel = 'N' THEN COALESCE(nett_weight1, 0) ELSE 0 END) as completed_weight,
        SUM(CASE WHEN is_complete = 'N' AND is_cancel = 'N' THEN COALESCE(nett_weight1, 0) ELSE 0 END) as pending_weight,
        SUM(CASE WHEN is_cancel = 'Y' THEN COALESCE(nett_weight1, 0) ELSE 0 END) as cancelled_weight
        FROM Weight 
        WHERE status = '0' AND raw_mat_name IS NOT NULL".$searchQuery."
        GROUP BY batch_drum, raw_mat_name
        ORDER BY batch_drum, raw_mat_name";
} else {
    $query = "SELECT 
        batch_drum,
        product_name,
        SUM(CASE WHEN is_complete = 'Y' AND is_cancel = 'N' THEN COALESCE(nett_weight1, 0) ELSE 0 END) as completed_weight,
        SUM(CASE WHEN is_complete = 'N' AND is_cancel = 'N' THEN COALESCE(nett_weight1, 0) ELSE 0 END) as pending_weight,
        SUM(CASE WHEN is_cancel = 'Y' THEN COALESCE(nett_weight1, 0) ELSE 0 END) as cancelled_weight
        FROM Weight 
        WHERE status = '0' AND product_name IS NOT NULL".$searchQuery."
        GROUP BY batch_drum, product_name
        ORDER BY batch_drum, product_name";
}

$records = mysqli_query($db, $query);

// Generate text report in MT format
$reportTitle = strtoupper($_POST['transactionStatus'])." REPORT";
$text = $reportTitle."\n";
$dateRange = '';
if($_POST['fromDate'] != null && $_POST['fromDate'] != '' && $_POST['toDate'] != null && $_POST['toDate'] != '') {
    $dateRange = $_POST['fromDate'].' -> '.$_POST['toDate'];
} else {
    $dateRange = date('d/m/Y');
}
$text .= $dateRange."\n\n";

$currentBatchDrum = '';
while($row = mysqli_fetch_assoc($records)) {
    if($currentBatchDrum != $row['batch_drum']) {
        if($currentBatchDrum != '') $text .= "\n";
        $text .= $row['batch_drum'].":\n";
        $currentBatchDrum = $row['batch_drum'];
    }
    
    $completed = number_format($row['completed_weight']/1000, 2);
    $pending = number_format($row['pending_weight']/1000, 2);
    $cancelled = number_format($row['cancelled_weight']/1000, 2);
    
    $text .= $row['product_name']."\n";
    $text .= "Completed: ".$completed." MT\n";
    $text .= "Pending: ".$pending." MT\n";
    $text .= "Cancelled: ".$cancelled." MT\n\n";
}

echo json_encode([
    "status" => "success",
    "message" => $text
]);
?>