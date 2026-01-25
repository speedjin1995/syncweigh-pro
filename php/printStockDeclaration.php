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
                            </style>
                        </head>
                        <body>
                            <table>
                                <tr>
                                    <td rowspan="2" style="width: 15%; text-align: center; font-weight: bold;">Logo</td>
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
                                </tr>
                                <tr><td>10 mm</td><td></td><td>5.10%</td><td>-</td><td></td><td></td><td></td><td></td><td>-</td><td>-</td><td></td><td></td></tr>
                                <tr><td>20 mm</td><td></td><td>4.30%</td><td>-</td><td></td><td></td><td></td><td></td><td>-</td><td>-</td><td></td><td></td></tr>
                                <tr><td>3/8 WC</td><td></td><td>5.20%</td><td>-</td><td></td><td></td><td></td><td></td><td>-</td><td>-</td><td></td><td></td></tr>
                                <tr><td>AC 10</td><td></td><td>5.10%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>AC 14</td><td></td><td>4.90%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>AC 28</td><td></td><td>4.10%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>SMA20</td><td></td><td>5.30%</td><td>-</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>ACB 20</td><td></td><td>4.00%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>ACB 28</td><td></td><td>4.00%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>ACW 14</td><td></td><td>5.10%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>ACW 20</td><td></td><td>4.90%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>Dust Mix</td><td></td><td>6.50%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td><b>Subtotal</b></td><td></td><td></td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td><b>Incoming</b></td><td></td><td></td><td></td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Ordered Bitumen</b></td><td></td><td></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Opening Stock</b></td><td></td><td></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td class="left"><b>Targeted Usage</b></td><td></td><td></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td></td><td></td><td></td><td></td></tr>
                                <tr class="no-border"><td></td><td></td><td></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td></td><td></td><td></td><td></td></tr>
                                <tr>
                                </tr>
                                <tr class="no-border">
                                    <td colspan="8"></td>
                                    <td class="right"><b>Date :</b></td>
                                    <td colspan="3"></td>
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
                                </tr>
                                <tr><td>10 mm</td><td></td><td>5.10%</td><td>-</td><td></td><td></td><td></td><td></td><td>-</td><td>-</td><td></td><td></td></tr>
                                <tr><td>20 mm</td><td></td><td>4.30%</td><td>-</td><td></td><td></td><td></td><td></td><td>-</td><td>-</td><td></td><td></td></tr>
                                <tr><td>3/8 WC</td><td></td><td>5.20%</td><td>-</td><td></td><td></td><td></td><td></td><td>-</td><td>-</td><td></td><td></td></tr>
                                <tr><td>AC 10</td><td></td><td>5.10%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>AC 14</td><td></td><td>4.90%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>AC 28</td><td></td><td>4.10%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>SMA20</td><td></td><td>5.30%</td><td>-</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>ACB 20</td><td></td><td>4.00%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>ACB 28</td><td></td><td>4.00%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>ACW 14</td><td></td><td>5.10%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>ACW 20</td><td></td><td>4.90%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td>Dust Mix</td><td></td><td>6.50%</td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td><b>Subtotal</b></td><td></td><td></td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td><b>Incoming</b></td><td></td><td></td><td></td><td>-</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="left"><b>Opening Stock</b></td><td></td><td></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="left"><b>Supplied Bitumen</b></td><td></td><td></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td class="left"><b>LFO Usage</b></td><td>#DIV/0!</td><td></td><td></td></tr>
                                <tr><td class="left"><b>Targeted Bitumen Usage</b></td><td></td><td></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td class="left"><b>Diesel Usage</b></td><td>0.00</td><td></td><td></td></tr>
                                <tr><td class="left"><b>Targeted Closing Stock</b></td><td></td><td></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="left"><b>Actual Stock (after production)</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td class="left"><b>Variant :</b></td><td></td><td></td><td>0.00</td><td>0.00</td><td>0.00</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
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
                                    <td></td>
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
