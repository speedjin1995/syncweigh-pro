<?php
require_once 'db_connect.php';

session_start();

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

if (empty($_POST["datetime"])) {
    $declarationDatetime = null;
} else {
    $declarationDatetime = DateTime::createFromFormat('d-m-Y H:i', $_POST["datetime"])->format('Y-m-d H:i:s');
}   

# Processing for 60/70 data
if (!empty($_POST["no"]) && count($_POST["no"]) > 0) {
    $sixtySeventyData = [];
    $no = $_POST["no"];
    $sixtyseventy = $_POST["sixtyseventy"];
    $temp = $_POST["temp"];
    $level = $_POST["level"];

    foreach ($no as $key => $value) {
        $sixtySeventyData[] = array(
            "no" => $no[$key],
            "sixtyseventy" => $sixtyseventy[$key],
            "temperature" => $temp[$key],
            "level" => $level[$key],
        );
    }

    $sixtySeventyData['totalSixtySeventy'] = $_POST["totalSixtySeventy"];
    $sixtySeventyData['totalTemperature'] = $_POST["totalTemp"];
    $sixtySeventyData['totalLevel'] = $_POST["totalLevel"];
    $sixtySeventyData = json_encode($sixtySeventyData, JSON_PRETTY_PRINT);
} else {
    $sixtySeventyData = NULL;
}

# Processing for lfo data
if (!empty($_POST["lfoNo"]) && count($_POST["lfoNo"]) > 0) {
    $lfoData = [];
    $lfoNo = $_POST["lfoNo"];
    $lfoWeight = $_POST["lfoWeight"];

    foreach ($lfoNo as $key => $no) {
        $lfoData[] = array(
            "no" => $no,
            "lfoWeight" => $lfoWeight[$key]
        );
    }

    $lfoData['totalLfo'] = $_POST["totalLfo"];
    $lfoData = json_encode($lfoData, JSON_PRETTY_PRINT);
} else {
    $lfoData = NULL;
}

# Processing for diesel data
if (!empty($_POST["dieselNo"]) && count($_POST["dieselNo"]) > 0) {
    $dieselData = [];
    $dieselNo = $_POST["dieselNo"];
    $dieselWeight = $_POST["dieselWeight"];

    foreach ($dieselNo as $key => $no) {
        $dieselData[] = array(
            "no" => $no,
            "dieselWeight" => $dieselWeight[$key]
        );
    }

    $dieselData['totalDiesel'] = $_POST["totalDiesel"];
    $dieselData = json_encode($dieselData, JSON_PRETTY_PRINT);
} else {
    $dieselData = NULL;
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

# Processing for pg79No data
if (!empty($_POST["pg79No"]) && count($_POST["pg79No"]) > 0) {
    $pg79Data = [];
    $pg79No = $_POST["pg79No"];
    $pg79 = $_POST["pgSevenNine"];

    foreach ($pg79No as $key => $no) {
        $pg79Data[] = array(
            "no" => $no,
            "pgSevenNine" => $pg79[$key]
        );
    }

    $pg79Data['totalPgSevenNine'] = $_POST["totalPgSevenNine"];
    $pg79Data = json_encode($pg79Data, JSON_PRETTY_PRINT);
} else {
    $pg79Data = NULL;
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

if (empty($_POST["typeMR6"])) {
    $typeMR6 = null;
} else {
    $typeMR6 = trim($_POST["typeMR6"]);
} 

if (empty($_POST["typeRPF"])) {
    $typeRPF = null;
} else {
    $typeRPF = trim($_POST["typeRPF"]);
} 

if (empty($_POST["typeNovaFiber"])) {
    $typeNovaFiber = null;
} else {
    $typeNovaFiber = trim($_POST["typeNovaFiber"]);
} 

if (empty($_POST["typeFortaFiber"])) {
    $typeFortaFiber = null;
} else {
    $typeFortaFiber = trim($_POST["typeFortaFiber"]);
} 

if (empty($_POST["opcIncoming"])) {
    $opcIncoming = null;
} else {
    $opcIncoming = trim($_POST["opcIncoming"]);
} 

if (empty($_POST["qtyMR6"])) {
    $qtyMR6 = null;
} else {
    $qtyMR6 = trim($_POST["qtyMR6"]);
} 

if (empty($_POST["qtyRPF"])) {
    $qtyRPF = null;
} else {
    $qtyRPF = trim($_POST["qtyRPF"]);
} 

if (empty($_POST["qtyNovaFiber"])) {
    $qtyNovaFiber = null;
} else {
    $qtyNovaFiber = trim($_POST["qtyNovaFiber"]);
} 

if (empty($_POST["qtyFortaFiber"])) {
    $qtyFortaFiber = null;
} else {
    $qtyFortaFiber = trim($_POST["qtyFortaFiber"]);
} 

if (empty($_POST["opcDo"])) {
    $opcDo = null;
} else {
    $opcDo = trim($_POST["opcDo"]);
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

if (empty($_POST["limeIncoming"])) {
    $limeIncoming = null;
} else {
    $limeIncoming = trim($_POST["limeIncoming"]);
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

if (empty($_POST["limeDo"])) {
    $limeDo = null;
} else {
    $limeDo = trim($_POST["limeDo"]);
} 

# Processing for data
$data = array(
    "40mm" => $fortymm,
    "28mm" => $twentyeightmm,
    "20mm" => $twenty_mm,
    "14mm" => $fourteen_mm,
    "10mm" => $ten_mm,
    "QD" => $qd,
    "typeMR6" => $typeMR6,
    "typeRPF" => $typeRPF,
    "typeNovaFiber" => $typeNovaFiber,
    "typeFortaFiber" => $typeFortaFiber,
    "opcIncoming" => $opcIncoming,
    "qtyMR6" => $qtyMR6,
    "qtyRPF" => $qtyRPF,
    "qtyNovaFiber" => $qtyNovaFiber,
    "qtyFortaFiber" => $qtyFortaFiber,
    "opcDo" => $opcDo,
    "rs1k" => $rs1k,
    "k140" => $k140,
    "ss1k" => $ss1k,
    "others" => $others,
    "limeIncoming" => $limeIncoming,
    "transport" => $transport,
    "burner" => $burner,
    "limeDo" => $limeDo
);

$data = json_encode($data, JSON_PRETTY_PRINT);

if(!empty($bitumenId)){
    if ($update_stmt = $db->prepare("UPDATE Bitumen SET `60/70`=?, `pg76`=?, `lfo`=?, `diesel`=?, `hotoil`=?, `data`=?, `declaration_datetime`=?, `plant_id`=?, `plant_code`=?, modified_by=? WHERE id=?")) {
        $update_stmt->bind_param('sssssssssss', $sixtySeventyData, $pg79Data, $lfoData, $dieselData, $hotoilData, $data, $declarationDatetime, $plant, $plantCode, $username, $bitumenId);

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
    if ($insert_stmt = $db->prepare("INSERT INTO Bitumen (`60/70`, `pg76`, `lfo`, `diesel`, `hotoil`, `data`, `declaration_datetime`, `plant_id`, `plant_code`, `created_by`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
        $insert_stmt->bind_param('ssssssssss', $sixtySeventyData, $pg79Data, $lfoData, $dieselData, $hotoilData, $data, $declarationDatetime, $plant, $plantCode, $username);

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