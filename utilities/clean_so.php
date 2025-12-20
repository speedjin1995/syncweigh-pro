<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = mysqli_connect("localhost", "u664110560_blacktop", "@Sync5500", "u664110560_blacktop");

if(mysqli_connect_errno()){
    echo 'Database connection failed with following errors: ' . mysqli_connect_error();
    die();
}

try {

    // 1️⃣ Load open Sales Orders
    $soStmt = $db->prepare("
        SELECT order_no, product_code, customer_code, order_quantity
        FROM Sales_Order
    ");
    $soStmt->execute();
    $soResult = $soStmt->get_result();

    // 2️⃣ Prepare reusable statements
    $weightStmt = $db->prepare("
        SELECT COALESCE(SUM(nett_weight1), 0) AS total_weight
        FROM Weight
        WHERE purchase_order = ?
          AND product_code = ?
          AND customer_code = ?
          AND is_complete = 'Y'
          AND is_cancel <> 'Y'
          AND transaction_status = 'Sales' 
          AND status = '0'
    ");

    $productStmt = $db->prepare("
        SELECT id
        FROM Product
        WHERE product_code = ?
        LIMIT 1
    ");

    $uomStmt = $db->prepare("
        SELECT rate
        FROM Product_UOM
        WHERE product_id = ?
          AND unit_id = ?
          AND status = ?
        LIMIT 1
    ");

    $updateSoStmt = $db->prepare("
        UPDATE Sales_Order
        SET balance = ?,
            converted_balance = ?,
            status = ?
        WHERE order_no = ?
          AND product_code = ?
          AND customer_code = ?
    ");

    while ($so = $soResult->fetch_assoc()) {
        $orderNo      = $so['order_no'];
        $productCode  = $so['product_code'];
        $customerCode = $so['customer_code'];
        $orderQty     = (float) $so['order_quantity'];
        
        echo $orderNo.' - '.$productCode.' - '.$customerCode.PHP_EOL;

        // 3️⃣ Sum nett weight
        $weightStmt->bind_param('sss', $orderNo, $productCode, $customerCode);
        $weightStmt->execute();
        $usedKg = (float) $weightStmt->get_result()->fetch_assoc()['total_weight'];

        // 4️⃣ Balance (KG)
        $currentBalance = $orderQty - $usedKg;
        
        echo $orderQty.' - '.$usedKg.' - '.$currentBalance.PHP_EOL;

        // 5️⃣ Get product_id
        $productStmt->bind_param('s', $productCode);
        $productStmt->execute();
        $productResult = $productStmt->get_result();

        $rate = 1; // fallback

        if ($productResult->num_rows > 0) {
            $productId = $productResult->fetch_assoc()['id'];

            // 6️⃣ Get conversion rate
            $unitId = '2';
            $status = '0';

            $uomStmt->bind_param('sss', $productId, $unitId, $status);
            $uomStmt->execute();
            $uomResult = $uomStmt->get_result();

            if ($uomResult->num_rows > 0) {
                $rate = (float) $uomResult->fetch_assoc()['rate'];
            }
        }

        // 7️⃣ Converted balance
        $convertedBalance = $currentBalance * $rate;

        // 8️⃣ Auto close SO if < 26 KG
        $soStatus = ($currentBalance <= 26) ? 'Close' : 'Open';
        echo $currentBalance.' - '.$rate.' - '.$convertedBalance.' - '.$soStatus.PHP_EOL;

        // 9️⃣ Update Sales Order
        $updateSoStmt->bind_param(
            'ddssss',
            $currentBalance,
            $convertedBalance,
            $soStatus,
            $orderNo,
            $productCode,
            $customerCode
        );
        $updateSoStmt->execute();
    }

    $db->commit();
    echo "✅ Sales Order cleansing & auto-close completed";

} catch (Exception $e) {
    $db->rollback();
    echo "❌ Error: " . $e->getMessage();
}
?>