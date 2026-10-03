<?php
/**
 * Stock adjustment slip (A4), returned as HTML for the print window.
 * $adj comes from InventoryService::getAdjustmentPrint().
 *
 * Paged.js lays out the pages: the company header, title and column header repeat
 * at the top of every page and the remark / total / signature at the bottom.
 * The slip opens in its own tab; the user prints it with the Print button.
 */
function renderStockAdjustmentPrint(array $adj)
{
    $e = function ($value) {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    };
    $company = $adj['company'] ?? [];

    // Company header
    $address = array_filter([$company['address_line_1'] ?? '', $company['address_line_2'] ?? '', $company['address_line_3'] ?? '']);
    $contact = [];
    if (!empty($company['phone_no'])) {
        $contact[] = 'Tel: ' . $e($company['phone_no']);
    }
    if (!empty($company['fax_no'])) {
        // Some companies keep their email in the fax field
        $contact[] = (strpos($company['fax_no'], '@') !== false ? 'Email: ' : 'Fax: ') . $e($company['fax_no']);
    }

    // Item rows
    $rows = '';
    $no = 1;
    foreach ($adj['items'] ?? [] as $item) {
        $rows .= '<div class="item-row">
            <div class="col-no">' . $no++ . '</div>
            <div class="col-code">' . $e($item['code'] ?: $item['raw_mat_code']) . '</div>
            <div class="col-desc">' . $e($item['raw_mat_name']) . '</div>
            <div class="col-qty">' . number_format(floatval($item['adjustment_qty']), 2) . '</div>
            <div class="col-uom">KG</div>
            <div class="col-unit">' . number_format(floatval($item['unit_cost']), 2) . '</div>
            <div class="col-total">' . number_format(floatval($item['total_cost']), 2) . '</div>
        </div>';
    }

    $adjDate = !empty($adj['adjustment_date']) ? date('d/m/Y', strtotime($adj['adjustment_date'])) : '';
    $plant = trim(($adj['plant_code'] ?? '') . ' - ' . ($adj['plant_name'] ?? ''), ' -');

    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Adjustment - ' . $e($adj['adjustment_no']) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }

        /* Paged.js */
        @page {
            size: A4;
            margin: 65mm 10mm 45mm 10mm;
            @top-left { content: element(running-header); }
            @bottom-left { content: element(running-footer); }
        }
        .running-header { position: running(running-header); width: 100%; }
        .running-footer { position: running(running-footer); width: 100%; }

        /* Header */
        .header-block { padding: 15px 0; border-bottom: 2px solid #000; }
        .company-name { font-size: 16px; font-weight: bold; color: #000; }
        .company-reg { font-size: 12px; font-weight: normal; color: #666; }
        .company-detail { font-size: 12px; color: #555; line-height: 1.4; }

        /* Title & Reference */
        .title-section { text-align: center; padding: 15px 0; }
        .title { font-size: 18px; font-weight: bold; color: #000; margin-bottom: 5px; }
        .ref-row { display: flex; justify-content: space-between; font-size: 12px; text-align: left; }
        .ref-left { line-height: 1.6; }
        .ref-right { text-align: right; line-height: 1.6; }

        /* Items Table Header */
        .items-header { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-header th { padding: 8px 5px; text-align: left; border: solid black; border-width: 2px 0; font-weight: normal; }
        .tc { text-align: center; }
        .tr { text-align: right; }

        /* Body Items */
        .item-row { display: flex; border-bottom: 1px solid #ddd; padding: 8px 0; break-inside: avoid; }
        .item-row:nth-child(even) { background: #f9f9f9; }
        .item-row > div { padding: 0 5px; }
        .col-no { width: 5%; text-align: center; }
        .col-code { width: 15%; }
        .col-desc { width: 35%; }
        .col-qty { width: 12%; text-align: right; }
        .col-uom { width: 8%; text-align: center; }
        .col-unit { width: 12%; text-align: right; }
        .col-total { width: 13%; text-align: right; }

        /* Footer */
        .footer-block { border-top: 1px solid #333; padding-top: 10px; }
        .footer-row { display: flex; justify-content: space-between; align-items: flex-start; }
        .note-section { width: 60%; font-size: 12px; }
        .total-section { width: 40%; text-align: right; }
        .total-box { display: inline-block; border: 1px solid #333; padding: 5px 15px; min-width: 100px; text-align: right; font-weight: bold; }
        .signature-section { margin-top: 50px; text-align: right; }
        .signature-line { border-top: 1px solid #333; width: 150px; display: inline-block; margin-top: 30px; }
        .signature-label { font-size: 12px; }

    </style>
</head>
<body>

<!-- RUNNING HEADER -->
<div class="running-header">
    <div class="header-block">
        <div class="company-name">' . $e($company['name'] ?? '') . (!empty($company['company_reg_no']) ? ' <span class="company-reg">(' . $e($company['company_reg_no']) . ')</span>' : '') . '</div>
        <div class="company-detail">' . implode('<br>', array_map($e, $address)) . (!empty($contact) ? '<br>' . implode(' | ', $contact) : '') . '</div>
    </div>
    <div class="title-section">
        <div class="title">Stock Adjustment</div>
        <div class="ref-row">
            <div class="ref-left">
                <div><strong>Plant :</strong> ' . $e($plant) . '</div>
                <div><strong>Batch/Drum :</strong> ' . $e($adj['batch_drum']) . '</div>
            </div>
            <div class="ref-right">
                <div><strong>No :</strong> ' . $e($adj['adjustment_no']) . '</div>
                <div><strong>Date :</strong> ' . $adjDate . '</div>
            </div>
        </div>
    </div>
    <table class="items-header">
        <tr>
            <th style="width:5%;" class="tc">No</th>
            <th style="width:15%;">Item Code</th>
            <th style="width:35%;">Description</th>
            <th style="width:12%;" class="tr">Qty</th>
            <th style="width:8%;" class="tc">UOM</th>
            <th style="width:12%;" class="tr">Unit Cost</th>
            <th style="width:13%;" class="tr">Subtotal</th>
        </tr>
    </table>
</div>

<!-- BODY CONTENT -->
<div class="body-section">
    ' . $rows . '
</div>

<!-- RUNNING FOOTER -->
<div class="running-footer">
    <div class="footer-block">
        <div class="footer-row">
            <div class="note-section">
                <strong>Note :</strong> ' . $e($adj['remark']) . '
            </div>
            <div class="total-section">
                <strong>Total</strong>
                <span class="total-box">' . number_format(floatval($adj['total_cost'] ?? 0), 2) . '</span>
            </div>
        </div>
        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-label">Approved By</div>
        </div>
    </div>
</div>

<!-- Paged.js - load after content -->
<script src="https://unpkg.com/pagedjs@0.4.3/dist/paged.polyfill.js"></script>
<script>
    // Once the pages are laid out, add the Print button
    class PrintAfterRender extends Paged.Handler {
        constructor(chunker, polisher, caller) {
            super(chunker, polisher, caller);
        }
        afterRendered(pages) {
            // Added after Paged.js so it is not rewritten: Paged.js breaks after every page,
            // including the last, which printed an extra blank page
            var style = document.createElement("style");
            style.textContent = ".pagedjs_page:last-of-type { break-after: auto !important; page-break-after: auto !important; }";
            document.head.appendChild(style);

            var wrapper = document.createElement("div");
            wrapper.id = "printBtnFloat";
            wrapper.style.cssText = "position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:99999;";
            wrapper.innerHTML = \'<button type="button" onclick="printSlip();" style="background:#dc3545;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-size:14px;box-shadow:0 2px 6px rgba(0,0,0,0.15);">Print</button>\';
            document.body.appendChild(wrapper);
        }
    }
    Paged.registerHandlers(PrintAfterRender);

    // Hide the button while printing so it is not on the slip (and does not push out a blank page)
    function printSlip() {
        document.getElementById("printBtnFloat").style.display = "none";
        window.print();
    }
    window.addEventListener("afterprint", function () {
        var btn = document.getElementById("printBtnFloat");
        if (btn) {
            btn.style.display = "block";
        }
    });
</script>

</body>
</html>';
}
