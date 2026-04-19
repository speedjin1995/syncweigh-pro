<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";

if (!hasModulePermission('Accounting', 'Delivery Order (DO)', ['view', 'create', 'edit'])){
    header('Location: no-permission.php');
    exit;
}

$plantId = $_SESSION['plant'];

$vehicles = $db->query("SELECT DISTINCT veh_number FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC");
$vehicles2 = $db->query("SELECT * FROM Vehicle WHERE status = '0' ORDER BY veh_number ASC");
$customer = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$customer2 = $db->query("SELECT * FROM Customer WHERE status = '0' ORDER BY name ASC");
$product = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$product2 = $db->query("SELECT * FROM Product WHERE status = '0' ORDER BY name ASC");
$transporter = $db->query("SELECT * FROM Transporter WHERE status = '0' ORDER BY name ASC");
$destination = $db->query("SELECT * FROM Destination WHERE status = '0' ORDER BY name ASC");
$supplier = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$supplier2 = $db->query("SELECT * FROM Supplier WHERE status = '0' ORDER BY name ASC");
$unit = $db->query("SELECT * FROM Unit WHERE status = '0' ORDER BY unit ASC");
$unit2 = $db->query("SELECT * FROM Unit WHERE status = '0' ORDER BY unit ASC");
$purchaseOrder = $db->query("SELECT DISTINCT po_no FROM Purchase_Order WHERE status = 'Open' AND deleted = '0' ORDER BY po_no ASC");
$purchaseOrder2 = $db->query("SELECT DISTINCT po_no FROM Purchase_Order WHERE status = 'Open' AND deleted = '0' ORDER BY po_no ASC");
$salesOrder = $db->query("SELECT DISTINCT order_no FROM Sales_Order WHERE status = 'Open' AND deleted = '0' ORDER BY order_no ASC");
$salesOrder2 = $db->query("SELECT DISTINCT order_no FROM Sales_Order WHERE status = 'Open' AND deleted = '0' ORDER BY order_no ASC");
$agent = $db->query("SELECT * FROM Agents WHERE status = '0' ORDER BY name ASC");
$rawMaterial = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$rawMaterial2 = $db->query("SELECT * FROM Raw_Mat WHERE status = '0' ORDER BY name ASC");
$site = $db->query("SELECT * FROM Site WHERE status = '0' ORDER BY name ASC");
$reasons = $db->query("SELECT * FROM Reasons WHERE status = '0' ORDER BY reason ASC");

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

if(hasModulePermission('Accounting', 'Delivery Order (DO)', ['view_all_plants'])){
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0'");
}
else{
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
?>

<head>

    <title>Delivery Order | PWS - Weighing System</title>
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

<div class="loading" id="spinnerLoading" style="display:none">
  <div class='mdi mdi-loading' style='transform:scale(0.79);'>
    <div></div>
  </div>
</div>

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
                                                    <div class="col-3" id="customerSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="customerNoSearch" class="form-label">Customer No</label>
                                                            <select id="customerNoSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowPF = mysqli_fetch_assoc($customer2)){ ?>
                                                                    <option value="<?=$rowPF['customer_code'] ?>"><?=$rowPF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="productSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="productSearch" class="form-label">Product</label>
                                                            <select id="productSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowProductF=mysqli_fetch_assoc($product2)){ ?>
                                                                    <option value="<?=$rowProductF['product_code'] ?>"><?=$rowProductF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="plantSearch" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant)){ ?>
                                                                    <option value="<?=$rowPlantF['plant_code'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="soSearch" class="form-label">Customer P/O No</label>
                                                            <select id="soSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <?php while($rowSo = mysqli_fetch_assoc($salesOrder)){ ?>
                                                                    <option value="<?=$rowSo['order_no'] ?>"><?=$rowSo['order_no'] ?></option>
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
                                                                <h5 class="card-title mb-0">Delivery Order Records</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <!-- <button type="button" id="exportPdf" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Export Pdf
                                                                </button>-->

                                                                <?php if(hasModulePermission('Accounting', 'Delivery Order (DO)', ['export_excel'])): ?>
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                                <?php endif; ?>

                                                                <?php if(hasModulePermission('Accounting', 'Delivery Order (DO)', ['post_to_sql'])): ?>
                                                                <button type="button" id="postSQL" class="btn btn-danger waves-effect waves-light">
                                                                    <i class="ri-file-add-line align-middle me-1"></i>
                                                                    Post to SQL
                                                                </button>
                                                                <?php endif; ?>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th>Customer</th>
                                                                    <th>Plant</th>
                                                                    <th>Product</th>
                                                                    <th>Customer PO</th>
                                                                    <th>Delivery Date</th>
                                                                    <th>Total Delivery <br> Amount (KG)</th>
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
                                                                        <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="indicatorWeight">0</span> KG</h2>
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
                                                                        <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="currentWeight">0</span> KG</h2>
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
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row mb-3">
                                                                    <label for="vehiclePlateNo1" class="col-sm-4 col-form-label">Vehicle Plate No.</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="input-group">
                                                                            <div class="input-group-text">
                                                                                <input class="form-check-input mt-0" id="manualVehicle" name="manualVehicle" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                            </div>
                                                                            <input type="text" class="form-control" id="vehicleNoTxt" name="vehicleNoTxt" placeholder="Vehicle Plate No" style="display:none" required>
                                                                            <div class="col-10 index-vehicle">
                                                                                <select class="form-select select2" id="vehiclePlateNo1" name="vehiclePlateNo1" required>
                                                                                    <option selected="-">-</option>
                                                                                    <?php while($row2=mysqli_fetch_assoc($vehicles)){ ?>
                                                                                        <option value="<?=$row2['veh_number'] ?>"><?=$row2['veh_number'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                                <input type="text" class="form-control" id="vehiclePlateNo1Edit" name="vehiclePlateNo1Edit" hidden>
                                                                            </div>
                                                                            <div class="invalid-feedback">
                                                                                Please fill in the field.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divOrderWeight">
                                                                <div class="row">
                                                                    <label for="orderWeight" class="col-sm-4 col-form-label">Order Weight</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control" id="orderWeight" name="orderWeight"  placeholder="Order Weight">
                                                                            <div class="input-group-text" id="orderWeightUnit">KG</div>
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
                                                                            <div class="input-group-text" id="supplierWeightUnit">KG</div>
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
                                                                        <select id="weightType" name="weightType" class="form-select select2">
                                                                            <option selected>Normal</option>
                                                                            <option>Container</option>
                                                                        </select>   
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none">
                                                                <div class="row">
                                                                    <label for="containerNo" class="col-sm-4 col-form-label">Container No</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" class="form-control" id="containerNo" name="containerNo" placeholder="Container No">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divPurchaseOrder">
                                                                <div class="row">
                                                                    <label for="purchaseOrder" class="col-sm-4 col-form-label">Purchase Order</label>
                                                                    <div class="col-sm-8" id="poSelect">
                                                                        <select class="form-select js-choice select2" id="purchaseOrder" name="purchaseOrder" required>
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowPO=mysqli_fetch_assoc($purchaseOrder)){ ?>
                                                                                <option value="<?=$rowPO['po_no'] ?>"><?=$rowPO['po_no'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <!--input type="text" class="form-control" id="purchaseOrderEdit" name="purchaseOrderEdit" disabled style="display:none;"-->
                                                                    </div>
                                                                    <div class="col-sm-8" id="soSelect">
                                                                        <select class="form-select js-choice select2" id="salesOrder" name="salesOrder" required>
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowSO=mysqli_fetch_assoc($salesOrder)){ ?>
                                                                                <option value="<?=$rowSO['order_no'] ?>"><?=$rowSO['order_no'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <!--input type="text" class="form-control" id="salesOrderEdit" name="salesOrderEdit" disabled style="display:none;"-->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divPoSupplyWeight">
                                                                <div class="row">
                                                                    <label for="poSupplyWeight" class="col-sm-4 col-form-label">P/O Supply Weight</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control input-readonly" id="poSupplyWeight" name="poSupplyWeight" placeholder="P/O Supply Weight" readonly>
                                                                            <div class="input-group-text" id="poSupplyWeightUnit">KG</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row">
                                                                    <label for="customerType" class="col-sm-4 col-form-label">Customer Type</label>
                                                                    <div class="col-sm-8">
                                                                        <select id="customerType" name="customerType" class="form-select select2">
                                                                            <option>Cash</option>
                                                                            <option selected>Normal</option>
                                                                        </select>   
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row" id="productNameDisplay">
                                                                    <label for="productName" class="col-sm-4 col-form-label">Product Name</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select select2" id="productName" name="productName" required>
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                                                                <option 
                                                                                    value="<?=$rowProduct['name'] ?>" 
                                                                                    data-id="<?=$rowProduct['id'] ?>" 
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
                                                                        <select class="form-select select2" id="rawMaterialName" name="rawMaterialName" required>
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowRawMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                                                                                <option value="<?=$rowRawMat['name'] ?>" data-id="<?=$rowRawMat['id'] ?>" data-code="<?=$rowRawMat['raw_mat_code'] ?>"><?=$rowRawMat['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>           
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divWeightDifference">
                                                                <div class="row">
                                                                    <label for="weightDifference" class="col-sm-4 col-form-label">Weight Difference</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control input-readonly" id="weightDifference" name="weightDifference" placeholder="Weight Difference" readonly>
                                                                            <div class="input-group-text">KG</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                                <div class="row">
                                                                    <label for="reduceWeight" class="col-sm-4 col-form-label">Reduce Weight</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control" id="reduceWeight" name="reduceWeight" placeholder="0">
                                                                            <div class="input-group-text">KG</div>
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
                                                                        <select id="transactionStatus" name="transactionStatus" class="form-select select2">
                                                                            <option value="Sales" selected>Sales</option>
                                                                            <option value="Purchase">Purchase</option>
                                                                            <?php 
                                                                                if($role == 'SADMIN' || $role == 'ADMIN' || $role == 'MANAGER'){ 
                                                                                    echo '<option value="Local">Public</option>';
                                                                                }
                                                                            ?>                                                                                            
                                                                            <option value="WIP">WIP</option>
                                                                        </select>  
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divCustomerName">
                                                                <div class="row">
                                                                    <label for="customerName" class="col-sm-4 col-form-label">Customer Name</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select js-choice select2" id="customerName" name="customerName" required>
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
                                                                        <select class="form-select select2" id="supplierName" name="supplierName" required>
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
                                                                    <label for="balance" class="col-sm-4 col-form-label">Balance</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control input-readonly text-danger" id="balance" name="balance" placeholder="0" readonly>   
                                                                            <div class="input-group-text">KG</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-2" id="insufficientBalDisplay" style="display:none;">
                                                                    <span class="col-sm-4"></span>
                                                                    <label class="col-sm-8 text-danger">Insufficient Balance</label>
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
                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                                <div class="row">
                                                                    <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="Invoice No">
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
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="unitPriceDisplay">
                                                                <div class="row">
                                                                    <label for="unitPrice" class="col-sm-4 col-form-label">Unit Price</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control input-readonly" id="unitPrice" name="unitPrice" placeholder="0" required>
                                                                            <div class="input-group-text">RM</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row">
                                                                    <label for="agent" class="col-sm-4 col-form-label">Sales Representative</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select select2" id="agent" name="agent" >
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
                                                                    <label for="transporter" class="col-sm-4 col-form-label">Transporter</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select select2" id="transporter" name="transporter" required>
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowTransporter=mysqli_fetch_assoc($transporter)){ ?>
                                                                                <option value="<?=$rowTransporter['name'] ?>" data-code="<?=$rowTransporter['transporter_code'] ?>"><?=$rowTransporter['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>                                                                                          
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="sstDisplay">
                                                                <div class="row">
                                                                    <label for="sstPrice" class="col-sm-4 col-form-label">SST (8%)</label>
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
                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                                <div class="row">
                                                                    <label for="indicatorId" class="col-sm-4 col-form-label">Indicator ID</label>
                                                                    <div class="col-sm-8">
                                                                        <select id="indicatorId" name="indicatorId" class="form-select select2" >
                                                                            <option selected>ind12345</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3"  <?php 
                                                                if($_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'MANAGER'){
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
                                                                    <label for="destination" class="col-sm-4 col-form-label">Destination</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select select2" id="destination" name="destination" required>
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowDestination=mysqli_fetch_assoc($destination)){ ?>
                                                                                <option value="<?=$rowDestination['name'] ?>" data-code="<?=$rowDestination['destination_code'] ?>"><?=$rowDestination['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>            
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="subTotalPriceDisplay">
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
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="doDisplay">
                                                                <div class="row">
                                                                    <label for="deliveryNo" class="col-sm-4 col-form-label">Delivery No</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" class="form-control" id="deliveryNo" name="deliveryNo" placeholder="Delivery No">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none">
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
                                                                    <label for="batchDrum" class="col-sm-4 col-form-label">By-Batch/By-Drum</label>
                                                                    <div class="col-sm-8">
                                                                        <select id="batchDrum" name="batchDrum" class="form-select select2" required>
                                                                            <option value="Batch">Batch</option>
                                                                            <option value="Drum">Drum</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="totalPriceDisplay">
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
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row">
                                                                    <label for="plant" class="col-sm-4 col-form-label">Plant</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select select2" id="plant" name="plant" required>
                                                                            <?php while($rowPlantw=mysqli_fetch_assoc($plant2)){ ?>
                                                                                <option value="<?=$rowPlantw['name'] ?>" data-code="<?=$rowPlantw['plant_code'] ?>" data-id="<?=$rowPlantw['id'] ?>"><?=$rowPlantw['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row">
                                                                    <label for="siteName" class="col-sm-4 col-form-label">Project</label>
                                                                    <div class="col-sm-8">
                                                                        <select class="form-select select2" id="siteName" name="siteName">
                                                                            <option selected="-">-</option>
                                                                            <?php while($rowSite=mysqli_fetch_assoc($site)){ ?>
                                                                                <option value="<?=$rowSite['name'] ?>" data-code="<?=$rowSite['site_code'] ?>"><?=$rowSite['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="tinNoDisplay">
                                                                <div class="row">
                                                                    <label for="tinNo" class="col-sm-4 col-form-label">Tin No</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" class="form-control" id="tinNo" name="tinNo" placeholder="Tin No">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="idNoDisplay">
                                                                <div class="row">
                                                                    <label for="idNo" class="col-sm-4 col-form-label">Id No</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" class="form-control" id="idNo" name="idNo" placeholder="Id No">  
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="idTypeDisplay">
                                                                <div class="row">
                                                                    <label for="idType" class="col-sm-4 col-form-label">Id Type</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" class="form-control" id="idType" name="idType" placeholder="Id Type">  
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
                                                                    <div class="input-group-text">KG</div>
                                                                    <button class="input-group-text btn btn-danger fs-5" id="grossCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <label for="grossIncomingDate" class="col-sm-4 col-form-label">Incoming Date</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate" name="grossIncomingDate">
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
                                                                    <div class="input-group-text">KG</div>
                                                                    <button class="input-group-text btn btn-danger fs-5" id="tareCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                </div>                                                                                       
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="tareOutgoingDate" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control input-readonly" id="tareOutgoingDate" name="tareOutgoingDate">
                                                            </div>
                                                        </div>                                                                        
                                                        <div class="row mb-3">
                                                            <label for="nettWeight" class="col-sm-4 col-form-label">Nett Weight</label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control input-readonly" id="nettWeight" name="nettWeight" placeholder="0" readonly>
                                                                    <div class="input-group-text">KG</div>
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
                                                                        <select class="form-select select2" id="vehiclePlateNo2" name="vehiclePlateNo2">
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
                                                            <label for="grossIncoming2" class="col-sm-4 col-form-label">Incoming</label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming2" name="grossIncoming2" placeholder="0" readonly>
                                                                    <div class="input-group-text">KG</div>
                                                                    <button class="input-group-text btn btn-danger fs-5" id="grossCapture2"><i class="mdi mdi-sync" type="button"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="grossIncomingDate2" class="col-sm-4 col-form-label">Incoming Date</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate2" name="grossIncomingDate2">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="tareOutgoing2" class="col-sm-4 col-form-label">Outgoing</label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control input-readonly" id="tareOutgoing2" name="tareOutgoing2" placeholder="0" readonly>
                                                                    <div class="input-group-text">KG</div>
                                                                    <button class="input-group-text btn btn-danger fs-5" id="tareCapture2" type="button"><i class="mdi mdi-sync"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="tareOutgoingDate2" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control input-readonly" placeholder="" id="tareOutgoingDate2" name="tareOutgoingDate2">
                                                            </div>
                                                        </div>                                                                        
                                                        <div class="row mb-3">
                                                            <label for="nettWeight2" class="col-sm-4 col-form-label">Nett Weight</label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control input-readonly" id="nettWeight2" name="nettWeight2" placeholder="0" readonly>
                                                                    <div class="input-group-text">KG</div>
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
                                                <!--button type="button" class="btn btn-danger" id="submitWeightPrint">Submit & Print</button-->
                                                <button type="button" class="btn btn-primary" id="submitWeight">Submit</button>
                                            </div>
                                        </div><!--end col-->   
                                        
                                        <input type="hidden" id="bypassReason" name="bypassReason">
                                        <input type="hidden" id="finalWeight" name="finalWeight">
                                        <input type="hidden" id="customerCode" name="customerCode">
                                        <input type="hidden" id="custName" name="custName">
                                        <input type="hidden" id="destinationCode" name="destinationCode">
                                        <input type="hidden" id="plantId" name="plantId">
                                        <input type="hidden" id="plantCode" name="plantCode">
                                        <input type="hidden" id="agentCode" name="agentCode">
                                        <input type="hidden" id="status" name="status">
                                        <input type="hidden" id="productId" name="productId">
                                        <input type="hidden" id="productCode" name="productCode">
                                        <input type="hidden" id="productDescription" name="productDescription">
                                        <input type="hidden" id="productPrice" name="productPrice">
                                        <input type="hidden" id="productHigh" name="productHigh">
                                        <input type="hidden" id="productLow" name="productLow">
                                        <input type="hidden" id="productVariance" name="productVariance">
                                        <input type="hidden" id="transporterCode" name="transporterCode">
                                        <input type="hidden" id="transporterName" name="transporterName">
                                        <input type="hidden" id="supplierCode" name="supplierCode">
                                        <input type="hidden" id="rawMaterialCode" name="rawMaterialCode">
                                        <input type="hidden" id="rawMaterialId" name="rawMaterialId">
                                        <input type="hidden" id="siteCode" name="siteCode">
                                        <input type="hidden" id="id" name="id">  
                                        <input type="hidden" id="weighbridge" name="weighbridge" value="Weigh1">
                                        <input type="hidden" id="previousRecordsTag" name="previousRecordsTag">
                                        <input type="hidden" id="basicNettWeight" name="basicNettWeight">
                                    </form>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableDO" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableDO"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form role="form" id="doForm" class="needs-validation" novalidate autocomplete="off">
                                        
                                    </form>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
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
    var userRole = '<?=$_SESSION["roles"] ?>';
    var table = null;
    let soPoTag = false;
    let addNewTag = false;
    let isSyncing = false;
    let isEdit = false;
    let salesOption = $('#salesOrder option').clone();
    let purchaseOption = $('#purchaseOrder option').clone();
    let transporterOption = $('#transporter option').clone();
    var grossIncomingDatePicker;
    var tareOutgoingDatePicker; 
    var grossIncomingDatePicker2;
    var tareOutgoingDatePicker2; 
    var permissions = <?= json_encode($_SESSION['permissions']) ?>;
    var isSADMIN = <?= json_encode($_SESSION['roles'] == 'SADMIN') ?>;

    $(function () {
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

        grossIncomingDatePicker = $('#grossIncomingDate').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d/m/Y H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
            clickOpens: <?= hasPermission('Weighing', ['manual_date_change']) ? 'true' : 'false' ?>,
            onReady: function(selectedDates, dateStr, instance) {
                <?php if (!hasPermission('Weighing', ['manual_date_change'])): ?>
                    instance._input.setAttribute('readonly', true);
                    instance.close();
                <?php endif; ?>
            }
        });

        tareOutgoingDatePicker = $('#tareOutgoingDate').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d/m/Y H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
            clickOpens: <?= hasPermission('Weighing', ['manual_date_change']) ? 'true' : 'false' ?>,
            onReady: function(selectedDates, dateStr, instance) {
                <?php if (!hasPermission('Weighing', ['manual_date_change'])): ?>
                    instance._input.setAttribute('readonly', true);
                    instance.close();
                <?php endif; ?>
            }
        });

        grossIncomingDatePicker2 = $('#grossIncomingDate2').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d/m/Y H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
            clickOpens: <?= hasPermission('Weighing', ['manual_date_change']) ? 'true' : 'false' ?>,
            onReady: function(selectedDates, dateStr, instance) {
                <?php if (!hasPermission('Weighing', ['manual_date_change'])): ?>
                    instance._input.setAttribute('readonly', true);
                    instance.close();
                <?php endif; ?>
            }
        });

        tareOutgoingDatePicker2 = $('#tareOutgoingDate2').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d/m/Y H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
            clickOpens: <?= hasPermission('Weighing', ['manual_date_change']) ? 'true' : 'false' ?>,
            onReady: function(selectedDates, dateStr, instance) {
                <?php if (!hasPermission('Weighing', ['manual_date_change'])): ?>
                    instance._input.setAttribute('readonly', true);
                    instance.close();
                <?php endif; ?>
            }
        });

        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y H:i:S",
            enableTime: true,
            time_24hr: true,
            defaultDate: yesterday
        });

        $('#toDateSearch').flatpickr({
            //dateFormat: "d-m-Y",
            dateFormat: "d-m-Y H:i:S",
            enableTime: true,
            time_24hr: true,
            defaultDate: today
        });

        $('#transactionDate').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        $('.select2').each(function() {
            $(this).select2({
                allowClear: true,
                placeholder: "Please Select",
                // Conditionally set dropdownParent based on the element’s location
                dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal-body') : undefined
            });
        });

        // Apply custom styling to Select2 elements in addModal
        $('.select2-container .select2-selection--single').css({
            'padding-top': '4px',
            'padding-bottom': '4px',
            'height': 'auto'
        });

        $('.select2-container .select2-selection__arrow').css({
            'padding-top': '33px',
            'height': 'auto'
        });

        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#weightTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var statusI = 'Sales';
        var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
        var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
        var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
        var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
        var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var soI = $('#soSearch').val() ? $('#soSearch').val() : '';

        table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterDoGr.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    status: statusI,
                    customer: customerNoI,
                    supplier: supplierNoI,
                    product: productI,
                    rawMaterial: rawMatI,
                    plant: plantI,
                    purchaseOrder: soI,
                    type: 'DO'
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
                { data: 'customer_name' },
                { data: 'plant_name' },
                { data: 'product_name' },
                { data: 'purchase_order' },
                { data: 'tare_weight1_date' },
                { data: 'order_weight' },
                { 
                    data: 'id',
                    class: 'action-button',
                    render: function ( data, type, row ) {
                        if (isSADMIN || (permissions['Accounting'] && permissions['Accounting']['Delivery Order (DO)'] && permissions['Accounting']['Delivery Order (DO)'].includes('post_to_sql'))) {
                            return `
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item post-item-btn" id="post${data}" onclick="post(${data})">
                                                <i class="mdi-post align-bottom me-2 text-muted"></i> Post
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            `;
                        }

                        return '';
                    }
                }
            ],
            "drawCallback": function(settings) {
                $('#salesInfo').text(settings.json.salesTotal);
                $('#purchaseInfo').text(settings.json.purchaseTotal);
                $('#localInfo').text(settings.json.localTotal);
            }   
        });

        $('#filterSearch').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = 'Sales';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var soI = $('#soSearch').val() ? $('#soSearch').val() : '';

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
                    'url':'php/filterDoGr.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        customer: customerNoI,
                        supplier: supplierNoI,
                        product: productI,
                        rawMaterial: rawMatI,
                        plant: plantI,
                        purchaseOrder: soI,
                        type: 'DO'
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
                    { data: 'customer_name' },
                    { data: 'plant_name' },
                    { data: 'product_name' },
                    { data: 'purchase_order' },
                    { data: 'tare_weight1_date' },
                    { data: 'order_weight' },
                    { 
                        data: 'id',
                        class: 'action-button',
                        render: function (data, type, row) {
                            if (isSADMIN || (permissions['Accounting'] && permissions['Accounting']['Delivery Order (DO)'] && permissions['Accounting']['Delivery Order (DO)'].includes('post_to_sql'))) {
                                return `
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item post-item-btn" id="post${data}" onclick="post(${data})">
                                                    <i class="mdi-post align-bottom me-2 text-muted"></i> Post
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                `;
                            }
                            return '';
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

        // Add event listener for opening and closing details on row click
        $('#weightTable tbody').on('click', 'tr', function (e) {
            var tr = $(this); // The row that was clicked
            var row = table.row(tr);
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();

            // Exclude specific td elements by checking the event target
            if ($(e.target).closest('td').hasClass('select-checkbox') || $(e.target).closest('td').hasClass('action-button')) {
                return;
            }

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.post('php/getWeight.php', { userID: row.data().id, fromDate: fromDateI, toDate: toDateI, format: 'EXPANDABLE', acctType: 'DO' }, function (data) {
                    var obj = JSON.parse(data);
                    if (obj.status === 'success') {
                        row.child(format(obj.message)).show();
                        tr.addClass("shown");
                    }
                });
            }
        });

        $.validator.setDefaults({
            submitHandler: function () {
                if($('#exportPdfModal').hasClass('show')){   
                    var fromDateI = $('#fromDateSearch').val();
                    var toDateI = $('#toDateSearch').val();
                    var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
                    var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
                    var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
                    var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
                    var customerTypeI = $('#customerTypeSearch').val() ? $('#customerTypeSearch').val() : '';
                    var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
                    var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
                    var destinationI = $('#destinationSearch').val() ? $('#destinationSearch').val() : '';
                    var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

                    $('#exportPdfForm').find('#fromDate').val(fromDateI);
                    $('#exportPdfForm').find('#toDate').val(toDateI);
                    $('#exportPdfForm').find('#status').val(statusI);
                    $('#exportPdfForm').find('#customer').val(customerNoI);
                    $('#exportPdfForm').find('#supplier').val(supplierNoI);
                    $('#exportPdfForm').find('#vehicle').val(vehicleNoI);
                    $('#exportPdfForm').find('#customerType').val(customerTypeI);
                    $('#exportPdfForm').find('#product').val(productI);
                    $('#exportPdfForm').find('#rawMat').val(rawMatI);
                    $('#exportPdfForm').find('#destination').val(destinationI);
                    $('#exportPdfForm').find('#plant').val(plantI);
                    $('#exportPdfForm').find('#file').val('weight');
                    $('#exportPdfModal').modal('hide');

                    $.post('php/exportPdf.php', $('#exportPdfForm').serialize(), function(response){
                        var obj = JSON.parse(response);

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
                    }).fail(function(error){
                        console.error("Error exporting PDF:", error);
                        alert("An error occurred while generating the PDF.");
                    });
                }
                else if($('#exportSoRepModal').hasClass('show')){   
                    var group1 = $('#exportSoRepModal').find('#group1').val();
                    var group2 = $('#exportSoRepModal').find('#group2').val();
                    var group3 = $('#exportSoRepModal').find('#group3').val();

                    // Added checking to ensure previous group is selected
                    if (group2 && !group1) {
                        alert("Please select Group 1 before selecting Group 2.");
                        return;
                    }
                    if (group3 && (!group1 || !group2)) {
                        alert("Please select Group 1 and Group 2 before selecting Group 3.");
                        return;
                    }

                    var fromDateI = $('#fromDateSearch').val();
                    var toDateI = $('#toDateSearch').val();
                    var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
                    var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
                    var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
                    var vehicleNoI = $('#vehicleNo').val() ? $('#vehicleNo').val() : '';
                    var customerTypeI = $('#customerTypeSearch').val() ? $('#customerTypeSearch').val() : '';
                    var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
                    var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
                    var destinationI = $('#destinationSearch').val() ? $('#destinationSearch').val() : '';
                    var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

                    $('#exportSoRepForm').find('#fromDate').val(fromDateI);
                    $('#exportSoRepForm').find('#toDate').val(toDateI);
                    $('#exportSoRepForm').find('#status').val(statusI);
                    $('#exportSoRepForm').find('#customer').val(customerNoI);
                    $('#exportSoRepForm').find('#supplier').val(supplierNoI);
                    $('#exportSoRepForm').find('#vehicle').val(vehicleNoI);
                    $('#exportSoRepForm').find('#customerType').val(customerTypeI);
                    $('#exportSoRepForm').find('#product').val(productI);
                    $('#exportSoRepForm').find('#rawMat').val(rawMatI);
                    $('#exportSoRepForm').find('#destination').val(destinationI);
                    $('#exportSoRepForm').find('#plant').val(plantI);
                    $('#exportSoRepForm').find('#type').val('Sales');
                    $('#exportSoRepModal').modal('hide');

                    $.post('php/exportSoPoReport.php', $('#exportSoRepForm').serialize(), function(response){
                        var obj = JSON.parse(response);

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
                    }).fail(function(error){
                        console.error("Error exporting PDF:", error);
                        alert("An error occurred while generating the PDF.");
                    });
                }
            }
        });

        $('#statusSearch').on('change', function(){
            var status = $(this).val();

            if (status == 'Purchase' || status == 'Local'){
                // Hide & reset customer then show supplier
                $('#customerSearchDisplay').hide();
                $('#customerSearchDisplay').find('#customerNoSearch').val('-').trigger('change');
                $('#supplierSearchDisplay').show();
                // Hide & reset product then show raw material
                $('#productSearchDisplay').find('#productSearch').val('-').trigger('change');
                $('#productSearchDisplay').hide();
                $('#rawMatSearchDisplay').show();
            }else{
                // Hide & reset supplier then show customer
                $('#supplierSearchDisplay').find('#supplierSearch').val('-').trigger('change');
                $('#supplierSearchDisplay').hide();
                $('#customerSearchDisplay').show();
                // Hide & reset raw material then show product
                $('#rawMatSearchDisplay').find('#rawMatSearch').val('-').trigger('change');
                $('#rawMatSearchDisplay').hide();
                $('#productSearchDisplay').show();
            }
        });

        // Trigger the function on change
        $('select[id^="group"]').on('change', function () {
            updateSelects();
        });

        // Post to SQL Handling
        $('#postSQL').on('click', function () {
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = 'Sales';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var soI = $('#soSearch').val() ? $('#soSearch').val() : '';
            var selectedIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                if (confirm('Are you sure you want to post to SQL these items?')) {
                    $('#spinnerLoading').show();
                    $.post('php/postDo.php', {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        customer: customerNoI,
                        product: productI,
                        plant: plantI,
                        purchaseOrder: soI,
                        userID: selectedIds, 
                        type: 'MULTI'
                    }, function(data){
                        var obj = JSON.parse(data);
                        
                        if(obj.status === 'success'){
                            $('#weightTable').DataTable().ajax.reload(null, false);
                            $('#spinnerLoading').hide();
                            toastr["success"](obj.message, "Success:");
                        }
                        else if(obj.status === 'failed'){
                            $('#spinnerLoading').hide();
                            toastr["error"](obj.message, "Failed:");
                        }
                        else{
                            $('#spinnerLoading').hide();
                            toastr["error"]("Something wrong when activate", "Failed:");
                        }
                    });
                }
            } 
            else {
                if (confirm('Are you sure you want to post to SQL?')) {
                    $('#spinnerLoading').show();
                    $.post('php/postDo.php', {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        customer: customerNoI,
                        product: productI,
                        plant: plantI,
                        purchaseOrder: soI,
                        type: 'ALL'
                    }, function(data){
                        var obj = JSON.parse(data);
                        
                        if(obj.status === 'success'){
                            $('#weightTable').DataTable().ajax.reload(null, false);
                            $('#spinnerLoading').hide();
                            toastr["success"](obj.message, "Success:");
                        }
                        else if(obj.status === 'failed'){
                            $('#spinnerLoading').hide();
                            toastr["error"](obj.message, "Failed:");
                        }
                        else{
                            $('#spinnerLoading').hide();
                            toastr["error"]("Something wrong when activate", "Failed:");
                        }
                    });
                }
            }     
        });

        // Export Excel
        $('#exportExcel').on('click', function () {
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = 'Sales';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var soI = $('#soSearch').val() ? $('#soSearch').val() : '';
            var selectedIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                window.open("php/exportDoGr.php?type=do&isMulti=Y&fromDate="+fromDateI+"&toDate="+toDateI+
                "&status="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&product="+productI+
                "&rawMaterial="+rawMatI+"&plant="+plantI+"&purchaseOrder="+soI+"&id="+selectedIds);
            } 
            else {
                window.open("php/exportDoGr.php?type=do&isMulti=N&fromDate="+fromDateI+"&toDate="+toDateI+
                "&status="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&product="+productI+
                "&rawMaterial="+rawMatI+"&plant="+plantI+"&purchaseOrder="+soI);
            }     
        });

        // Edit form sections
        $('#submitWeight').on('click', function(){
            // Check weight
            var trueWeight = 0;
            var variance = $('#productVariance').val() || '';
            var high = $('#productHigh').val() || '';
            var low = $('#productLow').val() || '';
            var final = $('#finalWeight').val() || '0';
            var completed = 'N';
            var pass = true;

            if($('#transactionStatus').val() == "Purchase"){
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

                    pass = false;
                } else {
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message

                    pass = true;
                }
            });

            if ($('#customerType').val() == 'Cash' && pass == true) {
                var unitPrice = parseFloat($('#addModal').find('#unitPrice').val());

                if (!unitPrice || unitPrice <= 0) {
                    alert('Unit price must be more than 0.');
                    return;
                } else {
                    debugger;
                    var productId = $('#addModal').find('#productName').attr('data-id');
                    $.post('php/getProduct.php', { userID: productId }, function (data) {
                        try {
                            var obj = JSON.parse(data);
                            if (obj.status === 'success') {
                                var price = obj.message.price;
                                if (unitPrice < price) {
                                    alert('Unit price doesn\'t meet the minimum value of RM ' + price);
                                    return;
                                } else {
                                    // Price validation passed, submit the form
                                    submitWeightForm();
                                }
                            } else {
                                alert('Error validating product price');
                            }
                        } catch (e) {
                            alert('Error processing product validation response');
                        }
                    }).fail(function() {
                        alert('Error connecting to server for price validation');
                    });
                    return; // Exit here to prevent immediate form submission
                }
            }

            // If not cash or validation passed, submit form
            if(pass && $('#weightForm').valid()){
                submitWeightForm();
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
                                    echo "window.location = 'deliveryOrder.php';";
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

            if($('#transactionStatus').val() == "Purchase"){
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

                    pass = false;
                } else {
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message

                    pass = true;
                }
            });

            if ($('#customerType').val() == 'Cash' && pass == true) {
                var unitPrice = parseFloat($('#addModal').find('#unitPrice').val());

                if (!unitPrice || unitPrice <= 0) {
                    alert('Unit price must be more than 0.');
                    return;
                }else{
                    var productId = $('#addModal').find('#productId').val();
                    $.post('php/getProduct.php', { userID: productId }, function (data) {
                        try {
                            var obj = JSON.parse(data);
                            if (obj.status === 'success') {
                                var price = obj.message.price;
                                if (unitPrice < price) {
                                    alert('Unit price doesn\'t meet the minimum value of RM ' + price);
                                    return;
                                }else{
                                    // Continue with form submission after price validation
                                    submitWeightPrintForm();
                                }
                            } else {
                                alert('Error validating product price.');
                            }
                        } catch (e) {
                            alert('Error processing product validation response.');
                        }
                    });
                    return; // Exit here, will continue in callback
                }
            }

            // Direct submission if not cash or validation passed
            submitWeightPrintForm();
        });

        $('#transactionStatus').on('change', function(){
            var customerType = $('#addModal').find('#customerType').val();

            if($(this).val() == "Purchase"){
                $('#divWeightDifference').show();
                $('#divSupplierWeight').show();
                $('#addModal').find('#orderWeight').val("");
                $('#addModal').find('#supplierWeight').val("0");
                $('#divSupplierName').show();
                $('#divOrderWeight').hide();
                $('#divCustomerName').hide();
                $('#rawMaterialDisplay').show();
                $('#productNameDisplay').hide();
                $('#addModal').find('#divPoSupplyWeight').show();
                
                <?php if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'MANAGER'){
                    echo "$('#doDisplay').show();";
                }
                else{
                    echo "//$('#doDisplay').show();";
                }
                ?>
                
                if ($(this).val() == "Purchase"){
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Purchase Order');
                    // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Purchase Order');
                    
                    //Hide SO Select
                    $('#divPurchaseOrder').find('#soSelect').hide();
                    $('#divPurchaseOrder').find('#poSelect').show();

                    // Hide Pricing Fields
                    $('#unitPriceDisplay').hide();
                    $('#subTotalPriceDisplay').hide();
                    $('#sstDisplay').hide();
                    $('#totalPriceDisplay').hide();
                    $('#tinNoDisplay').hide();
                    $('#idNoDisplay').hide();
                    $('#idTypeDisplay').hide();
                }else{
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                    // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');

                    //Hide PO Select
                    $('#divPurchaseOrder').find('#soSelect').show();
                    $('#divPurchaseOrder').find('#poSelect').hide();

                    if (customerType == 'Cash'){
                        $('#unitPriceDisplay').show();
                        $('#subTotalPriceDisplay').show();
                        $('#sstDisplay').show();
                        $('#totalPriceDisplay').show();
                        $('#tinNoDisplay').show();
                        $('#idNoDisplay').show();
                        $('#idTypeDisplay').show();
                    }else{
                        $('#unitPriceDisplay').hide();
                        $('#subTotalPriceDisplay').hide();
                        $('#sstDisplay').hide();
                        $('#totalPriceDisplay').hide();
                        $('#tinNoDisplay').hide();
                        $('#idNoDisplay').hide();
                        $('#idTypeDisplay').hide();
                    }
                }
            }
            else if($(this).val() == "Local"){
                $('#divOrderWeight').show();
                $('#addModal').find('#orderWeight').val("0");
                $('#addModal').find('#supplierWeight').val("");
                $('#divWeightDifference').show();
                $('#divSupplierWeight').hide();
                $('#divSupplierName').hide();
                $('#divCustomerName').show();
                $('#rawMaterialDisplay').hide();
                $('#productNameDisplay').show();
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');
                $('#addModal').find('#divPoSupplyWeight').hide();

                //Hide PO Select
                $('#divPurchaseOrder').find('#soSelect').show();
                $('#divPurchaseOrder').find('#poSelect').hide();

                <?php if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'MANAGER'){
                    echo "$('#doDisplay').show();";
                }
                else{
                    echo "//$('#doDisplay').show();";
                }
                ?>

                $('#unitPriceDisplay').hide();
                $('#subTotalPriceDisplay').hide();
                $('#sstDisplay').hide();
                $('#totalPriceDisplay').hide();
                $('#tinNoDisplay').hide();
                $('#idNoDisplay').hide();
                $('#idTypeDisplay').hide();
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
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');
                $('#addModal').find('#divPoSupplyWeight').hide();

                //Hide PO Select
                $('#divPurchaseOrder').find('#soSelect').show();
                $('#divPurchaseOrder').find('#poSelect').hide();

                <?php if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN' && $_SESSION["roles"] != 'MANAGER'){
                    echo "$('#doDisplay').hide();";
                }
                else{
                    echo "//$('#doDisplay').hide();";
                }
                ?>

                if (customerType == 'Cash'){
                    $('#unitPriceDisplay').show();
                    $('#subTotalPriceDisplay').show();
                    $('#sstDisplay').show();
                    $('#totalPriceDisplay').show();
                    $('#tinNoDisplay').show();
                    $('#idNoDisplay').show();
                    $('#idTypeDisplay').show();
                }else{
                    $('#unitPriceDisplay').hide();
                    $('#subTotalPriceDisplay').hide();
                    $('#sstDisplay').hide();
                    $('#totalPriceDisplay').hide();
                    $('#tinNoDisplay').hide();
                    $('#idNoDisplay').hide();
                    $('#idTypeDisplay').hide();
                }
            }
        });
    });

    // Function to handle weight form submission without printing
    function submitWeightForm() {
        debugger;
        if ($('#weightForm').valid()) {
            $('#spinnerLoading').show();
            $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                if(obj.status === 'success'){
                    <?php
                        if(isset($_GET['weight'])){
                            echo "window.location = 'deliveryOrder.php';";
                        }
                    ?>
                    table.ajax.reload();
                    window.location = 'deliveryOrder.php';
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
    }

    // Function to handle weight form submission with printing
    function submitWeightPrintForm() {
        if ($('#weightForm').valid()) {
            $('#spinnerLoading').show();
            $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                if(obj.status === 'success'){
                    $('#spinnerLoading').hide();
                    $('#addModal').modal('hide');
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();

                    $.post('php/print.php', {userID: obj.id, file: 'weight', prePrint: 'Y'}, function(data){
                        var obj2 = JSON.parse(data);

                        if(obj2.status === 'success'){
                            var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                            printWindow.document.write(obj2.message);
                            printWindow.document.close();
                            setTimeout(function(){
                                printWindow.print();
                                printWindow.close();
                                table.ajax.reload();
                                
                                setTimeout(function () {
                                    if (confirm("Do you need to reprint?")) {
                                        $.post('php/print.php', { userID: obj.id, file: 'weight', prePrint: 'Y'}, function (data) {
                                            var obj = JSON.parse(data);
                                            if (obj.status === 'success') {
                                                var reprintWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                                                reprintWindow.document.write(obj.message);
                                                reprintWindow.document.close();
                                                setTimeout(function () {
                                                    reprintWindow.print();
                                                    reprintWindow.close();
                                                    <?php
                                                        if(isset($_GET['weight'])){
                                                            echo "window.location = 'deliveryOrder.php';";
                                                        }
                                                    ?>
                                                }, 500);
                                            } 
                                            else {
                                                window.location = 'deliveryOrder.php';
                                            }
                                        });
                                    }
                                    else{
                                        <?php
                                            if(isset($_GET['weight'])){
                                                echo "window.location = 'deliveryOrder.php';";
                                            }
                                        ?>
                                    }
                                }, 500);
                            }, 500);
                        }
                        else if(obj.status === 'failed'){
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                        else{
                            $("#failBtn").attr('data-toast-text', "Something wrong when print");
                            $("#failBtn").click();
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
    }

    function getSoPo(){
        var transactionStatus = $('#addModal').find('#transactionStatus').val();
        var manualVehicle = $('#addModal').find('#manualVehicle').val();
        var transporter = $('#addModal').find('#transporter').val();

        var vehicle = '';
        if (manualVehicle == '1'){
            vehicle = $('#addModal').find('#vehicleNoTxt').val();
        }else{
            vehicle = $('#addModal').find('#vehiclePlateNo1').val();
        }

        soPoTag = true;
        if (transactionStatus == 'Purchase'){
            var customerSupplier = $('#addModal').find('#supplierName').val();
            // var options = $('#purchaseOrder option').clone();

            if (isEdit){
                $('#addModal').find('#purchaseOrder').empty();
                $('#addModal').find('#purchaseOrder').append(purchaseOption);
            }else{
                $.post('php/getOrderSupplier.php', {type: transactionStatus, format: 'getSoPo', vehicle: vehicle, transporter: transporter, customerSupplier: customerSupplier}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            var soPo = obj.message;
                            $('#addModal').find('#purchaseOrder').empty();
                            $('#addModal').find('#purchaseOrder').append(`<option selected="-">-</option>`);
                            for (var i = 0; i < soPo.length; i++) {
                                // Check if option with this value already exists
                                var existingOption = $('#addModal').find('#purchaseOrder option[value="' + soPo[i] + '"]');
                                if (existingOption.length === 0) {
                                    $('#addModal').find('#purchaseOrder').append(
                                        `<option value="${soPo[i]}">${soPo[i]}</option>`
                                    );
                                }                   
                            }

                            if ($('#addModal').find('#purchaseOrder option').length == 2){
                                $('#addModal').find('#purchaseOrder').val(soPo[0]).trigger('change');
                            }else{
                                $('#addModal').find('#purchaseOrder').val("");
                            }
                        }else{
                            $('#addModal').find('#purchaseOrder').empty();
                        }

                        soPoTag = false;
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                        soPoTag = false;
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                        soPoTag = false;
                    }
                });
            }
            
        }else if (transactionStatus == 'Sales'){
            var customerSupplier = $('#addModal').find('#customerName').val();

            if (isEdit){
                $('#addModal').find('#salesOrder').empty();
                $('#addModal').find('#salesOrder').append(salesOption);
            }else{
                $.post('php/getOrderSupplier.php', {type: transactionStatus, format: 'getSoPo', vehicle: vehicle, transporter: transporter, customerSupplier: customerSupplier}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            var soPo = obj.message;
                            $('#addModal').find('#salesOrder').empty();
                            $('#addModal').find('#salesOrder').append(`<option selected="-">-</option>`);
                            for (var i = 0; i < soPo.length; i++) {
                                // Check if option with this value already exists
                                var existingOption = $('#addModal').find('#salesOrder option[value="' + soPo[i] + '"]');
                                if (existingOption.length === 0) {
                                    $('#addModal').find('#salesOrder').append(
                                        `<option value="${soPo[i]}">${soPo[i]}</option>`
                                    );
                                }                   
                            }

                            if ($('#addModal').find('#salesOrder option').length == 2){
                                $('#addModal').find('#salesOrder').val(soPo[0]).trigger('change');
                            }else{
                                $('#addModal').find('#salesOrder').val("");
                            }
                        }else{
                            $('#addModal').find('#salesOrder').empty();
                        }

                        soPoTag = false;
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                        soPoTag = false;
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                        soPoTag = false;
                    }
                });
            }
        }
    }

    function format(row) {
        var returnString = `
        <!-- Weighing Section -->
        <div class="row">
            <p><span><strong style="font-size:120%; text-decoration: underline;">Delivery Order Information</strong></span><br>
            <div class="col-4">
                <p><strong>TOTAL DELIVERY AMOUNT:</strong> ${parseFloat(row.totalDeliverAmt)/1000} MT</p>
            </div>
            <div class="col-4">
                <p><strong>UNIT PRICE:</strong> RM ${row.weights[0].unit_price}</p>
            </div>
            <div class="col-4">
                <p><strong>TOTAL PRICE:</strong> RM ${parseFloat(parseFloat(row.weights[0].unit_price) * (parseFloat(row.totalDeliverAmt)/1000)).toFixed(2)}</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <table class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>DO No.</th>
                        <th>Vehicle</th>
                        <th>Transporter</th>
                        <th>Destination</th>
                        <th>Gross Incoming</th>
                        <th>Incoming Date</th>
                        <th>Tare Outgoing</th>
                        <th>Outgoing Date</th>
                        <th>Nett Weight</th>`;
                        if (isSADMIN || (permissions['Weighing'] && permissions['Weighing']['Sales'] && permissions['Weighing']['Sales'].includes('edit'))) {
                            returnString += `<th>Action</th>`;
                        }

                    returnString += `</tr>
                </thead>
                <tbody>`;

                for (var i = 0; i < row.weights.length; i++) {
                    var weights = row.weights; 
                    
                    returnString += `
                        <tr>
                            <td>${weights[i].transaction_id}</td>
                            <td>${weights[i].delivery_no}</td>
                            <td>${weights[i].lorry_plate_no1}</td>
                            <td>${weights[i].transporter}</td>
                            <td>${weights[i].destination}</td>
                            <td>${parseFloat(weights[i].gross_weight1)/1000} MT</td>
                            <td>${weights[i].gross_weight1_date}</td>
                            <td>${parseFloat(weights[i].tare_weight1)/1000} MT</td>
                            <td>${weights[i].tare_weight1_date}</td>
                            <td>${parseFloat(weights[i].nett_weight1)/1000} MT</td>`
                            if (isSADMIN || (permissions['Weighing'] && permissions['Weighing']['Sales'] && permissions['Weighing']['Sales'].includes('edit'))) {
                                returnString += `
                                <td>
                                    <button title="Edit" type="button" id="edit${weights[i].id}" onclick="edit(${weights[i].id})" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </td>`;
                            }
                        returnString += `</tr>`;
                }

                returnString += `</tbody>
            </table>
        </div>
        `;
        
        return returnString;
    }

    function edit(id){ 
        isEdit = true;
        $('#spinnerLoading').show();
        $.post('php/getWeight.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                if(obj.message.is_complete == 'Y'){
                    // Hide Capture Button When Edit
                    $('#addModal').find('#grossCapture').hide();
                    $('#addModal').find('#tareCapture').hide();
                }
                else{
                    // Show Capture Button When Edit
                    $('#addModal').find('#grossCapture').show();
                    $('#addModal').find('#tareCapture').show();
                }

                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#tinNo').val(obj.message.tin_no);
                $('#addModal').find('#idNo').val(obj.message.id_no);
                $('#addModal').find('#idType').val(obj.message.id_type);
                $('#addModal').find('#transactionId').val(obj.message.transaction_id);
                $('#addModal').find('#transactionStatus').val(obj.message.transaction_status).trigger('change');
                $('#addModal').find('#weightType').val(obj.message.weight_type).trigger('change');
                $('#addModal').find('#customerType').val(obj.message.customer_type).trigger('change');
                $('#addModal').find('#transactionDate').val(formatDate2(new Date(obj.message.transaction_date)));

                if(obj.message.transaction_status == "Purchase"){
                    $('#divWeightDifference').show();
                    $('#divSupplierWeight').show();
                    $('#divSupplierName').show();
                    $('#divOrderWeight').hide();
                    $('#divCustomerName').hide();
                }
                else{
                    $('#divOrderWeight').show();
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
                    $('#addModal').find('#vehiclePlateNo1Edit').val('EDIT');
                    $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
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
                
                $('#addModal').find('#productCode').val(obj.message.product_code);
                if (obj.message.ex_del == 'EX'){
                    $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true);
                }else{
                    $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true);
                }
                
                $('#addModal').find('#containerNo').val(obj.message.container_no);
                $('#addModal').find('#poSupplyWeight').val(obj.message.po_supply_weight);
                $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                $('#addModal').find('#transporter').val(obj.message.transporter).trigger('change');
                $('#addModal').find('#otherRemarks').val(obj.message.remarks);
                $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                grossIncomingDatePicker.setDate(obj.message.gross_weight1_date != null ? new Date(obj.message.gross_weight1_date) : null);
                $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                tareOutgoingDatePicker.setDate(obj.message.tare_weight1_date != null ? new Date(obj.message.tare_weight1_date) : null);
                $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);
                $('#addModal').find('#convertedNettWeight').val(obj.message.converted_nett_weight1);
                $('#addModal').find('#grossIncoming2').val(obj.message.gross_weight2);
                grossIncomingDatePicker2.setDate(obj.message.gross_weight2_date != null ? new Date(obj.message.gross_weight2_date) : null);
                $('#addModal').find('#tareOutgoing2').val(obj.message.tare_weight2);
                tareOutgoingDatePicker2.setDate(obj.message.tare_weight2_date != null ? new Date(obj.message.tare_weight2_date) : null);
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
                $('#addModal').find('#productDescription').val(obj.message.product_description);
                $('#addModal').find('#unitPrice').val(obj.message.unit_price).trigger('change');
                $('#addModal').find('#subTotalPrice').val(obj.message.sub_total);
                $('#addModal').find('#sstPrice').val(obj.message.sst);
                $('#addModal').find('#totalPrice').val(obj.message.total_price);
                $('#addModal').find('#finalWeight').val(obj.message.final_weight);
                $('#addModal').find('#currentWeight').text(obj.message.final_weight);

                if (obj.message.load_drum == 'LOAD'){
                    $('#addModal').find("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
                }else{
                    $('#addModal').find("input[name='loadDrum'][value='false']").prop("checked", true).trigger('change');
                }
                
                $('#addModal').find('#noOfDrum').val(obj.message.no_of_drum);                
                $('#addModal').find('#batchDrum').val(obj.message.batch_drum).trigger('change');

                if (obj.message.transaction_status == 'Purchase'){
                    //$('#addModal').find('#purchaseOrder').next('.select2-container').hide();
                    //('#addModal').find('#purchaseOrderEdit').val(obj.message.purchase_order).show();
                    // Check if purchaseOrder value exist in the select tag
                    var purchaseOrderExists = $('#addModal').find('#purchaseOrder option').filter(function() {
                        return $(this).val() === obj.message.purchase_order;
                    }).length > 0;

                    if (!purchaseOrderExists){
                        // Append missing purchaseOrder
                        $('#addModal').find('#purchaseOrder').append(
                            '<option value="'+obj.message.purchase_order+'">'+obj.message.purchase_order+'</option>'
                        );
                    }

                    $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order).select2('destroy').select2();
                    $('#addModal').trigger('orderLoaded', [obj.message]);
                }else{
                    //$('#addModal').find('#salesOrder').next('.select2-container').hide();
                    //$('#addModal').find('#salesOrderEdit').val(obj.message.purchase_order).show();

                    // Check if salesOrder value exist in the select tag
                    var salesOrderExists = $('#addModal').find('#salesOrder option').filter(function() {
                        return $(this).val() === obj.message.purchase_order;
                    }).length > 0;

                    if (!salesOrderExists){
                        // Append missing salesOrder
                        $('#addModal').find('#salesOrder').append(
                            '<option value="'+obj.message.purchase_order+'">'+obj.message.purchase_order+'</option>'
                        );
                    }

                    $('#addModal').find('#salesOrder').val(obj.message.purchase_order).select2('destroy').select2();
                    $('#addModal').trigger('orderLoaded', [obj.message]);
                }

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


                // Load these field after PO/SO is loaded
                //$('#addModal').on('orderLoaded', function() {
                $('#addModal').find('#customerCode').val(obj.message.customer_code);
                $('#addModal').find('#customerName').val(obj.message.customer_name).trigger('change');
                $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                $('#addModal').find('#supplierName').val(obj.message.supplier_name).trigger('change')
                $('#addModal').find('#siteCode').val(obj.message.site_code);
                $('#addModal').find('#siteName').val(obj.message.site_name).trigger('change');
                $('#addModal').find('#agent').val(obj.message.agent_name).trigger('change');
                $('#addModal').find('#agentCode').val(obj.message.agent_code);
                $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                $('#addModal').find('#orderWeight').val(obj.message.order_weight);
                $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                $('#addModal').find('#destination').val(obj.message.destination).trigger('change');
                $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                $('#addModal').find('#plantCode').val(obj.message.plant_code);
                $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
                $('#addModal').find('#rawMaterialName').val(obj.message.raw_mat_name).trigger('change');
                $('#addModal').find('#productName').val(obj.message.product_name).trigger('change');
                $('#addModal').find('#productCode').val(obj.message.product_code);

                    // Hide select and show input readonly
                    // if (obj.message.transaction_status == 'Purchase'){
                    //     $('#addModal').find('#purchaseOrder').next('.select2-container').hide();
                    //     $('#addModal').find('#purchaseOrderEdit').val(obj.message.purchase_order).show();
                    // }else{
                    //     $('#addModal').find('#salesOrder').next('.select2-container').hide();
                    //     $('#addModal').find('#salesOrderEdit').val(obj.message.purchase_order).show();
                    // }
                //});
                
                isEdit = false;

                // Remove Validation Error Message
                $('#addModal .is-invalid').removeClass('is-invalid');

                $('#addModal .select2[required]').each(function () {
                    var select2Field = $(this);
                    var select2Container = select2Field.next('.select2-container');
                    
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message
                });

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
        });
    }

    function updateSelects() { //Function to disable duplicated group
        const selectedValues = [
            $('#exportSoRepModal').find('#group1').val(),
            $('#exportSoRepModal').find('#group2').val(),
            $('#exportSoRepModal').find('#group3').val(),
        ];

        $('select[id^="group"]').each(function () {
            const currentSelect = $(this);
            const currentValue = currentSelect.val();

            currentSelect.find('option').each(function () {
                const option = $(this);
                const optionValue = option.val();

                if (optionValue === '') return; // Skip blank option (if any)

                // Disable if selected in other select, enable otherwise
                if (
                    selectedValues.includes(optionValue) &&
                    optionValue !== currentValue
                ) {
                    option.prop('disabled', true);
                } else {
                    option.prop('disabled', false);
                }
            });
        });
    }

    function post(id){
        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();

        // Exclude specific td elements by checking the event target
        $.post('php/getWeight.php', { userID: id, fromDate: fromDateI, toDate: toDateI, format: 'EXPANDABLE', acctType: 'DO' }, function (data) {
            var obj = JSON.parse(data);
            if (obj.status === 'success') {
                var weights = obj.message.weights;
                $('#exampleModalScrollableDO').text(obj.message.purchase_order);

                var tableHtml = `
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-striped align-middle" style="width:100%" id="doModalTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Transaction ID</th>
                                    <th>DO No.</th>
                                    <th>Vehicle</th>
                                    <th>Transporter</th>
                                    <th>Destination</th>
                                    <th>Gross Incoming</th>
                                    <th>Incoming Date</th>
                                    <th>Tare Outgoing</th>
                                    <th>Outgoing Date</th>
                                    <th>Nett Weight</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                for (var i = 0; i < weights.length; i++) {
                    var w = weights[i];
                    tableHtml += `
                        <tr>
                            <td><input type="checkbox" class="do-checkbox" value="${w.id}"></td>
                            <td>${w.transaction_id}</td>
                            <td>${w.delivery_no}</td>
                            <td>${w.lorry_plate_no1}</td>
                            <td>${w.transporter}</td>
                            <td>${w.destination}</td>
                            <td>${(parseFloat(w.gross_weight1) / 1000).toFixed(2)} MT</td>
                            <td>${w.gross_weight1_date}</td>
                            <td>${(parseFloat(w.tare_weight1) / 1000).toFixed(2)} MT</td>
                            <td>${w.tare_weight1_date}</td>
                            <td>${(parseFloat(w.nett_weight1) / 1000).toFixed(2)} MT</td>
                        </tr>
                    `;
                }

                tableHtml += `
                            </tbody>
                        </table>
                    </div>
                `;

                $('#doForm').html(tableHtml + '<div class="col-lg-12"><div class="hstack gap-2 justify-content-end"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-primary" id="submitDO">Submit</button></div></div>');

                // Select all handler
                $('#selectAll').on('change', function () {
                    $('.do-checkbox').prop('checked', this.checked);
                });

                $('#submitDO').off('click').on('click', function () {
                    var selectedDOs = [];

                    $("#doModalTable tbody input[type='checkbox']").each(function () {
                        if (this.checked) {
                            selectedDOs.push($(this).val());
                        }
                    });

                    if (selectedDOs.length > 0) {
                        if (confirm('Are you sure you want to post to SQL these items?')) {
                            $('#spinnerLoading').show();
                            $.post('php/postDo.php', {
                                userID: selectedDOs, 
                                type: 'MULTIDO'
                            }, function(data){
                                var obj = JSON.parse(data);
                                
                                if(obj.status === 'success'){
                                    $('#weightTable').DataTable().ajax.reload(null, false);
                                    $('#spinnerLoading').hide();
                                    $('#viewModal').modal('hide');
                                    toastr["success"](obj.message, "Success:");
                                }
                                else if(obj.status === 'failed'){
                                    $('#spinnerLoading').hide();
                                    toastr["error"](obj.message, "Failed:");
                                }
                                else{
                                    $('#spinnerLoading').hide();
                                    toastr["error"]("Something wrong when activate", "Failed:");
                                }
                            });
                        }
                    } 
                    else {
                        alert('Please select at least one DO to post');
                    }   
                });

                // Show modal
                $('#viewModal').modal('show');
            }
        });
    }
    </script>
</body>
</html>