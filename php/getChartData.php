<?php
## Database configuration
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

if ($_POST['status'] != null && $_POST['status'] != ''){
    if($_POST['status'] == 'Complete'){
        $searchQuery .= " AND is_complete = 'Y' AND is_cancel = 'N' ";
    } elseif($_POST['status'] == 'Pending'){
        $searchQuery .= " AND is_complete = 'N' AND is_cancel = 'N' ";
    } elseif($_POST['status'] == 'Cancel'){
        $searchQuery .= " AND is_cancel = 'Y' ";
    }
}

if ($_POST['type'] != null && $_POST['type'] != ''){
    $searchQuery .= " AND batch_drum = '".$_POST['type']."' ";
}

// Product Chart Data
if($_POST['transactionStatus'] == 'Purchase') {
    $productQuery = "SELECT 
        raw_mat_name as name,
        raw_mat_code as code,
        SUM(final_weight) / 1000 as total_weight
        FROM Weight 
        WHERE status = '0'".$searchQuery."
        AND raw_mat_name IS NOT NULL
        GROUP BY raw_mat_name, raw_mat_code
        ORDER BY total_weight DESC";
} else {
    $productQuery = "SELECT 
        product_name as name,
        product_code as code,
        SUM(final_weight) / 1000 as total_weight
        FROM Weight 
        WHERE status = '0'".$searchQuery."
        AND product_name IS NOT NULL
        GROUP BY product_name, product_code
        ORDER BY total_weight DESC";
}

$productRecords = mysqli_query($db, $productQuery);
$productLabels = array();
$productValues = array();
$productCodes = array();

while($row = mysqli_fetch_assoc($productRecords)) {
    $productLabels[] = $row['name'];
    $productValues[] = floatval($row['total_weight']);
    $productCodes[] = $row['code'];
}

// Customer Chart Data  
if($_POST['transactionStatus'] == 'Purchase') {
    $customerQuery = "SELECT 
        supplier_name as name,
        supplier_code as code,
        SUM(final_weight) / 1000 as total_weight
        FROM Weight 
        WHERE status = '0'".$searchQuery."
        AND supplier_name IS NOT NULL
        GROUP BY supplier_name, supplier_code
        ORDER BY total_weight DESC";
} else {
    $customerQuery = "SELECT 
        customer_name as name,
        customer_code as code,
        SUM(final_weight) / 1000 as total_weight
        FROM Weight 
        WHERE status = '0'".$searchQuery."
        AND customer_name IS NOT NULL
        GROUP BY customer_name, customer_code
        ORDER BY total_weight DESC";
}

$customerRecords = mysqli_query($db, $customerQuery);
$customerLabels = array();
$customerValues = array();

while($row = mysqli_fetch_assoc($customerRecords)) {
    $customerLabels[] = $row['name'];
    $customerValues[] = floatval($row['total_weight']);
    $customerCodes[] = $row['code'];
} 

## Response
$response = [
    "status" => "success",
    "productData" => [
        "labels" => $productLabels,
        "values" => $productValues,
        "codes" => $productCodes,
        "status" => $_POST['status'],
        "type" => $_POST['type']
    ],
    "customerData" => [
        "labels" => $customerLabels,
        "values" => $customerValues,
        "codes" => $customerCodes,
        "status" => $_POST['status'],
        "type" => $_POST['type']
    ]
];

echo json_encode($response);
?>