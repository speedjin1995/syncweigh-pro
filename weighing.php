<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "layouts/config.php";

//   $lots = $db->query("SELECT * FROM lots WHERE deleted = '0'");
  $vehicles = $db->query("SELECT * FROM Vehicle WHERE status = '0'");
  $customer = $db->query("SELECT * FROM Customer WHERE status = '0'");
  $product = $db->query("SELECT * FROM Product WHERE status = '0'");
  $transporter = $db->query("SELECT * FROM Transporter WHERE status = '0'");
  $destination = $db->query("SELECT * FROM Destination WHERE status = '0'");
  $unit = $db->query("SELECT * FROM Unit WHERE status = '0'");
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

    <?php include 'layouts/head-css.php'; ?>

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

                            <div class="row">
                                <div class="col-xl-3 col-md-6">
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
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">$<span
                                                            class="counter-value" data-target="559.25">0</span>k
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

                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                        Purchase</p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <h5 class="text-danger fs-14 mb-0">
                                                        <i class="ri-arrow-right-down-line fs-13 align-middle"></i>
                                                        -3.57 %
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                            class="counter-value" data-target="36894">0</span></h4>
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
                                    <div class="card card-animate">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                    Miscellaneous</p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <h5 class="text-success fs-14 mb-0">
                                                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                                                        +29.08 %
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span
                                                            class="counter-value" data-target="183.35">0</span>M
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
                                                    <form action="javascript:void(0);">
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-center">
                                                                <div class="col-xl-12 col-md-12 col-md-12">
                                                                    <div class="card bg-primary">
                                                                        <div class="card-body">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div>
                                                                                    <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value"
                                                                                            data-target="0">0</span> Kg</h2>
                                                                                </div>
                                                                                <div class="connected-align">
                                                                                    <div class="input-group-text color-palette" id="indicatorConnected"><i>Indicator Connected</i></div>
                                                                                    <div class="input-group-text bg-danger color-palette" id="checkingConnection"><i>Checking Connection</i></div>
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

                                                        <div class="row col-12">
                                                            <div class="col-xxl-8 col-lg-8">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionId" class="col-sm-4 col-form-label">Transaction ID</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="transactionId" placeholder="Transaction ID">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionDate" class="col-sm-4 col-form-label">Transaction Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="transactionDate">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="invoiceNo" placeholder="Invoice No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="weightType" class="col-sm-4 col-form-label">Weight Type</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="weightType" class="form-select" data-choices data-choices-sorting="true" >
                                                                                            <option selected>Normal</option>
                                                                                            <option>Container</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="deliveryNo" class="col-sm-4 col-form-label">Delivery No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="deliveryNo" placeholder="Delivery No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionStatus" class="col-sm-4 col-form-label">Transaction Status</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" class="form-select" data-choices data-choices-sorting="true" >
                                                                                            <option selected>Sales</option>
                                                                                            <option>Purchase</option>
                                                                                            <option>Local</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="purchaseOrder" class="col-sm-4 col-form-label">Purchase Order</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="purchaseOrder" placeholder="Purchase Order">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="weighbridge" class="col-sm-4 col-form-label">Weighbridge</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="number" class="form-control" id="weighbridge" placeholder="Weigh1" disabled>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="containerNo" class="col-sm-4 col-form-label">Container No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="containerNo" placeholder="Container No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="indicatorId" class="col-sm-4 col-form-label">Indicator ID</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" class="form-select" data-choices data-choices-sorting="true" >
                                                                                            <option selected>ind12345</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="customerName" class="col-sm-4 col-form-label">Customer Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="customerName" name="customerName" data-choices data-choices-sorting="true">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowCustomer=mysqli_fetch_assoc($customer)){ ?>
                                                                                                <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="manualWeight" class="col-sm-4 col-form-label">Manual Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="number" class="form-control" id="manualWeight" placeholder="No" disabled>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="productName" class="col-sm-4 col-form-label">Product Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="productName" name="productName" data-choices data-choices-sorting="true">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                                                                                <option value="<?=$rowProduct['id'] ?>"><?=$rowProduct['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transporter" class="col-sm-4 col-form-label">Transporter</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="transporter" name="transporter" data-choices data-choices-sorting="true">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowTransporter=mysqli_fetch_assoc($transporter)){ ?>
                                                                                                <option value="<?=$rowTransporter['id'] ?>"><?=$rowTransporter['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                          
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="destination" class="col-sm-4 col-form-label">Destination</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="destination" name="destination" data-choices data-choices-sorting="true">
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowDestination=mysqli_fetch_assoc($destination)){ ?>
                                                                                                <option value="<?=$rowDestination['id'] ?>"><?=$rowDestination['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                         
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="reduceWeight" class="col-sm-4 col-form-label">Reduce Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="reduceWeight" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="otherRemarks" class="col-sm-2 col-form-label">Other Remarks</label>
                                                                                    <div class="col-sm-10">
                                                                                        <textarea class="form-control" id="otherRemarks" rows="3" placeholder="Other Remarks"></textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-xxl-4 col-lg-4">
                                                                <div class="column">
                                                                    <div class="col-xxl-12 col-lg-12">
                                                                        <div class="card bg-light">
                                                                            <div class="card-body">
                                                                                <div class="row mb-3">
                                                                                    <label for="vehiclePlateNo1" class="col-sm-4 col-form-label">
                                                                                        Vehicle Plate No 1
                                                                                    </label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <div class="input-group-text">
                                                                                                <input class="form-check-input mt-0" id="manualVehicle" name="manualVehicle" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                            </div>
                                                                                            <input type="text" class="form-control" id="vehicleNoTxt" name="vehicleNoTxt" placeholder="Vehicle Plate No" style="display:none">
                                                                                            <div class="col-10 index-vehicle">
                                                                                                <select class="form-select" id="vehiclePlateNo1" name="vehiclePlateNo1" data-choices data-choices-sorting="true">
                                                                                                    <option selected="-">-</option>
                                                                                                    <?php while($row2=mysqli_fetch_assoc($vehicles)){ ?>
                                                                                                        <option value="<?=$row2['veh_number'] ?>" data-weight="<?=$row2['vehicle_weight'] ?>"><?=$row2['veh_number'] ?></option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="grossIncoming" class="col-sm-4 col-form-label">1.Gross Incoming</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="grossIncoming" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                            <button class="input-group-text btn btn-primary fs-5"><i class="mdi mdi-sync"></i></button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="grossIncomingDate" class="col-sm-4 col-form-label">Gross Incoming Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="grossIncomingDate">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="tareOutgoing" class="col-sm-4 col-form-label">2.Tare Outgoing</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="tareOutgoing" placeholder="Tare Outgoing">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                            <button class="input-group-text btn btn-primary fs-5"><i class="mdi mdi-sync"></i></button>
                                                                                        </div>                                                                                       
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="tareOutgoingDate" class="col-sm-4 col-form-label">Tare Outgoing Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="tareOutgoingDate">
                                                                                    </div>
                                                                                </div>                                                                        
                                                                                <div class="row mb-3">
                                                                                    <label for="nettWeight" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="nettWeight" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- <div class="d-grid" >
                                                                                    <button class="btn btn-primary" type="button">Accepted Indicator Weight Reading Value</button>
                                                                                </div> -->
                                                                            </div>                                                                                                                                  
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xxl-12 col-lg-12" id="containerCard" style="display:none;">
                                                                        <div class="card bg-light">
                                                                            <div class="card-body">
                                                                                <div class="row mb-3">
                                                                                    <label for="vehiclePlateNo2" class="col-sm-4 col-form-label">Vehicle Plate No 2</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="vehiclePlateNo1" placeholder="Vehicle Plate No 2">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="grossIncoming2" class="col-sm-4 col-form-label">3.Gross Incoming</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="grossIncoming2" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="grossIncomingDate2" class="col-sm-4 col-form-label">Gross Incoming Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="grossIncomingDate2">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="tareOutgoing2" class="col-sm-4 col-form-label">4.Tare Outgoing</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="number" class="form-control" id="tareOutgoing" placeholder="Tare Outgoing">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="tareOutgoingDate2" class="col-sm-4 col-form-label">Tare Outgoing Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="Tare Outgoing Date">
                                                                                    </div>
                                                                                </div>                                                                        
                                                                                <div class="row mb-3">
                                                                                    <label for="nettWeight2" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="nettWeight2" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="d-grid">
                                                                                    <button class="btn btn-primary" type="button">Accepted Indicator Weight Reading Value</button>
                                                                                </div>                                                                        
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

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
                                                                <button type="button" id="addVehicle" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                                Add New Weight
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="vehicleTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Status</th>
                                                                    <th>Weight Status</th>
                                                                    <th>Serial No</th>
                                                                    <th>Vehicle No</th>
                                                                    <th>Product Description Detail</th>
                                                                    <th>Incoming(Gross Weight)</th>
                                                                    <th>Incoming(Gross) Date Time</th>
                                                                    <th>Outgoing(Tare) Weight</th>
                                                                    <th>Outgoing(Tare) Date Time</th>
                                                                    <th>ToTal Nett Weight</th>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="assets/js/pages/datatables.init.js"></script>

    <script type="text/javascript">
        $(function () {
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
                    $('.index-vehicle').hide();
                    $('#vehicleNoTxt').show();
                }
                else{
                    $(this).val(0);
                    $('#vehicleNoTxt').hide();
                    $('.index-vehicle').show();
                }
            });
        });
    </script>

    </body>

    </html>