<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host     = "localhost";
$username = "root";
$password = "";
$database = "ProjectCCIT_PHP_NATIVE";

try {
    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    // Provide $conn alias pointing to the PDO instance for full compatibility
    $conn = $pdo;
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>