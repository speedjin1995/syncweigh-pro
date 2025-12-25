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

// Get consolidated status counts by batch_drum
$query = "SELECT 
    batch_drum,
    SUM(CASE WHEN is_complete = 'Y' AND is_cancel = 'N' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN is_complete = 'N' AND is_cancel = 'N' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN is_cancel = 'Y' THEN 1 ELSE 0 END) as cancelled
    FROM Weight 
    WHERE status = '0'".$searchQuery."
    GROUP BY batch_drum
    ORDER BY batch_drum";

$records = mysqli_query($db, $query);

// Generate text report
$text = "Product Status Report\n";
$text .= "Transaction: ".$_POST['transactionStatus']."\n";
$text .= "Plant: ".$_POST['plant']."\n";

while($row = mysqli_fetch_assoc($records)) {
    $text .= $row['batch_drum']."\n";
    $text .= "Completed: ".$row['completed']."\n";
    $text .= "Pending: ".$row['pending']."\n";
    $text .= "Cancelled: ".$row['cancelled']."\n\n";
}

echo json_encode([
    "status" => "success",
    "message" => $text
]);
?>