<?php
require 'php/requires/functions.php';

$diameter = 2.35;
$length   = 4.565;
$height   = 1.98;

$litres = calculateLFOVolumeLitres($diameter, $length, $height);

echo "LFO Volume: " . number_format($litres, 0) . " Litres";

$litres2 = calculateLFOVolumeLitres($diameter, $length, 1.45, 1100);

echo "LFO Volume: " . number_format($litres2, 0) . " Litres";
?>