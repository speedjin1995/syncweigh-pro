<?php
require_once __DIR__ . '/../php/db_connect3.php';
require_once __DIR__ . '/../php/requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);

$sql = "
    SELECT id 
    FROM Purchase_Order
    WHERE modified_date < DATE_SUB(NOW(), INTERVAL 2 MONTH)
      AND status = 'Open'
";

$stmt = $db->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();

$expiredPOs = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (count($expiredPOs) === 0) {
    echo "No expired POs found.\n";
    exit;
}

try {
    // Prepare update statement once
    $updatePOStmt = $db->prepare("
        UPDATE Purchase_Order
        SET status = 'Close',
            modified_by = 'SYSTEM',
            modified_date = NOW()
        WHERE id = ?
    ");

    foreach ($expiredPOs as $row) {
        $id = $row['id'];

        // 4️⃣ Update PO status
        $updatePOStmt->bind_param("s", $id);
        $updatePOStmt->execute();
    }

    /***********************************************************
     * Commit changes
     ***********************************************************/
    $db->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Expired POs closed successfully",
        "count" => count($expiredPOs)
    ]);

} catch (Exception $e) {
    $db->rollback();

    echo json_encode([
        "status" => "failed",
        "error" => $e->getMessage()
    ]);
}
?>