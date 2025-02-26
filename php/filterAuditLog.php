<?php
## Database configuration
require_once 'db_connect.php';

function searchActionNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Log_Action WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['description'];
        }
        $select_stmt->close();
    }

    return $id;
}

## Read value
// $draw = $_POST['draw'];
// $row = $_POST['start'];
// $rowperpage = $_POST['length']; // Rows display per page
// $columnIndex = $_POST['order'][0]['column']; // Column index
// $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
// $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
// $searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";

if($_POST['fromDateSearch'] != null && $_POST['fromDateSearch'] != ''){
    $fromDate = new DateTime($_POST['fromDateSearch']);
    $fromDateTime = date_format($fromDate,"Y-m-d 00:00:00");
     $searchQuery = " WHERE event_date >= '".$fromDateTime."'";
  }
  
  if($_POST['toDateSearch'] != null && $_POST['toDateSearch'] != ''){
    $toDate = new DateTime($_POST['toDateSearch']);
    $toDateTime = date_format($toDate,"Y-m-d 23:59:59");
      $searchQuery .= " and event_date <= '".$toDateTime."'";
  }

if($_POST['selectedValue'] == "Customer")
{
    if($_POST['customerCode'] != null && $_POST['customerCode'] != '' && $_POST['customerCode'] != '-'){
    $searchQuery .= " and customer_code = '".$_POST['customerCode']."'";
    }
}

if($_POST['selectedValue'] == "Destination")
{
    if($_POST['destinationCode'] != null && $_POST['destinationCode'] != '' && $_POST['destinationCode'] != '-'){
    $searchQuery .= " and destination_code = '".$_POST['destinationCode']."'";
    }
}

if($_POST['selectedValue'] == "Supplier")
{
    if($_POST['supplierCode'] != null && $_POST['supplierCode'] != '' && $_POST['supplierCode'] != '-'){
    $searchQuery .= " and supplier_code = '".$_POST['supplierCode']."'";
    }
}

if($_POST['selectedValue'] == "User")
{
    if($_POST['userCode'] != null && $_POST['userCode'] != ''){
    $searchQuery .= " and user_code like '%".$_POST['userCode']."%'";
    }
}

if($_POST['selectedValue'] == "Product")
{
    if($_POST['productCode'] != null && $_POST['productCode'] != ''){
    $searchQuery .= " and product_code like '%".$_POST['productCode']."%'";
    }
}

if($_POST['selectedValue'] == "Transporter")
{
    if($_POST['transporterCode'] != null && $_POST['transporterCode'] != '' && $_POST['transporterCode'] != '-'){
    $searchQuery .= " and transporter_code = '".$_POST['transporterCode']."'";
    }
}

if($_POST['selectedValue'] == "Unit")
{
    if($_POST['unit'] != null && $_POST['unit'] != '' && $_POST['unit'] != '-'){
    $searchQuery .= " and unit = '".$_POST['unit']."'";
    }
}

if($_POST['selectedValue'] == "Vehicle")
{
    if($_POST['vehicleNo'] != null && $_POST['vehicleNo'] != '' && $_POST['vehicleNo'] != '-'){
    $searchQuery .= " and veh_number = '".$_POST['vehicleNo']."'";
    }
}

if($_POST['selectedValue'] == "Bin")
{
    if($_POST['binCode'] != null && $_POST['binCode'] != '' && $_POST['binCode'] != '-'){
    $searchQuery .= " and bin_code = '".$_POST['binCode']."'";
    }
}

## Total number of records without filtering
// $sel = mysqli_query($db,"select count(*) as allcount from Customer_Log");
// $records = mysqli_fetch_assoc($sel);
// $totalRecords = $records['allcount'];

// ## Total number of record with filtering
// $sel = mysqli_query($db,"select count(*) as allcount from Customer_Log".$searchQuery);
// $records = mysqli_fetch_assoc($sel);
// $totalRecordwithFilter = $records['allcount'];

if($_POST['selectedValue'] == "Customer")
{
    ## Fetch records
    $empQuery = "select * from Customer_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Customer Id"=>$row['customer_id'],
        "Customer Code"=>$row['customer_code'],
        "Name"=>$row['name'],
        "Company Reg No"=>$row['company_reg_no'],
        "Address line 1"=>$row['address_line_1'],
        "Address line 2"=>$row['address_line_2'],
        "Address line 3"=>$row['address_line_3'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        "Phone No"=>$row['phone_no'],
        "Fax No"=>$row['fax_no']
        );
    }

    $columnNames = ["Customer Id", "Customer Code", "Name", "Company Reg No", "Action Id", "Action By", "Event Date", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No"];
}

if($_POST['selectedValue'] == "Destination")
{
    ## Fetch records
    $empQuery = "select * from Destination_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Destination Id"=>$row['destination_id'],
        "Destination Code"=>$row['destination_code'],
        "Name"=>$row['name'],
        "Description"=>$row['description'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["Destination Id", "Destination Code", "Name", "Description", "Action Id", "Action By", "Event Date"];
}

if($_POST['selectedValue'] == "Product")
{
    ## Fetch records
    $empQuery = "select * from Product_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Product Id"=>$row['product_id'],
        "Product Code"=>$row['product_code'],
        "Name"=>$row['name'],
        "Description"=>$row['description'],
        "Variance Type"=>$row['variance'],
        "High"=>$row['high'],
        "Low"=>$row['low'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["Product Id", "Product Code", "Name", "Description", "Variance Type", "High", "Low", "Action Id", "Action By", "Event Date"];
}

if($_POST['selectedValue'] == "Supplier")
{
    ## Fetch records
    $empQuery = "select * from Supplier_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Supplier Id"=>$row['supplier_id'],
        "Supplier Code"=>$row['supplier_code'],
        "Name"=>$row['name'],
        "Company Reg No"=>$row['company_reg_no'],
        "Address line 1"=>$row['address_line_1'],
        "Address line 2"=>$row['address_line_2'],
        "Address line 3"=>$row['address_line_3'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        "Phone No"=>$row['phone_no'],
        "Fax No"=>$row['fax_no']
        );
    }

    $columnNames = ["Supplier Id", "Supplier Code", "Name", "Company Reg No", "Action Id", "Action By", "Event Date", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No"];
}

if($_POST['selectedValue'] == "Transporter")
{
    ## Fetch records
    $empQuery = "select * from Transporter_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Transporter Id"=>$row['transporter_id'],
        "Transporter Code"=>$row['transporter_code'],
        "Name"=>$row['name'],
        "Company Reg No"=>$row['company_reg_no'],
        "Address line 1"=>$row['address_line_1'],
        "Address line 2"=>$row['address_line_2'],
        "Address line 3"=>$row['address_line_3'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        "Phone No"=>$row['phone_no'],
        "Fax No"=>$row['fax_no']
        );
    }

    $columnNames = ["Transporter Id", "Transporter Code", "Name", "Company Reg No", "Action Id", "Action By", "Event Date", "Address line 1", "Address line 2", "Address line 3", "Phone No", "Fax No"];
}

if($_POST['selectedValue'] == "User")
{
    ## Fetch records
    $empQuery = "select * from Users_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "User Id"=>$row['user_id'],
        "User Code"=>$row['employee_code'],
        "Name"=>$row['username'],
        "User Department"=>$row['user_department'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["User Id", "User Code", "Name", "User Department", "Action Id", "Action By", "Event Date"];
}

if($_POST['selectedValue'] == "Unit")
{
    ## Fetch records
    $empQuery = "select * from Unit_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Unit Id"=>$row['unit_id'],
        "Unit"=>$row['unit'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["Unit Id", "Unit", "Action Id", "Action By", "Event Date"];
}

if($_POST['selectedValue'] == "Vehicle")
{
    ## Fetch records
    $empQuery = "select * from Vehicle_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Vehicle Id"=>$row['vehicle_id'],
        "Vehicle No"=>$row['veh_number'],
        "Vehicle Weight"=>$row['vehicle_weight'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["Vehicle Id", "Vehicle No", "Vehicle Weight", "Action Id", "Action By", "Event Date"];
}

if($_POST['selectedValue'] == "Bin")
{
    ## Fetch records
    $empQuery = "select * from Bin_Log".$searchQuery;
    $empRecords = mysqli_query($db, $empQuery);
    $data = array();

    while($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array( 
        "id"=>$row['id'],
        "Bin Id"=>$row['bin_id'],
        "Bin Code"=>$row['bin_code'],
        "Name"=>$row['name'],
        "Description"=>$row['description'],
        "Action Id"=>searchActionNameById($row['action_id'],$db),
        "Action By"=>$row['action_by'],
        "Event Date"=>$row['event_date'],
        );
    }

    $columnNames = ["Bin Id", "Bin Code", "Name", "Description", "Action Id", "Action By", "Event Date"];
}

## Response
$response = [
    "columnNames" => $columnNames,
    "dataTable" => $data
];

header("Content-Type: application/json");
echo json_encode($response);
?>