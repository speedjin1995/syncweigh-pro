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

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer_id = '".$_POST['customer']."'";
}

if($_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
	$searchQuery .= " and product_id = '".$_POST['product']."'";
}

if($_POST['soNo'] != null && $_POST['soNo'] != '' && $_POST['soNo'] != '-'){
	$searchQuery .= " and order_no = '".$_POST['soNo']."'";
}

if($searchValue != ''){
  $searchQuery .= " and (
    c.customer_code like '%".$searchValue."%' or 
    c.name like '%".$searchValue."%' or 
    pl.plant_code like '%".$searchValue."%' or 
    pl.name like '%".$searchValue."%' or 
    p.product_code like '%".$searchValue."%' or 
    p.name like '%".$searchValue."%' or 
    so.order_no like '%".$searchValue."%' or 
    so.so_no like '%".$searchValue."%' or
    so.order_date like '%".$searchValue."%' or 
    so.exquarry_or_delivered like '%".$searchValue."%' or 
    so.modified_date like '%".$searchValue."%'
  )";
}

$allQuery = "select count(*) as allcount from Sales_Order where deleted = '0'";

$sel = mysqli_query($db, $allQuery); 
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery ="select count(*) as allcount FROM Sales_Order so
                  LEFT JOIN Company company ON so.company_id = company.id 
                  LEFT JOIN Customer c ON so.customer_id = c.id 
                  LEFT JOIN Site s ON so.site_id = s.id 
                  LEFT JOIN Agents a ON so.agent_id = a.id 
                  LEFT JOIN Destination d ON so.destination_id = d.id 
                  LEFT JOIN Product p ON so.product_id = p.id
                  LEFT JOIN Plant pl ON so.plant_id = pl.id
                  LEFT JOIN Transporter t ON so.transporter_id = t.id
                  WHERE so.deleted = '0'".$searchQuery;
$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery ="select so.*, company.company_code as companycode, company.name as companyname, c.customer_code as customercode, c.name AS customername, p.product_code as productcode, p.name as productname, pl.plant_code as plantcode, pl.name as plantname FROM Sales_Order so
            LEFT JOIN Company company ON so.company_id = company.id 
            LEFT JOIN Customer c ON so.customer_id = c.id 
            LEFT JOIN Site s ON so.site_id = s.id 
            LEFT JOIN Agents a ON so.agent_id = a.id 
            LEFT JOIN Destination d ON so.destination_id = d.id 
            LEFT JOIN Product p ON so.product_id = p.id
            LEFT JOIN Plant pl ON so.plant_id = pl.id
            LEFT JOIN Transporter t ON so.transporter_id = t.id
            WHERE so.deleted = '0'".$searchQuery."order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery); 
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array(
    "id"=>$row['id'],
    "company_code"=>$row['companycode'],
    "company_name"=>$row['companyname'],
    "customer_code"=>$row['customercode'],
    "customer_name"=>$row['customername'],
    "plant_code"=>$row['plantcode'],
    "plant_name"=>$row['plantname'],
    "product_code"=>$row['productcode'],
    "product_name"=>$row['productname'],
    "order_no"=>$row['order_no'],
    "so_no"=>$row['so_no'],
    "order_date" => !empty($row["order_date"]) ? DateTime::createFromFormat('Y-m-d H:i:s', $row["order_date"])->format('d-m-Y') : '',
    "exquarry_or_delivered"=>$row['exquarry_or_delivered'],
    "order_quantity"=>$row['order_quantity'],
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