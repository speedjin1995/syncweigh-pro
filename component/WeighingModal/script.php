<?php
/*
 * Weighing Modal JavaScript (for component/WeighingModal/modal.php).
 * Include after layouts/vendor-scripts.php and assets/js/additional.js, before the page script.
 *
 * Expects from the page:
 * - PHP: $plantName, hasPermission() (php/requires/permissions.php)
 * - JS globals: table (DataTable), permissions, isSADMIN
 * After saving, the browser returns to the current page (weighingModalPage).
 *
 * Opens with #addWeight (new) or edit(id) (existing weighing).
 */
?>
    <script type="text/javascript">
    // Page to return to after saving
    var weighingModalPage = '<?= basename($_SERVER['PHP_SELF']) ?>';
    let soPoTag = false;
    let addNewTag = false;
    let isSyncing = false;
    let isEdit = false;
    let editIsComplete = false;
    let closedSalesOrderSubmissionBlocked = false;
    let salesOption = $('#salesOrder option').clone();
    let purchaseOption = $('#purchaseOrder option').clone();
    let transporterOption = $('#transporter option').clone();
    var rawMaterialOption = $('#rawMaterialName option').clone();
    var supplierOption = $('#supplierName option').clone();
    var customerOption = $('#customerName option').clone();
    var productOption = $('#productName option').clone();
    var transactionStatusOption = $('#transactionStatus option').clone();
    var grossIncomingDatePicker;
    var tareOutgoingDatePicker; 
    var grossIncomingDatePicker2;
    var tareOutgoingDatePicker2; 

    $(function () {
        const today = getMalaysiaDate();

        grossIncomingDatePicker = $('#grossIncomingDate').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d/m/Y H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
            clickOpens: <?= hasPermission('Weighing', ['manual_date_change']) ? 'true' : 'false' ?>,
            onReady: function(selectedDates, dateStr, instance) {
                <?php if (!hasPermission('Weighing', ['manual_date_change'])): ?>
                    instance._input.setAttribute('readonly', true);
                    instance.close();
                <?php endif; ?>
            }
        });

        tareOutgoingDatePicker = $('#tareOutgoingDate').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d/m/Y H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
            clickOpens: <?= hasPermission('Weighing', ['manual_date_change']) ? 'true' : 'false' ?>,
            onReady: function(selectedDates, dateStr, instance) {
                <?php if (!hasPermission('Weighing', ['manual_date_change'])): ?>
                    instance._input.setAttribute('readonly', true);
                    instance.close();
                <?php endif; ?>
            }
        });

        grossIncomingDatePicker2 = $('#grossIncomingDate2').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d/m/Y H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
            clickOpens: <?= hasPermission('Weighing', ['manual_date_change']) ? 'true' : 'false' ?>,
            onReady: function(selectedDates, dateStr, instance) {
                <?php if (!hasPermission('Weighing', ['manual_date_change'])): ?>
                    instance._input.setAttribute('readonly', true);
                    instance.close();
                <?php endif; ?>
            }
        });

        tareOutgoingDatePicker2 = $('#tareOutgoingDate2').flatpickr({
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d/m/Y H:i:S",
            altInput: true,
            altFormat: "d/m/Y H:i:S K",
            allowInput: true,
            clickOpens: <?= hasPermission('Weighing', ['manual_date_change']) ? 'true' : 'false' ?>,
            onReady: function(selectedDates, dateStr, instance) {
                <?php if (!hasPermission('Weighing', ['manual_date_change'])): ?>
                    instance._input.setAttribute('readonly', true);
                    instance.close();
                <?php endif; ?>
            }
        });

        // Initialize all Select2 elements in the modal
        $('#addModal .select2').select2({
            allowClear: true,
            placeholder: "Please Select",
            dropdownParent: $('#addModal') // Ensures dropdown is not cut off
        });

        // Apply custom styling to Select2 elements in addModal
        $('#addModal .select2-container .select2-selection--single').css({
            'padding-top': '4px',
            'padding-bottom': '4px',
            'height': 'auto'
        });

        $('#addModal .select2-container .select2-selection__arrow').css({
            'padding-top': '33px',
            'height': 'auto'
        });

        $('#transactionDate').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: today
        });

        $('#submitWeight').on('click', function(){
            if (isClosedSalesOrderSubmissionBlocked()) {
                return;
            }

            // Check weight
            var trueWeight = 0;
            var variance = $('#productVariance').val() || '';
            var high = $('#productHigh').val() || '';
            var low = $('#productLow').val() || '';
            var final = $('#finalWeight').val() || '0';
            var completed = 'N';
            var pass = true;

            if($('#transactionStatus').val() == "Purchase"){
                trueWeight = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#addModal').find('#orderWeight').val());
            }

            if($('#weightType').val() == 'Normal' && ($('#grossIncoming').val() && $('#tareOutgoing').val())){
                isComplete = 'Y';
            }
            else if($('#weightType').val() == 'Container' && ($('#grossIncoming').val() && $('#tareOutgoing').val() && $('#grossIncoming2').val() && $('#tareOutgoing2').val())){
                isComplete = 'Y';
            }
            else{
                isComplete = 'N';
            }

            if (isComplete == 'Y' && variance != '') {
                final = parseFloat(final);
                low = low != '' ? parseFloat(low) : null;
                high = high != '' ? parseFloat(high) : null;
                
                if (variance == 'W') {
                    if (low !== null && (final < trueWeight - low)) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight + high)) {
                        pass = false;
                    }
                } 
                else if (variance == 'P') {
                    if (low !== null && (final < trueWeight * (1 - low / 100))) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight * (1 + high / 100))) {
                        pass = false;
                    }
                }
            }

            pass = true;

            // custom validation for select2
            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
                var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";

                // Check if the value is empty
                if (select2Field.val() === "" || select2Field.val() === null) {
                    select2Container.find('.select2-selection').css('border', '1px solid red'); // Add red border

                    // Add error message if not already present
                    if (select2Container.next('.select2-error').length === 0) {
                        select2Container.after(errorMsg);
                    }

                    pass = false;
                } else {
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message

                    pass = true;
                }
            });

            if ($('#customerType').val() == 'Cash' && pass == true) {
                var unitPrice = parseFloat($('#addModal').find('#unitPrice').val());

                if (!unitPrice || unitPrice <= 0) {
                    showAppAlert('Unit price must be more than 0.');
                    return;
                } else {
                    var productId = $('#addModal').find('#productId').val();
                    $.post('php/getProduct.php', { userID: productId }, function (data) {
                        try {
                            var obj = JSON.parse(data);
                            if (obj.status === 'success') {
                                var price = obj.message.price;
                                //if (unitPrice < price) {
                                    //showAppAlert('Unit price doesn\'t meet the minimum value of RM ' + price);
                                    //return;
                                //} else {
                                    // Price validation passed, submit the form
                                    requireEditReason(function(){ requireManualWeightReason(submitWeightForm); });
                                //}
                            } else {
                                showAppAlert('Error validating product price');
                            }
                        } catch (e) {
                            showAppAlert('Error processing product validation response');
                        }
                    }).fail(function() {
                        showAppAlert('Error connecting to server for price validation');
                    });
                    return; // Exit here to prevent immediate form submission
                }
            }
            else if($('#customerType').val() == 'Normal' && pass == true){
                var salesOrder = $('#addModal').find('#salesOrder').val();
                
                if (salesOrder == '-' && $('#transactionStatus').val() == "Sales") {
                    showAppAlert('Sales Order must be filled');
                    return;
                } else {
                    requireEditReason(function(){ requireManualWeightReason(submitWeightForm); });
                }
            }
            else{
                showAppAlert('Error when submit');
            }

            // If not cash or validation passed, submit form
            //if(pass && $('#weightForm').valid()){
            
            //}
            /*else{
                let userChoice = false;
                if (userChoice) {
                    $('#addModal').find('#status').val("pending");
                    $('#spinnerLoading').show();
                    $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                        var obj = JSON.parse(data); 
                        if(obj.status === 'success'){
                            <?php
                                if(isset($_GET['weight'])){
                                    echo "window.location = weighingModalPage;";
                                }
                            ?>
                            table.ajax.reload();
                            window.location = weighingModalPage;
                            $('#spinnerLoading').hide();
                            $('#addModal').modal('hide');
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
                            $("#failBtn").attr('data-toast-text', 'Failed to save');
                            $("#failBtn").click();
                        }
                    });
                } 
                else {
                    $('#bypassModal').find('#passcode').val("");
                    $('#bypassModal').find('#reason').val("");
                    $('#bypassModal').modal('show');
            
                    $('#bypassForm').validate({
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
            }*/
        });

        $('#submitWeightPrint').on('click', function(){
            if (isClosedSalesOrderSubmissionBlocked()) {
                return;
            }

            // Check weight
            var trueWeight = 0;
            var variance = $('#productVariance').val() || '';
            var high = $('#productHigh').val() || '';
            var low = $('#productLow').val() || '';
            var final = $('#finalWeight').val() || '0';
            var completed = 'N';
            var pass = true;

            if($('#transactionStatus').val() == "Purchase"){
                trueWeight = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#addModal').find('#orderWeight').val());
            }

            if($('#weightType').val() == 'Normal' && ($('#grossIncoming').val() && $('#tareOutgoing').val())){
                isComplete = 'Y';
            }
            else if($('#weightType').val() == 'Container' && ($('#grossIncoming').val() && $('#tareOutgoing').val() && $('#grossIncoming2').val() && $('#tareOutgoing2').val())){
                isComplete = 'Y';
            }
            else{
                isComplete = 'N';
            }

            if (isComplete == 'Y' && variance != '') {
                final = parseFloat(final);
                low = low != '' ? parseFloat(low) : null;
                high = high != '' ? parseFloat(high) : null;
                
                if (variance == 'W') {
                    if (low !== null && (final < trueWeight - low)) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight + high)) {
                        pass = false;
                    }
                } 
                else if (variance == 'P') {
                    if (low !== null && (final < trueWeight * (1 - low / 100))) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight * (1 + high / 100))) {
                        pass = false;
                    }
                }
            }

            pass = true;

            // custom validation for select2
            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
                var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";

                // Check if the value is empty
                if (select2Field.val() === "" || select2Field.val() === null) {
                    select2Container.find('.select2-selection').css('border', '1px solid red'); // Add red border

                    // Add error message if not already present
                    if (select2Container.next('.select2-error').length === 0) {
                        select2Container.after(errorMsg);
                    }

                    pass = false;
                } else {
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message

                    pass = true;
                }
            });

            if ($('#customerType').val() == 'Cash' && pass == true) {
                var unitPrice = parseFloat($('#addModal').find('#unitPrice').val());

                if (!unitPrice || unitPrice <= 0) {
                    showAppAlert('Unit price must be more than 0.');
                    return;
                }else{
                    var productId = $('#addModal').find('#productId').val();
                    $.post('php/getProduct.php', { userID: productId }, function (data) {
                        try {
                            var obj = JSON.parse(data);
                            if (obj.status === 'success') {
                                var price = obj.message.price;
                                // if (unitPrice < price) {
                                //     showAppAlert('Unit price doesn\'t meet the minimum value of RM ' + price);
                                //     return;
                                // }else{
                                    // Continue with form submission after price validation
                                    requireEditReason(function(){ requireManualWeightReason(submitWeightPrintForm); });
                                // }
                            } else {
                                showAppAlert('Error validating product price.');
                            }
                        } catch (e) {
                            showAppAlert('Error processing product validation response.');
                        }
                    });
                    return; // Exit here, will continue in callback
                }
            }

            // Direct submission if not cash or validation passed
            requireEditReason(function(){ requireManualWeightReason(submitWeightPrintForm); });
        });

        $('#submitWeightCancel').on('click', function(){
            if (isClosedSalesOrderSubmissionBlocked()) {
                return;
            }

            var nettWeight = $('#addModal').find('#nettWeight').val() ? parseFloat($('#addModal').find('#nettWeight').val()) : 0;

            if (nettWeight < -100 || nettWeight > 100) {
                showAppAlert('Nett weight must be between -100 and 100.');
                return;
            }

            // Check weight
            var trueWeight = 0;
            var variance = $('#productVariance').val() || '';
            var high = $('#productHigh').val() || '';
            var low = $('#productLow').val() || '';
            var final = $('#finalWeight').val() || '0';
            var completed = 'N';
            var pass = true;

            if($('#transactionStatus').val() == "Purchase"){
                trueWeight = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                trueWeight = parseFloat($('#addModal').find('#orderWeight').val());
            }

            if($('#weightType').val() == 'Normal' && ($('#grossIncoming').val() && $('#tareOutgoing').val())){
                isComplete = 'Y';
            }
            else if($('#weightType').val() == 'Container' && ($('#grossIncoming').val() && $('#tareOutgoing').val() && $('#grossIncoming2').val() && $('#tareOutgoing2').val())){
                isComplete = 'Y';
            }
            else{
                isComplete = 'N';
            }

            if (isComplete == 'Y' && variance != '') {
                final = parseFloat(final);
                low = low != '' ? parseFloat(low) : null;
                high = high != '' ? parseFloat(high) : null;
                
                if (variance == 'W') {
                    if (low !== null && (final < trueWeight - low)) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight + high)) {
                        pass = false;
                    }
                } 
                else if (variance == 'P') {
                    if (low !== null && (final < trueWeight * (1 - low / 100))) {
                        pass = false;
                    } 
                    else if (high !== null && (final > trueWeight * (1 + high / 100))) {
                        pass = false;
                    }
                }
            }

            pass = true;

            // custom validation for select2
            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container'); // Get Select2 UI
                var errorMsg = "<span class='select2-error text-danger' style='font-size: 11.375px;'>Please fill in the field.</span>";

                // Check if the value is empty
                if (select2Field.val() === "" || select2Field.val() === null) {
                    select2Container.find('.select2-selection').css('border', '1px solid red'); // Add red border

                    // Add error message if not already present
                    if (select2Container.next('.select2-error').length === 0) {
                        select2Container.after(errorMsg);
                    }

                    pass = false;
                } else {
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message

                    pass = true;
                }
            });

            if ($('#customerType').val() == 'Cash' && pass == true) {
                var unitPrice = parseFloat($('#addModal').find('#unitPrice').val());

                if (!unitPrice || unitPrice <= 0) {
                    showAppAlert('Unit price must be more than 0.');
                    return;
                } else {
                    var productId = $('#addModal').find('#productId').val();
                    $.post('php/getProduct.php', { userID: productId }, function (data) {
                        try {
                            var obj = JSON.parse(data);
                            if (obj.status === 'success') {
                                var price = obj.message.price;
                                //if (unitPrice < price) {
                                    //showAppAlert('Unit price doesn\'t meet the minimum value of RM ' + price);
                                    //return;
                                //} else {
                                    // Price validation passed, submit the form
                                    requireEditReason(function(){ requireManualWeightReason(submitWeightCancelForm); });
                                //}
                            } else {
                                showAppAlert('Error validating product price');
                            }
                        } catch (e) {
                            showAppAlert('Error processing product validation response');
                        }
                    }).fail(function() {
                        showAppAlert('Error connecting to server for price validation');
                    });
                    return; // Exit here to prevent immediate form submission
                }
            }
            else if($('#customerType').val() == 'Normal' && pass == true){
                var salesOrder = $('#addModal').find('#salesOrder').val();
                
                if (salesOrder == '-' && $('#transactionStatus').val() == "Sales") {
                    showAppAlert('Sales Order must be filled');
                    return;
                } else {
                    requireEditReason(function(){ requireManualWeightReason(submitWeightCancelForm); });
                }
            }
            else{
                showAppAlert('Error when submit');
            }            
        });

        $('#submitBypass').on('click', function(){
            if($('#bypassForm').valid()){
                $('#addModal').find('#bypassReason').val($('#bypassModal').find('#reason').val());
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        <?php
                            if(isset($_GET['weight'])){
                                echo "window.location = weighingModalPage;";
                            }
                        ?>
                        table.ajax.reload();
                        window.location = weighingModalPage;
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
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
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
            }
        });

        $('#addWeight').on('click', function(){ 
            addNewTag = true;
            isEdit = false;
            editIsComplete = false;

            // Show Capture Buttons When Add New
            $('#addModal').find('#grossCapture').show();
            $('#addModal').find('#tareCapture').show();
            $('#addModal').find('#id').val("");
            setClosedSalesOrderSubmissionState(false);
            $('#addModal').find('#transactionId').val("");
            filterTransactionStatus('create');
            $('#addModal').find('#transactionStatus').val("Sales").trigger('change').prop('disabled', false); // Enable changing transaction status on add new
            $('#addModal').find('input[name="transactionStatus"]').remove(); // remove hidden input if exists
            $('#addModal').find('#weightType').val("Normal").trigger('change');
            $('#addModal').find('#customerType').val("Normal").trigger('change');
            $('#addModal').find('#unitPrice').removeAttr('required');
            $('#addModal').find('#transactionDate').val(formatDate2(today));
            $('#addModal').find('#vehiclePlateNo1').val("").trigger('change');
            $('#addModal').find('#vehiclePlateNo2').val("").trigger('change');
            $('#addModal').find('#bypassReason').val("");
            $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');
            $('#addModal').find('#containerNo').val("");
            $('#addModal').find('#invoiceNo').val("");
            $('#addModal').find('#deliveryNo').val("");
            $('#addModal').find('#otherRemarks').val("");
            $('#addModal').find('#manualWeightReason').val("");
            $('#addModal').find('#manualWeightReasonInput').val("");
            $('#addModal').find('#manualWeightReasonDisplay').hide();
            $('#addModal').find('#editReason').val("");
            $('#addModal').find('#editReasonInput').val("");
            $('#addModal').find('#editReasonDisplay').hide();
            $('#addModal').find('#manualVehicle').prop('checked', false).trigger('change');
            $('#addModal').find('#manualVehicle2').prop('checked', false).trigger('change');
            $('#addModal').find('#grossIncoming').val("");
            grossIncomingDatePicker.clear();
            $('#addModal').find('#tareOutgoing').val("");
            tareOutgoingDatePicker.clear();
            $('#addModal').find('#nettWeight').val("");
            $('#addModal').find('#grossIncoming2').val("");
            $('#addModal').find('#status').val("");
            grossIncomingDatePicker2.clear();
            $('#addModal').find('#tareOutgoing2').val("");
            tareOutgoingDatePicker2.clear();
            $('#addModal').find('#nettWeight2').val("");
            $('#addModal').find('#reduceWeight').val("");
            // $('#addModal').find('#vehicleNo').val(obj.message.final_weight);
            $('#addModal').find('#weightDifference').val("");
            // $('#addModal').find('#id').val(obj.message.is_complete);
            // $('#addModal').find('#vehicleNo').val(obj.message.is_cancel);
            // $('#addModal').find("#manualWeightNo").prop("checked", true);
            // $('#addModal').find("#manualWeightYes").prop("checked", false);
            $('#addModal').find('#manualWeightNo').trigger('click');
            //$('#addModal').find('input[name="manualWeight"]').val("false");
            //$('#addModal').find('#indicatorId').val("");
            $('#addModal').find('#weighbridge').val("");
            //$('#addModal').find('#indicatorId2').val("");
            $('#addModal').find('#unitPrice').val("");
            $('#addModal').find('#subTotalPrice').val("0.00");
            $('#addModal').find('#sstPrice').val("0.00");
            $('#addModal').find('#productPrice').val("0.00");
            $('#addModal').find('#totalPrice').val("0.00");
            $('#addModal').find('#finalWeight').val("");
            $('#addModal').find("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
            $('#addModal').find('#batchDrum').val("").trigger('change');
            $('#addModal').find('#noOfDrum').val("");
            $('#addModal').find('#balance').val("");
            $('#addModal').find('#insufficientBalDisplay').hide();

            $('#addModal').find('#customerCode').val("");
            $('#addModal').find('#customerName').val("").trigger('change');
            $('#addModal').find('#supplierCode').val("");
            $('#addModal').find('#supplierName').val("").trigger('change');
            $('#addModal').find('#siteCode').val("");
            $('#addModal').find('#siteName').val("").trigger('change');
            $('#addModal').find('#agent').val("").trigger('change');
            $('#addModal').find('#agentCode').val("");
            $('#addModal').find('#plantCode').val("");
            $('#addModal').find('#plant').val("<?=$plantName ?>").trigger('change');
            $('#addModal').find('#orderWeight').val("0");
            $('#addModal').find('#supplierWeight').val("0");
            $('#addModal').find('#transporterCode').val("");
            $('#addModal').find('#transporter').val("").trigger('change');
            $('#addModal').find('#destinationCode').val("");
            $('#addModal').find('#destination').val("").trigger('change');
            $('#addModal').find('#productCode').val("");
            $('#addModal').find('#productName').val("").trigger('change');
            $('#addModal').find('#productDescription').val("");
            $('#addModal').find('#productHigh').val("");
            $('#addModal').find('#productLow').val("");
            $('#addModal').find('#productVariance').val("");
            $('#addModal').find('#rawMaterialCode').val("");
            $('#addModal').find('#rawMaterialName').val("").trigger('change');
            $('#addModal').find('#currentWeight').text(0);
            $('#addModal').find('#supplierWeightBasicUom').val(0).trigger('change');
            $('#addModal').find('#orderWeightBasicUom').val(0).trigger('change');

            // Show select and hide input readonly
            $('#addModal').find('#salesOrderEdit').val("").hide();
            $('#addModal').find('#purchaseOrderEdit').val("").hide();

            // Unset appended so/po fields
            $('#addModal').find('#salesOrder').empty();
            $('#addModal').find('#salesOrder').append(salesOption);
            $('#addModal').find('#salesOrder').val("").trigger('change');
            $('#addModal').find('#purchaseOrder').empty();
            $('#addModal').find('#purchaseOrder').append(purchaseOption);
            $('#addModal').find('#purchaseOrder').val("").trigger('change');

            // Remove Validation Error Message
            $('#addModal .is-invalid').removeClass('is-invalid');

            $('#addModal .select2[required]').each(function () {
                var select2Field = $(this);
                var select2Container = select2Field.next('.select2-container');
                
                select2Container.find('.select2-selection').css('border', ''); // Remove red border
                select2Container.next('.select2-error').remove(); // Remove error message
            });

            addNewTag = false;
            $('#addModal').modal('show');
            
            $('#weightForm').validate({
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    if (element.parent('.input-group').length) {
                        // if inside input-group → place error after the group
                        element.parent().after(error);
                    } else {
                        element.closest('.form-group').append(error);
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });

        $('#weightType').on('change', function(){
            if($(this).val() == "Container")
            {
                $('#containerCard').show();
            }
            else
            {
                $('#containerCard').hide();
            }
        });

        $('#customerType').on('change', function(){
            var transactionStatus = $('#addModal').find('#transactionStatus').val();
            if (transactionStatus == 'Purchase'){
                $('#unitPriceDisplay').hide();
                $('#subTotalPriceDisplay').hide();
                $('#sstDisplay').hide();
                $('#totalPriceDisplay').hide();
            }else{
                if($(this).val() == "Cash")
                {
                    if (transactionStatus == 'Sales'){
                        $('#unitPriceDisplay').show();
                        $('#unitPrice').prop('required',true);
                        $('#subTotalPriceDisplay').show();
                        $('#sstDisplay').show();
                        $('#totalPriceDisplay').show();
                        $('#tinNoDisplay').show();
                        $('#idNoDisplay').show();
                        $('#idTypeDisplay').show();
                    }else{
                        $('#unitPriceDisplay').hide();
                        $('#unitPrice').removeAttr('required');
                        $('#subTotalPriceDisplay').hide();
                        $('#sstDisplay').hide();
                        $('#totalPriceDisplay').hide();
                        $('#tinNoDisplay').hide();
                        $('#idNoDisplay').hide();
                        $('#idTypeDisplay').hide();
                    }
                    
                    $('#addModal').find('#salesOrder').prop('disabled', true);
                    $('#addModal').find('#purchaseOrder').prop('disabled', true);
                    
                    // Remove required attribute for cash transactions
                    $('#addModal').find('#salesOrder').removeAttr('required');
                    $('#addModal').find('#purchaseOrder').removeAttr('required');
                }
                else
                {
                    $('#unitPriceDisplay').hide();
                    $('#unitPrice').removeAttr('required');
                    $('#subTotalPriceDisplay').hide();
                    $('#sstDisplay').hide();
                    $('#totalPriceDisplay').hide();
                    $('#tinNoDisplay').hide();
                    $('#idNoDisplay').hide();
                    $('#idTypeDisplay').hide();

                    $('#addModal').find('#salesOrder').prop('disabled', false);
                    $('#addModal').find('#purchaseOrder').prop('disabled', false);
                    
                    // Add required attribute back for non-cash transactions
                    $('#addModal').find('#salesOrder').attr('required', 'required');
                    $('#addModal').find('#purchaseOrder').attr('required', 'required');
                }
            }
        });

        $('#manualVehicle').on('change', function(){
            if($(this).is(':checked')){
                $(this).val(1);
                $('#vehiclePlateNo1').val('-').trigger('change');
                $('.index-vehicle').hide();
                $('#vehicleNoTxt').show();
            }
            else{
                $(this).val(0);
                $('#vehicleNoTxt').hide();
                $('#vehicleNoTxt').val('');
                $('.index-vehicle').show();
            }
        });

        $('#vehicleNoTxt').on('keyup', function(){
            var x = $('#vehicleNoTxt').val();
            x = x.toUpperCase();
            $('#vehicleNoTxt').val(x);
            var transactionStatus = $('#addModal').find('#transactionStatus').val();

            if (x){
                $.post('php/getVehicle.php', {userID: x, type: 'lookup'}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        /*if (obj.message.length > 0){
                            if (obj.message.length > 1){ 
                                $('#addModal').find('#transporter').empty();
                                $('#addModal').find('#transporter').append(`<option selected="-">-</option>`);

                                var deliveredTransporter;
                                var hasValidTransporter = false; // Flag to check if any valid transporter exists

                                for (var i = 0; i < obj.message.length; i++) {
                                    var customerName = obj.message[i].customer_name;
                                    var customerCode = obj.message[i].customer_code;
                                    var transporterName = obj.message[i].transporter_name;
                                    var transporterCode = obj.message[i].transporter_code;
                                    var exDel = obj.message[i].ex_del;

                                    if (exDel == 'DEL'){
                                        deliveredTransporter = transporterName;
                                    }

                                    if (transporterName) {
                                        hasValidTransporter = true;
                                        $('#addModal').find('#transporter').append(
                                            `<option value="${transporterName}" data-code="${transporterCode}">${transporterName}</option>`
                                        );
                                    }
                                }

                                if (!hasValidTransporter){
                                    $('#addModal').find('#transporter').empty();
                                    $('#addModal').find('#transporter').append(transporterOption);
                                }

                                $('#addModal').find('#transporter').val(deliveredTransporter).trigger('change');
                            }
                            else{
                                var exDel = obj.message[0].ex_del;
                                var customerName = obj.message[0].customer_name;
                                var customerCode = obj.message[0].customer_code;
                                var transporterName = obj.message[0].transporter_name;
                                var transporterCode = obj.message[0].transporter_code;

                                $('#addModal').find('#transporter').empty();
                                $('#addModal').find('#transporter').append(transporterOption);

                                if (exDel == 'EX'){
                                    $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true).trigger('change');

                                    if (!$('#addModal').find('#transporter').val()) {
                                        $('#addModal').find('#transporter').val(transporterName).trigger('change');
                                        if (transporterName){
                                            // $('#addModal').find('#transporter').attr('disabled', true);
                                            $('#addModal').find('#transporterName').val(transporterName);
                                        }else{
                                            // $('#addModal').find('#transporter').attr('disabled', false);
                                        }
                                        $('#addModal').find('#transporterCode').val(transporterCode);
                                    }

                                    if (!$('#addModal').find('#customerName').val()) {
                                        $('#addModal').find('#customerName').val(customerName).trigger('change');
                                        if (customerName){
                                            // $('#addModal').find('#customerName').attr('disabled', true);
                                            $('#addModal').find('#custName').val(customerName);
                                        }else{
                                            // $('#addModal').find('#customerName').attr('disabled', false);
                                            $('#addModal').find('#custName').val(customerName);
                                        }
                                        $('#addModal').find('#customerCode').val(customerCode);
                                    }
                                }
                                else{
                                    $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');

                                    if (!$('#addModal').find('#transporter').val()) {
                                        $('#addModal').find('#transporter').val(transporterName).trigger('change');
                                        if (transporterName){
                                            // $('#addModal').find('#transporter').attr('disabled', true);
                                            $('#addModal').find('#transporterName').val(transporterName);
                                        }else{
                                            // $('#addModal').find('#transporter').attr('disabled', false);
                                            
                                        }
                                        $('#addModal').find('#transporterCode').val(transporterCode);
                                    }

                                    if (!$('#addModal').find('#customerName').val()) {
                                        $('#addModal').find('#customerName').val('').trigger('change');
                                        // $('#addModal').find('#customerName').attr('disabled', false);
                                        $('#addModal').find('#customerCode').val('');
                                    }
                                } 
                            }
                        } */   
                        
                        /*if (transactionStatus == 'Purchase'){
                            var purchaseOrder = $('#addModal').find('#purchaseOrder').val();

                            if(!purchaseOrder && !soPoTag && !addNewTag && $('#addModal').find('#supplierName').val()){
                                getSoPo();
                            }
                        }
                        else{
                            var salesOrder = $('#addModal').find('#salesOrder').val();

                            if(!salesOrder && !soPoTag && !addNewTag && $('#addModal').find('#customerName').val()){
                                getSoPo();
                            }
                        }*/
                    }
                    else if(obj.status === 'error'){
                        showAppAlert(obj.message);
                        $('#vehiclePlateNo1').val('').trigger('change');
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
            // var exDel = $('input[name="exDel"]:checked').val();
            // if (exDel == 'true'){
            //     // $('#addModal').find('#transporter').val('Own Transportation').trigger('change');
            //     // $('#addModal').find('#transporterCode').val('T01');
            //     $.post('php/getVehicle.php', {userID: x, type: 'lookup'}, function (data){
            //         var obj = JSON.parse(data);

            //         if (obj.status == 'success'){
            //             // var customerName = obj.message.customer_name;
            //             // var customerCode = obj.message.customer_code;

            //             // $('#addModal').find('#customerName').val(customerName).trigger('change');
            //             // $('#addModal').find('#customerCode').val(customerCode);
            //         }
            //         else if(obj.status === 'error'){
            //             showAppAlert(obj.message);
            //             $('#vehicleNoTxt').val('');
            //         }
            //         else if(obj.status === 'failed'){
            //             $('#spinnerLoading').hide();
            //             $("#failBtn").attr('data-toast-text', obj.message );
            //             $("#failBtn").click();
            //         }
            //         else{
            //             $('#spinnerLoading').hide();
            //             $("#failBtn").attr('data-toast-text', obj.message );
            //             $("#failBtn").click();
            //         }
            //     });
            // }else{
            //     // $('#addModal').find('#customerName').val('').trigger('change');
            //     // $('#addModal').find('#customerCode').val('');

            //     $.post('php/getVehicle.php', {userID: x, type: 'lookup'}, function (data){
            //         var obj = JSON.parse(data);

            //         if (obj.status == 'success'){
            //             // var transporterName = obj.message.transporter_name;
            //             // var transporterCode = obj.message.transporter_code;

            //             // $('#addModal').find('#transporter').val(transporterName).trigger('change');
            //             // $('#addModal').find('#transporterCode').val(transporterCode);
            //         }
            //         else if(obj.status === 'error'){
            //             showAppAlert(obj.message);
            //             $('#vehicleNoTxt').val('');
            //         }
            //         else if(obj.status === 'failed'){
            //             $('#spinnerLoading').hide();
            //             $("#failBtn").attr('data-toast-text', obj.message );
            //             $("#failBtn").click();
            //         }
            //         else{
            //             $('#spinnerLoading').hide();
            //             $("#failBtn").attr('data-toast-text', obj.message );
            //             $("#failBtn").click();
            //         }
            //     });
            // }
        });

        $('#vehiclePlateNo1').on('change', function(){
            //var tare = $('#vehiclePlateNo1 :selected').data('weight') ? parseFloat($('#vehiclePlateNo1 :selected').data('weight')) : 0;
        
            //if($('#transactionStatus').val() == "Purchase" || $(this).val() == "Local"){
                //$('#grossIncoming').val(parseFloat(tare).toFixed(0));
                //$('#grossIncoming').trigger('keyup');
            /*}
            else{
                $('#tareOutgoing').val(parseFloat(tare).toFixed(0));
                $('#tareOutgoing').trigger('keyup');
            }*/

            var vehicleNo1 = $(this).val();
            var transactionStatus = $('#addModal').find('#transactionStatus').val();
            var vehicleNo1Edit = $('#vehiclePlateNo1Edit').val();
            if (vehicleNo1Edit == 'EDIT'){
                return;
            }else{
                if (vehicleNo1){
                    $.post('php/getVehicle.php', {userID: vehicleNo1, type: 'lookup'}, function (data){
                        var obj = JSON.parse(data);

                        if (obj.status == 'success'){
                            /*if (obj.message.length > 0){
                                if (obj.message.length > 1){
                                    $('#addModal').find('#transporter').empty();
                                    $('#addModal').find('#transporter').append(`<option selected="-">-</option>`);

                                    var deliveredTransporter;
                                    var hasValidTransporter = false; // Flag to check if any valid transporter exists

                                    for (var i = 0; i < obj.message.length; i++) {
                                        var customerName = obj.message[i].customer_name;
                                        var customerCode = obj.message[i].customer_code;
                                        var transporterName = obj.message[i].transporter_name;
                                        var transporterCode = obj.message[i].transporter_code;
                                        var exDel = obj.message[i].ex_del;

                                        if (exDel == 'DEL'){
                                            deliveredTransporter = transporterName;
                                        }

                                        if (transporterName) {
                                            hasValidTransporter = true;
                                            $('#addModal').find('#transporter').append(
                                                `<option value="${transporterName}" data-code="${transporterCode}">${transporterName}</option>`
                                            );
                                        }
                                    }

                                    if (!hasValidTransporter){
                                        $('#addModal').find('#transporter').empty();
                                        $('#addModal').find('#transporter').append(transporterOption);
                                    }

                                    $('#addModal').find('#transporter').val(deliveredTransporter).trigger('change');
                                }
                                else{
                                    var exDel = obj.message[0].ex_del;
                                    var customerName = obj.message[0].customer_name;
                                    var customerCode = obj.message[0].customer_code;
                                    var transporterName = obj.message[0].transporter_name;
                                    var transporterCode = obj.message[0].transporter_code;

                                    $('#addModal').find('#transporter').empty();
                                    $('#addModal').find('#transporter').append(transporterOption);

                                    if (exDel == 'EX'){
                                        $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true).trigger('change');

                                        if (!$('#addModal').find('#transporter').val()) {
                                            $('#addModal').find('#transporter').val(transporterName).trigger('change');
                                            if (transporterName){
                                                // $('#addModal').find('#transporter').attr('disabled', true);
                                                $('#addModal').find('#transporterName').val(transporterName);
                                            }else{
                                                // $('#addModal').find('#transporter').attr('disabled', false);
                                            }
                                            $('#addModal').find('#transporterCode').val(transporterCode);
                                        }

                                        if (!$('#addModal').find('#customerName').val()) {
                                            $('#addModal').find('#customerName').val(customerName).trigger('change');
                                            if (customerName){
                                                // $('#addModal').find('#customerName').attr('disabled', true);
                                                $('#addModal').find('#custName').val(customerName);
                                            }else{
                                                // $('#addModal').find('#customerName').attr('disabled', false);
                                                $('#addModal').find('#custName').val(customerName);
                                            }
                                            $('#addModal').find('#customerCode').val(customerCode);
                                        }
                                    }
                                    else{
                                        $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');

                                        if (!$('#addModal').find('#transporter').val()) {
                                            $('#addModal').find('#transporter').val(transporterName).trigger('change');
                                            if (transporterName){
                                                // $('#addModal').find('#transporter').attr('disabled', true);
                                                $('#addModal').find('#transporterName').val(transporterName);
                                            }else{
                                                // $('#addModal').find('#transporter').attr('disabled', false);
                                                
                                            }
                                            $('#addModal').find('#transporterCode').val(transporterCode);
                                        }

                                        if (!$('#addModal').find('#customerName').val()) {
                                            $('#addModal').find('#customerName').val('').trigger('change');
                                            // $('#addModal').find('#customerName').attr('disabled', false);
                                            $('#addModal').find('#customerCode').val('');
                                        }
                                    } 
                                }
                            }*/   
                            
                            /*if (transactionStatus == 'Purchase'){
                                var purchaseOrder = $('#addModal').find('#purchaseOrder').val();

                                if(!purchaseOrder && !soPoTag && !addNewTag && $('#addModal').find('#supplierName').val()){
                                    getSoPo();
                                }
                            }
                            else{
                                var salesOrder = $('#addModal').find('#salesOrder').val();

                                if(!salesOrder && !soPoTag && !addNewTag && $('#addModal').find('#customerName').val()){
                                    getSoPo();
                                }
                            }*/
                        }
                        else if(obj.status === 'error'){
                            showAppAlert(obj.message);
                            $('#vehiclePlateNo1').val('').trigger('change');
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
                
                // $('#addModal').find('#customerName').val('').trigger('change');
                // $('#addModal').find('#customerCode').val('');

                // $.post('php/getVehicle.php', {userID: vehicleNo1, type: 'lookup'}, function (data){
                //     var obj = JSON.parse(data);

                //     if (obj.status == 'success'){
                //         // var transporterName = obj.message.transporter_name;
                //         // var transporterCode = obj.message.transporter_code;

                //         // $('#addModal').find('#transporter').val(transporterName).trigger('change');
                //         // $('#addModal').find('#transporterCode').val(transporterCode);
                //     }
                //     else if(obj.status === 'error'){
                //         showAppAlert(obj.message);
                //         $('#vehiclePlateNo1').val('').trigger('change');
                //     }
                //     else if(obj.status === 'failed'){
                //         $('#spinnerLoading').hide();
                //         $("#failBtn").attr('data-toast-text', obj.message );
                //         $("#failBtn").click();
                //     }
                //     else{
                //         $('#spinnerLoading').hide();
                //         $("#failBtn").attr('data-toast-text', obj.message );
                //         $("#failBtn").click();
                //     }
                // });
            }
        });

        $('#vehiclePlateNo2').on('change', function(){
            //var tare = $('#vehiclePlateNo2 :selected').data('weight') ? parseFloat($('#vehiclePlateNo2 :selected').data('weight')) : 0;
        
            //if($('#transactionStatus').val() == "Purchase" || $(this).val() == "Local"){
                //$('#grossIncoming2').val(parseFloat(tare).toFixed(0));
                //$('#grossIncoming2').trigger('keyup');
            /*}
            else{
                $('#tareOutgoing2').val(parseFloat(tare).toFixed(0));
                $('#tareOutgoing2').trigger('keyup');
            }*/
        });

        $('#manualVehicle2').on('click', function(){
            if($(this).is(':checked')){
                $(this).val(1);
                $('#vehiclePlateNo2').val('-');
                $('.index-vehicle2').hide();
                $('#vehicleNoTxt2').show();
            }
            else{
                $(this).val(0);
                $('#vehicleNoTxt2').hide();
                $('#vehicleNoTxt2').val('');
                $('.index-vehicle2').show();
            }
        });

        $('#vehicleNoTxt2').on('keyup', function(){
            var x = $('#vehicleNoTxt2').val();
            x = x.toUpperCase();
            $('#vehicleNoTxt2').val(x);
        });

        $('.radio-manual-weight').on('click', function(){
            if($('input[name="manualWeight"]:checked').val() == "true"){
                $('#tareOutgoing').removeAttr('readonly');
                $('#grossIncoming').removeAttr('readonly');
                $('#tareOutgoing2').removeAttr('readonly');
                $('#grossIncoming2').removeAttr('readonly');
            }
            else{
                $('#grossIncoming').attr('readonly', 'readonly');
                $('#tareOutgoing').attr('readonly', 'readonly');
                $('#grossIncoming2').attr('readonly', 'readonly');
                $('#tareOutgoing2').attr('readonly', 'readonly');
            }
        });

        $('#grossIncoming').on('keyup', function(){
            var gross = $(this).val() ? parseFloat($(this).val()) : 0;
            var tare = $('#tareOutgoing').val() ? parseFloat($('#tareOutgoing').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#nettWeight').trigger('change');

            // Update the Flatpickr instance
            grossIncomingDatePicker.setDate(getMalaysiaDate()); // sets it to current date/time in Malaysia timezone
            $('#grossIncomingDate').trigger('change');
        });

        $('#grossCapture').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#grossIncoming').val(parseFloat(text).toFixed(0));
            $('#grossIncoming').trigger('keyup');
        });

        $('#tareOutgoing').on('keyup', function(){
            var tare = $(this).val() ? parseFloat($(this).val()) : 0;
            var gross = $('#grossIncoming').val() ? parseFloat($('#grossIncoming').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight').val(nett.toFixed(0));
            $('#nettWeight').trigger('change');

            // Update the Flatpickr instance
            tareOutgoingDatePicker.setDate(getMalaysiaDate()); // sets it to current date/time in Malaysia timezone
            $('#tareOutgoingDate').trigger('change');
        });

        $('#tareCapture').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing').trigger('keyup');
        });

        $('#nettWeight').on('change', function(){
            var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett2 = $('#nettWeight2').val() ? parseFloat($('#nettWeight2').val()) : 0;
            var current = Math.abs(nett1 + nett2);
            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#currentWeight').trigger('change');
            $('#finalWeight').trigger('change');

            // Logic for Converted UOM
            var transactionStatus = $('#addModal').find('#transactionStatus').val();
            var prodRawCode = '';
            var type = '';
            var prodRawId = '';
            if(transactionStatus == 'Sales'){
                prodRawId = $('#addModal').find('#productName :selected').data('id');
                type = 'SO';
            }else if (transactionStatus == 'Purchase'){
                prodRawId = $('#addModal').find('#rawMaterialName :selected').data('id');
                type = 'PO';
            }
            
            if (prodRawId && nettWeight){
                $.post('php/getProdRawMatUOM.php', {userID: prodRawId, type: type}, function(data)
                {
                    var obj = JSON.parse(data);
                    if(obj.status === 'success'){
                        // Processing for order quantity (KG)
                        var rate = parseFloat(obj.message.rate);
                        var basicNettWeight = nett1*rate;

                        $('#addModal').find('#basicNettWeight').val(basicNettWeight);
                    }
                    else if(obj.status === 'failed'){
                        showAppAlert(obj.message);
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        showAppAlert(obj.message);
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                });
            }

        });

        $('#finalWeight').on('change', function(){
            var nett1 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett2 = 0;

            if($('#transactionStatus').val() == "Purchase"){
                nett2 = parseFloat($('#addModal').find('#supplierWeight').val());
            }
            else{
                nett2 = parseFloat($('#addModal').find('#orderWeight').val());
            }
            
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
        });

        $('#orderWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));

            var previousRecordsTag = $('#addModal').find('#previousRecordsTag').val();

            if (previousRecordsTag == 'false'){
                $('#addModal').find('#balance').val($(this).val());
                if ($(this).val() <= 0) {
                    $('#addModal').find('#insufficientBalDisplay').hide();
                } else {
                    $('#addModal').find('#insufficientBalDisplay').show();
                }
            }
        });

        $('#supplierWeight').on('change', function(){
            var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var current = nett1 - nett2;
            $('#weightDifference').val(current.toFixed(0));
            
            var previousRecordsTag = $('#addModal').find('#previousRecordsTag').val();

            if (previousRecordsTag == 'false'){
                $('#addModal').find('#balance').val($(this).val());
                if ($(this).val() <= 0) {
                    $('#addModal').find('#insufficientBalDisplay').hide();
                } else {
                    $('#addModal').find('#insufficientBalDisplay').show();
                }
            }
        });

        $('#grossIncoming2').on('keyup', function(){
            var gross = $(this).val() ? parseFloat($(this).val()) : 0;
            var tare = $('#tareOutgoing2').val() ? parseFloat($('#tareOutgoing2').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight2').val(nett.toFixed(0));
            $('#nettWeight2').trigger('change');

            // Update the Flatpickr instance
            grossIncomingDatePicker2.setDate(getMalaysiaDate()); // sets it to current date/time in Malaysia timezone
            $('#grossIncomingDate2').trigger('change');
        });

        $('#grossCapture2').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#grossIncoming2').val(parseFloat(text).toFixed(0));
            $('#grossIncoming2').trigger('keyup');
        });

        $('#tareOutgoing2').on('keyup', function(){
            var tare = $(this).val() ? parseFloat($(this).val()) : 0;
            var gross = $('#grossIncoming2').val() ? parseFloat($('#grossIncoming2').val()) : 0;
            var nett = Math.abs(gross - tare);
            $('#nettWeight2').val(nett.toFixed(0));
            $('#nettWeight2').trigger('change');

            // Update the Flatpickr instance
            tareOutgoingDatePicker2.setDate(getMalaysiaDate()); // sets it to current date/time in Malaysia timezone
            $('#tareOutgoingDate2').trigger('change');
        });

        $('#tareCapture2').on('click', function(){
            var text = $('#indicatorWeight').text();
            $('#tareOutgoing2').val(parseFloat(text).toFixed(0));
            $('#tareOutgoing2').trigger('keyup');
        });

        $('#nettWeight2').on('change', function(){
            var nett2 = $(this).val() ? parseFloat($(this).val()) : 0;
            var nett1 = $('#nettWeight').val() ? parseFloat($('#nettWeight').val()) : 0;
            var current = Math.abs(nett1 + nett2);

            // if ($('#weightType').val() == "Container"){
            //     var gross1 = $('#grossIncoming').val() ? parseFloat($('#grossIncoming').val()) : 0;
            //     var tare1 = $('#tareOutgoing').val() ? parseFloat($('#tareOutgoing').val()) : 0;
            //     var gross2 = $('#grossIncoming2').val() ? parseFloat($('#grossIncoming2').val()) : 0;
            //     var tare2 = $('#tareOutgoing2').val() ? parseFloat($('#tareOutgoing2').val()) : 0;
            //     current = Math.abs((gross1 + gross2) - (tare1 + tare2));
            // }

            $('#currentWeight').text(current.toFixed(0));
            $('#finalWeight').val(current.toFixed(0));
            $('#currentWeight').trigger('change');
            $('#finalWeight').trigger('change');
        });

        $('#currentWeight').on('change', function(){
            var productId = $('#addModal').find('#productId').val();
            // var price = $('#productPrice').val() ? parseFloat($('#productPrice').val()).toFixed(2) : 0.00;
            var price = $('#unitPrice').val() ? parseFloat($('#unitPrice').val()).toFixed(2) : 0.00;
            var weight = $('#currentWeight').text() ? parseFloat($('#currentWeight').text()) : 0;

            if (productId && price && weight){
                calculatePrice(price, weight, productId);
            }
        });

        $('#transactionStatus').on('change', function(){
            var customerType = $('#addModal').find('#customerType').val();
            var transactionKey = ($(this).val() == 'Local') ? 'Public' : $(this).val();

            if($(this).val() == "Purchase"){
                $('#divWeightDifference').show();
                $('#divSupplierWeight').show();
                $('#addModal').find('#orderWeight').val("");
                $('#addModal').find('#supplierWeight').val("0");
                $('#divSupplierName').show();
                $('#divOrderWeight').hide();
                $('#divCustomerName').hide();
                $('#rawMaterialDisplay').show();
                $('#productNameDisplay').hide();
                $('#addModal').find('#divPoSupplyWeight').show();

                if (isSADMIN || (permissions['Weighing'] && permissions['Weighing'][transactionKey] && permissions['Weighing'][transactionKey].includes('display_do'))) {
                    $('#doDisplay').show();
                }
                
                if ($(this).val() == "Purchase"){
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Purchase Order');
                    // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Purchase Order');
                    
                    //Hide SO Select
                    $('#divPurchaseOrder').find('#soSelect').hide();
                    $('#divPurchaseOrder').find('#poSelect').show();

                    // Hide Pricing Fields
                    $('#unitPriceDisplay').hide();
                    $('#unitPrice').removeAttr('required');
                    $('#subTotalPriceDisplay').hide();
                    $('#sstDisplay').hide();
                    $('#totalPriceDisplay').hide();
                    $('#tinNoDisplay').hide();
                    $('#idNoDisplay').hide();
                    $('#idTypeDisplay').hide();
                }else{
                    $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                    // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');

                    //Hide PO Select
                    $('#divPurchaseOrder').find('#soSelect').show();
                    $('#divPurchaseOrder').find('#poSelect').hide();

                    if (customerType == 'Cash'){
                        $('#unitPriceDisplay').show();
                        $('#unitPrice').prop('required',true);
                        $('#subTotalPriceDisplay').show();
                        $('#sstDisplay').show();
                        $('#totalPriceDisplay').show();
                        $('#tinNoDisplay').show();
                        $('#idNoDisplay').show();
                        $('#idTypeDisplay').show();
                    }else{
                        $('#unitPriceDisplay').hide();
                        $('#unitPrice').removeAttr('required');
                        $('#subTotalPriceDisplay').hide();
                        $('#sstDisplay').hide();
                        $('#totalPriceDisplay').hide();
                        $('#tinNoDisplay').hide();
                        $('#idNoDisplay').hide();
                        $('#idTypeDisplay').hide();
                    }
                }

                // Set non-purchase related fields to empty
                $('#addModal').find('#salesOrder').val("").trigger('change');
                $('#addModal').find('#customerName').val("").trigger('change');
                $('#addModal').find('#productName').val("").trigger('change');
                $('#addModal').find('#orderWeightBasicUom').val("").trigger('change');
                $('#addModal').find('#balance').val(0);
            }
            else if($(this).val() == "Local"){
                $('#divOrderWeight').show();
                $('#addModal').find('#orderWeight').val("0");
                $('#addModal').find('#supplierWeight').val("");
                $('#divWeightDifference').show();
                $('#divSupplierWeight').hide();
                $('#divSupplierName').hide();
                $('#divCustomerName').show();
                $('#rawMaterialDisplay').hide();
                $('#productNameDisplay').show();
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');
                $('#addModal').find('#divPoSupplyWeight').hide();

                //Hide PO Select
                $('#divPurchaseOrder').find('#soSelect').show();
                $('#divPurchaseOrder').find('#poSelect').hide();

                if (isSADMIN || (permissions['Weighing'] && permissions['Weighing'][transactionKey] && permissions['Weighing'][transactionKey].includes('display_do'))) {
                    $('#doDisplay').show();
                }
                
                $('#unitPriceDisplay').hide();
                $('#unitPrice').removeAttr('required');
                $('#subTotalPriceDisplay').hide();
                $('#sstDisplay').hide();
                $('#totalPriceDisplay').hide();
                $('#tinNoDisplay').hide();
                $('#idNoDisplay').hide();
                $('#idTypeDisplay').hide();

                // Set non-local related fields to empty
                $('#addModal').find('#purchaseOrder').val("").trigger('change');
                $('#addModal').find('#supplierName').val("").trigger('change');
                $('#addModal').find('#rawMaterialName').val("").trigger('change');
                $('#addModal').find('#supplierWeightBasicUom').val("").trigger('change');
                $('#addModal').find('#poSupplyWeight').val("");
                $('#addModal').find('#balance').val(0);
            }
            else{
                $('#divOrderWeight').show();
                $('#addModal').find('#orderWeight').val("0");
                $('#addModal').find('#supplierWeight').val("");

                if ($(this).val() == "Sales"){
                    $('#weightDifference').val(0);
                    $('#divWeightDifference').hide();
                }else{
                    $('#divWeightDifference').show();
                }

                $('#divSupplierWeight').hide();
                $('#divSupplierName').hide();
                $('#divCustomerName').show();
                $('#rawMaterialDisplay').hide();
                $('#productNameDisplay').show();
                $('#divPurchaseOrder').find('label[for="purchaseOrder"]').text('Sale Order');
                // $('#divPurchaseOrder').find('#purchaseOrder').attr('placeholder', 'Sale Order');
                $('#addModal').find('#divPoSupplyWeight').hide();

                //Hide PO Select
                $('#divPurchaseOrder').find('#soSelect').show();
                $('#divPurchaseOrder').find('#poSelect').hide();

                if (isSADMIN || (permissions['Weighing'] && permissions['Weighing'][transactionKey] && permissions['Weighing'][transactionKey].includes('display_do'))) {
                    $('#doDisplay').hide();
                }

                if (customerType == 'Cash'){
                    $('#unitPriceDisplay').show();
                    $('#unitPrice').prop('required',true);
                    $('#subTotalPriceDisplay').show();
                    $('#sstDisplay').show();
                    $('#totalPriceDisplay').show();
                    $('#tinNoDisplay').show();
                    $('#idNoDisplay').show();
                    $('#idTypeDisplay').show();
                }else{
                    $('#unitPriceDisplay').hide();
                    $('#unitPrice').removeAttr('required');
                    $('#subTotalPriceDisplay').hide();
                    $('#sstDisplay').hide();
                    $('#totalPriceDisplay').hide();
                    $('#tinNoDisplay').hide();
                    $('#idNoDisplay').hide();
                    $('#idTypeDisplay').hide();
                }

                // Set non-related fields to empty
                $('#addModal').find('#purchaseOrder').val("").trigger('change');
                $('#addModal').find('#supplierName').val("").trigger('change');
                $('#addModal').find('#rawMaterialName').val("").trigger('change');
                $('#addModal').find('#supplierWeightBasicUom').val("").trigger('change');
                $('#addModal').find('#poSupplyWeight').val("");
                $('#addModal').find('#balance').val(0);
            }
        });

        //productName
        $('#productName').on('change', function(){
            var productId = $('#productName :selected').data('id');
            $('#productId').val(productId);
            $('#productCode').val($('#productName :selected').data('code'));
            $('#productDescription').val($('#productName :selected').data('description'));
            $('#productPrice').val($('#productName :selected').data('price'));
            $('#productHigh').val($('#productName :selected').data('high'));
            $('#productLow').val($('#productName :selected').data('low'));
            $('#productVariance').val($('#productName :selected').data('variance'));

            var price = $('#productPrice').val() ? parseFloat($('#productPrice').val()).toFixed(2) : 0.00;
            var weight = $('#currentWeight').text() ? parseFloat($('#currentWeight').text())/1000 : 0;
            var subTotalPrice = price * weight;
            // var sstPrice = subTotalPrice * 0.08;
            var sstPrice = subTotalPrice * 0;
            var totalPrice = subTotalPrice + sstPrice;

            if($('#customerType').val() != 'Cash'){
                // $('#unitPrice').val(price);
                $('#subTotalPrice').val(subTotalPrice.toFixed(2));
                $('#sstPrice').val(sstPrice.toFixed(2));
                $('#totalPrice').val(totalPrice.toFixed(2));
            }

            var salesOrder = $('#addModal').find('#salesOrder').val();
            var type = $('#addModal').find('#transactionStatus').val();
            var productName = $('#productName :selected').data('code');
            var customerCode = $('#addModal').find('#customerCode').val();
            var plant = $('#addModal').find('#plantCode').val();

            //if (salesOrder && salesOrder != '-' && plant && productName){
            if (salesOrder && salesOrder != '-' && productName && customerCode){
                //if (!isEdit){
                    $.post('php/getOrderSupplier.php', {code: salesOrder, type: type, material: productName, plant: plant, customer: customerCode}, function (data){
                        var obj = JSON.parse(data);
    
                        if (obj.status == 'success'){
                            var customerSupplierName = obj.message.customer_supplier_name;
                            var destinationName = obj.message.destination_name;
                            var siteName = obj.message.site_name;
                            var agentName = obj.message.agent_name;
                            var productName = obj.message.product_name;
                            var plantName = obj.message.plant_name;
                            var transporterName = obj.message.transporter_name;
                            var vehNo = obj.message.veh_number;
                            var batchDrum = obj.message.batch_drum;
                            var exDel = obj.message.ex_del;
                            var orderSupplierWeight = obj.message.order_supplier_weight;
                            var convertedOrderSupplierWeight = obj.message.converted_order_supplier_weight;
                            var balance = obj.message.balance; 
                            var remarks = obj.message.remarks; 
                            var unitPrice = obj.message.unit_price; 
                            // var finalWeight = obj.message.final_weight;
                            // var previousRecordsTag = obj.message.previousRecordsTag;
    
                            // Change Details
                            // if (!$('#addModal').find('#customerName').val()) {
                            //     $('#addModal').find('#customerName').val(customerSupplierName).trigger('change');
                            // }
                            if (!$('#addModal').find('#destination').val()) {
                                $('#addModal').find('#destination').val(destinationName).trigger('change');
                            }
                            if (!$('#addModal').find('#siteName').val()) {
                                $('#addModal').find('#siteName').val(siteName).trigger('change');
                            }
                            if (!$('#addModal').find('#agent').val()) {
                                $('#addModal').find('#agent').val(agentName).trigger('change');
                            }
                            // if (!$('#addModal').find('#productName').val()) {
                            //     $('#addModal').find('#productName').val(productName).trigger('change');
                            // }
                            if (!$('#addModal').find('#plant').val()) {
                                $('#addModal').find('#plant').val(plantName).trigger('change');
                            }
                            
                            if (!$('#addModal').find('#vehiclePlateNo1').val()) {
                                $('#addModal').find('#vehiclePlateNo1').val(vehNo).select2('destroy').select2();
                            }

                            if (exDel == 'E') {
                                $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true).trigger('change');
                            } else {
                                $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');
                            }

                            if (!isEdit){
                                $('#addModal').find('#transporter').val(transporterName).trigger('change');
                            }

                            $('#addModal').find('#batchDrum').val(batchDrum).trigger('change');
                            $('#addModal').find('#orderWeightBasicUom').val(convertedOrderSupplierWeight).trigger('change');
                            $('#addModal').find('#balance').val(balance);

                            if (!$('#addModal').find('#otherRemarks').val()) {
                                $('#addModal').find('#otherRemarks').val(remarks);
                            }
                            $('#addModal').find('#unitPrice').val(unitPrice).trigger('change');
                            // $('#addModal').find('#basicUOM').val(convertedOrderSupplierWeight);
                            // $('#addModal').find('#basicUOMUnit').text(convertedOrderSupplierUnit);

                            // Initialize all Select2 elements in the modal
                            $('#addModal .select2').select2({
                                allowClear: true,
                                placeholder: "Please Select",
                                dropdownParent: $('#addModal') // Ensures dropdown is not cut off
                            });

                            // Apply custom styling to Select2 elements in addModal
                            $('#addModal .select2-container .select2-selection--single').css({
                                'padding-top': '4px',
                                'padding-bottom': '4px',
                                'height': 'auto'
                            });

                            $('#addModal .select2-container .select2-selection__arrow').css({
                                'padding-top': '33px',
                                'height': 'auto'
                            });
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
                /*}
                else{
                    $('#addModal').trigger('orderLoaded');
                }*/
            }else{
                // if (!soPoTag && !addNewTag){
                //     getSoPo();
                // }
                var orderWeight = $('#addModal').find('#orderWeightBasicUom').val() || 0;
                $('#orderWeightBasicUom').val(orderWeight).trigger('change');
            }
        });

        //rawMaterialName
        $('#rawMaterialName').on('change', function(){
            $('#rawMaterialCode').val($('#rawMaterialName :selected').data('code'));
            $('#rawMaterialId').val($('#rawMaterialName :selected').data('id'));
            var purchaseOrder = $('#addModal').find('#purchaseOrder').val();
            var type = $('#addModal').find('#transactionStatus').val();
            var rawMat = $('#rawMaterialName :selected').data('code');
            var supplierCode = $('#addModal').find('#supplierCode').val();
            var plant = $('#addModal').find('#plantCode').val();

            //if (purchaseOrder && purchaseOrder != '-' && plant && rawMat){
            if (purchaseOrder && purchaseOrder != '-' && rawMat && supplierCode){
                //if (!isEdit){
                    $.post('php/getOrderSupplier.php', {code: purchaseOrder, type: type, material: rawMat, plant: plant, supplier: supplierCode}, function (data){
                        var obj = JSON.parse(data);
    
                        if (obj.status == 'success'){
                            var customerSupplierName = obj.message.customer_supplier_name;
                            var destinationName = obj.message.destination_name;
                            var siteName = obj.message.site_name;
                            var agentName = obj.message.agent_name;
                            var productName = obj.message.product_name;
                            var plantName = obj.message.plant_name;
                            var transporterName = obj.message.transporter_name;
                            var vehNo = obj.message.veh_number;
                            var batchDrum = obj.message.batch_drum;
                            var exDel = obj.message.ex_del;
                            var orderSupplierWeight = obj.message.order_supplier_weight;
                            var orderSupplierWeight = obj.message.order_supplier_weight;
                            var balance = obj.message.balance;
                            var remarks = obj.message.remarks;
                            // var finalWeight = obj.message.final_weight;
                            // var previousRecordsTag = obj.message.previousRecordsTag;
    
                            // Change Details
                            // if (!$('#addModal').find('#supplierName').val()) {
                            //     $('#addModal').find('#supplierName').val(customerSupplierName).trigger('change');
                            // }
                            if (!$('#addModal').find('#destination').val()) {
                                $('#addModal').find('#destination').val(destinationName).trigger('change');
                            }
                            if (!$('#addModal').find('#siteName').val()) {
                                $('#addModal').find('#siteName').val(siteName).trigger('change');
                            }
                            if (!$('#addModal').find('#agent').val()) {
                                $('#addModal').find('#agent').val(agentName).trigger('change');
                            }
                            // if (!$('#addModal').find('#rawMaterialName').val()) {
                            //     $('#addModal').find('#rawMaterialName').val(productName).trigger('change');
                            // }
                            if (!$('#addModal').find('#plant').val()) {
                                $('#addModal').find('#plant').val(plantName).trigger('change');
                            }
                            
                            if (!$('#addModal').find('#vehiclePlateNo1').val()) {
                                $('#addModal').find('#vehiclePlateNo1').val(vehNo).select2('destroy').select2();
                            }

                            if (exDel == 'E') {
                                $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true).trigger('change');
                            } else {
                                $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true).trigger('change');
                            }
                            
                            //$('#addModal').find('#transporter').val(transporterName).trigger('change');
                            if (!isEdit){
                                $('#addModal').find('#transporter').val(transporterName).trigger('change');
                            }
                            $('#addModal').find('#batchDrum').val(batchDrum).trigger('change');
                            $('#addModal').find('#supplierWeightBasicUom').val(0).trigger('change');
                            $('#addModal').find('#poSupplyWeight').val(orderSupplierWeight);
                            $('#addModal').find('#balance').val(balance);

                            if (!$('#addModal').find('#otherRemarks').val()) {
                                $('#addModal').find('#otherRemarks').val(remarks);
                            }
                            // $('#addModal').find('#basicUOM').val(convertedOrderSupplierWeight);
                            // $('#addModal').find('#basicUOMUnit').val(convertedOrderSupplierUnit).trigger('change');

                            // Initialize all Select2 elements in the modal
                            $('#addModal .select2').select2({
                                allowClear: true,
                                placeholder: "Please Select",
                                dropdownParent: $('#addModal') // Ensures dropdown is not cut off
                            });

                            // Apply custom styling to Select2 elements in addModal
                            $('#addModal .select2-container .select2-selection--single').css({
                                'padding-top': '4px',
                                'padding-bottom': '4px',
                                'height': 'auto'
                            });

                            $('#addModal .select2-container .select2-selection__arrow').css({
                                'padding-top': '33px',
                                'height': 'auto'
                            });
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
                /*}
                else{
                    $('#addModal').trigger('orderLoaded');
                }*/
            }
            else{
                // if (!soPoTag && !addNewTag){
                //     getSoPo();
                // }
                var supplierWeight = $('#addModal').find('#supplierWeightBasicUom').val() || 0;
                $('#supplierWeightBasicUom').val(supplierWeight).trigger('change');
            }
        });

        $('#unitPrice').on('change', function() {
            var productId = $('#addModal').find('#productId').val();
            var unitPrice = $(this).val() ? parseFloat($(this).val()).toFixed(2) : 0.00;
            var weight = $('#currentWeight').text() ? parseFloat($('#currentWeight').text()) : 0;

            if (productId && unitPrice && weight){
                calculatePrice(unitPrice, weight, productId);
            }
        });

        //supplierName
        $('#supplierName').on('change', function(){
            $('#supplierCode').val($('#supplierName :selected').data('code'));

            var purchaseOrder = $('#addModal').find('#purchaseOrder').val();
            var rawMatName = $('#addModal').find('#rawMaterialName').val();

            if (!purchaseOrder && !soPoTag && !addNewTag){
                getSoPo();
            }

            if (purchaseOrder && purchaseOrder != '-' && $(this).val() && rawMatName){
                $('#addModal').find('#rawMaterialName').trigger('change');
            }
        });

        //transporter
        $('#transporter').on('change', function(){
            if (isSyncing) return;

            $('#transporterCode').val($('#transporter :selected').data('code'));
            $('#transporterName').val($(this).val());

            isSyncing = true;

            if ($(this).val() == 'OWN TRANSPORTATION' || $(this).val() == 'OWN COLLECTION'){
                $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true);
                // $(this).attr('disabled', true);
            }else{
                $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true);
                // $(this).attr('disabled', false);
            }

            isSyncing = false;

        });

        //destination
        $('#destination').on('change', function(){
            $('#destinationCode').val($('#destination :selected').data('code'));
        });

        //plant
        $('#plant').on('change', function(){
            var plantCode = $('#plant :selected').data('code');
            var plantId = $('#plant :selected').data('id');
            $('#plantCode').val(plantCode);
            $('#plantId').val(plantId);

            if (plantId){
                $.post('php/getPlant.php', {userID: plantId}, function(data)
                {
                    var obj = JSON.parse(data);
                    if(obj.status === 'success'){
                        $('#addModal').find('#batchDrum').val(obj.message.default_type).trigger('change');
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
                    $('#spinnerLoading').hide();
                });

            }

            /*var transactionStatus = $('#addModal').find('#transactionStatus').val();
            if (transactionStatus == 'Sales'){
                $('#addModal').find('#productName').trigger('change');
            }else if(transactionStatus == 'Purchase'){
                $('#addModal').find('#rawMaterialName').trigger('change');
            }*/
        });
        
        $('#addModal').on('orderLoaded', function(e, data) {
            $('#addModal').find('#customerCode').val(data.customer_code);
            $('#addModal').find('#customerName').val(data.customer_name).trigger('change');
            $('#addModal').find('#supplierCode').val(data.supplier_code);
            $('#addModal').find('#supplierName').val(data.supplier_name).trigger('change');
            $('#addModal').find('#siteCode').val(data.site_code);
            $('#addModal').find('#siteName').val(data.site_name).trigger('change');
            $('#addModal').find('#agent').val(data.agent_name).trigger('change');
            $('#addModal').find('#agentCode').val(data.agent_code);
            $('#addModal').find('#plant').val(data.plant_name).trigger('change');
            $('#addModal').find('#plantCode').val(data.plant_code);
            $('#addModal').find('#destinationCode').val(data.destination_code);
            $('#addModal').find('#destination').val(data.destination).trigger('change');

            // Find product with correct code and name then only select
            var $prodOpt = $('#addModal').find('#productName option').filter(function(){ return $(this).data('code') == data.product_code && $(this).val() == data.product_name; });
            $('#addModal').find('#productName option:selected').prop('selected', false);
            $prodOpt.prop('selected', true);
            $('#addModal').find('#productName').trigger('change');
            
            // Find raw mat with correct code and name then only select
            var $opt = $('#addModal').find('#rawMaterialName option').filter(function(){ return $(this).data('code') == data.raw_mat_code && $(this).val() == data.raw_mat_name; });
            $('#addModal').find('#rawMaterialName option:selected').prop('selected', false);
            $opt.prop('selected', true);
            $('#addModal').find('#rawMaterialName').trigger('change');
            
            setTimeout(() => {
                $('#addModal').find('#supplierWeightBasicUom').val(data.supplier_weight_uom).trigger('change');
                $('#addModal').find('#orderWeightBasicUom').val(data.order_weight_uom).trigger('change');
                $('#addModal').find('#transporter').val(data.transporter).trigger('change');
                $('#addModal').find('#transporterCode').val(data.transporter_code);
                $('#addModal').find('#batchDrum').val(data.batch_drum).trigger('change');
            }, 500);

            // Optional: Show read-only fields instead of dropdown if needed
            // if (data.transaction_status === 'Purchase') {
            //     $('#addModal').find('#purchaseOrder').next('.select2-container').hide();
            //     $('#addModal').find('#purchaseOrderEdit').val(data.purchase_order).show();
            // } else {
            //     $('#addModal').find('#salesOrder').next('.select2-container').hide();
            //     $('#addModal').find('#salesOrderEdit').val(data.purchase_order).show();
            // }
        });

        // SRP
        $('#agent').on('change', function(){
            $('#agentCode').val($('#agent :selected').data('code'));
        });

        //customerName
        $('#customerName').on('change', function(){
            $('#customerCode').val($('#customerName :selected').data('code'));
            $('#custName').val($(this).val());

            var salesOrder = $('#addModal').find('#salesOrder').val();
            var productName = $('#addModal').find('#productName').val(); 
            
            if (!salesOrder && !soPoTag && !addNewTag){
                getSoPo();
            }

            if (salesOrder && salesOrder != '-' && $(this).val() && productName){
                $('#addModal').find('#productName').trigger('change');
            }
        });

        $('input[name="exDel"]').change(function() {
            if (isSyncing) return;

            var vehicleNo1 = $('#addModal').find('#vehiclePlateNo1').val();
            var exDel = $('input[name="exDel"]:checked').val();

            isSyncing = true;

            if (exDel == 'true'){
                /*$('#addModal').find('#transporter').val('OWN TRANSPORTATION').trigger('change.select2');
                $('#addModal').find('#transporterCode').val('T01');
                $('#addModal').find('#transporterName').val('OWN TRANSPORTATION');*/
                var $transporter = $('#addModal #transporter');

                if ($transporter.find("option[value='OWN COLLECTION']").length > 0) {
                    $transporter.val('OWN COLLECTION').trigger('change');
                    $('#addModal #transporterCode').val('OWN');
                    $('#addModal #transporterName').val('OWN COLLECTION');
                } else {
                    $transporter.val('OWN TRANSPORTATION').trigger('change');
                    $('#addModal #transporterCode').val('T01');
                    $('#addModal #transporterName').val('OWN TRANSPORTATION');
                }

                // $('#addModal').find('#transporter').val('Own Transportation').trigger('change');
                // $('#addModal').find('#transporterCode').val('T01');
                // $.post('php/getVehicle.php', {userID: vehicleNo1, type: 'lookup'}, function(data){
                //     var obj = JSON.parse(data);
                //     if(obj.status === 'success'){
                //         // var customerName = obj.message.customer_name;
                //         // var customerCode = obj.message.customer_code;

                //         // $('#addModal').find('#customerName').val(customerName).trigger('change');
                //         // $('#addModal').find('#customerCode').val(customerCode);
                //     }   
                //     else if(obj.status === 'failed'){
                //         $("#failBtn").attr('data-toast-text', obj.message );
                //         $("#failBtn").click();
                //     }
                //     else{
                //         $("#failBtn").attr('data-toast-text', obj.message );
                //         $("#failBtn").click();
                //     }
                // });
            }else{
                // $('#addModal').find('#customerName').val('').trigger('change');
                // $('#addModal').find('#customerCode').val('');

                // $.post('php/getVehicle.php', {userID: vehicleNo1, type: 'lookup'}, function (data){
                //     var obj = JSON.parse(data);

                //     if (obj.status == 'success'){
                //         // var transporterName = obj.message.transporter_name;
                //         // var transporterCode = obj.message.transporter_code;

                //         // $('#addModal').find('#transporter').val(transporterName).trigger('change');
                //         // $('#addModal').find('#transporterCode').val(transporterCode);
                //     }
                //     else if(obj.status === 'failed'){
                //         $("#failBtn").attr('data-toast-text', obj.message );
                //         $("#failBtn").click();
                //     }
                //     else{
                //         $("#failBtn").attr('data-toast-text', obj.message );
                //         $("#failBtn").click();
                //     }
                // });

                $('#addModal').find('#transporter').val('').select2('destroy').select2();

                // Initialize all Select2 elements in the modal
                $('#addModal .select2').select2({
                    allowClear: true,
                    placeholder: "Please Select",
                    dropdownParent: $('#addModal') // Ensures dropdown is not cut off
                });

                // Apply custom styling to Select2 elements in addModal
                $('#addModal .select2-container .select2-selection--single').css({
                    'padding-top': '4px',
                    'padding-bottom': '4px',
                    'height': 'auto'
                });

                $('#addModal .select2-container .select2-selection__arrow').css({
                    'padding-top': '33px',
                    'height': 'auto'
                });
            }

            isSyncing = false;
        });

        //siteName
        $('#siteName').on('change', function(){
            $('#siteCode').val($('#siteName :selected').data('code'));
        });

        $('input[name="loadDrum"]').change(function() {
            var selected = $(this).val();
            if (selected == 'true'){
                $("#noOfDrumDisplay").hide();
            }else{
                $("#noOfDrumDisplay").show();
            }
        });

        $('#purchaseOrder').on('change', function (){
            var purchaseOrder = $(this).val();
            var type = $('#addModal').find('#transactionStatus').val();

            if (purchaseOrder && !isEdit){
                // if (isEdit){
                //     $('#addModal').find('#purchaseOrder').empty();
                //     $('#addModal').find('#purchaseOrder').append(purchaseOption);
                //     $('#addModal').find('#purchaseOrder').val(purchaseOrder);
                //     //$('#addModal').trigger('orderLoaded');
                // }else{
                    $.post('php/getOrderSupplier.php', {code: purchaseOrder, type: type, format: 'getProdRaw'}, function (data){
                        var obj = JSON.parse(data);

                        if (obj.status == 'success'){
                            if (obj.message.length > 0){
                                var purchaseOrders = obj.message;
                                $('#addModal').find('#rawMaterialName').empty();
                                $('#addModal').find('#rawMaterialName').append(`<option selected="-">-</option>`);
                                for (var i = 0; i < purchaseOrders.length; i++) {
                                    // Check if option with this value already exists
                                    var existingOption = $('#addModal').find('#rawMaterialName option[value="' + purchaseOrders[i].prodMatName + '"]');
                                    if (existingOption.length === 0) {
                                        $('#addModal').find('#rawMaterialName').append(
                                            `<option value="${purchaseOrders[i].prodMatName}" data-id="${purchaseOrders[i].prodMatId}" data-code="${purchaseOrders[i].prodMatCode}">${purchaseOrders[i].prodMatCode} - ${purchaseOrders[i].prodMatName}</option>`
                                        );
                                    }                   
                                }

                                // Supplier Logic
                                var supplierName = $('#addModal').find('#supplierName').val();

                                $('#addModal').find('#supplierName').empty();
                                $('#addModal').find('#supplierName').append(`<option selected="-">-</option>`);
                                for (var i = 0; i < purchaseOrders.length; i++) {
                                    // Check if option with this value already exists
                                    var existingOption = $('#addModal').find('#supplierName option[value="' + purchaseOrders[i].custSuppName + '"]');
                                    if (existingOption.length === 0) {
                                        $('#addModal').find('#supplierName').append(
                                            `<option value="${purchaseOrders[i].custSuppName}" data-id="${purchaseOrders[i].custSuppId}" data-code="${purchaseOrders[i].custSuppCode}">${purchaseOrders[i].custSuppName}</option>`
                                        );
                                    }                   
                                }

                                if (!supplierName){
                                    var suppCount = $('#addModal').find('#supplierName option').length;
                                    if (suppCount == 2){
                                        $('#addModal').find('#supplierName').val(purchaseOrders[0].custSuppName).trigger('change');
                                    }
                                }else{
                                    $('#addModal').find('#supplierName').val(supplierName).trigger('change');
                                }
                            }else{
                                $('#addModal').find('#rawMaterialName').empty();
                                $('#addModal').find('#rawMaterialName').append(rawMaterialOption);
                                $('#addModal').find('#rawMaterialName').val('').trigger('change');
                                $('#addModal').find('#supplierName').empty();
                                $('#addModal').find('#supplierName').append(supplierOption);
                                $('#addModal').find('#supplierName').val('').trigger('change');
                            }

                            //$('#addModal').trigger('orderLoaded');
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
                // }
            }
        });

        $('#salesOrder').on('change', function (){
            var salesOrder = $(this).val();
            var type = $('#addModal').find('#transactionStatus').val(); 

            if (salesOrder && !isEdit){
                // if (isEdit){
                //     $('#addModal').find('#salesOrder').empty();
                //     $('#addModal').find('#salesOrder').append(salesOption);
                //     $('#addModal').find('#salesOrder').val(salesOrder);
                //     //$('#addModal').trigger('orderLoaded');
                // }else{
                    $.post('php/getOrderSupplier.php', {code: salesOrder, type: type, format: 'getProdRaw'}, function (data){
                        var obj = JSON.parse(data);

                        if (obj.status == 'success'){
                            if (obj.message.length > 0){
                                var salesOrders = obj.message;

                                // Product Logic
                                $('#addModal').find('#productName').empty();
                                $('#addModal').find('#productName').append(`<option selected="-">-</option>`);
                                for (var i = 0; i < salesOrders.length; i++) {
                                    // Check if option with this value already exists
                                    var existingOption = $('#addModal').find('#productName option[value="' + salesOrders[i].prodMatName + '"]');
                                    if (existingOption.length === 0) {
                                        $('#addModal').find('#productName').append(
                                            `<option value="${salesOrders[i].prodMatName}" data-id="${salesOrders[i].prodMatId}" data-code="${salesOrders[i].prodMatCode}">${salesOrders[i].prodMatCode} - ${salesOrders[i].prodMatName}</option>`
                                        );
                                    }                   
                                }

                                // Customer Logic
                                var customerName = $('#addModal').find('#customerName').val();

                                $('#addModal').find('#customerName').empty();
                                $('#addModal').find('#customerName').append(`<option selected="-">-</option>`);
                                for (var i = 0; i < salesOrders.length; i++) {
                                    // Check if option with this value already exists
                                    var existingOption = $('#addModal').find('#customerName option[value="' + salesOrders[i].custSuppName + '"]');
                                    if (existingOption.length === 0) {
                                        $('#addModal').find('#customerName').append(
                                            `<option value="${salesOrders[i].custSuppName}" data-id="${salesOrders[i].custSuppId}" data-code="${salesOrders[i].custSuppCode}">${salesOrders[i].custSuppName}</option>`
                                        );
                                    }                   
                                }

                                if (!customerName){
                                    var custCount = $('#addModal').find('#customerName option').length;
                                    if (custCount == 2){
                                        $('#addModal').find('#customerName').val(salesOrders[0].custSuppName).trigger('change');
                                    }
                                }else{
                                    $('#addModal').find('#customerName').val(customerName).trigger('change');
                                }
                            }else{
                                $('#addModal').find('#productName').empty();
                                $('#addModal').find('#productName').append(productOption);
                                $('#addModal').find('#productName').val('').trigger('change');
                                $('#addModal').find('#customerName').empty();
                                $('#addModal').find('#customerName').append(customerOption);
                                $('#addModal').find('#customerName').val('').trigger('change');
                            }

                            //$('#addModal').trigger('orderLoaded');
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
                // }
            }
        });

        $('#orderWeightBasicUom').on('change', function(){
            var value = $(this).val();
            var productId = $('#addModal').find('#productId').val();
            var transactionStatus = $('#addModal').find('#transactionStatus').val();
            convertWeight(value, productId, transactionStatus, function (result) {
                $('#orderWeight').val(parseFloat(result.convertedValue).toFixed(0)).trigger('change');
                $('#orderWeightUnit').text(result.basicUomLabel);
            });
        });

        $('#supplierWeightBasicUom').on('change', function(){
            var value = $(this).val();
            var rawMatId = $('#addModal').find('#rawMaterialId').val();
            var transactionStatus = $('#addModal').find('#transactionStatus').val();
            convertWeight(value, rawMatId, transactionStatus, function (result) {
                $('#supplierWeight').val(parseFloat(result.convertedValue).toFixed(0)).trigger('change');
                $('#supplierWeightUnit').text(result.basicUomLabel);
            });
        });

        //basicUOM
        // $('#basicUOM').on('change', function(){
        //     var value = $(this).val();
        //     var transactionStatus = $('#transactionStatus').val();
        //     var unit = $('#orderWeightUnitId').val();
        //     = '';

        //     if (transactionStatus == 'Purchase'){
        //         prodRawMatCode = $('#rawMaterialName :selected').data('id');
        //     }else{
        //         prodRawMatCode = $('#productName :selected').data('id');
        //     }
            
        //     if (unit == 2){
        //         $('#basicUOM').val(value);
        //         var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
        //         var nett2 = $('#basicUOM').val() ? $('#basicUOM').val() : 0;
        //         var current = nett1 - nett2;
        //         $('#weightDifference').val(current.toFixed(0));

        //         var previousRecordsTag = $('#addModal').find('#previousRecordsTag').val();

        //         if (previousRecordsTag == 'false'){
        //             $('#addModal').find('#balance').val($(this).val());
        //             if ($(this).val() <= 0) {
        //                 $('#addModal').find('#insufficientBalDisplay').hide();
        //             } else {
        //                 $('#addModal').find('#insufficientBalDisplay').show();
        //             }
        //         }
        //     }else{
        //         // Call to backend to get conversion rate
        //         if (value && prodRawMatCode){
        //             if (transactionStatus == 'Purchase'){
        //                 $.post('php/getProdRawMatUOM.php', {userID: prodRawMatCode, type: 'PO'}, function(data)
        //                 {
        //                     var obj = JSON.parse(data);
        //                     if(obj.status === 'success'){
        //                         var rate = parseFloat(obj.message.rate);
        //                         var orderQty = value/rate;

        //                         $('#basicUOM').val(orderQty);
        //                     }
        //                     else if(obj.status === 'failed'){
        //                         showAppAlert(obj.message);
        //                         $("#failBtn").attr('data-toast-text', obj.message );
        //                         $("#failBtn").click();
        //                     }
        //                     else{
        //                         showAppAlert(obj.message);
        //                         $("#failBtn").attr('data-toast-text', obj.message );
        //                         $("#failBtn").click();
        //                     }
        //                 });
        //             }else{
        //                 $.post('php/getProdRawMatUOM.php', {userID: prodRawMatCode, type: 'SO'}, function(data)
        //                 {
        //                     var obj = JSON.parse(data);
        //                     if(obj.status === 'success'){
        //                         var rate = parseFloat(obj.message.rate);
        //                         var orderQty = value/rate;

        //                         $('#basicUOM').val(orderQty);
        //                         var nett1 = $('#finalWeight').val() ? parseFloat($('#finalWeight').val()) : 0;
        //                         var nett2 = $('#basicUOM').val() ? $('#basicUOM').val() : 0;
        //                         var current = nett1 - nett2;
        //                         $('#weightDifference').val(current.toFixed(0));

        //                         var previousRecordsTag = $('#addModal').find('#previousRecordsTag').val();

        //                         if (previousRecordsTag == 'false'){
        //                             $('#addModal').find('#balance').val($(this).val());
        //                             if ($(this).val() <= 0) {
        //                                 $('#addModal').find('#insufficientBalDisplay').hide();
        //                             } else {
        //                                 $('#addModal').find('#insufficientBalDisplay').show();
        //                             }
        //                         }
        //                     }
        //                     else if(obj.status === 'failed'){
        //                         showAppAlert(obj.message);
        //                         $("#failBtn").attr('data-toast-text', obj.message );
        //                         $("#failBtn").click();
        //                     }
        //                     else{
        //                         showAppAlert(obj.message);
        //                         $("#failBtn").attr('data-toast-text', obj.message );
        //                         $("#failBtn").click();
        //                     }
        //                 });
        //             }
        //         }
        //     }
        // });

        $('#submitCancel').on('click', function(){
            if($('#cancelForm').valid()){
                $('#spinnerLoading').show();
                var id = $('#cancelModal').find('#id').val();
                $.post('php/deleteWeight.php', $('#cancelForm').serialize(), function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        table.ajax.reload();
                        $('#spinnerLoading').hide();
                        $('#cancelModal').modal('hide');
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
        });
    });

    // Function to filter transaction status 
    function filterTransactionStatus(action, currentVal) {
        var $select = $('#transactionStatus');
        $select.empty();
        var weighing = permissions['Weighing'] || {};
        transactionStatusOption.each(function() {
            var val = $(this).val();
            var permKey = (val === 'Local') ? 'Public' : val;
            if (isSADMIN || (weighing[permKey] && weighing[permKey].includes(action)) || val === currentVal) {
                $select.append($(this).clone());
            }
        });
    }

    // Function to convert basic uom to kg
    function convertWeight(value, productRawMatId, transactionStatus, callback) {
        if (productRawMatId && transactionStatus) {
            $('#spinnerLoading').show();

            var url = transactionStatus === 'Purchase'
                ? 'php/getRawMaterial.php'
                : 'php/getProduct.php';

            $.post(url, { userID: productRawMatId }, function (data) {
                var obj = JSON.parse(data);
                var conversionData;

                if (obj.status === 'success') {
                    // KG rate (and its default when none is set) comes from the backend: getKgConversion in php/requires/lookup.php
                    var basicUomLabel = obj.message.kg_basic_uom;
                    var rate = parseFloat(obj.message.kg_rate);

                    conversionData = {
                        basicUomLabel,
                        rate,
                        convertedValue: parseFloat(value) / rate
                    };
                } else {
                    conversionData = {
                        basicUomLabel: '',
                        rate: 1,
                        convertedValue: parseFloat(value)
                    };
                }

                $('#spinnerLoading').hide();
                callback(conversionData); 
            });
        } else {
            callback({
                basicUomLabel: 'KG',
                rate: 1,
                convertedValue: parseFloat(value)
            });
        }
    }

    function isManualReasonRequired() {
        var manualWeight = $('#addModal').find('input[name="manualWeight"]:checked').val();
        var hasIncoming = $.trim($('#addModal').find('#grossIncoming').val()) !== '' || $.trim($('#addModal').find('#grossIncoming2').val()) !== '';
        var hasOutgoing = $.trim($('#addModal').find('#tareOutgoing').val()) !== '' || $.trim($('#addModal').find('#tareOutgoing2').val()) !== '';

        return manualWeight == 'true' && (hasIncoming || hasOutgoing);
    }

    function validateManualWeightReason(reason) {
        var cleaned = $.trim(reason || '').replace(/\s+/g, ' ');
        var lowerReason = cleaned.toLowerCase();
        var dummyReasons = [
            'test', 'testing', 'dummy', 'na', 'n/a', 'nil', 'none', 'no',
            'no reason', 'reason', 'manual', 'manual weighing', 'manual weight',
            '-', '--', '.', '..', 'abc', 'abcd', 'asdf', 'qwerty', '123', '1234'
        ];

        if (cleaned.length < 8) {
            return false;
        }

        if (dummyReasons.indexOf(lowerReason) !== -1) {
            return false;
        }

        if (!/[a-zA-Z]/.test(cleaned) || /^([a-zA-Z0-9])\1+$/.test(cleaned.replace(/\s+/g, ''))) {
            return false;
        }

        return true;
    }

    function showManualWeightReasonError(message) {
        $('#manualWeightReasonPrompt').addClass('is-invalid');
        $('#manualWeightReasonError').text(message);
    }

    function clearManualWeightReasonError() {
        $('#manualWeightReasonPrompt').removeClass('is-invalid');
        $('#manualWeightReasonError').text('');
    }

    var pendingManualWeightReason = null;

    function applyPendingManualWeightReason() {
        if (pendingManualWeightReason !== null) {
            $('#addModal').find('#manualWeightReason').val(pendingManualWeightReason);
            $('#addModal').find('#manualWeightReasonInput').val(pendingManualWeightReason);
            $('#addModal').find('#manualWeightReasonDisplay').show();
            pendingManualWeightReason = null;
        }
    }

    function clearPendingManualWeightReason() {
        pendingManualWeightReason = null;
    }

    function setClosedSalesOrderSubmissionState(isBlocked) {
        closedSalesOrderSubmissionBlocked = isBlocked === true || isBlocked == '1';
        $('#closedSalesOrderWarning').toggle(closedSalesOrderSubmissionBlocked);
        $('#submitWeightPrint, #submitWeightCancel, #submitWeight').prop('disabled', closedSalesOrderSubmissionBlocked);
    }

    function isClosedSalesOrderSubmissionBlocked() {
        if (closedSalesOrderSubmissionBlocked) {
            showAppAlert('The Sales Order close, please contact Admin');
            return true;
        }

        return false;
    }

    function requireManualWeightReason(callback) {
        var existingReason = $('#addModal').find('#manualWeightReasonInput').val();

        if (!isManualReasonRequired() || validateManualWeightReason(existingReason)) {
            clearPendingManualWeightReason();
            callback();
            return;
        }

        $('#manualWeightReasonPrompt').val(existingReason);
        $('#submitManualWeightReason').prop('disabled', false);
        clearManualWeightReasonError();
        $('#manualWeightReasonModal').data('callback', callback).modal('show');
    }

    // Editing a completed transaction requires a reason
    function requireEditReason(callback) {
        var existingReason = $('#addModal').find('#editReasonInput').val();

        if (!editIsComplete || validateManualWeightReason(existingReason)) {
            callback();
            return;
        }

        $('#editReasonPrompt').val(existingReason).removeClass('is-invalid');
        $('#editReasonError').text('');
        $('#submitEditReason').prop('disabled', false);
        $('#editReasonModal').data('callback', callback).modal('show');
    }

    function showAppConfirm(title, message, confirmLabel, confirmClass, callback, cancelLabel, cancelCallback) {
        $('#confirmDialogModalTitle').text(title);
        $('#confirmDialogMessage').text(message);
        $('#confirmDialogCancel').text(cancelLabel || 'Close');
        var confirmButton = $('#confirmDialogSubmit');
        confirmButton
            .text(confirmLabel || 'Submit')
            .removeClass('btn-primary btn-danger btn-warning btn-success btn-info')
            .addClass(confirmClass || 'btn-primary')
            .prop('disabled', true);

        $('#confirmDialogModal')
            .data('callback', callback)
            .data('cancelCallback', cancelCallback)
            .data('confirmed', false)
            .modal('show');

        setTimeout(function(){
            confirmButton.prop('disabled', false);
        }, 400);
    }

    function showAppAlert(message) {
        $("#failBtn").attr('data-toast-text', message);
        $("#failBtn").click();
    }

    $('#manualWeightReasonModal, #editReasonModal, #confirmDialogModal').on('show.bs.modal', function(){
        var zIndex = 1055 + (10 * $('.modal.show').length);
        $(this).css('z-index', zIndex);

        setTimeout(function(){
            $('.modal-backdrop').not('.modal-stack').last().css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    $('#manualWeightReasonModal, #editReasonModal, #confirmDialogModal').on('hidden.bs.modal', function(){
        $(this).css('z-index', '');

        if ($('#addModal').hasClass('show')) {
            $('body').addClass('modal-open');
        }

        if ($(this).attr('id') == 'confirmDialogModal') {
            var cancelCallback = $(this).data('cancelCallback');

            if (!$(this).data('confirmed') && $.isFunction(cancelCallback)) {
                cancelCallback();
            }
        }
    });

    $('#submitManualWeightReason').on('click', function(){
        var reasonButton = $(this);
        var reason = $('#manualWeightReasonPrompt').val();

        if (!validateManualWeightReason(reason)) {
            showManualWeightReasonError('Please enter a meaningful reason with at least 8 characters.');
            return;
        }

        reasonButton.prop('disabled', true);
        var cleaned = $.trim(reason).replace(/\s+/g, ' ');
        var callback = $('#manualWeightReasonModal').data('callback');

        pendingManualWeightReason = cleaned;
        $('#manualWeightReasonModal').one('hidden.bs.modal', function(){
            reasonButton.prop('disabled', false);
            if ($.isFunction(callback)) {
                callback();
            }
        });
        $('#manualWeightReasonModal').modal('hide');
    });

    $('#manualWeightReasonPrompt').on('input', function(){
        clearManualWeightReasonError();
    });

    $('#submitEditReason').on('click', function(){
        var reasonButton = $(this);
        var reason = $('#editReasonPrompt').val();

        if (!validateManualWeightReason(reason)) {
            $('#editReasonPrompt').addClass('is-invalid');
            $('#editReasonError').text('Please enter a meaningful reason with at least 8 characters.');
            return;
        }

        reasonButton.prop('disabled', true);
        var callback = $('#editReasonModal').data('callback');

        $('#addModal').find('#editReasonInput').val($.trim(reason).replace(/\s+/g, ' '));
        $('#editReasonModal').one('hidden.bs.modal', function(){
            reasonButton.prop('disabled', false);
            if ($.isFunction(callback)) {
                callback();
            }
        });
        $('#editReasonModal').modal('hide');
    });

    $('#editReasonPrompt').on('input', function(){
        $(this).removeClass('is-invalid');
        $('#editReasonError').text('');
    });

    $('#confirmDialogSubmit').on('click', function(){
        $(this).prop('disabled', true);
        var callback = $('#confirmDialogModal').data('callback');

        $('#confirmDialogModal').data('confirmed', true);
        $('#confirmDialogModal').modal('hide');

        if ($.isFunction(callback)) {
            callback();
        }
    });

    // Function to handle weight form submission without printing
    function submitWeightForm() {
        if (isClosedSalesOrderSubmissionBlocked()) {
            return;
        }

        var nettWeight = $('#addModal').find('#nettWeight').val();
        var transactionStatus = $('#addModal').find('#transactionStatus').val();
        var purchaseOrder = (transactionStatus == 'Sales' ? $('#addModal').find('#salesOrder').val() : $('#addModal').find('#purchaseOrder').val());
        var customerSupplier = (transactionStatus == 'Sales' ? $('#addModal').find('#customerName').val() : $('#addModal').find('#supplierName').val());
        var product = (transactionStatus == 'Sales' ? $('#addModal').find('#productName').val() : $('#addModal').find('#rawMaterialName').val());
        var transporter = $('#addModal').find('#transporterCode').val();
        var destination = $('#addModal').find('#destinationCode').val();
        var product = (transactionStatus == 'Sales' ? $('#addModal').find('#orderWeight').val() : $('#addModal').find('#supplierWeight').val());

        var msg = 
        "Purchase Order: " + purchaseOrder + "\n" +
        "Customer/Supplier: " + customerSupplier + "\n" +
        "Product/Raw Mat: " + product + "\n" +
        "Transporter: " + transporter + "\n" +
        "Destination: " + destination + "\n" + 
        "Supplier/Order Weight: " + nettWeight + "\n\n" +
        "Confirm submit?";

        if (nettWeight > 0){
            if ($('#weightForm').valid()){
                showAppConfirm('Confirm Submit', msg, 'Submit', 'btn-primary', function(){
                applyPendingManualWeightReason();
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        <?php
                            if(isset($_GET['weight'])){
                                echo "window.location = weighingModalPage;";
                            }
                        ?>
                        table.ajax.reload();
                        window.location = weighingModalPage;
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
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
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
                }, 'Close', clearPendingManualWeightReason);
            }
        }else{
            showAppAlert('Nett Weight must be more than 0');
            return;
        }
    }

    // Function to handle weight form submission with printing
    function submitWeightPrintForm() {
        if (isClosedSalesOrderSubmissionBlocked()) {
            return;
        }

        var nettWeight = $('#addModal').find('#nettWeight').val();
        var transactionStatus = $('#addModal').find('#transactionStatus').val();
        var purchaseOrder = (transactionStatus == 'Sales' ? $('#addModal').find('#salesOrder').val() : $('#addModal').find('#purchaseOrder').val());
        var customerSupplier = (transactionStatus == 'Sales' ? $('#addModal').find('#customerName').val() : $('#addModal').find('#supplierName').val());
        var product = (transactionStatus == 'Sales' ? $('#addModal').find('#productName').val() : $('#addModal').find('#rawMaterialName').val());
        var transporter = $('#addModal').find('#transporterCode').val();
        var destination = $('#addModal').find('#destinationCode').val();
        var product = (transactionStatus == 'Sales' ? $('#addModal').find('#orderWeight').val() : $('#addModal').find('#supplierWeight').val());

        var msg = 
        "Purchase Order: " + purchaseOrder + "\n" +
        "Customer/Supplier: " + customerSupplier + "\n" +
        "Product/Raw Mat: " + product + "\n" +
        "Transporter: " + transporter + "\n" +
        "Destination: " + destination + "\n" + 
        "Supplier/Order Weight: " + nettWeight + "\n\n" +
        "Confirm submit?";

        if (nettWeight > 0){
            if ($('#weightForm').valid()){
                showAppConfirm('Confirm Submit', msg, 'Submit', 'btn-danger', function(){
                applyPendingManualWeightReason();
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();

                        $.post('php/print.php', {userID: obj.id, file: 'weight', prePrint: 'Y'}, function(data){
                            var obj2 = JSON.parse(data);

                            if(obj2.status === 'success'){
                                var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                                printWindow.document.write(obj2.message);
                                printWindow.document.close();
                                setTimeout(function(){
                                    printWindow.print();
                                    printWindow.close();
                                    table.ajax.reload();
                                    
                                    setTimeout(function () {
                                        showAppConfirm('Confirm Reprint', 'Do you need to reprint?', 'Reprint', 'btn-primary', function(){
                                            $.post('php/print.php', { userID: obj.id, file: 'weight', prePrint: 'Y'}, function (data) {
                                                var obj = JSON.parse(data);
                                                if (obj.status === 'success') {
                                                    var reprintWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
                                                    reprintWindow.document.write(obj.message);
                                                    reprintWindow.document.close();
                                                    setTimeout(function () {
                                                        reprintWindow.print();
                                                        reprintWindow.close();
                                                        <?php
                                                            if(isset($_GET['weight'])){
                                                                echo "window.location = weighingModalPage;";
                                                            }
                                                        ?>
                                                    }, 500);
                                                } 
                                                else {
                                                    window.location = weighingModalPage;
                                                }
                                            });
                                        }, 'No', function(){
                                            <?php
                                                if(isset($_GET['weight'])){
                                                    echo "window.location = weighingModalPage;";
                                                }
                                            ?>
                                        });
                                    }, 500);
                                }, 500);
                            }
                            else if(obj2.status === 'failed'){
                                $("#failBtn").attr('data-toast-text', obj2.message );
                                $("#failBtn").click();
                            }
                            else{
                                $("#failBtn").attr('data-toast-text', "Something wrong when print");
                                $("#failBtn").click();
                            }
                        });
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
                }, 'Close', clearPendingManualWeightReason);
            }
        }
        else{
            showAppAlert('Nett Weight must be more than 0');
            return;
        }
    }

    // Function to handle weight form submission with cancel
    function submitWeightCancelForm() {
        if (isClosedSalesOrderSubmissionBlocked()) {
            return;
        }

        var nettWeight = $('#addModal').find('#nettWeight').val();
        var transactionStatus = $('#addModal').find('#transactionStatus').val();
        var purchaseOrder = (transactionStatus == 'Sales' ? $('#addModal').find('#salesOrder').val() : $('#addModal').find('#purchaseOrder').val());
        var customerSupplier = (transactionStatus == 'Sales' ? $('#addModal').find('#customerName').val() : $('#addModal').find('#supplierName').val());
        var product = (transactionStatus == 'Sales' ? $('#addModal').find('#productName').val() : $('#addModal').find('#rawMaterialName').val());
        var transporter = $('#addModal').find('#transporterCode').val();
        var destination = $('#addModal').find('#destinationCode').val();
        var product = (transactionStatus == 'Sales' ? $('#addModal').find('#orderWeight').val() : $('#addModal').find('#supplierWeight').val());

        var msg = 
        "Purchase Order: " + purchaseOrder + "\n" +
        "Customer/Supplier: " + customerSupplier + "\n" +
        "Product/Raw Mat: " + product + "\n" +
        "Transporter: " + transporter + "\n" +
        "Destination: " + destination + "\n" + 
        "Supplier/Order Weight: " + nettWeight + "\n\n" +
        "Confirm submit?";

        if (nettWeight > 0){
            if ($('#weightForm').valid()){
                showAppConfirm('Confirm Submit', msg, 'Submit', 'btn-warning', function(){
                applyPendingManualWeightReason();
                $('#spinnerLoading').show();
                $.post('php/weight.php', $('#weightForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    if(obj.status === 'success'){
                        $('#spinnerLoading').hide();
                        $('#addModal').modal('hide');
                        $("#successBtn").attr('data-toast-text', obj.message);
                        $("#successBtn").click();

                        // Open Cancel Modal
                        $('#cancelModal').find('#id').val(obj.id);
                        $('#cancelModal').modal('show');

                        $('#cancelForm').validate({
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
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', 'Failed to save');
                        $("#failBtn").click();
                    }
                });
                }, 'Close', clearPendingManualWeightReason);
            }
        }
        else{
            showAppAlert('Nett Weight must be more than 0');
            return;
        }
    }

    function getSoPo(){
        var transactionStatus = $('#addModal').find('#transactionStatus').val();
        var manualVehicle = $('#addModal').find('#manualVehicle').val();
        var transporter = $('#addModal').find('#transporter').val();

        var vehicle = '';
        if (manualVehicle == '1'){
            vehicle = $('#addModal').find('#vehicleNoTxt').val();
        }else{
            vehicle = $('#addModal').find('#vehiclePlateNo1').val();
        }

        soPoTag = true;
        if (transactionStatus == 'Purchase'){
            var customerSupplier = $('#addModal').find('#supplierName').val();
            // var options = $('#purchaseOrder option').clone();

            if (isEdit){
                $('#addModal').find('#purchaseOrder').empty();
                $('#addModal').find('#purchaseOrder').append(purchaseOption);
            }else{
                $.post('php/getOrderSupplier.php', {type: transactionStatus, format: 'getSoPo', vehicle: vehicle, transporter: transporter, customerSupplier: customerSupplier}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            var soPo = obj.message;
                            $('#addModal').find('#purchaseOrder').empty();
                            $('#addModal').find('#purchaseOrder').append(`<option selected="Pending PO verification">Pending PO verification</option>`);
                            for (var i = 0; i < soPo.length; i++) {
                                // Check if option with this value already exists
                                var existingOption = $('#addModal').find('#purchaseOrder option[value="' + soPo[i] + '"]');
                                if (existingOption.length === 0) {
                                    $('#addModal').find('#purchaseOrder').append(
                                        `<option value="${soPo[i]}">${soPo[i]}</option>`
                                    );
                                }                   
                            }

                            if ($('#addModal').find('#purchaseOrder option').length == 2){
                                $('#addModal').find('#purchaseOrder').val(soPo[0]).trigger('change');
                            }else{
                                $('#addModal').find('#purchaseOrder').val("");
                            }
                        }else{
                            $('#addModal').find('#purchaseOrder').empty();
                        }

                        soPoTag = false;
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                        soPoTag = false;
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                        soPoTag = false;
                    }
                });
            }
            
        }else if (transactionStatus == 'Sales'){
            var customerSupplier = $('#addModal').find('#customerName').val();

            if (isEdit){
                $('#addModal').find('#salesOrder').empty();
                $('#addModal').find('#salesOrder').append(salesOption);
            }else{
                $.post('php/getOrderSupplier.php', {type: transactionStatus, format: 'getSoPo', vehicle: vehicle, transporter: transporter, customerSupplier: customerSupplier}, function (data){
                    var obj = JSON.parse(data);

                    if (obj.status == 'success'){
                        if (obj.message.length > 0){
                            var soPo = obj.message;
                            $('#addModal').find('#salesOrder').empty();
                            $('#addModal').find('#salesOrder').append(`<option selected="-">-</option>`);
                            for (var i = 0; i < soPo.length; i++) {
                                // Check if option with this value already exists
                                var existingOption = $('#addModal').find('#salesOrder option[value="' + soPo[i] + '"]');
                                if (existingOption.length === 0) {
                                    $('#addModal').find('#salesOrder').append(
                                        `<option value="${soPo[i]}">${soPo[i]}</option>`
                                    );
                                }                   
                            }

                            if ($('#addModal').find('#salesOrder option').length == 2){
                                $('#addModal').find('#salesOrder').val(soPo[0]).trigger('change');
                            }else{
                                $('#addModal').find('#salesOrder').val("");
                            }
                        }else{
                            $('#addModal').find('#salesOrder').empty();
                        }

                        soPoTag = false;
                    }
                    else if(obj.status === 'failed'){
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                        soPoTag = false;
                    }
                    else{
                        $('#spinnerLoading').hide();
                        $("#failBtn").attr('data-toast-text', obj.message );
                        $("#failBtn").click();
                        soPoTag = false;
                    }
                });
            }
        }
    }

    function calculatePrice(unitPrice, weight, productId){
        var subTotalPrice = 0;
        var sstPrice = 0;
        var totalPrice = 0;

        if (productId){
            $.post('php/getProdRawMatUOM.php', {userID: productId, unitId: 2, type: 'SO'}, function(data)
            {
                var obj = JSON.parse(data);
                if(obj.status === 'success'){
                    var rate = parseFloat(obj.message.rate);
                    var basicWeight = weight * rate;

                    subTotalPrice = unitPrice * basicWeight;
                    // var sstPrice = subTotalPrice * 0.08;
                    sstPrice = subTotalPrice * 0;
                    totalPrice = subTotalPrice + sstPrice;

                    $('#subTotalPrice').val(subTotalPrice.toFixed(2));
                    $('#sstPrice').val(sstPrice.toFixed(2));
                    $('#totalPrice').val(totalPrice.toFixed(2));
                }
                else if(obj.status === 'failed'){
                    showAppAlert(obj.message);
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
                else{
                    showAppAlert(obj.message);
                    $("#failBtn").attr('data-toast-text', obj.message );
                    $("#failBtn").click();
                }
            });
        }else{
            subTotalPrice = unitPrice * weight/1000;
            // var sstPrice = subTotalPrice * 0.08;
            sstPrice = subTotalPrice * 0;
            totalPrice = subTotalPrice + sstPrice;

            $('#subTotalPrice').val(subTotalPrice.toFixed(2));
            $('#sstPrice').val(sstPrice.toFixed(2));
            $('#totalPrice').val(totalPrice.toFixed(2));
        }
    }

    function edit(id){ 
        isEdit = true;
        setClosedSalesOrderSubmissionState(false);
        $('#spinnerLoading').show();
        $.post('php/getWeight.php', {userID: id}, function(data)
        {
            var obj = JSON.parse(data);
            if(obj.status === 'success'){
                setClosedSalesOrderSubmissionState(obj.message.sales_order_closed);
                editIsComplete = obj.message.is_complete == 'Y';

                if(obj.message.is_complete == 'Y'){
                    // Hide Capture Button When Edit
                    $('#addModal').find('#grossCapture').hide();
                    $('#addModal').find('#tareCapture').hide();
                }
                else{
                    // Show Capture Button When Edit
                    $('#addModal').find('#grossCapture').show();
                    $('#addModal').find('#tareCapture').show();
                }

                $('#addModal').find('#id').val(obj.message.id);
                $('#addModal').find('#tinNo').val(obj.message.tin_no);
                $('#addModal').find('#idNo').val(obj.message.id_no);
                $('#addModal').find('#idType').val(obj.message.id_type);
                $('#addModal').find('#transactionId').val(obj.message.transaction_id);
                $('#addModal').find('#transactionStatus').val(obj.message.transaction_status).trigger('change').prop('disabled', true); // Disable changing transaction status on edit
                $('#addModal').find('form').append('<input type="hidden" name="transactionStatus" value="' + obj.message.transaction_status + '">'); // Add hidden input to preserve transaction status to pass to backend
                $('#addModal').find('#weightType').val(obj.message.weight_type).trigger('change');
                $('#addModal').find('#customerType').val(obj.message.customer_type).trigger('change');
                $('#addModal').find('#transactionDate').val(formatDate2(new Date(obj.message.transaction_date)));

                if(obj.message.transaction_status == "Purchase"){
                    $('#divWeightDifference').show();
                    $('#divSupplierWeight').show();
                    $('#divSupplierName').show();
                    $('#divOrderWeight').hide();
                    $('#divCustomerName').hide();
                }
                else{
                    $('#divOrderWeight').show();
                    $('#divWeightDifference').show();
                    $('#divSupplierWeight').hide();
                    $('#divSupplierName').hide();
                    $('#divCustomerName').show();
                }

                if(obj.message.vehicleNoTxt != null){
                    $('#addModal').find('#vehicleNoTxt').val(obj.message.vehicleNoTxt);
                    $('#manualVehicle').val(1);
                    $('#manualVehicle').prop("checked", true);
                    $('.index-vehicle').hide();
                    $('#vehicleNoTxt').show();
                }
                else{
                    $('#addModal').find('#vehiclePlateNo1Edit').val('EDIT');
                    $('#addModal').find('#vehiclePlateNo1').val(obj.message.lorry_plate_no1).trigger('change');
                    $('#manualVehicle').val(0);
                    $('#manualVehicle').prop("checked", false);
                    $('.index-vehicle').show();
                    $('#vehicleNoTxt').hide();
                }

                if(obj.message.vehicleNoTxt2 != null){
                    $('#addModal').find('#vehicleNoTxt2').val(obj.message.vehicleNoTxt2);
                    $('#manualVehicle2').val(1);
                    $('#manualVehicle2').prop("checked", true);
                    $('.index-vehicle2').hide();
                    $('#vehicleNoTxt2').show();
                }
                else{
                    $('#addModal').find('#vehiclePlateNo2').val(obj.message.lorry_plate_no2);
                    $('#manualVehicle2').val(0);
                    $('#manualVehicle2').prop("checked", false);
                    $('.index-vehicle2').show();
                    $('#vehicleNoTxt2').hide();
                }
                
                $('#addModal').find('#productCode').val(obj.message.product_code);
                if (obj.message.ex_del == 'EX'){
                    $('#addModal').find("input[name='exDel'][value='true']").prop("checked", true);
                }else{
                    $('#addModal').find("input[name='exDel'][value='false']").prop("checked", true);
                }
                
                $('#addModal').find('#containerNo').val(obj.message.container_no);
                $('#addModal').find('#poSupplyWeight').val(obj.message.po_supply_weight);
                $('#addModal').find('#invoiceNo').val(obj.message.invoice_no);
                $('#addModal').find('#deliveryNo').val(obj.message.delivery_no);
                $('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                $('#addModal').find('#transporter').val(obj.message.transporter).trigger('change');
                $('#addModal').find('#otherRemarks').val(obj.message.remarks);
                var manualWeightReason = $.trim(obj.message.manual_weight_reason || '');
                $('#addModal').find('#manualWeightReason').val(manualWeightReason);
                $('#addModal').find('#manualWeightReasonInput').val(manualWeightReason);
                if (manualWeightReason != '') {
                    $('#addModal').find('#manualWeightReasonDisplay').show();
                } else {
                    $('#addModal').find('#manualWeightReasonDisplay').hide();
                }
                var editReason = $.trim(obj.message.edit_reason || '');
                $('#addModal').find('#editReason').val(editReason);
                $('#addModal').find('#editReasonInput').val("");
                if (editReason != '') {
                    $('#addModal').find('#editReasonDisplay').show();
                } else {
                    $('#addModal').find('#editReasonDisplay').hide();
                }
                $('#addModal').find('#grossIncoming').val(obj.message.gross_weight1);
                grossIncomingDatePicker.setDate(obj.message.gross_weight1_date != null ? new Date(obj.message.gross_weight1_date) : null);
                $('#addModal').find('#tareOutgoing').val(obj.message.tare_weight1);
                tareOutgoingDatePicker.setDate(obj.message.tare_weight1_date != null ? new Date(obj.message.tare_weight1_date) : null);
                $('#addModal').find('#nettWeight').val(obj.message.nett_weight1);
                $('#addModal').find('#convertedNettWeight').val(obj.message.converted_nett_weight1);
                $('#addModal').find('#grossIncoming2').val(obj.message.gross_weight2);
                grossIncomingDatePicker2.setDate(obj.message.gross_weight2_date != null ? new Date(obj.message.gross_weight2_date) : null);
                $('#addModal').find('#tareOutgoing2').val(obj.message.tare_weight2);
                tareOutgoingDatePicker2.setDate(obj.message.tare_weight2_date != null ? new Date(obj.message.tare_weight2_date) : null);
                $('#addModal').find('#nettWeight2').val(obj.message.nett_weight2);
                $('#addModal').find('#reduceWeight').val(obj.message.reduce_weight);
                $('#addModal').find('#weightDifference').val(obj.message.weight_different);

                if(obj.message.manual_weight == 'true'){
                    $("#manualWeightYes").prop("checked", true);
                    $("#manualWeightNo").prop("checked", false);
                    $('#manualWeightYes').trigger('click');
                }
                else{
                    $("#manualWeightYes").prop("checked", false);
                    $("#manualWeightNo").prop("checked", true);
                    $('#manualWeightNo').trigger('click');
                }

                $('#addModal').find('#indicatorId').val(obj.message.indicator_id);
                $('#addModal').find('#weighbridge').val(obj.message.weighbridge_id);
                $('#addModal').find('#indicatorId2').val(obj.message.indicator_id_2);
                $('#addModal').find('#productDescription').val(obj.message.product_description);
                $('#addModal').find('#unitPrice').val(obj.message.unit_price).trigger('change');
                $('#addModal').find('#subTotalPrice').val(obj.message.sub_total);
                $('#addModal').find('#sstPrice').val(obj.message.sst);
                $('#addModal').find('#totalPrice').val(obj.message.total_price);
                $('#addModal').find('#finalWeight').val(obj.message.final_weight);
                $('#addModal').find('#currentWeight').text(obj.message.final_weight);

                if (obj.message.load_drum == 'LOAD'){
                    $('#addModal').find("input[name='loadDrum'][value='true']").prop("checked", true).trigger('change');
                }else{
                    $('#addModal').find("input[name='loadDrum'][value='false']").prop("checked", true).trigger('change');
                }
                
                $('#addModal').find('#noOfDrum').val(obj.message.no_of_drum);                

                if (obj.message.transaction_status == 'Purchase'){
                    //$('#addModal').find('#purchaseOrder').next('.select2-container').hide();
                    //('#addModal').find('#purchaseOrderEdit').val(obj.message.purchase_order).show();
                    // Check if purchaseOrder value exist in the select tag
                    var purchaseOrderExists = $('#addModal').find('#purchaseOrder option').filter(function() {
                        return $(this).val() === obj.message.purchase_order;
                    }).length > 0;

                    if (!purchaseOrderExists){
                        // Append missing purchaseOrder
                        $('#addModal').find('#purchaseOrder').append(
                            '<option value="'+obj.message.purchase_order+'">'+obj.message.purchase_order+'</option>'
                        );
                    }

                    //$('#addModal').find('#purchaseOrder').val(obj.message.purchase_order).select2('destroy').select2();
                    $('#addModal').find('#purchaseOrder').val(obj.message.purchase_order).trigger('change');
                    /*setTimeout(function() {
                      const purchaseOrder = $('#addModal').find('#purchaseOrder');
                      purchaseOrder.select2('destroy').select2();
                      purchaseOrder.val(obj.message.purchase_order).trigger('change');
                    }, 100);*/
                    $('#addModal').trigger('orderLoaded', [obj.message]);
                }else{
                    //$('#addModal').find('#salesOrder').next('.select2-container').hide();
                    //$('#addModal').find('#salesOrderEdit').val(obj.message.purchase_order).show();

                    // Check if salesOrder value exist in the select tag
                    var salesOrderExists = $('#addModal').find('#salesOrder option').filter(function() {
                        return $(this).val() === obj.message.purchase_order;
                    }).length > 0;

                    if (!salesOrderExists){
                        // Append missing salesOrder
                        $('#addModal').find('#salesOrder').append(
                            '<option value="'+obj.message.purchase_order+'">'+obj.message.purchase_order+'</option>'
                        );
                    }

                    //$('#addModal').find('#salesOrder').val(obj.message.purchase_order).select2('destroy').select2();
                    $('#addModal').find('#salesOrder').val(obj.message.purchase_order).trigger('change');
                    /*setTimeout(function() {
                      const $salesOrder = $('#addModal').find('#salesOrder');
                      $salesOrder.select2('destroy').select2();
                      $salesOrder.val(obj.message.purchase_order).trigger('change');
                    }, 100);*/
                    $('#addModal').trigger('orderLoaded', [obj.message]);
                }

                // Initialize all Select2 elements in the modal
                $('#addModal .select2').select2({
                    allowClear: true,
                    placeholder: "Please Select",
                    dropdownParent: $('#addModal') // Ensures dropdown is not cut off
                });

                // Apply custom styling to Select2 elements in addModal
                $('#addModal .select2-container .select2-selection--single').css({
                    'padding-top': '4px',
                    'padding-bottom': '4px',
                    'height': 'auto'
                });

                $('#addModal .select2-container .select2-selection__arrow').css({
                    'padding-top': '33px',
                    'height': 'auto'
                });


                // Load these field after PO/SO is loaded
                $('#addModal').on('orderLoaded', function() {
                    /*$('#addModal').find('#customerCode').val(obj.message.customer_code);
                    $('#addModal').find('#customerName').val(obj.message.customer_name).trigger('change');
                    $('#addModal').find('#supplierCode').val(obj.message.supplier_code);
                    $('#addModal').find('#supplierName').val(obj.message.supplier_name).trigger('change')
                    $('#addModal').find('#siteCode').val(obj.message.site_code);
                    $('#addModal').find('#siteName').val(obj.message.site_name).trigger('change');
                    $('#addModal').find('#agent').val(obj.message.agent_name).trigger('change');
                    $('#addModal').find('#agentCode').val(obj.message.agent_code);
                    $('#addModal').find('#supplierWeight').val(obj.message.supplier_weight);
                    $('#addModal').find('#orderWeight').val(obj.message.order_weight);
                    $('#addModal').find('#destinationCode').val(obj.message.destination_code);
                    $('#addModal').find('#destination').val(obj.message.destination).trigger('change');
                    $('#addModal').find('#plant').val(obj.message.plant_name).trigger('change');
                    $('#addModal').find('#plantCode').val(obj.message.plant_code);
                    $('#addModal').find('#rawMaterialCode').val(obj.message.raw_mat_code);
                    $('#addModal').find('#rawMaterialName').val(obj.message.raw_mat_name).trigger('change');
                    $('#addModal').find('#productName').val(obj.message.product_name).trigger('change');
                    $('#addModal').find('#productCode').val(obj.message.product_code);*/

                    // Hide select and show input readonly
                    // if (obj.message.transaction_status == 'Purchase'){
                    //     $('#addModal').find('#purchaseOrder').next('.select2-container').hide();
                    //     $('#addModal').find('#purchaseOrderEdit').val(obj.message.purchase_order).show();
                    // }else{
                    //     $('#addModal').find('#salesOrder').next('.select2-container').hide();
                    //     $('#addModal').find('#salesOrderEdit').val(obj.message.purchase_order).show();
                    // }
                    //$('#addModal').find('#transporterCode').val(obj.message.transporter_code);
                    //$('#addModal').find('#transporter').val(obj.message.transporter).trigger('change');
                });
                
                isEdit = false;

                // Remove Validation Error Message
                $('#addModal .is-invalid').removeClass('is-invalid');

                $('#addModal .select2[required]').each(function () {
                    var select2Field = $(this);
                    var select2Container = select2Field.next('.select2-container');
                    
                    select2Container.find('.select2-selection').css('border', ''); // Remove red border
                    select2Container.next('.select2-error').remove(); // Remove error message
                });

                $('#addModal').modal('show');
            
                $('#weightForm').validate({
                    errorElement: 'span',
                    errorPlacement: function (error, element) {
                        error.addClass('invalid-feedback');
                        if (element.parent('.input-group').length) {
                        // if inside input-group → place error after the group
                        element.parent().after(error);
                        } else {
                            element.closest('.form-group').append(error);
                        }
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
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            else{
                $('#spinnerLoading').hide();
                $("#failBtn").attr('data-toast-text', obj.message );
                $("#failBtn").click();
            }
            $('#spinnerLoading').hide();
        });
    }

    </script>
