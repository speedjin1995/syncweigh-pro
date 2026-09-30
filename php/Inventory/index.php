<?php
/**
 * Inventory endpoint: php/Inventory/
 *
 * Single entry point for all inventory backend calls. Post an "action" field
 * (see InventoryController for the list); the controller calls the services.
 */
session_start(); // must run before permissions.php is loaded
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../controllers/InventoryController.php';

$controller = new InventoryController($db);
$controller->handleRequest();
