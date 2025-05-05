<?php
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('memory_limit', '512M');
set_time_limit(300);
session_start();
$uid = $_SESSION['username'];

$url = "https://sturgeon-still-falcon.ngrok-free.app/items";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPGET, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 60);
curl_setopt($curl, CURLOPT_VERBOSE, true);

$response = curl_exec($curl);

if ($response === false) {
    echo json_encode([
        "status" => "failed",
        "message" => curl_error($curl)
    ]);
    exit;
}

curl_close($curl);

// Decode the JSON
$data = json_decode($response, true);

if (!empty($data['data'])) {
    require_once 'db_connect.php';
    $agents = $data['data'];
    
    foreach ($agents as $agent) {
        $code = $db->real_escape_string($agent['CODE']);
        $desc = $db->real_escape_string($agent['DESCRIPTION']);
        $active = ($agent['ISACTIVE'] === "True") ? 1 : 0;

        // Use a new statement for checking existence
        $checkQuery = "SELECT COUNT(*) AS count FROM Raw_Mat WHERE raw_mat_code = ?";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bind_param("s", $code);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if ($result['count'] > 0) {
            // Update if exists
            $updateQuery = "UPDATE Raw_Mat SET name = ?, description = ?, modified_by = ? WHERE raw_mat_code = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bind_param("ssss", $desc, $desc, $uid, $code);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Insert if not exists
            $insertQuery = "INSERT INTO Raw_Mat (raw_mat_code, name, description, created_by, modified_by) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bind_param("sssss", $code, $desc, $desc, $uid, $uid);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }

    $db->close();

    echo json_encode([
        "status" => "success",
        "message" => "Data synced successfully!"
    ]);
} else {
    echo json_encode([
        "status" => "failed",
        "message" => "Invalid data received from API"
    ]);
}
?>