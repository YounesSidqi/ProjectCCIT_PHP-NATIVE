<?php
// ==========================================
// CONFIG: Database Connection & Global Setup
// ==========================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Global User Constant
define('CURRENT_USER_ID', 1);

$host     = "127.0.0.1";
$username = "root";
$password = "";
$database = "ProjectCCIT_PHP_NATIVE";

try {
    $dsn = "mysql:host={$host};port=3306;dbname={$database};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    $conn = $pdo;
} catch (PDOException $e) {
    // Fallback to localhost if 127.0.0.1 fails
    try {
        $dsnLocal = "mysql:host=localhost;dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsnLocal, $username, $password, $options);
        $conn = $pdo;
    } catch (PDOException $e2) {
        die("Koneksi Database Gagal: " . $e->getMessage());
    }
}
?>