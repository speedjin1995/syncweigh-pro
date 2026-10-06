<?php
/*
 * Report Export Modals (shared by weighingReport.php, salesReport.php, purchaseReport.php, publicReport.php).
 *
 * Set before including:
 *   $exportReportTypes  report types shown in the PDF export, picked from $exportReportTypeLabels below
 *   $exportGroupReport  'Sales' | 'Purchase' | 'Public' to add the grouped report modal, or leave unset for none
 *
 * JavaScript lives in script.php.
 */

// Master list of PDF report types: change a label here and every report page follows
$exportReportTypeLabels = array(
    'SUMMARY' => 'Summary Report',
    'PRODUCT' => 'Product Report',
    'S&P' => 'Overall Report - Product',
    'S&PC' => 'Overall Report - Customer',
    'DO' => 'Overall Report - DO',
    'CANCEL' => 'Cancellation & Amendment Report'
);

// Excel export types (Export Excel button)
$exportExcelTypeLabels = array(
    'WEIGHING' => 'Weighing Records',
    'CANCEL' => 'Cancellation & Amendment Report'
);

$exportReportTypes = isset($exportReportTypes) ? $exportReportTypes : array_keys($exportReportTypeLabels);
$exportGroupReport = isset($exportGroupReport) ? $exportGroupReport : null;

// Search filters passed to the export endpoints
$exportFilterFields = array('fromDate', 'toDate', 'status', 'customer', 'supplier', 'vehicle', 'weighingType', 'customerType',
    'product', 'rawMat', 'destination', 'plant', 'batchDrum');

// Grouping options for the grouped report
if ($exportGroupReport == 'Purchase') {
    $exportGroupOptions = array('supplier_code' => 'Supplier', 'raw_mat_code' => 'Raw Material');
} else {
    $exportGroupOptions = array('customer_code' => 'Customer', 'product_code' => 'Product');
}

$exportGroupOptions += array(
    'lorry_plate_no1' => 'Vehicle',
    'destination_code' => 'Destination',
    'transporter_code' => 'Transporter',
    'plant_code' => 'Plant',
    'batch_drum' => 'Batch/Drum'
);
?>
    <div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportPdfModalTitle">Export Weighing Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportPdfForm" class="needs-validation" novalidate autocomplete="off">
                        <div class="row col-12">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="hidden" class="form-control" id="id" name="id">
                                            <div class="col-12">
                                                <div class="row">
                                                    <label for="reportType" class="col-sm-4 col-form-label">Report Type *</label>
                                                    <div class="col-sm-8">
                                                        <select id="reportType" name="reportType" class="form-select" required>
                                                            <?php foreach ($exportReportTypes as $reportTypeCode) { ?>
                                                                <option value="<?= htmlspecialchars($reportTypeCode) ?>"><?= htmlspecialchars($exportReportTypeLabels[$reportTypeCode]) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php foreach ($exportFilterFields as $filterField) { ?>
                                                <input type="hidden" class="form-control" id="<?= $filterField ?>" name="<?= $filterField ?>">
                                            <?php } ?>
                                            <input type="hidden" class="form-control" id="soNo" name="soNo">
                                            <input type="hidden" class="form-control" id="file" name="file" value="weight">
                                            <input type="hidden" class="form-control" id="isMulti" name="isMulti">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger" id="submit">Submit</button>
                            </div>
                        </div><!--end col-->
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="exportExcelModal" tabindex="-1" role="dialog" aria-labelledby="exportExcelModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportExcelModalTitle">Export Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row col-12">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <label for="excelReportType" class="col-sm-4 col-form-label">Report Type *</label>
                                        <div class="col-sm-8">
                                            <select id="excelReportType" name="excelReportType" class="form-select">
                                                <?php foreach ($exportExcelTypeLabels as $excelTypeCode => $excelTypeLabel) { ?>
                                                    <option value="<?= htmlspecialchars($excelTypeCode) ?>"><?= htmlspecialchars($excelTypeLabel) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger" id="exportExcelSubmit">Submit</button>
                        </div>
                    </div><!--end col-->
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

<?php if ($exportGroupReport != null) { ?>
    <div class="modal fade" id="exportGroupRepModal" tabindex="-1" role="dialog" aria-labelledby="exportGroupRepModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportGroupRepModalTitle">Export <?= htmlspecialchars($exportGroupReport) ?> Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportGroupRepForm" class="needs-validation" novalidate autocomplete="off">
                        <div class="row col-12">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="hidden" class="form-control" id="id" name="id">
                                            <div class="col-12">
                                                <div class="row">
                                                    <?php for ($groupNo = 1; $groupNo <= 4; $groupNo++) { ?>
                                                        <div class="form-group col-4 mb-3">
                                                            <label for="group<?= $groupNo ?>">Group <?= $groupNo ?></label>
                                                            <select id="group<?= $groupNo ?>" name="group<?= $groupNo ?>" class="form-select">
                                                                <option value=""></option>
                                                                <?php foreach ($exportGroupOptions as $groupValue => $groupLabel) { ?>
                                                                    <option value="<?= $groupValue ?>"><?= $groupLabel ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php foreach ($exportFilterFields as $filterField) { ?>
                                                <input type="hidden" class="form-control" id="<?= $filterField ?>" name="<?= $filterField ?>">
                                            <?php } ?>
                                            <input type="hidden" class="form-control" id="type" name="type" value="<?= ($exportGroupReport == 'Purchase' ? 'Purchase' : 'Sales') ?>">
                                            <input type="hidden" class="form-control" id="isMulti" name="isMulti">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger" id="submit">Submit</button>
                            </div>
                        </div><!--end col-->
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
<?php } ?>
