<?php
/**
 * Translation endpoint: php/modules/Translation/
 *
 * Post an "action" field (see TranslationController for the list).
 */
session_start(); // must run before permissions.php is loaded
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../controllers/TranslationController.php';

$controller = new TranslationController($db);
$controller->handleRequest();
