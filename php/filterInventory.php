<?php
## Database configuration
session_start();
require_once 'db_connect.php';
require_once 'requires/permissions.php';

function formatInventoryNumber($value) {
  $formatted = number_format((float)$value, 3, '.', '');
  $formatted = rtrim(rtrim($formatted, '0'), '.');
  return $formatted === '' ? '0' : $formatted;
}

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = strtolower($_POST['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';
$searchValue = mysqli_real_escape_string($db, $_POST['search']['value']); // Search value

$plantQuery = " ";
$searchQuery = " ";

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
  $plant = mysqli_real_escape_string($db, $_POST['plant']);
	$plantQuery .= " and Inventory.plant_code = '".$plant."'";
} else {
  // Restrict to own plant only if no permission
  if (!hasModulePermission('Stock Management', 'Inventory', ['view_all_plants'])){
    $username = implode("', '", array_map(function($plantCode) use ($db) {
      return mysqli_real_escape_string($db, $plantCode);
    }, $_SESSION["plant"]));
    $plantQuery .= " and Inventory.plant_code IN ('$username')";
  }
}

if($searchValue != ''){
  $searchQuery .= " and (Raw_Mat.raw_mat_code like '%".$searchValue."%' or Raw_Mat.name like '%".$searchValue."%')";
}

$fromClause = "
  FROM Inventory
  INNER JOIN Raw_Mat ON Inventory.raw_mat_id = Raw_Mat.id
  LEFT JOIN (
    SELECT
      inventory_id,
      SUM(weight_adjustment) AS total_weight_adjustment,
      SUM(drum_adjustment) AS total_drum_adjustment
    FROM inventory_adjustment
    WHERE status = '0'
    GROUP BY inventory_id
  ) Inventory_Adjustment_Sum ON Inventory_Adjustment_Sum.inventory_id = Inventory.id
  WHERE Inventory.status = '0'
";

## Total number of records without filtering
$allQuery = "select count(*) as allcount ".$fromClause.$plantQuery;
$sel = mysqli_query($db, $allQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery = "select count(*) as allcount ".$fromClause.$plantQuery.$searchQuery;
$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

$orderColumns = array(
  'no' => 'Inventory.id',
  'raw_mat_code' => 'Raw_Mat.raw_mat_code',
  'name' => 'Raw_Mat.name',
  'raw_mat_weight' => 'adjusted_raw_mat_weight',
  'raw_mat_count' => 'adjusted_raw_mat_count',
  'id' => 'Inventory.id'
);

$orderBy = isset($orderColumns[$columnName]) ? $orderColumns[$columnName] : 'Raw_Mat.raw_mat_code';

## Fetch records
$empQuery = "
  SELECT
    Inventory.*,
    Raw_Mat.raw_mat_code,
    Raw_Mat.name,
    (
      CAST(Inventory.raw_mat_weight AS DECIMAL(18,3)) +
      COALESCE(Inventory_Adjustment_Sum.total_weight_adjustment, 0)
    ) AS adjusted_raw_mat_weight,
    (
      CAST(Inventory.raw_mat_count AS DECIMAL(18,3)) +
      COALESCE(Inventory_Adjustment_Sum.total_drum_adjustment, 0)
    ) AS adjusted_raw_mat_count
  ".$fromClause.$plantQuery.$searchQuery."
  order by ".$orderBy." ".$columnSortOrder."
  limit ".intval($row).",".intval($rowperpage);

$empRecords = mysqli_query($db, $empQuery);
$data = array();
$salesCount = intval($row) + 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array(
    "id"=>$row['id'],
    "no"=>$salesCount,
    "raw_mat_code"=>$row['raw_mat_code'],
    "name"=>$row['name'],
    "raw_mat_weight"=>formatInventoryNumber($row['adjusted_raw_mat_weight']),
    "raw_mat_count"=>formatInventoryNumber($row['adjusted_raw_mat_count'])
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
