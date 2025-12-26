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
    return $volume;
    //return $volume * $sg;
}

/* ===========================
   LFO Caluculation
   =========================== */
function calculateLFOVolumeLitres(
    float $diameter,
    float $length,
    float $liquidHeight,
    float $constant = 1000 // Litres per m³
) {
    $PI = 3.14;               // Excel π
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
