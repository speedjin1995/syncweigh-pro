<?php
## Database configuration
session_start();
require_once 'db_connect.php';

## DataTable parameters
$draw = $_POST['draw'];

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
    $searchQuery .= " and transaction_status = '".$_POST['transactionStatus']."'";
}

if ($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
    $searchQuery .= " and plant_code = '".$_POST['plant']."'";
}


$groupQuery = "SELECT 
    CASE 
        WHEN is_complete = 'Y' AND is_cancel = 'N' THEN 'Complete'
        WHEN is_complete = 'N' AND is_cancel = 'N' THEN 'Pending'
        WHEN is_cancel = 'Y' THEN 'Cancel'
    END as status,
    COUNT(CASE WHEN batch_drum = 'Batch' THEN 1 END) as batch_count,
    SUM(CASE WHEN batch_drum = 'Batch' THEN final_weight ELSE 0 END) / 1000 as batch_mt,
    COUNT(CASE WHEN batch_drum = 'Drum' THEN 1 END) as drum_count,
    SUM(CASE WHEN batch_drum = 'Drum' THEN final_weight ELSE 0 END) / 1000 as drum_mt,
    COUNT(*) as total_count,
    SUM(final_weight) / 1000 as total_mt
    FROM Weight 
    WHERE status = '0'".$searchQuery."
    GROUP BY 
        CASE 
            WHEN is_complete = 'Y' AND is_cancel = 'N' THEN 'Complete'
            WHEN is_complete = 'N' AND is_cancel = 'N' THEN 'Pending'
            WHEN is_cancel = 'Y' THEN 'Cancel'
        END
    ORDER BY status";

$groupRecords = mysqli_query($db, $groupQuery);
$data = array();
$statusData = array();

while($row = mysqli_fetch_assoc($groupRecords)) {
    $statusData[$row['status']] = [
        'status' => $row['status'],
        'batch_no' => $row['batch_count'],
        'batch_mt' => number_format($row['batch_mt'], 2),
        'drum_no' => $row['drum_count'], 
        'drum_mt' => number_format($row['drum_mt'], 2),
        'total_no' => $row['total_count'],
        'total_mt' => number_format($row['total_mt'], 2)
    ];
}

// Ensure all statuses are present with 0 values if missing in specific order
$allStatuses = ['Complete', 'Pending', 'Cancel'];
$data = array();
$totalBatchNo = 0;
$totalBatchMt = 0;
$totalDrumNo = 0;
$totalDrumMt = 0;
$totalNo = 0;
$totalMt = 0;

foreach($allStatuses as $status) {
    if (isset($statusData[$status])) {
        $data[] = $statusData[$status];
        $totalBatchNo += $statusData[$status]['batch_no'];
        $totalBatchMt += floatval(str_replace(',', '', $statusData[$status]['batch_mt']));
        $totalDrumNo += $statusData[$status]['drum_no'];
        $totalDrumMt += floatval(str_replace(',', '', $statusData[$status]['drum_mt']));
        $totalNo += $statusData[$status]['total_no'];
        $totalMt += floatval(str_replace(',', '', $statusData[$status]['total_mt']));
    } else {
        $data[] = [
            'status' => $status,
            'batch_no' => 0,
            'batch_mt' => '0.00',
            'drum_no' => 0,
            'drum_mt' => '0.00',
            'total_no' => 0,
            'total_mt' => '0.00'
        ];
    }
}

// Add total row
$data[] = [
    'status' => 'Total',
    'batch_no' => $totalBatchNo,
    'batch_mt' => number_format($totalBatchMt, 2),
    'drum_no' => $totalDrumNo,
    'drum_mt' => number_format($totalDrumMt, 2),
    'total_no' => $totalNo,
    'total_mt' => number_format($totalMt, 2)
];

## DataTable Response
$response = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => 4,
    "recordsFiltered" => 4,
    "data" => $data
];

echo json_encode($response);
?>