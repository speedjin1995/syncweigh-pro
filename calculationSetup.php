<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";
if (!hasModulePermission('Stock Management', 'Calculation Setup', ['view', 'create', 'edit'])){
    header('Location: no-permission.php');
    exit;
}

$user = $_SESSION['id'];
$plantId = $_SESSION['plant'];

if (hasModulePermission('Stock Management', 'Calculation Setup', ['view_all_plants'])){
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' ORDER BY name ASC");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' ORDER BY name ASC");
}else{
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username') ORDER BY name ASC");
    $plant2 = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username') ORDER BY name ASC");
}
?>

<head>

    <title>Calculation Setup | PWS - Weighing System</title>
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
                                                            <label for="typeSearch" class="form-label">Type</label>
                                                            <select id="typeSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <option value="BITULEVEL">Bitumen Level</option>
                                                                <option value="BITUSG">Bitumen SG</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="batchDrumSearch" class="form-label">Batch/Drum</label>
                                                            <select id="batchDrumSearch" class="form-select select2">
                                                                <option selected>-</option>
                                                                <option value="Batch">Batch</option>
                                                                <option value="Drum">Drum</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="plantSearch" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select select2">
                                                                <option selected>-</option>
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
                                <div class="col-xl-3 col-md-6 add-new-weight">
                                    <!-- /.modal-dialog -->
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="calculationForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="plant" class="col-sm-4 col-form-label">Plant *</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="plant" name="plant" class="form-select select2" required>
                                                                                            <?php while($rowPlantF = mysqli_fetch_assoc($plant2)){ ?>
                                                                                                <option value="<?=$rowPlantF['id'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="batchDrum" class="col-sm-4 col-form-label">Batch/Drum *</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="batchDrum" name="batchDrum" class="form-select select2" required>
                                                                                            <option value="Batch">Batch</option>
                                                                                            <option value="Drum">Drum</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                <div class="row">
                                                                                    <label for="type" class="col-sm-4 col-form-label">Type *</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="type" name="type" class="form-select select2" required>
                                                                                            <option value="BITULEVEL">Bitumen Level</option>
                                                                                            <option value="BITUSG">Bitumen SG</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3" id="bitumenLevelDiv" style="display: none;">
                                                                                <div class="card bg-light">
                                                                                    <div class="card-header">
                                                                                        <div class="d-flex justify-content-between">
                                                                                            <div>
                                                                                                <h5 class="card-title mb-0">Bitumen Level</h5>
                                                                                            </div>
                                                                                            <div class="flex-shrink-0">
                                                                                                <button type="button" class="btn btn-danger add-level"><i class="ri-add-circle-line align-middle me-1"></i>Add New</button>
                                                                                            </div> 
                                                                                        </div> 
                                                                                    </div>

                                                                                    <div class="card-body">
                                                                                        <div class="row">
                                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                                <table class="table table-primary">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Level (mm)</th>
                                                                                                            <th>Volume (m³)</th>
                                                                                                            <th>Action</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody id="levelTable"></tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-12 col-lg-12 mb-3" id="bitumenSgDiv" style="display: none;">
                                                                                <div class="card bg-light">
                                                                                    <div class="card-header">
                                                                                        <div class="d-flex justify-content-between">
                                                                                            <div>
                                                                                                <h5 class="card-title mb-0">Bitumen SG</h5>
                                                                                            </div>
                                                                                            <div class="flex-shrink-0">
                                                                                                <button type="button" class="btn btn-danger add-sg"><i class="ri-add-circle-line align-middle me-1"></i>Add New</button>
                                                                                            </div> 
                                                                                        </div> 
                                                                                    </div>

                                                                                    <div class="card-body">
                                                                                        <div class="row">
                                                                                            <div class="col-xxl-12 col-lg-12 mb-3">
                                                                                                <table class="table table-primary">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Temperature (°C)</th>
                                                                                                            <th>SG</th>
                                                                                                            <th>Action</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody id="sgTable"></tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <input type="hidden" class="form-control" id="id" name="id">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger" id="submitCalculation">Submit</button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    <div class="modal fade" id="uploadModal" style="display:none">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="uploadForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title">Upload Excel File</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <select id="uploadType" name="uploadType" class="form-select mb-3">
                                                                    <option value="LEVEL">Bitumen Level</option>
                                                                    <option value="SG">Bitumen SG</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <input type="file" id="fileInput" class="form-control mb-3">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button type="button" id="previewButton" class="btn btn-primary mb-3">Preview Data</button>
                                                            </div>
                                                        </div>
                                                        <div id="previewTable" style="overflow: auto;"></div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-danger" id="uploadButton">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="errorModal" style="display:none">
                                        <div class="modal-dialog modal-xl" style="max-width: 50%;">
                                            <div class="modal-content">
                                                <div class="modal-header bg-gray-dark color-palette">
                                                    <h4 class="modal-title">Error Log</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <ol id="errorList" class="text-danger mt-2" style="padding-left: 20px;"></ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="downloadTemplateModal" style="display:none">
                                        <div class="modal-dialog modal-xl" style="max-width: 50%;">
                                            <div class="modal-content">
                                                <div class="modal-header bg-gray-dark color-palette">
                                                    <h4 class="modal-title">Download Template</h4>

                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="downloadTemplateForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row col-12">
                                                            <div class="col-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <input type="hidden" class="form-control" id="id" name="id"> 
                                                                            <div class="col-12">
                                                                                <div class="row">
                                                                                    <label for="templateType" class="col-sm-4 col-form-label">Template Type *</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="templateType" name="templateType" class="form-select" required>
                                                                                            <option value="LEVEL">Bitumen Level</option>
                                                                                            <option value="SG">Bitumen SG</option>
                                                                                        </select>   
                                                                                    </div>
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
                                                                <button type="submit" class="btn btn-danger" id="submitDownload">Submit</button>
                                                            </div>
                                                        </div><!--end col-->                                                               
                                                    </form>
                                                </div>
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
                                                                <h5 class="card-title mb-0">Calculation Setup</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <?php if (hasModulePermission('Stock Management', 'Calculation Setup', ['download_template'])){ ?>
                                                                <button type="button" id="downloadTemplateBtn" class="btn btn-info waves-effect waves-light">
                                                                    <i class="mdi mdi-file-import-outline align-middle me-1"></i>
                                                                    Download Template 
                                                                </button>
                                                                <?php } ?>

                                                                <?php if (hasModulePermission('Stock Management', 'Calculation Setup', ['upload_excel'])){ ?>
                                                                <button type="button" id="uploadExcel" class="btn btn-warning waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Upload Excel
                                                                </button>
                                                                <?php } ?>

                                                                <!-- <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button> -->

                                                                <?php if (hasModulePermission('Stock Management', 'Calculation Setup', ['create'])){ ?>
                                                                <button type="button" id="addCalculation" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-add-circle-line align-middle me-1"></i>
                                                                    Add New
                                                                </button>
                                                                <?php } ?>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="assetTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                                    <th>Type</th>
                                                                    <th>Plant</th>
                                                                    <th>Batch/Drum</th>
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

    <script type="text/html" id="levelDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="levelNo" name="levelNo" hidden>
                <input type="text" class="form-control" id="levelId" name="levelId" hidden>
                <input type="number" class="form-control" id="levelMm" name="levelMm" style="background-color:white;" value="0">
            </td>
            <td>
                <input type="number" class="form-control" id="volume" name="volume" style="background-color:white;" value="0">
            </td>
            <td class="d-flex" style="text-align:center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/html" id="tempDetail">
        <tr class="details">
            <td>
                <input type="text" class="form-control" id="sgNo" name="sgNo" hidden>
                <input type="text" class="form-control" id="sgId" name="sgId" hidden>
                <input type="number" class="form-control" id="temperature" name="temperature" style="background-color:white;" value="0">
            </td>
            <td>
                <input type="number" class="form-control" id="sg" name="sg" style="background-color:white;" value="0">
            </td>
            <td class="d-flex" style="text-align:center">
                <button class="btn btn-danger" id="remove" style="background-color: #f06548;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/javascript">

    var table = null;
    var wasErrorModalShown = false;
    var levelCount = 0;
    var sgCount = 0;
    var permissions = <?= json_encode($_SESSION['permissions']) ?>;
    var isSADMIN = <?= json_encode($_SESSION['roles'] == 'SADMIN') ?>;

    $(function () {
        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#assetTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });

        // Initialize all Select2 elements in the search bar
        $('#collapseSearch .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
        });

        // Initialize all Select2 elements in the modal
        $('#addModal .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
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

        var typeI = $('#typeSearch').val() ? $('#typeSearch').val() : '';
        var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var batchDrumI = $('#batchDrumSearch').val() ? $('#batchDrumSearch').val() : '';

        table = $("#assetTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterCalculations.php',
                'data': {
                    type: typeI,
                    plant: plantI,
                    batchDrum: batchDrumI
                } 
            },
            'columns': [
                {
                    // Add a checkbox with a unique ID for each row
                    data: 'id',
                    className: 'select-checkbox',
                    orderable: false,
                    render: function (data, type, row) {
                        return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                    }
                },
                { data: 'type' },
                { data: 'plant' },
                { data: 'batch_drum' },
                {
                    data: 'id',
                    class: 'action-button',
                    render: function (data, type, row) {
                        var buttons = '<div class="row g-1 d-flex">';

                        if (isSADMIN || (permissions['Stock Management'] && permissions['Stock Management']['Calculation Setup'] && ['edit', 'delete'].some(p => permissions['Stock Management']['Calculation Setup'].includes(p)))) {
                            if (isSADMIN || (permissions['Stock Management'] && permissions['Stock Management']['Calculation Setup'] && permissions['Stock Management']['Calculation Setup'].includes('edit'))){
                                buttons += `
                                    <div class="col-auto">
                                        <button title="Edit" type="button" id="edit${data}" onclick="edit(${data})" class="btn btn-warning btn-sm">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                `;
                            }

                            if (isSADMIN || (permissions['Stock Management'] && permissions['Stock Management']['Calculation Setup'] && permissions['Stock Management']['Calculation Setup'].includes('delete'))){
                                buttons += `
                                    <div class="col-auto">
                                        <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data})" class="btn btn-danger btn-sm">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }

                        buttons += '</div>';
                        return buttons;
                    }
                }
            ]
        });

        $('#filterSearch').on('click', function(){
            var typeI = $('#typeSearch').val() ? $('#typeSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var batchDrumI = $('#batchDrumSearch').val() ? $('#batchDrumSearch').val() : '';

            //Destroy the old Datatable
            $("#assetTable").DataTable().clear().destroy();

            //Create new Datatable
            table = $("#assetTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'searching': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':'php/filterCalculations.php',
                    'data': {
                        type: typeI,
                        plant: plantI,
                        batchDrum: batchDrumI
                    } 
                },
                'columns': [
                    {
                        // Add a checkbox with a unique ID for each row
                        data: 'id',
                        className: 'select-checkbox',
                        orderable: false,
                        render: function (data, type, row) {
                            return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                        }
                    },
                    { data: 'type' },
                    { data: 'plant' },
                    { data: 'batch_drum' },
                    {
                        data: 'id',
                        class: 'action-button',
                        render: function (data, type, row) {
                            var buttons = '<div class="row g-1 d-flex">';

                            if (isSADMIN || (permissions['Stock Management'] && permissions['Stock Management']['Calculation Setup'] && ['edit', 'delete'].some(p => permissions['Stock Management']['Calculation Setup'].includes(p)))) {
                                if (isSADMIN || (permissions['Stock Management'] && permissions['Stock Management']['Calculation Setup'] && permissions['Stock Management']['Calculation Setup'].includes('edit'))){
                                    buttons += `
                                        <div class="col-auto">
                                            <button title="Edit" type="button" id="edit${data}" onclick="edit(${data})" class="btn btn-warning btn-sm">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    `;
                                }

                                if (isSADMIN || (permissions['Stock Management'] && permissions['Stock Management']['Calculation Setup'] && permissions['Stock Management']['Calculation Setup'].includes('delete'))){
                                    buttons += `
                                        <div class="col-auto">
                                            <button title="Delete" type="button" id="delete${data}" onclick="deactivate(${data})" class="btn btn-danger btn-sm">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    `;
                                }
                            }

                            buttons += '</div>';
                            return buttons;
                        }
                    }
                ]
            });
        });

        $('#addCalculation').on('click', function(){
            $('#addModal').find('#id').val("");
            $('#addModal').find('#type').val("").trigger('change');
            $('#addModal').find('#plant').val("").trigger('change');
            $('#addModal').find('#batchDrum').val("").trigger('change');

            // Empty level and sg tables
            $('#levelTable').empty();
            $('#sgTable').empty();
            levelCount = 0;
            sgCount = 0;

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container');
                
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            });

            $('#addModal').modal('show');
            
            $('#calculationForm').validate({
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

        $('#submitCalculation').on('click', function(){
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

            if($('#calculationForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/calculations.php', $('#calculationForm').serialize(), function(data){
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
                        alert(obj.message);
                        $("#failBtn").attr('data-toast-text', obj.message);
                        $("#failBtn").click();
                    }
                    else
                    {
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                        $("#failBtn").attr('data-toast-text', obj.message);
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('#downloadTemplateBtn').on('click', function(){
            $('#templateType').val("").trigger('change');
            $('#downloadTemplateModal').modal('show');

            $('#downloadTemplateForm').validate({
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

        $.validator.setDefaults({
            submitHandler: function () {
                if($('#downloadTemplateModal').hasClass('show')){   
                    var templateType = $('#downloadTemplateModal').find('#templateType').val();
                    
                    // Create hidden download link and click it
                    var $link = $('<a>');
                    if(templateType == 'LEVEL') {
                        $link.attr('href', 'template/Bitumen_Level_Template.xlsx');
                        $link.attr('download', 'Bitumen_Level_Template.xlsx');
                    } else if(templateType == 'SG') {
                        $link.attr('href', 'template/Bitumen_SG_Template.xlsx');
                        $link.attr('download', 'Bitumen_SG_Template.xlsx');
                    }
                    
                    $('body').append($link);
                    $link[0].click();
                    $link.remove();
                    $('#downloadTemplateModal').modal('hide');
                }
            }
        });

        $('#uploadButton').on('click', function(){
            $('#spinnerLoading').show();
            var uploadType = $('#uploadType').val();
            var formData = $('#uploadForm').serializeArray();
            var data = [];
            var rowIndex = -1;
            formData.forEach(function(field) {
            var match = field.name.match(/([a-zA-Z0-9]+)\[(\d+)\]/);
            if (match) {
                var fieldName = match[1];
                var index = parseInt(match[2], 10);
                if (index !== rowIndex) {
                rowIndex = index;
                data.push({});
                }
                data[index][fieldName] = field.value;
            }
            });

            // Send the JSON array to the server
            $.ajax({
                url: 'php/uploadCalculation.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({uploadType: uploadType, data: data}),
                success: function(response) {
                    var obj = JSON.parse(response);
                    if (obj.status === 'success') {
                        $('#spinnerLoading').hide();
                        $('#uploadModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                        window.location.reload();
                    } 
                    else if (obj.status === 'failed') {
                        $('#spinnerLoading').hide();
                        $('#uploadModal').modal('hide');
                        alert(obj.message);
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    } 
                    else if (obj.status === 'error') {
                        $('#spinnerLoading').hide();
                        $('#uploadModal').modal('hide');
                        // alert(obj.message);
                        // $("#failBtn").attr('data-toast-text', obj.message );
                        // $("#failBtn").click();
                        $('#errorModal').find('#errorList').empty();
                        var errorMessage = obj.message;
                        for (var i = 0; i < errorMessage.length; i++) {
                            $('#errorModal').find('#errorList').append(`<li>${errorMessage[i]}</li>`);                            
                        }
                        $('#errorModal').modal('show');
                    } 
                    else {
                        $('#spinnerLoading').hide();
                        alert(obj.message);
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                }
            });
        });                                                                  

        $('#uploadExcel').on('click', function(){
            $('#uploadModal').modal('show');

            $('#uploadForm').validate({
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
        
        $('#uploadModal').find('#previewButton').on('click', function(){
            var fileInput = document.getElementById('fileInput');
            var file = fileInput.files[0];
            var reader = new FileReader();
            
            reader.onload = function(e) {
                var data = e.target.result;
                // Process data and display preview
                displayPreview(data);
            };

            reader.readAsBinaryString(file);
        });

        $('#exportExcel').on('click', function(){
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = $('#statusSearch').val() ? $('#statusSearch').val() : '';
            var companyI = $('#companySearch').val() ? $('#companySearch').val() : '';
            var siteI = $('#siteSearch').val() ? $('#siteSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';

            window.open("php/exportSoPo.php?report=Sales&type=Sales&fromDate="+fromDateI+"&toDate="+toDateI+
            "&status="+statusI+"&company="+companyI+"&site="+siteI+"&plant="+plantI+
            "&customer="+customerNoI+"&product="+productI);
        });

        $('#errorModal').on('shown.bs.modal', function () {
            wasErrorModalShown = true;
        });
        
        $('#errorModal').on('hidden.bs.modal', function () {
            if (wasErrorModalShown) {
                wasErrorModalShown = false; // Reset flag
                window.location.reload();
            }
        });

        $('#type').on('change', function(){
            var selectedType = $(this).val();
            if(selectedType == 'BITULEVEL'){
                $('#bitumenLevelDiv').show();
                $('#bitumenSgDiv').hide();
            }else if (selectedType == 'BITUSG'){
                $('#bitumenLevelDiv').hide();
                $('#bitumenSgDiv').show();
            }else{
                $('#bitumenLevelDiv').hide();
                $('#bitumenSgDiv').hide();
            }
        });

        // Find and remove level table rows
        $("#levelTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();
        });

        $(".add-level").click(function(){
            var $addContents = $("#levelDetail").clone();
            $("#levelTable").append($addContents.html());

            $("#levelTable").find('.details:last').attr("id", "detail" + levelCount);
            $("#levelTable").find('.details:last').attr("data-index", levelCount);
            $("#levelTable").find('#remove:last').attr("id", "remove" + levelCount);

            $("#levelTable").find('#levelNo:last').attr('name', 'levelNo['+levelCount+']').attr("id", "levelNo" + levelCount).val(levelCount);
            $("#levelTable").find('#levelMm:last').attr('name', 'levelMm['+levelCount+']').attr("id", "levelMm" + levelCount);
            $("#levelTable").find('#volume:last').attr('name', 'volume['+levelCount+']').attr("id", "volume" + levelCount);

            levelCount++;
        });

        // Find and remove sg table rows
        $("#sgTable").on('click', 'button[id^="remove"]', function () {
            $(this).parents("tr").remove();
        });

        $(".add-sg").click(function(){
            var $addContents = $("#tempDetail").clone();
            $("#sgTable").append($addContents.html());

            $("#sgTable").find('.details:last').attr("id", "detail" + sgCount);
            $("#sgTable").find('.details:last').attr("data-index", sgCount);
            $("#sgTable").find('#remove:last').attr("id", "remove" + sgCount);

            $("#sgTable").find('#sgNo:last').attr('name', 'sgNo['+sgCount+']').attr("id", "sgNo" + sgCount).val(sgCount);
            $("#sgTable").find('#temperature:last').attr('name', 'temperature['+sgCount+']').attr("id", "temperature" + sgCount);
            $("#sgTable").find('#sg:last').attr('name', 'sg['+sgCount+']').attr("id", "sg" + sgCount);

            sgCount++;
        });
    });

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getCalculations.php', {userID: id}, function(data)
        { 
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#type').val(obj.message.type).trigger('change');
                $('#addModal').find('#plant').val(obj.message.plant_id).trigger('change');
                $('#addModal').find('#batchDrum').val(obj.message.batch_drum).trigger('change');

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

                // Remove Validation Error Message
                $('#addModal .is-invalid').removeClass('is-invalid');

                $('#addModal .select2[required]').each(function () {
                    var select2Field = $(this);
                    var select2Container = select2Field.next('.select2-container');
                    
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message
                });

                $('#levelTable').html('');
                $('#sgTable').html('');
                levelCount = 0;
                sgCount = 0;

                if (obj.message.values.length > 0){
                    for(var i = 0; i < obj.message.values.length; i++){
                        var item = obj.message.values[i];

                        if (obj.message.type == 'BITULEVEL'){
                            var $addContents = $("#levelDetail").clone();
                            $("#levelTable").append($addContents.html());

                            $("#levelTable").find('.details:last').attr("id", "detail" + levelCount);
                            $("#levelTable").find('.details:last').attr("data-index", levelCount);
                            $("#levelTable").find('#remove:last').attr("id", "remove" + levelCount);

                            $("#levelTable").find('#levelNo:last').attr('name', 'levelNo['+levelCount+']').attr("id", "levelNo" + levelCount).val(item.no);
                            $("#levelTable").find('#levelId:last').attr('name', 'levelId['+levelCount+']').attr("id", "levelId" + levelCount).val(item.id);
                            $("#levelTable").find('#levelMm:last').attr('name', 'levelMm['+levelCount+']').attr("id", "levelMm" + levelCount).val(item.level);
                            $("#levelTable").find('#volume:last').attr('name', 'volume['+levelCount+']').attr("id", "volume" + levelCount).val(item.volume);

                            levelCount++;
                        }
                        else if (obj.message.type == 'BITUSG'){
                            var $addContents = $("#tempDetail").clone();
                            $("#sgTable").append($addContents.html());

                            $("#sgTable").find('.details:last').attr("id", "detail" + sgCount);
                            $("#sgTable").find('.details:last').attr("data-index", sgCount);
                            $("#sgTable").find('#remove:last').attr("id", "remove" + sgCount);

                            $("#sgTable").find('#sgNo:last').attr('name', 'sgNo['+sgCount+']').attr("id", "sgNo" + sgCount).val(item.no);
                            $("#sgTable").find('#sgId:last').attr('name', 'sgId['+sgCount+']').attr("id", "sgId" + sgCount).val(item.id);
                            $("#sgTable").find('#temperature:last').attr('name', 'temperature['+sgCount+']').attr("id", "temperature" + sgCount).val(item.temperature);
                            $("#sgTable").find('#sg:last').attr('name', 'sg['+sgCount+']').attr("id", "sg" + sgCount).val(item.sg);

                            sgCount++;
                        }
                    }
                }


                $('#addModal').modal('show');
            
                $('#calculationForm').validate({
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
                alert(obj.message);
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                alert(obj.message);
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            $('#spinnerLoading').hide();
        });
    }

    function deactivate(id){
        if (confirm('Are you sure you want to delete this item?')) {
            $('#spinnerLoading').show();
            $.post('php/deleteCalculation.php', {userID: id}, function(data){
                var obj = JSON.parse(data);
                
                if(obj.status === 'success'){
                    table.ajax.reload();
                    $('#spinnerLoading').hide();
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                }
                else if(obj.status === 'failed'){
                    $('#spinnerLoading').hide();
                    alert(obj.message);
                    $("#failBtn").attr('data-toast-text', obj.message);
                    $("#failBtn").click();
                }
                else{
                    $('#spinnerLoading').hide();
                    alert(obj.message);
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
            });
        }
    }

    function displayPreview(data) {
        // Parse the Excel data
        var workbook = XLSX.read(data, { type: 'binary' });

        // Get the first sheet
        var sheetName = workbook.SheetNames[0];
        var sheet = workbook.Sheets[sheetName];

        // Convert the sheet to an array of objects
        var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        // Get the headers
        var headers = jsonData[0];

        // Ensure we handle cases where there may be less than 4 columns
        while (headers.length < 4) {
            headers.push(''); // Adding empty headers to reach 4 columns
        }

        // Create HTML table headers
        var htmlTable = '<table style="width:50%;"><thead><tr>';
        headers.forEach(function(header) {
            htmlTable += '<th>' + header + '</th>';
        });
        htmlTable += '</tr></thead><tbody>';

        // Iterate over the data and create table rows
        for (var i = 1; i < jsonData.length; i++) {
            htmlTable += '<tr>';
            var rowData = jsonData[i];

            // Ensure we handle cases where there may be less than 4 cells in a row
            while (rowData.length < 4) {
                rowData.push(''); // Adding empty cells to reach 4 columns
            }

            for (var j = 0; j < 4; j++) {
                var cellData = rowData[j];
                var formattedData = cellData;

                // Check if cellData is a valid Excel date serial number and format it to DD/MM/YYYY
                if (typeof cellData === 'number' && cellData > 0 && j == 0) {
                    var dateObj = XLSX.SSF.parse_date_code(cellData);
                    if (dateObj) {
                        // Format the date as DD/MM/YYYY
                        var day = String(dateObj.d).padStart(2, '0');
                        var month = String(dateObj.m).padStart(2, '0');
                        var year = dateObj.y;
                        formattedData = `${day}/${month}/${year}`;
                    }
                }

                htmlTable += '<td><input type="text" id="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+(i-1)+'" name="'+headers[j].replace(/[^a-zA-Z0-9]/g, '')+'['+(i-1)+']" value="' + (formattedData == null ? '' : formattedData) + '" /></td>';
            }
            htmlTable += '</tr>';
        }

        htmlTable += '</tbody></table>';

        var previewTable = document.getElementById('previewTable');
        previewTable.innerHTML = htmlTable;
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
                alert(obj.message);
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                alert(obj.message);
                $("#failBtn").attr('data-toast-text', "Something wrong when print");
                $("#failBtn").click();
            }
        });
    }

    </script>
</body>
</html>