<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/permissions.php';
require_once 'requires/lookup.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "failed", "message" => "Session expired"]);
    exit;
}

if (!hasModulePermission('Report', 'Audit Log', ['view'])) {
    echo json_encode(["status" => "failed", "message" => "No permission"]);
    exit;
}

if (!isset($_POST['stk_adjustment_id'])) {
    echo json_encode(["status" => "failed", "message" => "Missing adjustment ID"]);
    exit;
}

$adjustmentId = intval($_POST['stk_adjustment_id']);

/**
 * Compare two consecutive log rows and return field-level differences.
 * For the first row (create), $prev is null and all fields show as new.
 */
function diffAdjustmentRows($prev, $current, $trackedFields, $numericFields = [])
{
    $changes = [];

    foreach ($trackedFields as $field) {
        $oldVal = $prev !== null ? ($prev[$field] ?? null) : null;
        $newVal = $current[$field] ?? null;

        // Normalize: treat null and '' as equal
        $oldNorm = ($oldVal === null || $oldVal === '') ? null : $oldVal;
        $newNorm = ($newVal === null || $newVal === '') ? null : $newVal;

        // For numeric fields, compare as floats
        if (in_array($field, $numericFields)) {
            $oldFloat = $oldNorm !== null ? floatval($oldNorm) : null;
            $newFloat = $newNorm !== null ? floatval($newNorm) : null;

            if ($oldFloat !== $newFloat) {
                $changes[$field] = [
                    'from' => $oldNorm,
                    'to' => $newNorm
                ];
            }
        } else {
            if ($oldNorm !== $newNorm) {
                $changes[$field] = [
                    'from' => $oldNorm,
                    'to' => $newNorm
                ];
            }
        }
    }

    return $changes;
}

/**
 * Build history array with diffs from log rows.
 */
function buildHistory($rows, $trackedFields, $numericFields, $db)
{
    $history = [];
    $prev = null;

    foreach ($rows as $row) {
        $eventDate = $row['event_date'];
        $dt = new DateTime($eventDate, new DateTimeZone('GMT'));
        $dt->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));

        $actionId = intval($row['action_id']);
        $actionLabel = $actionId === 1 ? 'Created' : ($actionId === 3 ? 'Deleted' : 'Updated');

        $changes = diffAdjustmentRows($prev, $row, $trackedFields, $numericFields);

        // Show plant name instead of plant id
        if (isset($changes['plant_id'])) {
            $changes['plant_id'] = [
                'from' => $prev !== null ? ($prev['plant_name'] ?? null) : null,
                'to' => $row['plant_name'] ?? null
            ];
        }

        $history[] = [
            'id' => $row['id'],
            'action' => $actionLabel,
            'action_id' => $actionId,
            'action_by' => $row['action_by_name'] ?? $row['action_by'],
            'event_date' => $dt->format('Y-m-d H:i:s'),
            'timestamp' => $dt->getTimestamp(),
            'changes' => $changes,
            'snapshot' => array_intersect_key($row, array_flip($trackedFields))
        ];

        $prev = $row;
    }

    return $history;
}

// Header tracked fields
$headerFields = ['adjustment_no', 'adjustment_date', 'plant_id', 'batch_drum', 'remark', 'total_items', 'total_qty', 'total_cost'];
$headerNumeric = ['total_items', 'total_qty', 'total_cost'];

// Item tracked fields
$itemFields = ['raw_mat_code', 'quantity_before', 'adjustment_qty', 'quantity_after', 'unit_cost', 'total_cost', 'reason'];
$itemNumeric = ['quantity_before', 'adjustment_qty', 'quantity_after', 'unit_cost', 'total_cost'];

// Fetch header history
$headerHistory = [];
$headerQuery = "SELECT sal.*, p.name as plant_name, COALESCE(u.name, sal.action_by) as action_by_name
                FROM Stock_Adjustment_Log sal 
                LEFT JOIN Plant p ON sal.plant_id = p.id
                LEFT JOIN Users u ON sal.action_by = u.id 
                WHERE sal.stk_adjustment_id = ? 
                ORDER BY sal.id ASC";

if ($stmt = $db->prepare($headerQuery)) {
    $stmt->bind_param('i', $adjustmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    $headerHistory = buildHistory($rows, $headerFields, $headerNumeric, $db);

    // Add plant_name to each snapshot for display
    foreach ($headerHistory as $i => &$entry) {
        if (isset($rows[$i]['plant_name'])) {
            $entry['snapshot']['plant_name'] = $rows[$i]['plant_name'];
        }
    }
    unset($entry);
}

// Fetch item history grouped by item_id
$itemHistory = [];
$itemQuery = "SELECT sail.*, rm.name as raw_mat_name, COALESCE(u.name, sail.action_by) as action_by_name 
              FROM Stock_Adjustment_Item_Log sail 
              LEFT JOIN Raw_Mat rm ON sail.raw_mat_id = rm.id
              LEFT JOIN Users u ON sail.action_by = u.id 
              WHERE sail.adjustment_id = ? 
              ORDER BY sail.item_id ASC, sail.id ASC";

if ($stmt = $db->prepare($itemQuery)) {
    $stmt->bind_param('i', $adjustmentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $itemsByItemId = [];
    while ($row = $result->fetch_assoc()) {
        $itemId = $row['item_id'];
        if (!isset($itemsByItemId[$itemId])) {
            $itemsByItemId[$itemId] = [];
        }
        $itemsByItemId[$itemId][] = $row;
    }
    $stmt->close();

    foreach ($itemsByItemId as $itemId => $rows) {
        $history = buildHistory($rows, $itemFields, $itemNumeric, $db);

        // Add raw_mat_name to each snapshot
        foreach ($history as $i => &$entry) {
            if (isset($rows[$i]['raw_mat_name'])) {
                $entry['snapshot']['raw_mat_name'] = $rows[$i]['raw_mat_name'];
            }
        }
        unset($entry);

        $itemHistory[] = [
            'item_id' => $itemId,
            'raw_mat_code' => $rows[0]['raw_mat_code'] ?? '',
            'raw_mat_name' => $rows[0]['raw_mat_name'] ?? '',
            'history' => $history
        ];
    }
}

// Get adjustment info for the modal title
$adjustmentNo = '';
if (!empty($headerHistory)) {
    $adjustmentNo = $headerHistory[0]['snapshot']['adjustment_no'] ?? '';
}

echo json_encode([
    "status" => "success",
    "adjustment_no" => $adjustmentNo,
    "header_history" => $headerHistory,
    "item_history" => $itemHistory
]);

$db->close();
?>
