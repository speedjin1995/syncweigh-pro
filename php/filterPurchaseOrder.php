<?php
## Database configuration
require_once 'db_connect.php';
session_start();

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = "";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and order_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and order_date <= '".$toDateTime."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and status = '".$_POST['status']."'";
}

if($_POST['company'] != null && $_POST['company'] != '' && $_POST['company'] != '-'){
	$searchQuery .= " and company_id = '".$_POST['company']."'";
}

if($_POST['site'] != null && $_POST['site'] != '' && $_POST['site'] != '-'){
	$searchQuery .= " and site_id = '".$_POST['site']."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_id = '".$_POST['plant']."'";
}

if($_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
	$searchQuery .= " and supplier_id = '".$_POST['supplier']."'";
}

if($_POST['rawMaterial'] != null && $_POST['rawMaterial'] != '' && $_POST['rawMaterial'] != '-'){
	$searchQuery .= " and raw_mat_id = '".$_POST['rawMaterial']."'";
}

if($_POST['poNo'] != null && $_POST['poNo'] != '' && $_POST['poNo'] != '-'){
	$searchQuery .= " and po_no = '".$_POST['poNo']."'";
}

if($searchValue != ''){
  $searchQuery .= " and (
    sup.supplier_code like '%".$searchValue."%' or 
    sup.name like '%".$searchValue."%' or 
    pl.plant_code like '%".$searchValue."%' or 
    pl.name like '%".$searchValue."%' or 
    rw.raw_mat_code like '%".$searchValue."%' or 
    rw.name like '%".$searchValue."%' or 
    po.order_no like '%".$searchValue."%' or 
    po.po_no like '%".$searchValue."%' or
    po.order_date like '%".$searchValue."%' or 
    po.exquarry_or_delivered like '%".$searchValue."%' or 
    po.modified_date like '%".$searchValue."%'
  )";
}

$allQuery = "select count(*) as allcount from Purchase_Order where deleted = '0'";

$sel = mysqli_query($db, $allQuery); 
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery = "select count(*) as allcount from Purchase_Order po
                  LEFT JOIN Company company ON po.company_id = company.id 
                  LEFT JOIN Supplier sup ON po.supplier_id = sup.id 
                  LEFT JOIN Site s ON po.site_id = s.id 
                  LEFT JOIN Agents a ON po.agent_id = a.id 
                  LEFT JOIN Destination d ON po.destination_id = d.id 
                  LEFT JOIN Raw_Mat rw ON po.raw_mat_id = rw.id
                  LEFT JOIN Plant pl ON po.plant_id = pl.id
                  LEFT JOIN Transporter t ON po.transporter_id = t.id
                  where po.deleted = '0'".$searchQuery;
$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select po.*, company.company_code as companycode, company.name as companyname, sup.supplier_code as supcode, sup.name AS supname, rw.raw_mat_code as rwcode, rw.name as rwname, pl.plant_code as plantcode, pl.name as plantname from Purchase_Order po
            LEFT JOIN Company company ON po.company_id = company.id 
            LEFT JOIN Supplier sup ON po.supplier_id = sup.id 
            LEFT JOIN Site s ON po.site_id = s.id 
            LEFT JOIN Agents a ON po.agent_id = a.id 
            LEFT JOIN Destination d ON po.destination_id = d.id 
            LEFT JOIN Raw_Mat rw ON po.raw_mat_id = rw.id
            LEFT JOIN Plant pl ON po.plant_id = pl.id
            LEFT JOIN Transporter t ON po.transporter_id = t.id
            where po.deleted = '0'".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery); 
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array(
    "id"=>$row['id'],
    "company_code"=>$row['companycode'],
    "company_name"=>$row['companyname'],
    "supplier_code"=>$row['supcode'],
    "supplier_name"=>$row['supname'],
    "plant_code"=>$row['plantcode'],
    "plant_name"=>$row['plantname'],
    "raw_mat_code"=>$row['rwcode'],
    "raw_mat_name"=>$row['rwname'],
    "order_no"=>$row['order_no'],
    "po_no"=>$row['po_no'],
    "order_date" => !empty($row["order_date"]) ? DateTime::createFromFormat('Y-m-d H:i:s', $row["order_date"])->format('d-m-Y') : '',
    "exquarry_or_delivered"=>$row['exquarry_or_delivered'],
    "balance"=>$row['balance'],
    "status"=>$row['status'],
    "modified_date" => !empty($row["modified_date"]) ? DateTime::createFromFormat('Y-m-d H:i:s', $row["modified_date"])->format('d-m-Y') : ''
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
);

echo json_encode($response);

?>