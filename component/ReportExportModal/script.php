<?php
/*
 * Report Export Modal JavaScript (for component/ReportExportModal/modal.php).
 * Include after layouts/vendor-scripts.php, before the page script.
 *
 * Page buttons call:
 *   openReportExportPdf()    PDF export (#exportPdfModal)
 *   openReportExportGroup()  grouped SO/PO report (#exportGroupRepModal)
 */
?>
    <script type="text/javascript">
    // Copy the current search filters (and any ticked rows) into an export form
    function fillReportExportForm(form) {
        var selectedIds = [];
        $("#weightTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            form.find('#id').val(selectedIds);
            form.find('#isMulti').val('Y');
        }else{
            form.find('#id').val('');
            form.find('#isMulti').val('N');
        }

        form.find('#fromDate').val($('#fromDateSearch').val());
        form.find('#toDate').val($('#toDateSearch').val());
        form.find('#status').val($('#statusSearch').val() ? $('#statusSearch').val() : '');
        form.find('#customer').val($('#customerNoSearch').val() ? $('#customerNoSearch').val() : '');
        form.find('#supplier').val($('#supplierSearch').val() ? $('#supplierSearch').val() : '');
        form.find('#vehicle').val($('#vehicleNo').val() ? $('#vehicleNo').val() : '');
        form.find('#customerType').val($('#customerTypeSearch').val() ? $('#customerTypeSearch').val() : '');
        form.find('#product').val($('#productSearch').val() ? $('#productSearch').val() : '');
        form.find('#rawMat').val($('#rawMatSearch').val() ? $('#rawMatSearch').val() : '');
        form.find('#destination').val($('#destinationSearch').val() ? $('#destinationSearch').val() : '');
        form.find('#plant').val($('#plantSearch').val() ? $('#plantSearch').val() : '');
        form.find('#batchDrum').val($('#batchDrumSearch').val() ? $('#batchDrumSearch').val() : '');
        form.find('#soNo').val($('#soSearch').val() ? $('#soSearch').val() : '');
    }

    // Post an export form and open the generated report in a new tab
    function previewReportExport(url, form) {
        $.post(url, form.serialize(), function(response){
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

    function validateReportExportForm(form) {
        form.validate({
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

    function openReportExportPdf() {
        $("#exportPdfModal").find('#reportType').val('');
        $("#exportPdfModal").modal("show");
        validateReportExportForm($('#exportPdfForm'));
    }

    function openReportExportGroup() {
        $("#exportGroupRepModal").find('select[id^="group"]').val('');
        $("#exportGroupRepModal").find('select[id^="group"] option').prop('disabled', false);
        $("#exportGroupRepModal").modal("show");
        validateReportExportForm($('#exportGroupRepForm'));
    }

    // Disable a group option once it is picked in another group
    function updateReportExportGroups() {
        var groupSelects = $('#exportGroupRepModal').find('select[id^="group"]');
        var selectedValues = groupSelects.map(function () { return $(this).val(); }).get();

        groupSelects.each(function () {
            var currentValue = $(this).val();

            $(this).find('option').each(function () {
                var optionValue = $(this).val();

                if (optionValue === '') return; // Skip blank option

                $(this).prop('disabled', selectedValues.includes(optionValue) && optionValue !== currentValue);
            });
        });
    }

    $(function () {
        $.validator.setDefaults({
            submitHandler: function () {
                if($('#exportPdfModal').hasClass('show')){
                    fillReportExportForm($('#exportPdfForm'));
                    $('#exportPdfModal').modal('hide');
                    previewReportExport('php/exportPdf.php', $('#exportPdfForm'));
                }
                else if($('#exportGroupRepModal').hasClass('show')){
                    var group1 = $('#exportGroupRepModal').find('#group1').val();
                    var group2 = $('#exportGroupRepModal').find('#group2').val();
                    var group3 = $('#exportGroupRepModal').find('#group3').val();
                    var group4 = $('#exportGroupRepModal').find('#group4').val();

                    // Added checking to ensure previous group is selected
                    if (group2 && !group1) {
                        alert("Please select Group 1 before selecting Group 2.");
                        return;
                    }
                    if (group3 && (!group1 || !group2)) {
                        alert("Please select Group 1 and Group 2 before selecting Group 3.");
                        return;
                    }
                    if (group4 && (!group1 || !group2 || !group3)) {
                        alert("Please select Group 1, Group 2, and Group 3 before selecting Group 4.");
                        return;
                    }

                    fillReportExportForm($('#exportGroupRepForm'));
                    $('#exportGroupRepModal').modal('hide');
                    previewReportExport('php/exportSoPoReport.php', $('#exportGroupRepForm'));
                }
            }
        });

        $('#exportGroupRepModal').find('select[id^="group"]').on('change', function () {
            updateReportExportGroups();
        });
    });
    </script>
