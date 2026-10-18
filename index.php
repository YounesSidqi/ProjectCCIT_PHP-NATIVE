<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ProjectCCIT_PHP_NATIVE — Front Controller
// Delegates request routing to controllers/controllers.php
require_once __DIR__ . '/controllers/controllers.php';
?>
