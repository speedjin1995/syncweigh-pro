<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
require_once "php/db_connect.php";
$plantId = $_SESSION['plant'];
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

if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
}
else{
    $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
}
?>

<head>

    <title>Stock Used | Synctronix - Weighing System</title>
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
                                                            <label for="plantSearch" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select select2" >
                                                                <?php while($rowPlantF = mysqli_fetch_assoc($plant)){ ?>
                                                                    <option value="<?=$rowPlantF['id'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-primary" id="filterSearch"><i class="bx bx-search-alt"></i> Search</button>
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
                                                                <h5 class="card-title mb-0">Stock Used Records</h5>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <button type="button" id="exportExcel" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                                    <i class="ri-file-excel-line align-middle me-1"></i>
                                                                    Export Excel
                                                                </button>
                                                            </div> 
                                                        </div> 
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="weightTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Tonnage</th>
                                                                    <th>60/70</th>
                                                                    <th>PG76</th>
                                                                    <th>CRMB</th>
                                                                    <th>CMB</th>
                                                                    <th>LMB</th>
                                                                    <th>LEMB</th>
                                                                    <th>% Bit Usage</th>
                                                                    <th>Actual Bit Usage</th>
                                                                    <th>Bit %</th>
                                                                    <th>Plant Control Bit %</th>
                                                                    <th>Q.Dust</th>
                                                                    <th>10mm</th>
                                                                    <th>14mm</th>
                                                                    <th>20mm</th>
                                                                    <th>28mm</th>
                                                                    <th>40mm</th>
                                                                    <th>OPC</th>
                                                                    <th>Lime</th>
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

    <script type="text/javascript">
    $(function () {
        const today = new Date();
        const tomorrow = new Date(today);
        const yesterday = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        yesterday.setDate(yesterday.getDate() - 1);
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        //Date picker
        $('#fromDateSearch').flatpickr({
            dateFormat: "d-m-Y H:i",
            enableTime: true,
            time_24hr: true,
            defaultDate: startOfMonth
        });

        $('#toDateSearch').flatpickr({
            dateFormat: "d-m-Y",
            dateFormat: "d-m-Y H:i",
            enableTime: true,
            time_24hr: true,
            defaultDate: endOfMonth
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
        var plantSearchI = $('#plantSearch').val();

        var table = $("#weightTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'paging': false,
            'searching': false,
            'info': false,
            'lengthChange': false,
            'ordering': false,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/filterStockUsed.php',
                'data': {
                    fromDate: fromDateI,
                    toDate: toDateI,
                    plant: plantSearchI
                } 
            },
            'columns': [    
                { title: "Product", data: "Product" },
                { title: "Tonnage", data: "Tonnage" },
                { title: "60/70", data: "BITUMEN 60/70" },
                { title: "PG76", data: "BITUMEN PG76" },
                { title: "CRMB", data: "CRMB" },
                { title: "CMB", data: "CMB" },
                { title: "LMB", data: "LMB" },
                { title: "LEMB", data: "LEMB" },
                { title: "% bit usage", data: "% Bit Usage" },
                { title: "Actual bit usage", data: "Actual Bit Usage" },
                { title: "bit %", data: "Bit %" },
                { title: "plant control bit %", data: "Plant Control Bit %" },
                { title: "Q.Dust", data: "QUARRY DUST" },
                { title: "10mm", data: "10MM AGGREGATE" },
                { title: "14mm", data: "14MM AGGREGATE" },
                { title: "20mm", data: "20MM AGGREGATE" },
                { title: "28mm", data: "28MM AGGREGATE" },
                { title: "40mm", data: "40MM AGGREGATE" },
                { title: "OPC", data: "OPC" },
                { title: "Lime", data: "Lime" }
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
            var plantSearchI = $('#plantSearch').val();

            //Destroy the old Datatable
            $("#weightTable").DataTable().clear().destroy();

            //Create new Datatable
            table = $("#weightTable").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'paging': false,
                'searching': false,
                'info': false,
                'lengthChange': false,
                'ordering': false,
                'serverMethod': 'post',
                'ajax': {
                    'url':'php/filterStockUsed.php',
                    'data': {
                        fromDate: fromDateI,
                        toDate: toDateI,
                        plant: plantSearchI
                    } 
                },
                'columns': [
                    { title: "Product", data: "Product" },
                    { title: "Tonnage", data: "Tonnage" },
                    { title: "60/70", data: "BITUMEN 60/70" },
                    { title: "PG76", data: "BITUMEN PG76" },
                    { title: "CRMB", data: "CRMB" },
                    { title: "CMB", data: "CMB" },
                    { title: "LMB", data: "LMB" },
                    { title: "LEMB", data: "LEMB" },
                    { title: "% bit usage", data: "% Bit Usage" },
                    { title: "Actual bit usage", data: "Actual Bit Usage" },
                    { title: "bit %", data: "Bit %" },
                    { title: "plant control bit %", data: "Plant Control Bit %" },
                    { title: "Q.Dust", data: "QUARRY DUST" },
                    { title: "10mm", data: "10MM AGGREGATE" },
                    { title: "14mm", data: "14MM AGGREGATE" },
                    { title: "20mm", data: "20MM AGGREGATE" },
                    { title: "28mm", data: "28MM AGGREGATE" },
                    { title: "40mm", data: "40MM AGGREGATE" },
                    { title: "OPC", data: "OPC" },
                    { title: "Lime", data: "Lime" }
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
                
            }
        });

        // Export Excel
        $('#exportExcel').on('click', function () {
            var fromDateI = $('#fromDateSearch').val();
            var toDateI = $('#toDateSearch').val();
            var plantSearchI = $('#plantSearch').val();
            
            window.open("php/exportStockUsed.php?fromDate="+fromDateI+"&toDate="+toDateI+"&plant="+plantSearchI); 
        });
    });
    </script>
</body>
</html>