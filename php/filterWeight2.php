<?php
session_start();
## Database configuration
require_once 'db_connect.php';

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

if($searchValue != ''){
  $searchQuery .= " and (lorry_plate_no1 like '%".$searchValue."%')";
}

$allQuery = "select COUNT(*) as allcount FROM Weight WHERE status = '0'";
  
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
  $username = implode("', '", $_SESSION["plant"]);
  $allQuery = "select COUNT(*) as allcount FROM Weight WHERE status = '0' and plant_code IN ('$username') and is_complete = 'N'";
}

$sel = mysqli_query($db, $allQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery = "select count(*) as allcount FROM Weight where status = '0' and is_complete = 'N'".$searchQuery; 
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
  $username = implode("', '", $_SESSION["plant"]);
  $filteredQuery = "select count(*) as allcount FROM Weight where status = '0' and is_complete = 'N' and plant_code IN ('$username')".$searchQuery;
}

$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from Weight where status = '0' and is_complete = 'N'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
  $username = implode("', '", $_SESSION["plant"]);
  $empQuery = "select * from Weight where status = '0' and is_complete = 'N' and plant_code IN ('$username')".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
}

// var_dump($empQuery);
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$salesCount = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $transactionStatus = '';
  if($row['transaction_status'] == 'Sales'){
    $transactionStatus = 'Dispatch';
  }
  else if($row['transaction_status'] == 'Purchase'){
    $transactionStatus = 'Receiving';
  }
  else if($row['transaction_status'] == 'Misc'){
    $transactionStatus = 'Miscellaneous';
  }
  else{
    $transactionStatus = 'Internal Transfer';
  }

  if($row['weight_type'] == 'Container'){
    $weightType = 'Primer Mover';
  }elseif($row['weight_type'] == 'Empty Container'){
    $weightType = 'Primer Mover + Container';
  }else if($row['weight_type'] == 'Different Container'){
    $weightType = 'Primer Mover + Different Container';
  } else{
    $weightType = $row['weight_type'];
  }

  $data[] = array( 
    "no"=>$salesCount,
    "id"=>$row['id'],
    "transaction_id"=>$row['transaction_id'],
    "transaction_status"=>$transactionStatus,
    "weight_type"=>$weightType,
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
    "customer"=>($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local' ? $row['supplier_name'] : $row['customer_name']),
    "product_code"=>($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local' ? $row['raw_mat_code'] : $row['product_code']), 
    "product_name"=>($row['transaction_status'] == 'Purchase' || $row['transaction_status'] == 'Local' ? $row['raw_mat_name'] : $row['product_name']), 
    "container_no"=>$row['container_no'],
    "seal_no"=>$row['seal_no'],
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

  $salesCount++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

?>