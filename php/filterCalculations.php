<?php
## Database configuration
session_start();
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
$searchQuery = "";

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_id = '".$_POST['plant']."'";
}

if($_POST['batchDrum'] != null && $_POST['batchDrum'] != '' && $_POST['batchDrum'] != '-'){
	$searchQuery .= " and batch_drum = '".$_POST['batchDrum']."'";
}

if($_POST['type'] != null && $_POST['type'] != '' && $_POST['type'] != '-'){
	$searchQuery .= " and type = '".$_POST['type']."'";
}

if($searchValue != ''){
  $searchQuery = " and (
    Calculations.type like '%".$searchValue."%' or 
    Calculations.batch_drum like '%".$searchValue."%' or 
    Plant.name like '%".$searchValue."%'
  )";
}

$allQuery = "select count(*) as allcount from Calculations, Plant where Calculations.plant_id = Plant.id and Calculations.deleted = '0'";

$sel = mysqli_query($db, $allQuery); 
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery = "select count(*) as allcount from Calculations, Plant where Calculations.plant_id = Plant.id and Calculations.deleted = '0'".$searchQuery;
$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select Calculations.*, Plant.name as plant from Calculations, Plant where Calculations.plant_id = Plant.id and Calculations.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery); 
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array(
    "id"=>$row['id'],
    "type"=>($row['type'] == 'BITULEVEL') ? 'Bitumen Level' : (($row['type'] == 'BITUSG') ? 'Bitumen SG' : $row['type']),
    "plant"=>$row['plant'],
    "batch_drum"=>$row['batch_drum']
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