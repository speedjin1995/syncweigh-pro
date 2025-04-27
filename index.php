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

if($plantId != null && count($plantId) > 0){
    $stmt2 = $db->prepare("SELECT * from Plant WHERE plant_code = ?");
    $stmt2->bind_param('s', $plantId[0]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
        
    if(($row2 = $result2->fetch_assoc()) !== null){
        $plantName = $row2['name'];
    }
}

//   $lots = $db->query("SELECT * FROM lots WHERE deleted = '0'");
$vehicles = $db->query("SELECT * FROM Vehicle WHERE status = '0'");
$vehicles2 = $db->query("SELECT * FROM Vehicle WHERE status = '0'");
$customer = $db->query("SELECT * FROM Customer WHERE status = '0'");
$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0'");
$customer3 = $db->query("SELECT * FROM Customer WHERE status = '0'");
$driver = $db->query("SELECT * FROM Driver WHERE status = '0'");
$product = $db->query("SELECT * FROM Product WHERE status = '0'");
$product2 = $db->query("SELECT * FROM Product WHERE status = '0'");
$transporter = $db->query("SELECT * FROM Transporter WHERE status = '0'");
$destination = $db->query("SELECT * FROM Destination WHERE status = '0'");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0'");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0'");
$unit = $db->query("SELECT * FROM Unit WHERE status = '0'");

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
}

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
else{
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
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

        .product-table thead tr th{
            border:none;
            font-weight: bold;
        }

        .product-table thead tr th span{
            border-bottom: 1px solid black;
        }

        .product-table tbody tr td{
            border: none;
        }

        .product-table tbody tr td span{
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .product-table .align-center {
            text-align: center;
        }
    </style>
</head>

<?php include 'layouts/body.php'; ?>

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
                                    <div class="card-header fs-5" href="#collapseSearch" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseSearch" style="background-color: #a8d190;">
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
                                                            <label for="statusSearch" class="form-label">Status</label>
                                                            <select id="statusSearch" class="form-select">
                                                                <option selected>-</option>
                                                                <option value="Sales">Sales</option>
                                                                <option value="Purchase">Purchase</option>
                                                                <option value="Local">Local</option>
                                                                <option value="Misc">Misc</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="customerNoSearch" class="form-label" id="labelCustomer">Customer Name</label>
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
                                                                <!--option value="Container">Container</option-->
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" style="display:none;">
                                                        <div class="mb-3">
                                                            <label for="batchNoSearch" class="form-label">Status</label>
                                                            <select id="batchNoSearch" class="form-select">
                                                                <option selected>-</option>
                                                                <option value="N" selected>Pending</option>
                                                                <option value="Y">Complete</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->                                                
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="ForminputState" class="form-label">Product</label>
                                                            <select id="transactionStatusSearch" class="form-select" >
                                                                <option selected>-</option>
                                                                <?php while($rowProductF=mysqli_fetch_assoc($product2)){ ?>
                                                                    <option value="<?=$rowProductF['product_code'] ?>"><?=$rowProductF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="plantSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="plantSearch" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant2)){ ?>
                                                                    <option value="<?=$rowPlantF['plant_code'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-success" id="filterSearch"><i class="bx bx-search-alt"></i> Search</button>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end row-->
                                            </form>                                                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate card-success">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-truncate mb-0" style="color: black;">
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
                                                    <span class="avatar-title bg-soft-danger rounded fs-3">
                                                        <i class="bx bx-dollar-circle text-danger"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->

                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate card-warning">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-truncate mb-0" style="color: black;">
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

                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate card-info">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-truncate mb-0" style="color: black;">
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

                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate card-danger">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-truncate mb-0" style="color: black;">
                                                    Misc</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                            class="counter-value" id="miscInfo">0</span>
                                                    </h4>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                                        <i class="bx bx-ruler text-warning"></i>
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
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Daily Weighing Transaction Record</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="weightForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-4">
                                                                                <div class="hstack gap-2 justify-content-center">
                                                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                                                        <div class="card bg-success" style="margin-bottom: 0.3rem;">
                                                                                            <div class="card-body">
                                                                                                <div class="d-flex justify-content-between align-items-center justify-content-center">
                                                                                                    <div class="d-flex align-items-center justify-content-center">
                                                                                                    <!-- <div> -->
                                                                                                        <h4 class="mt-4 ff-secondary fw-semibold display-5 text-white"><span class="counter-value" id="indicatorWeight">0</span> Kg</h4>
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
                                                                            <div class="col-lg-8">
                                                                                <div class="row">
                                                                                    <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                        <div class="row">
                                                                                            <label for="weightType" class="col-sm-4 col-form-label">Weight Type *</label>
                                                                                            <div class="col-sm-8">
                                                                                                <select id="weightType" name="weightType" class="form-select">
                                                                                                    <option selected>Normal</option>
                                                                                                    <!-- <option>Container</option> -->
                                                                                                </select>   
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                        <div class="row">
                                                                                            <label for="transactionId" class="col-sm-4 col-form-label">Transaction ID *</label>
                                                                                            <div class="col-sm-8">
                                                                                                <input type="text" class="form-control input-readonly" id="transactionId" name="transactionId" placeholder="Transaction ID" readonly>                                                                                  
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                        <div class="row">
                                                                                            <label for="transactionStatus" class="col-sm-4 col-form-label">Transaction Status *</label>
                                                                                            <div class="col-sm-8">
                                                                                                <select id="transactionStatus" name="transactionStatus" class="form-select">
                                                                                                    <option value="Sales" selected>Sales</option>
                                                                                                    <option value="Purchase">Purchase</option>
                                                                                                    <option value="Local">Local</option>
                                                                                                    <option value="Misc">Misc</option>
                                                                                                </select>  
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                        <div class="row">
                                                                                            <label for="transactionDate" class="col-sm-4 col-form-label">Transaction Date *</label>
                                                                                            <div class="col-sm-8">
                                                                                                <input type="date" class="form-control" data-provider="flatpickr" id="transactionDate" name="transactionDate" required>
                                                                                                <div class="invalid-feedback">
                                                                                                    Please fill in the field.
                                                                                                </div>    
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                        <div class="row">
                                                                                            <label for="plant" class="col-sm-4 col-form-label">Plant *</label>
                                                                                            <div class="col-sm-8">
                                                                                                <select class="form-select select2" id="plant" name="plant" required>
                                                                                                    <?php while($rowPlant=mysqli_fetch_assoc($plant)){ ?>
                                                                                                        <option value="<?=$rowPlant['name'] ?>" data-code="<?=$rowPlant['plant_code'] ?>"><?=$rowPlant['name'] ?></option>
                                                                                                    <?php } ?>
                                                                                                </select>        
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div><!-- end card body -->
                                                                </div><!-- end card -->
                                                            </div><!-- end col -->
                                                        </div><!-- end row -->
                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
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
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
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
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
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
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                        </div><!-- end row -->
                                                                        <div class="row">
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
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl --> 
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
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="purchaseOrder" class="col-sm-4 col-form-label">Purchase Order</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="purchaseOrder" name="purchaseOrder" placeholder="Purchase Order">
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl --> 
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divOrderWeight">
                                                                                <div class="row">
                                                                                    <label for="supplyWeight" class="col-sm-4 col-form-label">Order Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="orderWeight" name="orderWeight" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divSupplierWeight" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="supplierWeight" class="col-sm-4 col-form-label">Supply Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="supplierWeight" name="supplierWeight"  placeholder="Supplier Weight">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl --> 
                                                                        </div><!-- end row -->
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divDriverName">
                                                                                <div class="row">
                                                                                    <label for="driverName" class="col-sm-4 col-form-label">Driver Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select js-choice" id="driverName" name="driverName">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowDriver=mysqli_fetch_assoc($driver)){ ?>
                                                                                                <option data-ic="<?=$rowDriver['driver_ic'] ?>" value="<?=$rowDriver['driver_name'] ?>" data-code="<?=$rowDriver['driver_code'] ?>" data-phone="<?=$rowDriver['driver_phone'] ?>"><?=$rowDriver['driver_name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="Invoice No">
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divWeightDifference">
                                                                                <div class="row">
                                                                                    <label for="weightDifference" class="col-sm-4 col-form-label">Weight Difference</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="weightDifference" name="weightDifference" placeholder="Weight Difference" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                        </div><!-- end row -->
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divDriverICNo">
                                                                                <div class="row">
                                                                                    <label for="driverICNo" class="col-sm-4 col-form-label">Driver I/C No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="driverICNo" name="driverICNo" placeholder="Driver IC">
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <!-- <label for="productName" class="col-sm-4 col-form-label">Product Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="productName" name="productName" >
                                                                                            <option selected="-">-</option>
                                                                                            
                                                                                        </select>                                                                                        
                                                                                    </div> -->
                                                                                    <label for="deliveryNo" class="col-sm-4 col-form-label">Delivery No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="deliveryNo" name="deliveryNo" placeholder="Delivery No">
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="reduceWeight" class="col-sm-4 col-form-label">Overall Reduce Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="reduceWeight" name="reduceWeight" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                        </div><!-- end row -->
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divDriverPhone">
                                                                                <div class="row">
                                                                                    <label for="driverPhone" class="col-sm-4 col-form-label">Driver Contact No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="driverPhone" name="driverPhone" placeholder="Driver Phone">
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                        </div><!-- end row -->
                                                                    </div><!-- end card body -->
                                                                </div><!-- end card -->
                                                            </div><!-- end col -->
                                                        </div><!-- end row -->
                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
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
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="grossIncomingDate" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly" id="grossIncomingDate" name="grossIncomingDate" readonly>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="grossIncoming" class="col-sm-4 col-form-label">Incoming Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="grossIncoming" name="grossIncoming" placeholder="0" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                            <button class="input-group-text btn btn-success fs-5" id="grossCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                        </div>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                        </div><!-- end row -->
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" style="display:none;">
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="tareOutgoingDate" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly" id="tareOutgoingDate" name="tareOutgoingDate" readonly>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="tareOutgoing" class="col-sm-4 col-form-label">Outgoing Weight</label>
                                                                                    <div class="col-sm-8">                                                                                     
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="tareOutgoing" name="tareOutgoing" placeholder="0" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                            <button class="input-group-text btn btn-success fs-5" id="tareCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                        </div>                                                                                       
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                        </div><!-- end row -->
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="remarks" class="col-sm-4 col-form-label">Remarks</label>
                                                                                    <div class="col-sm-8">
                                                                                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Remarks"></textarea>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="estimateLoading" class="col-sm-4 col-form-label">Estimate Loading (Mins)</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly" id="estimateLoading" name="estimateLoading" readonly>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="nettWeight" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="nettWeight" name="nettWeight" placeholder="0" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl --> 
                                                                        </div><!-- end row -->
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" style="display:none;">
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" style="display:none;">
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl -->
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="finalWeight" class="col-sm-4 col-form-label">Final Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="finalWeight" name="finalWeight" placeholder="0" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div><!-- end col -->
                                                                                </div><!-- end row -->
                                                                            </div><!-- end col-xxl --> 
                                                                        </div><!-- end row -->
                                                                    </div><!-- end card body -->
                                                                </div><!-- end card -->
                                                            </div><!-- end col -->
                                                        </div><!-- end row -->
                                                        <div class="col-xxl-4 col-lg-4">
                                                            <div class="row">
                                                                <div class="col-xxl-3 col-lg-3">
                                                                    <div class="hstack">
                                                                        <div class="card bg-light">
                                                                            <div class="row">
                                                                                <button type="button" class="btn btn-success add-product" id="addProduct">Add Product</button>
                                                                            </div>
                                                                        </div><!-- end card -->
                                                                    </div>
                                                                </div><!-- end col-xxl -->
                                                                <?php
                                                                    if($_SESSION["roles"] == 'ADMIN' || $_SESSION["roles"] == 'SADMIN'){
                                                                        echo '<div class="col-xxl-9 col-lg-9">
                                                                            <div class="row">
                                                                                <label for="manualPrice" class="col-sm-4 col-form-label">Manual Price</label>
                                                                                <div class="col-sm-8">
                                                                                    <div class="form-check align-radio mr-2">
                                                                                        <input class="form-check-input radio-manual-price" type="radio" name="manualPrice" id="manualPriceYes" value="true">
                                                                                        <label class="form-check-label" for="manualPriceYes">
                                                                                            Yes
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="form-check align-radio">
                                                                                        <input class="form-check-input radio-manual-price" type="radio" name="manualPrice" id="manualPriceNo" value="false" checked>
                                                                                        <label class="form-check-label" for="manualPriceNo">
                                                                                            No
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>';
                                                                    }
                                                                ?>
                                                                <!-- <div class="col-xxl-9 col-lg-9">
                                                                    <div class="row">
                                                                        <label for="manualPrice" class="col-sm-4 col-form-label">Manual Price</label>
                                                                        <div class="col-sm-8">
                                                                            <div class="form-check align-radio mr-2">
                                                                                <input class="form-check-input radio-manual-price" type="radio" name="manualPrice" id="manualPriceYes" value="true">
                                                                                <label class="form-check-label" for="manualPriceYes">
                                                                                    Yes
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check align-radio">
                                                                                <input class="form-check-input radio-manual-price" type="radio" name="manualPrice" id="manualPriceNo" value="false" checked>
                                                                                <label class="form-check-label" for="manualPriceNo">
                                                                                    No
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div> -->
                                                            </div><!-- end row -->
                                                        </div><!-- end col -->
                                                        <div class="col-xxl-12 col-lg-12" id="multipleCard">
                                                            <div class="card bg-light">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <table class="table table-primary" style="text-align: center;">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="5%">No</th>
                                                                                    <th width="15%">Part Code</th>
                                                                                    <th>Products Description</th>
                                                                                    <th>Percentage (%)</th>
                                                                                    <th>Item Weight (kg)</th>
                                                                                    <th>Unit Price (RM)</th>
                                                                                    <th>Total Price (RM)</th>
                                                                                    <!-- <th>Variance (KG)</th> -->
                                                                                    <th>Action</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="productTable"></tbody>
                                                                            <tfoot>
                                                                                <th colspan="6">Total Price (RM)</th>
                                                                                <th><input type="number" class="form-control" id="totalPrice" name="totalPrice" style="background-color:white;" value="0" readonly></th>
                                                                                <th></th>
                                                                            </tfoot>
                                                                        </table>                                            
                                                                    </div><!-- end row -->                                                    
                                                                </div><!-- end card-body -->
                                                            </div><!-- end card -->
                                                        </div><!-- end col -->
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger" id="submitWeightPrint">Submit & Print</button>
                                                                <button type="button" class="btn btn-success" id="submitWeight">Submit</button>
                                                            </div>
                                                        </div><!--end col-->
                                                        
                                                        <input type="hidden" id="containerNo" name="containerNo">
                                                        <input type="hidden" id="bypassReason" name="bypassReason">
                                                        <!-- <input type="hidden" id="finalWeight" name="finalWeight"> -->
                                                        <input type="hidden" id="customerCode" name="customerCode">
                                                        <input type="hidden" id="destinationCode" name="destinationCode">
                                                        <input type="hidden" id="driverCode" name="driverCode">
                                                        <!-- <input type="hidden" id="driverPhone" name="driverPhone"> -->
                                                        <input type="hidden" id="plantCode" name="plantCode">
                                                        <input type="hidden" id="status" name="status">
                                                        <input type="hidden" id="productCode" name="productCode">
                                                        <input type="hidden" id="productDescription" name="productDescription">
                                                        <input type="hidden" id="productPrice" name="productPrice">
                                                        <input type="hidden" id="productHigh" name="productHigh">
                                                        <input type="hidden" id="productLow" name="productLow">
                                                        <input type="hidden" id="productVariance" name="productVariance">
                                                        <input type="hidden" id="transporterCode" name="transporterCode">
                                                        <input type="hidden" id="supplierCode" name="supplierCode">
                                                        <input type="hidden" id="id" name="id">  
                                                        <input type="hidden" id="weighbridge" name="weighbridge" value="Weigh1">
                                                    </form>
                                                </div><!-- end modal-body -->
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
                                                            <label for="password" class="col-sm-4 col-form-label">Password</label>
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
                                                                <button type="button" class="btn btn-success" id="submitBypass">Submit</button>
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
                                                                <button type="button" class="btn btn-success" id="submitApproval">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- end col -->
                            </div> <!-- end row-->

                            <div class="row">
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header" style="background-color: #a8d190;">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0">Previous Records</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <button type="button" id="multiDeactivate" class="btn btn-danger waves-effect waves-light">
                                                                    <i class="fa-solid fa-ban align-middle me-1"></i>
                                                                    Delete Weight
                                                                </button>
                                                                <button type="button" id="addWeight" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
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
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th>Transaction <br>Id</th>
                                                                    <th>Weight <br> Status</th>
                                                                    <th>Weight <br> Type</th>
                                                                    <th>Vehicle</th>
                                                                    <!--th>Product</th-->
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
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->
    </div>

    <script type="text/html" id="productDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="no" name="no" readonly>
                <input type="text" class="form-control" id="weightProductId" name="weightProductId" hidden>
            </td>
            <td>
                <select class="form-control form-select" style="width: 100%; background-color:white;" id="productPartCode" name="productPartCode" required>
                    <option selected="-">-</option>    
                    <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                        <option 
                            value="<?=$rowProduct['product_code'] ?>" 
                            data-price="<?=$rowProduct['price'] ?>" 
                            data-code="<?=$rowProduct['name'] ?>" 
                            data-high="<?=$rowProduct['high'] ?>" 
                            data-low="<?=$rowProduct['low'] ?>" 
                            data-variance="<?=$rowProduct['variance'] ?>" 
                            data-description="<?=$rowProduct['description'] ?>">
                            <?=$rowProduct['product_code'] ?> - <?=$rowProduct['name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="products" name="products" style="background-color:white;" readonly required>
            </td>
            <td>
                <input type="number" class="form-control" id="productPercentage" name="productPercentage" style="background-color:white;" value="0" required>
            </td>
            <td>
                <input type="number" class="form-control" id="productItemWeight" name="productItemWeight" style="background-color:white;" value="0" readonly required>
            </td>
            <td>
                <input type="number" class="form-control input-readonly" id="productUnitPrice" name="productUnitPrice" value="0" readonly required>
            </td>
            <td>
                <input type="number" class="form-control" id="productTotalPrice" name="productTotalPrice" style="background-color:white;" value="0" required>
            </td>
            <!-- <td>
                <input type="number" class="form-control" id="productActualWeight" name="productActualWeight" style="background-color:white;" value="0">
                <input type="hidden" id="productActualWeightHidden" name="productActualWeightHidden">
            </td>
            <td>
                <input type="date" class="form-control" data-provider="flatpickr" id="productStartDate" name="productStartDate" style="background-color:white;">
            </td>
            <td>
                <input type="date" class="form-control" data-provider="flatpickr" id="productEndDate" name="productEndDate" style="background-color:white;">
            </td>
            <td>
                <input type="number" class="form-control" id="productVariance" name="productVariance" style="background-color:white;" value="0">
                <input type="hidden" id="productVarianceHidden" name="productVarianceHidden">
            </td> -->
            <td class="d-flex" style="text-align:center">
                <!-- <button class="btn btn-success me-2" id="productWeightCapture" type="button">
                    <i class="mdi mdi-sync"></i>
                </button> -->
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times">x</i>
                </button>
            </td>
        </tr>
    </script>

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
    var rowCount = $("#productTable").find(".details").length;
    
    $(function () {
        var userRole = '<?=$role ?>';
        var ind = '<?=$indicator ?>';
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

        // Date picker
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

        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#weightTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        if (userRole == 'SADMIN' || userRole == 'ADMIN' || userRole == 'MANAGER'){
            $('#plantSearchDisplay').show();
        }else{
            $('#plantSearchDisplay').hide();
        }

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
        var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
        var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
        var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
        var batchNoI = $('#batchNoSearch').val() ? $('#batchNoSearch').val() : '';
        var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
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
                    product: transactionStatusI,
                    plant: plantNoI,
                } 
            },
            'columns': [
                {
                    // Add a checkbox with a unique ID for each row
                    data: 'id', // Assuming 'serialNo' is a unique identifier for each row
                    className: 'select-checkbox',
                    orderable: false,
                    render: function (data, type, row) {
                        return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                    }
                },
                { data: 'transaction_id' },
                { data: 'transaction_status' },
                { data: 'weight_type' },
                { data: 'lorry_plate_no1' },
                //{ data: 'product_description' },
                { data: 'gross_weight1' },
                { data: 'gross_weight1_date' },
                { data: 'tare_weight1' },
                { data: 'tare_weight1_date' },
                { data: 'nett_weight1' },
                { 
                    data: 'id',
                    className: 'action-button',
                    render: function (data, type, row) {
                        let dropdownMenu = '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">';

                        if (userRole == 'ADMIN' || userRole == 'SADMIN'){
                            dropdownMenu += '<li><a class="dropdown-item edit-item-btn" id="edit' + data + '" onclick="edit(' + data + ')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>';
                        }

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
                $('#miscInfo').text(settings.json.miscTotal);
            }   
        });

        // Add event listener for opening and closing details on row click
        $('#weightTable tbody').on('click', 'tr', function (e) {
            var tr = $(this); // The row that was clicked
            var row = table.row(tr);

            // Exclude specific td elements by checking the event target
            if ($(e.target).closest('td').hasClass('select-checkbox') || $(e.target).closest('td').hasClass('action-button')) {
                return;
            }

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.post('php/getWeight.php', { userID: row.data().id, format: 'EXPANDABLE' }, function (data) {
                    var obj = JSON.parse(data);
                    if (obj.status === 'success') {
                        row.child(format(obj.message)).show();
                        tr.addClass("shown");
                    }
                });
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

            // let totalSum = 0;

            // $('[id^="productPercentage"]').each(function() {
            //     let value = parseFloat($(this).val()) || 0; // Convert value to a number, default to 0 if NaN
            //     totalSum += value;
            // });

            // if(totalSum > 100){
            //     alert("⚠️ Total percentage cannot exceed 100!");
            //     event.preventDefault(); // Prevent default action (like form submission)
            //     return;
            // }

            pass = true;

            if(pass && $('#weightForm').valid()){
                $('#spinnerLoading').show();

                let productRow = $('#addModal').find($('#productTable tr'));

                if (productRow.length > 0 ) {
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
                }else{
                    $('#spinnerLoading').hide();
                    alert("Product cannot be empty. Please add product.");
                }
            }
            /*else{
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
            }*/
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

            pass = true;

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
            /*else{
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
            }*/
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
                    else if(ind == 'BDI'){
                        if(data.includes("GS")){
                            var text = data.split(" ");
                            var text2 = text[text.length - 1];
                            text2 = text2.replace("kg", "").replace("KG", "").replace("Kg", "");
                            $('#indicatorWeight').html(text2);
                            $('#indicatorConnected').addClass('bg-primary');
                            $('#checkingConnection').removeClass('bg-danger');
                        }
                    }
                    else if(ind == 'EX2001'){
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
            var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
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
                        product: transactionStatusI,
                        plant: plantNoI,
                    } 
                },
                'columns': [
                    {
                        // Add a checkbox with a unique ID for each row
                        data: 'id', // Assuming 'serialNo' is a unique identifier for each row
                        className: 'select-checkbox',
                        orderable: false,
                        render: function (data, type, row) {
                            return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                        }
                    },
                    { data: 'transaction_id' },
                    { data: 'transaction_status' },
                    { data: 'weight_type' },
                    { data: 'lorry_plate_no1' },
                    //{ data: 'product_description' },
                    { data: 'gross_weight1' },
                    { data: 'gross_weight1_date' },
                    { data: 'tare_weight1' },
                    { data: 'tare_weight1_date' },
                    { data: 'nett_weight1' },
                    { 
                        data: 'id',
                        className: 'action-button',
                        render: function (data, type, row) {
                            let dropdownMenu = '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                            '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">';

                            if (userRole == 'ADMIN' || userRole == 'SADMIN'){
                                dropdownMenu += '<li><a class="dropdown-item edit-item-btn" id="edit' + data + '" onclick="edit(' + data + ')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>';
                            }

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
                    $('#miscInfo').text(settings.json.miscTotal);
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
            $('#addModal').find('#driverCode').val("");
            $('#addModal').find('#driverName').val("");
            $('#addModal').find('#driverICNo').val("");
            $('#addModal').find('#supplierCode').val("");
            $('#addModal').find('#supplierName').val("");
            $('#addModal').find('#productCode').val("");
            $('#addModal').find('#plantCode').val("");
            $('#addModal').find('#plant').val("<?=$plantName ?>").trigger('change');
            $('#addModal').find('#productName').val("");
            $('#addModal').find('#containerNo').val("");
            $('#addModal').find('#invoiceNo').val("");
            $('#addModal').find('#purchaseOrder').val("");
            $('#addModal').find('#supplyWeight').val("");
            $('#addModal').find('#deliveryNo').val("");
            $('#addModal').find('#transporterCode').val("");
            $('#addModal').find('#transporter').val("");
            $('#addModal').find('#destinationCode').val("");
            $('#addModal').find('#destination').val("");
            $('#addModal').find('#remarks').val("");
            $('#addModal').find('#grossIncoming').val("");
            $('#addModal').find('#grossIncomingDate').val("");
            $('#addModal').find('#tareOutgoing').val("");
            $('#addModal').find('#tareOutgoingDate').val("");
            $('#addModal').find('#nettWeight').val("");
            $('#addModal').find('#estimateLoading').val("");
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
            // $('#addModal').find("#manualWeightNo").prop("checked", true);
            // $('#addModal').find("#manualWeightYes").prop("checked", false);
            $('#addModal').find('#manualWeightNo').trigger('click');
            $('#addModal').find("#manualPriceNo").prop("checked", true);
            $('#addModal').find("#manualPriceYes").prop("checked", false);
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
            $('#addModal').find('#productTable').html('');
            rowCount = 0;
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
                //$('#grossIncoming').val(parseFloat(tare).toFixed(0));
                //$('#grossIncoming').trigger('keyup');
            /*}
            else{
                $('#tareOutgoing').val(parseFloat(tare).toFixed(0));
                $('#tareOutgoing').trigger('keyup');
            }*/
        });

        $('#vehiclePlateNo2').on('change', function(){
            var tare = $('#vehiclePlateNo2 :selected').data('weight') ? parseFloat($('#vehiclePlateNo2 :selected').data('weight')) : 0;
        
            //if($('#transactionStatus').val() == "Purchase" || $(this).val() == "Local"){
                //$('#grossIncoming2').val(parseFloat(tare).toFixed(0));
                //$('#grossIncoming2').trigger('keyup');
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

        $('.radio-manual-price').on('click', function(){
            if($('input[name="manualPrice"]:checked').val() == "true"){
                $('[id^="productUnitPrice"]').removeAttr('readonly');
            }
            else{
                $('[id^="productUnitPrice"]').attr('readonly', true);
            }
        });

        $('#grossIncoming').on('keyup', function(){
            var gross = $(this).val() ? parseFloat($(this).val()) : 0;
            var tare = $('#tareOutgoing').val() ? parseFloat($('#tareOutgoing').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#grossIncomingDate').val(formatDate3(new Date())).trigger('keyup');
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
            $('#tareOutgoingDate').val(formatDate3(new Date())).trigger('keyup');
            $('#nettWeight').trigger('change');
        });

        $('#tareCapture').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing').trigger('keyup');
        });

        $('#grossIncomingDate').on('keyup', function(){
            let startDate = $(this).val();
            let endDate = $('#tareOutgoingDate').val();

            if(startDate && endDate) {
                let start = parseDate(startDate);
                let end = parseDate(endDate);

                let diffInMinutes = Math.ceil((end - start) / 60000); // Convert milliseconds to minutes
                $('#estimateLoading').val(Math.abs(diffInMinutes));
            }
        });

        $('#tareOutgoingDate').on('keyup', function(){
            let startDate = $('#grossIncomingDate').val();
            let endDate = $(this).val();
            
            if(startDate && endDate) {
                let start = parseDate(startDate);
                let end = parseDate(endDate);

                let diffInMinutes = Math.ceil((end - start) / 60000); // Convert milliseconds to minutes
                $('#estimateLoading').val(Math.abs(diffInMinutes));
            }
        });

        $('#nettWeight').on('change', function(){
            var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
            var reduce = $('#reduceWeight').val() ? parseFloat($('#reduceWeight').val()) : 0;
            var final = Math.abs(nett1 - reduce);
            // $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(final.toFixed(0));
            // $('#currentWeight').trigger('change');
            $('#finalWeight').trigger('change');
        });

        $('#reduceWeight').on('change', function(){
            var reduce = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
            var final = Math.abs(nett1 - reduce);
            // $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(final.toFixed(0));
            // $('#currentWeight').trigger('change');
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

            // Loop directly through productItemWeight fields
            $('#productTable input[id^="productItemWeight"]').each(function() {
                var row = $(this).closest('.details'); // Find the closest row
                var productPercentage = parseFloat(row.find('input[id^="productPercentage"]').val()) || 0;
                var productItemWeight = (finalWeight * productPercentage) / 100;

                // Update the productItemWeight field
                $(this).val(productItemWeight.toFixed(2));

                // Trigger change on productUnitPrice to recalculate dependent values
                row.find('input[id^="productUnitPrice"]').trigger('change');
            });
        });

        $('#orderWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
        });

        $('#supplierWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
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
            }
            else{
                $('#divOrderWeight').show();
                $('#addModal').find('#orderWeight').val("0");
                $('#addModal').find('#supplierWeight').val("");
                $('#divWeightDifference').show();
                $('#divSupplierWeight').hide();
                $('#divSupplierName').hide();
                $('#divCustomerName').show();
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

        //driver
        $('#driverName').on('change', function(){
            $('#driverCode').val($('#driverName :selected').data('code'));
            $('#driverPhone').val($('#driverName :selected').data('phone'));
            $('#driverICNo').val($('#driverName :selected').data('ic')); // 
        });

        //customerName
        $('#customerName').on('change', function(){
            $('#customerCode').val($('#customerName :selected').data('code'));
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

        $('#statusSearch').on('change', function () {
            var status = $(this).val();

            if(status == 'Sales' || status == '-') {
                $('#labelCustomer').text('Customer Name');

                <?php 
                $options = [];
                while($rowPF = mysqli_fetch_assoc($customer3)){
                    $options[] = ['value' => $rowPF['customer_code'], 'text' => $rowPF['name']];
                }
                ?>
                var options = <?= json_encode($options) ?>;
            } else {
                $('#labelCustomer').text('Supplier Name');

                <?php 
                $options = [];
                while($rowPF = mysqli_fetch_assoc($supplier2)){
                    $options[] = ['value' => $rowPF['supplier_code'], 'text' => $rowPF['name']];
                }
                ?>
                var options = <?= json_encode($options) ?>;
            }

            var $select = $('#customerNoSearch');
            $select.empty(); // clear existing options if needed

            // Add default option
            var $defaultOption = $('<option></option>')
                .val('-')
                .text('-');
            $select.append($defaultOption);

            options.forEach(function(opt) {
                var $option = $('<option></option>')
                    .val(opt.value)
                    .text(opt.text);
                $select.append($option);
            });
        });

        // Find and remove selected table rows
        $("#productTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#productTable tr").each(function (index) {
                $(this).find('input[name^="no"]').val(index + 1);
            });

            rowCount--;
        });

        // Find and refresh actual weight
        $("#productTable").on('change', 'select[id^="productPartCode"]', function () {
            var selectedOption = $(this).find(":selected"); // Get only the selected option within this select
            var text = selectedOption.data('code');  // Get data-code
            var price = selectedOption.data('price'); // Get data-price

            // Update the respective input fields within the same row
            $(this).closest('.details').find('input[id^="products"]').val(text);
            $(this).closest('.details').find('input[id^="productUnitPrice"]').val(parseFloat(price).toFixed(2)).trigger('change');
        });
        
        // Event delegation for order weight to calculate variance
        $("#productTable").on('change', 'input[id^="productPercentage"]', function(){
            // Retrieve the input's attributes
            var productPercentage = $(this).val();
            var finalWeight = $('#finalWeight').val();
            var productItemWeight = parseFloat(finalWeight) * (parseFloat(productPercentage) / 100);

            // Update the respective inputs for variance
            $(this).closest('.details').find('input[id^="productItemWeight"]').val(productItemWeight);
            $(this).closest('.details').find('input[id^="productUnitPrice"]').trigger('change');

            // Check the total sum of all productPercentage inputs
            var totalPercentage = 0;
            $('input[id^="productPercentage"]').each(function() {
                totalPercentage += parseFloat($(this).val()) || 0;
            });

            if (totalPercentage > 100) {
                alert("Total percentage cannot exceed 100%!");
                $(this).val(0); // Reset the input to prevent exceeding 100%
            }
        });

        // Event delegation for order weight to calculate variance
        $("#productTable").on('change', 'input[id^="productUnitPrice"]', function(){
            // Retrieve the input's attributes
            var unitPrice = parseFloat($(this).val()) || 0;
            var productItemWeight = parseFloat($(this).closest('.details').find('input[id^="productItemWeight"]').val()) || 0;
            var variance = parseFloat(unitPrice) * parseFloat(productItemWeight);

            // Update the respective inputs for variance
            $(this).closest('.details').find('input[id^="productTotalPrice"]').val(variance.toFixed(2)).trigger('change');
        });

        // Event delegation for order weight to calculate variance
        $("#productTable").on('change', 'input[id^="productTotalPrice"]', function(){
            var totalSum = 0;

            // Loop through each productTotalPrice input and sum up the values
            $('input[id^="productTotalPrice"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the totalPrice input field
            $('#totalPrice').val(totalSum.toFixed(2));
        });

        //plant
        $('#plant').on('change', function(){
            $('#plantCode').val($('#plant :selected').data('code'));
        });

        $(".add-product").click(function(){
            var manualPrice = $('#addModal').find('input[name="manualPrice"]:checked').val();
            if(manualPrice == 'false'){
                var readonly = true;
            }else{
                var readonly = false;
            }            

            var $addContents = $("#productDetail").clone();
            $("#productTable").append($addContents.html());

            $("#productTable").find('.details:last').attr("id", "detail" + rowCount);
            $("#productTable").find('.details:last').attr("data-index", rowCount);
            $("#productTable").find('#productWeightCapture:last').attr("id", "productWeightCapture" + rowCount);
            $("#productTable").find('#remove:last').attr("id", "remove" + rowCount);

            $("#productTable").find('#no:last').attr('name', 'no['+rowCount+']').attr("id", "no" + rowCount).val(rowCount + 1);
            $("#productTable").find('#weightProductId:last').attr('name', 'weightProductId['+rowCount+']').attr("id", "weightProductId" + rowCount);
            $("#productTable").find('#productPartCode:last').attr('name', 'productPartCode['+rowCount+']').attr("id", "productPartCode" + rowCount);
            $("#productTable").find('#products:last').attr('name', 'products['+rowCount+']').attr("id", "products" + rowCount);
            $("#productTable").find('#productPercentage:last').attr('name', 'productPercentage['+rowCount+']').attr("id", "productPercentage" + rowCount);
            $("#productTable").find('#productItemWeight:last').attr('name', 'productItemWeight['+rowCount+']').attr("id", "productItemWeight" + rowCount);
            $("#productTable").find('#productUnitPrice:last').attr('name', 'productUnitPrice['+rowCount+']').attr("id", "productUnitPrice" + rowCount).attr("readonly", readonly);
            $("#productTable").find('#productTotalPrice:last').attr('name', 'productTotalPrice['+rowCount+']').attr("id", "productTotalPrice" + rowCount).attr("readonly", readonly);

            rowCount++;
        });

        $('#multiDeactivate').on('click', function () {
            $('#spinnerLoading').show();
            var selectedIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                if (confirm('Are you sure you want to cancel these items?')) {
                    $.post('php/deleteWeight.php', {userID: selectedIds, type: 'MULTI'}, function(data){
                        var obj = JSON.parse(data);
                        
                        if(obj.status === 'success'){
                            table.ajax.reload();
                            toastr["success"](obj.message, "Success:");
                            $('#spinnerLoading').hide();
                        }
                        else if(obj.status === 'failed'){
                            toastr["error"](obj.message, "Failed:");
                            $('#spinnerLoading').hide();
                        }
                        else{
                            toastr["error"]("Something wrong when activate", "Failed:");
                            $('#spinnerLoading').hide();
                        }
                    });
                }

                $('#spinnerLoading').hide();
            } 
            else {
                // Optionally, you can display a message or take another action if no IDs are selected
                alert("Please select at least one weight to delete.");
                $('#spinnerLoading').hide();
            }     
        });

    });

    function format (row) {
        var returnString = `
        <!-- Weighing Section -->
        <!--div class="row">
            
        </div><hr-->
        <div class="row ps-5 pe-5">
            <div class="col-4">
                <p><strong>${row.name}</strong></p>
                <p>${row.address_line_1}</p>
                <p>${row.address_line_2}</p>
                <p>${row.address_line_3}</p>`;

            if(row.cust_supp_tag == 'Y'){
                returnString += `<p>TEL: ${row.phone_no} FAX: ${row.fax_no}</p>`;
            }else{
                returnString += `<p></p>`;
            }
            
            returnString += `</div>
            <div class="col-4">
                <p><strong>TRANSPORTER NAME:</strong> ${row.transporter}</p>
                <p><strong>DRIVER NAME:</strong> ${row.driver_name}</p>
                <p><strong>DRIVER I/C:</strong> ${row.driver_ic}</p>
                <p><strong>DRIVER CONTACT:</strong> ${row.driver_phone}</p>
                <p><strong>VEHICLE PLATE:</strong> ${row.lorry_plate_no1}</p>
            </div>
            <div class="col-4">
                <p><strong>TRANSACTION ID:</strong> ${row.transaction_id}</p>
                <p><strong>WEIGHT STATUS:</strong> ${row.transaction_status}</p>
                <p><strong>INVOICE NO:</strong> ${row.invoice_no}</p>
                <p><strong>DELIVERY NO:</strong> ${row.delivery_no}</p>
                <p><strong>PURCHASE NO:</strong> ${row.purchase_order}</p>
            </div>
            
        </div><br>
        <!-- Product Section -->
        <div class="row ps-5 pe-5">
            <div class="col-7">
                <table class="product-table" width="80%">
                    <thead>
                        <tr>
                            <th><span>PRODUCT Description</span></th>
                            <th class="align-center"><span>Percentage</span></th>
                            <th class="align-center"><span>Weight (KG)</span></th>
                            <th class="align-center"><span>Unit Price</span></th>
                            <th class="align-center"><span>Total Price</span></th>
                        </tr>
                    </thead>
                    <tbody>`;

                    if (row.weight_product.length > 0) {
                        let subTotalPrice = 0;

                        for (var i = 0; i < row.weight_product.length; i++) {
                            var item = row.weight_product[i];
                            let totalPrice = item.total_price;

                            returnString += `
                                <tr>
                                    <td>${i+1} ${item.product_code} - ${item.product_name}</td>
                                    <td class="align-center">${item.percentage}%</td>
                                    <td class="align-center">${item.item_weight}</td>
                                    <td class="align-center">RM ${item.unit_price}</td>
                                    <td class="align-center">RM ${item.total_price}</td>
                                </tr>
                            `;

                            subTotalPrice += parseFloat(totalPrice);
                        }

                        subTotalPrice = subTotalPrice.toFixed(2);

                        returnString += `
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="align-center" style="font-weight: bold"><span>Sub Total Price</span></td>
                                <td class="align-center"><span>RM ${subTotalPrice}</span></td>
                            </tr>
                        `;
                    }else{
                        returnString += `
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td  class="align-center" style="font-weight: bold"><span>Sub Total Price</span></td>
                                <td class="align-center"><span>RM 0.00</span></td>
                            </tr>
                        `;
                    }

                    returnString += `
                    </tbody>
                </table>
                <br>
                <br>
                <br>
                <p><strong>REMARK:</strong> ${row.remarks}</p>
            </div>
            <div class="col-5">
                <p><strong>IN WEIGHT:</strong> ${row.gross_weight1} kg(${row.gross_weight1_date})</p>
                <p><strong>OUT WEIGHT:</strong> ${row.tare_weight1} kg(${row.tare_weight1_date})</p>
                <p><strong>NETT WEIGHT:</strong> ${row.nett_weight1} kg</p>
                <p><strong>REDUCE WEIGHT:</strong> ${row.reduce_weight} kg</p>
                <p><strong>SUB TOTAL WEIGHT:</strong> ${row.final_weight} kg</p>
            </div>
        </div>`;
        
        return returnString;
    }

    function edit(id){
        $('#spinnerLoading').show();

        $.post('php/getWeight.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#transactionId').val(obj.message.transaction_id);
                $('#addModal').find('#transactionStatus').val(obj.message.transaction_status);
                $('#addModal').find('#weightType').val(obj.message.weight_type);
                $('#addModal').find('#transactionDate').val(formatDate2(new Date(obj.message.transaction_date)));
                $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                $('#addModal').find('#plantCode').val(obj.message.plant_code);

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
                } console.log(obj.message.driver_phone);
                
                $('#addModal').find('#customerCode').val(obj.message.customer_code);
                $('#addModal').find('#customerName').val(obj.message.customer_name);
                $('#addModal').find('#driverCode').val(obj.message.driver_code); //
                $('#addModal').find('#driverName').val(obj.message.driver_name); //
                $('#addModal').find('#driverICNo').val(obj.message.driver_ic); //
                $('#addModal').find('#driverPhone').val(obj.message.driver_phone); //
                $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                $('#addModal').find('#supplierName').val(obj.message.supplier_name);
                $('#addModal').find('#productCode').val(obj.message.product_code);
                $('#addModal').find('#containerNo').val(obj.message.container_no);
                $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                $('#addModal').find('#supplyWeight').val(obj.message.supply_weight); // 
                $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                $('#addModal').find('#transporter').val(obj.message.transporter);
                $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                $('#addModal').find('#destination').val(obj.message.destination);
                $('#addModal').find('#remarks').val(obj.message.remarks); //
                $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                $('#addModal').find('#grossIncomingDate').val(formatDate3(new Date(obj.message.gross_weight1_date)));
                $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                $('#addModal').find('#tareOutgoingDate').val(obj.message.tare_weight1_date != null ? formatDate3(new Date(obj.message.tare_weight1_date)) : '');
                $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);
                $('#addModal').find('#estimateLoading').val(obj.message.estimate_loading); //
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

                if(obj.message.manual_price == 'true'){
                    $("#manualPriceYes").prop("checked", true);
                    $("#manualPriceNo").prop("checked", false);
                    $('#manualPriceYes').trigger('click');
                }
                else{
                    $("#manualPriceYes").prop("checked", false);
                    $("#manualPriceNo").prop("checked", true);
                    $('#manualPriceNo').trigger('click');
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

                $('#productTable').html('');
                rowCount = 0;

                if (obj.message.products.length > 0){
                    for(var i = 0; i < obj.message.products.length; i++){
                        var item = obj.message.products[i];
                        var $addContents = $("#productDetail").clone();
                        $("#productTable").append($addContents.html());

                        $("#productTable").find('.details:last').attr("id", "detail" + rowCount);
                        $("#productTable").find('.details:last').attr("data-index", rowCount);
                        $("#productTable").find('#productWeightCapture:last').attr("id", "productWeightCapture" + rowCount);
                        $("#productTable").find('#remove:last').attr("id", "remove" + rowCount);

                        $("#productTable").find('#no:last').attr('name', 'no['+rowCount+']').attr("id", "no" + rowCount).val(rowCount + 1);
                        $("#productTable").find('#weightProductId:last').attr('name', 'weightProductId['+rowCount+']').attr("id", "weightProductId" + rowCount).val(item.id);
                        $("#productTable").find('#productPartCode:last').attr('name', 'productPartCode['+rowCount+']').attr("id", "productPartCode" + rowCount).val(item.product_code);
                        $("#productTable").find('#products:last').attr('name', 'products['+rowCount+']').attr("id", "products" + rowCount).val(item.product_name);
                        $("#productTable").find('#productPercentage:last').attr('name', 'productPercentage['+rowCount+']').attr("id", "productPercentage" + rowCount).val(item.percentage);
                        $("#productTable").find('#productItemWeight:last').attr('name', 'productItemWeight['+rowCount+']').attr("id", "productItemWeight" + rowCount).val(item.item_weight);
                        $("#productTable").find('#productUnitPrice:last').attr('name', 'productUnitPrice['+rowCount+']').attr("id", "productUnitPrice" + rowCount).val(item.unit_price);
                        $("#productTable").find('#productTotalPrice:last').attr('name', 'productTotalPrice['+rowCount+']').attr("id", "productTotalPrice" + rowCount).attr("readonly", true).val(item.total_price);

                        rowCount++;
                    }
                }

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
