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

        .inventory-expand-icon {
            font-size: 16px;
            vertical-align: middle;
        }

        .inventory-adjustment-wrap {
            background: var(--vz-light);
            border-left: 3px solid var(--vz-primary);
            padding: 12px;
        }

        .inventory-adjustment-table {
            margin-bottom: 0;
        }

        .inventory-adjustment-table th,
        .inventory-adjustment-table td {
            padding: 0.5rem 0.75rem;
            vertical-align: middle;
        }

        #weightTable > tbody > tr {
            cursor: pointer;
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
                                        <!--datatable--> 
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h5 class="card-title mb-0">Inventory</h5>
                                                            </div>
                                                            <!--div class="flex-shrink-0">
                                                                <button type="button" id="exportPdf" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Export PDF
                                                                </button>
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                            </div--> 
                                                        </div> 
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
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">Adjust Inventory</h5>
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
                                                        <label for="currentWeight" class="col-sm-4 col-form-label">Current Weight</label>
                                                        <div class="col-sm-8">
                                                            <div class="input-group">
                                                                <input type="number" class="form-control input-readonly" id="currentWeight" name="currentWeight" readonly>
                                                                <div class="input-group-text">KG</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="currentDrum" class="col-sm-4 col-form-label">Current Drum</label>
                                                        <div class="col-sm-8">
                                                            <input type="number" class="form-control input-readonly" id="currentDrum" name="currentDrum" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="weightAdjustment" class="col-sm-4 col-form-label">Weight Adjustment</label>
                                                        <div class="col-sm-8">
                                                            <div class="input-group">
                                                                <input type="number" step="any" class="form-control" id="weightAdjustment" name="weightAdjustment" placeholder="Weight Adjustment">
                                                                <div class="input-group-text">KG</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="drumAdjustment" class="col-sm-4 col-form-label">Drum Adjustment</label>
                                                        <div class="col-sm-8">
                                                            <input type="number" step="any" class="form-control" id="drumAdjustment" name="drumAdjustment" placeholder="Drum Adjustment">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="newWeightBalance" class="col-sm-4 col-form-label">New Weight Balance</label>
                                                        <div class="col-sm-8">
                                                            <div class="input-group">
                                                                <input type="number" class="form-control input-readonly" id="newWeightBalance" name="newWeightBalance" readonly>
                                                                <div class="input-group-text">KG</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="newDrumBalance" class="col-sm-4 col-form-label">New Drum Balance</label>
                                                        <div class="col-sm-8">
                                                            <input type="number" class="form-control input-readonly" id="newDrumBalance" name="newDrumBalance" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-12 col-lg-12 mb-3">
                                                    <div class="row">
                                                        <label for="adjustmentRemarks" class="col-sm-4 col-form-label">Remarks</label>
                                                        <div class="col-sm-8">
                                                            <textarea class="form-control" id="adjustmentRemarks" name="adjustmentRemarks" rows="3" placeholder="Remarks"></textarea>
                                                        </div>
                                                    </div>
                                                </div>                                                       
                                                <input type="hidden" class="form-control" id="id" name="id">
                                                <input type="hidden" class="form-control" id="rawMatId" name="rawMatId">
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

    var permissions = <?= json_encode($_SESSION['permissions'] ?? []) ?>;
    var isSADMIN = <?= json_encode($_SESSION['roles'] == 'SADMIN') ?>;
    var table;

    $(function () {
        initInventoryTable($('#plantSearch').val() ? $('#plantSearch').val() : '');

        $('#filterSearch').on('click', function(){
            initInventoryTable($('#plantSearch').val() ? $('#plantSearch').val() : '');
        });

        $('#weightAdjustment, #drumAdjustment').on('input', function(){
            updateProjectedBalance();
        });

        $('#weightTable tbody').on('click', 'tr', function(e) {
            if ($(e.target).closest('.dropdown, .dropdown-menu, a, button, .inventory-adjustment-wrap').length) {
                return;
            }

            var tr = $(this);
            var row = table.row(tr);

            if (!row.data()) {
                return;
            }

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                tr.find('.inventory-expand-icon').removeClass('mdi-chevron-down').addClass('mdi-chevron-right');
                return;
            }

            row.child(formatAdjustmentDetails(null)).show();
            tr.addClass('shown');
            tr.find('.inventory-expand-icon').removeClass('mdi-chevron-right').addClass('mdi-chevron-down');
            loadAdjustmentDetails(row);
        });

        $('#submitSite').on('click', function(){
            var weightAdjustment = readAdjustmentValue('#weightAdjustment');
            var drumAdjustment = readAdjustmentValue('#drumAdjustment');

            if (weightAdjustment === null || drumAdjustment === null) {
                showFailedToast('Please key in valid adjustment values');
                return;
            }

            if (weightAdjustment === 0 && drumAdjustment === 0) {
                showFailedToast('Please key in Weight Adjustment or Drum Adjustment');
                return;
            }

            if (!updateProjectedBalance()) {
                showFailedToast('Total inventory cannot be negative');
                return;
            }

            $('#spinnerLoading').show();
            $.post('php/inventoryAdjustment.php', $('#siteForm').serialize(), function(data){
                var obj = parseResponse(data);

                if(obj.status === 'success'){
                    table.ajax.reload(null, false);
                    $('#spinnerLoading').hide();
                    $('#addModal').modal('hide');
                    showSuccessToast(obj.message);
                }
                else{
                    $('#spinnerLoading').hide();
                    showFailedToast(obj.message);
                }
            }).fail(function(){
                $('#spinnerLoading').hide();
                showFailedToast('Something went wrong');
            });
        });
    });

    function initInventoryTable(plantNoI) {
        if ($.fn.DataTable.isDataTable('#weightTable')) {
            $("#weightTable").DataTable().clear().destroy();
        }

        table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'order': [[ 1, 'asc' ]],
            'columnDefs': [ { orderable: false, targets: [0, 5] }],
            'ajax': {
                'url':'php/filterInventory.php',
                'data': {
                    plant: plantNoI,
                }
            },
            'columns': [
                {
                    data: 'no',
                    render: function (data) {
                        return `<span class="d-inline-flex align-items-center"><i class="mdi mdi-chevron-right inventory-expand-icon me-1"></i>${data}</span>`;
                    }
                },
                { data: 'raw_mat_code' },
                { data: 'name' },
                { data: 'raw_mat_weight' },
                { data: 'raw_mat_count' },
                {
                    data: 'id',
                    orderable: false,
                    render: function ( data ) {
                        if (canAdjustInventory()){
                            return `
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item edit-item-btn" id="adjust${data}" onclick="adjust(${data})">
                                                <i class="ri-pen align-bottom me-2 text-muted"></i> Adjust
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

    function canAdjustInventory() {
        return isSADMIN || (
            permissions['Stock Management'] &&
            permissions['Stock Management']['Inventory'] &&
            permissions['Stock Management']['Inventory'].includes('edit')
        );
    }

    function adjust(id){
        $('#spinnerLoading').show();
        $('#siteForm')[0].reset();
        $('#siteForm').find('.is-invalid').removeClass('is-invalid');

        $.post('php/getInventory.php', {userID: id}, function(data)
        {
            var obj = parseResponse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#rawMatId').val(obj.message.raw_mat_id);
                $('#addModal').find('#rawMatCode').val(obj.message.raw_mat_code);
                $('#addModal').find('#rawMatName').val(obj.message.name);
                $('#addModal').find('#currentWeight').val(formatNumber(obj.message.raw_mat_weight));
                $('#addModal').find('#currentDrum').val(formatNumber(obj.message.raw_mat_count));
                $('#addModal').find('#newWeightBalance').val(formatNumber(obj.message.raw_mat_weight));
                $('#addModal').find('#newDrumBalance').val(formatNumber(obj.message.raw_mat_count));
                $('#addModal').modal('show');
            }
            else{
                showFailedToast(obj.message);
            }
            $('#spinnerLoading').hide();
        }).fail(function(){
            $('#spinnerLoading').hide();
            showFailedToast('Something went wrong');
        });
    }

    function readAdjustmentValue(selector) {
        var value = $(selector).val();

        if (value === null || value.trim() === '') {
            return 0;
        }

        var number = parseFloat(value);
        return isNaN(number) ? null : number;
    }

    function updateProjectedBalance() {
        var weightAdjustment = readAdjustmentValue('#weightAdjustment');
        var drumAdjustment = readAdjustmentValue('#drumAdjustment');

        if (weightAdjustment === null || drumAdjustment === null) {
            return false;
        }

        var currentWeight = parseFloat($('#currentWeight').val()) || 0;
        var currentDrum = parseFloat($('#currentDrum').val()) || 0;
        var newWeight = currentWeight + weightAdjustment;
        var newDrum = currentDrum + drumAdjustment;
        var valid = newWeight >= 0 && newDrum >= 0;

        $('#newWeightBalance').val(formatNumber(newWeight));
        $('#newDrumBalance').val(formatNumber(newDrum));
        $('#weightAdjustment').toggleClass('is-invalid', newWeight < 0);
        $('#drumAdjustment').toggleClass('is-invalid', newDrum < 0);

        return valid;
    }

    function loadAdjustmentDetails(row) {
        $.post('php/getInventoryAdjustments.php', {inventoryId: row.data().id}, function(data){
            var obj = parseResponse(data);

            if (obj.status === 'success') {
                row.child(formatAdjustmentDetails(obj.message)).show();
            } else {
                row.child(formatAdjustmentDetails([], obj.message)).show();
            }
        }).fail(function(){
            row.child(formatAdjustmentDetails([], 'Unable to load adjustments')).show();
        });
    }

    function formatAdjustmentDetails(adjustments, message) {
        if (adjustments === null) {
            return `<div class="inventory-adjustment-wrap text-muted">Loading adjustments...</div>`;
        }

        if (message) {
            return `<div class="inventory-adjustment-wrap text-danger">${escapeHtml(message)}</div>`;
        }

        if (!adjustments.length) {
            return `<div class="inventory-adjustment-wrap text-muted">No adjustments found</div>`;
        }

        var rows = adjustments.map(function(adjustment) {
            return `
                <tr>
                    <td>${escapeHtml(adjustment.created_date)}</td>
                    <td>${escapeHtml(adjustment.weight_adjustment)}</td>
                    <td>${escapeHtml(adjustment.drum_adjustment)}</td>
                    <td>${escapeHtml(adjustment.created_by)}</td>
                    <td>${escapeHtml(adjustment.remarks)}</td>
                </tr>`;
        }).join('');

        return `
            <div class="inventory-adjustment-wrap">
                <table class="table table-bordered table-striped align-middle inventory-adjustment-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Weight Adjustment (Kg)</th>
                            <th>Drum Adjustment</th>
                            <th>Action By</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    }

    function parseResponse(data) {
        try {
            return typeof data === 'object' ? data : JSON.parse(data);
        } catch (e) {
            return {status: 'failed', message: 'Invalid server response'};
        }
    }

    function formatNumber(value) {
        var number = parseFloat(value);

        if (isNaN(number)) {
            number = 0;
        }

        return parseFloat(number.toFixed(3)).toString();
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function showSuccessToast(message) {
        $("#successBtn").attr('data-toast-text', message);
        $("#successBtn").click();
    }

    function showFailedToast(message) {
        $("#failBtn").attr('data-toast-text', message || 'Something went wrong');
        $("#failBtn").click();
    }
    </script>
</body>
</html>
