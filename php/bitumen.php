<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
$phoneNo = $_SESSION['plant'];
$faxNo = date("Y-m-d H:i:s");

// Processing form data when form is submitted
if (empty($_POST["bitumenId"])) {
    $bitumenId = null;
} else {
    $bitumenId = trim($_POST["bitumenId"]);
}

if (empty($_POST["plant"])) {
    $plant = null;
} else {
    $plant = trim($_POST["plant"]);
}

if (empty($_POST["plantCode"])) {
    $plantCode = null;
} else {
    $plantCode = trim($_POST["plantCode"]);
} 

if (empty($_POST["batchDrum"])) {
    $batchDrum = null;
} else {
    $batchDrum = trim($_POST["batchDrum"]);
} 

if (empty($_POST["datetime"])) {
    $declarationDatetime = null;
} else {
    $declarationDatetime = DateTime::createFromFormat('d-m-Y H:i', $_POST["datetime"])->format('Y-m-d H:i:s');
}

# Processing for 60/70 data
if (empty($_POST["totalSixtySeventy"])) {
    $totalSixtySeventy = null;
} else {
    $totalSixtySeventy = trim($_POST["totalSixtySeventy"]);
}

if (empty($_POST["bitumenIncoming"])) {
    $bitumenIncoming = null;
} else {
    $bitumenIncoming = trim($_POST["bitumenIncoming"]);
}

if (!empty($_POST["no"]) && count($_POST["no"]) > 0) {
    $sixtySeventyData = [];
    $no = $_POST["no"];
    $assetId = $_POST["assetId"];
    $name = $_POST["name"];
    $bitumenStatus = $_POST["bitumenStatus"];
    $temp = $_POST["temp"];
    $level = $_POST["level"];
    $actualLevel = $_POST["actualLevel"];
    $sixtyseventy = $_POST["sixtyseventy"];

    foreach ($no as $key => $value) {
        $sixtySeventyData[] = array(
            "no" => $no[$key],
            "assetId" => $assetId[$key],
            "name" => $name[$key],
            "bitumenStatus" => $bitumenStatus[$key],
            "temperature" => $temp[$key],
            "level" => $level[$key],
            "actualLevel" => $actualLevel[$key],
            "sixtyseventy" => $sixtyseventy[$key],
        );
    }

    $sixtySeventyData['bitumenIncoming'] = $bitumenIncoming;
    $sixtySeventyData['totalSixtySeventy'] = $totalSixtySeventy;
    // $sixtySeventyData['totalTemperature'] = $_POST["totalTemp"];
    // $sixtySeventyData['totalLevel'] = $_POST["totalLevel"];
    $sixtySeventyData = json_encode($sixtySeventyData, JSON_PRETTY_PRINT);
} else {
    $sixtySeventyData = [];

    $sixtySeventyData['bitumenIncoming'] = $bitumenIncoming;
    $sixtySeventyData['totalSixtySeventy'] = $totalSixtySeventy;
    $sixtySeventyData = json_encode($sixtySeventyData, JSON_PRETTY_PRINT);
}

# Processing for lfo data
if (empty($_POST["lfoIncoming"])) {
    $lfoIncoming = null;
} else {
    $lfoIncoming = trim($_POST["lfoIncoming"]);
}

if (empty($_POST["lfoLastMeterReading"])) {
    $lfoLastMeterReading = null;
} else {
    $lfoLastMeterReading = trim($_POST["lfoLastMeterReading"]);
}

if (empty($_POST["totalLfo"])) {
    $totalLfo = null;
} else {
    $totalLfo = trim($_POST["totalLfo"]);
}

if (!empty($_POST["lfoNo"]) && count($_POST["lfoNo"]) > 0) {
    $lfoData = [];
    $lfoNo = $_POST["lfoNo"];
    $lfoAssetId = $_POST["lfoAssetId"];
    $lfoName = $_POST["lfoName"];
    $lfoStatus = $_POST["lfoStatus"];
    $lfoLevel = $_POST["lfoLevel"];
    $lfoActualLevel = $_POST["lfoActualLevel"];
    $lfoVolume = $_POST["lfoVolume"];
    $lfoWeight = $_POST["lfoWeight"];

    foreach ($lfoNo as $key => $no) {
        $lfoData[] = array(
            "no" => $no,
            "lfoAssetId" => $lfoAssetId[$key],
            "lfoName" => $lfoName[$key],
            "lfoStatus" => $lfoStatus[$key],
            "lfoLevel" => $lfoLevel[$key],
            "lfoActualLevel" => $lfoActualLevel[$key],
            "lfoVolume" => $lfoVolume[$key],
            "lfoWeight" => $lfoWeight[$key]
        );
    }

    $lfoData['lfoIncoming'] = $lfoIncoming;
    $lfoData['lfoLastMeterReading'] = $lfoLastMeterReading;
    $lfoData['totalLfo'] = $totalLfo;
    $lfoData = json_encode($lfoData, JSON_PRETTY_PRINT);
} else {
    $lfoData = [];

    $lfoData['lfoIncoming'] = $lfoIncoming;
    $lfoData['lfoLastMeterReading'] = $lfoLastMeterReading;
    $lfoData['totalLfo'] = $totalLfo;
    $lfoData = json_encode($lfoData, JSON_PRETTY_PRINT);
}

# Processing for diesel data
// if (empty($_POST["dieselSupplierTransport"])) {
//     $dieselSupplierTransport = null;
// } else {
//     $dieselSupplierTransport = trim($_POST["dieselSupplierTransport"]);
// }

// if (empty($_POST["dieselUsageTransport"])) {
//     $dieselUsageTransport = null;
// } else {
//     $dieselUsageTransport = trim($_POST["dieselUsageTransport"]);
// }

// if (empty($_POST["dieselWeightTransport"])) {
//     $dieselWeightTransport = null;
// } else {
//     $dieselWeightTransport = trim($_POST["dieselWeightTransport"]);
// }

// if (empty($_POST["dieselSupplierHotoil"])) {
//     $dieselSupplierHotoil = null;
// } else {
//     $dieselSupplierHotoil = trim($_POST["dieselSupplierHotoil"]);
// }

// if (empty($_POST["dieselUsageHotoil"])) {
//     $dieselUsageHotoil = null;
// } else {
//     $dieselUsageHotoil = trim($_POST["dieselUsageHotoil"]);
// }

// if (empty($_POST["dieselWeightHotoil"])) {
//     $dieselWeightHotoil = null;
// } else {
//     $dieselWeightHotoil = trim($_POST["dieselWeightHotoil"]);
// }

// if (empty($_POST["dieselSupplierBurner"])) {
//     $dieselSupplierBurner = null;
// } else {
//     $dieselSupplierBurner = trim($_POST["dieselSupplierBurner"]);
// }

// if (empty($_POST["dieselUsageBurner"])) {
//     $dieselUsageBurner = null;
// } else {
//     $dieselUsageBurner = trim($_POST["dieselUsageBurner"]);
// }

// if (empty($_POST["dieselWeightBurner"])) {
//     $dieselWeightBurner = null;
// } else {
//     $dieselWeightBurner = trim($_POST["dieselWeightBurner"]);
// }

if (empty($_POST["dieselIncoming"])) {
    $dieselIncoming = null;
} else {
    $dieselIncoming = trim($_POST["dieselIncoming"]);
}

if (empty($_POST["previousDieselReading"])) {
    $previousDieselReading = null;
} else {
    $previousDieselReading = trim($_POST["previousDieselReading"]);
}

if (empty($_POST["dieselLastMeterReading"])) {
    $dieselLastMeterReading = null;
} else {
    $dieselLastMeterReading = trim($_POST["dieselLastMeterReading"]);
}

if (empty($_POST["totalDiesel"])) {
    $totalDiesel = null;
} else {
    $totalDiesel = trim($_POST["totalDiesel"]);
}

if (!empty($_POST["dieselNo"]) && count($_POST["dieselNo"]) > 0) {
    $dieselData = [];
    // $dieselData[] = array(
    //     "dieselSupplierTransport" => $dieselSupplierTransport,
    //     "dieselUsageTransport" => $dieselUsageTransport,
    //     "dieselWeightTransport" => $dieselWeightTransport,
    // );
    // $dieselData[] = array(
    //     "dieselSupplierHotoil" => $dieselSupplierHotoil,
    //     "dieselUsageHotoil" => $dieselUsageHotoil,
    //     "dieselWeightHotoil" => $dieselWeightHotoil,
    // );
    // $dieselData[] = array(
    //     "dieselSupplierBurner" => $dieselSupplierBurner,
    //     "dieselUsageBurner" => $dieselUsageBurner,
    //     "dieselWeightBurner" => $dieselWeightBurner,
    // );

    // $dieselSupplier = $_POST["dieselSupplier"];
    // $dieselUsage = $_POST["dieselUsage"];
    $dieselNo = $_POST["dieselNo"];
    $dieselAssetId = $_POST["dieselAssetId"];
    $dieselName = $_POST["dieselName"];
    $dieselStatus = $_POST["dieselStatus"];
    $dieselLevel = $_POST["dieselLevel"];
    $dieselActualLevel = $_POST["dieselActualLevel"];
    $dieselVolume = $_POST["dieselVolume"];
    $dieselWeight = $_POST["dieselWeight"];

    foreach ($dieselNo as $key => $no) {
        $dieselData[] = array(
            "no" => $no,
            "dieselAssetId" => $dieselAssetId[$key],
            "dieselName" => $dieselName[$key],
            "dieselStatus" => $dieselStatus[$key],
            "dieselLevel" => $dieselLevel[$key],
            "dieselActualLevel" => $dieselActualLevel[$key],
            "dieselVolume" => $dieselVolume[$key],
            // "dieselSupplier" => $dieselSupplier[$key],
            // "dieselUsage" => $dieselUsage[$key],
            "dieselWeight" => $dieselWeight[$key]
        );
    }

    $dieselData['dieselIncoming'] = $dieselIncoming;
    $dieselData['previousDieselReading'] = $previousDieselReading;
    $dieselData['dieselLastMeterReading'] = $dieselLastMeterReading;
    $dieselData['totalDiesel'] = $totalDiesel;
    $dieselData = json_encode($dieselData, JSON_PRETTY_PRINT);
} else {
    $dieselData = [];
    // $dieselData[] = array(
    //     "dieselSupplierTransport" => $dieselSupplierTransport,
    //     "dieselUsageTransport" => $dieselUsageTransport,
    //     "dieselWeightTransport" => $dieselWeightTransport,
    // );
    // $dieselData[] = array(
    //     "dieselSupplierHotoil" => $dieselSupplierHotoil,
    //     "dieselUsageHotoil" => $dieselUsageHotoil,
    //     "dieselWeightHotoil" => $dieselWeightHotoil,
    // );
    // $dieselData[] = array(
    //     "dieselSupplierBurner" => $dieselSupplierBurner,
    //     "dieselUsageBurner" => $dieselUsageBurner,
    //     "dieselWeightBurner" => $dieselWeightBurner,
    // );

    $dieselData['dieselIncoming'] = $dieselIncoming;
    $dieselData['previousDieselReading'] = $previousDieselReading;
    $dieselData['dieselLastMeterReading'] = $dieselLastMeterReading;
    $dieselData['totalDiesel'] = $totalDiesel;
    $dieselData = json_encode($dieselData, JSON_PRETTY_PRINT);
}

# Processing for Other Diesel Table data
if (empty($_POST["otherDieselTotalTransportUsage"])) {
    $otherDieselTotalTransportUsage = null;
} else {
    $otherDieselTotalTransportUsage = trim($_POST["otherDieselTotalTransportUsage"]);
}

if (!empty($_POST["otherDieselNo"]) && count($_POST["otherDieselNo"]) > 0) {
    $otherDieselData = [];
    $no = $_POST["otherDieselNo"];
    $otherDieselType = $_POST["otherDieselType"];
    $otherDieselVehicleNo = $_POST["otherDieselVehicleNo"];
    $otherDieselFirstReading = $_POST["otherDieselFirstReading"];
    $otherDieselSecondReading = $_POST["otherDieselSecondReading"];
    $otherDieselUsage = $_POST["otherDieselUsage"];

    foreach ($no as $key => $value) {
        $otherDieselData[] = array(
            "no" => $no[$key],
            "otherDieselType" => $otherDieselType[$key],
            "otherDieselVehicleNo" => $otherDieselVehicleNo[$key],
            "otherDieselFirstReading" => $otherDieselFirstReading[$key],
            "otherDieselSecondReading" => $otherDieselSecondReading[$key],
            "otherDieselUsage" => $otherDieselUsage[$key]
        );
    }

    $otherDieselData['otherDieselTotalTransportUsage'] = $otherDieselTotalTransportUsage;
    $otherDieselData = json_encode($otherDieselData, JSON_PRETTY_PRINT);
} else {
    $otherDieselData = NULL;
}

# Processing for hotoil data
if (!empty($_POST["hotoilNo"]) && count($_POST["hotoilNo"]) > 0) {
    $hotoilData = [];
    $hotoilNo = $_POST["hotoilNo"];
    $hotoilWeight = $_POST["hotoilWeight"];

    foreach ($hotoilNo as $key => $no) {
        $hotoilData[] = array(
            "no" => $no,
            "hotoilWeight" => $hotoilWeight[$key]
        );
    }

    $hotoilData['totalHotoil'] = $_POST["totalHotoil"];
    $hotoilData = json_encode($hotoilData, JSON_PRETTY_PRINT);
} else {
    $hotoilData = NULL;
}

# Processing for pg76No data
if (empty($_POST["totalPg76"])) {
    $totalPg76 = null;
} else {
    $totalPg76 = trim($_POST["totalPg76"]);
}

if (!empty($_POST["pg76No"]) && count($_POST["pg76No"]) > 0) {
    $pg76Data = [];
    $pg76No = $_POST["pg76No"];
    $pg76AssetId = $_POST["pg76AssetId"];
    $pg76Name = $_POST["pg76Name"];
    $pg76Status = $_POST["pg76Status"];
    $pg76Temp = $_POST["pg76Temp"];
    $pg76Level = $_POST["pg76Level"];
    $pg76ActualLevel = $_POST["pg76ActualLevel"];
    $pgSeventySix = $_POST["pgSeventySix"];

    foreach ($pg76No as $key => $no) {
        $pg76Data[] = array(
            "no" => $no,
            "pg76AssetId" => $pg76AssetId[$key],
            "pg76Name" => $pg76Name[$key],
            "pg76Status" => $pg76Status[$key],
            "pg76Temp" => $pg76Temp[$key],
            "pg76Level" => $pg76Level[$key],
            "pg76ActualLevel" => $pg76ActualLevel[$key],
            "pgSeventySix" => $pgSeventySix[$key]
        );
    }

    $pg76Data['totalPg76'] = $totalPg76;
    $pg76Data = json_encode($pg76Data, JSON_PRETTY_PRINT);
} else {
    $pg76Data = [];

    $pg76Data['totalPg76'] = $totalPg76;
    $pg76Data = json_encode($pg76Data, JSON_PRETTY_PRINT);

}


if (empty($_POST["40mm"])) {
    $fortymm = null;
} else {
    $fortymm = trim($_POST["40mm"]);
} 

if (empty($_POST["28mm"])) {
    $twentyeightmm = null;
} else {
    $twentyeightmm = trim($_POST["28mm"]);
} 

if (empty($_POST["20mm"])) {
    $twenty_mm = null;
} else {
    $twenty_mm = trim($_POST["20mm"]);
} 

if (empty($_POST["14mm"])) {
    $fourteen_mm = null;
} else {
    $fourteen_mm = trim($_POST["14mm"]);
} 

if (empty($_POST["10mm"])) {
    $ten_mm = null;
} else {
    $ten_mm = trim($_POST["10mm"]);
} 

if (empty($_POST["QD"])) {
    $qd = null;
} else {
    $qd = trim($_POST["QD"]);
} 

if (empty($_POST["fibreNameMr6"])) {
    $fibreNameMr6 = null;
} else {
    $fibreNameMr6 = trim($_POST["fibreNameMr6"]);
}

if (empty($_POST["fibreTypeMr6"])) {
    $fibreTypeMr6 = 0.00;
} else {
    $fibreTypeMr6 = trim($_POST["fibreTypeMr6"]);
}

if (empty($_POST["fibreBagsMr6"])) {
    $fibreBagsMr6 = 0;
} else {
    $fibreBagsMr6 = trim($_POST["fibreBagsMr6"]);
}

if (empty($_POST["fibreQtyMr6"])) {
    $fibreQtyMr6 = 0.00;
} else {
    $fibreQtyMr6 = trim($_POST["fibreQtyMr6"]);
}

if (empty($_POST["fibreNameRpf"])) {
    $fibreNameRpf = null;
} else {
    $fibreNameRpf = trim($_POST["fibreNameRpf"]);
}

if (empty($_POST["fibreTypeRpf"])) {
    $fibreTypeRpf = 0.00;
} else {
    $fibreTypeRpf = trim($_POST["fibreTypeRpf"]);
}

if (empty($_POST["fibreBagsRpf"])) {
    $fibreBagsRpf = 0;
} else {
    $fibreBagsRpf = trim($_POST["fibreBagsRpf"]);
}

if (empty($_POST["fibreQtyRpf"])) {
    $fibreQtyRpf = 0.00;
} else {
    $fibreQtyRpf = trim($_POST["fibreQtyRpf"]);
}

if (empty($_POST["fibreNameNova"])) {
    $fibreNameNova = null;
} else {
    $fibreNameNova = trim($_POST["fibreNameNova"]);
}

if (empty($_POST["fibreTypeNova"])) {
    $fibreTypeNova = 0.00;
} else {
    $fibreTypeNova = trim($_POST["fibreTypeNova"]);
}

if (empty($_POST["fibreBagsNova"])) {
    $fibreBagsNova = 0;
} else {
    $fibreBagsNova = trim($_POST["fibreBagsNova"]);
}

if (empty($_POST["fibreQtyNova"])) {
    $fibreQtyNova = 0.00;
} else {
    $fibreQtyNova = trim($_POST["fibreQtyNova"]);
}

if (empty($_POST["fibreNameForta"])) {
    $fibreNameForta = null;
} else {
    $fibreNameForta = trim($_POST["fibreNameForta"]);
}

if (empty($_POST["fibreTypeForta"])) {
    $fibreTypeForta = 0.00;
} else {
    $fibreTypeForta = trim($_POST["fibreTypeForta"]);
}

if (empty($_POST["fibreBagsForta"])) {
    $fibreBagsForta = 0;
} else {
    $fibreBagsForta = trim($_POST["fibreBagsForta"]);
}

if (empty($_POST["fibreQtyForta"])) {
    $fibreQtyForta = 0.00;
} else {
    $fibreQtyForta = trim($_POST["fibreQtyForta"]);
}

# Processing for fibreNo data
if (!empty($_POST["fibreNo"]) && count($_POST["fibreNo"]) > 0) {
    $fibreData = [];
    $fibreNo = $_POST["fibreNo"];
    $fibreName = $_POST["fibreName"];
    $fibreType = $_POST["fibreType"];
    $fibreNoOfBags = $_POST["fibreNoOfBags"];
    $fibreQty = $_POST["fibreQty"];

    $fibreData[] = array(
        "fibreNameMr6" => $fibreNameMr6,
        "fibreTypeMr6" => $fibreTypeMr6,
        "fibreBagsMr6" => $fibreBagsMr6,
        "fibreQtyMr6" => $fibreQtyMr6,
    );
    $fibreData[] = array(
        "fibreNameRpf" => $fibreNameRpf,
        "fibreTypeRpf" => $fibreTypeRpf,
        "fibreBagsRpf" => $fibreBagsRpf,
        "fibreQtyRpf" => $fibreQtyRpf,
    );
    $fibreData[] = array(
        "fibreNameNova" => $fibreNameNova,
        "fibreTypeNova" => $fibreTypeNova,
        "fibreBagsNova" => $fibreBagsNova,
        "fibreQtyNova" => $fibreQtyNova,
    );
    $fibreData[] = array(
        "fibreNameForta" => $fibreNameForta,
        "fibreTypeForta" => $fibreTypeForta,
        "fibreBagsForta" => $fibreBagsForta,
        "fibreQtyForta" => $fibreQtyForta,
    );

    foreach ($fibreNo as $key => $no) {
        $fibreData[] = array(
            "no" => $no,
            "fibreName" => $fibreName[$key],
            "fibreType" => $fibreType[$key],
            "fibreBags" => $fibreNoOfBags[$key],
            "fibreQty" => $fibreQty[$key],
        );
    }

    $fibreData = json_encode($fibreData, JSON_PRETTY_PRINT);
} else {
    $fibreData = [];

    $fibreData[] = array(
        "fibreNameMr6" => $fibreNameMr6,
        "fibreTypeMr6" => $fibreTypeMr6,
        "fibreBagsMr6" => $fibreBagsMr6,
        "fibreQtyMr6" => $fibreQtyMr6,
    );
    $fibreData[] = array(
        "fibreNameRpf" => $fibreNameRpf,
        "fibreTypeRpf" => $fibreTypeRpf,
        "fibreBagsRpf" => $fibreBagsRpf,
        "fibreQtyRpf" => $fibreQtyRpf,
    );
    $fibreData[] = array(
        "fibreNameNova" => $fibreNameNova,
        "fibreTypeNova" => $fibreTypeNova,
        "fibreBagsNova" => $fibreBagsNova,
        "fibreQtyNova" => $fibreQtyNova,
    );
    $fibreData[] = array(
        "fibreNameForta" => $fibreNameForta,
        "fibreTypeForta" => $fibreTypeForta,
        "fibreBagsForta" => $fibreBagsForta,
        "fibreQtyForta" => $fibreQtyForta,
    );

    $fibreData = json_encode($fibreData, JSON_PRETTY_PRINT);
}

if (empty($_POST["rs1k"])) {
    $rs1k = null;
} else {
    $rs1k = trim($_POST["rs1k"]);
} 

if (empty($_POST["k140"])) {
    $k140 = null;
} else {
    $k140 = trim($_POST["k140"]);
} 

if (empty($_POST["ss1k"])) {
    $ss1k = null;
} else {
    $ss1k = trim($_POST["ss1k"]);
} 

if (empty($_POST["others"])) {
    $others = null;
} else {
    $others = trim($_POST["others"]);
} 

if (empty($_POST["transport"])) {
    $transport = null;
} else {
    $transport = trim($_POST["transport"]);
} 

if (empty($_POST["burner"])) {
    $burner = null;
} else {
    $burner = trim($_POST["burner"]);
} 

if (empty($_POST["opcDo"])) {
    $opcDo = null;
} else {
    $opcDo = trim($_POST["opcDo"]);
} 

if (empty($_POST["opcIncoming"])) {
    $opcIncoming = null;
} else {
    $opcIncoming = trim($_POST["opcIncoming"]);
} 

if (empty($_POST["opcQty"])) {
    $opcQty = null;
} else {
    $opcQty = trim($_POST["opcQty"]);
} 

if (empty($_POST["limeDo"])) {
    $limeDo = null;
} else {
    $limeDo = trim($_POST["limeDo"]);
} 

if (empty($_POST["limeIncoming"])) {
    $limeIncoming = null;
} else {
    $limeIncoming = trim($_POST["limeIncoming"]);
} 

if (empty($_POST["limeQty"])) {
    $limeQty = null;
} else {
    $limeQty = trim($_POST["limeQty"]);
} 

# Processing for data
$data = array(
    "40mm" => $fortymm,
    "28mm" => $twentyeightmm,
    "20mm" => $twenty_mm,
    "14mm" => $fourteen_mm,
    "10mm" => $ten_mm,
    "QD" => $qd,
    "rs1k" => $rs1k,
    "k140" => $k140,
    "ss1k" => $ss1k,
    "others" => $others,
    "transport" => $transport,
    "burner" => $burner,
    "opcDo" => $opcDo,
    "opcIncoming" => $opcIncoming,
    "opcQty" => $opcQty,
    "limeDo" => $limeDo,
    "limeIncoming" => $limeIncoming,
    "limeQty" => $limeQty,
);

$data = json_encode($data, JSON_PRETTY_PRINT);

if(!empty($bitumenId)){
    if ($update_stmt = $db->prepare("UPDATE Bitumen SET `60/70`=?, `pg76`=?, `lfo`=?, `diesel`=?, `other_diesel`=?, `hotoil`=?, `fibre`=?, `data`=?, `declaration_datetime`=?, `plant_id`=?, `plant_code`=?, `batch_drum`=?, modified_by=? WHERE id=?")) {
        $update_stmt->bind_param('ssssssssssssss', $sixtySeventyData, $pg76Data, $lfoData, $dieselData, $otherDieselData, $hotoilData, $fibreData, $data, $declarationDatetime, $plant, $plantCode, $batchDrum, $username, $bitumenId);

        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $update_stmt->error
                )
            );
        }
        else{
            $update_stmt->close();
            $db->close();

            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Updated Successfully!!" 
                )
            );
        }
    }
}
else
{ 
    if ($insert_stmt = $db->prepare("INSERT INTO Bitumen (`60/70`, `pg76`, `lfo`, `diesel`, `other_diesel`, `hotoil`, `fibre`, `data`, `declaration_datetime`, `plant_id`, `plant_code`, `batch_drum`, `created_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('sssssssssssss', $sixtySeventyData, $pg76Data, $lfoData, $dieselData, $otherDieselData, $hotoilData, $fibreData, $data, $declarationDatetime, $plant, $plantCode, $batchDrum, $username);

        // Execute the prepared query.
        if (! $insert_stmt->execute()) {
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $insert_stmt->error
                )
            );
        }
        else{
            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Added Successfully!!" 
                )
            );

            $insert_stmt->close();
            $db->close();
            
        }
    }
}
?>