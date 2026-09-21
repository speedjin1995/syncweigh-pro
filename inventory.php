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
                                                                    <th>Weight (Kg)</th>
                                                                    <th>Drum</th>
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
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="adjustmentModalTitle"><i class="ri-list-settings-line me-2"></i>Stock Adjustment - New</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="adjustmentForm" autocomplete="off">
                            <!-- Header Section -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Adjustment Date <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="adjDate" name="adjDate" value="<?= date('d/m/Y') ?>" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Plant <span class="text-danger">*</span></label>
                                            <select class="form-select" id="adjPlant" name="adjPlant" required>
                                                <option value="">Select Plant</option>
                                                <?php 
                                                $plant->data_seek(0);
                                                while($rowP = mysqli_fetch_assoc($plant)){ ?>
                                                <option value="<?=$rowP['id']?>"><?=$rowP['name']?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Batch/Drum <span class="text-danger">*</span></label>
                                            <select class="form-select" id="adjBatchDrum" name="adjBatchDrum" required>
                                                <option value="">Select</option>
                                                <option value="Batch">Batch</option>
                                                <option value="Drum">Drum</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Remark</label>
                                            <input type="text" class="form-control" id="adjRemark" name="adjRemark" placeholder="Enter Remark">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Line Items Section -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0"><i class="ri-list-check me-2"></i>Items</h6>
                                        <button type="button" class="btn btn-primary btn-sm" id="addLineItem">
                                            <i class="ri-add-line"></i> Add Item
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="lineItemsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:220px">RAW MATERIAL</th>
                                                    <th style="width:100px" class="text-center">CURRENT QTY</th>
                                                    <th style="width:100px" class="text-center">ADJUST QTY</th>
                                                    <th style="width:100px" class="text-center">NEW QTY</th>
                                                    <th style="width:100px" class="text-center">UNIT COST</th>
                                                    <th style="width:100px" class="text-center">TOTAL COST</th>
                                                    <th>REASON</th>
                                                    <th style="width:50px"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="lineItemsBody">
                                                <!-- Dynamic rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td class="text-end fw-bold">Total:</td>
                                                    <td class="text-center fw-bold" id="totalCurrentQty">0.00</td>
                                                    <td class="text-center fw-bold" id="totalAdjustQty">0.00</td>
                                                    <td class="text-center fw-bold" id="totalNewQty">0.00</td>
                                                    <td></td>
                                                    <td class="text-center fw-bold" id="totalCost">0.00</td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="submitAdjustment"><i class="ri-save-line me-1"></i>Save</button>
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
                $.post('php/inventory.php', $('#siteForm').serialize(), function(data){
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

        // Raw materials cache - load when plant changes
        $('#adjPlant').on('change', function() {
            var plantCode = $(this).val();
            $('#lineItemsBody').empty();
            lineItemCounter = 0;
            updateTotals();
            
            if (plantCode) {
                $.post('php/controllers/StockAdjustmentController.php', { action: 'getRawMaterials', plant: plantCode }, function(data) {
                    rawMaterialsCache = data.status === 'success' ? data.data : [];
                });
            } else {
                rawMaterialsCache = [];
            }
        });

        // Add line item button
        $('#addLineItem').on('click', function() {
            if (!$('#adjPlant').val()) {
                $("#failBtn").attr('data-toast-text', 'Please select a plant first');
                $("#failBtn").click();
                return;
            }
            addLineItem();
        });

        // Reset modal on open
        $('#addAdjustment').on('click', function() {
            $('#adjustmentForm')[0].reset();
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

            $('#spinnerLoading').show();
            $.post('php/controllers/StockAdjustmentController.php', {
                action: 'create',
                plant: plant,
                batch_drum: batchDrum,
                remark: remark,
                items: JSON.stringify(items)
            }, function(data) {
                var obj = JSON.parse(data);
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
                'url':'php/controllers/StockAdjustmentController.php',
                'data': {
                    action: 'list',
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
                { data: 'remark' }
            ] 
        });
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
                    <select class="form-select form-select-sm raw-mat-select" name="items[${lineItemCounter}][raw_mat_id]" onchange="onRawMatChange(this)" required>
                        ${options}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center current-qty" value="0.00" readonly style="background-color:#e9ecef">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center adjust-qty" name="items[${lineItemCounter}][qty]" placeholder="+/-" step="0.01" onchange="calculateNewQty(this)" onkeyup="calculateNewQty(this)" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center new-qty" value="0.00" readonly style="background-color:#e9ecef">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center unit-cost" placeholder="0.00" step="0.01" min="0" onchange="calculateTotalCost(this)" onkeyup="calculateTotalCost(this)">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-center total-cost" value="0.00" readonly style="background-color:#e9ecef">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="items[${lineItemCounter}][reason]" placeholder="Enter reason">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeLineItem(this)">
                        <i class="ri-close-line"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#lineItemsBody').append(row);
    }

    function onRawMatChange(select) {
        var row = $(select).closest('tr');
        var selectedOption = $(select).find('option:selected');
        var currentQty = parseFloat(selectedOption.data('qty')) || 0;
        row.find('.current-qty').val(currentQty.toFixed(2));
        row.find('.adjust-qty').val('');
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
        $('#totalAdjustQty').text(totalAdjust.toFixed(2));
        $('#totalNewQty').text(totalNew.toFixed(2));
        $('#totalCost').text(totalCost.toFixed(2));
    }

    function removeLineItem(btn) {
        $(btn).closest('tr').remove();
        updateTotals();
    }

    function initInventoryTable() {
        var plantNoI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        
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
                'url':'php/filterInventory.php',
                'data': {
                    plant: plantNoI
                } 
            },
            'columns': [
                { data: 'no' },
                { data: 'raw_mat_code' },
                { data: 'name' },
                { data: 'raw_mat_weight' },
                { data: 'raw_mat_count' },
                { 
                    data: 'id',
                    orderable: false,
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
        $.post('php/getInventory.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
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