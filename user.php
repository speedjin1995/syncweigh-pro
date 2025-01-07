<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
// Initialize the session
//session_start();
// Include config file
require_once "layouts/config.php";

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

$employeeCode = $username = $useremail = $roles = "";
$employeeCode_err = $username_err = $useremail_err = $roles_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["employeeCode"]))) {
        $employeeCode_err = "Please enter employee code.";
    } else {
        $employeeCode = trim($_POST["employeeCode"]);
    }

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["useremail"]))) {
        $useremail_err = "Please enter email.";
    } else {
        $useremail = trim($_POST["useremail"]);
    }

    if (empty(trim($_POST["roles"]))) {
        $roles_err = "Please enter your roles.";
    } else {
        $roles = trim($_POST["roles"]);
    }

    if (empty($employeeCode_err) && empty($username_err) && empty($useremail_err) && empty($roles_err)) {
        $sql2 = "SELECT * from Users WHERE employee_code = ?";
        $action = "1";

        if ($stmt = mysqli_prepare($link, $sql2)) {
            mysqli_stmt_bind_param($stmt, "s", $param_employeeCode);
            $param_employeeCode = $employeeCode;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $sql = "UPDATE Users SET username=?, useremail=?, role=?, modified_by=? WHERE employee_code=?";
                    $action = "2";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_useremail, $param_role, $param_modified_by, $param_code);

                        // Set parameters
                        $param_code = $employeeCode;
                        $param_useremail = $useremail;
                        $param_username = $username;
                        $param_role = $roles;
                        $param_modified_by = $name;

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            echo "Updated";
                        } else {
                            echo "Something went wrong. Please try again later.";
                        }

                        // Close statement
                        mysqli_stmt_close($stmt);
                    }
                }
                else{
                    $action = "1";
                    $sql = "INSERT INTO Users (employee_code, useremail, username, password, token, role, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "ssssssss", $param_code, $param_useremail, $param_username, $param_password, $param_token, $param_role, $param_created_by, $param_modified_by);

                        // Set parameters
                        $param_code = $employeeCode;
                        $password = "123456";
                        $param_useremail = $useremail;
                        $param_username = $username;
                        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                        $param_token = bin2hex(random_bytes(50)); // generate unique token
                        $param_role = $roles;
                        $param_created_by = $name;
                        $param_modified_by = $name;

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            echo "Added";
                        } else {
                            echo "Something went wrong. Please try again later.";
                        }

                        // Close statement
                        mysqli_stmt_close($stmt);
                    }
                }

                if($action == "1"){
                    $sql3 = "INSERT INTO Users_Log (employee_code, username, user_department, status, password, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?)";

                    if ($stmt3 = mysqli_prepare($link, $sql3)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt3, "sssssss", $param_code, $param_username, $param_role, $param_status, $param_password, $param_action, $param_actionBy);

                        // Set parameters
                        $param_code = $employeeCode;
                        $param_username = $username;
                        $param_password = "123456"; // Creates a password hash
                        $param_role = $roles;
                        $param_status = "0";
                        $param_action = $action;
                        $param_actionBy = $name;

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt3)) {
                            echo "Added";
                        } else {
                            echo "Something went wrong. Please try again later.";
                        }

                        // Close statement
                        mysqli_stmt_close($stmt3);
                    }
                }
                else{

                }
            }
        }
    }
}
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
                    <form id="memberForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="row col-12">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
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
                                                        <select id="roles" name="roles" class="form-select" data-choices data-choices-sorting="true" >
                                                            <option select="selected" value="">Please Select</option>
                                                            <?php while(mysqli_stmt_fetch($stmt2)){ ?>
                                                                <option value="<?=$code ?>"><?=$name ?></option>
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
    <!--script src="assets/js/pages/datatables.init.js"></script-->
    <script>
    $(function () {
        $("#usersTable").DataTable({
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
                        $('#addModal').modal('hide');
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
                        toastr["error"]("Something wrong when edit", "Failed:");
                        $('#spinnerLoading').hide();
                    }
                });
            }
        });

        $('#addMembers').on('click', function(){
            $('#addModal').find('#id').val("");
            $('#addModal').find('#username').val("");
            $('#addModal').find('#name').val("");
            $('#addModal').find('#userRole').val("");
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
                $('#addModal').find('#employeeCode').val(obj.message.employee_code);
                $('#addModal').find('#username').val(obj.message.username);
                $('#addModal').find('#useremail').val(obj.message.useremail);
                $('#addModal').find('#roles').val(obj.message.role_code);
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