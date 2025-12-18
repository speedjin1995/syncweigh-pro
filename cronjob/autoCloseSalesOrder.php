<?php
require_once __DIR__ . '/../php/db_connect2.php';
require_once __DIR__ . '/../php/requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();

try {

    // 🔹 1. Close SO where LAST weight > 2 months ago
    $sqlCloseOldWeight = "
        UPDATE Sales_Order so
        JOIN (
            SELECT 
                purchase_order,
                product_code,
                customer_code,
                MAX(tare_weight1_date) AS last_weight_date
            FROM Weight
            WHERE status = '0'
              AND is_cancel <> 'Y'
            GROUP BY purchase_order, product_code, customer_code
        ) w ON w.purchase_order = so.order_no
           AND w.product_code = so.product_code
           AND w.customer_code = so.customer_code
        SET so.status = '1'
        WHERE so.status = '0'
          AND w.last_weight_date < DATE_SUB(NOW(), INTERVAL 2 MONTH)
    ";

    $db->query($sqlCloseOldWeight);


    // 🔹 2. Close SO with NO weight records & created > 2 months ago
    $sqlCloseNoWeight = "
        UPDATE Sales_Order so
        LEFT JOIN Weight w
          ON w.purchase_order = so.order_no
         AND w.product_code = so.product_code
         AND w.customer_code = so.customer_code
         AND w.status = '0'
         AND w.is_cancel <> 'Y'
        SET so.status = '1'
        WHERE so.status = '0'
          AND w.purchase_order IS NULL
          AND so.created_at < DATE_SUB(NOW(), INTERVAL 2 MONTH)
    ";

    $db->query($sqlCloseNoWeight);

    $db->commit();

    echo "✅ Stale Sales Orders auto-closed successfully";

} catch (Exception $e) {
    $db->rollback();
    echo "❌ Error: " . $e->getMessage();
}
?>