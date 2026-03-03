<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
}

$destination = $db->query("SELECT * FROM Destination WHERE status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier3 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier4 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");

?>

<head>

    <title>Stock Take | PWS - Weighing System</title>
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
                                                            <label for="ForminputState" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select" >
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant)){ ?>
                                                                    <option value="<?=$rowPlantF['id'] ?>"><?=$rowPlantF['name'] ?></option>
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
                                <div class="col">
                                    <div class="h-100">
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0">Stock Take</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <button type="button" id="addWeight" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                                Add Stock Take
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Plant</th>
                                                                    <th>Batch/ <br> Drum</th>
                                                                    <th>Declaration <br> Date</th>
                                                                    <!-- <th>Total (60/70) <br> Weight</th> -->
                                                                    <!-- <th>Total (60/70) <br> Temperature</th> -->
                                                                    <!-- <th>Total (60/70) <br> Level</th> -->
                                                                    <!-- <th>Total <br> LFO</th> -->
                                                                    <!-- <th>Total <br> Diesel</th> -->
                                                                    <!-- <th>Total <br> Hotoil</th> -->
                                                                    <!-- <th>Total <br> PG76</th> -->
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
            </div>

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->
        <!-- /.modal-dialog -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">Add Stock Take</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="siteForm" class="needs-validation" novalidate autocomplete="off">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="plant" class="col-sm-4 col-form-label">Plant</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-select select2" id="plant" name="plant" required>
                                                                <?php while($rowPlant=mysqli_fetch_assoc($plant2)){ ?>
                                                                    <option value="<?=$rowPlant['id'] ?>" data-code="<?=$rowPlant['plant_code'] ?>"><?=$rowPlant['name'] ?></option>
                                                                <?php } ?>
                                                            </select>  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="datetime" class="col-sm-4 col-form-label">Date/Time</label>
                                                        <div class="col-sm-8">
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="datetime" name="datetime" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="batchDrum" class="col-sm-4 col-form-label">By-Batch/By-Drum</label>
                                                        <div class="col-sm-8">
                                                            <select id="batchDrum" name="batchDrum" class="form-select select2">
                                                                <option selected>-</option>
                                                                <option value="Batch">Batch</option>
                                                                <option value="Drum">Drum</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" class="form-control" id="bitumenId" name="bitumenId"> 
                                                <input type="hidden" class="form-control" id="plantCode" name="plantCode">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Bitumen</h5>
                                                    <button type="button" class="btn btn-danger add-bitumen" id="addBitumen">Add Bitumen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Name</th>
                                                            <th>Status</th>
                                                            <!-- <th>Temperature (&deg;C)</th> -->
                                                            <th>Level (mm)</th>
                                                            <th>Actual Level (mm)</th>
                                                            <th>MT</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bitumenTable"></tbody>
                                                    <tfoot>
                                                        <th colspan="2">Incoming (MT)</th>
                                                        <th colspan="2"><input type="number" class="form-control" id="bitumenIncoming" name="bitumenIncoming" style="background-color:white;text-align: center;" value="0"></th>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalSixtySeventy" name="totalSixtySeventy" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <!-- <th><input type="number" class="form-control" id="totalTemp" name="totalTemp" style="background-color:white;text-align: center; visibility:hidden;" value="0" readonly></th>
                                                        <th><input type="number" class="form-control" id="totalLevel" name="totalLevel" style="background-color:white;text-align: center;" value="0" readonly></th> -->
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">LFO</h5>
                                                    <button type="button" class="btn btn-danger add-lfo" id="addLFO">Add LFO</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Name</th>
                                                            <th>Status</th>
                                                            <th>Level (m)</th>
                                                            <th>Actual Level (m)</th>
                                                            <th>Volume (&#76;)</th>
                                                            <!-- <th>MT</th> -->
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="lfoTable"></tbody>
                                                    <tfoot>
                                                        <th>Incoming (&#76;)</th>
                                                        <th><input type="number" class="form-control" id="lfoIncoming" name="lfoIncoming" style="background-color:white;text-align: center;" value="0"></th>
                                                        <th>Last Meter Reading</th>
                                                        <th><input type="number" class="form-control" id="lfoLastMeterReading" name="lfoLastMeterReading" style="background-color:white;text-align: center;" value="0"></th>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalLfo" name="totalLfo" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Diesel</h5>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <label for="previousDieselReading" class="form-label mb-0">Previous Reading (&#76;):</label>
                                                        <input type="number" class="form-control" id="previousDieselReading" name="previousDieselReading" value="0" style="width: 120px;">
                                                        <button type="button" class="btn btn-danger add-diesel ms-4" id="addDiesel">Add Diesel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Name</th>
                                                            <th>Status</th>
                                                            <th>Level (m)</th>
                                                            <th>Actual Level (m)</th>
                                                            <th>Volume (&#76;)</th>
                                                            <!-- <th>MT</th> -->
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <!-- Transport -->
                                                    <!-- <tr>
                                                        <td>
                                                            <select class="form-select select2" id="dieselSupplierTransport" name="dieselSupplierTransport">
                                                                <?php while($rowSupplier=mysqli_fetch_assoc($supplier)){ ?>
                                                                    <option value="<?=$rowSupplier['id'] ?>"><?=$rowSupplier['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control text-center" id="dieselUsageTransport" name="dieselUsageTransport" value="Transport" readonly>
                                                        </td>
                                                        <td><input type="number" class="form-control text-center" id="dieselWeightTransport" name="dieselWeightTransport" value="0.00"></td>
                                                        <td></td>
                                                    </tr> -->
                                                    <!-- Hotoil -->
                                                    <!-- <tr>
                                                        <td>
                                                            <select class="form-select select2" id="dieselSupplierHotoil" name="dieselSupplierHotoil">
                                                                <?php while($rowSupplier=mysqli_fetch_assoc($supplier2)){ ?>
                                                                    <option value="<?=$rowSupplier['id'] ?>"><?=$rowSupplier['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control text-center" id="dieselUsageHotoil" name="dieselUsageHotoil" value="Hotoil" readonly>
                                                        </td>
                                                        <td><input type="number" class="form-control text-center" id="dieselWeightHotoil" name="dieselWeightHotoil" value="0.00"></td>
                                                        <td></td>
                                                    </tr> -->
                                                    <!-- Burner -->
                                                    <!-- <tr>
                                                        <td>
                                                            <select class="form-select select2" id="dieselSupplierBurner" name="dieselSupplierBurner">
                                                                <?php while($rowSupplier=mysqli_fetch_assoc($supplier3)){ ?>
                                                                    <option value="<?=$rowSupplier['id'] ?>"><?=$rowSupplier['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control text-center" id="dieselUsageBurner" name="dieselUsageBurner" value="Burner" readonly>
                                                        </td>
                                                        <td><input type="number" class="form-control text-center" id="dieselWeightBurner" name="dieselWeightBurner" value="0.00"></td>
                                                        <td></td>
                                                    </tr> -->
                                                    <tbody id="dieselTable"></tbody>
                                                    <tfoot>
                                                        <th>Incoming (&#76;)</th>
                                                        <th><input type="number" class="form-control" id="dieselIncoming" name="dieselIncoming" style="background-color:white;text-align: center;" value="0"></th>
                                                        <th>Last Meter Reading</th>
                                                        <th><input type="number" class="form-control" id="dieselLastMeterReading" name="dieselLastMeterReading" style="background-color:white;text-align: center;" value="0"></th>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalDiesel" name="totalDiesel" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Transport</h5>
                                                    <button type="button" class="btn btn-danger add-other-diesel" id="addOtherDiesel">Add</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Type</th>
                                                            <th>Vehicle No</th>
                                                            <th>1st Reading</th>
                                                            <th>2nd Reading</th>
                                                            <th>Usage</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="otherDieselTable"></tbody>
                                                    <tfoot>
                                                        <th colspan="4"></th>
                                                        <th>Total Transport Usage</th>
                                                        <th><input type="number" class="form-control" id="otherDieselTotalTransportUsage" name="otherDieselTotalTransportUsage" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12" style="display: none;">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Hotoil</h5>
                                                    <button type="button" class="btn btn-danger add-hotoil" id="addHotoil">Add Hotoil</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Hotoil</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="hotoilTable"></tbody>
                                                    <tfoot>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalHotoil" name="totalHotoil" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Bitumen PG 76</h5>
                                                    <button type="button" class="btn btn-danger add-pg-76" id="addpg76">Add PG 76</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">No</th>
                                                            <th>Name</th>
                                                            <th>Status</th>
                                                            <!-- <th>Temperature (&deg;C)</th> -->
                                                            <th>Level (mm)</th>
                                                            <th>Actual Level (mm)</th>
                                                            <th>MT</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="pg76Table"></tbody>
                                                    <tfoot>
                                                        <th colspan="4"></th>
                                                        <th>Total</th>
                                                        <th><input type="number" class="form-control" id="totalPg76" name="totalPg76" style="background-color:white;text-align: center;" value="0" readonly></th>
                                                        <!-- <th><input type="number" class="form-control" id="totalTemp" name="totalTemp" style="background-color:white;text-align: center; visibility:hidden;" value="0" readonly></th>
                                                        <th><input type="number" class="form-control" id="totalLevel" name="totalLevel" style="background-color:white;text-align: center;" value="0" readonly></th> -->
                                                        <th></th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Fiber & Additive</h5>
                                                    <button type="button" class="btn btn-danger add-fibre" id="addFibre">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Type (kg/bag)</th>
                                                            <th>No. of Bags</th>
                                                            <th>Quantity (mt)</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tr>
                                                        <td><input type="text" class="form-control text-center" id="fibreNameMr6" name="fibreNameMr6" value="MR6" readonly></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreTypeMr6" name="fibreTypeMr6" value="0.00"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreBagsMr6" name="fibreBagsMr6" value="0"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreQtyMr6" name="fibreQtyMr6" value="0.00" readonly></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control text-center" id="fibreNameRpf" name="fibreNameRpf" value="RPF" readonly></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreTypeRpf" name="fibreTypeRpf" value="0.00"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreBagsRpf" name="fibreBagsRpf" value="0"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreQtyRpf" name="fibreQtyRpf" value="0.00" readonly></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control text-center" id="fibreNameNova" name="fibreNameNova" value="Nova Fiber" readonly></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreTypeNova" name="fibreTypeNova" value="0.00"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreBagsNova" name="fibreBagsNova" value="0"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreQtyNova" name="fibreQtyNova" value="0.00" readonly></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control text-center" id="fibreNameForta" name="fibreNameForta" value="Forta Fiber" readonly></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreTypeForta" name="fibreTypeForta" value="0.00"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreBagsForta" name="fibreBagsForta" value="0"></td>
                                                        <td><input type="number" class="form-control text-center" id="fibreQtyForta" name="fibreQtyForta" value="0.00" readonly></td>
                                                        <td></td>
                                                    </tr>
                                                    <tbody id="fibreTable"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-primary" style="text-align: center;">
                                                    <tbody>
                                                        <tr>
                                                            <th>Aggregrates</th>
                                                            <th>40mm</th>
                                                            <th>28mm</th>
                                                            <th>20mm</th>
                                                            <th>14mm</th>
                                                            <th>10mm</th>
                                                            <th>QD</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Quantity (mt)</td>
                                                            <td><input type="number" class="form-control" id="40mm" name="40mm"></td>
                                                            <td><input type="number" class="form-control" id="28mm" name="28mm"></td>
                                                            <td><input type="number" class="form-control" id="20mm" name="20mm"></td>
                                                            <td><input type="number" class="form-control" id="14mm" name="14mm"></td>
                                                            <td><input type="number" class="form-control" id="10mm" name="10mm"></td>
                                                            <td><input type="number" class="form-control" id="QD" name="QD"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Emulsion</th>
                                                            <th>RS1-K</th>
                                                            <th>K1-40</th>
                                                            <th>SS1K</th>
                                                            <th>Others</th>
                                                            <th>Transport</th>
                                                            <th>Burner</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Quantity (dr)</td>
                                                            <td><input type="number" class="form-control" id="rs1k" name="rs1k"></td>
                                                            <td><input type="number" class="form-control" id="k140" name="k140"></td>
                                                            <td><input type="number" class="form-control" id="ss1k" name="ss1k"></td>
                                                            <td><input type="number" class="form-control" id="others" name="others"></td>
                                                            <td><input type="number" class="form-control" id="transport" name="transport"></td>
                                                            <td><input type="number" class="form-control" id="burner" name="burner"></td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="3">OPC</th>
                                                            <th colspan="3">Lime/Filler</th>
                                                            <th></th>
                                                        </tr>
                                                        <tr>
                                                            <td>D/O No</td>
                                                            <td>Incoming (mt)</td>
                                                            <td>Quantity (mt)</td>
                                                            <td>D/O No</td>
                                                            <td>Incoming (mt)</td>
                                                            <td>Quantity (mt)</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="number" class="form-control" id="opcDo" name="opcDo"></td>
                                                            <td><input type="number" class="form-control" id="opcIncoming" name="opcIncoming"></td>
                                                            <td><input type="number" class="form-control" id="opcQty" name="opcQty"></td>
                                                            <td><input type="number" class="form-control" id="limeDo" name="limeDo"></td>
                                                            <td><input type="number" class="form-control" id="limeIncoming" name="limeIncoming"></td>
                                                            <td><input type="number" class="form-control" id="limeQty" name="limeQty"></td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-danger" id="submitSite">Submit</button>
                                </div>
                            </div><!--end col-->                                                               
                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
    <!-- END layout-wrapper -->

    <script type="text/html" id="bitumenDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="no" name="no" readonly>
                <input type="hidden" id="bitumenLength" name="bitumenLength">
                <input type="hidden" id="bitumenHeight" name="bitumenHeight">
                <input type="hidden" id="bitumenDiameter" name="bitumenDiameter">
                <input type="hidden" id="assetId" name="assetId">
            </td>
            <td>
                <input type="text" class="form-control" id="name" name="name">
            </td>
            <td>
                <select class="form-select" id="bitumenStatus" name="bitumenStatus">
                    <option value="Filled">Filled</option>
                    <option value="Empty">Empty</option>
                </select>
            </td>
            <td style="display: none;">
                <input type="number" class="form-control" id="temp" name="temp" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="level" name="level" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="actualLevel" name="actualLevel" style="background-color:white;" value="0.00" required readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="sixtyseventy" name="sixtyseventy" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="lfoDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="lfoNo" name="lfoNo" readonly>
                <input type="hidden" id="lfoLength" name="lfoLength">
                <input type="hidden" id="lfoHeight" name="lfoHeight">
                <input type="hidden" id="lfoDiameter" name="lfoDiameter">
                <input type="hidden" id="lfoAssetId" name="lfoAssetId">
            </td>
            <td>
                <input type="text" class="form-control" id="lfoName" name="lfoName">
            </td>
            <td>
                <select class="form-select" id="lfoStatus" name="lfoStatus">
                    <option value="Filled">Filled</option>
                    <option value="Empty">Empty</option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control" id="lfoLevel" name="lfoLevel" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="lfoActualLevel" name="lfoActualLevel" style="background-color:white;" value="0.00" required readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="lfoVolume" name="lfoVolume" style="background-color:white;" value="0.00" required>
            </td>
            <td style="display: none;">
                <input type="number" class="form-control" id="lfoWeight" name="lfoWeight" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="dieselDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="dieselNo" name="dieselNo" readonly>
                <input type="hidden" id="dieselLength" name="dieselLength">
                <input type="hidden" id="dieselHeight" name="dieselHeight">
                <input type="hidden" id="dieselDiameter" name="dieselDiameter">
                <input type="hidden" id="dieselAssetId" name="dieselAssetId">
            </td>
            <td>
                <input type="text" class="form-control" id="dieselName" name="dieselName">
            </td>
            <td>
                <select class="form-select" id="dieselStatus" name="dieselStatus">
                    <option value="Filled">Filled</option>
                    <option value="Empty">Empty</option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control" id="dieselLevel" name="dieselLevel" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="dieselActualLevel" name="dieselActualLevel" style="background-color:white;" value="0.00" required readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="dieselVolume" name="dieselVolume" style="background-color:white;" value="0.00" required>
            </td>
            <td style="display:none">
                <input type="number" class="form-control" id="dieselWeight" name="dieselWeight" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="otherDieselDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="otherDieselNo" name="otherDieselNo" readonly>
            </td>
            <td>
                <select class="form-select" id="otherDieselType" name="otherDieselType">
                    <option value="Transport">Transport</option>
                    <!-- <option value="Hotoil">Hotoil</option>
                    <option value="Burner">Burner</option> -->
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="otherDieselVehicleNo" name="otherDieselVehicleNo" style="background-color:white;" required>
            </td>
            <td>
                <input type="number" class="form-control" id="otherDieselFirstReading" name="otherDieselFirstReading" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="otherDieselSecondReading" name="otherDieselSecondReading" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="otherDieselUsage" name="otherDieselUsage" style="background-color:white;" value="0.00" required readonly>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <!-- <script type="text/html" id="dieselDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="dieselNo" name="dieselNo" hidden>
                <select class="form-select select2" id="dieselSupplier" name="dieselSupplier" required>
                    <?php while($rowSupplier=mysqli_fetch_assoc($supplier4)){ ?>
                        <option value="<?=$rowSupplier['id'] ?>" data-code="<?=$rowSupplier['supplier_code'] ?>"><?=$rowSupplier['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" id="dieselUsage" name="dieselUsage" required>
            </td>
            <td>
                <input type="number" class="form-control" id="dieselWeight" name="dieselWeight" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script> -->

    <script type="text/html" id="hotoilDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="hotoilNo" name="hotoilNo" readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="hotoilWeight" name="hotoilWeight" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="pg76Detail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="pg76No" name="pg76No" readonly>
                <input type="hidden" id="pg76Length" name="pg76Length">
                <input type="hidden" id="pg76Height" name="pg76Height">
                <input type="hidden" id="pg76Diameter" name="pg76Diameter">
                <input type="hidden" id="pg76AssetId" name="pg76AssetId">
            </td>
            <td>
                <input type="text" class="form-control" id="pg76Name" name="pg76Name">
            </td>
            <td>
                <select class="form-select" id="pg76Status" name="pg76Status">
                    <option value="Filled">Filled</option>
                    <option value="Empty">Empty</option>
                </select>
            </td>
            <td style="display: none;">
                <input type="number" class="form-control" id="pg76Temp" name="pg76Temp" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="pg76Level" name="pg76Level" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="pg76ActualLevel" name="pg76ActualLevel" style="background-color:white;" value="0.00" required readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="pgSeventySix" name="pgSeventySix" style="background-color:white;" value="0.00" required>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="fibreDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="fibreNo" name="fibreNo" hidden>
                <input type="text" class="form-control" id="fibreName" name="fibreName" style="background-color:white;" required>
            </td>
            <td>
                <input type="number" class="form-control" id="fibreType" name="fibreType" style="background-color:white;" value="0.00" required>
            </td>
            <td>
                <input type="number" class="form-control" id="fibreNoOfBags" name="fibreNoOfBags" style="background-color:white;" value="0" required>
            </td>
            <td>
                <input type="number" class="form-control" id="fibreQty" name="fibreQty" style="background-color:white;" value="0.00" readonly>
            </td>
            <td class="d-flex justify-content-center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

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

    var bitumenCount = $("#bitumenTable").find(".details").length;
    var lfoCount = $("#lfoTable").find(".details").length;
    var dieselCount = $("#dieselTable").find(".details").length;
    var otherDieselCount = $("#otherDieselTable").find(".details").length;
    var hotoilCount = $("#hotoilTable").find(".details").length;
    var pg76Count = $("#pg76Table").find(".details").length;
    var fibreCount = $("#fibreTable").find(".details").length;

    $(function () {
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

        // Initialize all Select2 elements in the modal
        $('#addModal .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
        });

        // Apply custom styling to Select2 elements in addModal
        $('#addModal .select2-container .select2-selection--single').css({
            'padding-top': '4px',
            'padding-bottom': '4px',
            'height': 'auto'
        });

        $('#addModal .select2-container .select2-selection__arrow').css({
            'padding-top': '33px',
            'height': 'auto'
        });

        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: yesterday
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });
        
        $('#datetime').flatpickr({
            dateFormat: "d-m-Y H:i",
            enableTime: true,
            time_24hr: true,
            defaultDate: today
        });

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

        var table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'order': [[ 1, 'asc' ]],
            'columnDefs': [ 
                { orderable: false, targets: [0] },
                // { orderable: false, targets: [4] },
                // { orderable: false, targets: [5] },
                // { orderable: false, targets: [6] },
                // { orderable: false, targets: [7] },            
            ],
            'ajax': {
                'url':'php/filterBitumen.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    plant: plantNoI
                } 
            },
            'columns': [
                { data: 'no' },
                { data: 'plant' },
                { data: 'batch_drum' },
                { data: 'declaration_datetime' },
                // { data: 'totalSixtySeventy' },
                // { data: 'totalTemperature' },
                // { data: 'totalLevel' },
                //{ data: 'totalLfo' },
                // { data: 'totalDiesel' },
                // { data: 'totalHotoil' },
                // { data: 'totalPgSevenNine' },
                { 
                    data: 'id',
                    render: function ( data, type, row ) {
                        return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Edit</a></li>' +
                        '<li><a class="dropdown-item" onclick="printDeclaration('+data+')"><i class="ri-printer-line align-bottom me-2 text-muted"></i> Print</a></li></ul></div>';
                    }
                }
            ] 
        });

        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

            $("#weightTable").DataTable().clear().destroy();

            table = $("#weightTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'searching': true,
                'serverMethod': 'post',
                'order': [[ 1, 'asc' ]],
                'columnDefs': [ 
                    { orderable: false, targets: [0] },
                    // { orderable: false, targets: [4] },
                    // { orderable: false, targets: [5] },
                    // { orderable: false, targets: [6] },
                    // { orderable: false, targets: [7] },
                ],
                'ajax': {
                    'url':'php/filterBitumen.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        plant: plantNoI
                    } 
                },
                'columns': [
                    { data: 'no' },
                    { data: 'plant' },
                    { data: 'batch_drum' },
                    { data: 'declaration_datetime' },
                    // { data: 'totalSixtySeventy' },
                    // { data: 'totalTemperature' },
                    // { data: 'totalLevel' },
                    // { data: 'totalLfo' },
                    // { data: 'totalDiesel' },
                    // { data: 'totalHotoil' },
                    // { data: 'totalPgSevenNine' },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                            '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Edit</a></li>' +
                            '<li><a class="dropdown-item" onclick="printDeclaration('+data+')"><i class="ri-printer-line align-bottom me-2 text-muted"></i> Print</a></li></ul></div>';
                        }
                    }
                ] 
            });
        });

        $('#addWeight').on('click', function(){
            // Reset counts to 0
            bitumenCount = 0;
            lfoCount = 0;
            dieselCount = 0;
            otherDieselCount = 0;
            hotoilCount = 0;
            pg79Count = 0;
            fibreCount = 0;

            $('#addModal').find('#bitumenId').val("");
            $('#addModal').find('#plant').val("").trigger('change');
            $('#addModal').find('#batchDrum').val("").trigger('change');
            $('#addModal').find('#datetime').val(formatDate4(today));
            $('#bitumenTable').html('');
            $('#addModal').find('#totalSixtySeventy').val(0);
            $('#addModal').find('#bitumenIncoming').val(0);
            // $('#addModal').find('#totalTemp').val(0);
            $('#addModal').find('#totalLevel').val(0);
            $('#lfoTable').html('');
            $('#addModal').find('#lfoLastMeterReading').val(0);
            $('#addModal').find('#totalLfo').val(0);
            $('#addModal').find('#lfoIncoming').val(0);
            $('#dieselTable').html('');
            $('#addModal').find('#previousDieselReading').val(0.00);
            $('#addModal').find('#dieselSupplierTransport').val('').trigger('change');
            $('#addModal').find('#dieselSupplierHotoil').val('').trigger('change');
            $('#addModal').find('#dieselSupplierBurner').val('').trigger('change');
            $('#addModal').find('#dieselWeightTransport').val(0.00).trigger('change');
            $('#addModal').find('#dieselWeightHotoil').val(0.00).trigger('change');
            $('#addModal').find('#dieselWeightBurner').val(0.00).trigger('change');
            $('#addModal').find('#totalDiesel').val(0);
            $('#addModal').find('#dieselIncoming').val(0);
            $('#addModal').find('#dieselLastMeterReading').val(0);
            $('#addModal').find('#otherDieselTable').html('');
            $('#addModal').find('#otherDieselTotalTransportUsage').val(0);
            $('#hotoilTable').html('');
            $('#addModal').find('#totalHotoil').val(0);
            $('#pg76Table').html('');
            $('#addModal').find('#totalPg76').val(0);
            $('#addModal').find('#fibreTypeMr6').val(0.00);
            $('#addModal').find('#fibreBagsMr6').val(0);
            $('#addModal').find('#fibreQtyMr6').val(0.00);
            $('#addModal').find('#fibreTypeRpf').val(0.00);
            $('#addModal').find('#fibreBagsRpf').val(0);
            $('#addModal').find('#fibreQtyRpf').val(0.00);
            $('#addModal').find('#fibreTypeNova').val(0.00);
            $('#addModal').find('#fibreBagsNova').val(0);
            $('#addModal').find('#fibreQtyNova').val(0.00);
            $('#addModal').find('#fibreTypeForta').val(0.00);
            $('#addModal').find('#fibreBagsForta').val(0);
            $('#addModal').find('#fibreQtyForta').val(0.00);
            $('#fibreTable').html('');
            $('#addModal').find('#40mm').val("");
            $('#addModal').find('#28mm').val("");
            $('#addModal').find('#20mm').val("");
            $('#addModal').find('#14mm').val("");
            $('#addModal').find('#10mm').val("");
            $('#addModal').find('#QD').val("");
            $('#addModal').find('#rs1k').val("");
            $('#addModal').find('#k140').val("");
            $('#addModal').find('#ss1k').val("");
            $('#addModal').find('#others').val("");
            $('#addModal').find('#transport').val("");
            $('#addModal').find('#burner').val("");
            $('#addModal').find('#limeDo').val("");
            $('#addModal').find('#limeIncoming').val("");
            $('#addModal').find('#limeQty').val("");
            $('#addModal').find('#opcDo').val("");
            $('#addModal').find('#opcIncoming').val("");
            $('#addModal').find('#opcQty').val("");

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container');
                
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            });

            $('#addModal').modal('show');
            
            $('#siteForm').validate({
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

        $('#submitSite').on('click', function(){
            // custom validation for select2
            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
                var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";

                // Check if the value is empty
                if (select2Field.val() === "" || select2Field.val() === null) {
                    select2Container.find('.select2-selection').css('border', '1px solid red'); // Add red border

                    // Add error message if not already present
                    if (select2Container.next('.select2-error').length === 0) {
                        select2Container.after(errorMsg);
                    }

                    isValid = false;
                } else {
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message
                }
            });

            if($('#siteForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/bitumen.php', $('#siteForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{

                    }
                });
            }
        });

        $('#exportPdf').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';

            $.post('php/exportPdf.php', {
                file: 'weight',
                fromDate: fromDateI,
                toDate: toDateI,
                status: statusI,
                customer: customerNoI,
                vehicle: vehicleNoI,
                weighingType: invoiceNoI,
                product: transactionStatusI
            }, function(response){
                var obj = JSON.parse(response);

                if(obj.status === 'success'){
                    var printWindow = window.open('', '', 'height=400,width=800');
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
            }).fail(function(error){
                console.error("Error exporting PDF:", error);
                alert("An error occurred while generating the PDF.");
            });
        });

        $('#exportExcel').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
            var invoiceNoI = $('#invoiceNoSearch').val() ? $('#invoiceNoSearch').val() : '';
            var transactionStatusI = $('#transactionStatusSearch').val() ? $('#transactionStatusSearch').val() : '';
            
            window.open("php/export.php?file=weight&fromDate="+fromDateI+"&toDate="+toDateI+
            "&status="+statusI+"&customer="+customerNoI+"&vehicle="+vehicleNoI+
            "&weighingType="+invoiceNoI+"&product="+transactionStatusI);
        });

        $('#plant').on('change', function(){
            var plantId = $(this).val();
            $('#plantCode').val($('#plant :selected').data('code'));

            if (plantId){
                $.post('php/getPlant.php', {userID: plantId}, function(data)
                {
                    var obj = JSON.parse(data);
                    if(obj.status === 'success'){
                        $('#addModal').find('#batchDrum').val(obj.message.default_type).trigger('change');
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
        });

        $('#datetime').on('change', function(){
            var declarationDate = $(this).val();
            var plantId = $('#plant').val();
            var plantCode = $('#plantCode').val();
            var batchDrum = $('#batchDrum').val();

            if (plantId && batchDrum){
                // Get Previous Stock Take
                getPrevStockTake(plantId, batchDrum, declarationDate);

                if (plantCode){
                    getPo(plantCode, batchDrum, declarationDate);
                }
            }
        });

        $('#batchDrum').on('change', function(){
            var declarationDate = $('#datetime').val();
            var plantId = $('#plant').val();
            var plantCode = $('#plantCode').val();
            var batchDrum = $(this).val();

            if (plantId && batchDrum){
                // Load Assets based on selected plant and batch drum
                loadAssets(plantId, batchDrum);

                // Get Previous Stock Take
                getPrevStockTake(plantId, batchDrum, declarationDate);

                if (plantCode){
                    getPo(plantCode, batchDrum, declarationDate);
                }
            }
        });

        /* Bitumen Table Start */
        // Find and remove selected table rows for bitumenTable
        $("#bitumenTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#bitumenTable tr").each(function (index) {
                $(this).find('input[name^="no"]').val(index + 1);
            });

            bitumenCount--;
        });

        // Event delegation for status
        $("#bitumenTable").on('change', 'select[id^="bitumenStatus"]', function(){
            var plantId = $('#plant').val();
            var batchDrum = $('#batchDrum').val();
            var row = $(this).closest('tr');
            var status = $(this).val();
            var temp = row.find('input[id^="temp"]').val();
            var level = row.find('input[id^="level"]').val();
            var height = (parseFloat(row.find('input[id^="bitumenHeight"]').val()) || 0) * 1000;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="actualLevel"]').val(actualLevel.toFixed(2));  
            
            // Calculate Weight MT
            if (plantId && batchDrum && actualLevel && temp){
                calculateBitumenWeight(plantId, batchDrum, actualLevel, temp, function(weight){
                    row.find('input[id^="sixtyseventy"]').val(weight.toFixed(2));
                    $('#addModal').find("#totalSixtySeventy").trigger('change');
                });
            } else {
                row.find('input[id^="sixtyseventy"]').val('0.00');
                $('#addModal').find("#totalSixtySeventy").trigger('change');
            }
        });

        // Event delegation for temp
        $("#bitumenTable").on('change', 'input[id^="temp"]', function(){
            var plantId = $('#plant').val();
            var batchDrum = $('#batchDrum').val();
            var row = $(this).closest('tr');
            var status =row.find('select[id^="bitumenStatus"]').val();
            var temp = $(this).val();
            var level = row.find('input[id^="level"]').val();
            var height = (parseFloat(row.find('input[id^="bitumenHeight"]').val()) || 0) * 1000;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="actualLevel"]').val(actualLevel.toFixed(2));

            // Calculate Weight MT
            if (plantId && batchDrum && actualLevel && temp){
                calculateBitumenWeight(plantId, batchDrum, actualLevel, temp, function(weight){
                    row.find('input[id^="sixtyseventy"]').val(weight.toFixed(2));
                    $('#addModal').find("#totalSixtySeventy").trigger('change');
                });
            } else {
                row.find('input[id^="sixtyseventy"]').val('0.00');
                $('#addModal').find("#totalSixtySeventy").trigger('change');
            }
        });

        // Event delegation for level
        $("#bitumenTable").on('change', 'input[id^="level"]', function(){
            var plantId = $('#plant').val();
            var batchDrum = $('#batchDrum').val();
            var row = $(this).closest('tr');
            var status = row.find('select[id^="bitumenStatus"]').val();
            var temp = row.find('input[id^="temp"]').val();
            var level = $(this).val();
            var height = (parseFloat(row.find('input[id^="bitumenHeight"]').val()) || 0) * 1000;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="actualLevel"]').val(actualLevel.toFixed(2));

            // Calculate Weight MT
            if (plantId && batchDrum && actualLevel && temp){
                calculateBitumenWeight(plantId, batchDrum, actualLevel, temp, function(weight){
                    row.find('input[id^="sixtyseventy"]').val(weight.toFixed(2));
                    $('#addModal').find("#totalSixtySeventy").trigger('change');
                });
            } else {
                row.find('input[id^="sixtyseventy"]').val('0.00');
                $('#addModal').find("#totalSixtySeventy").trigger('change');
            }

        });

        // Event delegation for level
        $("#bitumenTable").on('change', 'input[id^="sixtyseventy"]', function(){
            $('#addModal').find("#totalSixtySeventy").trigger('change');
        });

        // Calculate Total MT on totalSixtySeventy change
        $("#addModal").on('change', '#totalSixtySeventy', function(){
            var totalWeight = 0.00;
            $('#bitumenTable').find('input[id^="sixtyseventy"]').each(function(){
                var weight = parseFloat($(this).val()) || 0;
                totalWeight += weight;
            });

            $(this).val(totalWeight.toFixed(2));
        });

        $(".add-bitumen").click(function(event, asset){
            var $addContents = $("#bitumenDetail").clone();
            $("#bitumenTable").append($addContents.html());

            $("#bitumenTable").find('.details:last').attr("id", "detail" + bitumenCount);
            $("#bitumenTable").find('.details:last').attr("data-index", bitumenCount);
            $("#bitumenTable").find('#remove:last').attr("id", "remove" + bitumenCount);

            $("#bitumenTable").find('#no:last').attr('name', 'no['+bitumenCount+']').attr("id", "no" + bitumenCount).css("text-align", "center").val(bitumenCount + 1);
            $("#bitumenTable").find('#name:last').attr('name', 'name['+bitumenCount+']').attr("id", "name" + bitumenCount).css("text-align", "center").val(asset ? asset.name : '').prop('readonly', asset ? true : false);
            $("#bitumenTable").find('#bitumenStatus:last').attr('name', 'bitumenStatus['+bitumenCount+']').attr("id", "bitumenStatus" + bitumenCount);
            $("#bitumenTable").find('#temp:last').attr('name', 'temp['+bitumenCount+']').attr("id", "temp" + bitumenCount).css("text-align", "center");
            $("#bitumenTable").find('#level:last').attr('name', 'level['+bitumenCount+']').attr("id", "level" + bitumenCount).css("text-align", "center");
            $("#bitumenTable").find('#actualLevel:last').attr('name', 'actualLevel['+bitumenCount+']').attr("id", "actualLevel" + bitumenCount).css("text-align", "center");
            $("#bitumenTable").find('#sixtyseventy:last').attr('name', 'sixtyseventy['+bitumenCount+']').attr("id", "sixtyseventy" + bitumenCount).css("text-align", "center");

            // Hidden fields
            $("#bitumenTable").find('#bitumenLength:last').attr('name', 'bitumenLength['+bitumenCount+']').attr("id", "bitumenLength" + bitumenCount).val(asset ? asset.length || '' : '');
            $("#bitumenTable").find('#bitumenHeight:last').attr('name', 'bitumenHeight['+bitumenCount+']').attr("id", "bitumenHeight" + bitumenCount).val(asset ? asset.height || '' : '');
            $("#bitumenTable").find('#bitumenDiameter:last').attr('name', 'bitumenDiameter['+bitumenCount+']').attr("id", "bitumenDiameter" + bitumenCount).val(asset ? asset.diameter || '' : '');
            $("#bitumenTable").find('#assetId:last').attr('name', 'assetId['+bitumenCount+']').attr("id", "assetId" + bitumenCount).val(asset ? asset.id || '' : '');
            bitumenCount++;
        });

        /* Bitumen Table End */


        /* LFO Table Start */
        // Find and remove selected table rows for lfoTable
        $("#lfoTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#lfoTable tr").each(function (index) {
                $(this).find('input[name^="lfoNo"]').val(index + 1);
            });

            $('input[id^="lfoWeight"]').trigger('change');

            lfoCount--;
        });

        // Event delegation for lfoStatus
        $("#lfoTable").on('change', 'select[id^="lfoStatus"]', function(){
            var row = $(this).closest('tr');
            var status = $(this).val();
            var level = row.find('input[id^="lfoLevel"]').val();
            var height = parseFloat(row.find('input[id^="lfoHeight"]').val()) || 0;
            var diameter = parseFloat(row.find('input[id^="lfoDiameter"]').val()) || 0;
            var length = parseFloat(row.find('input[id^="lfoLength"]').val()) || 0;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="lfoActualLevel"]').val(actualLevel.toFixed(2));  
            
            // Calculate Weight MT
            if (actualLevel){
                calculateLiquid('LFFO001', diameter, length, actualLevel, function(calculationData){
                    row.find('input[id^="lfoVolume"]').val(calculationData.volume.toFixed(2));
                    row.find('input[id^="lfoWeight"]').val(calculationData.volumeMt.toFixed(2));
                    $('#addModal').find('#totalLfo').trigger('change');
                });
            } else {
                row.find('input[id^="lfoVolume"]').val('0.00');
                row.find('input[id^="lfoWeight"]').val('0.00');
                $('#addModal').find('#totalLfo').trigger('change');
            }
        });

        // Event delegation for lfoLevel
        $("#lfoTable").on('change', 'input[id^="lfoLevel"]', function(){
            var row = $(this).closest('tr');
            var status = row.find('select[id^="lfoStatus"]').val();
            var level = $(this).val();
            var height = parseFloat(row.find('input[id^="lfoHeight"]').val()) || 0;
            var diameter = parseFloat(row.find('input[id^="lfoDiameter"]').val()) || 0;
            var length = parseFloat(row.find('input[id^="lfoLength"]').val()) || 0;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="lfoActualLevel"]').val(actualLevel.toFixed(2));  

            // Calculate Weight MT
            if (actualLevel){
                calculateLiquid('LFFO001', diameter, length, actualLevel, function(calculationData){
                    row.find('input[id^="lfoVolume"]').val(calculationData.volume.toFixed(2));
                    row.find('input[id^="lfoWeight"]').val(calculationData.volumeMt.toFixed(2));
                    $('#addModal').find('#totalLfo').trigger('change');
                });
            } else {
                row.find('input[id^="lfoVolume"]').val('0.00');
                row.find('input[id^="lfoWeight"]').val('0.00');
                $('#addModal').find('#totalLfo').trigger('change');
            }
        });

        // Event delegation for level
        $("#lfoTable").on('change', 'input[id^="lfoWeight"]', function(){
            $('#addModal').find("#totalLfo").trigger('change');
        });

        // Calculate Total MT on totalLfo change
        $("#addModal").find('#totalLfo').on('change', function(){
            var totalWeight = 0.00;
            $('#lfoTable').find('input[id^="lfoWeight"]').each(function(){
                var weight = parseFloat($(this).val()) || 0;
                totalWeight += weight;
            });

            $(this).val(totalWeight.toFixed(2));
        });

        $(".add-lfo").click(function(event, asset){
            var $addContents = $("#lfoDetail").clone();
            $("#lfoTable").append($addContents.html());

            $("#lfoTable").find('.details:last').attr("id", "detail" + lfoCount);
            $("#lfoTable").find('.details:last').attr("data-index", lfoCount);
            $("#lfoTable").find('#remove:last').attr("id", "remove" + lfoCount);

            $("#lfoTable").find('#lfoNo:last').attr('name', 'lfoNo['+lfoCount+']').attr("id", "lfoNo" + lfoCount).css("text-align", "center").val(lfoCount + 1);
            $("#lfoTable").find('#lfoName:last').attr('name', 'lfoName['+lfoCount+']').attr("id", "lfoName" + lfoCount).css("text-align", "center").val(asset ? asset.name : '').prop('readonly', asset ? true : false);
            $("#lfoTable").find('#lfoStatus:last').attr('name', 'lfoStatus['+lfoCount+']').attr("id", "lfoStatus" + lfoCount);
            $("#lfoTable").find('#lfoLevel:last').attr('name', 'lfoLevel['+lfoCount+']').attr("id", "lfoLevel" + lfoCount);
            $("#lfoTable").find('#lfoActualLevel:last').attr('name', 'lfoActualLevel['+lfoCount+']').attr("id", "lfoActualLevel" + lfoCount);
            $("#lfoTable").find('#lfoVolume:last').attr('name', 'lfoVolume['+lfoCount+']').attr("id", "lfoVolume" + lfoCount);
            $("#lfoTable").find('#lfoWeight:last').attr('name', 'lfoWeight['+lfoCount+']').attr("id", "lfoWeight" + lfoCount).css("text-align", "center");

            // Hidden fields
            $("#lfoTable").find('#lfoLength:last').attr('name', 'lfoLength['+lfoCount+']').attr("id", "lfoLength" + lfoCount).val(asset ? asset.length || '' : '');
            $("#lfoTable").find('#lfoHeight:last').attr('name', 'lfoHeight['+lfoCount+']').attr("id", "lfoHeight" + lfoCount).val(asset ? asset.height || '' : '');
            $("#lfoTable").find('#lfoDiameter:last').attr('name', 'lfoDiameter['+lfoCount+']').attr("id", "lfoDiameter" + lfoCount).val(asset ? asset.diameter || '' : '');
            $("#lfoTable").find('#lfoAssetId:last').attr('name', 'lfoAssetId['+lfoCount+']').attr("id", "lfoAssetId" + lfoCount).val(asset ? asset.id || '' : '');

            lfoCount++;
        });

        /* LFO Table End */

        /* Diesel Table Start */
        // Find and remove selected table rows for dieselTable  
        $("#dieselTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#dieselTable tr").each(function (index) {
                $(this).find('input[name^="dieselNo"]').val(index + 1);
            });

            $('input[id^="dieselWeight"]').trigger('change');

            dieselCount--;
        });

        
        // Event delegation for dieselStatus
        $("#dieselTable").on('change', 'select[id^="dieselStatus"]', function(){
            var row = $(this).closest('tr');
            var status = $(this).val();
            var level = row.find('input[id^="dieselLevel"]').val();
            var height = parseFloat(row.find('input[id^="dieselHeight"]').val()) || 0;
            var diameter = parseFloat(row.find('input[id^="dieselDiameter"]').val()) || 0;
            var length = parseFloat(row.find('input[id^="dieselLength"]').val()) || 0;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="dieselActualLevel"]').val(actualLevel.toFixed(2));  
            
            // Calculate Weight MT
            if (actualLevel){
                calculateLiquid('DIE001', diameter, length, actualLevel, function(calculationData){
                    row.find('input[id^="dieselVolume"]').val(calculationData.volume.toFixed(2));
                    row.find('input[id^="dieselWeight"]').val(calculationData.volumeMt.toFixed(2));
                    $('#addModal').find('#totalDiesel').trigger('change');
                });
            } else {
                row.find('input[id^="dieselVolume"]').val('0.00');
                row.find('input[id^="dieselWeight"]').val('0.00');
                $('#addModal').find('#totalDiesel').trigger('change');
            }
        });

        // Event delegation for level
        $("#dieselTable").on('change', 'input[id^="dieselLevel"]', function(){
            var row = $(this).closest('tr');
            var status = row.find('select[id^="dieselStatus"]').val();
            var level = $(this).val();
            var height = parseFloat(row.find('input[id^="dieselHeight"]').val()) || 0;
            var diameter = parseFloat(row.find('input[id^="dieselDiameter"]').val()) || 0;
            var length = parseFloat(row.find('input[id^="dieselLength"]').val()) || 0;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="dieselActualLevel"]').val(actualLevel.toFixed(2));  

            // Calculate Weight MT
            if (actualLevel){
                calculateLiquid('DIE001', diameter, length, actualLevel, function(calculationData){
                    row.find('input[id^="dieselVolume"]').val(calculationData.volume.toFixed(2));
                    row.find('input[id^="dieselWeight"]').val(calculationData.volumeMt.toFixed(2));
                    $('#addModal').find('#totalDiesel').trigger('change');
                });
            } else {
                row.find('input[id^="dieselVolume"]').val('0.00');
                row.find('input[id^="dieselWeight"]').val('0.00');
                $('#addModal').find('#totalDiesel').trigger('change');
            }
        });

        // Event delegation for level
        $("#dieselTable").on('change', 'input[id^="dieselVolume"]', function(){
            $('#addModal').find("#totalDiesel").trigger('change');
        });

        // Calculate Total MT on totalDiesel change
        $("#addModal").find('#totalDiesel').on('change', function(){
            var totalWeight = 0.00;
            $('#dieselTable').find('input[id^="dieselVolume"]').each(function(){
                var weight = parseFloat($(this).val()) || 0;
                totalWeight += weight;
            });

            $(this).val(totalWeight.toFixed(2));
        });

        $(".add-diesel").click(function(event, asset){
            var $addContents = $("#dieselDetail").clone();
            $("#dieselTable").append($addContents.html());

            $("#dieselTable").find('.details:last').attr("id", "detail" + dieselCount);
            $("#dieselTable").find('.details:last').attr("data-index", dieselCount);
            $("#dieselTable").find('#remove:last').attr("id", "remove" + dieselCount);

            $("#dieselTable").find('#dieselNo:last').attr('name', 'dieselNo['+dieselCount+']').attr("id", "dieselNo" + dieselCount).css("text-align", "center").val(dieselCount + 1);
            $("#dieselTable").find('#dieselName:last').attr('name', 'dieselName['+dieselCount+']').attr("id", "dieselName" + dieselCount).css("text-align", "center").val(asset ? asset.name : '').prop('readonly', asset ? true : false);
            $("#dieselTable").find('#dieselStatus:last').attr('name', 'dieselStatus['+dieselCount+']').attr("id", "dieselStatus" + dieselCount);
            $("#dieselTable").find('#dieselLevel:last').attr('name', 'dieselLevel['+dieselCount+']').attr("id", "dieselLevel" + dieselCount);
            $("#dieselTable").find('#dieselActualLevel:last').attr('name', 'dieselActualLevel['+dieselCount+']').attr("id", "dieselActualLevel" + dieselCount);
            $("#dieselTable").find('#dieselVolume:last').attr('name', 'dieselVolume['+dieselCount+']').attr("id", "dieselVolume" + dieselCount);
            $("#dieselTable").find('#dieselWeight:last').attr('name', 'dieselWeight['+dieselCount+']').attr("id", "dieselWeight" + dieselCount).css("text-align", "center");

            // Hidden fields
            $("#dieselTable").find('#dieselLength:last').attr('name', 'dieselLength['+dieselCount+']').attr("id", "dieselLength" + dieselCount).val(asset ? asset.length || '' : '');
            $("#dieselTable").find('#dieselHeight:last').attr('name', 'dieselHeight['+dieselCount+']').attr("id", "dieselHeight" + dieselCount).val(asset ? asset.height || '' : '');
            $("#dieselTable").find('#dieselDiameter:last').attr('name', 'dieselDiameter['+dieselCount+']').attr("id", "dieselDiameter" + dieselCount).val(asset ? asset.diameter || '' : '');
            $("#dieselTable").find('#dieselAssetId:last').attr('name', 'dieselAssetId['+dieselCount+']').attr("id", "dieselAssetId" + dieselCount).val(asset ? asset.id || '' : '');

            dieselCount++;

            // var $addContents = $("#dieselDetail").clone();
            // $("#dieselTable").append($addContents.html());

            // $("#dieselTable").find('.details:last').attr("id", "detail" + dieselCount);
            // $("#dieselTable").find('.details:last').attr("data-index", dieselCount);
            // $("#dieselTable").find('#remove:last').attr("id", "remove" + dieselCount);

            // $("#dieselTable").find('#dieselNo:last').attr('name', 'dieselNo['+dieselCount+']').attr("id", "dieselNo" + dieselCount).css("text-align", "center").val(dieselCount + 1);
            // $("#dieselTable").find('#dieselSupplier:last').attr('name', 'dieselSupplier['+dieselCount+']').attr("id", "dieselSupplier" + dieselCount).css("text-align", "center").val('').trigger('change');
            // $("#dieselTable").find('#dieselUsage:last').attr('name', 'dieselUsage['+dieselCount+']').attr("id", "dieselUsage" + dieselCount).css("text-align", "center").val('').trigger('change');
            // $("#dieselTable").find('#dieselWeight:last').attr('name', 'dieselWeight['+dieselCount+']').attr("id", "dieselWeight" + dieselCount).css("text-align", "center");

            // // Initialize all Select2 elements in the modal
            // $('#addModal .select2').select2({
            //     allowClear: true,
            //     placeholder: "Please Select",
            //     dropdownParent: $('#addModal') // Ensures dropdown is not cut off
            // });

            // // Apply custom styling to Select2 elements in addModal
            // $('#addModal .select2-container .select2-selection--single').css({
            //     'padding-top': '4px',
            //     'padding-bottom': '4px',
            //     'height': 'auto'
            // });

            // $('#addModal .select2-container .select2-selection__arrow').css({
            //     'padding-top': '33px',
            //     'height': 'auto'
            // });

            // dieselCount++;
        });

        $('#addModal').find('#previousDieselReading').on('change', function(){
            $('#addModal').find('#otherDieselTotalTransportUsage').trigger('change');
        });

        $('#addModal').find('#totalDiesel').on('change', function(){
            $('#addModal').find('#otherDieselTotalTransportUsage').trigger('change');
        });
        /* Diesel Table End */


        /* Other Diesel Table Start */

        // Find and remove selected table rows for otherDieselTable  
        $("#otherDieselTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#otherDieselTable tr").each(function (index) {
                $(this).find('input[name^="otherDieselNo"]').val(index + 1);
            });

            otherDieselCount--;
        });

        // Event delegation for otherDieselType
        $("#otherDieselTable").on('change', 'select[id^="otherDieselType"]', function(){
            var row = $(this).closest('tr');
            var type = $(this).val();

            if (type == 'Transport'){
                row.find('input[id^="otherDieselVehicleNo"]').prop('readonly', false).prop('required', true);
            }else{
                row.find('input[id^="otherDieselVehicleNo"]').prop('readonly', true).prop('required', false);
            }
        });

        // Event delegation for otherDieselFirstReading
        $("#otherDieselTable").on('change', 'input[id^="otherDieselFirstReading"]', function(){
            var row = $(this).closest('tr');
            row.find('input[id^="otherDieselUsage"]').trigger('change');
        });

        // Event delegation for otherDieselSecondReading
        $("#otherDieselTable").on('change', 'input[id^="otherDieselSecondReading"]', function(){
            var row = $(this).closest('tr');
            row.find('input[id^="otherDieselUsage"]').trigger('change');
        });

        // Event delegation for otherDieselUsage
        $("#otherDieselTable").on('change', 'input[id^="otherDieselUsage"]', function(){
            var row = $(this).closest('tr');
            var firstReading = parseFloat(row.find('input[id^="otherDieselFirstReading"]').val()) || 0;
            var secondReading = parseFloat(row.find('input[id^="otherDieselSecondReading"]').val()) || 0;
            var usage = firstReading - secondReading;

            $(this).val(usage.toFixed(2));
            $('#addModal').find('#otherDieselTotalTransportUsage').trigger('change');
        });

        $('#addOtherDiesel').click(function(){
            var $addContents = $("#otherDieselDetail").clone();
            $("#otherDieselTable").append($addContents.html());

            $("#otherDieselTable").find('.details:last').attr("id", "detail" + otherDieselCount);
            $("#otherDieselTable").find('.details:last').attr("data-index", otherDieselCount);
            $("#otherDieselTable").find('#remove:last').attr("id", "remove" + otherDieselCount);

            $("#otherDieselTable").find('#otherDieselNo:last').attr('name', 'otherDieselNo['+otherDieselCount+']').attr("id", "otherDieselNo" + otherDieselCount).css("text-align", "center").val(otherDieselCount + 1);
            $("#otherDieselTable").find('#otherDieselType:last').attr('name', 'otherDieselType['+otherDieselCount+']').attr("id", "otherDieselType" + otherDieselCount);
            $("#otherDieselTable").find('#otherDieselVehicleNo:last').attr('name', 'otherDieselVehicleNo['+otherDieselCount+']').attr("id", "otherDieselVehicleNo" + otherDieselCount).css("text-align", "center");
            $("#otherDieselTable").find('#otherDieselFirstReading:last').attr('name', 'otherDieselFirstReading['+otherDieselCount+']').attr("id", "otherDieselFirstReading" + otherDieselCount).css("text-align", "center");
            $("#otherDieselTable").find('#otherDieselSecondReading:last').attr('name', 'otherDieselSecondReading['+otherDieselCount+']').attr("id", "otherDieselSecondReading" + otherDieselCount).css("text-align", "center");
            $("#otherDieselTable").find('#otherDieselUsage:last').attr('name', 'otherDieselUsage['+otherDieselCount+']').attr("id", "otherDieselUsage" + otherDieselCount).css("text-align", "center");

            otherDieselCount++;
        });

        $('#addModal').find('#otherDieselTotalTransportUsage').on('change', function(){
            var previousReading = parseFloat($('#addModal').find('#previousDieselReading').val()) || 0;
            var totalDiesel = parseFloat($('#addModal').find('#totalDiesel').val()) || 0;
            var totalUsage = 0.00;
            var totalTransportUsage = 0.00;

            // Sum up all transport usage from otherDieselTable
            $('#otherDieselTable').find('input[id^="otherDieselUsage"]').each(function(){
                var usage = parseFloat($(this).val()) || 0;
                totalUsage += usage;
            });

            var totalTransportUsage = totalDiesel - previousReading - totalUsage;
            $(this).val(totalTransportUsage.toFixed(2));
        });

        /* Other Diesel Table End */


        // Find and remove selected table rows for hotoilTable
        $("#hotoilTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#hotoilTable tr").each(function (index) {
                $(this).find('input[name^="hotoilNo"]').val(index + 1);
            });

            $('input[id^="hotoilWeight"]').trigger('change');

            hotoilCount--;
        });

        // Event delegation for order weight to calculate hotoil total
        $("#hotoilTable").on('change', 'input[id^="hotoilWeight"]', function(){
            var totalSum = 0;

            // Loop through each hotoil input and sum up the values
            $('input[id^="hotoilWeight"]').each(function(){
                totalSum += parseFloat($(this).val()) || 0;
            });

            // Set the total sum into the hotoil input field
            $('#totalHotoil').val(totalSum.toFixed(2));
        });

        $(".add-hotoil").click(function(){
            var $addContents = $("#hotoilDetail").clone();
            $("#hotoilTable").append($addContents.html());

            $("#hotoilTable").find('.details:last').attr("id", "detail" + hotoilCount);
            $("#hotoilTable").find('.details:last').attr("data-index", hotoilCount);
            $("#hotoilTable").find('#remove:last').attr("id", "remove" + hotoilCount);

            $("#hotoilTable").find('#hotoilNo:last').attr('name', 'hotoilNo['+hotoilCount+']').attr("id", "hotoilNo" + hotoilCount).css("text-align", "center").val(hotoilCount + 1);
            $("#hotoilTable").find('#hotoilWeight:last').attr('name', 'hotoilWeight['+hotoilCount+']').attr("id", "hotoilWeight" + hotoilCount).css("text-align", "center");

            hotoilCount++;
        });

        /* PG 76 Table Start */
        // Find and remove selected table rows for lfoTable
        $("#pg76Table").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#pg76Table tr").each(function (index) {
                $(this).find('input[name^="pg76No"]').val(index + 1);
            });

            $('input[id^="pgSeventySix"]').trigger('change');

            pg76Count--;
        });

        // Event delegation for pg76Status
        $("#pg76Table").on('change', 'select[id^="pg76Status"]', function(){
            var plantId = $('#plant').val();
            var batchDrum = $('#batchDrum').val();
            var row = $(this).closest('tr');
            var status = $(this).val();
            var temp = row.find('input[id^="pg76Temp"]').val();
            var level = row.find('input[id^="pg76Level"]').val();
            var height = (parseFloat(row.find('input[id^="pg76Height"]').val()) || 0) * 1000;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="pg76ActualLevel"]').val(actualLevel.toFixed(2));  
            
            // Calculate Weight MT
            if (plantId && batchDrum && actualLevel && temp){
                calculateBitumenWeight(plantId, batchDrum, actualLevel, temp, function(weight){
                    row.find('input[id^="pgSeventySix"]').val(weight.toFixed(2));
                    $('#addModal').find("#totalPg76").trigger('change');
                });
            } else {
                row.find('input[id^="pgSeventySix"]').val('0.00');
                $('#addModal').find("#totalPg76").trigger('change');
            }
        });

        // Event delegation for pg76Temp
        $("#pg76Table").on('change', 'input[id^="pg76Temp"]', function(){
            var plantId = $('#plant').val();
            var batchDrum = $('#batchDrum').val();
            var row = $(this).closest('tr');
            var status =row.find('select[id^="pg76Status"]').val();
            var temp = $(this).val();
            var level = row.find('input[id^="pg76Level"]').val();
            var height = (parseFloat(row.find('input[id^="pg76Height"]').val()) || 0) * 1000;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="pg76ActualLevel"]').val(actualLevel.toFixed(2));

            // Calculate Weight MT
            if (plantId && batchDrum && actualLevel && temp){
                calculateBitumenWeight(plantId, batchDrum, actualLevel, temp, function(weight){
                    row.find('input[id^="pgSeventySix"]').val(weight.toFixed(2));
                    $('#addModal').find("#totalPg76").trigger('change');
                });
            } else {
                row.find('input[id^="pgSeventySix"]').val('0.00');
                $('#addModal').find("#totalPg76").trigger('change');
            }
        });

        // Event delegation for pg76Level
        $("#pg76Table").on('change', 'input[id^="pg76Level"]', function(){
            var plantId = $('#plant').val();
            var batchDrum = $('#batchDrum').val();
            var row = $(this).closest('tr');
            var status = row.find('select[id^="pg76Status"]').val();
            var temp = row.find('input[id^="pg76Temp"]').val();
            var level = $(this).val();
            var height = (parseFloat(row.find('input[id^="pg76Height"]').val()) || 0) * 1000;
            var actualLevel = 0;

            // Calculate actual level based on status, level, and height
            if (status && level){
                actualLevel = calculateActualLevel(status, level, height);
            }

            row.find('input[id^="pg76ActualLevel"]').val(actualLevel.toFixed(2));

            // Calculate Weight MT
            if (plantId && batchDrum && actualLevel && temp){
                calculateBitumenWeight(plantId, batchDrum, actualLevel, temp, function(weight){
                    row.find('input[id^="pgSeventySix"]').val(weight.toFixed(2));
                    $('#addModal').find("#totalPg76").trigger('change');
                });
            } else {
                row.find('input[id^="pgSeventySix"]').val('0.00');
                $('#addModal').find("#totalPg76").trigger('change');
            }
        });

        // Event delegation for level
        $("#pg76Table").on('change', 'input[id^="pgSeventySix"]', function(){
            $('#addModal').find("#totalPg76").trigger('change');
        });

        // Calculate Total MT on totalPg76 change
        $("#addModal").on('change', '#totalPg76', function(){
            var totalWeight = 0.00;
            $('#pg76Table').find('input[id^="pgSeventySix"]').each(function(){
                var weight = parseFloat($(this).val()) || 0;
                totalWeight += weight;
            });

            $(this).val(totalWeight.toFixed(2));
        });

        $(".add-pg-76").click(function(event, asset){
            var $addContents = $("#pg76Detail").clone();
            $("#pg76Table").append($addContents.html());

            $("#pg76Table").find('.details:last').attr("id", "detail" + pg76Count);
            $("#pg76Table").find('.details:last').attr("data-index", pg76Count);
            $("#pg76Table").find('#remove:last').attr("id", "remove" + pg76Count);

            $("#pg76Table").find('#pg76No:last').attr('name', 'pg76No['+pg76Count+']').attr("id", "pg76No" + pg76Count).css("text-align", "center").val(pg76Count + 1);
            $("#pg76Table").find('#pg76Name:last').attr('name', 'pg76Name['+pg76Count+']').attr("id", "pg76Name" + pg76Count).css("text-align", "center").val(asset ? asset.name : '').prop('readonly', asset ? true : false);
            $("#pg76Table").find('#pg76Status:last').attr('name', 'pg76Status['+pg76Count+']').attr("id", "pg76Status" + pg76Count);
            $("#pg76Table").find('#pg76Temp:last').attr('name', 'pg76Temp['+pg76Count+']').attr("id", "pg76Temp" + pg76Count).css("text-align", "center");
            $("#pg76Table").find('#pg76Level:last').attr('name', 'pg76Level['+pg76Count+']').attr("id", "pg76Level" + pg76Count).css("text-align", "center");
            $("#pg76Table").find('#pg76ActualLevel:last').attr('name', 'pg76ActualLevel['+pg76Count+']').attr("id", "pg76ActualLevel" + pg76Count).css("text-align", "center");
            $("#pg76Table").find('#pgSeventySix:last').attr('name', 'pgSeventySix['+pg76Count+']').attr("id", "pgSeventySix" + pg76Count).css("text-align", "center");

            // Hidden fields
            $("#pg76Table").find('#pg76Length:last').attr('name', 'pg76Length['+pg76Count+']').attr("id", "pg76Length" + pg76Count).val(asset ? asset.length || '' : '');
            $("#pg76Table").find('#pg76Height:last').attr('name', 'pg76Height['+pg76Count+']').attr("id", "pg76Height" + pg76Count).val(asset ? asset.height || '' : '');
            $("#pg76Table").find('#pg76Diameter:last').attr('name', 'pg76Diameter['+pg76Count+']').attr("id", "pg76Diameter" + pg76Count).val(asset ? asset.diameter || '' : '');
            $("#pg76Table").find('#pg76AssetId:last').attr('name', 'pg76AssetId['+pg76Count+']').attr("id", "pg76AssetId" + pg76Count).val(asset ? asset.id || '' : '');

            pg76Count++;
        });

        /* PG 76 Table End */

        // Find and remove selected table rows for fibreTable
        $("#fibreTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();

            $("#fibreTable tr").each(function (index) {
                $(this).find('input[name^="fibreNo"]').val(index + 1);
            });

            fibreCount--;
        });

        // Event delegation for order weight to calculate fibreQty for each fibre type
        $("#fibreTable").on('change', 'input[id^="fibreType"]', function(){
            $(this).closest('.details').find('input[id^="fibreQty"]').trigger('change');
        });

        $("#fibreTable").on('change', 'input[id^="fibreNoOfBags"]', function(){
            $(this).closest('.details').find('input[id^="fibreQty"]').trigger('change');
        });

        $("#fibreTable").on('change', 'input[id^="fibreQty"]', function(){
            var type = parseFloat($(this).closest('.details').find('input[id^="fibreType"]').val()) || 0;
            var bags = parseFloat($(this).closest('.details').find('input[id^="fibreNoOfBags"]').val()) || 0;

            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });

        $(".add-fibre").click(function(){
            var $addContents = $("#fibreDetail").clone();
            $("#fibreTable").append($addContents.html());

            $("#fibreTable").find('.details:last').attr("id", "detail" + fibreCount);
            $("#fibreTable").find('.details:last').attr("data-index", fibreCount);
            $("#fibreTable").find('#remove:last').attr("id", "remove" + fibreCount);

            $("#fibreTable").find('#fibreNo:last').attr('name', 'fibreNo['+fibreCount+']').attr("id", "fibreNo" + fibreCount).css("text-align", "center").val(fibreCount + 1);
            $("#fibreTable").find('#fibreName:last').attr('name', 'fibreName['+fibreCount+']').attr("id", "fibreName" + fibreCount).css("text-align", "center");
            $("#fibreTable").find('#fibreType:last').attr('name', 'fibreType['+fibreCount+']').attr("id", "fibreType" + fibreCount).css("text-align", "center");
            $("#fibreTable").find('#fibreNoOfBags:last').attr('name', 'fibreNoOfBags['+fibreCount+']').attr("id", "fibreNoOfBags" + fibreCount).css("text-align", "center");
            $("#fibreTable").find('#fibreQty:last').attr('name', 'fibreQty['+fibreCount+']').attr("id", "fibreQty" + fibreCount).css("text-align", "center");

            fibreCount++;
        });

        $('#fibreTypeMr6').on('keyup', function(){
            $('#fibreQtyMr6').trigger('change');
        });

        $('#fibreBagsMr6').on('keyup', function(){
            $('#fibreQtyMr6').trigger('change');
        });

        $('#fibreQtyMr6').on('change', function(){
            // Handle the change event for fibreQtyMr6
            var type = parseFloat($('#fibreTypeMr6').val());
            var bags = parseFloat($('#fibreBagsMr6').val());
            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });

        $('#fibreTypeRpf').on('keyup', function(){
            $('#fibreQtyRpf').trigger('change');
        });

        $('#fibreBagsRpf').on('keyup', function(){
            $('#fibreQtyRpf').trigger('change');
        });

        $('#fibreQtyRpf').on('change', function(){
            // Handle the change event for fibreQtyRpf
            var type = parseFloat($('#fibreTypeRpf').val());
            var bags = parseFloat($('#fibreBagsRpf').val());
            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });

        $('#fibreTypeNova').on('keyup', function(){
            $('#fibreQtyNova').trigger('change');
        });

        $('#fibreBagsNova').on('keyup', function(){
            $('#fibreQtyNova').trigger('change');
        });

        $('#fibreQtyNova').on('change', function(){
            // Handle the change event for fibreQtyNova
            var type = parseFloat($('#fibreTypeNova').val());
            var bags = parseFloat($('#fibreBagsNova').val());
            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });

        $('#fibreTypeForta').on('keyup', function(){
            $('#fibreQtyForta').trigger('change');
        });

        $('#fibreBagsForta').on('keyup', function(){
            $('#fibreQtyForta').trigger('change');
        });

        $('#fibreQtyForta').on('change', function(){
            // Handle the change event for fibreQtyForta
            var type = parseFloat($('#fibreTypeForta').val());
            var bags = parseFloat($('#fibreBagsForta').val());
            var qty = type * bags;
            $(this).val(qty.toFixed(2));
        });
    });

    function calculateActualLevel(status, level, height) {
        var actualLevel = 0;
        if (status == 'Empty') {
            actualLevel = parseFloat(height) - parseFloat(level);
        }else{
            actualLevel = parseFloat(level);
        }

        return actualLevel;
    }

    function calculateBitumenWeight(plantId, batchDrum, level, temp, callback){
        $.post('php/calculateBitumen.php', {
            plantId: plantId,
            batchDrum: batchDrum,
            level: level,
            temp: temp
        }, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                callback(parseFloat(obj.message.observedMT));
            }
            else if(obj.status === 'failed'){
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
                callback(0);
            }
            else{
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
                callback(0);
            }
        });
    }

    function calculateLiquid(rawMatCode, diameter, length, height, callback){
        $.post('php/calculateLiquid.php', {
            rawMatCode: rawMatCode,
            diameter: diameter,
            length: length,
            height: height
        }, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                var calculationData = {
                    'volume': parseFloat(obj.message.volumeLitres),
                    'volumeKg': parseFloat(obj.message.volumeKg),
                    'volumeMt': parseFloat(obj.message.volumeMt)
                }
                callback(calculationData);
            }
            else if(obj.status === 'failed'){
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
                callback(0);
            }
            else{
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
                callback(0);
            }
        });
    }

    function getPrevStockTake(plantId, batchDrum, declarationDate){
        if (declarationDate && plantId && batchDrum){
            // load previous diesel reading
            $('#spinnerLoading').show();
            $.post('php/getPreviousStockTake.php', {declarationDate: declarationDate, plantId: plantId, batchDrum: batchDrum}, function(data)
            {
                var obj = JSON.parse(data);
                if(obj.status === 'success'){
                    $('#addModal').find('#previousDieselReading').val(obj.message.previous_diesel || '0.00');
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
    }

    function getPo(plantCode, batchDrum, declarationDate){
        if (declarationDate && plantCode && batchDrum){
            // load previous diesel reading
            $('#spinnerLoading').show();
            $.post('php/getPurchaseOrder.php', {type: 'StockTake', declarationDate: declarationDate, userID: plantCode, batchDrum: batchDrum}, function(data)
            {
                var obj = JSON.parse(data);
                if(obj.status === 'success'){
                    $('#addModal').find('#bitumenIncoming').val(obj.message.bitumenIncoming || 0);
                    $('#addModal').find('#dieselIncoming').val(obj.message.dieselIncoming || 0);
                    $('#addModal').find('#lfoIncoming').val(obj.message.lfoIncoming || 0);
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
    }

    function loadAssets(plant, batchDrum){
        $('#spinnerLoading').show();
        $.post('php/getPlantAssets.php', {plantId: plant, batchDrum: batchDrum}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                if (obj.message.length > 0){
                    // Reset Bitumen Table
                    $('#bitumenTable').html('');
                    bitumenCount = 0;

                    // Reset LFO Table
                    $('#lfoTable').html('');
                    lfoCount = 0;
                    
                    // Reset Diesel Table
                    $('#dieselTable').html('');
                    dieselCount = 0;
                    
                    // Reset PG76 Table
                    $('#pg76Table').html('');
                    pg76Count = 0;
                    
                    obj.message.forEach(function(asset) {
                        if(asset.type == 'Bitumen') {
                            $('.add-bitumen').trigger('click', [asset]);
                        }else if(asset.type == 'LFO') {
                            $('.add-lfo').trigger('click', [asset]);
                        }else if (asset.type == 'Diesel') {
                            $('.add-diesel').trigger('click', [asset]);
                        }else if (asset.type == 'PG 76') {
                            $('.add-pg-76').trigger('click', [asset]);
                        }
                    });
                }else{
                    // Reset Bitumen Table
                    $('#bitumenTable').html('');
                    bitumenCount = 0;

                    // Reset LFO Table
                    $('#lfoTable').html('');
                    lfoCount = 0;

                    // Reset Diesel Table
                    $('#dieselTable').html('');
                    dieselCount = 0;

                    // Reset PG76 Table
                    $('#pg76Table').html('');
                    pg76Count = 0;
                }
                
                $('#spinnerLoading').hide();
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

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getBitumen.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#bitumenId').val(obj.message.id);
                $('#addModal').find('#plant').val(obj.message.plant_id).select2('destroy').select2();
                $('#addModal').find('#plantCode').val(obj.message.plant_code);
                $('#addModal').find('#batchDrum').val(obj.message.batch_drum).select2('destroy').select2();
                $('#addModal').find('#datetime').val(formatDate4(new Date(obj.message.declaration_datetime)));

                // Bitumen Table Processing
                $('#bitumenTable').html('');
                bitumenCount = 0;
                if (obj.message.sixtysevn.length > 0){
                    for(var i = 0; i < obj.message.sixtysevn.length; i++){
                        var item = obj.message.sixtysevn[i]; 
                        var $addContents = $("#bitumenDetail").clone();
                        $("#bitumenTable").append($addContents.html());

                        $("#bitumenTable").find('.details:last').attr("id", "detail" + bitumenCount);
                        $("#bitumenTable").find('.details:last').attr("data-index", bitumenCount);
                        $("#bitumenTable").find('#remove:last').attr("id", "remove" + bitumenCount);

                        $("#bitumenTable").find('#no:last').attr('name', 'no['+bitumenCount+']').attr("id", "no" + bitumenCount).css("text-align", "center").val(bitumenCount + 1);
                        $("#bitumenTable").find('#name:last').attr('name', 'name['+bitumenCount+']').attr("id", "name" + bitumenCount).css("text-align", "center").val(item.name || '');
                        $("#bitumenTable").find('#bitumenStatus:last').attr('name', 'bitumenStatus['+bitumenCount+']').attr("id", "bitumenStatus" + bitumenCount).val(item.bitumenStatus || '');
                        $("#bitumenTable").find('#temp:last').attr('name', 'temp['+bitumenCount+']').attr("id", "temp" + bitumenCount).css("text-align", "center").val(item.temperature);
                        $("#bitumenTable").find('#level:last').attr('name', 'level['+bitumenCount+']').attr("id", "level" + bitumenCount).css("text-align", "center").val(item.level);
                        $("#bitumenTable").find('#actualLevel:last').attr('name', 'actualLevel['+bitumenCount+']').attr("id", "actualLevel" + bitumenCount).css("text-align", "center").val(item.actualLevel || '0.00');
                        $("#bitumenTable").find('#sixtyseventy:last').attr('name', 'sixtyseventy['+bitumenCount+']').attr("id", "sixtyseventy" + bitumenCount).css("text-align", "center").val(item.sixtyseventy);

                        // Hidden fields
                        $("#bitumenTable").find('#assetId:last').attr('name', 'assetId['+bitumenCount+']').attr("id", "assetId" + bitumenCount).val(item.assetId || '');
                        $("#bitumenTable").find('#bitumenLength:last').attr('name', 'bitumenLength['+bitumenCount+']').attr("id", "bitumenLength" + bitumenCount).val(item.assetLength || '');
                        $("#bitumenTable").find('#bitumenHeight:last').attr('name', 'bitumenHeight['+bitumenCount+']').attr("id", "bitumenHeight" + bitumenCount).val(item.assetHeight || '');
                        $("#bitumenTable").find('#bitumenDiameter:last').attr('name', 'bitumenDiameter['+bitumenCount+']').attr("id", "bitumenDiameter" + bitumenCount).val(item.assetDiameter || '');

                        bitumenCount++;
                    }
                }
                $('#addModal').find('#bitumenIncoming').val(obj.message.bitumenIncoming);
                $('#addModal').find('#totalSixtySeventy').val(obj.message.totalSixtySeventy);
                // $('#addModal').find('#totalTemp').val(obj.message.totalTemp);
                // $('#addModal').find('#totalLevel').val(obj.message.totalLevel);

                // LFO Table Processing
                $('#lfoTable').html('');
                lfoCount = 0;
                if (obj.message.lfo.length > 0){
                    for(var i = 0; i < obj.message.lfo.length; i++){
                        var item = obj.message.lfo[i]; 
                        var $addContents = $("#lfoDetail").clone();
                        $("#lfoTable").append($addContents.html());

                        $("#lfoTable").find('.details:last').attr("id", "detail" + lfoCount);
                        $("#lfoTable").find('.details:last').attr("data-index", lfoCount);
                        $("#lfoTable").find('#remove:last').attr("id", "remove" + lfoCount);

                        $("#lfoTable").find('#lfoNo:last').attr('name', 'lfoNo['+lfoCount+']').attr("id", "lfoNo" + lfoCount).css("text-align", "center").val(lfoCount + 1);
                        $("#lfoTable").find('#lfoName:last').attr('name', 'lfoName['+lfoCount+']').attr("id", "lfoName" + lfoCount).css("text-align", "center").val(item.lfoName);
                        $("#lfoTable").find('#lfoStatus:last').attr('name', 'lfoStatus['+lfoCount+']').attr("id", "lfoStatus" + lfoCount).val(item.lfoStatus);
                        $("#lfoTable").find('#lfoLevel:last').attr('name', 'lfoLevel['+lfoCount+']').attr("id", "lfoLevel" + lfoCount).css("text-align", "center").val(item.lfoLevel);
                        $("#lfoTable").find('#lfoActualLevel:last').attr('name', 'lfoActualLevel['+lfoCount+']').attr("id", "lfoActualLevel" + lfoCount).css("text-align", "center").val(item.lfoActualLevel);
                        $("#lfoTable").find('#lfoVolume:last').attr('name', 'lfoVolume['+lfoCount+']').attr("id", "lfoVolume" + lfoCount).css("text-align", "center").val(item.lfoVolume);
                        $("#lfoTable").find('#lfoWeight:last').attr('name', 'lfoWeight['+lfoCount+']').attr("id", "lfoWeight" + lfoCount).css("text-align", "center").val(item.lfoWeight);

                        $("#lfoTable").find('#lfoAssetId:last').attr('name', 'lfoAssetId['+lfoCount+']').attr("id", "lfoAssetId" + lfoCount).val(item.lfoAssetId || '');
                        $("#lfoTable").find('#lfoLength:last').attr('name', 'lfoLength['+lfoCount+']').attr("id", "lfoLength" + lfoCount).val(item.lfoLength || '');
                        $("#lfoTable").find('#lfoHeight:last').attr('name', 'lfoHeight['+lfoCount+']').attr("id", "lfoHeight" + lfoCount).val(item.lfoHeight || '');
                        $("#lfoTable").find('#lfoDiameter:last').attr('name', 'lfoDiameter['+lfoCount+']').attr("id", "lfoDiameter" + lfoCount).val(item.lfoDiameter || '');

                        lfoCount++;
                    }
                }
                $('#addModal').find('#lfoIncoming').val(obj.message.lfoIncoming);
                $('#addModal').find('#totalLfo').val(obj.message.totalLfo);
                $('#addModal').find('#lfoLastMeterReading').val(obj.message.lfoLastMeterReading);

                // Diesel Table Processing
                // $('#addModal').find('#dieselSupplierTransport').val(obj.message.dieselSupplierTransport);
                // $('#addModal').find('#dieselSupplierHotoil').val(obj.message.dieselSupplierHotoil);
                // $('#addModal').find('#dieselSupplierBurner').val(obj.message.dieselSupplierBurner);
                // $('#addModal').find('#dieselWeightTransport').val(obj.message.dieselWeightTransport);
                // $('#addModal').find('#dieselWeightHotoil').val(obj.message.dieselWeightHotoil);
                // $('#addModal').find('#dieselWeightBurner').val(obj.message.dieselWeightBurner);

                $('#dieselTable').html('');
                dieselCount = 0;
                if (obj.message.diesel.length > 0){
                    for(var i = 0; i < obj.message.diesel.length; i++){
                        var item = obj.message.diesel[i]; 
                        var $addContents = $("#dieselDetail").clone();
                        $("#dieselTable").append($addContents.html());

                        $("#dieselTable").find('.details:last').attr("id", "detail" + dieselCount);
                        $("#dieselTable").find('.details:last').attr("data-index", dieselCount);
                        $("#dieselTable").find('#remove:last').attr("id", "remove" + dieselCount);

                        $("#dieselTable").find('#dieselNo:last').attr('name', 'dieselNo['+dieselCount+']').attr("id", "dieselNo" + dieselCount).css("text-align", "center").val(dieselCount + 1);
                        $("#dieselTable").find('#dieselName:last').attr('name', 'dieselName['+dieselCount+']').attr("id", "dieselName" + dieselCount).css("text-align", "center").val(item.dieselName);
                        $("#dieselTable").find('#dieselStatus:last').attr('name', 'dieselStatus['+dieselCount+']').attr("id", "dieselStatus" + dieselCount).val(item.dieselStatus);
                        $("#dieselTable").find('#dieselLevel:last').attr('name', 'dieselLevel['+dieselCount+']').attr("id", "dieselLevel" + dieselCount).css("text-align", "center").val(item.dieselLevel);
                        $("#dieselTable").find('#dieselActualLevel:last').attr('name', 'dieselActualLevel['+dieselCount+']').attr("id", "dieselActualLevel" + dieselCount).css("text-align", "center").val(item.dieselActualLevel);
                        $("#dieselTable").find('#dieselVolume:last').attr('name', 'dieselVolume['+dieselCount+']').attr("id", "dieselVolume" + dieselCount).css("text-align", "center").val(item.dieselVolume);
                        $("#dieselTable").find('#dieselWeight:last').attr('name', 'dieselWeight['+dieselCount+']').attr("id", "dieselWeight" + dieselCount).css("text-align", "center").val(item.dieselWeight);
                        // $("#dieselTable").find('#dieselSupplier:last').attr('name', 'dieselSupplier['+dieselCount+']').attr("id", "dieselSupplier" + dieselCount).css("text-align", "center").val(item.dieselSupplier).trigger('change');
                        // $("#dieselTable").find('#dieselUsage:last').attr('name', 'dieselUsage['+dieselCount+']').attr("id", "dieselUsage" + dieselCount).css("text-align", "center").val(item.dieselUsage).trigger('change');

                        // Hidden fields
                        $("#dieselTable").find('#dieselAssetId:last').attr('name', 'dieselAssetId['+dieselCount+']').attr("id", "dieselAssetId" + dieselCount).val(item.dieselAssetId || '');
                        $("#dieselTable").find('#dieselLength:last').attr('name', 'dieselLength['+dieselCount+']').attr("id", "dieselLength" + dieselCount).val(item.dieselLength || '');
                        $("#dieselTable").find('#dieselHeight:last').attr('name', 'dieselHeight['+dieselCount+']').attr("id", "dieselHeight" + dieselCount).val(item.dieselHeight || '');
                        $("#dieselTable").find('#dieselDiameter:last').attr('name', 'dieselDiameter['+dieselCount+']').attr("id", "dieselDiameter" + dieselCount).val(item.dieselDiameter || '');


                        // Initialize all Select2 elements in the modal
                        $('#addModal .select2').select2({
                            allowClear: true,
                            placeholder: "Please Select",
                            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
                        });

                        // Apply custom styling to Select2 elements in addModal
                        $('#addModal .select2-container .select2-selection--single').css({
                            'padding-top': '4px',
                            'padding-bottom': '4px',
                            'height': 'auto'
                        });

                        $('#addModal .select2-container .select2-selection__arrow').css({
                            'padding-top': '33px',
                            'height': 'auto'
                        });

                        dieselCount++;
                    }
                }
                $('#addModal').find('#dieselIncoming').val(obj.message.dieselIncoming);
                $('#addModal').find('#previousDieselReading').val(obj.message.previousDieselReading);
                $('#addModal').find('#dieselLastMeterReading').val(obj.message.dieselLastMeterReading);
                $('#addModal').find('#totalDiesel').val(obj.message.totalDiesel);

                // Other Diesel Table Processing
                $('#otherDieselTable').html('');
                otherDieselCount = 0;
                if (obj.message.other_diesel.length > 0){
                    for(var i = 0; i < obj.message.other_diesel.length; i++){
                        var item = obj.message.other_diesel[i]; 
                        var $addContents = $("#otherDieselDetail").clone();
                        $("#otherDieselTable").append($addContents.html());

                        $("#otherDieselTable").find('.details:last').attr("id", "detail" + otherDieselCount);
                        $("#otherDieselTable").find('.details:last').attr("data-index", otherDieselCount);
                        $("#otherDieselTable").find('#remove:last').attr("id", "remove" + otherDieselCount);

                        $("#otherDieselTable").find('#otherDieselNo:last').attr('name', 'otherDieselNo['+otherDieselCount+']').attr("id", "otherDieselNo" + otherDieselCount).css("text-align", "center").val(otherDieselCount + 1);
                        $("#otherDieselTable").find('#otherDieselType:last').attr('name', 'otherDieselType['+otherDieselCount+']').attr("id", "otherDieselType" + otherDieselCount).val(item.otherDieselType);
                        $("#otherDieselTable").find('#otherDieselVehicleNo:last').attr('name', 'otherDieselVehicleNo['+otherDieselCount+']').attr("id", "otherDieselVehicleNo" + otherDieselCount).css("text-align", "center").val(item.otherDieselVehicleNo);
                        $("#otherDieselTable").find('#otherDieselFirstReading:last').attr('name', 'otherDieselFirstReading['+otherDieselCount+']').attr("id", "otherDieselFirstReading" + otherDieselCount).css("text-align", "center").val(item.otherDieselFirstReading);
                        $("#otherDieselTable").find('#otherDieselSecondReading:last').attr('name', 'otherDieselSecondReading['+otherDieselCount+']').attr("id", "otherDieselSecondReading" + otherDieselCount).css("text-align", "center").val(item.otherDieselSecondReading);
                        $("#otherDieselTable").find('#otherDieselUsage:last').attr('name', 'otherDieselUsage['+otherDieselCount+']').attr("id", "otherDieselUsage" + otherDieselCount).css("text-align", "center").val(item.otherDieselUsage);

                        otherDieselCount++;
                    }
                }

                $('#addModal').find('#otherDieselTotalTransportUsage').val(obj.message.otherDieselTotalTransportUsage);

                // Hotoil Table Processing
                $('#hotoilTable').html('');
                hotoilCount = 0;
                if (obj.message.hotoil.length > 0){
                    for(var i = 0; i < obj.message.hotoil.length; i++){
                        var item = obj.message.hotoil[i]; 
                        var $addContents = $("#hotoilDetail").clone();
                        $("#hotoilTable").append($addContents.html());

                        $("#hotoilTable").find('.details:last').attr("id", "detail" + hotoilCount);
                        $("#hotoilTable").find('.details:last').attr("data-index", hotoilCount);
                        $("#hotoilTable").find('#remove:last').attr("id", "remove" + hotoilCount);

                        $("#hotoilTable").find('#hotoilNo:last').attr('name', 'hotoilNo['+hotoilCount+']').attr("id", "hotoilNo" + hotoilCount).css("text-align", "center").val(hotoilCount + 1);
                        $("#hotoilTable").find('#hotoilWeight:last').attr('name', 'hotoilWeight['+hotoilCount+']').attr("id", "hotoilWeight" + hotoilCount).css("text-align", "center").val(item.hotoilWeight);

                        hotoilCount++;
                    }
                }
                $('#addModal').find('#totalHotoil').val(obj.message.totalHotoil);

                // PG79 Table Processing
                $('#pg76Table').html('');
                pg76Count = 0;
                if (obj.message.pg76.length > 0){ 
                    for(var i = 0; i < obj.message.pg76.length; i++){
                        var item = obj.message.pg76[i];
                        var $addContents = $("#pg76Detail").clone();
                        $("#pg76Table").append($addContents.html());

                        $("#pg76Table").find('.details:last').attr("id", "detail" + pg76Count);
                        $("#pg76Table").find('.details:last').attr("data-index", pg76Count);
                        $("#pg76Table").find('#remove:last').attr("id", "remove" + pg76Count);

                        $("#pg76Table").find('#pg76No:last').attr('name', 'pg76No['+pg76Count+']').attr("id", "pg76No" + pg76Count).css("text-align", "center").val(pg76Count + 1);
                        $("#pg76Table").find('#pg76Name:last').attr('name', 'pg76Name['+pg76Count+']').attr("id", "pg76Name" + pg76Count).css("text-align", "center").val(item.pg76Name || '');
                        $("#pg76Table").find('#pg76Status:last').attr('name', 'pg76Status['+pg76Count+']').attr("id", "pg76Status" + pg76Count).val(item.pg76Status || '');
                        $("#pg76Table").find('#pg76Temp:last').attr('name', 'pg76Temp['+pg76Count+']').attr("id", "pg76Temp" + pg76Count).css("text-align", "center").val(item.pg76Temp);
                        $("#pg76Table").find('#pg76Level:last').attr('name', 'pg76Level['+pg76Count+']').attr("id", "pg76Level" + pg76Count).css("text-align", "center").val(item.pg76Level);
                        $("#pg76Table").find('#pg76ActualLevel:last').attr('name', 'pg76ActualLevel['+pg76Count+']').attr("id", "pg76ActualLevel" + pg76Count).css("text-align", "center").val(item.pg76ActualLevel || '0.00');
                        $("#pg76Table").find('#pgSeventySix:last').attr('name', 'pgSeventySix['+pg76Count+']').attr("id", "pgSeventySix" + pg76Count).css("text-align", "center").val(item.pgSeventySix);

                        // Hidden fields
                        $("#pg76Table").find('#pg76AssetId:last').attr('name', 'pg76AssetId['+pg76Count+']').attr("id", "pg76AssetId" + pg76Count).val(item.pg76AssetId || '');
                        $("#pg76Table").find('#pg76Length:last').attr('name', 'pg76Length['+pg76Count+']').attr("id", "pg76Length" + pg76Count).val(item.pg76Length || '');
                        $("#pg76Table").find('#pg76Height:last').attr('name', 'pg76Height['+pg76Count+']').attr("id", "pg76Height" + pg76Count).val(item.pg76Height || '');
                        $("#pg76Table").find('#pg76Diameter:last').attr('name', 'pg76Diameter['+pg76Count+']').attr("id", "pg76Diameter" + pg76Count).val(item.pg76Diameter || '');

                        pg76Count++;
                    }
                }
                $('#addModal').find('#totalPg76').val(obj.message.totalPg76);

                // Fibre Table Processing
                $('#addModal').find('#fibreNameMr6').val(obj.message.fibreNameMr6);
                $('#addModal').find('#fibreTypeMr6').val(obj.message.fibreTypeMr6);
                $('#addModal').find('#fibreBagsMr6').val(obj.message.fibreBagsMr6);
                $('#addModal').find('#fibreQtyMr6').val(obj.message.fibreQtyMr6);
                $('#addModal').find('#fibreNameRpf').val(obj.message.fibreNameRpf);
                $('#addModal').find('#fibreTypeRpf').val(obj.message.fibreTypeRpf);
                $('#addModal').find('#fibreBagsRpf').val(obj.message.fibreBagsRpf);
                $('#addModal').find('#fibreQtyRpf').val(obj.message.fibreQtyRpf);
                $('#addModal').find('#fibreNameNova').val(obj.message.fibreNameNova);
                $('#addModal').find('#fibreTypeNova').val(obj.message.fibreTypeNova);
                $('#addModal').find('#fibreBagsNova').val(obj.message.fibreBagsNova);
                $('#addModal').find('#fibreQtyNova').val(obj.message.fibreQtyNova);
                $('#addModal').find('#fibreNameForta').val(obj.message.fibreNameForta);
                $('#addModal').find('#fibreTypeForta').val(obj.message.fibreTypeForta);
                $('#addModal').find('#fibreBagsForta').val(obj.message.fibreBagsForta);
                $('#addModal').find('#fibreQtyForta').val(obj.message.fibreQtyForta);

                $('#fibreTable').html('');
                fibreCount = 0;
                if (obj.message.fibre.length > 0){ 
                    for(var i = 0; i < obj.message.fibre.length; i++){
                        var item = obj.message.fibre[i];
                        var $addContents = $("#fibreDetail").clone();
                        $("#fibreTable").append($addContents.html());

                        $("#fibreTable").find('.details:last').attr("id", "detail" + fibreCount);
                        $("#fibreTable").find('.details:last').attr("data-index", fibreCount);
                        $("#fibreTable").find('#remove:last').attr("id", "remove" + fibreCount);

                        $("#fibreTable").find('#fibreNo:last').attr('name', 'fibreNo['+fibreCount+']').attr("id", "fibreNo" + fibreCount).css("text-align", "center").val(fibreCount + 1);
                        $("#fibreTable").find('#fibreName:last').attr('name', 'fibreName['+fibreCount+']').attr("id", "fibreName" + fibreCount).css("text-align", "center").val(item.fibreName);
                        $("#fibreTable").find('#fibreType:last').attr('name', 'fibreType['+fibreCount+']').attr("id", "fibreType" + fibreCount).css("text-align", "center").val(item.fibreType);
                        $("#fibreTable").find('#fibreNoOfBags:last').attr('name', 'fibreNoOfBags['+fibreCount+']').attr("id", "fibreNoOfBags" + fibreCount).css("text-align", "center").val(item.fibreBags);
                        $("#fibreTable").find('#fibreQty:last').attr('name', 'fibreQty['+fibreCount+']').attr("id", "fibreQty" + fibreCount).css("text-align", "center").val(item.fibreQty);

                        fibreCount++;
                    }
                }

                // Remaining Fields Processing
                $('#addModal').find('#40mm').val(obj.message.fortymm);
                $('#addModal').find('#28mm').val(obj.message.twentyeightmm);
                $('#addModal').find('#20mm').val(obj.message.twentyMM);
                $('#addModal').find('#14mm').val(obj.message.fourteenMM);
                $('#addModal').find('#10mm').val(obj.message.tenMM);
                $('#addModal').find('#QD').val(obj.message.QD);
                $('#addModal').find('#rs1k').val(obj.message.rs1k);
                $('#addModal').find('#k140').val(obj.message.k140);
                $('#addModal').find('#ss1k').val(obj.message.ss1k);
                $('#addModal').find('#others').val(obj.message.others);
                $('#addModal').find('#transport').val(obj.message.transport);
                $('#addModal').find('#burner').val(obj.message.burner);
                $('#addModal').find('#opcDo').val(obj.message.opcDo);
                $('#addModal').find('#opcIncoming').val(obj.message.opcIncoming);
                $('#addModal').find('#opcQty').val(obj.message.opcQty);
                $('#addModal').find('#limeDo').val(obj.message.limeDo);
                $('#addModal').find('#limeIncoming').val(obj.message.limeIncoming);
                $('#addModal').find('#limeQty').val(obj.message.limeQty);

                // Initialize all Select2 elements in the modal
                $('#addModal .select2').select2({
                    allowClear: true,
                    placeholder: "Please Select",
                    dropdownParent: $('#addModal') // Ensures dropdown is not cut off
                });

                // Apply custom styling to Select2 elements in addModal
                $('#addModal .select2-container .select2-selection--single').css({
                    'padding-top': '4px',
                    'padding-bottom': '4px',
                    'height': 'auto'
                });

                $('#addModal .select2-container .select2-selection__arrow').css({
                    'padding-top': '33px',
                    'height': 'auto'
                });

                $('#addModal').modal('show');
            
                $('#siteForm').validate({
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

    function printDeclaration(id) {
        window.open('php/printStockDeclaration.php?id=' + id, '_blank');
    }
    </script>
</body>
</html>