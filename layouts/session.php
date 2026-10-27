<?php
// Bump when assets/js/additional.js changes so browsers fetch the new file
define('JS_VERSION', '1.0.0');

// Initialize the session
session_start();

// // Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>