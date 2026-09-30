<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";

if (!hasModulePermission('Stock Management', 'Inventory', ['view', 'edit'])){
    header('Location: no-permission.php');
    exit;
}

if (hasModulePermission('Stock Management', 'Inventory', ['view_all_plants'])){
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' ORDER BY name ASC");
}else{
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username') ORDER BY name ASC");
}
?>

<head>

    <title>Inventory | PWS - Weighing System</title>
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

        .nav-tabs .nav-link.active {
            background-color: #dc3545 !important;
            color: #fff !important;
            border-color: #dc3545 !important;
        }

        .nav-tabs .nav-link {
            color: #dc3545;
            border: 1px solid #dee2e6;
            margin-right: 5px;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            background-color: #f8d7da;
            border-color: #dc3545;
        }

        /* ---- Stock Adjustment modal ---- */
        #adjustmentModal .modal-header,
        #adjustmentModal .modal-footer {
            background-color: #f8f9fa;
        }

        #adjustmentModal .form-label {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        /* Items scroll on their own so the modal header, totals and footer stay in view.
           The subtracted height is the rest of the modal chrome, which keeps the whole
           dialog inside the viewport down to a 768px-tall screen. */
        #adjustmentModal .items-scroll {
            max-height: max(168px, calc(100vh - 490px));
            overflow-y: auto;
        }

        #adjustmentModal #lineItemsTable thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: #eff2f7;
            box-shadow: inset 0 -1px 0 #dee2e6;
            padding: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            vertical-align: middle;
        }

        #adjustmentModal #lineItemsTable td {
            padding: 0.5rem;
            vertical-align: middle;
        }

        /* Adjust Qty is the field being keyed in, so it carries the most weight */
        #adjustmentModal #lineItemsTable .adjust-qty {
            font-weight: 600;
        }

        /* Keeps the remove button level with the inputs and the Select2 picker */
        #adjustmentModal #lineItemsTable tbody .btn {
            height: 38px;
        }

        /* Keeps a negative adjustment readable at a glance */
        #adjustmentModal .is-negative {
            color: #dc3545;
        }

        /* Extra side padding lines the totals up with the numbers inside the inputs above,
           which sit one input padding + border further in than a plain cell */
        #adjustmentModal #lineItemsTable tfoot td {
            position: sticky;
            bottom: 0;
            z-index: 2;
            padding: 0.5rem calc(1rem + 1px);
            background-color: #eff2f7;
            border-top: 2px solid #adb5bd;
            font-weight: 600;
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

                            <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
                            <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>

                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#inventoryTab" role="tab">
                                        <i class="ri-store-2-line me-1"></i> Inventory
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#stockAdjustmentTab" role="tab">
                                        <i class="ri-exchange-line me-1"></i> Stock Adjustment
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Inventory Tab -->
                                <div class="tab-pane active" id="inventoryTab" role="tabpanel">
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
                                                                    <label for="ForminputState" class="form-label">Plant</label>
                                                                    <select id="plantSearch" class="form-select" >
                                                                        <?php while($rowPlantF=mysqli_fetch_assoc($plant)){ ?>
                                                                            <option value="<?=$rowPlantF['plant_code'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-3">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Batch/Drum</label>
                                                                    <select id="batchDrumSearch" class="form-select">
                                                                        <option value="">All</option>
                                                                        <option value="Batch">Batch</option>
                                                                        <option value="Drum">Drum</option>
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
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Inventory</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Raw Material Code</th>
                                                                    <th>Raw Material Name</th>
                                                                    <th>Plant</th>
                                                                    <th>Batch/Drum</th>
                                                                    <th>Weight (Kg)</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock Adjustment Tab -->
                                <div class="tab-pane" id="stockAdjustmentTab" role="tabpanel">
                                    <div class="row">
                                        <div class="col">
                                            <div class="h-100">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between">
                                                            <h5 class="card-title mb-0">Stock Adjustment History</h5>
                                                            <?php if (hasModulePermission('Stock Management', 'Inventory', ['create', 'edit'])){ ?>
                                                            <button type="button" id="addAdjustment" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                                Add Adjustment
                                                            </button>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="adjustmentTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Adjustment No</th>
                                                                    <th>Date</th>
                                                                    <th>Plant</th>
                                                                    <th>Batch/Drum</th>
                                                                    <th>Items</th>
                                                                    <th>Total Qty</th>
                                                                    <th>Total Cost</th>
                                                                    <th>Remark</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end tab-content -->
                    

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
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="siteForm" class="needs-validation" novalidate autocomplete="off">
                            <div class=" row col-12">
                                <div class="col-xxl-12 col-lg-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="rawMatCode" class="col-sm-4 col-form-label">Raw Material Code</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="rawMatCode" name="rawMatCode" placeholder="Raw Material Code" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="rawMatName" class="col-sm-4 col-form-label">Raw Material Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="rawMatName" name="rawMatName" placeholder="Raw Material Name" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="basicUom" class="col-sm-4 col-form-label">Basic UOM</label>
                                                        <div class="col-sm-8">
                                                            <div class="input-group">
                                                                <input type="number" class="form-control" id="basicUom" name="basicUom" required>
                                                                <div class="input-group-text" id="basicUomUnit">KG</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="weight" class="col-sm-4 col-form-label">Weight</label>
                                                        <div class="col-sm-8">
                                                            <div class="input-group">
                                                                <input type="number" class="form-control input-readonly" id="weight" name="weight" readonly>
                                                                <div class="input-group-text">KG</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="drum" class="col-sm-4 col-form-label">Drum</label>
                                                        <div class="col-sm-8">
                                                            <input type="number" class="form-control" id="drum" name="drum" placeholder="Raw Material Count">
                                                        </div>
                                                    </div>
                                                </div>                                                       
                                                <input type="hidden" class="form-control" id="id" name="id">
                                                <input type="hidden" class="form-control" id="rawMatId" name="rawMatId">
                                                <input type="hidden" class="form-control" id="basicUnitId" name="basicUnitId">
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

        <!-- Stock Adjustment Modal -->
        <div class="modal fade" id="adjustmentModal" tabindex="-1" role="dialog" aria-labelledby="adjustmentModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="adjustmentModalTitle"><i class="ri-list-settings-line me-2"></i>Stock Adjustment - New</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="adjustmentForm" autocomplete="off">
                            <input type="hidden" id="adjId" name="adjId" value="">
                            <!-- Header Section -->
                            <div class="card border mb-3">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-4 col-md-6">
                                            <label class="form-label" for="adjDate">Adjustment Date <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control input-readonly" id="adjDate" name="adjDate" value="<?= date('d/m/Y') ?>" readonly>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <label class="form-label" for="adjPlant">Plant <span class="text-danger">*</span></label>
                                            <select class="form-select select2" id="adjPlant" name="adjPlant" required>
                                                <option value="">Select Plant</option>
                                                <?php 
                                                $plant->data_seek(0);
                                                while($rowP = mysqli_fetch_assoc($plant)){ ?>
                                                <option value="<?=$rowP['id']?>"><?=$rowP['name']?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <label class="form-label" for="adjBatchDrum">Batch/Drum <span class="text-danger">*</span></label>
                                            <select class="form-select select2" id="adjBatchDrum" name="adjBatchDrum" required>
                                                <option value="">Select</option>
                                                <option value="Batch">Batch</option>
                                                <option value="Drum">Drum</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-12 col-md-12">
                                            <label class="form-label" for="adjRemark">Remark</label>
                                            <input type="text" class="form-control" id="adjRemark" name="adjRemark" placeholder="Enter Remark">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Line Items Section -->
                            <div class="card border mb-0">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0"><i class="ri-list-check me-2"></i>Items</h6>
                                    <button type="button" class="btn btn-danger btn-sm" id="addLineItem">
                                        <i class="ri-add-line align-bottom me-1"></i>Add Item
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive items-scroll">
                                        <table class="table table-bordered align-middle mb-0" id="lineItemsTable">
                                            <thead>
                                                <tr>
                                                    <th style="width:240px">RAW MATERIAL</th>
                                                    <th style="width:110px" class="text-end">CURRENT QTY</th>
                                                    <th style="width:110px" class="text-end">ADJUST QTY</th>
                                                    <th style="width:110px" class="text-end">NEW QTY</th>
                                                    <th style="width:110px" class="text-end">UNIT COST</th>
                                                    <th style="width:110px" class="text-end">TOTAL COST</th>
                                                    <th style="min-width:200px">REASON</th>
                                                    <th style="width:48px"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="lineItemsBody">
                                                <!-- Dynamic rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-end">Total:</td>
                                                    <td class="text-end" id="totalCurrentQty">0.00</td>
                                                    <td class="text-end" id="totalAdjustQty">0.00</td>
                                                    <td class="text-end" id="totalNewQty">0.00</td>
                                                    <td></td>
                                                    <td class="text-end" id="totalCost">0.00</td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" id="submitAdjustment"><i class="ri-save-line align-bottom me-1"></i>Save</button>
                    </div>
                </div>
            </div>
        </div><!-- /.adjustment modal -->

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

    var INVENTORY_API = 'php/Inventory/index.php'; // all inventory backend calls go through this endpoint
    var permissions = <?= json_encode($_SESSION['permissions']) ?>;
    var isSADMIN = <?= json_encode($_SESSION['roles'] == 'SADMIN') ?>;
    var table = null;
    var adjustmentTable = null;
    var rawMaterialsCache = [];
    var lineItemCounter = 0;

    $(function () {
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';

        // Initialize inventory table
        initInventoryTable();

        $('#filterSearch').on('click', function(){
            initInventoryTable();
        });

        $('#submitSite').on('click', function(){
            if($('#siteForm').valid()){
                $('#spinnerLoading').show();
                $.post(INVENTORY_API, $('#siteForm').serialize() + '&action=update', function(data){
                    var obj = typeof data === 'string' ? JSON.parse(data) : data;
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
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

                    }
                });
            }
        });

        $('#basicUom').on('keyup', function(){
            var basicUom = parseFloat($(this).val());
            var basicUomUnitId = $('#addModal').find('#basicUnitId').val();
            var rawMatId = $('#addModal').find('#rawMatId').val();

            if (basicUomUnitId == 2){
                $('#addModal').find('#weight').val(basicUom);
            }else{
                // Call to backend to get conversion rate
                if (rawMatId && basicUom){
                    $.post('php/getProdRawMatUOM.php', {userID: rawMatId, type: 'PO'}, function(data)
                    {
                        var obj = JSON.parse(data);
                        if(obj.status === 'success'){
                            // Processing for order quantity (KG)
                            var rate = parseFloat(obj.message.rate);
                            var weight = basicUom/rate;
                            weight = parseInt(weight);

                            $('#addModal').find('#weight').val(weight);
                        }
                        else if(obj.status === 'failed'){
                            alert(obj.message);
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                        else{
                            alert(obj.message);
                            $("#failBtn").attr('data-toast-text', obj.message );
                            $("#failBtn").click();
                        }
                    });
                }
            }
        });

        // Initialize adjustment table when tab is shown
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('href') === '#stockAdjustmentTab') {
                initAdjustmentTable();
            }
        });

        // Select2 for the stock adjustment modal dropdowns
        $('#adjustmentModal .select2').select2({
            placeholder: "Please Select",
            width: '100%',
            dropdownParent: $('#adjustmentModal') // Ensures dropdown is not cut off by the modal
        });
        styleSelect2($('#adjustmentModal'));

        // Raw materials cache - reload when plant or batch/drum changes
        $('#adjPlant, #adjBatchDrum').on('change', function() {
            var plantId = $('#adjPlant').val();
            var batchDrum = $('#adjBatchDrum').val();
            $('#lineItemsBody').empty();
            lineItemCounter = 0;
            rawMaterialsCache = [];
            updateTotals();

            if (plantId && batchDrum) {
                $.post(INVENTORY_API, { action: 'adj_raw_materials', plant: plantId, batch_drum: batchDrum }, function(data) {
                    var obj = typeof data === 'string' ? JSON.parse(data) : data;
                    rawMaterialsCache = obj.status === 'success' ? obj.data : [];
                });
            }
        });

        // Add line item button
        $('#addLineItem').on('click', function() {
            if (!$('#adjPlant').val() || !$('#adjBatchDrum').val()) {
                $("#failBtn").attr('data-toast-text', 'Please select plant and Batch/Drum first');
                $("#failBtn").click();
                return;
            }
            addLineItem();
        });

        // Reset modal on open
        $('#addAdjustment').on('click', function() {
            $('#adjustmentForm')[0].reset();
            $('#adjId').val('');
            $('#adjustmentModalTitle').html('<i class="ri-list-settings-line me-2"></i>Stock Adjustment - New');
            $('#adjPlant, #adjBatchDrum').prop('disabled', false); // plant and batch/drum are locked only when editing
            $('#adjPlant, #adjBatchDrum').val('').trigger('change.select2'); // refresh Select2 display after reset
            $('#adjDate').val('<?= date("d/m/Y") ?>');
            $('#lineItemsBody').empty();
            lineItemCounter = 0;
            rawMaterialsCache = [];
            updateTotals();
        });

        // Submit stock adjustment
        $('#submitAdjustment').on('click', function() {
            var plant = $('#adjPlant').val();
            var batchDrum = $('#adjBatchDrum').val();
            var remark = $('#adjRemark').val();
            var items = [];
            var valid = true;

            if (!plant) {
                $("#failBtn").attr('data-toast-text', 'Please select a plant');
                $("#failBtn").click();
                return;
            }

            if (!batchDrum) {
                $("#failBtn").attr('data-toast-text', 'Please select Batch/Drum');
                $("#failBtn").click();
                return;
            }

            $('#lineItemsBody tr').each(function() {
                var rawMatId = $(this).find('select').val();
                var adjustQty = $(this).find('.adjust-qty').val();
                var reason = $(this).find('input[name*="reason"]').val();
                var currentQty = $(this).find('.current-qty').val();
                var newQty = $(this).find('.new-qty').val();
                var unitCost = $(this).find('.unit-cost').val() || '0';
                var totalCost = $(this).find('.total-cost').val() || '0';
                
                if (!rawMatId || !adjustQty || parseFloat(adjustQty) == 0) {
                    valid = false;
                    return false;
                }
                items.push({ 
                    raw_mat_id: rawMatId, 
                    qty: adjustQty,
                    qty_before: currentQty,
                    qty_after: newQty,
                    unit_cost: unitCost,
                    total_cost: totalCost,
                    reason: reason
                });
            });

            if (!valid || items.length === 0) {
                $("#failBtn").attr('data-toast-text', 'Please add at least one valid item with adjustment quantity');
                $("#failBtn").click();
                return;
            }

            var adjId = $('#adjId').val();
            $('#spinnerLoading').show();
            $.post(INVENTORY_API, {
                action: adjId ? 'adj_update' : 'adj_create',
                id: adjId,
                plant: plant,
                batch_drum: batchDrum,
                remark: remark,
                items: JSON.stringify(items)
            }, function(data) {
                var obj = typeof data === 'string' ? JSON.parse(data) : data;
                if (obj.status === 'success') {
                    if (adjustmentTable) adjustmentTable.ajax.reload();
                    if (table) table.ajax.reload();
                    $('#spinnerLoading').hide();
                    $('#adjustmentModal').modal('hide');
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                } else {
                    $('#spinnerLoading').hide();
                    $("#failBtn").attr('data-toast-text', obj.message);
                    $("#failBtn").click();
                }
            });
        });
    });

    // Stock Adjustment Tab - Initialize DataTable
    function initAdjustmentTable() {
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        
        if (adjustmentTable) {
            adjustmentTable.destroy();
        }

        adjustmentTable = $("#adjustmentTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'order': [[ 1, 'desc' ]],
            'ajax': {
                'url': INVENTORY_API,
                'data': {
                    action: 'adj_list',
                    plant: plantNoI
                } 
            },
            'columns': [
                { data: 'no' },
                { data: 'adjustment_no' },
                { data: 'adjustment_date' },
                { data: 'plant_name' },
                { data: 'batch_drum' },
                { data: 'total_items' },
                { data: 'total_qty' },
                { data: 'total_cost' },
                { data: 'remark' },
                {
                    data: 'id',
                    orderable: false,
                    render: function ( data, type, row ) {
                        var canEdit = isSADMIN || hasInventoryPermission('edit');
                        var canDelete = isSADMIN || hasInventoryPermission('delete');
                        if (!canEdit && !canDelete) {
                            return '';
                        }
                        var items = '';
                        if (canEdit) {
                            items += `<li><a class="dropdown-item edit-item-btn" onclick="editAdjustment(${data})"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>`;
                        }
                        if (canDelete) {
                            items += `<li><a class="dropdown-item remove-item-btn" onclick="deleteAdjustment(${data})"><i class="ri-delete-bin-5-line align-bottom me-2 text-muted"></i> Delete</a></li>`;
                        }
                        return `
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-more-fill align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">${items}</ul>
                            </div>`;
                    }
                }
            ]
        });
    }

    function hasInventoryPermission(permission) {
        return !!(permissions['Stock Management'] && permissions['Stock Management']['Inventory'] && permissions['Stock Management']['Inventory'].includes(permission));
    }

    // Open the adjustment modal filled with an existing adjustment
    function editAdjustment(id) {
        $('#spinnerLoading').show();
        $.post(INVENTORY_API, {action: 'adj_get', id: id}, function(data) {
            var obj = typeof data === 'string' ? JSON.parse(data) : data;
            $('#spinnerLoading').hide();
            if (obj.status !== 'success') {
                $("#failBtn").attr('data-toast-text', obj.message);
                $("#failBtn").click();
                return;
            }

            var adj = obj.message;
            $('#adjustmentForm')[0].reset();
            $('#adjId').val(adj.id);
            $('#adjustmentModalTitle').html('<i class="ri-list-settings-line me-2"></i>Stock Adjustment - Edit ' + adj.adjustment_no);
            $('#adjDate').val(adj.adjustment_date);
            $('#adjRemark').val(adj.remark);
            // trigger only the Select2 refresh so the plant/batch change handler does not clear the rows
            $('#adjPlant').val(adj.plant_id).trigger('change.select2');
            $('#adjBatchDrum').val(adj.batch_drum).trigger('change.select2');
            $('#adjPlant, #adjBatchDrum').prop('disabled', true); // plant and batch/drum cannot change on edit

            $('#lineItemsBody').empty();
            lineItemCounter = 0;
            rawMaterialsCache = adj.raw_materials; // current qty here already excludes this adjustment
            adj.items.forEach(function(item) {
                addLineItem();
                var $row = $('#lineItemsBody tr').last();
                $row.find('.raw-mat-select').val(item.raw_mat_id).trigger('change');
                $row.find('.adjust-qty').val(item.qty);
                $row.find('.unit-cost').val(item.unit_cost);
                $row.find('input[name*="reason"]').val(item.reason);
                calculateNewQty($row.find('.adjust-qty')[0]);
            });
            updateTotals();
            $('#adjustmentModal').modal('show');
        });
    }

    function deleteAdjustment(id) {
        if (confirm('Are you sure you want to delete this stock adjustment? The stock will be reversed.')) {
            $('#spinnerLoading').show();
            $.post(INVENTORY_API, {action: 'adj_delete', id: id}, function(data) {
                var obj = typeof data === 'string' ? JSON.parse(data) : data;
                $('#spinnerLoading').hide();
                if (obj.status === 'success') {
                    if (adjustmentTable) adjustmentTable.ajax.reload();
                    if (table) table.ajax.reload();
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                } else {
                    $("#failBtn").attr('data-toast-text', obj.message);
                    $("#failBtn").click();
                }
            });
        }
    }

    // Global functions for stock adjustment
    function addLineItem() {
        lineItemCounter++;
        var options = '<option value="">- Select -</option>';
        rawMaterialsCache.forEach(function(item) {
            options += '<option value="' + item.id + '" data-code="' + item.raw_mat_code + '" data-qty="' + (item.current_qty || 0) + '">' + item.raw_mat_code + ' - ' + item.name + '</option>';
        });

        var row = `
            <tr data-row="${lineItemCounter}">
                <td>
                    <select class="form-select raw-mat-select" name="items[${lineItemCounter}][raw_mat_id]" required>
                        ${options}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control text-end input-readonly current-qty" value="0.00" readonly>
                </td>
                <td>
                    <input type="number" class="form-control text-end adjust-qty" name="items[${lineItemCounter}][qty]" placeholder="+/-" step="0.01" onchange="calculateNewQty(this)" onkeyup="calculateNewQty(this)" required>
                </td>
                <td>
                    <input type="text" class="form-control text-end input-readonly new-qty" value="0.00" readonly>
                </td>
                <td>
                    <input type="number" class="form-control text-end unit-cost" placeholder="0.00" step="0.01" min="0" onchange="calculateTotalCost(this)" onkeyup="calculateTotalCost(this)">
                </td>
                <td>
                    <input type="text" class="form-control text-end input-readonly total-cost" value="0.00" readonly>
                </td>
                <td>
                    <input type="text" class="form-control" name="items[${lineItemCounter}][reason]" placeholder="Reason for adjustment">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="removeLineItem(this)" title="Remove item">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        `;
        var $row = $(row);
        $('#lineItemsBody').append($row);

        // Searchable Select2 for the raw material picker
        $row.find('.raw-mat-select').select2({
            placeholder: "- Select -",
            width: '100%',
            dropdownParent: $('#adjustmentModal')
        }).on('change', function() {
            onRawMatChange(this);
        });
        styleSelect2($row);
    }

    // Match Select2 height and arrow position to the other form controls
    function styleSelect2($scope) {
        $scope.find('.select2-container .select2-selection--single').css({
            'padding-top': '4px',
            'padding-bottom': '4px',
            'height': 'auto'
        });
        $scope.find('.select2-container .select2-selection__arrow').css({
            'padding-top': '33px',
            'height': 'auto'
        });
    }

    function onRawMatChange(select) {
        var row = $(select).closest('tr');
        var selectedOption = $(select).find('option:selected');
        var currentQty = parseFloat(selectedOption.data('qty')) || 0;
        row.find('.current-qty').val(currentQty.toFixed(2));
        row.find('.adjust-qty').val('').removeClass('is-negative');
        row.find('.new-qty').val(currentQty.toFixed(2));
        updateTotals();
    }

    function calculateNewQty(input) {
        var row = $(input).closest('tr');
        var currentQty = parseFloat(row.find('.current-qty').val()) || 0;
        var adjustQty = parseFloat($(input).val()) || 0;
        var newQty = currentQty + adjustQty;
        if (newQty < 0) newQty = 0;
        row.find('.new-qty').val(newQty.toFixed(2));
        $(input).toggleClass('is-negative', adjustQty < 0); // display only, the value is unchanged
        calculateTotalCost(row.find('.unit-cost')[0]);
        updateTotals();
    }

    function calculateTotalCost(input) {
        var row = $(input).closest('tr');
        var adjustQty = Math.abs(parseFloat(row.find('.adjust-qty').val()) || 0);
        var unitCost = parseFloat($(input).val()) || 0;
        var totalCost = adjustQty * unitCost;
        row.find('.total-cost').val(totalCost.toFixed(2));
        updateTotals();
    }

    function updateTotals() {
        var totalCurrent = 0, totalAdjust = 0, totalNew = 0, totalCost = 0;
        $('#lineItemsBody tr').each(function() {
            totalCurrent += parseFloat($(this).find('.current-qty').val()) || 0;
            totalAdjust += parseFloat($(this).find('.adjust-qty').val()) || 0;
            totalNew += parseFloat($(this).find('.new-qty').val()) || 0;
            totalCost += parseFloat($(this).find('.total-cost').val()) || 0;
        });
        $('#totalCurrentQty').text(totalCurrent.toFixed(2));
        $('#totalAdjustQty').text(totalAdjust.toFixed(2)).toggleClass('is-negative', totalAdjust < 0);
        $('#totalNewQty').text(totalNew.toFixed(2));
        $('#totalCost').text(totalCost.toFixed(2));
    }

    function removeLineItem(btn) {
        $(btn).closest('tr').remove();
        updateTotals();
    }

    function initInventoryTable() {
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var batchDrumFilter = $('#batchDrumSearch').val() ? $('#batchDrumSearch').val() : '';
        
        if (table) {
            table.destroy();
        }

        table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'order': [[ 1, 'asc' ]],
            'columnDefs': [ { orderable: false, targets: [0] }],
            'ajax': {
                'url': INVENTORY_API,
                'data': {
                    action: 'list',
                    plant: plantNoI,
                    batch_drum: batchDrumFilter
                } 
            },
            'columns': [
                { data: 'no' },
                { data: 'raw_mat_code' },
                { data: 'name' },
                { data: 'plant_name' },
                { data: 'batch_drum' },
                { data: 'raw_mat_weight' },
                { 
                    data: 'id',
                    orderable: false,
                    visible: false, // Edit button hidden for now; remove this line to show it again
                    render: function ( data, type, row ) {
                        if (isSADMIN || (permissions['Stock Management'] && permissions['Stock Management']['Inventory'] && permissions['Stock Management']['Inventory'].includes('edit'))){
                            return `
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item edit-item-btn" id="edit${data}" onclick="edit(${data})">
                                                <i class="ri-pen align-bottom me-2 text-muted"></i> Edit
                                            </a>
                                        </li>
                                    </ul>
                                </div>`;
                        }
                        return '';
                    }
                }
            ] 
        });
    }

    function edit(id){
        $('#spinnerLoading').show();
        $.post(INVENTORY_API, {action: 'get', id: id}, function(data)
        {
            var obj = typeof data === 'string' ? JSON.parse(data) : data;
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#rawMatId').val(obj.message.raw_mat_id);
                $('#addModal').find('#rawMatCode').val(obj.message.raw_mat_code);
                $('#addModal').find('#rawMatName').val(obj.message.name);
                $('#addModal').find('#basicUom').val(obj.message.raw_mat_basic_uom);
                $('#addModal').find('#basicUomUnit').text(obj.message.basic_uom);
                $('#addModal').find('#basicUnitId').val(obj.message.basic_uom_id);
                $('#addModal').find('#weight').val(obj.message.raw_mat_weight);
                $('#addModal').find('#drum').val(obj.message.raw_mat_count);
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
    
    </script>
</body>
</html>