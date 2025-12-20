<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
    require_once "php/db_connect.php";

    if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
        $username = implode("', '", $_SESSION["plant"]);
        $plant = $db->query("SELECT * FROM Plant WHERE status = '0' and plant_code IN ('$username')");
    }
    else{
        $plant = $db->query("SELECT * FROM Plant WHERE status = '0'");
    }
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
                                                    <!-- <div class="col-sm-auto">
                                                        <div class="input-group">
                                                            <input type="text"
                                                                class="form-control border-0 dash-filter-picker shadow"
                                                                data-provider="flatpickr" data-range-date="true"
                                                                data-date-format="d M, Y"
                                                                data-deafult-date="01 Jan 2023 to 31 Jan 2023">
                                                            <div
                                                                class="input-group-text bg-primary border-primary text-white">
                                                                <i class="ri-calendar-2-line"></i>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                    <!--end col-->
                                                    <!--div class="col-auto">
                                                        <button type="button" class="btn btn-soft-success"><i
                                                                class="ri-add-circle-line align-middle me-1"></i>
                                                            Add Product</button>
                                                    </div>
                                                    <!--end col-->
                                                    <!--div class="col-auto">
                                                        <button type="button"
                                                            class="btn btn-soft-info btn-icon waves-effect waves-light layout-rightside-btn"><i
                                                                class="ri-pulse-line"></i></button>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </form>
                                        </div>
                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <div class="col-xxl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header fs-5" href="#collapseOne" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="mdi mdi-chevron-down pull-right"></i>
                                        Search Records
                                    </div>
                                    <div id="collapseOne" aria-labelledby="collapseOne">                                    
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
                                                            <label for="transactionStatusSearch" class="form-label">Transaction Status</label>
                                                            <select id="transactionStatusSearch" class="form-select select2" data-choices data-choices-sorting="true" >
                                                                <option value="Sales" selected>Sales</option>
                                                                <option value="Purchase">Purchase</option>
                                                                <?php 
                                                                    if($role == 'SADMIN' || $role == 'ADMIN' || $role == 'MANAGER'){ 
                                                                        echo '<option value="Local">Public</option>';
                                                                    }
                                                                ?>                                                                                     
                                                                <option value="WIP">WIP</option>
                                                                <option value="Return">Return</option>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="plantSearch" class="form-label">Plant</label>
                                                            <select id="plantSearch" class="form-select select2" >
                                                                <option selected>-</option>
                                                                <?php while($rowPlantF=mysqli_fetch_assoc($plant)){ ?>
                                                                    <option value="<?=$rowPlantF['plant_code'] ?>"><?=$rowPlantF['name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-danger" id="searchFilter">
                                                                <i class="bx bx-search-alt"></i>
                                                                Search
                                                            </button>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end row-->
                                            </form>                                                                        
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--datatable--> 
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h5 class="card-title mb-0">Dashboard Summary</h5>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <!-- <button type="button" class="btn btn-danger waves-effect waves-light" id="excelSearch">
                                                    <i class="mdi mdi-file-excel-outline"></i>
                                                    Export Excel
                                                    </button> -->
                                                    <button type="button" class="btn btn-warning waves-effect waves-light" id="exportDashboard">
                                                    <i class="mdi mdi-content-copy"></i>
                                                    Copy
                                                    </button>
                                                </div> 
                                            </div>                                            
                                        </div>                                      
                                        <div class="card-body">                                              
                                            <table id="dashboard-summary" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2" class="text-center">Status</th>
                                                        <th colspan="2" class="text-center">Batch</th>
                                                        <th colspan="2" class="text-center">Drum</th>
                                                        <th colspan="2" class="text-center">Total</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">No.</th>
                                                        <th class="text-center">MT</th>
                                                        <th class="text-center">No.</th>
                                                        <th class="text-center">MT</th>
                                                        <th class="text-center">No.</th>
                                                        <th class="text-center">MT</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="dashboard-tbody">
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end row-->

                            <!-- Doughnut Chart -->
                            <div class="row" id="doughnutChartRow" style="display: none;">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Distribution Charts</h5>
                                        </div>                             
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <h6 class="text-center mb-3">Product Distribution (MT)</h6>
                                                    <div id="productDoughnutChart" style="height: 400px; display: flex; justify-content: center; align-items: center;">
                                                        <canvas id="productCanvas"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <h6 class="text-center mb-3">Customer Distribution (MT)</h6>
                                                    <div id="customerDoughnutChart" style="height: 400px; display: flex; justify-content: center; align-items: center;">
                                                        <canvas id="customerCanvas"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end row-->
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
    <script src="assets/js/pages/form-validation.init.js"></script>
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
    <!-- Chart.js -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- Additional js -->
    <script src="assets/js/additional.js"></script>

    <script type="text/javascript">
        var dashboardTable = null;
        var productChart = null;
        var customerChart = null;
        $(function () {
            const today = new Date();
            const tomorrow = new Date(today);
            const yesterday = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            yesterday.setDate(yesterday.getDate() - 1);

            // Initialize all Select2 elements in the modal
            $('.select2').select2({
                allowClear: true,
                placeholder: "Please Select",
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

            $("#fromDateSearch").flatpickr({
                dateFormat: "d-m-Y H:i",
                enableTime: true,
                time_24hr: true,
                defaultDate: today
            });

            $('#toDateSearch').flatpickr({
                dateFormat: "d-m-Y H:i",
                enableTime: true,
                time_24hr: true,
                defaultDate: today
            });

            // Initialize DataTable
            var fromDate = $('#fromDateSearch').val();
            var toDate = $('#toDateSearch').val();
            var transactionStatus = $('#transactionStatusSearch').val();
            var plant = $('#plantSearch').val();

            fetchData(fromDate, toDate, transactionStatus, plant);
            
            $('#searchFilter').on('click', function() {
                var fromDate = $('#fromDateSearch').val();
                var toDate = $('#toDateSearch').val();
                var transactionStatus = $('#transactionStatusSearch').val();
                var plant = $('#plantSearch').val();

                // Validate date range
                if (parseDate(fromDate) > parseDate(toDate)) {
                    alert('From Date cannot be later than To Date.');
                    return;
                }

                // Call function to fetch and display data based on filters
                fetchData(fromDate, toDate, transactionStatus, plant);
            });

            // Add click event for table cells
            $('#dashboard-summary tbody').on('click', 'td', function() {
                var columnIndex = $(this).index();
                var rowData = dashboardTable.row($(this).parent()).data();
                var status = rowData.status;
                
                if (columnIndex == 1 || columnIndex == 2) { // Batch columns
                    showPieChart(status, 'Batch');
                } else if (columnIndex == 3 || columnIndex == 4) { // Drum columns
                    showPieChart(status, 'Drum');
                }
            });
            
            // Export dashboard report
            $('#exportDashboard').on('click', function() {
                var fromDate = $('#fromDateSearch').val();
                var toDate = $('#toDateSearch').val();
                var transactionStatus = $('#transactionStatusSearch').val();
                var plant = $('#plantSearch').val();
                
                $.post('php/exportDashboardReport.php', {
                    fromDate: fromDate,
                    toDate: toDate,
                    transactionStatus: transactionStatus,
                    plant: plant
                }, function(response) {
                    var obj = JSON.parse(response);
                    if(obj.status === 'success') {
                        navigator.clipboard.writeText(obj.message).then(function() {
                            alert('Report copied to clipboard!');
                        });
                    }
                });
            });
        });

        // Convert d-m-Y format to Date objects
        function parseDate(dateStr) {
            var parts = dateStr.split('-');
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }

        function fetchData(fromDate, toDate, transactionStatus, plant) {
            // Destroy existing DataTable if it exists
            if (dashboardTable) {
                dashboardTable.destroy();
            }
            
            dashboardTable = $("#dashboard-summary").DataTable({
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'serverSide': true,
                'searching': false,
                'paging': false,
                'info': false,
                'lengthChange': false,
                'ordering': false,
                'serverMethod': 'post',
                'ajax': {
                    'url':'php/filterDashboard.php',
                    'data': {
                        fromDate: fromDate,
                        toDate: toDate,
                        transactionStatus: transactionStatus,
                        plant: plant
                    } 
                },
                'columns': [
                    { data: 'status' },
                    { data: 'batch_no' },
                    { data: 'batch_mt' },
                    { data: 'drum_no' },
                    { data: 'drum_mt' },
                    { data: 'total_no' },
                    { data: 'total_mt' }
                ],
                'columnDefs': [
                    { className: 'text-center clickable-cell', targets: '_all' }
                ]
            });
        }
                
        function showPieChart(status, type) {
            var fromDate = $('#fromDateSearch').val();
            var toDate = $('#toDateSearch').val();
            var transactionStatus = $('#transactionStatusSearch').val();
            var plant = $('#plantSearch').val();

            $.post('php/getChartData.php', {
                fromDate: fromDate,
                toDate: toDate,
                transactionStatus: transactionStatus,
                plant: plant,
                status: status,
                type: type
            }, function(data) {
                var obj = JSON.parse(data);
                if(obj.status === 'success') {
                    renderDoughnutCharts(obj.productData, obj.customerData);
                }
            });

        }
                
        function renderDoughnutCharts(productData, customerData) {
            // Destroy existing charts if they exist
            if (productChart) {
                productChart.destroy();
            }
            if (customerChart) {
                customerChart.destroy();
            }
            
            // Product Doughnut Chart
            var backgroundColors = generateColors(productData.values.length);

            productChart = new Chart($('#productCanvas'), {
                type: 'doughnut',
                data: {
                    labels: productData.labels,
                    datasets: [{
                        data: productData.values,
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var total = data.datasets[tooltipItem.datasetIndex].data.reduce((a, b) => a + b, 0);
                                var percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value.toLocaleString() + ' MT (' + percentage + '%)';
                            }
                        }
                    },
                    onClick: function (evt, elements) {
                        if (elements.length > 0) {
                            var element = elements[0];

                            var datasetIndex = element._datasetIndex;
                            var index = element._index;

                            var productName = this.data.labels[index];
                            var productCode = productData.codes[index];
                            var fromDate = $('#fromDateSearch').val();
                            var toDate = $('#toDateSearch').val();
                            var transactionStatus = $('#transactionStatusSearch').val();
                            var plant = $('#plantSearch').val(); 
                            var status = productData.status;
                            var type = productData.type;

                            var postData = {
                                fromDate: fromDate,
                                toDate: toDate,
                                status: transactionStatus,
                                plant: plant,
                                file: 'weight',
                                reportType: 'S&P',
                                currentStatus: status,
                                batchDrum: type,
                                isDashboard: 'Y'
                            };
                            
                            if (transactionStatus == 'Purchase') {
                                postData.rawMat = productCode;
                            } else {
                                postData.product = productCode;
                            }
                            
                            $.post('php/exportPdf.php', postData, function(response){
                                var obj = JSON.parse(response);

                                if(obj.status === 'success'){
                                    var previewWindow = window.open('', '_blank');
                                    previewWindow.document.write(obj.message);
                                    previewWindow.document.close();
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
                }
            });
            
            // Customer Doughnut Chart
            var backgroundColors = generateColors(customerData.values.length);

            customerChart = new Chart($('#customerCanvas'), {
                type: 'doughnut',
                data: {
                    labels: customerData.labels,
                    datasets: [{
                        data: customerData.values,
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var total = data.datasets[tooltipItem.datasetIndex].data.reduce((a, b) => a + b, 0);
                                var percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value.toLocaleString() + ' MT (' + percentage + '%)';
                            }
                        }
                    },
                    onClick: function (evt, elements) {
                        if (elements.length > 0) {
                            var element = elements[0];

                            var datasetIndex = element._datasetIndex;
                            var index = element._index;

                            var customerName = this.data.labels[index];
                            var customerCode = customerData.codes[index];
                            var fromDate = $('#fromDateSearch').val();
                            var toDate = $('#toDateSearch').val();
                            var transactionStatus = $('#transactionStatusSearch').val();
                            var plant = $('#plantSearch').val(); 
                            var status = customerData.status;
                            var type = customerData.type;

                            var postData = {
                                fromDate: fromDate,
                                toDate: toDate,
                                status: transactionStatus,
                                plant: plant,
                                file: 'weight',
                                reportType: 'S&PC',
                                currentStatus: status,
                                batchDrum: type,
                                isDashboard: 'Y'
                            };
                            
                            if (transactionStatus == 'Purchase') {
                                postData.supplier = customerCode;
                            } else {
                                postData.customer = customerCode;
                            }
                            
                            $.post('php/exportPdf.php', postData, function(response){
                                var obj = JSON.parse(response);

                                if(obj.status === 'success'){
                                    var previewWindow = window.open('', '_blank');
                                    previewWindow.document.write(obj.message);
                                    previewWindow.document.close();
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
                },
            });

            $('#doughnutChartRow').show();
        }

        function generateColors(count) {
            const colors = [];
            for (var i = 0; i < count; i++) {
                const hue = Math.floor((360 / count) * i);
                colors.push(`hsl(${hue}, 70%, 60%)`);
            }
            return colors;
        }
    </script>
    </body>

    </html>