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
if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
    $username = implode("', '", $_SESSION["plant"]);
    $query4 = "SELECT id, name FROM Plant WHERE status = '0' and plant_code IN ('$username')";
}
else{
    $query4 = "SELECT id, name FROM Plant WHERE status = '0'";
}

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
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

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
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h5 class="card-title mb-0">User Records</h5>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <button type="button" id="downloadTemplate" class="btn btn-info waves-effect waves-light">
                                                        <i class="ri-file-pdf-line align-middle me-1"></i>
                                                        Download Template
                                                    </button>
                                                    <button type="button" id="uploadExcel" class="btn btn-success waves-effect waves-light">
                                                        <i class="ri-file-pdf-line align-middle me-1"></i>
                                                        Upload Excel
                                                    </button>
                                                    <button type="button" id="multiDeactivate" class="btn btn-warning waves-effect waves-light">
                                                        <i class="fa-solid fa-ban align-middle me-1"></i>
                                                        Delete User
                                                    </button>
                                                    <button type="button" id="addMembers" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">
                                                        <i class="ri-add-circle-line align-middle me-1"></i>
                                                        Add New User
                                                    </button>
                                                </div> 
                                            </div> 

                                            <!-- <div class="row">
                                                <div class="col-10">
                                                    <h5 class="card-title mb-0">User Records</h5>
                                                </div>
                                                <div class="col-2 d-flex justify-content-end">
                                                    <button type="button" id="addMembers" class="btn btn-md btn-soft-success" data-bs-toggle="modal" data-bs-target="#addModal">
                                                        <i class="ri-add-circle-line align-middle me-1"></i>
                                                        Add New User
                                                    </button>              
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="card-body">
                                            <table id="usersTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                                        <th>Employee Code</th>
                                                        <th>Username</th>
                                                        <th>Name</th>
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
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="employeeCode" class="col-sm-4 col-form-label">Employee Code </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="employeeCode" name="employeeCode" placeholder="Employee Code" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                <label for="username" class="col-sm-4 col-form-label">Username *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                <label for="name" class="col-sm-4 col-form-label">User Name *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="User Name" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                <label for="useremail" class="col-sm-4 col-form-label">User Email</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="useremail" name="useremail" placeholder="User Email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
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
                                            <div class="col-12 mb-3">
                                                <div class="row">
                                                    <label for="plantId" class="col-sm-4 col-form-label">Plant</label>
                                                    <div class="col-sm-8">
                                                        <select id="plantId" name="plantId[]" class="select2" multiple="multiple">
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
                                <button type="button" class="btn btn-danger" id="submitMember">Submit</button>
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
    <script src="plugins/select2/js/select2.full.min.js"></script>

    <script>
    $(function () {
        $('.select2').select2({
            allowClear: true
        });

        $('#selectAllCheckbox').on('change', function() {
            var checkboxes = $('#usersTable tbody input[type="checkbox"]');
            checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
        });
        
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
                {
                    // Add a checkbox with a unique ID for each row
                    data: 'id', // Assuming 'serialNo' is a unique identifier for each row
                    className: 'select-checkbox',
                    orderable: false,
                    render: function (data, type, row) {
                        return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                    }
                },
                { data: 'employee_code' },
                { data: 'username' },
                { data: 'name' },
                { data: 'useremail' },
                { data: 'role' },
                { data: 'plant' },
                { 
                    data: 'id',
                    render: function ( data, type, row ) {
                        // return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
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
            $('#addModal').find('#employeeCode').val("");
            $('#addModal').find('#username').val("");
            $('#addModal').find('#name').val("");
            $('#addModal').find('#useremail').val("");
            $('#addModal').find('#roles').val("");
            $('#addModal').find('#plantId').select2('destroy').val('').select2();

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

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

        $('#uploadExcel').on('click', function(){

        });

        $('#multiDeactivate').on('click', function () {
            $('#spinnerLoading').show();
            var selectedIds = []; // An array to store the selected 'id' values

            $("#usersTable tbody input[type='checkbox']").each(function () {
                if (this.checked) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                if (confirm('Are you sure you want to cancel these items?')) {
                    $.post('php/deleteUser.php', {userID: selectedIds, type: 'MULTI'}, function(data){
                        var obj = JSON.parse(data);
                        
                        if(obj.status === 'success'){
                            table.ajax.reload();
                            toastr["success"](obj.message, "Success:");
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

                $('#spinnerLoading').hide();
            } 
            else {
                // Optionally, you can display a message or take another action if no IDs are selected
                alert("Please select at least one user to delete.");
                $('#spinnerLoading').hide();
            }     
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
                $('#addModal').find('#name').val(obj.message.name);
                $('#addModal').find('#useremail').val(obj.message.useremail);
                $('#addModal').find('#roles').val(obj.message.role_code);
                $('#addModal').find("select[name='plant[]']").val(obj.message.plant).trigger('change');

                // Remove Validation Error Message
                $('#addModal .is-invalid').removeClass('is-invalid');

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