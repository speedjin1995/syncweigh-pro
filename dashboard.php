<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
require_once "php/db_connect.php";

$weighing3 = $db->query("SELECT * FROM Weight WHERE is_complete = 'N'");
$weighingList2 = array();

while($row3 = mysqli_fetch_assoc($weighing3)) {
    array_push($weighingList2, $row3);
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
    <style>
    body {
        margin: 0;
        background-color: black;
        font-family: Arial, sans-serif;
        color: white;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    th, td {
        border: 1px solid #333;
        padding: 10px;
    }

    .header {
        background-color: green;
        color: white;
        font-size: 24px;
        font-weight: bold;
    }

    .sub-header {
        background-color: green;
        color: white;
        font-size: 18px;
    }

    .counter {
        background-color: black;
        color: red;
        font-size: 48px;
        font-weight: bold;
    }

    .table-header {
        background-color: #d3e4c2;
        color: black;
        font-weight: bold;
    }

    .yellow {
        color: yellow;
    }
    
    .red-row {
        color: red;
    }
  </style>
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
            <table>
                <thead>
                    <tr>
                        <th colspan="4" class="header">VEHICLE PENDING IN WAREHOUSE</th>
                        <th rowspan="2" class="counter"><?= count($weighingList2) ?></th>
                    </tr><!-- Header row with title and count -->
                    <tr>
                        <th colspan="4" class="sub-header">AT : 05/03/2025 - 10:30:35AM</th>
                    </tr><!-- Sub-header row -->
                    <tr class="table-header">
                        <td>PLATE NO.</td>
                        <td>DATE</td>
                        <td>TIME IN</td>
                        <td>STATUS</td>
                        <td>1st WEIGHT</td>
                    </tr><!-- Table column headers -->
                </thead>
                <tbody>
                <?php foreach($weighingList2 as $row): ?>
                <?php
                    $isSales = strtolower($row['transaction_status']) === 'sales';
                    $rowClass = !$isSales ? 'red-row' : '';
                    $statusClass = $isSales ? 'yellow' : '';
                ?>
                <tr class="<?= $rowClass ?>">
                    <td><?= htmlspecialchars($row['lorry_plate_no1']) ?></td>
                    <td><?= date("d/m/Y", strtotime($row['transaction_date'])) ?></td>
                    <td><?= date("h:i:sa", strtotime($row['transaction_date'])) ?></td>
                    <td class="<?= $statusClass ?>"><?= strtoupper($row['transaction_status']) ?></td>
                    <td><?= htmlspecialchars($row['gross_weight1']) ?> kg</td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
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


    <script type="text/javascript">
    $(function () {

    });
    </script>
    </body>

    </html>