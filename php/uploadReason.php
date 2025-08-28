<?php
session_start();
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    foreach ($data as $rows) {
        $Reason = !empty($rows['Reason']) ? trim($rows['Reason']) : '';

        # Check if reason exist in DB
        $status = "0";
        $reasonQuery = "SELECT * FROM Reasons WHERE reason = '$Reason' AND status = '$status'";
        $reasonDetail = mysqli_query($db, $reasonQuery);
        $reasonRow = mysqli_fetch_assoc($reasonDetail);

        if(empty($reasonRow)){
            if ($insert_stmt = $db->prepare("INSERT INTO Reasons (reason, created_by) VALUES (?, ?)")) {
                $insert_stmt->bind_param('ss', $Reason, $uid);
                $insert_stmt->execute();
                $insert_stmt->close();                           
            }
        }
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
