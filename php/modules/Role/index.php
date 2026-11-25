<?php
/**
 * Role endpoint: php/modules/Role/
 *
 * Post or get an "action" field (see RoleController for the list).
 */
session_start(); // must run before permissions.php is loaded
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../controllers/RoleController.php';

$controller = new RoleController($db);
$controller->handleRequest();
