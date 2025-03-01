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
$searchQuery = " ";
if($searchValue != ''){
   $searchQuery = " and (Users.username like '%".$searchValue."%' or 
        Users.useremail like '%".$searchValue."%' or
        roles.role_name like'%".$searchValue."%' ) ";
}

## Total number of records without filtering
$allQuery = "select count(*) as allcount from Users where status = '0'";
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
  $username = $_SESSION["plant"];
  $allQuery = "select count(*) as allcount from Users where status = '0' and plant_id='$username'";
}

$sel = mysqli_query($db, $allQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filteredQuery = "select count(*) as allcount from Users, roles WHERE Users.role = roles.role_code AND Users.status = '0'".$searchQuery;
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
  $username = $_SESSION["plant"];
  $filteredQuery = "select count(*) as allcount from Users, roles WHERE Users.role = roles.role_code AND Users.status = '0' AND Users.plant_id='$username'".$searchQuery;
}

$sel = mysqli_query($db, $filteredQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select Users.id, Users.employee_code, Users.username, Users.useremail, roles.role_name, Plant.name from Users, roles, Plant WHERE 
Users.role = roles.role_code AND Users.status = '0' AND Users.role <> 'SADMIN' AND Users.plant_id = Plant.id".$searchQuery." 
order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
  $username = $_SESSION["plant"];
  $empQuery = "select Users.name AS empname, Users.id, Users.employee_code, Users.username, Users.useremail, roles.role_name, Plant.name from Users, roles, Plant WHERE 
  Users.role = roles.role_code AND Users.status = '0' AND Users.role <> 'SADMIN' AND Users.plant_id = Plant.id AND Users.plant_id='$username'".$searchQuery." 
  order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
}

$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "id"=>$row['id'],
      "employee_code"=>$row['employee_code'],
      "username"=>$row['username'],
      "name"=>$row['empname'] ?? '',
      "useremail"=>$row['useremail'],
      "role"=>$row['role_name'],
      "plant"=>$row['name']
    );
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