<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";

$user = $_SESSION['id'];
$plantId = $_SESSION['plant'];
$stmt = $db->prepare("SELECT * from Port WHERE weighind_id = ?");
$stmt->bind_param('s', $user);
$stmt->execute();
$result = $stmt->get_result();
//$role = 'NORMAL';
$port = 'COM5';
$baudrate = 9600;
$databits = "8";
$parity = "N";
$stopbits = '1';
$indicator = 'BX23';
    
if(($row = $result->fetch_assoc()) !== null){
    //$role = $row['role_code'];
    $port = $row['com_port'];
    $baudrate = $row['bits_per_second'];
    $databits = $row['data_bits'];
    $parity = $row['parity'];
    $stopbits = $row['stop_bits'];
    $indicator = $row['indicator'];
}

$plantName = '-';

if($plantId != null && $plantId != ''){
    $stmt2 = $db->prepare("SELECT * from Plant WHERE plant_code = ?");
    $stmt2->bind_param('s', $plantId);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
        
    if(($row2 = $result2->fetch_assoc()) !== null){
        $plantName = $row2['name'];
    }
}

$role = 'NORMAL';
if ($user != null && $user != ''){
    $stmt3 = $db->prepare("SELECT * from Users WHERE id = ?");
    $stmt3->bind_param('s', $user);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
        
    if(($row3 = $result3->fetch_assoc()) !== null){
        $role = $row3['role'];
    }
}


//$lots = $db->query("SELECT * FROM lots WHERE deleted = '0'");
$vehicles = $db->query("SELECT * FROM Vehicle WHERE status = '0'");
$vehicles2 = $db->query("SELECT * FROM Vehicle WHERE status = '0'");
$customer = $db->query("SELECT * FROM Customer WHERE status = '0'");
$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0'");
$product = $db->query("SELECT * FROM Product WHERE status = '0'");
$product2 = $db->query("SELECT * FROM Product WHERE status = '0'");
$transporter = $db->query("SELECT * FROM Transporter WHERE status = '0'");
$destination = $db->query("SELECT * FROM Destination WHERE status = '0'");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0'");
$unit = $db->query("SELECT * FROM Unit WHERE status = '0'");

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = $_SESSION["plant"];
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code='$username'");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
}

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = $_SESSION["plant"];
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code='$username'");
}
else{
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
}


$agent = $db->query("SELECT * FROM Agents WHERE status = '0'");
$rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0'");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0'");
$site = $db->query("SELECT * FROM Site WHERE status = '0'");
?>

<head>

    <title>Weighing | Synctronix - Weighing System</title>
    <?php include 'layouts/title-meta.php'; ?>

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include jQuery Validate plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <?php include 'layouts/head-css.php'; ?>
    <style>
        .mb-3 {
            margin-bottom: 0.5rem !important;
        }

        .modal-header {
            padding: var(1rem, 1rem) !important;
        }
    </style>
</head>

<?php include 'layouts/body.php'; ?>

<!-- <div class="loading" id="spinnerLoading" style="display:none">
  <div class='mdi mdi-loading' style='transform:scale(0.79);'>
    <div></div>
  </div>
</div> -->

<!-- Begin page -->
<div id="layout-wrapper">

    <?php include 'layouts/menu.php'; ?>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="h-100">
                            <div class="row mb-3 pb-1">
                                <div class="col-12">
                                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                        <div class="flex-grow-1">
                                            <!--h4 class="fs-16 mb-1">Good Morning, Anna!</h4>
                                            <p class="text-muted mb-0">Here's what's happening with your store
                                                today.</p-->
                                        </div>
                                        <div class="mt-3 mt-lg-0">
                                            <form action="javascript:void(0);">
                                                <div class="row g-3 mb-0 align-items-center">

                                            </form>
                                        </div>
                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header fs-5" href="#collapseSearch" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseSearch">
                                        <i class="mdi mdi-chevron-down pull-right"></i>
                                        Search Records
                                    </div>
                                    <div id="collapseSearch" class="collapse" aria-labelledby="collapseSearch">                                    
                                        <div class="card-body">
                                            <form action="javascript:void(0);">
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="fromDateSearch" class="form-label">From Date</label>
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="fromDateSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="toDateSearch" class="form-label">To Date</label>
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="toDateSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="statusSearch" class="form-label">Transaction Status</label>
                                                            <select id="statusSearch" class="form-select">
                                                                <option selected>-</option>
                                                                <option value="Sales">Sales</option>
                                                                <option value="Purchase">Purchase</option>
                                                                <option value="Local">Local</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="customerNoSearch" class="form-label">Customer No</label>
                                                            <select id="customerNoSearch" class="form-select" >
                                                                <option selected>-</option>
                                                                <?php while($rowPF = mysqli_fetch_assoc($customer2)){ ?>
                                                                    <option value="<?=$rowPF['customer_code'] ?>"><?=$rowPF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="vehicleNo" class="form-label">Vehicle No</label>
                                                            <input type="text" class="form-control" placeholder="Vehicle No" id="vehicleNo">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="invoiceNoSearch" class="form-label">Weighing Type</label>
                                                            <select id="invoiceNoSearch" class="form-select"  >
                                                                <option selected>-</option>
                                                                <option value="Normal">Normal</option>
                                                                <option value="Container">Container</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="batchNoSearch" class="form-label">Status</label>
                                                            <select id="batchNoSearch" class="form-select">
                                                                <option value="N" selected>Pending</option>
                                                                <option value="Y">Complete</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->                                                
                                                    <div class="col-3" id="productSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="ForminputState" class="form-label">Product</label>
                                                            <select id="productSearch" class="form-select" >
                                                                <option selected>-</option>
                                                                <?php while($rowProductF=mysqli_fetch_assoc($product2)){ ?>
                                                                    <option value="<?=$rowProductF['product_code'] ?>"><?=$rowProductF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="rawMatSearchDisplay" style="display:none">
                                                        <div class="mb-3">
                                                            <label for="ForminputState" class="form-label">Raw Material</label>
                                                            <select id="rawMatSearch" class="form-select" >
                                                                <option selected>-</option>
                                                                <?php while($rowRawMatF=mysqli_fetch_assoc($rawMaterial2)){ ?>
                                                                    <option value="<?=$rowRawMatF['raw_mat_code'] ?>"><?=$rowRawMatF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="plantSearchDisplay" style="display:none">
                                                        <div class="mb-3">
                                                            <label for="ForminputState" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select" >
                                                                <option selected>-</option>
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant2)){ ?>
                                                                    <option value="<?=$rowPlantF['plant_code'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-danger" id="filterSearch"><i class="bx bx-search-alt"></i> Search</button>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end row-->
                                            </form>                                                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Sales</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                            class="counter-value" id="salesInfo">0</span>
                                                    </h4>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                                        <i class="bx bx-dollar-circle text-success"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->

                                <div class="col-xl-4 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Purchase</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                            class="counter-value" id="purchaseInfo">0</span></h4>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                                        <i class="bx bx-shopping-bag text-info"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->

                                <div class="col-xl-4 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                    Local</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                            class="counter-value" id="localInfo">0</span>
                                                    </h4>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                                        <i class="bx bx-user-circle text-warning"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->

                                <div class="col-xl-3 col-md-6 add-new-weight">
                                    <!-- <button type="button" class="btn btn-lg btn-soft-success" data-bs-toggle="modal" data-bs-target="#addModal"><i
                                            class="ri-add-circle-line align-middle me-1"></i>
                                        Add New Weight</button> -->

                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Entry</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="weightForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="hstack gap-2 justify-content-center">
                                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                                        <div class="card bg-danger">
                                                                            <div class="card-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="indicatorWeight">0</span> Kg</h2>
                                                                                    </div>
                                                                                    <!--div class="connected-align">
                                                                                        <div class="input-group-text color-palette" id="indicatorConnected"><i>Indicator Connected</i></div>
                                                                                        <div class="input-group-text bg-danger color-palette" id="checkingConnection"><i>Checking Connection</i></div>
                                                                                    </div-->
                                                                                    <div>
                                                                                        <div class="avatar-sm flex-shrink-0">
                                                                                            <span class="avatar-title bg-soft-light rounded-circle fs-2">
                                                                                                <i class="mdi mdi-weight-kilogram"></i>
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!-- end card body -->
                                                                        </div> <!-- end card-->
                                                                    </div> <!-- end col-->
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="hstack gap-2 justify-content-center">
                                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                                        <div class="card bg-danger">
                                                                            <div class="card-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="currentWeight">0</span> Kg</h2>
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="avatar-sm flex-shrink-0">
                                                                                            <span class="avatar-title bg-soft-light rounded-circle fs-2">
                                                                                                <i class="mdi mdi-weight-kilogram"></i>
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!-- end card body -->
                                                                        </div> <!-- end card-->
                                                                    </div> <!-- end col-->
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionId" class="col-sm-4 col-form-label">Transaction ID</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly" id="transactionId" name="transactionId" placeholder="Transaction ID" readonly>                                                                                  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divPurchaseOrder">
                                                                                <div class="row">
                                                                                    <label for="purchaseOrder" class="col-sm-4 col-form-label">Purchase Order</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="purchaseOrder" name="purchaseOrder" placeholder="Purchase Order">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divOrderWeight">
                                                                                <div class="row">
                                                                                    <label for="orderWeight" class="col-sm-4 col-form-label">Order Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="orderWeight" name="orderWeight"  placeholder="Order Weight">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divSupplierWeight" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="supplierWeight" class="col-sm-4 col-form-label">Supplier Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="supplierWeight" name="supplierWeight"  placeholder="Supplier Weight">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>  
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="weightType" class="col-sm-4 col-form-label">Weight Type</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="weightType" name="weightType" class="form-select">
                                                                                            <option selected>Normal</option>
                                                                                            <option>Container</option>
                                                                                        </select>   
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="containerNo" class="col-sm-4 col-form-label">Container No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="containerNo" name="containerNo" placeholder="Container No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divWeightDifference">
                                                                                <div class="row">
                                                                                    <label for="weightDifference" class="col-sm-4 col-form-label">Weight Difference</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="weightDifference" name="weightDifference" placeholder="Weight Difference" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionStatus" class="col-sm-4 col-form-label">Transaction Status</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" name="transactionStatus" class="form-select">
                                                                                            <option value="Sales" selected>Sales</option>
                                                                                            <option value="Purchase">Purchase</option>
                                                                                            <option value="Local">Local</option>
                                                                                        </select>  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divCustomerName">
                                                                                <div class="row">
                                                                                    <label for="customerName" class="col-sm-4 col-form-label">Customer Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select js-choice" id="customerName" name="customerName">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowCustomer=mysqli_fetch_assoc($customer)){ ?>
                                                                                                <option value="<?=$rowCustomer['name'] ?>" data-code="<?=$rowCustomer['customer_code'] ?>"><?=$rowCustomer['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divSupplierName" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="supplierName" class="col-sm-4 col-form-label">Supplier Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="supplierName" name="supplierName">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowSupplier=mysqli_fetch_assoc($supplier)){ ?>
                                                                                                <option value="<?=$rowSupplier['name'] ?>" data-code="<?=$rowSupplier['supplier_code'] ?>"><?=$rowSupplier['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="reduceWeight" class="col-sm-4 col-form-label">Reduce Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="reduceWeight" name="reduceWeight" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionDate" class="col-sm-4 col-form-label">Transaction Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="transactionDate" name="transactionDate" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>    
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" id="productNameDisplay">
                                                                                    <label for="productName" class="col-sm-4 col-form-label">Product Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="productName" name="productName" >
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                                                                                <option 
                                                                                                    value="<?=$rowProduct['name'] ?>" 
                                                                                                    data-price="<?=$rowProduct['price'] ?>" 
                                                                                                    data-code="<?=$rowProduct['product_code'] ?>" 
                                                                                                    data-high="<?=$rowProduct['high'] ?>" 
                                                                                                    data-low="<?=$rowProduct['low'] ?>" 
                                                                                                    data-variance="<?=$rowProduct['variance'] ?>" 
                                                                                                    data-description="<?=$rowProduct['description'] ?>">
                                                                                                    <?=$rowProduct['name'] ?>
                                                                                                </option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row" id="rawMaterialDisplay" style="display:none;">
                                                                                    <label for="rawMaterialName" class="col-sm-4 col-form-label">Raw Material</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="rawMaterialName" name="rawMaterialName" >
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowRowMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                                                                                                <option value="<?=$rowRowMat['name'] ?>" data-code="<?=$rowRowMat['raw_mat_code'] ?>"><?=$rowRowMat['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>           
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="subTotalPrice" class="col-sm-4 col-form-label">Sub-Total Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="subTotalPrice" name="subTotalPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>   
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="Invoice No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transporter" class="col-sm-4 col-form-label">Transporter</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="transporter" name="transporter" >
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowTransporter=mysqli_fetch_assoc($transporter)){ ?>
                                                                                                <option value="<?=$rowTransporter['name'] ?>" data-code="<?=$rowTransporter['transporter_code'] ?>"><?=$rowTransporter['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                          
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="sstPrice" class="col-sm-4 col-form-label">SST (6%)</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="sstPrice" name="sstPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="deliveryNo" class="col-sm-4 col-form-label">Delivery No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="deliveryNo" name="deliveryNo" placeholder="Delivery No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="destination" class="col-sm-4 col-form-label">Destination</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="destination" name="destination">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowDestination=mysqli_fetch_assoc($destination)){ ?>
                                                                                                <option value="<?=$rowDestination['name'] ?>" data-code="<?=$rowDestination['destination_code'] ?>"><?=$rowDestination['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                         
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="totalPrice" class="col-sm-4 col-form-label">Total Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="totalPrice" name="totalPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="indicatorId" class="col-sm-4 col-form-label">Indicator ID</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="indicatorId" name="indicatorId" class="form-select" >
                                                                                            <option selected>ind12345</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3"  <?php 
                                                                                if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
                                                                                    echo 'style="display:none;"';
                                                                                }?>>
                                                                                <div class="row">
                                                                                    <label for="manualWeight" class="col-sm-4 col-form-label">Manual Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="form-check align-radio mr-2">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="manualWeight" id="manualWeightYes" value="true">
                                                                                            <label class="form-check-label" for="manualWeightYes">
                                                                                               Yes
                                                                                            </label>
                                                                                        </div>

                                                                                        <div class="form-check align-radio">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="manualWeight" id="manualWeightNo" value="false" checked>
                                                                                            <label class="form-check-label" for="manualWeightNo">
                                                                                               No
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="exDel" class="col-sm-4 col-form-label">Ex-Quarry/Delivered</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="form-check align-radio mr-2">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="exDel" id="manualEx" value="true">
                                                                                            <label class="form-check-label" for="manualEx">
                                                                                               Ex-Quarry
                                                                                            </label>
                                                                                        </div>

                                                                                        <div class="form-check align-radio">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="exDel" id="manualDel" value="false" checked>
                                                                                            <label class="form-check-label" for="manualDel">
                                                                                               Delivered
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="agent" class="col-sm-4 col-form-label">Sales Representative</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="agent" name="agent" >
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowAgent=mysqli_fetch_assoc($agent)){ ?>
                                                                                                <option value="<?=$rowAgent['name'] ?>" data-code="<?=$rowAgent['agent_code'] ?>"><?=$rowAgent['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                         
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="plant" class="col-sm-4 col-form-label">Plant *</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="plant" name="plant" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowPlant=mysqli_fetch_assoc($plant)){ ?>
                                                                                                <option value="<?=$rowPlant['name'] ?>" data-code="<?=$rowPlant['plant_code'] ?>"><?=$rowPlant['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="loadDrum" class="col-sm-4 col-form-label">By-Load/By-Drum</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="form-check align-radio mr-2">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="loadDrum" id="manualLoad" value="true" checked>
                                                                                            <label class="form-check-label" for="manualLoad">
                                                                                               By-Load
                                                                                            </label>
                                                                                        </div>

                                                                                        <div class="form-check align-radio">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="loadDrum" id="manualDrum" value="false">
                                                                                            <label class="form-check-label" for="manualDrum">
                                                                                               By-Drum
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="siteName" class="col-sm-4 col-form-label">Site *</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="siteName" name="siteName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowSite=mysqli_fetch_assoc($site)){ ?>
                                                                                                <option value="<?=$rowSite['name'] ?>" data-code="<?=$rowSite['site_code'] ?>"><?=$rowSite['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="balance" class="col-sm-4 col-form-label">Balance *</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly text-danger" id="balance" name="balance" placeholder="0" readonly>   
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-12">
                                                            <div class="col-xxl-4 col-lg-4">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row mb-3">
                                                                            <label for="vehiclePlateNo1" class="col-sm-4 col-form-label">
                                                                                Vehicle Plate No.
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manualVehicle" name="manualVehicle" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>
                                                                                    <input type="text" class="form-control" id="vehicleNoTxt" name="vehicleNoTxt" placeholder="Vehicle Plate No" style="display:none" required>
                                                                                    <div class="col-10 index-vehicle">
                                                                                        <select class="form-select" id="vehiclePlateNo1" name="vehiclePlateNo1" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($row2=mysqli_fetch_assoc($vehicles)){ ?>
                                                                                                <option value="<?=$row2['veh_number'] ?>" data-weight="<?=$row2['vehicle_weight'] ?>"><?=$row2['veh_number'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="invalid-feedback">
                                                                                        Please fill in the field.
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3" id="noOfDrumDisplay" style="display:none;">
                                                                            <label for="noOfDrum" class="col-sm-4 col-form-label">No of Drum</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="number" class="form-control" id="noOfDrum" name="noOfDrum">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncoming" class="col-sm-4 col-form-label">Incoming</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <!-- <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manual" name="manual" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>                                                                                             -->
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming" name="grossIncoming" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-danger fs-5" id="grossCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate" name="grossIncomingDate" readonly>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoing" class="col-sm-4 col-form-label">Outgoing</label>
                                                                            <div class="col-sm-8">                                                                                     
                                                                                <div class="input-group">
                                                                                    <!-- <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manualOutgoing" name="manualOutgoing" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>                                                                                                -->
                                                                                    <input type="number" class="form-control input-readonly" id="tareOutgoing" name="tareOutgoing" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-danger fs-5" id="tareCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>                                                                                       
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoingDate" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="tareOutgoingDate" name="tareOutgoingDate" readonly>
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <div class="row mb-3">
                                                                            <label for="nettWeight" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="nettWeight" name="nettWeight" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>                                                                                                                                  
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4" id="containerCard" style="display:none;">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row mb-3">
                                                                            <label for="vehiclePlateNo2" class="col-sm-4 col-form-label">Vehicle Plate No 2</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manualVehicle2" name="manualVehicle2" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>
                                                                                    <input type="text" class="form-control" id="vehicleNoTxt2" name="vehicleNoTxt2" placeholder="Vehicle Plate No" style="display:none">
                                                                                    <div class="col-10 index-vehicle2">
                                                                                        <select class="form-select" id="vehiclePlateNo2" name="vehiclePlateNo2">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowv2=mysqli_fetch_assoc($vehicles2)){ ?>
                                                                                                <option value="<?=$rowv2['veh_number'] ?>" data-weight="<?=$rowv2['vehicle_weight'] ?>"><?=$rowv2['veh_number'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="invalid-feedback">
                                                                                        Please fill in the field.
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncoming2" class="col-sm-4 col-form-label">3.Gross Incoming</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming2" name="grossIncoming2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-danger fs-5" id="grossCapture2"><i class="mdi mdi-sync" type="button"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate2" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate2" name="grossIncomingDate2" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoing2" class="col-sm-4 col-form-label">4.Tare Outgoing</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="tareOutgoing2" name="tareOutgoing2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                    <button class="input-group-text btn btn-danger fs-5" id="tareCapture2" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoingDate2" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" placeholder="" id="tareOutgoingDate2" name="tareOutgoingDate2" readonly>
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <div class="row mb-3">
                                                                            <label for="nettWeight2" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="nettWeight2" name="nettWeight2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">Kg</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>                                                                    
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row">
                                                                    <label for="otherRemarks" class="col-sm-2 col-form-label">Other Remarks</label>
                                                                    <div class="col-sm-10">
                                                                        <textarea class="form-control" id="otherRemarks" name="otherRemarks" rows="3" placeholder="Other Remarks"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger" id="submitWeightPrint">Submit & Print</button>
                                                                <button type="button" class="btn btn-primary" id="submitWeight">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                        
                                                        <input type="hidden" id="bypassReason" name="bypassReason">
                                                        <input type="hidden" id="finalWeight" name="finalWeight">
                                                        <input type="hidden" id="customerCode" name="customerCode">
                                                        <input type="hidden" id="destinationCode" name="destinationCode">
                                                        <input type="hidden" id="plantCode" name="plantCode">
                                                        <input type="hidden" id="agentCode" name="agentCode">
                                                        <input type="hidden" id="status" name="status">
                                                        <input type="hidden" id="productCode" name="productCode">
                                                        <input type="hidden" id="productDescription" name="productDescription">
                                                        <input type="hidden" id="productPrice" name="productPrice">
                                                        <input type="hidden" id="productHigh" name="productHigh">
                                                        <input type="hidden" id="productLow" name="productLow">
                                                        <input type="hidden" id="productVariance" name="productVariance">
                                                        <input type="hidden" id="transporterCode" name="transporterCode">
                                                        <input type="hidden" id="supplierCode" name="supplierCode">
                                                        <input type="hidden" id="rawMaterialCode" name="rawMaterialCode">
                                                        <input type="hidden" id="siteCode" name="siteCode">
                                                        <input type="hidden" id="id" name="id">  
                                                        <input type="hidden" id="weighbridge" name="weighbridge" value="Weigh1">
                                                        <input type="hidden" id="previousRecordsTag" name="previousRecordsTag">
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div class="modal fade" id="bypassModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <form role="form" id="bypassForm" class="needs-validation" novalidate autocomplete="off">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalScrollableTitle">Key in reasons</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-12">
                                                            <label for="nettWeight" class="col-sm-4 col-form-label">Password</label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="passcode" name="passcode" placeholder="0" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-xxl-12 col-lg-12 mb-12">
                                                            <div class="row">
                                                                <label for="reason" class="col-sm-2 col-form-label">Reasons *</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Reasons" required></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger" id="submitBypass">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <form role="form" id="approvalForm" class="needs-validation" novalidate autocomplete="off">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalScrollableTitle">Key in reasons</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" id="id" name="id"/>
                                                        <div class="row  col-xxl-12 col-lg-12 mb-1">
                                                            <div class="row">
                                                                <label for="statusA" class="col-sm-2 col-form-label">Approve?</label>
                                                                <div class="col-sm-8">
                                                                    <select class="form-select" id="statusA" name="statusA" required>
                                                                        <option value="Y">Approve</option>
                                                                        <option value="N">Reject</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-xxl-12 col-lg-12 mb-12">
                                                            <div class="row">
                                                                <label for="reasons" class="col-sm-2 col-form-label">Reasons *</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control" id="reasons" name="reasons" rows="3" placeholder="Reasons" required></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger" id="submitApproval">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end row-->

                            <div class="row">
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0">Previous Records</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <button type="button" id="addWeight" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                                Add New Weight
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Transaction <br>Id</th>
                                                                    <th>Weight <br> Status</th>
                                                                    <th>Weight <br> Type</th>
                                                                    <th>Vehicle</th>
                                                                    <th>Product</th>
                                                                    <th>Gross <br>Incoming</th>
                                                                    <th>Incoming <br>Date</th>
                                                                    <th>Tare <br>Outgoing</th>
                                                                    <th>Outgoing <br>Date</th>
                                                                    <th>Nett <br>Weight</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--end row-->
                                    </div> <!-- end .h-100-->
                                </div> <!-- end col -->
                            </div><!-- container-fluid -->
                        </div> <!-- end .h-100-->

                    </div> <!-- end col -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <div class="modal fade" id="setupModal">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <form role="form" id="setupForm">
                        <div class="modal-header bg-gray-dark color-palette">
                            <h4 class="modal-title">Setup</h4>
                            <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Serial Port</label>
                                        <input class="form-control" type="text" id="serialPort" name="serialPort" value="<?=$port ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Baud Rate</label>
                                        <input class="form-control" type="number" id="serialPortBaudRate" name="serialPortBaudRate" value="<?=$baudrate ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Data Bits</label>
                                        <input class="form-control" type="text" id="serialPortDataBits" name="serialPortDataBits" value="<?=$databits ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Parity</label>
                                        <input class="form-control" type="text" id="serialPortParity" name="serialPortParity" value="<?=$parity ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Stop bits</label>
                                        <input class="form-control" type="text" id="serialPortStopBits" name="serialPortStopBits" value="<?=$stopbits ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>
    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>
    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>   
    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <!-- prismjs plugin -->
    <script src="assets/libs/prismjs/prism.js"></script>
    <!-- notifications init -->
    <script src="assets/js/pages/notifications.init.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="assets/js/pages/datatables.init.js"></script>
    <!-- Additional js -->
    <script src="assets/js/additional.js"></script>

    <script type="text/javascript">
    var table = null;
    
    $(function () {
        var userRole = '<?=$role ?>';
        var ind = '<?=$indicator ?>';
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: yesterday
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        $('#transactionDate').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        if (userRole == 'SADMIN' || userRole == 'ADMIN'){
            $('#plantSearchDisplay').show();
        }else{
            $('#plantSearchDisplay').hide();
        }

        $('#statusSearch').on('change', function(){
            var status = $(this).val();

            if (status == 'Purchase' || status == 'Local'){
                $('#productSearchDisplay').hide();
                $('#rawMatSearchDisplay').show();
            }else{
                $('#productSearchDisplay').show();
                $('#rawMatSearchDisplay').hide();
            }
        });

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
        var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
        var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
        var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
        var batchNoI = $('#batchNoSearch').val() ? $('#batchNoSearch').val() : '';
        var productSearchI = $('#productSearch').val() ? $('#productSearch').val() : '';
        var rawMaterialI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

        table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterWeight.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    status: statusI,
                    customer: customerNoI,
                    vehicle: vehicleNoI,
                    invoice: invoiceNoI,
                    batch: batchNoI,
                    product: productSearchI,
                    rawMaterial: rawMaterialI,
                    plant: plantNoI,
                } 
            },
            'columns': [
                { data: 'transaction_id' },
                { data: 'transaction_status' },
                { data: 'weight_type' },
                { data: 'lorry_plate_no1' },
                { data: 'product_description' },
                { data: 'gross_weight1' },
                { data: 'gross_weight1_date' },
                { data: 'tare_weight1' },
                { data: 'tare_weight1_date' },
                { data: 'nett_weight1' },
                { 
                    data: 'id',
                    render: function (data, type, row) {
                        let dropdownMenu = '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                                        '<li><a class="dropdown-item edit-item-btn" id="edit' + data + '" onclick="edit(' + data + ')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
                                        '<li><a class="dropdown-item print-item-btn" id="print' + data + '" onclick="print(' + data + ')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li>' +
                                        '<li><a class="dropdown-item remove-item-btn" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>';

                        if (row.is_approved == 'Y') {
                            dropdownMenu += '<li><a class="dropdown-item print-item-btn" id="print' + data + '" onclick="print(' + data + ')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li>';
                        }

                        if (row.is_approved == 'N') {
                            dropdownMenu += '<li><a class="dropdown-item approval-item-btn" id="approve' + data + '" onclick="approve(' + data + ')"><i class="ri-check-fill align-bottom me-2 text-muted"></i> Approval</a></li>';
                        }

                        dropdownMenu += '<li><a class="dropdown-item remove-item-btn" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>';

                        dropdownMenu += '</ul></div>';
                        return dropdownMenu;
                    }
                }
            ],
            "drawCallback": function(settings) {
                $('#salesInfo').text(settings.json.salesTotal);
                $('#purchaseInfo').text(settings.json.purchaseTotal);
                $('#localInfo').text(settings.json.localTotal);
            }   
        });

        $('#submitWeight').on('click', function(){
            // Check weight
            var trueWeight = 0;
            var variance = $('#productVariance').val() || '';
            var high = $('#productHigh').val() || '';
            var low = $('#productLow').val() || '';
            var final = $('#finalWeight').val() || '0';
            var completed = 'N';
            var pass = true;

            if($('#transactionStatus').val() == "Purchase" || $('#transactionStatus').val() == "Local"){
                trueWeight = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#addModal').find('#orderWeight').val());
            }

            if($('#weightType').val() == 'Normal' && ($('#grossIncoming').val() && $('#tareOutgoing').val())){
                isComplete = 'Y';
            }
            else if($('#weightType').val() == 'Container' && ($('#grossIncoming').val() && $('#tareOutgoing').val() && $('#grossIncoming2').val() && $('#tareOutgoing2').val())){
                isComplete = 'Y';
            }
            else{
                isComplete = 'N';
            }

            if (isComplete == 'Y' && variance != '') {
                final = parseFloat(final);
                low = low != '' ? parseFloat(low) : null;
                high = high != '' ? parseFloat(high) : null;
                
                if (variance == 'W') {
                    if (low !== null && (final < trueWeight - low)) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight + high)) {
                        pass = false;
                    }
                } 
                else if (variance == 'P') {
                    if (low !== null && (final < trueWeight * (1 - low / 100))) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight * (1 + high / 100))) {
                        pass = false;
                    }
                }
            }

            if(pass && $('#weightForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        <?php
                            if(isset($_GET['weight'])){
                                echo "window.location = 'index.php';";
                            }
                        ?>
                        table.ajax.reload();
                        window.location = 'index.php';
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
            }
            else{
                let userChoice = confirm('The final value is out of the acceptable range. Do you want to send for approval (OK) or bypass (Cancel)?');
                if (userChoice) {
                    $('#addModal').find('#status').val("pending");
                    $('#spinnerLoading').show();
                    $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                        var obj = JSON.parse(data); 
                        if(obj.status === 'success'){
                            <?php
                                if(isset($_GET['weight'])){
                                    echo "window.location = 'index.php';";
                                }
                            ?>
                            table.ajax.reload();
                            window.location = 'index.php';
                            $('#spinnerLoading').hide();
                            $('#addModal').modal('hide');
                            $("#successBtn").attr('data-toast-text', obj.message);
                            $("#successBtn").click();
                        }
                        else if(obj.status === 'failed'){
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                        else{
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', 'Failed to save');
                            $("#failBtn").click();
                        }
                    });
                } 
                else {
                    $('#bypassModal').find('#passcode').val("");
                    $('#bypassModal').find('#reason').val("");
                    $('#bypassModal').modal('show');
            
                    $('#bypassForm').validate({
                        errorElement: 'span',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass('is-invalid');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass('is-invalid');
                        }
                    });
                }
            }
        });

        $('#submitWeightPrint').on('click', function(){
            // Check weight
            var trueWeight = 0;
            var variance = $('#productVariance').val() || '';
            var high = $('#productHigh').val() || '';
            var low = $('#productLow').val() || '';
            var final = $('#finalWeight').val() || '0';
            var completed = 'N';
            var pass = true;

            if($('#transactionStatus').val() == "Purchase" || $('#transactionStatus').val() == "Local"){
                trueWeight = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#addModal').find('#orderWeight').val());
            }

            if($('#weightType').val() == 'Normal' && ($('#grossIncoming').val() && $('#tareOutgoing').val())){
                isComplete = 'Y';
            }
            else if($('#weightType').val() == 'Container' && ($('#grossIncoming').val() && $('#tareOutgoing').val() && $('#grossIncoming2').val() && $('#tareOutgoing2').val())){
                isComplete = 'Y';
            }
            else{
                isComplete = 'N';
            }

            if (isComplete == 'Y' && variance != '') {
                final = parseFloat(final);
                low = low != '' ? parseFloat(low) : null;
                high = high != '' ? parseFloat(high) : null;
                
                if (variance == 'W') {
                    if (low !== null && (final < trueWeight - low)) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight + high)) {
                        pass = false;
                    }
                } 
                else if (variance == 'P') {
                    if (low !== null && (final < trueWeight * (1 - low / 100))) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight * (1 + high / 100))) {
                        pass = false;
                    }
                }
            }

            if(pass && $('#weightForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();

                        $.post('php/print.php', {userID: obj.id, file: 'weight'}, function(data){
                            var obj = JSON.parse(data);

                            if(obj.status === 'success'){
                                debugger;
                                var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                                printWindow.document.write(obj.message);
                                printWindow.document.close();
                                setTimeout(function(){
                                    printWindow.print();
                                    printWindow.close();
                                    table.ajax.reload();
                                    window.location = 'index.php';
                                }, 500);
                            }
                            else if(obj.status === 'failed'){
                                toastr["error"](obj.message, "Failed:");
                            }
                            else{
                                toastr["error"]("Something wrong when activate", "Failed:");
                            }
                        });
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
            }
            else{
                let userChoice = confirm('The final value is out of the acceptable range. Do you want to send for approval (OK) or bypass (Cancel)?');
                if (userChoice) {
                    $('#addModal').find('#status').val("pending");
                    $('#spinnerLoading').show();
                    $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                        var obj = JSON.parse(data); 
                        if(obj.status === 'success'){
                            <?php
                                if(isset($_GET['weight'])){
                                    echo "window.location = 'index.php';";
                                }
                            ?>
                            table.ajax.reload();
                            window.location = 'index.php';
                            $('#spinnerLoading').hide();
                            $('#addModal').modal('hide');
                            $("#successBtn").attr('data-toast-text', obj.message);
                            $("#successBtn").click();
                        }
                        else if(obj.status === 'failed'){
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                        else{
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', 'Failed to save');
                            $("#failBtn").click();
                        }
                    });
                } 
                else {
                    $('#bypassModal').find('#passcode').val("");
                    $('#bypassModal').find('#reason').val("");
                    $('#bypassModal').modal('show');
            
                    $('#bypassForm').validate({
                        errorElement: 'span',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass('is-invalid');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass('is-invalid');
                        }
                    });
                }
            }
        });

        $('#submitBypass').on('click', function(){
            if($('#bypassForm').valid()){
                $('#addModal').find('#bypassReason').val($('#bypassModal').find('#reason').val());
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        <?php
                            if(isset($_GET['weight'])){
                                echo "window.location = 'index.php';";
                            }
                        ?>
                        table.ajax.reload();
                        window.location = 'index.php';
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('#submitApproval').on('click', function(){
            if($('#approvalForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/updateApproval.php', $('#approvalForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        <?php
                            if(isset($_GET['approve'])){
                                echo "window.location = 'index.php';";
                            }
                        ?>
                        table.ajax.reload();
                        window.location = 'index.php';
                        $('#spinnerLoading').hide();
                        $('#approvalModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
            }
        });

        $.post('http://127.0.0.1:5002/', $('#setupForm').serialize(), function(data){
            if(data == "true"){
                $('#indicatorConnected').addClass('bg-primary');
                $('#checkingConnection').removeClass('bg-danger');
                //$('#captureWeight').removeAttr('disabled');
            }
            else{
                $('#indicatorConnected').removeClass('bg-primary');
                $('#checkingConnection').addClass('bg-danger');
                //$('#captureWeight').attr('disabled', true);
            }
        });

        setInterval(function () {
            $.post('http://127.0.0.1:5002/handshaking', function(data){
                if(data != "Error"){
                    console.log("Data Received:" + data);
                    
                    if(ind == 'X2S' || ind == 'X722'){
                        var text = data.split(" ");
                        var text2 = text[text.length - 1];
                        text2 = text2.replace("kg", "").replace("KG", "").replace("Kg", "");
                        $('#indicatorWeight').html(text2);
                        $('#indicatorConnected').addClass('bg-primary');
                        $('#checkingConnection').removeClass('bg-danger');
                    }
                    else if(ind == 'BX23'){
                        var text = data.split(" ");
                        let newArray = text.slice(1, -1);
                        let newtext = newArray.join();
                        $('#indicatorWeight').html(newtext.replaceAll(",", "").trim());
                        $('#indicatorConnected').addClass('bg-primary');
                        $('#checkingConnection').removeClass('bg-danger');
                    }
                    else if(ind == '205'){
                        var text = data.split(" ");
                        let newArray = text.slice(1, -1);
                        let newtext = newArray.join();
                        $('#indicatorWeight').html(newtext.replaceAll(",", "").trim());
                        $('#indicatorConnected').addClass('bg-primary');
                        $('#checkingConnection').removeClass('bg-danger');
                    }
                }
                else{
                    $('#indicatorWeight').html('0');
                    $('#indicatorConnected').removeClass('bg-primary');
                    $('#checkingConnection').addClass('bg-danger');
            }
            });
        }, 500);

        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var batchNoI = $('#batchNoSearch').val() ? $('#batchNoSearch').val() : '';
            var productSearchI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMaterialI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

            //Destroy the old Datatable
            $("#weightTable").DataTable().clear().destroy();

            //Create new Datatable
            table = $("#weightTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'searching': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':'php/filterWeight.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        customer: customerNoI,
                        vehicle: vehicleNoI,
                        invoice: invoiceNoI,
                        batch: batchNoI,
                        product: productSearchI,
                        rawMaterial: rawMaterialI,
                        plant: plantNoI,
                    } 
                },
                'columns': [
                    { data: 'transaction_id' },
                    { data: 'transaction_status' },
                    { data: 'weight_type' },
                    { data: 'lorry_plate_no1' },
                    { data: 'product_description' },
                    { data: 'gross_weight1' },
                    { data: 'gross_weight1_date' },
                    { data: 'tare_weight1' },
                    { data: 'tare_weight1_date' },
                    { data: 'nett_weight1' },
                    { 
                        data: 'id',
                        render: function (data, type, row) {
                            let dropdownMenu = '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                            '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                                            '<li><a class="dropdown-item edit-item-btn" id="edit' + data + '" onclick="edit(' + data + ')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
                                            '<li><a class="dropdown-item print-item-btn" id="print' + data + '" onclick="print(' + data + ')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li>' +
                                            '<li><a class="dropdown-item remove-item-btn" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>';

                            if (row.is_approved == 'Y') {
                                dropdownMenu += '<li><a class="dropdown-item print-item-btn" id="print' + data + '" onclick="print(' + data + ')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li>';
                            }

                            if (row.is_approved == 'N') {
                                dropdownMenu += '<li><a class="dropdown-item approval-item-btn" id="approve' + data + '" onclick="approve(' + data + ')"><i class="ri-check-fill align-bottom me-2 text-muted"></i> Approval</a></li>';
                            }

                            dropdownMenu += '<li><a class="dropdown-item remove-item-btn" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>';

                            dropdownMenu += '</ul></div>';
                            return dropdownMenu;
                        }
                }
                ],
                "drawCallback": function(settings) {
                    $('#salesInfo').text(settings.json.salesTotal);
                    $('#purchaseInfo').text(settings.json.purchaseTotal);
                    $('#localInfo').text(settings.json.localTotal);
                }   
            });
        });

        $('#addWeight').on('click', function(){
            $('#addModal').find('#id').val("");
            $('#addModal').find('#transactionId').val("");
            $('#addModal').find('#transactionStatus').val("Sales").trigger('change');
            $('#addModal').find('#weightType').val("Normal").trigger('change');
            $('#addModal').find('#transactionDate').val(formatDate2(today));
            $('#addModal').find('#vehiclePlateNo1').val("");
            $('#addModal').find('#vehiclePlateNo2').val("");
            $('#addModal').find('#supplierWeight').val("");
            $('#addModal').find('#bypassReason').val("");
            $('#addModal').find('#customerCode').val("");
            $('#addModal').find('#customerName').val("");
            $('#addModal').find('#supplierCode').val("");
            $('#addModal').find('#supplierName').val("");
            $('#addModal').find('#productCode').val("");
            $('#addModal').find('#productName').val("");
            $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');
            $('#addModal').find('#rawMaterialCode').val("");
            $('#addModal').find('#rawMaterialName').val("");
            $('#addModal').find('#siteCode').val("");
            $('#addModal').find('#siteName').val("");
            $('#addModal').find('#containerNo').val("");
            $('#addModal').find('#invoiceNo').val("");
            $('#addModal').find('#purchaseOrder').val("");
            $('#addModal').find('#deliveryNo').val("");
            $('#addModal').find('#transporterCode').val("");
            $('#addModal').find('#transporter').val("");
            $('#addModal').find('#destinationCode').val("");
            $('#addModal').find('#agent').val("");
            $('#addModal').find('#agentCode').val("");
            $('#addModal').find('#plantCode').val("");
            $('#addModal').find('#plant').val("<?=$plantName ?>").trigger('change');
            $('#addModal').find('#destination').val("");
            $('#addModal').find('#otherRemarks').val("");
            $('#addModal').find('#grossIncoming').val("");
            $('#addModal').find('#grossIncomingDate').val("");
            $('#addModal').find('#tareOutgoing').val("");
            $('#addModal').find('#tareOutgoingDate').val("");
            $('#addModal').find('#nettWeight').val("");
            $('#addModal').find('#grossIncoming2').val("");
            $('#addModal').find('#status').val("");
            $('#addModal').find('#grossIncomingDate2').val("");
            $('#addModal').find('#tareOutgoing2').val("");
            $('#addModal').find('#tareOutgoingDate2').val("");
            $('#addModal').find('#nettWeight2').val("");
            $('#addModal').find('#reduceWeight').val("");
            // $('#addModal').find('#vehicleNo').val(obj.message.final_weight);
            $('#addModal').find('#weightDifference').val("");
            // $('#addModal').find('#id').val(obj.message.is_complete);
            // $('#addModal').find('#vehicleNo').val(obj.message.is_cancel);
            $('#addModal').find("#manualWeightNo").prop("checked", true);
            $('#addModal').find("#manualWeightYes").prop("checked", false);
            //$('#addModal').find('input[name="manualWeight"]').val("false");
            //$('#addModal').find('#indicatorId').val("");
            $('#addModal').find('#weighbridge').val("");
            //$('#addModal').find('#indicatorId2').val("");
            $('#addModal').find('#productDescription').val("");
            $('#addModal').find('#productHigh').val("");
            $('#addModal').find('#productLow').val("");
            $('#addModal').find('#productVariance').val("");
            $('#addModal').find('#orderWeight').val("0");
            $('#addModal').find('#subTotalPrice').val("0.00");
            $('#addModal').find('#sstPrice').val("0.00");
            $('#addModal').find('#productPrice').val("0.00");
            $('#addModal').find('#totalPrice').val("0.00");
            $('#addModal').find('#finalWeight').val("");
            $('#addModal').find("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
            $('#addModal').find('#noOfDrum').val("");

            $('#addModal').modal('show');
            
            $('#weightForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });

        $('#weightType').on('change', function(){
            if($(this).val() == "Container")
            {
                $('#containerCard').show();
            }
            else
            {
                $('#containerCard').hide();
            }
        });

        $('#manualVehicle').on('click', function(){
            if($(this).is(':checked')){
                $(this).val(1);
                $('#vehiclePlateNo1').val('-');
                $('.index-vehicle').hide();
                $('#vehicleNoTxt').show();
            }
            else{
                $(this).val(0);
                $('#vehicleNoTxt').hide();
                $('#vehicleNoTxt').val('');
                $('.index-vehicle').show();
            }
        });

        $('#vehiclePlateNo1').on('change', function(){
            var tare = $('#vehiclePlateNo1 :selected').data('weight') ? parseFloat($('#vehiclePlateNo1 :selected').data('weight')) : 0;
        
            //if($('#transactionStatus').val() == "Purchase" || $(this).val() == "Local"){
                $('#grossIncoming').val(parseFloat(tare).toFixed(0));
                $('#grossIncoming').trigger('keyup');
            /*}
            else{
                $('#tareOutgoing').val(parseFloat(tare).toFixed(0));
                $('#tareOutgoing').trigger('keyup');
            }*/
        });

        $('#vehiclePlateNo2').on('change', function(){
            var tare = $('#vehiclePlateNo2 :selected').data('weight') ? parseFloat($('#vehiclePlateNo2 :selected').data('weight')) : 0;
        
            //if($('#transactionStatus').val() == "Purchase" || $(this).val() == "Local"){
                $('#grossIncoming2').val(parseFloat(tare).toFixed(0));
                $('#grossIncoming2').trigger('keyup');
            /*}
            else{
                $('#tareOutgoing2').val(parseFloat(tare).toFixed(0));
                $('#tareOutgoing2').trigger('keyup');
            }*/
        });

        $('#manualVehicle2').on('click', function(){
            if($(this).is(':checked')){
                $(this).val(1);
                $('#vehiclePlateNo2').val('-');
                $('.index-vehicle2').hide();
                $('#vehicleNoTxt2').show();
            }
            else{
                $(this).val(0);
                $('#vehicleNoTxt2').hide();
                $('#vehicleNoTxt2').val('');
                $('.index-vehicle2').show();
            }
        });

        $('.radio-manual-weight').on('click', function(){
            if($('input[name="manualWeight"]:checked').val() == "true"){
                $('#tareOutgoing').removeAttr('readonly');
                $('#grossIncoming').removeAttr('readonly');
                $('#tareOutgoing2').removeAttr('readonly');
                $('#grossIncoming2').removeAttr('readonly');
            }
            else{
                $('#grossIncoming').attr('readonly', 'readonly');
                $('#tareOutgoing').attr('readonly', 'readonly');
                $('#grossIncoming2').attr('readonly', 'readonly');
                $('#tareOutgoing2').attr('readonly', 'readonly');
            }
        });

        $('#grossIncoming').on('keyup', function(){
            var gross = $(this).val() ? parseFloat($(this).val()) : 0;
            var tare = $('#tareOutgoing').val() ? parseFloat($('#tareOutgoing').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#grossIncomingDate').val(formatDate3(new Date()));
            $('#nettWeight').trigger('change');
        });

        $('#grossCapture').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#grossIncoming').val(parseFloat(text).toFixed(0));
            $('#grossIncoming').trigger('keyup');
        });

        $('#tareOutgoing').on('keyup', function(){
            var tare = $(this).val() ? parseFloat($(this).val()) : 0;
            var gross = $('#grossIncoming').val() ? parseFloat($('#grossIncoming').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#tareOutgoingDate').val(formatDate3(new Date()));
            $('#nettWeight').trigger('change');
        });

        $('#tareCapture').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing').trigger('keyup');
        });

        $('#nettWeight').on('change', function(){
            var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett2 = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
            var current = Math.abs(nett1 - nett2);
            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#currentWeight').trigger('change');
            $('#finalWeight').trigger('change');
        });

        $('#finalWeight').on('change', function(){
            var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett2 = 0;

            if($('#transactionStatus').val() == "Purchase" || $('#transactionStatus').val() == "Local"){
                nett2 = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                nett2 = parseFloat($('#addModal').find('#orderWeight').val());
            }
            
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
        });

        $('#orderWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));

            var previousRecordsTag = $('#addModal').find('#previousRecordsTag').val();

            if (previousRecordsTag == 'false'){
                $('#addModal').find('#balance').val($(this).val());
            }
        });

        $('#supplierWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
            
            var previousRecordsTag = $('#addModal').find('#previousRecordsTag').val();

            if (previousRecordsTag == 'false'){
                $('#addModal').find('#balance').val($(this).val());
            }
        });

        $('#grossIncoming2').on('keyup', function(){
            var gross = $(this).val() ? parseFloat($(this).val()) : 0;
            var tare = $('#tareOutgoing2').val() ? parseFloat($('#tareOutgoing2').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight2').val(nett.toFixed(0));
            $('#grossIncomingDate2').val(formatDate3(new Date()));
            $('#nettWeight2').trigger('change');
        });

        $('#grossCapture2').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#grossIncoming2').val(parseFloat(text).toFixed(0));
            $('#grossIncoming2').trigger('keyup');
        });

        $('#tareOutgoing2').on('keyup', function(){
            var tare = $(this).val() ? parseFloat($(this).val()) : 0;
            var gross = $('#grossIncoming2').val() ? parseFloat($('#grossIncoming2').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight2').val(nett.toFixed(0));
            $('#tareOutgoingDate2').val(formatDate3(new Date()));
            $('#nettWeight2').trigger('change');
        });

        $('#tareCapture2').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing2').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing2').trigger('keyup');
        });

        $('#nettWeight2').on('change', function(){
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
            var current = Math.abs(nett1 - nett2);
            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#currentWeight').trigger('change');
            $('#finalWeight').trigger('change');
        });

        $('#currentWeight').on('change', function(){
            var price = $('#productPrice').val() ? parseFloat($('#productPrice').val()).toFixed(2) : 0.00;
            var weight = $('#currentWeight').text() ? parseFloat($('#currentWeight').text()) : 0;
            var subTotalPrice = price * weight;
            var sstPrice = subTotalPrice * 0.06;
            var totalPrice = subTotalPrice + sstPrice;
            $('#subTotalPrice').val(subTotalPrice.toFixed(2));
            $('#sstPrice').val(sstPrice.toFixed(2));
            $('#totalPrice').val(totalPrice.toFixed(2));
        });

        $('#transactionStatus').on('change', function(){
            if($(this).val() == "Purchase" || $(this).val() == "Local"){
                $('#divWeightDifference').show();
                $('#divSupplierWeight').show();
                $('#addModal').find('#orderWeight').val("");
                $('#addModal').find('#supplierWeight').val("0");
                $('#divSupplierName').show();
                $('#divOrderWeight').hide();
                $('#divCustomerName').hide();
                $('#rawMaterialDisplay').show();
                $('#productNameDisplay').hide();

                if ($(this).val() == "Purchase"){
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                    $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');

                }else{
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Purchase Order');
                    $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Purchase Order');

                }
            }
            else{
                $('#divOrderWeight').show();
                $('#addModal').find('#orderWeight').val("0");
                $('#addModal').find('#supplierWeight').val("");
                $('#divWeightDifference').show();
                $('#divSupplierWeight').hide();
                $('#divSupplierName').hide();
                $('#divCustomerName').show();
                $('#rawMaterialDisplay').hide();
                $('#productNameDisplay').show();
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Purchase Order');
                $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Purchase Order');
            }
        });

        //productName
        $('#productName').on('change', function(){
            $('#productCode').val($('#productName :selected').data('code'));
            $('#productDescription').val($('#productName :selected').data('description'));
            $('#productPrice').val($('#productName :selected').data('price'));
            $('#productHigh').val($('#productName :selected').data('high'));
            $('#productLow').val($('#productName :selected').data('low'));
            $('#productVariance').val($('#productName :selected').data('variance'));

            var price = $('#productPrice').val() ? parseFloat($('#productPrice').val()).toFixed(2) : 0.00;
            var weight = $('#currentWeight').text() ? parseFloat($('#currentWeight').text()) : 0;
            var subTotalPrice = price * weight;
            var sstPrice = subTotalPrice * 0.06;
            var totalPrice = subTotalPrice + sstPrice;
            $('#subTotalPrice').val(subTotalPrice.toFixed(2));
            $('#sstPrice').val(sstPrice.toFixed(2));
            $('#totalPrice').val(totalPrice.toFixed(2));
        });

        //supplierName
        $('#supplierName').on('change', function(){
            $('#supplierCode').val($('#supplierName :selected').data('code'));
        });

        //transporter
        $('#transporter').on('change', function(){
            $('#transporterCode').val($('#transporter :selected').data('code'));
        });

        //destination
        $('#destination').on('change', function(){
            $('#destinationCode').val($('#destination :selected').data('code'));
        });

        //plant
        $('#plant').on('change', function(){
            $('#plantCode').val($('#plant :selected').data('code'));
        });

        // SRP
        $('#agent').on('change', function(){
            $('#agentCode').val($('#agent :selected').data('code'));
        });

        //customerName
        $('#customerName').on('change', function(){
            $('#customerCode').val($('#customerName :selected').data('code'));
        });

        $('input[name="exDel"]').change(function() {
            var selected = $(this).val();
            if (selected == 'true'){
                $("#transporter").val('Own Transportation').trigger('change');
            }else{
                $("#transporter").val('').trigger('change');
            }
        });

        //rawMaterialName
        $('#rawMaterialName').on('change', function(){
            $('#rawMaterialCode').val($('#rawMaterialName :selected').data('code'));
        });

        //siteName
        $('#siteName').on('change', function(){
            $('#siteCode').val($('#siteName :selected').data('code'));
        });

        $('input[name="loadDrum"]').change(function() {
            var selected = $(this).val();
            if (selected == 'true'){
                $("#noOfDrumDisplay").hide();
            }else{
                $("#noOfDrumDisplay").show();
            }
        });

        $('#purchaseOrder').on('change', function (){
            var purchaseOrder = $(this).val();
            var type = $('#addModal').find('#transactionStatus').val();
            $.post('php/getOrderSupplier.php', {code: purchaseOrder, type: type}, function (data){
                var obj = JSON.parse(data);

                if (obj.status == 'success'){
                    var customerName = obj.message.customer_name;
                    var productName = obj.message.product_name;
                    var orderSupplierWeight = obj.message.order_supplier_weight;
                    var finalWeight = obj.message.final_weight;
                    var previousRecordsTag = obj.message.previousRecordsTag;

                    $('#addModal').find('#previousRecordsTag').val(previousRecordsTag);

                    if (previousRecordsTag){
                        $('#addModal').find('#customerName').val(customerName);
                        $('#addModal').find('#productName').val(productName);
                        $('#addModal').find('#balance').val(parseFloat(orderSupplierWeight) - parseFloat(finalWeight));

                        if (type == 'Purchase'){
                            $('#addModal').find('#supplierWeight').val(orderSupplierWeight);
                        }else{
                            $('#addModal').find('#orderWeight').val(orderSupplierWeight);
                        }
                    }else{
                        var weight = 0;
                        if (type == 'Purchase'){
                            weight = $('#addModal').find('#supplierWeight').val();
                        }else{
                            weight = $('#addModal').find('#orderWeight').val();
                        }

                        $('#addModal').find('#balance').val(weight);
                    }
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
            });

        });

        <?php
            if(isset($_GET['weight'])){
                echo 'edit('.$_GET['weight'].');';
            }
        ?>

        <?php
            if(isset($_GET['approve'])){
                echo 'approve('.$_GET['approve'].');';
            }
        ?>
    });

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getWeight.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#transactionId').val(obj.message.transaction_id);
                $('#addModal').find('#transactionStatus').val(obj.message.transaction_status).trigger('change');
                $('#addModal').find('#weightType').val(obj.message.weight_type);
                $('#addModal').find('#transactionDate').val(formatDate2(new Date(obj.message.transaction_date)));

                if(obj.message.transaction_status == "Purchase" || obj.message.transaction_status == "Local"){
                    $('#divWeightDifference').show();
                    $('#divSupplierWeight').show();
                    $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                    $('#addModal').find('#orderWeight').val("");
                    $('#divSupplierName').show();
                    $('#divOrderWeight').hide();
                    $('#divCustomerName').hide();
                }
                else{
                    $('#divOrderWeight').show();
                    $('#addModal').find('#orderWeight').val(obj.message.order_weight);
                    $('#addModal').find('#supplierWeight').val("");
                    $('#divWeightDifference').show();
                    $('#divSupplierWeight').hide();
                    $('#divSupplierName').hide();
                    $('#divCustomerName').show();
                }

                if(obj.message.vehicleNoTxt != null){
                    $('#addModal').find('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                    $('#manualVehicle').val(1);
                    $('#manualVehicle').prop("checked", true);
                    $('.index-vehicle').hide();
                    $('#vehicleNoTxt').show();
                }
                else{
                    $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1);
                    $('#manualVehicle').val(0);
                    $('#manualVehicle').prop("checked", false);
                    $('.index-vehicle').show();
                    $('#vehicleNoTxt').hide();
                }

                if(obj.message.vehicleNoTxt2 != null){
                    $('#addModal').find('#vehicleNoTxt2').val(obj.message.vehicleNoTxt2);
                    $('#manualVehicle2').val(1);
                    $('#manualVehicle2').prop("checked", true);
                    $('.index-vehicle2').hide();
                    $('#vehicleNoTxt2').show();
                }
                else{
                    $('#addModal').find('#vehiclePlateNo2').val(obj.message.lorry_plate_no2);
                    $('#manualVehicle2').val(0);
                    $('#manualVehicle2').prop("checked", false);
                    $('.index-vehicle2').show();
                    $('#vehicleNoTxt2').hide();
                }
                
                $('#addModal').find('#customerCode').val(obj.message.customer_code);
                $('#addModal').find('#customerName').val(obj.message.customer_name);
                $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                $('#addModal').find('#supplierName').val(obj.message.supplier_name);
                $('#addModal').find('#productCode').val(obj.message.product_code);
                if (obj.message.ex_del == 'EX'){
                    $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true);
                }else{
                    $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true);
                }
                $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
                $('#addModal').find('#rawMaterialName').val(obj.message.raw_mat_name);
                $('#addModal').find('#siteCode').val(obj.message.site_code);
                $('#addModal').find('#siteName').val(obj.message.site_name);
                $('#addModal').find('#containerNo').val(obj.message.container_no);
                $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                $('#addModal').find('#transporter').val(obj.message.transporter);
                $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                $('#addModal').find('#agent').val(obj.message.agent_name);
                $('#addModal').find('#agentCode').val(obj.message.agent_code);
                $('#addModal').find('#plant').val(obj.message.plant_name);
                $('#addModal').find('#plantCode').val(obj.message.plant_code);
                $('#addModal').find('#destination').val(obj.message.destination);
                $('#addModal').find('#otherRemarks').val(obj.message.remarks);
                $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1); console.lo
                $('#addModal').find('#grossIncomingDate').val(formatDate3(new Date(obj.message.gross_weight1_date)));
                $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                $('#addModal').find('#tareOutgoingDate').val(obj.message.tare_weight1_date != null ? formatDate3(new Date(obj.message.tare_weight1_date)) : '');
                $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);
                $('#addModal').find('#grossIncoming2').val(obj.message.gross_weight2);
                $('#addModal').find('#grossIncomingDate2').val(obj.message.gross_weight2_date != null ? formatDate3(new Date(obj.message.gross_weight2_date)) : '');
                $('#addModal').find('#tareOutgoing2').val(obj.message.tare_weight2);
                $('#addModal').find('#tareOutgoingDate2').val(obj.message.tare_weight2_date != null ? formatDate3(new Date(obj.message.tare_weight2_date)) : '');
                $('#addModal').find('#nettWeight2').val(obj.message.nett_weight2);
                $('#addModal').find('#reduceWeight').val(obj.message.reduce_weight);
                $('#addModal').find('#weightDifference').val(obj.message.weight_different);

                if(obj.message.manual_weight == 'true'){
                    $("#manualWeightYes").prop("checked", true);
                    $("#manualWeightNo").prop("checked", false);
                    $('#manualWeightYes').trigger('click');
                }
                else{
                    $("#manualWeightYes").prop("checked", false);
                    $("#manualWeightNo").prop("checked", true);
                    $('#manualWeightNo').trigger('click');
                }

                $('#addModal').find('#indicatorId').val(obj.message.indicator_id);
                $('#addModal').find('#weighbridge').val(obj.message.weighbridge_id);
                $('#addModal').find('#indicatorId2').val(obj.message.indicator_id_2);
                $('#addModal').find('#productName').val(obj.message.product_name).trigger('change');
                $('#addModal').find('#productDescription').val(obj.message.product_description);
                $('#addModal').find('#subTotalPrice').val(obj.message.product_description);
                $('#addModal').find('#sstPrice').val(obj.message.product_description);
                $('#addModal').find('#totalPrice').val(obj.message.total_price);
                $('#addModal').find('#finalWeight').val(obj.message.final_weight);

                if (obj.message.load_drum == 'LOAD'){
                    $('#addModal').find("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
                }else{
                    $('#addModal').find("input[name='loadDrum'][value='false']").prop("checked", true).trigger('change');
                }

                $('#addModal').find('#noOfDrum').val(obj.message.no_of_drum);

                $('#addModal').modal('show');
            
                $('#weightForm').validate({
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    }
                });
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            $('#spinnerLoading').hide();
        });
    }

    function approve(id){
        $('#spinnerLoading').show();
        $.post('php/getWeight.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#approvalModal').find('#id').val(obj.message.id);
                $('#approvalModal').find('#statusA').val('');
                $('#approvalModal').find('#reasons').val('');
                $('#approvalModal').modal('show');
            
                $('#approvalForm').validate({
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    }
                });
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            $('#spinnerLoading').hide();
        });
    }

    function deactivate(id){
        $('#spinnerLoading').show();
        $.post('php/deleteWeight.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                table.ajax.reload();
                $('#spinnerLoading').hide();
                $("#successBtn").attr('data-toast-text', obj.message);
                $("#successBtn").click();
            }
            else if(obj.status === 'failed'){
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
        });
    }

    function print(id) {
        $.post('php/print.php', {userID: id, file: 'weight'}, function(data){
            var obj = JSON.parse(data);

            if(obj.status === 'success'){
                var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                printWindow.document.write(obj.message);
                printWindow.document.close();
                setTimeout(function(){
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }
            else if(obj.status === 'failed'){
                toastr["error"](obj.message, "Failed:");
            }
            else{
                toastr["error"]("Something wrong when activate", "Failed:");
            }
        });
    }
    </script>
</body>
</html>