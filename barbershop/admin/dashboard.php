<?php
require '../config/koneksi.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../auth/login.php"); exit; }

$jml_layanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM layanan"))['total'];
$jml_alat    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM alat"))['total'];
$jml_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi"))['total'];
$pendapatan  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE status_bayar='Lunas'"))['total'];

include '../includes/header.php';
include '../includes/sidebar.php';
?>
<h4 class="fw-bold mb-4">Dashboard Admin</h4>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm p-3 bg-white"><small class="text-muted">Total Layanan</small><h3 class="fw-bold"><?= $jml_layanan; ?></h3></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm p-3 bg-white"><small class="text-muted">Total Unit Alat</small><h3 class="fw-bold"><?= $jml_alat ?: 0; ?></h3></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm p-3 bg-white"><small class="text-muted">Booking Masuk</small><h3 class="fw-bold"><?= $jml_booking; ?></h3></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm p-3 bg-white"><small class="text-muted">Total Pendapatan</small><h3 class="fw-bold"><?= rupiah($pendapatan ?: 0); ?></h3></div></div>
</div>
<?php include '../includes/footer.php'; ?>