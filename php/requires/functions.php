<?php

/* ===========================
   Get Calculation (Tank) ID
   =========================== */
function getCalculationId($db, $plantId, $tank, $type)
{
    $sql = "SELECT id FROM Calculations
            WHERE plant_id = ?
              AND batch_drum = ?
              AND type = ?
              AND deleted = 0";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("iss", $plantId, $tank, $type);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    return $res ? $res['id'] : null;
}

/* ===========================
   Level → Volume (Interpolation)
   =========================== */
function getVolumeFromLevel($db, $calculationId, $level)
{
    $sql = "SELECT level, volume
            FROM Calculation_Value
            WHERE calculation_id = ?
              AND level IS NOT NULL
              AND deleted = 0
            ORDER BY level ASC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $calculationId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    for ($i = 0; $i < count($rows) - 1; $i++) {
        $l1 = (float)$rows[$i]['level'];
        $l2 = (float)$rows[$i + 1]['level'];

        if ($level >= $l1 && $level <= $l2) {
            $v1 = (float)$rows[$i]['volume'];
            $v2 = (float)$rows[$i + 1]['volume'];

            return $v1 + ($level - $l1) * ($v2 - $v1) / ($l2 - $l1);
        }
    }

    return null;
}

/* ===========================
   Get Fixed SG
   =========================== */
function getSG($db, $calculationId, $temperature)
{
    $sql = "SELECT sg
            FROM Calculation_Value
            WHERE calculation_id = ?
              AND temperature = ?
              AND deleted = 0
            LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $calculationId, $temperature);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    return $res ? (float)$res['sg'] : null;
}

/* ===========================
   Temperature → TCF (Option B)
   =========================== */
function getTCF($db, $calculationId, $temperature)
{
    $sql = "SELECT temperature, sg
            FROM Calculation_Value
            WHERE calculation_id = ?
              AND temperature = ?
              AND deleted = 0
            ORDER BY temperature ASC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $calculationId, $temperature);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as $row) {
        if ($temperature <= (float)$row['temperature']) {
            return (float)$row['sg']; // sg column used as TCF
        }
    }

    return (float)end($rows)['sg'];
}

function getTonnes($volume, $sg)
{
    // return $volume;
    return $volume * $sg;
}

/* ===========================
   LFO Caluculation
   =========================== */
function calculateLFOVolumeLitres(
    float $diameter,
    float $length,
    float $liquidHeight,
    float $constant = 1000.24 // Litres per m³
) {
    $PI = 22/7;               // Excel π
    $R  = $diameter / 2;

    if ($liquidHeight <= 0) {
        return 0;
    }

    if ($liquidHeight >= $diameter) {
        $area = $PI * $R * $R;
    } else {
        $area =
            ($R * $R * acos(($R - $liquidHeight) / $R)) -
            (($R - $liquidHeight) * sqrt((2 * $R * $liquidHeight) - ($liquidHeight * $liquidHeight)));
    }

    $volume_m3 = $area * $length;

    // ⚠️ IMPORTANT: multiply by 1100, not 1000
    return round($volume_m3 * $constant, 0);
}

// Update weighing with modules
function updateWeighingValue($db, $oldValue, $newValue, $modules, $custSupCode = "", $prodRawCode = "")
{
    $sql = "";

    if($modules == 'Customer'){
        $sql = "UPDATE Weight SET customer_code = ? WHERE customer_code = ?";
    }
    else if($modules == 'Supplier'){
        $sql = "UPDATE Weight SET supplier_code = ? WHERE supplier_code = ?";
    }
    else if($modules == 'Destination'){
        $sql = "UPDATE Weight SET destination_code = ? WHERE destination_code = ?";
    }
    else if($modules == 'SalesOrder'){
        $sql = "UPDATE Weight SET purchase_order = ? WHERE purchase_order = ? AND customer_code = ? AND product_code = ?";
    }
    else if($modules == 'PurchaseOrder'){
        $sql = "UPDATE Weight SET purchase_order = ? WHERE purchase_order = ? AND supplier_code = ? AND raw_mat_code = ?";
    }

    if($sql != ""){
        $weight_stmt = $db->prepare($sql);
        if($modules == 'Customer' || $modules == 'Supplier' || $modules == 'Destination'){
            $weight_stmt->bind_param("ss", $newValue, $oldValue);
        } else {
            $weight_stmt->bind_param("ssss", $newValue, $oldValue, $custSupCode, $prodRawCode);
        }
        $weight_stmt->execute();
        $weight_stmt->close();
    }
}