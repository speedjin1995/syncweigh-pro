<?php
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
                $batchDrum = $row['batch_drum'];

                // Get Bitumen Raw Mat Id
                $result = $db->query("SELECT id FROM Raw_Mat WHERE raw_mat_code = 'BTBI001' AND status = 0 LIMIT 1");
                $bitumenRawMatId = $result ? $result->fetch_assoc()['id'] ?? null : null;

                $products = $db->query("
                    SELECT STL.*, P.name AS product_name, PRW.raw_mat_basic_uom AS percentage FROM Stock_Take_List STL 
                    JOIN Product P ON STL.product_id = P.id 
                    JOIN Product_RawMat PRW ON PRW.product_id = P.id
                    WHERE STL.plant_id = " . $plantId . " AND STL.batch_drum = '" . $batchDrum ."' 
                    AND PRW.raw_mat_id = ". $bitumenRawMatId ." AND PRW.plant_id = " . $plantId . " AND PRW.batch_drum = '" . $batchDrum ."' AND PRW.status = 0
                    
                    ORDER BY STL.sort ASC"
                );

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
                                    <td colspan="6" style="width: 45%; text-align: center; font-weight: bold;">
                                        <div class="title">EAST ROCK MARKETING SDN BHD</div>
                                        <div style="font-weight: normal;">(130037-H)</div>
                                    </td>
                                    <td colspan="5" style="width: 20%; text-align: center; font-weight: bold;">LOCATION:<br><span style="font-weight: normal;">' . htmlspecialchars($row['plant_code']) . '</span></td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="width: 45%; text-align: center; font-weight: bold;">DAILY STOCK ANALYSIS</td>
                                    <td colspan="5" style="width: 20%; text-align: center; font-weight: bold;">WORKSHEET:<br> <span style="font-weight: normal;">ERM-OP-02-01</span></td>
                                </tr>
                                <tr class="no-border">
                                    <td colspan="12"></td>
                                </tr>
                                <tr class="no-border">
                                    <td class="left" colspan="5"><b>Plant :</b> '.htmlspecialchars($row['batch_drum']).'</td>
                                    <td colspan="4"></td>
                                    <td class="right" colspan="2"><b>Date :</b> '.date('d/m/Y', strtotime($row['declaration_datetime'])).'</td>
                                    <td></td>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                    <th>Planning</th>
                                    <th colspan="7">Targeted Bitumen (ton)</th>
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
                                    <th>PG76</th>
                                    <th>LMB (LATEX)</th>
                                    <th>CMB</th>
                                    <th>CRMB</th>
                                    <th>(litre)</th>
                                    <th>(litre)</th>
                                    <th></th>
                                    <th></th>
                                </tr>';

                                $rowNum = 10;
                                foreach ($products as $product){
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
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    ';
                                    $rowNum++;
                                }

                                $html .= '
                                <tr><td><b>Subtotal</b></td><td>=SUM(B10:B'.($rowNum-1).')</td><td></td><td>=SUM(D10:D'.($rowNum-1).')</td><td>=SUM(E10:E'.($rowNum-1).')</td><td>=SUM(F10:F'.($rowNum-1).')</td><td>=SUM(G10:G'.($rowNum-1).')</td><td>=SUM(H10:H'.($rowNum-1).')</td><td></td><td></td><td></td><td></td></tr>
                                <tr><td><b>Incoming</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Ordered Bitumen</b></td><td></td><td></td><td>=D'.($rowNum+1).'</td><td>=E'.($rowNum+1).'</td><td>=F'.($rowNum+1).'</td><td>=G'.($rowNum+1).'</td><td>=H'.($rowNum+1).'</td><td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Opening Stock</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Targeted Usage</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td></td><td></td><td></td><td class="border-up-down">=SUM(D'.($rowNum+2).'+D'.($rowNum+3).'-D'.($rowNum+4).')</td><td class="border-up-down">=SUM(E'.($rowNum+2).'+E'.($rowNum+3).'-E'.($rowNum+4).')</td><td class="border-up-down">=SUM(F'.($rowNum+2).'+F'.($rowNum+3).'-F'.($rowNum+4).')</td><td class="border-up-down">=SUM(G'.($rowNum+2).'+G'.($rowNum+3).'-G'.($rowNum+4).')</td><td class="border-up-down">=SUM(H'.($rowNum+2).'+H'.($rowNum+3).'-H'.($rowNum+4).')</td><td></td><td></td><td></td><td></td></tr>
                                <tr>
                                </tr>
                                <tr class="no-border">
                                    <td colspan="9"></td>
                                    <td class="right"><b>Date :</b></td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th>Actual</th>
                                    <th colspan="7">Targeted Bitumen (ton)</th>
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
                                    <th>PG76</th>
                                    <th>LMB (LATEX)</th>
                                    <th>CMB</th>
                                    <th>CRMB</th>
                                    <th>(litre)</th>
                                    <th>(litre)</th>
                                    <th></th>
                                    <th></th>
                                </tr>';

                                $rowNum = $rowNum+10;
                                $subtotalRowStart = $rowNum;
                                foreach ($products as $product){
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
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    ';
                                    $rowNum++;
                                }

                                $html .= '
                                <tr><td><b>Subtotal</b></td><td>=SUM(B'.$subtotalRowStart.':B'.($rowNum-1).')</td><td></td><td>=SUM(D'.$subtotalRowStart.':D'.($rowNum-1).')</td><td>=SUM(E'.$subtotalRowStart.':E'.($rowNum-1).')</td><td>=SUM(F'.$subtotalRowStart.':F'.($rowNum-1).')</td><td>=SUM(G'.$subtotalRowStart.':G'.($rowNum-1).')</td><td>=SUM(H'.$subtotalRowStart.':H'.($rowNum-1).')</td><td></td><td></td><td></td><td></td></tr>
                                <tr><td><b>Incoming</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="left"><b>Opening Stock</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="left"><b>Supplied Bitumen</b></td><td></td><td></td><td>=D'.($rowNum+1).'</td><td>=E'.($rowNum+1).'</td><td>=F'.($rowNum+1).'</td><td>=G'.($rowNum+1).'</td><td>=H'.($rowNum+1).'</td><td class="left"><b>LFO Usage</b></td><td>=B'.$rowNum.'/I'.$rowNum.'</td><td>Litre / ton</td><td></td></tr>
                                <tr><td class="left"><b>Targeted Bitumen Usage</b></td><td></td><td></td><td>=D'.($rowNum).'</td><td>=E'.($rowNum).'</td><td>=F'.($rowNum).'</td><td>=G'.($rowNum).'</td><td>=H'.($rowNum).'</td><td class="left"><b>Diesel Usage</b></td><td>=J'.$rowNum.'</td><td>Litre /day</td><td></td></tr>
                                <tr><td class="left"><b>Targeted Closing Stock</b></td><td></td><td></td><td>=SUM(D'.($rowNum+2).'+D'.($rowNum+3).'-D'.($rowNum+4).')</td><td>=SUM(E'.($rowNum+2).'+E'.($rowNum+3).'-E'.($rowNum+4).')</td><td>=SUM(F'.($rowNum+2).'+F'.($rowNum+3).'-F'.($rowNum+4).')</td><td>=SUM(G'.($rowNum+2).'+G'.($rowNum+3).'-G'.($rowNum+4).')</td><td>=SUM(H'.($rowNum+2).'+H'.($rowNum+3).'-H'.($rowNum+4).')</td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="left"><b>Actual Stock (after production)</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="left"><b>Variant :</b></td><td></td><td></td><td>=D'.($rowNum+6).'-D'.($rowNum+5).'</td><td>=E'.($rowNum+6).'-E'.($rowNum+5).'</td><td>=F'.($rowNum+6).'-F'.($rowNum+5).'</td><td>=G'.($rowNum+6).'-G'.($rowNum+5).'</td><td>=H'.($rowNum+6).'-H'.($rowNum+5).'</td><td></td><td></td><td></td><td></td></tr>
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
