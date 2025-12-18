<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = mysqli_connect("localhost", "u664110560_blacktop", "@Sync5500", "u664110560_blacktop");

if(mysqli_connect_errno()){
    echo 'Database connection failed with following errors: ' . mysqli_connect_error();
    die();
}

try {

    // 1️⃣ Load all Purchase Orders
    $poStmt = $db->prepare("
        SELECT po_no, product_code, supplier_code, order_quantity
        FROM Purchase_Order
    ");
    $poStmt->execute();
    $poResult = $poStmt->get_result();

    // 2️⃣ Prepare reusable statements
    $weightStmt = $db->prepare("
        SELECT COALESCE(SUM(supplier_weight), 0) AS total_weight
        FROM Weight
        WHERE purchase_order = ?
          AND raw_mat_code = ?
          AND supplier_code = ?
          AND is_complete = 'Y'
          AND is_cancel <> 'Y'
          AND transaction_status = 'Purchase' 
          AND status = '0'
    ");

    $productStmt = $db->prepare("
        SELECT id
        FROM Raw_Mat
        WHERE raw_mat_code = ?
        LIMIT 1
    ");

    $uomStmt = $db->prepare("
        SELECT rate
        FROM Raw_Mat_UOM
        WHERE raw_mat_id = ?
          AND unit_id = ?
          AND status = ?
        LIMIT 1
    ");

    $updatePoStmt = $db->prepare("
        UPDATE Purchase_Order
        SET balance = ?,
            converted_balance = ?,
            status = ?
        WHERE po_no = ?
          AND raw_mat_code = ?
          AND supplier_code = ?
    ");

    while ($po = $poResult->fetch_assoc()) {

        $poNo         = $po['po_no'];
        $productCode  = $po['raw_mat_code'];
        $supplierCode = $po['supplier_code'];
        $orderQty     = (float) $po['order_quantity'];

        echo "PO: $poNo | $productCode | $supplierCode" . PHP_EOL;

        // 3️⃣ Sum nett weight
        $weightStmt->bind_param('sss', $poNo, $productCode, $supplierCode);
        $weightStmt->execute();
        $usedKg = (float) $weightStmt->get_result()->fetch_assoc()['total_weight'];

        // 4️⃣ Balance (never negative)
        $currentBalance = $orderQty - $usedKg;

        echo "Qty: $orderQty | Used: $usedKg | Balance: $currentBalance" . PHP_EOL;

        // 5️⃣ Product → UOM conversion
        $rate = 1;

        $productStmt->bind_param('s', $productCode);
        $productStmt->execute();
        $productResult = $productStmt->get_result();

        if ($productResult->num_rows > 0) {
            $productId = $productResult->fetch_assoc()['id'];

            $unitId = '2';
            $status = '0';

            $uomStmt->bind_param('sss', $productId, $unitId, $status);
            $uomStmt->execute();
            $uomResult = $uomStmt->get_result();

            if ($uomResult->num_rows > 0) {
                $rate = (float) $uomResult->fetch_assoc()['rate'];
            }
        }

        // 6️⃣ Converted balance
        $convertedBalance = $currentBalance * $rate;

        // 7️⃣ Auto-close rule
        $poStatus = ($currentBalance <= 10000) ? 'Close' : 'Open';

        echo "Rate: $rate | ConvBal: $convertedBalance | Status: $poStatus" . PHP_EOL . PHP_EOL;

        // 8️⃣ Update Purchase Order
        $updatePoStmt->bind_param(
            'ddssss',
            $currentBalance,
            $convertedBalance,
            $poStatus,
            $poNo,
            $productCode,
            $supplierCode
        );
        $updatePoStmt->execute();
    }

    $db->commit();
    echo "✅ Purchase Order cleansing completed";

} catch (Exception $e) {
    $db->rollback();
    echo "❌ Error: " . $e->getMessage();
}
?>