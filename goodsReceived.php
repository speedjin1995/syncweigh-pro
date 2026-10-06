<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";
if (!hasModulePermission('Accounting', 'Goods Received (GR)', ['view', 'create', 'edit'])){
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

if(hasModulePermission('Accounting', 'Goods Received (GR)', ['view_all_plants'])){
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

    <title>Goods Received | PWS - Weighing System</title>
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
                                                    <div class="col-3" id="supplierSearchDisplay">
                                                        <div class="mb-3">
                                                            <label for="supplierSearch" class="form-label">Supplier</label>
                                                            <select id="supplierSearch" class="form-select" >
                                                                <option selected>-</option>
                                                                <?php while($rowSF=mysqli_fetch_assoc($supplier2)){ ?>
                                                                    <option value="<?=$rowSF['supplier_code'] ?>"><?=$rowSF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3" id="rawMatSearchDisplay">
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
                                                            <label for="poSearch" class="form-label">PO No</label>
                                                            <select id="poSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <option value="Pending PO verification">Pending PO verification</option>
                                                                <?php while($rowPo = mysqli_fetch_assoc($purchaseOrder)){ ?>
                                                                    <option value="<?=$rowPo['po_no'] ?>"><?=$rowPo['po_no'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col--> 
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="deliveryNoSearch" class="form-label">Delivery No</label>
                                                            <input id="deliveryNoSearch" name="deliveryNoSearch" class="form-control">
                                                        </div>
                                                    </div><!--end col--> 
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="transactionIdSearch" class="form-label">Transaction ID</label>
                                                            <input id="transactionIdSearch" name="transactionIdSearch" class="form-control">
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
                                                                <h5 class="card-title mb-0">Goods Received Records</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <!-- <button type="button" id="exportPdf" class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-file-pdf-line align-middle me-1"></i>
                                                                    Export Pdf
                                                                </button> -->
                                                                <?php if(hasModulePermission('Accounting', 'Goods Received (GR)', ['export_excel'])): ?>
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                                <?php endif; ?>

                                                                <?php if(hasModulePermission('Accounting', 'Goods Received (GR)', ['post_to_sql'])): ?>
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
                                                                    <th>Supplier</th>
                                                                    <th>Plant</th>
                                                                    <th>Raw Material</th>
                                                                    <th>Purchase Order</th>
                                                                    <th>Received Date</th>
                                                                    <th>Total Received <br> Amount (KG)</th>
                                                                    <!-- <th>Action</th> -->
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
                    <?php include 'component/WeighingModal/modal.php'; ?>
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

    <?php include 'component/WeighingModal/script.php'; ?>

    <script type="text/javascript">
    var userRole = '<?=$_SESSION["roles"] ?>';
    var table = null;
    var permissions = <?= json_encode($_SESSION['permissions']) ?>;
    var isSADMIN = <?= json_encode($_SESSION['roles'] == 'SADMIN') ?>;

    $(function () {
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);

        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y H:i:S",
            enableTime: true,
            time_24hr: true,
            defaultDate: yesterday
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            dateFormat: "d-m-Y H:i:S",
            enableTime: true,
            time_24hr: true,
            defaultDate: today
        });

        $('.select2').not('#addModal .select2').each(function() {
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

        loadWeightTable();

        $('#filterSearch').on('click', function(){
            loadWeightTable();
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
                $.post('php/getWeight.php', { userID: row.data().id, fromDate: fromDateI, toDate: toDateI, format: 'EXPANDABLE', acctType: 'GR' }, function (data) {
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
            $('#spinnerLoading').show();
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = 'Purchase';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var poI = $('#poSearch').val() ? $('#poSearch').val() : '';
            var selectedIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                if (confirm('Are you sure you want to post to SQL these items?')) {
                    $.post('php/postGr.php', {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        supplier: supplierNoI,
                        rawMat: rawMatI,
                        plant: plantI,
                        purchaseOrder: poI,
                        userID: selectedIds, 
                        type: 'MULTI'
                    }, function(data){
                        var obj = JSON.parse(data);
                        
                        if(obj.status === 'success'){
                            toastr["success"](obj.message, "Success:");
                            $('#weightTable').DataTable().ajax.reload(null, false);
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

                //$('#spinnerLoading').hide();
            } 
            else {
                if (confirm('Are you sure you want to post to SQL?')) {
                    $.post('php/postGr.php', {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        status: statusI,
                        supplier: supplierNoI,
                        rawMat: rawMatI,
                        plant: plantI,
                        purchaseOrder: poI,
                        type: 'ALL'
                    }, function(data){
                        var obj = JSON.parse(data);
                        
                        if(obj.status === 'success'){
                            toastr["success"](obj.message, "Success:");
                            $('#weightTable').DataTable().ajax.reload(null, false);
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

                //$('#spinnerLoading').hide();
            }     
        });

        // Export Excel
        $('#exportExcel').on('click', function () {
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var statusI = 'Purchase';
            var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
            var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
            var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
            var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
            var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
            var poI = $('#poSearch').val() ? $('#poSearch').val() : '';
            var selectedIds = []; // An array to store the selected 'id' values

            $("#weightTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                window.open("php/exportDoGr.php?type=gr&isMulti=Y&fromDate="+fromDateI+"&toDate="+toDateI+
                "&status="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&product="+productI+
                "&rawMaterial="+rawMatI+"&plant="+plantI+"&purchaseOrder="+poI+"&id="+selectedIds);
            } 
            else {
                window.open("php/exportDoGr.php?type=gr&isMulti=N&fromDate="+fromDateI+"&toDate="+toDateI+
                "&status="+statusI+"&customer="+customerNoI+"&supplier="+supplierNoI+"&product="+productI+
                "&rawMaterial="+rawMatI+"&plant="+plantI+"&purchaseOrder="+poI);
            }     
        });
    });

    // Load (or reload) the weighing table using the current search filters
    function loadWeightTable() {
        var fromDateI = $('#fromDateSearch').val();
        var toDateI = $('#toDateSearch').val();
        var statusI = 'Purchase';
        var customerNoI = $('#customerNoSearch').val() ? $('#customerNoSearch').val() : '';
        var supplierNoI = $('#supplierSearch').val() ? $('#supplierSearch').val() : '';
        var productI = $('#productSearch').val() ? $('#productSearch').val() : '';
        var rawMatI = $('#rawMatSearch').val() ? $('#rawMatSearch').val() : '';
        var plantI = $('#plantSearch').val() ? $('#plantSearch').val() : '';
        var poI = $('#poSearch').val() ? $('#poSearch').val() : '';
        var deliveryNoI = $('#deliveryNoSearch').val() ? $('#deliveryNoSearch').val() : '';
        var transactionIdI = $('#transactionIdSearch').val() ? $('#transactionIdSearch').val() : '';

        // Destroy the old Datatable
        if ($.fn.DataTable.isDataTable('#weightTable')) {
            $("#weightTable").DataTable().clear().destroy();
        }

        table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': false,
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
                    purchaseOrder: poI,
                    deliveryNo: deliveryNoI,
                    transactionId: transactionIdI,
                    type: 'GR'
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
                { data: 'supplier_name' },
                { data: 'plant_name' },
                { data: 'product_name' },
                { data: 'purchase_order' },
                { data: 'tare_weight1_date' },
                { data: 'po_supply_weight' },
                // { 
                //     data: 'id',
                //     class: 'action-button',
                //     render: function ( data, type, row ) {
                //         // return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                //         return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                //         '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                //         '<li><a class="dropdown-item print-item-btn" id="print'+data+'" onclick="print('+data+')"><i class="ri-printer-fill align-bottom me-2 text-muted"></i> Print</a></li></ul></div>';
                //     }
                // }
            ],
            "drawCallback": function(settings) {
                $('#salesInfo').text(settings.json.salesTotal);
                $('#purchaseInfo').text(settings.json.purchaseTotal);
                $('#localInfo').text(settings.json.localTotal);
            }   
        });
    }

    function format(row) {
        var returnString = `
        <!-- Weighing Section -->
        <div class="row">
            <p><span><strong style="font-size:120%; text-decoration: underline;">Goods Received Information</strong></span><br>
            <div class="col-4">
                <p><strong>TOTAL RECEIVED AMOUNT:</strong> ${parseFloat(row.po_supply_weight)/1000} MT</p>
            </div>`;

            if (isSADMIN || (permissions['Accounting'] && permissions['Accounting']['Goods Received (GR)'] && permissions['Accounting']['Goods Received (GR)'].includes('include_price'))) {
                returnString += `
                    <div class="col-4">
                        <p><strong>UNIT PRICE:</strong> RM ${row.weights[0].unit_price}</p>
                    </div>
                    <div class="col-4">
                        <p><strong>TOTAL PRICE:</strong> RM ${parseFloat(parseFloat(row.weights[0].unit_price) * (parseFloat(row.po_supply_weight)/1000)).toFixed(2)}</p>
                    </div>
                `;
            }

            returnString += `
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
                        if (isSADMIN || (permissions['Weighing'] && permissions['Weighing']['Purchase'] && permissions['Weighing']['Purchase'].includes('edit'))) {
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
                            if (isSADMIN || (permissions['Weighing'] && permissions['Weighing']['Purchase'] && permissions['Weighing']['Purchase'].includes('edit'))) {
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
    </script>
</body>
</html>