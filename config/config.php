<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "ProjectCCIT_PHP_NATIVE"; // Sesuaikan dengan nama database baru kamu

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}
?>