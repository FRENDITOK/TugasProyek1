<div class="col-md-2 sidebar p-3 d-none d-md-block">
    <h4 class="fw-bold text-white mb-4 px-2"><i class="bi bi-scissors text-info"></i> BarberPro</h4>
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="konsumen.php" class="list-group-item list-group-item-action py-2 ripple <?= basename($_SERVER['PHP_SELF']) == 'konsumen.php' ? 'active' : ''; ?>">
    <i class="bi bi-people me-2"></i> Kelola Konsumen
</a>
        <a href="layanan.php"><i class="bi bi-list-check me-2"></i> CRUD Layanan</a>
        <a href="alat.php"><i class="bi bi-tools me-2"></i> Inventaris Alat</a>
        <a href="laporan.php"><i class="bi bi-cash-coin me-2"></i> Laporan & Bagi Hasil</a>
        <a href="profil.php" class="list-group-item list-group-item-action py-2 ripple <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
    <i class="bi bi-person-gear me-2"></i> Edit Profil
</a>
    <?php else: ?>
        <a href="index.php"><i class="bi bi-calendar-plus me-2"></i> Booking Potong</a>
        <a href="riwayat.php"><i class="bi bi-clock-history me-2"></i> Riwayat & QRIS</a>
        <a href="profil.php" class="list-group-item list-group-item-action py-2 ripple <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
    <i class="bi bi-person-gear me-2"></i> Edit Profil
</a>
    <?php endif; ?>
    <hr class="text-secondary">
    <a href="../auth/logout.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
</div>

<div class="col-md-10 main-content p-0">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3">
        <div class="container-fluid">
            <span class="navbar-brand fw-semibold">Halo, <?= htmlspecialchars($_SESSION['nama']); ?></span>
            <span class="badge bg-dark text-uppercase"><?= $_SESSION['role']; ?></span>
        </div>
    </nav>
    <div class="content-wrapper p-4">

