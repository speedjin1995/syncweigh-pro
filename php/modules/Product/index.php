<?php
/**
 * Product endpoint: php/modules/Product/
 *
 * Post an "action" field (see ProductController for the list).
 */
session_start(); // must run before permissions.php is loaded
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController($db);
$controller->handleRequest();
