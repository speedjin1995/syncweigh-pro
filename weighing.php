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
  $supplier = $db->query("SELECT * FROM Supplier WHERE status = '0'");
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

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include jQuery Validate plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <?php include 'layouts/head-css.php'; ?>

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
                                                            <label for="fromDateSearch" class="form-label">First Name</label>
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="fromDateSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="toDateSearch" class="form-label">Last Name</label>
                                                            <input type="date" class="form-control" data-provider="flatpickr" id="toDateSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="statusSearch" class="form-label">Status</label>
                                                            <select id="statusSearch" class="form-select"  >
                                                                <option selected>Sales</option>
                                                                <option>Purchase</option>
                                                                <option>Local</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="customerNoSearch" class="form-label">Customer No</label>
                                                            <select id="customerNoSearch" class="form-select" >
                                                                <option selected>...</option>
                                                                <!-- <option>Purchase</option>
                                                                <option>Local</option> -->
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
                                                            <label for="invoiceNoSearch" class="form-label">Invoice No</label>
                                                            <input type="text" class="form-control" placeholder="Invoice No" id="invoiceNoSearch">
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="batchNoSearch" class="form-label">Batch No</label>
                                                            <input type="text" class="form-control" placeholder="Batch No" id="batchNoSearch">
                                                        </div>
                                                    </div><!--end col-->                                                
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="ForminputState" class="form-label">Product</label>
                                                            <select id="transactionStatusSearch" class="form-select" >
                                                                <option selected>...</option>
                                                                <!-- <option>Purchase</option>
                                                                <option>Local</option> -->
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="bx bx-search-alt"></i>
                                                                Search</button>
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

                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                                    Local</p>
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
                                                            class="counter-value" data-target="183.35">0</span>
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
                                                                                        <input type="text" class="form-control" id="transactionId" name="transactionId" placeholder="Transaction ID" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>                                                                                       
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
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
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="Invoice No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
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
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="deliveryNo" class="col-sm-4 col-form-label">Delivery No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="deliveryNo" name="deliveryNo" placeholder="Delivery No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionStatus" class="col-sm-4 col-form-label">Transaction Status</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" name="transactionStatus" class="form-select">
                                                                                            <option value="Sales" selected>Sales</option>
                                                                                            <option value="Purchase">Purchase</option>
                                                                                            <option value="Local">Local</option>
                                                                                            <option value="MISC">MISC</option>
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
                                                                                        <input type="text" class="form-control" id="purchaseOrder" name="purchaseOrder" placeholder="Purchase Order">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="weighbridge" class="col-sm-4 col-form-label">Weighbridge</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly" id="weighbridge" name="weighbridge" placeholder="Weigh1" value="Weigh1" readonly>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="containerNo" class="col-sm-4 col-form-label">Container No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="containerNo" name="containerNo" placeholder="Container No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="indicatorId" class="col-sm-4 col-form-label">Indicator ID</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="indicatorId" name="indicatorId" class="form-select" >
                                                                                            <option selected>ind12345</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3" id="divCustomerName">
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
                                                                            <div class="col-xxl-6 col-lg-6 mb-3" id="divSupplierName" style="display:none;">
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
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
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
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="productName" class="col-sm-4 col-form-label">Product Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select" id="productName" name="productName" >
                                                                                            <option selected="-">-</option>
                                                                                            <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                                                                                <option value="<?=$rowProduct['name'] ?>" data-code="<?=$rowProduct['product_code'] ?>" data-description="<?=$rowProduct['description'] ?>"><?=$rowProduct['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                            </div>                                                                         
                                                                            <div class="col-xxl-6 col-lg-6 mb-3" id="divSupplierWeight" style="display:none;">
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
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
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
                                                                            <div class="col-xxl-6 col-lg-6 mb-3" id="divWeightDifference"  style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="weightDifference" class="col-sm-4 col-form-label">Weight Difference</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="weightDifference" name="weightDifference" placeholder="Weight Difference">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>                                                                            
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
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
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
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
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                            </div>
                                                                            <div class="col-xxl-6 col-lg-6 mb-3">
                                                                                <div class="row">
                                                                                    <label for="totalPrice" class="col-sm-4 col-form-label">Total Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="totalPrice" name="totalPrice" placeholder="0">
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
                                                                                        <textarea class="form-control" id="otherRemarks" name="otherRemarks" rows="3" placeholder="Other Remarks"></textarea>
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
                                                                                <div class="row mb-3">
                                                                                    <label for="grossIncoming" class="col-sm-4 col-form-label">1.Gross Incoming</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <!-- <div class="input-group-text">
                                                                                                <input class="form-check-input mt-0" id="manual" name="manual" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                            </div>                                                                                             -->
                                                                                            <input type="number" class="form-control input-readonly" id="grossIncoming" name="grossIncoming" placeholder="0.00" value="234" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                            <button class="input-group-text btn btn-primary fs-5"><i class="mdi mdi-sync"></i></button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-3">
                                                                                    <label for="grossIncomingDate" class="col-sm-4 col-form-label">Gross Incoming Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="grossIncomingDate" name="grossIncomingDate" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the date.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-3">
                                                                                    <label for="tareOutgoing" class="col-sm-4 col-form-label">2.Tare Outgoing</label>
                                                                                    <div class="col-sm-8">                                                                                     
                                                                                        <div class="input-group">
                                                                                            <!-- <div class="input-group-text">
                                                                                                <input class="form-check-input mt-0" id="manualOutgoing" name="manualOutgoing" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                            </div>                                                                                                -->
                                                                                            <input type="number" class="form-control input-readonly" id="tareOutgoing" name="tareOutgoing" placeholder="0.00" value="234" readonly>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                            <button class="input-group-text btn btn-primary fs-5"><i class="mdi mdi-sync"></i></button>
                                                                                        </div>                                                                                       
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="tareOutgoingDate" class="col-sm-4 col-form-label">Tare Outgoing Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="tareOutgoingDate" name="tareOutgoingDate" required>
                                                                                        <div class="invalid-feedback">
                                                                                                Please fill in the date.
                                                                                        </div>
                                                                                    </div>
                                                                                </div>                                                                        
                                                                                <div class="row mb-3">
                                                                                    <label for="nettWeight" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="nettWeight" name="nettWeight" placeholder="0" required>
                                                                                            <div class="input-group-text">Kg</div>
                                                                                            <div class="invalid-feedback">
                                                                                                Please fill in the field.
                                                                                            </div>
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
                                                                                        <input type="text" class="form-control" id="vehiclePlateNo2" name="vehiclePlateNo2" placeholder="Vehicle Plate No 2">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="grossIncoming2" class="col-sm-4 col-form-label">3.Gross Incoming</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="grossIncoming2" name="grossIncoming2" placeholder="0">
                                                                                            <div class="input-group-text">Kg</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="grossIncomingDate2" class="col-sm-4 col-form-label">Gross Incoming Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="grossIncomingDate2" name="grossIncomingDate2">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="tareOutgoing2" class="col-sm-4 col-form-label">4.Tare Outgoing</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="number" class="form-control" id="tareOutgoing2" name="tareOutgoing2" placeholder="Tare Outgoing">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mb-3">
                                                                                    <label for="tareOutgoingDate2" class="col-sm-4 col-form-label">Tare Outgoing Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" placeholder="Tare Outgoing Date" id="tareOutgoingDate2" name="tareOutgoingDate2">
                                                                                    </div>
                                                                                </div>                                                                        
                                                                                <div class="row mb-3">
                                                                                    <label for="nettWeight2" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="nettWeight2" name="nettWeight2" placeholder="0">
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
                                                                <button type="button" class="btn btn-primary" id="submitWeight">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                        
                                                        <input type="hidden" id="customerCode" name="customerCode">
                                                        <input type="hidden" id="destinationCode" name="destinationCode">
                                                        <input type="hidden" id="productCode" name="productCode">
                                                        <input type="hidden" id="productDescription" name="productDescription">
                                                        <input type="hidden" id="transporterCode" name="transporterCode">
                                                        <input type="hidden" id="supplierCode" name="supplierCode">
                                                        <input type="hidden" id="id" name="id">  
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
                                                                <button type="button" id="addWeight" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
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
                                                                    <th>No</th>
                                                                    <th>Status</th>
                                                                    <th>Weight Status</th>
                                                                    <th>Transaction Id</th>
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

    <script type="text/javascript">
        $(function () {

            table = $("#weightTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':'php/loadWeight.php'
                },
                'columns': [
                    { data: 'id' },
                    { data: 'transaction_status' },
                    { data: 'weight_type' },
                    { data: 'transaction_id' },
                    { data: 'lorry_plate_no1' },
                    { data: 'product_description' },
                    { data: 'gross_weight1' },
                    { data: 'gross_weight1_date' },
                    { data: 'tare_weight1' },
                    { data: 'tare_weight1_date' },
                    { data: 'nett_weight1' },
                    { 
                        data: 'id',
                        render: function ( data, type, row ) {
                            // return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                            return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                            '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
                            '<li><a class="dropdown-item remove-item-btn" id="deactivate'+data+'" onclick="deactivate('+data+')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete </a></li></ul></div>';
                        }
                    }
                ]       
            });

            $('#submitWeight').on('click', function(){
                if($('#weightForm').valid()){
                    $('#spinnerLoading').show();
                    $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                        var obj = JSON.parse(data); 
                        if(obj.status === 'success')
                        {
                            table.ajax.reload();
                            $('#spinnerLoading').hide();
                            $('#addModal').modal('hide');
                            $("#successBtn").attr('data-toast-text', obj.message);
                            $("#successBtn").click();
                        }
                        else if(obj.status === 'failed')
                        {
                            $('#spinnerLoading').hide();
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                        else
                        {

                        }
                    });
                }
                // }
            });

            $('#addWeight').on('click', function(){
                $('#addModal').find('#id').val("");
                $('#addModal').find('#transactionId').val("");
                $('#addModal').find('#transactionStatus').val("");
                $('#addModal').find('#weightType').val("");
                $('#addModal').find('#transactionDate').val("");
                $('#addModal').find('#vehiclePlateNo1').val("");

                // if(obj.message.vehicleNoTxt != null)
                // {
                //     $('#addModal').find('#vehicleNoTxt').val(obj.message.lorry_plate_no1);
                // }

                $('#addModal').find('#vehiclePlateNo2').val("");
                $('#addModal').find('#supplierWeight').val("");
                $('#addModal').find('#customerCode').val("");
                $('#addModal').find('#customerName').val("");
                $('#addModal').find('#supplierCode').val("");
                $('#addModal').find('#supplierName').val("");
                $('#addModal').find('#productCode').val("");
                $('#addModal').find('#productName').val("");
                $('#addModal').find('#containerNo').val("");
                $('#addModal').find('#invoiceNo').val("");
                $('#addModal').find('#purchaseOrder').val("");
                $('#addModal').find('#deliveryNo').val("");
                $('#addModal').find('#transporterCode').val("");
                $('#addModal').find('#transporter').val("");
                $('#addModal').find('#destinationCode').val("");
                $('#addModal').find('#destination').val("");
                $('#addModal').find('#otherRemarks').val("");
                $('#addModal').find('#grossIncoming').val("");
                $('#addModal').find('#grossIncomingDate').val("");
                $('#addModal').find('#tareOutgoing').val("");
                $('#addModal').find('#tareOutgoingDate').val("");
                $('#addModal').find('#nettWeight').val("");
                $('#addModal').find('#grossIncoming2').val("");
                $('#addModal').find('#grossIncomingDate2').val("");
                $('#addModal').find('#tareOutgoing2').val("");
                $('#addModal').find('#tareOutgoingDate2').val("");
                $('#addModal').find('#nettWeight2').val("");
                $('#addModal').find('#reduceWeight').val("");
                // $('#addModal').find('#vehicleNo').val(obj.message.final_weight);
                $('#addModal').find('#weightDifference').val("");
                // $('#addModal').find('#id').val(obj.message.is_complete);
                // $('#addModal').find('#vehicleNo').val(obj.message.is_cancel);
                $('#addModal').find('#manualWeight').val("");
                $('#addModal').find('#indicatorId').val("");
                $('#addModal').find('#weighbridge').val("");
                $('#addModal').find('#indicatorId2').val("");
                $('#addModal').find('#productDescription').val("");
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

            $(':input[type="number"]').change(function(){
                this.value = parseFloat(this.value).toFixed(2);
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
                    $('.index-vehicle').hide();
                    $('#vehicleNoTxt').show();
                }
                else{
                    $(this).val(0);
                    $('#vehicleNoTxt').hide();
                    $('.index-vehicle').show();
                }
            });

            $('.radio-manual-weight').on('click', function(){
                if($('input[name="manualWeight"]:checked').val() == "true"){
                    // $(this).val(1);
                    $('#tareOutgoing').removeAttr('readonly');
                    $('#grossIncoming').removeAttr('readonly');
                }
                else{
                    // $(this).val(0);
                    $('#grossIncoming').attr('readonly', 'readonly');
                    $('#tareOutgoing').attr('readonly', 'readonly');
                }
            });

            $('#transactionStatus').on('change', function(){
                if($(this).val() == "Purchase" || $(this).val() == "Local")
                {
                    $('#divWeightDifference').show();
                    $('#divSupplierWeight').show();
                    $('#divSupplierName').show();
                    $('#divCustomerName').hide();
                }
                else{
                    $('#divWeightDifference').hide();
                    $('#divSupplierWeight').hide();
                    $('#divSupplierName').hide();
                    $('#divCustomerName').show();
                }
            });

            //productName
            $('#productName').on('change', function(){
                $('#productCode').val($('#productName :selected').data('code'));
                $('#productDescription').val($('#productName :selected').data('description'));
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
             //customerName
            $('#customerName').on('change', function(){
                $('#customerCode').val($('#customerName :selected').data('code'));
            });

        });

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getWeight.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#transactionId').val(obj.message.transaction_id);
                $('#addModal').find('#transactionStatus').val(obj.message.transaction_status);
                $('#addModal').find('#weightType').val(obj.message.weight_type);
                $('#addModal').find('#transactionDate').val(obj.message.transaction_date);
                $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1);

                if(obj.message.vehicleNoTxt != null)
                {
                    $('#addModal').find('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                }

                $('#addModal').find('#vehiclePlateNo2').val(obj.message.lorry_plate_no2);
                $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                $('#addModal').find('#customerCode').val(obj.message.customer_code);
                $('#addModal').find('#customerName').val(obj.message.customer_name);
                $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                $('#addModal').find('#supplierName').val(obj.message.supplier_name);
                $('#addModal').find('#productCode').val(obj.message.product_code);
                $('#addModal').find('#productName').val(obj.message.product_name);
                $('#addModal').find('#containerNo').val(obj.message.container_no);
                $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order);
                $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                $('#addModal').find('#transporter').val(obj.message.transporter);
                $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                $('#addModal').find('#destination').val(obj.message.destination);
                $('#addModal').find('#otherRemarks').val(obj.message.remarks);
                $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                $('#addModal').find('#grossIncomingDate').val(obj.message.gross_weight1_date);
                $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                $('#addModal').find('#tareOutgoingDate').val(obj.message.tare_weight1_date);
                $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);
                $('#addModal').find('#grossIncoming2').val(obj.message.gross_weight2);
                $('#addModal').find('#grossIncomingDate2').val(obj.message.gross_weight2_date);
                $('#addModal').find('#tareOutgoing2').val(obj.message.tare_weight2);
                $('#addModal').find('#tareOutgoingDate2').val(obj.message.tare_weight2_date);
                $('#addModal').find('#nettWeight2').val(obj.message.nett_weight2);
                $('#addModal').find('#reduceWeight').val(obj.message.reduce_weight);
                // $('#addModal').find('#vehicleNo').val(obj.message.final_weight);
                $('#addModal').find('#weightDifference').val(obj.message.weight_different);
                // $('#addModal').find('#id').val(obj.message.is_complete);
                // $('#addModal').find('#vehicleNo').val(obj.message.is_cancel);
                $('#addModal').find('#manualWeight').val(obj.message.manual_weight);
                $('#addModal').find('#indicatorId').val(obj.message.indicator_id);
                $('#addModal').find('#weighbridge').val(obj.message.weighbridge_id);
                $('#addModal').find('#indicatorId2').val(obj.message.indicator_id_2);
                $('#addModal').find('#productDescription').val(obj.message.product_description);
                $('#addModal').modal('show');
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
    </script>

    </body>

    </html>