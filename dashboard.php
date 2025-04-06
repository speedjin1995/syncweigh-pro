<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

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
                        <th colspan="4" class="header" style="text-align: left;">VEHICLE PENDING IN WAREHOUSE</th>
                        <th rowspan="2" class="counter">6</th>
                    </tr><!-- Header row with title and count -->
                    <tr>
                        <th colspan="4" class="sub-header" style="text-align: left;">AT : 05/03/2025 - 10:30:35AM</th>
                    </tr><!-- Sub-header row -->
                    <tr class="table-header">
                        <td>PLATE</td>
                        <td>DATE</td>
                        <td>TIME IN</td>
                        <td>STATUS</td>
                        <td>1st WEIGHT</td>
                    </tr><!-- Table column headers -->
                </thead>
                <tbody>
                    <tr>
                        <td>PPD 8877</td>
                        <td>05/03/2025</td>
                        <td>09:30:35AM</td>
                        <td class="yellow">SALES</td>
                        <td>48500 kg</td>
                    </tr>
                        <tr class="red-row">
                        <td>BSS 4562</td>
                        <td>06/03/2025</td>
                        <td>09:00:00AM</td>
                        <td>PURCHASE</td>
                        <td>10000 kg</td>
                    </tr>
                    <tr>
                        <td>ABC 4564</td>
                        <td>07/03/2025</td>
                        <td>09:52:35AM</td>
                        <td class="yellow">PURCHASE</td>
                        <td>10000 kg</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>WMS 6545</td>
                        <td>08/03/2025</td>
                        <td>09:27:42AM</td>
                        <td class="yellow">PURCHASE</td>
                        <td>10000 kg</td>
                    </tr>
                    <tr>
                        <td>FBS 4566</td>
                        <td>09/03/2025</td>
                        <td>09:38:23AM</td>
                        <td class="yellow">PURCHASE</td>
                        <td>10000 kg</td>
                    </tr>
                    <tr>
                        <td>FBS 4567</td>
                        <td>10/03/2025</td>
                        <td>09:31:42AM</td>
                        <td class="yellow">PURCHASE</td>
                        <td>10000 kg</td>
                    </tr>
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