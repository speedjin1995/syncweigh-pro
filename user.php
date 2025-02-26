<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
// Initialize the session
//session_start();
// Include config file
require_once "layouts/config.php";
require_once "php/db_connect.php";

// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
$name = $_SESSION["username"];

$query = "SELECT role_code, role_name from roles WHERE role_code <> 'SADMIN' AND deleted = '0'";

if($_SESSION["roles"] == 'ADMIN'){
    $query = "SELECT role_code, role_name from roles WHERE role_code <> 'SADMIN' AND role_code <> 'ADMIN' AND deleted = '0'";
}

$stmt2 = $link->prepare($query);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);
mysqli_stmt_bind_result($stmt2, $code, $name);

// Pull plants
$query4 = "SELECT id, name FROM Plant WHERE status = '0'";

$stmt4 = $link->prepare($query4);
mysqli_stmt_execute($stmt4);
mysqli_stmt_store_result($stmt4);
mysqli_stmt_bind_result($stmt4, $pcode, $pname);
?>

<head>

    <title>Users | Synctronix - Weighing System</title>
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

    <?php include 'layouts/head-css.php'; ?>

</head>

<?php include 'layouts/body.php'; ?>

<!-- Begin page -->
<div id="layout-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="h-100">
                            <!--datatable--> 
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-10">
                                                    <h5 class="card-title mb-0">User Records</h5>
                                                </div>
                                                <div class="col-2">
                                                    <button type="button" class="btn btn-md btn-soft-success" data-bs-toggle="modal" data-bs-target="#addModal"><i class="ri-add-circle-line align-middle me-1"></i>Add New User</button>              
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <table id="usersTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Employee Code</th>
                                                        <th>User Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Plant Name</th>
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
            </div> <!-- End Page-content -->

            <?php include 'layouts/footer.php'; ?>
        </div><!-- end main content-->
    </div><!-- END layout-wrapper -->

    <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
    <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form id="memberForm" class="needs-validation" novalidate autocomplete="off">
                        <div class="row col-12">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="hidden" class="form-control" id="id" name="id"> 
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="employeeCode" class="col-sm-4 col-form-label">Employee Code *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="employeeCode" name="employeeCode" placeholder="Employee Code" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                <label for="username" class="col-sm-4 col-form-label">User Name *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="username" name="username" placeholder="User Name" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                <label for="useremail" class="col-sm-4 col-form-label">User Email</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="useremail" name="useremail" placeholder="User Email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="roles" class="col-sm-4 col-form-label">Role *</label>
                                                    <div class="col-sm-8">
                                                        <select id="roles" name="roles" class="form-select">
                                                            <option select="selected" value="">Please Select</option>
                                                            <?php while(mysqli_stmt_fetch($stmt2)){ ?>
                                                                <option value="<?=$code ?>"><?=$name ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="plantId" class="col-sm-4 col-form-label">Plant</label>
                                                    <div class="col-sm-8">
                                                        <select id="plantId" name="plantId" class="form-select">
                                                            <option select="selected" value="">Please Select</option>
                                                            <?php while(mysqli_stmt_fetch($stmt4)){ ?>
                                                                <option value="<?=$pcode ?>"><?=$pname ?></option>
                                                            <?php } ?>
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
                                <button type="submit" class="btn btn-primary" id="submitMember">Submit</button>
                            </div>
                        </div><!--end col-->                                                               
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <!-- <script src="assets/js/pages/datatables.init.js"></script> -->
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

    <script>
    $(function () {
        var table = $("#usersTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url':'php/loadMembers.php'
            },
            'columns': [
                { data: 'employee_code' },
                { data: 'username' },
                { data: 'useremail' },
                { data: 'role' },
                { data: 'plant' },
                { 
                    data: 'id',
                    render: function ( data, type, row ) {
                        // return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-primary btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                        return '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item edit-item-btn" id="edit'+data+'" onclick="edit('+data+')"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
                        '<li><a class="dropdown-item remove-item-btn" id="deactivate'+data+'" onclick="deactivate('+data+')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete </a></li></ul></div>';
                    }
                }
            ]
        });
        
        $('#submitMember').on('click', function(){
            if($('#memberForm').valid()){
                $('#spinnerLoading').show();
                $.post('php/users.php', $('#memberForm').serialize(), function(data){
                    var obj = JSON.parse(data); 

                    if(obj.status === 'success'){
                        table.ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();
                    }
                    else if(obj.status === 'failed')
                    {
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Something wrong when edit');
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('#addMembers').on('click', function(){
            $('#addModal').find('#id').val("");
            $('#addModal').find('#username').val("");
            $('#addModal').find('#name').val("");
            $('#addModal').find('#userRole').val("");
            $('#addModal').find('#plantId').val("");
            $('#addModal').modal('show');
            
            $('#memberForm').validate({
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
    });

    function edit(id){
        $('#spinnerLoading').show();
        $.post('php/getUser.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#employeeCode').val(obj.message.employee_code);
                $('#addModal').find('#username').val(obj.message.username);
                $('#addModal').find('#useremail').val(obj.message.useremail);
                $('#addModal').find('#roles').val(obj.message.role_code);
                $('#addModal').find('#plantId').val(obj.message.plant);
                $('#addModal').modal('show');
                
                $('#memberForm').validate({
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
                toastr["error"](obj.message, "Failed:");
            }
            else{
                toastr["error"]("Something wrong when activate", "Failed:");
            }
            $('#spinnerLoading').hide();
        });
    }

    function deactivate(id){
        $('#spinnerLoading').show();
        $.post('php/deleteUser.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $.get('users.php', function(data) {
                    $('#mainContents').html(data);
                    $('#spinnerLoading').hide();
                });
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
    </script>

    </body>

    </html>