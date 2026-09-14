<?php
session_start();
header('Content-Type: application/json');

echo json_encode(
    array(
        "status"=> "failed",
        "message"=> "Direct inventory editing is not allowed. Please use inventory adjustment."
    )
);
?>
