<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
    if (!hasModulePermission('Setting', 'Translation', ['view', 'create', 'edit'])){
        header('Location: no-permission.php');
        exit;
    }

    require_once "php/db_connect.php";
    require_once "php/requires/translation.php";
    $translations = loadTranslations($db, $_SESSION['language'] ?? 'en');
?>
<head>
    <title><?=t('translation_code')?> | PWS - Weighing System</title>
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

<div class="loading" id="spinnerLoading" style="display:none">
  <div class='mdi mdi-loading' style='transform:scale(0.79);'>
    <div></div>
  </div>
</div>

<!-- Begin page -->
<div id="layout-wrapper">

    <?php include 'layouts/menu.php'; ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="h-100">
                            <button type="button" hidden id="successBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>
                            <button type="button" hidden id="failBtn" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="center" data-toast-duration="3000" data-toast-close="close" class="btn btn-light w-xs">Top Center</button>

                            <!-- /.modal-dialog -->
                            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalScrollableTitle"><?=t('add_new_translation_code')?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form role="form" id="translationForm" class="needs-validation" novalidate autocomplete="off">
                                                <div class="row col-12">
                                                    <div class="col-xxl-12 col-lg-12">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-12 mb-3">
                                                                        <div class="row">
                                                                            <label for="messageKeyCode" class="col-sm-4 col-form-label"><?=t('message_key_code_code')?> *</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control" id="messageKeyCode" name="messageKeyCode" placeholder="e.g. stock_take_code" required>
                                                                                <div class="invalid-feedback"><?=t('fill_field_code')?></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                        <div class="row">
                                                                            <label for="en" class="col-sm-4 col-form-label"><?=t('english_code')?> *</label>
                                                                            <div class="col-sm-8">
                                                                                <textarea class="form-control" id="en" name="en" rows="2" required></textarea>
                                                                                <div class="invalid-feedback"><?=t('fill_field_code')?></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                        <div class="row">
                                                                            <label for="zh" class="col-sm-4 col-form-label"><?=t('chinese_code')?></label>
                                                                            <div class="col-sm-8">
                                                                                <textarea class="form-control" id="zh" name="zh" rows="2"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                        <div class="row">
                                                                            <label for="my" class="col-sm-4 col-form-label"><?=t('malay_code')?></label>
                                                                            <div class="col-sm-8">
                                                                                <textarea class="form-control" id="my" name="my" rows="2"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                        <div class="row">
                                                                            <label for="ne" class="col-sm-4 col-form-label"><?=t('nepali_code')?></label>
                                                                            <div class="col-sm-8">
                                                                                <textarea class="form-control" id="ne" name="ne" rows="2"></textarea>
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
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=t('close_code')?></button>
                                                        <button type="button" class="btn btn-danger" id="submitTranslation"><?=t('submit_code')?></button>
                                                    </div>
                                                </div><!--end col-->
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h5 class="card-title mb-0"><?=t('translation_code')?></h5>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <?php if(hasModulePermission('Setting', 'Translation', ['create'])): ?>
                                                    <button type="button" id="addTranslation" class="btn btn-danger waves-effect waves-light">
                                                        <i class="ri-add-circle-line align-middle me-1"></i>
                                                        <?=t('add_new_translation_code')?>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <table id="translationTable" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th><?=t('message_key_code_code')?></th>
                                                        <th><?=t('english_code')?></th>
                                                        <th><?=t('chinese_code')?></th>
                                                        <th><?=t('malay_code')?></th>
                                                        <th><?=t('nepali_code')?></th>
                                                        <th><?=t('action_code')?></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end row-->

                        </div> <!-- end .h-100-->
                    </div> <!-- end col -->
                </div>
            </div>
            <!-- End Page-content -->

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <?php include 'layouts/customizer.php'; ?>

    <?php include 'layouts/vendor-scripts.php'; ?>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/pages/notifications.init.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script type="text/javascript">

var table;
var MSG = <?= json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;
var permissions = <?= json_encode($_SESSION['permissions']) ?>;
var isSADMIN = <?= json_encode($_SESSION['roles'] == 'SADMIN') ?>;

function canTranslation(perm) {
    if (isSADMIN) {
        return true;
    }
    return !!(permissions['Setting'] && permissions['Setting']['Translation'] && permissions['Setting']['Translation'].includes(perm));
}

function escapeHtml(text) {
    return $('<div>').text(text == null ? '' : text).html();
}

$(function () {
    table = $("#translationTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 0, 'asc' ]],
        'ajax': {
            'url': 'php/modules/Translation/index.php',
            'data': { action: 'list' }
        },
        'columns': [
            { data: 'message_key_code', render: function (data) { return escapeHtml(data); } },
            { data: 'en', render: function (data) { return escapeHtml(data); } },
            { data: 'zh', render: function (data) { return escapeHtml(data); } },
            { data: 'my', render: function (data) { return escapeHtml(data); } },
            { data: 'ne', render: function (data) { return escapeHtml(data); } },
            {
                data: 'id',
                responsivePriority: 1,
                orderable: false,
                render: function ( data, type, row ) {
                    var buttons = '';
                    if (canTranslation('edit') || canTranslation('cancelled')) {
                        buttons += `
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-more-fill align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">`;

                        if (canTranslation('edit')) {
                            buttons += `
                                    <li>
                                        <a class="dropdown-item edit-item-btn" onclick="edit(${data})">
                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> ${escapeHtml(MSG.edit_code)}
                                        </a>
                                    </li>`;
                        }

                        if (canTranslation('cancelled')) {
                            buttons += `
                                    <li>
                                        <a class="dropdown-item remove-item-btn" onclick="deactivate(${data})">
                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> ${escapeHtml(MSG.delete_code)}
                                        </a>
                                    </li>`;
                        }

                        buttons += `
                                </ul>
                            </div>`;
                    }

                    return buttons;
                }
            }
        ]
    });

    $('#submitTranslation').on('click', function(){
        if($('#translationForm').valid()){
            $('#spinnerLoading').show();
            $.post('php/modules/Translation/index.php', $('#translationForm').serialize() + '&action=save', function(obj){
                $('#spinnerLoading').hide();
                if(obj.status === 'success')
                {
                    table.ajax.reload();
                    $('#addModal').modal('hide');
                    $("#successBtn").attr('data-toast-text', obj.message);
                    $("#successBtn").click();
                }
                else
                {
                    $("#failBtn").attr('data-toast-text', obj.message);
                    $("#failBtn").click();
                }
            });
        }
    });

    $('#addTranslation').on('click', function(){
        $('#translationForm')[0].reset();
        $('#addModal').find('#id').val("");
        $('#addModal').find('#messageKeyCode').prop('readonly', false);
        $('#exampleModalScrollableTitle').text(MSG.add_new_translation_code);
        $('#addModal .is-invalid').removeClass('is-invalid');
        $('#addModal').modal('show');
    });

    $('#translationForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.col-sm-8').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/modules/Translation/index.php', {action: 'get', id: id}, function(obj)
    {
        $('#spinnerLoading').hide();
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#messageKeyCode').val(obj.message.message_key_code).prop('readonly', true);
            $('#addModal').find('#en').val(obj.message.en);
            $('#addModal').find('#zh').val(obj.message.zh);
            $('#addModal').find('#my').val(obj.message.my);
            $('#addModal').find('#ne').val(obj.message.ne);
            $('#exampleModalScrollableTitle').text(MSG.edit_translation_code);
            $('#addModal .is-invalid').removeClass('is-invalid');
            $('#addModal').modal('show');
        }
        else{
            $("#failBtn").attr('data-toast-text', obj.message);
            $("#failBtn").click();
        }
    });
}

function deactivate(id){
    if (confirm(MSG.confirm_delete_translation_code)) {
        $('#spinnerLoading').show();
        $.post('php/modules/Translation/index.php', {action: 'delete', id: id}, function(obj){
            $('#spinnerLoading').hide();
            if(obj.status === 'success'){
                table.ajax.reload();
                $("#successBtn").attr('data-toast-text', obj.message);
                $("#successBtn").click();
            }
            else{
                $("#failBtn").attr('data-toast-text', obj.message);
                $("#failBtn").click();
            }
        });
    }
}

</script>
    </body>

</html>
