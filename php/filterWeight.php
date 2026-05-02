<?php
## Database configuration
session_start();
require_once 'db_connect.php';
require_once 'requires/permissions.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
$searchQuery2 = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and transaction_date >= '".$fromDateTime."'";
  $searchQuery2 = " and transaction_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and transaction_date <= '".$toDateTime."'";
  $searchQuery2 .= " and transaction_date <= '".$toDateTime."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and transaction_status = '".$_POST['status']."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer_code = '".$_POST['customer']."'";
}

if($_POST['vehicle'] != null && $_POST['vehicle'] != '' && $_POST['vehicle'] != '-'){
	$searchQuery .= " and lorry_plate_no1 like '%".$_POST['vehicle']."%'";
}

if($_POST['invoice'] != null && $_POST['invoice'] != '' && $_POST['invoice'] != '-'){
	$searchQuery .= " and weight_type = '".$_POST['invoice']."'";
}

if($_POST['batch'] != null && $_POST['batch'] != '' && $_POST['batch'] != '-'){
	$searchQuery .= " and is_complete = '".$_POST['batch']."'";
}

if($_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
	$searchQuery .= " and product_code = '".$_POST['product']."'";
}

if($_POST['rawMaterial'] != null && $_POST['rawMaterial'] != '' && $_POST['rawMaterial'] != '-'){
	$searchQuery .= " and raw_mat_code = '".$_POST['rawMaterial']."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_POST['plant']."'";
}

if($_POST['soNo'] != null && $_POST['soNo'] != '' && $_POST['soNo'] != '-'){
  $searchQuery .= " and purchase_order = '".mysqli_real_escape_string($db, $_POST['soNo'])."'";
}

if($_POST['poNo'] != null && $_POST['poNo'] != '' && $_POST['poNo'] != '-'){
  $searchQuery .= " and purchase_order = '".mysqli_real_escape_string($db, $_POST['poNo'])."'";
}

if($_POST['batchDrum'] != null && $_POST['batchDrum'] != '' && $_POST['batchDrum'] != '-'){
	$searchQuery .= " and batch_drum = '".$_POST['batchDrum']."'";
}
// Restrict normal user to only see non-local transactions
if ($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'MANAGER') {
  $searchQuery .= " and transaction_status != 'Local'";
}

if($searchValue != ''){
  $searchQuery = " and (transaction_id like '%".$searchValue."%' or lorry_plate_no1 like '%".$searchValue."%')";
}

$salesPendingCount = 0;
$salesCompleteCount = 0;
$salesCancelCount = 0;
$purchasePendingCount = 0;
$purchaseCompleteCount = 0;
$purchaseCancelCount = 0;
$localPendingCount = 0;
$localCompleteCount = 0;
$localCancelCount = 0;
$miscPendingCount = 0;
$miscCompleteCount = 0;
$miscCancelCount = 0;

## Total number of records without filtering
$allQuery = "select count(*) as allcount from Weight where status = '0' and is_cancel = 'N'";
if (!hasPermission('Weighing', ['view_all_plants'])){
  $username = implode("', '", $_SESSION["plant"]);
  $allQuery = "select count(*) as allcount from Weight where status = '0' and is_cancel = 'N' and plant_code IN ('$username')";
}
$sel = mysqli_query($db, $allQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery = "select count(*) as allcount from Weight where status = '0' and is_cancel = 'N'".$searchQuery;
$filteredQuery2 = "select * from Weight where status = '0' and is_cancel = 'N'".$searchQuery2;
if (!hasPermission('Weighing', ['view_all_plants'])){
  $username = implode("', '", $_SESSION["plant"]);
  $filteredQuery = "select count(*) as allcount from Weight where status = '0' and is_cancel = 'N' and plant_code IN ('$username')".$searchQuery;
  $filteredQuery2 = "select * from Weight where status = '0' and is_cancel = 'N' and plant_code IN ('$username')".$searchQuery2;
}

$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

$salesPendingCount = 0;
$salesCompleteCount = 0;
$salesCancelCount = 0;
$purchasePendingCount = 0;
$purchaseCompleteCount = 0;
$purchaseCancelCount = 0;
$returnPendingCount = 0;
$returnCompleteCount = 0;
$returnCancelCount = 0;

$countQuery = mysqli_query($db, $filteredQuery2);
while($countRow = mysqli_fetch_assoc($countQuery)) {
  if ($countRow['transaction_status'] == 'Sales') {
    if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
      $salesPendingCount++;
    } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
      $salesCompleteCount++;
    } elseif ($countRow['is_cancel'] == 'Y') {
      $salesCancelCount++;
    }
  } elseif ($countRow['transaction_status'] == 'Purchase') {
    if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
      $purchasePendingCount++;
    } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
      $purchaseCompleteCount++;
    } elseif ($countRow['is_cancel'] == 'Y') {
      $purchaseCancelCount++;
    }
  } elseif ($countRow['transaction_status'] == 'Return') {
    if ($countRow['is_complete'] == 'N' && $countRow['is_cancel'] == 'N') {
      $returnPendingCount++;
    } elseif ($countRow['is_complete'] == 'Y' && $countRow['is_cancel'] == 'N') {
      $returnCompleteCount++;
    } elseif ($countRow['is_cancel'] == 'Y') {
      $returnCancelCount++;
    }
  }
}

## Fetch records
if ($columnName == 'product') {
  $columnName = "CASE WHEN transaction_status IN ('Sales', 'Local', 'WIP') THEN CONCAT(product_code, ' - ', product_name) ELSE CONCAT(raw_mat_code, ' - ', raw_mat_name) END";
} elseif ($columnName == 'customer') {
  $columnName = "CASE WHEN transaction_status IN ('Sales', 'Local', 'WIP') THEN customer_name ELSE supplier_name END";
}

$empQuery = "select * from Weight where status = '0' and is_cancel = 'N'".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
if (!hasPermission('Weighing', ['view_all_plants'])){
  $username = implode("', '", $_SESSION["plant"]);
  $empQuery = "select * from Weight where status = '0' and is_cancel = 'N' and plant_code IN ('$username')".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
}

$empRecords = mysqli_query($db, $empQuery);
$data = array();
while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "id"=>$row['id'],
    "transaction_id"=>$row['transaction_id'],
    "transaction_status"=>$row['transaction_status'],
    "weight_type"=>$row['weight_type'],
    "transaction_date"=>$row['transaction_date'],
    "lorry_plate_no1"=>$row['lorry_plate_no1'],
    "lorry_plate_no2"=>$row['lorry_plate_no2'],
    "supplier_weight"=>$row['supplier_weight'],
    "customer_code"=>$row['customer_code'],
    "customer_name"=>$row['customer_name'],
    "plant_code"=>$row['plant_code'],
    "plant_name"=>$row['plant_name'],
    "agent_code"=>$row['agent_code'],
    "agent_name"=>$row['agent_name'],
    "supplier_code"=>$row['supplier_code'],
    "supplier_name"=>$row['supplier_name'],
    "customer"=>($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' || $row['transaction_status'] == 'WIP' ? $row['customer_name'] : $row['supplier_name']),
    "product"=>($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' || $row['transaction_status'] == 'WIP' ? $row['product_code']. ' - ' .$row['product_name'] : $row['raw_mat_code']. ' - ' .$row['raw_mat_name']),
    "product_code"=>($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' || $row['transaction_status'] == 'WIP' ? $row['product_code'] : $row['raw_mat_code']),
    "product_name"=>($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Local' || $row['transaction_status'] == 'WIP' ? $row['product_name'] : $row['raw_mat_name']),
    "container_no"=>$row['container_no'],
    "invoice_no"=>$row['invoice_no'],
    "purchase_order"=>$row['purchase_order'],
    "delivery_no"=>$row['delivery_no'],
    "transporter_code"=>$row['transporter_code'],
    "transporter"=>$row['transporter'],
    "destination_code"=>$row['destination_code'],
    "destination"=>$row['destination'],
    "remarks"=>$row['remarks'],
    "gross_weight1"=>$row['gross_weight1'],
    "gross_weight1_date"=>$row['gross_weight1_date'],
    "tare_weight1"=>$row['tare_weight1'],
    "tare_weight1_date"=>$row['tare_weight1_date'],
    "nett_weight1"=>$row['nett_weight1'],
    "gross_weight2"=>$row['gross_weight2'],
    "gross_weight2_date"=>$row['gross_weight2_date'],
    "tare_weight2"=>$row['tare_weight2'],
    "tare_weight2_date"=>$row['tare_weight2_date'],
    "nett_weight2"=>$row['nett_weight2'],
    "final_weight"=>$row['final_weight'],
    "weight_different"=>$row['weight_different'],
    "is_complete"=>$row['is_complete'],
    "is_cancel"=>$row['is_cancel'],
    "is_approved"=>$row['is_approved'],
    "approved_by"=>$row['approved_by'],
    "approved_reason"=>$row['approved_reason'],
    "manual_weight"=>$row['manual_weight'],
    "indicator_id"=>$row['indicator_id'],
    "weighbridge_id"=>$row['weighbridge_id'],
    "created_date"=>$row['created_date'],
    "created_by"=>$row['created_by'],
    "modified_date"=>$row['modified_date'],
    "modified_by"=>$row['modified_by'],
    "indicator_id_2"=>$row['indicator_id_2'],
    "product_description"=>$row['product_description']
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "salesTotalPending" => $salesPendingCount,
  "salesTotalComplete" => $salesCompleteCount,
  "salesTotalCancel" => $salesCancelCount,
  "purchaseTotalPending" => $purchasePendingCount,
  "purchaseTotalComplete" => $purchaseCompleteCount,
  "purchaseTotalCancel" => $purchaseCancelCount,
  "returnTotalPending" => $returnPendingCount,
  "returnTotalComplete" => $returnCompleteCount,
  "returnTotalCancel" => $returnCancelCount
);

echo json_encode($response);

?>