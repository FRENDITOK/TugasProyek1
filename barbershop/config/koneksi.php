<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db   = "barbershop_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>