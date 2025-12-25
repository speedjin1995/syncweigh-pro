<?php
require 'layouts/session.php';
require 'php/db_connect.php';
require 'php/requires/functions.php';

$plantId = 30;
$tank    = "Batch";
$level   = 1740;   // HIGH
$temp    = 165;    // Temperature

$calculationIdSG = getCalculationId($db, $plantId, $tank, 'BITUSG');
$calculationIdLevel = getCalculationId($db, $plantId, $tank, 'BITULEVEL');
if (!$calculationIdSG || !$calculationIdLevel) {
    die("Invalid tank configuration");
}

$volume = getVolumeFromLevel($db, $calculationIdLevel, $level);
$sg     = getSG($db, $calculationIdSG, $temp);
$tcf    = getTCF($db, $calculationIdSG, $temp);

if ($volume === null || $sg === null) {
    die("Calculation data missing");
}

$observedMT  = getTonnes($volume, $sg);
$correctedMT = $observedMT * $tcf;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bitumen Tank Calculation Result</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; margin-top: 20px; }
        td { padding: 8px 12px; border: 1px solid #ccc; }
        .label { background: #f4f4f4; font-weight: bold; }
    </style>
</head>
<body>

<h2>Bitumen Tank Calculation</h2>

<table>
    <tr>
        <td class="label">Plant ID</td>
        <td><?= htmlspecialchars($plantId) ?></td>
    </tr>
    <tr>
        <td class="label">Tank</td>
        <td><?= htmlspecialchars($tank) ?></td>
    </tr>
    <tr>
        <td class="label">Level (HIGH)</td>
        <td><?= number_format($level, 2) ?></td>
    </tr>
    <tr>
        <td class="label">Temperature (°C)</td>
        <td><?= number_format($temp, 1) ?></td>
    </tr>
    <tr>
        <td class="label">Volume (L)</td>
        <td><?= number_format($volume, 2) ?></td>
    </tr>
    <tr>
        <td class="label">SG</td>
        <td><?= number_format($sg, 4) ?></td>
    </tr>
    <tr>
        <td class="label">TCF</td>
        <td><?= number_format($tcf, 4) ?></td>
    </tr>
    <tr>
        <td class="label">Observed MT</td>
        <td><?= number_format($observedMT, 4) ?></td>
    </tr>
    <tr>
        <td class="label">Corrected MT</td>
        <td><strong><?= number_format($correctedMT, 3) ?></strong></td>
    </tr>
</table>

</body>
</html>
