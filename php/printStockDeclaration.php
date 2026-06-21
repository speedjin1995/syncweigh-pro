<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    
    if ($stmt = $db->prepare("SELECT * FROM Bitumen WHERE id = ?")) {
        $stmt->bind_param('s', $id);

        if (!$stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $stmt->execute();
            $result = $stmt->get_result();
            if($row = $result->fetch_assoc()){
                $plantId = $row['plant_id'];
                $plantCode = $row['plant_code'];
                $plant = searchPlantById($row['plant_id'], $db);
                $batchDrum = $row['batch_drum'];
                $declarationDate = !empty($row['declaration_datetime']) ? date('Y-m-d', strtotime($row['declaration_datetime'])) : null;

                if (empty($declarationDate)){
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Declaration Date not found"
                        ));
                    exit;
                }

                // Get Company Detail
                $companyStmt = $db->prepare("SELECT * FROM Company WHERE id = 1");
                $companyStmt->execute();
                $companyResult = $companyStmt->get_result();
                $companyRow = $companyResult->fetch_assoc();

                // Get Bitumen Raw Mat Id
                $result = $db->query("SELECT id FROM Raw_Mat WHERE raw_mat_code = 'BTBI001' AND status = 0 LIMIT 1");
                $bitumenRawMatId = $result ? $result->fetch_assoc()['id'] ?? null : null;

                $products = $db->query("
                    SELECT STL.*, P.product_code AS product_code, P.name AS product_name, PRW.raw_mat_basic_uom AS percentage FROM Stock_Take_List STL 
                    JOIN Product P ON STL.product_id = P.id 
                    JOIN Product_RawMat PRW ON PRW.product_id = P.id
                    WHERE STL.plant_id = " . $plantId . " AND STL.batch_drum = '" . $batchDrum ."' 
                    AND PRW.raw_mat_id = ". $bitumenRawMatId ." AND PRW.plant_id = " . $plantId . " AND PRW.batch_drum = '" . $batchDrum ."' AND PRW.status = 0
                    ORDER BY STL.sort ASC"
                ); 

                // Get Other Bitumen
                $otherRawMatList = [];
                if (!empty($row['pg76'])){
                    $otherRawMats = json_decode($row['pg76'], true);

                    foreach ($otherRawMats as $key => $otherRawMat){
                        if (!is_numeric($key)) {
                            continue;
                        }

                        $otherRawMatList[$otherRawMat['pg76Name']] = [
                            "no" => $otherRawMat['no'],
                            "pg76Incoming" => $otherRawMat['pg76Incoming'],
                            "pg76Status" => $otherRawMat['pg76Status'],
                            "pg76Temp" => $otherRawMat['pg76Temp'],
                            "pg76Level" => $otherRawMat['pg76Level'],
                            "pg76ActualLevel" => $otherRawMat['pg76ActualLevel'],
                            "pgSeventySix" => $otherRawMat['pgSeventySix'],
                        ];
                    }
                }

                // Append other bitumen products into $products
                $products = $products ? $products->fetch_all(MYSQLI_ASSOC) : [];
                foreach ($otherRawMatList as $rawMatId => $otherRawMat) {
                    $otherProducts = $db->query("
                        SELECT STL.*, P.product_code AS product_code, P.name AS product_name, PRW.raw_mat_basic_uom AS percentage FROM Stock_Take_List STL 
                        JOIN Product P ON STL.product_id = P.id 
                        JOIN Product_RawMat PRW ON PRW.product_id = P.id
                        JOIN Raw_Mat RM ON RM.id = PRW.raw_mat_id AND RM.type = 'Bitumen'
                        WHERE STL.plant_id = " . $plantId . " AND STL.batch_drum = '" . $batchDrum ."' 
                        AND PRW.raw_mat_id = ". $rawMatId ." AND PRW.plant_id = " . $plantId . " AND PRW.batch_drum = '" . $batchDrum ."' AND PRW.status = 0
                        ORDER BY STL.sort ASC"
                    );
                    if ($otherProducts) {
                        while ($otherProductRow = $otherProducts->fetch_assoc()) {
                            array_push($products, $otherProductRow);
                        }
                    }
                }

                $otherCount = count($otherRawMatList);
                $totalCols   = 10 + $otherCount; // Mix + Qty + % + 60/70 + CMB + CRMB + LMB + others + LFO + Diesel + 2 trailing
                $midColspan  = 6 + $otherCount; // company name / DAILY STOCK ANALYSIS span
                $rightColspan = $totalCols - $midColspan; // LOCATION / WORKSHEET span

                $html = '
                    <html>
                        <head>
                            <meta charset="UTF-8">
                            <title>Daily Stock Analysis</title>
                            <style>
                                body { font-family: Calibri, sans-serif; font-size: 10pt; }
                                table { border-collapse: collapse; width: 100%; }
                                th, td {
                                    border: 1px solid #000;
                                    padding: 4px;
                                    text-align: center;
                                    vertical-align: middle;
                                    font-family: Calibri, sans-serif;
                                    font-size: 10pt;
                                }
                                .no-border td { border: none; }
                                .header { font-weight: bold; font-size: 16px; }
                                .subheader { font-weight: bold; font-size: 14px; }
                                .left { text-align: left; }
                                .right { text-align: right; }
                                td.border-up-down { border-top: 1px solid black !important; border-bottom: 1px solid black !important; border-left: none; border-right: none; }
                            </style>
                        </head>
                        <body>
                            <table>
                                <tr>
                                    <td rowspan="2" style="width: 15%; text-align: center; font-weight: bold;"><img src="path/to/logo.png" alt="Logo" style="max-width: 100%; height: auto;"></td>
                                    <td colspan="'.$midColspan.'" style="width: 45%; text-align: center; font-weight: bold;">
                                        <div class="title">'.$companyRow['name'].'</div>
                                        <div style="font-weight: normal;">('.$companyRow['company_reg_no'].')</div>
                                    </td>
                                    <td colspan="'.$rightColspan.'" style="width: 20%; text-align: center; font-weight: bold;">LOCATION:<br><span style="font-weight: normal;">' . htmlspecialchars($plant['plant_code']) . ' - ' . $plant['name'] . '</span></td>
                                </tr>
                                <tr>
                                    <td colspan="'.$midColspan.'" style="width: 45%; text-align: center; font-weight: bold;">DAILY STOCK ANALYSIS</td>
                                    <td colspan="'.$rightColspan.'" style="width: 20%; text-align: center; font-weight: bold;">WORKSHEET:<br> <span style="font-weight: normal;">ERM-OP-02-01</span></td>
                                </tr>
                                <tr class="no-border">
                                    <td colspan="'.($totalCols + 1).'"</td>
                                </tr>
                                <tr class="no-border">
                                    <td class="left" colspan="'.($totalCols - 1).'"><b>Plant :</b> '.htmlspecialchars($row['batch_drum']).'</td>
                                    <td class="right" colspan="2"><b>Date :</b> '.date('d/m/Y', strtotime($row['declaration_datetime'])).'</td>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                    <th>Planning</th>
                                    <th colspan="' . (6 + count($otherRawMatList)) . '">Targeted Bitumen (ton)</th>
                                    <th>Targeted LFO</th>
                                    <th>Targeted Diesel</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>Mix</th>
                                    <th>Qty (ton)</th>
                                    <th>(%)</th>
                                    <th>60/70</th>
                                    <th>CMB</th>
                                    <th>CRMB</th>
                                    <th>LMB (LATEX)</th>
                                ';

                                foreach ($otherRawMatList as $rawMatId => $otherRawMat) {
                                    $html .= '
                                        <th>'.searchRawMatNameById($rawMatId, $db).'</th>
                                    ';
                                }

                                $html .= '
                                    <th>(litre)</th>
                                    <th>(litre)</th>
                                    <th></th>
                                    <th></th>
                                </tr>';

                                $rowNum = 10;
                                foreach ($products as $product){
                                    $productCode = $product['product_code'];
                                    $productName = $product['product_name'];
                                    $bitumenRawMatPercentage = $product['percentage'];

                                    $html .= '
                                        <tr>
                                            <td>'.htmlspecialchars($productName).'</td>
                                            <td></td>
                                            <td>'.number_format($bitumenRawMatPercentage*100, 2).'%</td>
                                            <td>=B'.$rowNum.'*C'.$rowNum.'</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                    ';
                                    $html .= str_repeat('<td></td>', count($otherRawMatList));
                                    $html .= '
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    ';
                                    $rowNum++;
                                }

                                $planningEmptyCols = str_repeat('<td></td>', count($otherRawMatList));
                                $html .= '
                                <tr><td><b>Subtotal</b></td><td>=SUM(B10:B'.($rowNum-1).')</td><td></td><td>=SUM(D10:D'.($rowNum-1).')</td><td></td><td></td><td></td>'.$planningEmptyCols.'<td></td><td></td><td></td><td></td></tr>
                                <tr><td><b>Incoming</b></td><td></td><td></td><td></td><td></td><td></td><td></td>'.$planningEmptyCols.'<td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Ordered Bitumen</b></td><td></td><td></td><td>=D'.($rowNum+1).'</td><td></td><td></td><td></td>'.$planningEmptyCols.'<td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Opening Stock</b></td><td></td><td></td><td></td><td></td><td></td><td></td>'.$planningEmptyCols.'<td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Targeted Usage</b></td><td></td><td></td><td></td><td></td><td></td><td></td>'.$planningEmptyCols.'<td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td></td><td></td><td></td><td class="border-up-down">=SUM(D'.($rowNum+2).'+D'.($rowNum+3).'-D'.($rowNum+4).')</td><td></td><td></td><td></td>'.$planningEmptyCols.'<td></td><td></td><td></td><td></td></tr>
                                <tr>
                                </tr>
                                <tr class="no-border">
                                    <td colspan="'.($totalCols - 1).'"></td>
                                    <td class="right" colspan="2"><b>Date : </b>'.date('d/m/Y', strtotime($row['declaration_datetime'])).'</td>
                                </tr>
                                <tr>
                                    <th>Actual</th>
                                    <th colspan="' . (6 + count($otherRawMatList)) . '">Actual Bitumen (ton)</th>
                                    <th>Actual LFO</th>
                                    <th>Actual Diesel</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>Mix</th>
                                    <th>Qty (ton)</th>
                                    <th>(%)</th>
                                    <th>60/70</th>
                                    <th>CMB</th>
                                    <th>CRMB</th>
                                    <th>LMB (LATEX)</th>
                                ';

                                foreach ($otherRawMatList as $rawMatId => $otherRawMat) {
                                    $html .= '<th>'.searchRawMatNameById($rawMatId, $db).'</th>';
                                }

                                $html .= '
                                    <th>(litre)</th>
                                    <th>(litre)</th>
                                    <th></th>
                                    <th></th>
                                </tr>';

                                // Get LFO Stock Take
                                $lfoPrevReading = 0;
                                $lfoIncoming = 0;
                                $totalLfoLitre = 0;
                                $totalLfo = 0;
                                $totalLfoUsage = 0;
                                if (!empty($row['lfo'])){
                                    $lfo = json_decode($row['lfo'], true);
                                    $lfoPrevReading = floatval($lfo['previousLfoReading']);
                                    $lfoIncoming = floatval($lfo['lfoIncoming']);
                                    $totalLfoLitre = floatval($lfo['totalLfoLitre']);
                                    $totalLfo = floatval($lfo['totalLfo']);
                                    $totalLfoUsage = floatval($lfo['totalLfoUsage']);
                                }

                                // Get Diesel Stock Take
                                $dieselIncoming = 0;
                                $previousDieselReading = 0;
                                $totalDiesel = 0;
                                $totalDieselUsage = 0;
                                if (!empty($row['diesel'])){
                                    $diesel = json_decode($row['diesel'], true);
                                    $dieselIncoming = floatval($diesel['dieselIncoming']);
                                    $previousDieselReading = floatval($diesel['previousDieselReading']);
                                    $totalDiesel = floatval($diesel['totalDiesel']);
                                    $totalDieselUsage = floatval($diesel['totalDieselUsage']);
                                }

                                // Get Other Diesel Stock Take
                                $otherDieselTotalTransportUsage = 0;
                                $totalDieselProduction = 0;
                                if (!empty($row['other_diesel'])){
                                    $otherDiesel = json_decode($row['other_diesel'], true);
                                    $otherDieselTotalTransportUsage = floatval($otherDiesel['otherDieselTotalTransportUsage']);
                                    $totalDieselProduction = floatval($otherDiesel['totalDieselProduction']);
                                }

                                // Compute dynamic column letters based on otherRawMatList count
                                // A=Mix, B=Qty, C=%, D=60/70, E=CMB, F=CRMB, G=LMB, H..=otherRawMat cols, then LFO, Diesel
                                $lfoColLetter    = chr(ord('H') + $otherCount);      // LFO column
                                $dieselColLetter = chr(ord('I') + $otherCount);      // Diesel column

                                $rowNum = $rowNum+10;
                                $subtotalRowStart = $rowNum;
                                $productLoopCount = 0;
                                foreach ($products as $product){
                                    $productCode = $product['product_code'];
                                    $productName = $product['product_name'];
                                    $bitumenRawMatPercentage = $product['percentage'];
                                    $productNettWeight = 0;

                                    // Query Sales for each Product
                                    if ($product_stmt = $db->prepare("SELECT * FROM Weight WHERE product_code = ? AND plant_code = ? and batch_drum = ? AND DATE(tare_weight1_date) = ? AND transaction_status = 'SALES' AND is_complete = 'Y' AND is_cancel <> 'Y' AND status = '0'")){
                                        $product_stmt->bind_param("ssss", $productCode, $plantCode, $batchDrum, $declarationDate);
                                        $product_stmt->execute();
                                        $productResult = $product_stmt->get_result();
                            
                                        while ($productRow = $productResult->fetch_assoc()) {
                                            $productNettWeight += floatval($productRow['nett_weight1']);
                                        }
                                    }

                                    // Convert KG to MT
                                    $productNettWeight = $productNettWeight / 1000;

                                    $lfoVal    = $productLoopCount == 0 ? round($lfoPrevReading, 2)
                                               : ($productLoopCount == 1 ? round($lfoIncoming, 2)
                                               : ($productLoopCount == 2 ? round($totalLfoLitre, 2)
                                               : ($productLoopCount == 3 ? '='.$lfoColLetter.($rowNum-3).'+'.$lfoColLetter.($rowNum-2).'-'.$lfoColLetter.($rowNum-1)
                                               : '')));
                                    $dieselVal = $productLoopCount == 0 ? round($previousDieselReading, 2)
                                               : ($productLoopCount == 1 ? round($dieselIncoming, 2)
                                               : ($productLoopCount == 2 ? round($totalDiesel, 2)
                                               : ($productLoopCount == 3 ? '='.$dieselColLetter.($rowNum-3).'+'.$dieselColLetter.($rowNum-2).'-'.$dieselColLetter.($rowNum-1)
                                               : ($productLoopCount == 4 ? round($otherDieselTotalTransportUsage, 2)
                                               : ($productLoopCount == 5 ? '='.$dieselColLetter.($rowNum-2).'-'.$dieselColLetter.($rowNum-1)
                                               : '')))));

                                    $otherRawMatCols = '';
                                    $otherPctColLetter = 'H';
                                    foreach ($otherRawMatList as $rawMatId => $otherRawMat) {
                                        $otherRawMatCols .= '<td>=ROUND(B'.$rowNum.'*'.$otherPctColLetter.$rowNum.',2)</td>';
                                        $otherPctColLetter++;
                                    }
                                    
                                    $html .= '
                                        <tr>
                                            <td>'.htmlspecialchars($productName).'</td>
                                            <td style="mso-number-format:\'0\.00\'">'.round($productNettWeight, 2).'</td>
                                            <td>'.number_format($bitumenRawMatPercentage*100, 2).'%</td>
                                            <td>=ROUND(B'.$rowNum.'*C'.$rowNum.',2)</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            '.$otherRawMatCols.'
                                            <td style="mso-number-format:\'0\.00\'">'.$lfoVal.'</td>
                                            <td style="mso-number-format:\'0\.00\'">'.$dieselVal.'</td>
                                            <td></td>
                                            <td></td>
                                        </tr>';

                                    $rowNum++;
                                    $productLoopCount++;
                                }

                                // Get Bitumen Stock Take
                                $bitumenActualStock = 0;
                                $bitumenIncomingWeight = 0;
                                if (!empty($row['60/70'])){
                                    $bitumen = json_decode($row['60/70'], true);
                                    $bitumenActualStock = floatval($bitumen['totalSixtySeventy']);
                                    $bitumenIncomingWeight = floatval($bitumen['bitumenIncoming']);
                                }

                                // Get Previous Declaration Date Stock Take
                                $bitumenOpeningStock = 0;
                                $otherBitumenOpeningMap = [];
                                if($prevStmt = $db->prepare("SELECT * FROM Bitumen WHERE plant_id = ? AND batch_drum = ? AND DATE(declaration_datetime) < ? AND status = 0 ORDER BY declaration_datetime DESC LIMIT 1")){
                                    $prevStmt->bind_param("sss", $plantId, $batchDrum, $declarationDate);
                                    $prevStmt->execute();
                                    $prevResult = $prevStmt->get_result();
                                    $prevStockTakeRow = $prevResult->fetch_assoc();
                                    $bitumenOpeningStock = json_decode($prevStockTakeRow['60/70'], true)['totalSixtySeventy'];
                                    if (!empty($prevStockTakeRow['pg76'])) {
                                        $prevPg76 = json_decode($prevStockTakeRow['pg76'], true);
                                        foreach ($prevPg76 as $key => $prevOtherRawMat) {
                                            if (!is_numeric($key)) continue;
                                            $otherBitumenOpeningMap[$prevOtherRawMat['pg76Name']] = floatval($prevOtherRawMat['pgSeventySix']);
                                        }
                                    }
                                }

                                $otherBitumenEmptyCols = str_repeat('<td></td>', $otherCount);
                                $otherBitumenSumCols = '';
                                $otherBitumenRefCols = '';
                                $otherBitumenIncomingCols = '';
                                $otherBitumenClosingCols = '';
                                $otherBitumenVariantCols = '';
                                $otherBitumenActualCols = '';
                                $otherBitumenOpeningCols = '';
                                $colLetter = 'H';
                                foreach ($otherRawMatList as $rawMatId => $otherRawMat) {
                                    $otherBitumenSumCols .= '<td>=SUM('.$colLetter.$subtotalRowStart.':'.$colLetter.($rowNum-1).')</td>';
                                    $otherBitumenRefCols .= '<td>='.$colLetter.($rowNum+1).'</td>';
                                    $otherBitumenIncomingCols .= '<td style="mso-number-format:\'0\.00\'">'.round(floatval($otherRawMat['pg76Incoming']), 2).'</td>';
                                    $otherBitumenClosingCols .= '<td>=SUM('.$colLetter.($rowNum+2).'+'.$colLetter.($rowNum+3).'-'.$colLetter.($rowNum+4).')</td>';
                                    $otherBitumenVariantCols .= '<td>='.$colLetter.($rowNum+6).'-'.$colLetter.($rowNum+5).'</td>';
                                    $otherBitumenActualCols .= '<td style="mso-number-format:\'0\.00\'">'.round(floatval($otherRawMat['pgSeventySix']), 2).'</td>';
                                    $otherBitumenOpeningCols .= '<td style="mso-number-format:\'0\.00\'">'.round($otherBitumenOpeningMap[$rawMatId] ?? 0, 2).'</td>';
                                    $otherBitumenTargetedUsageCols .= '<td>='.$colLetter.($rowNum+2).'</td>';

                                    $colLetter++;
                                }

                                $html .= '
                                <tr>
                                    <td><b>Subtotal</b></td>
                                    <td>=SUM(B'.$subtotalRowStart.':B'.($rowNum-1).')</td>
                                    <td></td>
                                    <td>=SUM(D'.$subtotalRowStart.':D'.($rowNum-1).')</td>
                                    <td>=SUM(E'.$subtotalRowStart.':E'.($rowNum-1).')</td>
                                    <td>=SUM(F'.$subtotalRowStart.':F'.($rowNum-1).')</td>
                                    <td>=SUM(G'.$subtotalRowStart.':G'.($rowNum-1).')</td>
                                    '.$otherBitumenSumCols.'
                                    <td style="mso-number-format:\'0\.00\'">'.round($totalLfoUsage, 2).'</td>
                                    <td style="mso-number-format:\'0\.00\'">'.round($totalDieselUsage, 2).'</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>Incoming</b></td>
                                    <td></td>
                                    <td></td>
                                    <td style="mso-number-format:\'0\.00\'">'.round($bitumenIncomingWeight, 2).'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    '.$otherBitumenIncomingCols.'
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="left"><b>Opening Stock</b></td>
                                    <td></td>
                                    <td></td>
                                    <td style="mso-number-format:\'0\.00\'">'.round($bitumenOpeningStock, 2).'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    '.$otherBitumenOpeningCols.'
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="left"><b>Supplied Bitumen</b></td>
                                    <td></td>
                                    <td></td>
                                    <td>=D'.($rowNum+1).'</td>
                                    <td>=E'.($rowNum+1).'</td>
                                    <td>=F'.($rowNum+1).'</td>
                                    <td>=G'.($rowNum+1).'</td>
                                    '.$otherBitumenRefCols.'
                                    <td class="left"><b>LFO Usage</b></td>
                                    <td style="mso-number-format:\'0\.00\'">=ROUND(B'.$rowNum.'/'.$lfoColLetter.$rowNum.',2)</td>
                                    <td>Litre / ton</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="left"><b>Targeted Bitumen Usage</b></td>
                                    <td></td>
                                    <td></td>
                                    <td>=D'.$rowNum.'</td>
                                    <td>=E'.$rowNum.'</td>
                                    <td>=F'.$rowNum.'</td>
                                    <td>=G'.$rowNum.'</td>
                                    '.$otherBitumenTargetedUsageCols.'
                                    <td class="left"><b>Diesel Usage</b></td>
                                    <td style="mso-number-format:\'0\.00\'">=ROUND('.$dieselColLetter.$rowNum.',2)</td>
                                    <td>Litre /day</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="left"><b>Targeted Closing Stock</b></td>
                                    <td></td>
                                    <td></td>
                                    <td>=SUM(D'.($rowNum+2).'+D'.($rowNum+3).'-D'.($rowNum+4).')</td>
                                    <td>=SUM(E'.($rowNum+2).'+E'.($rowNum+3).'-E'.($rowNum+4).')</td>
                                    <td>=SUM(F'.($rowNum+2).'+F'.($rowNum+3).'-F'.($rowNum+4).')</td>
                                    <td>=SUM(G'.($rowNum+2).'+G'.($rowNum+3).'-G'.($rowNum+4).')</td>
                                    '.$otherBitumenClosingCols.'
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="left"><b>Actual Stock (after production)</b></td>
                                    <td></td>
                                    <td></td>
                                    <td style="mso-number-format:\'0\.00\'">'.round($bitumenActualStock, 2).'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    '.$otherBitumenActualCols.'
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="left"><b>Variant :</b></td>
                                    <td></td>
                                    <td></td>
                                    <td>=D'.($rowNum+6).'-D'.($rowNum+5).'</td>
                                    <td>=E'.($rowNum+6).'-E'.($rowNum+5).'</td>
                                    <td>=F'.($rowNum+6).'-F'.($rowNum+5).'</td>
                                    <td>=G'.($rowNum+6).'-G'.($rowNum+5).'</td>
                                    '.$otherBitumenVariantCols.'
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>

                            <br>

                            <!-- FOOTER -->
                            <table>
                                <tr>
                                    <td colspan="2" class="left"><b>Plant Production :</b></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="left"><b>Weighbridge weight :</b></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="left"><b>Difference of Production :</b></td>
                                    <td>=((C'.($rowNum+9).'-C'.($rowNum+10).')/C'.($rowNum+9).')*100</td>
                                    <td b>%</td>
                                </tr>
                            </table>

                            <br>

                            <table class="no-border">
                                <tr>
                                    <td>Prepared By :</td>
                                    <td colspan="2"></td>
                                    <td>Checked By :</td>
                                    <td colspan="3"></td>
                                    <td>Approved By :</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr></tr>
                                <tr></tr>
                                <tr>
                                    <td style="border-bottom: 1px dotted #000;"></td>
                                    <td colspan="2"></td>
                                    <td style="border-bottom: 1px dotted #000;"></td>
                                    <td colspan="3"></td>
                                    <td style="border-bottom: 1px dotted #000;"></td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                        </body>
                    </html>
                ';
                
                $filename = 'Daily_Stock_Analysis_' . date('Ymd_His') . '.xls';
                
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                
                echo $html;
            } else {
                echo json_encode([
                    "status" => "failed", 
                    "message" => "Record not found"
                    ]);
            }
        }
    }else{
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Something Goes Wrong"
            ));
    }
} else {
    echo json_encode([
        "status" => "failed", 
        "message" => "Please fill in all the fields"
    ]);
}
?>
