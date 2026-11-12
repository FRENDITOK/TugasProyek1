<?php
require '../config/koneksi.php';
if ($_SESSION['role'] != 'konsumen') { header("Location: ../auth/login.php"); exit; }

include '../includes/header.php';
include '../includes/sidebar.php';
?>
<h4 class="fw-bold mb-4">Riwayat Booking & Pembayaran</h4>

<?php if (isset($_GET['bayar_kode'])): 
    $kode = mysqli_real_escape_string($conn, $_GET['bayar_kode']);
    $trx  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE kode_booking='$kode'"));
    if ($trx && $trx['status_bayar'] == 'Pending' && $trx['metode_bayar'] == 'QRIS'):
?>
<div class="card border-info shadow-sm mb-4 text-center p-4" style="max-width: 450px;">
    <h5 class="fw-bold">Scan QRIS untuk Membayar</h5>
    <p class="text-muted mb-1">Kode: <strong><?= $trx['kode_booking']; ?></strong></p>
    <h4 class="text-primary fw-bold mb-3"><?= rupiah($trx['total_harga']); ?></h4>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= $trx['kode_booking']; ?>" class="img-fluid mx-auto mb-3 border p-2 rounded bg-white" alt="QRIS">
    <small class="text-muted d-block mb-2">Mendukung GoPay, DANA, OVO, ShopeePay, dll</small>
</div>
<?php endif; endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body">
    <table class="table align-middle">
        <thead class="table-light"><tr><th>Kode Booking</th><th>Layanan</th><th>Jadwal</th><th>Total</th><th>Metode</th><th>Status</th></tr></thead>
        <tbody>
            <?php $uid = $_SESSION['user_id'];
            $q = mysqli_query($conn, "SELECT t.*, l.nama_layanan FROM transaksi t JOIN layanan l ON t.layanan_id = l.id WHERE t.user_id = $uid ORDER BY t.id DESC");
            if(mysqli_num_rows($q)==0): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat booking.</td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($q)): ?>
            <tr>
                <td class="fw-bold"><?= $r['kode_booking']; ?></td>
                <td><?= htmlspecialchars($r['nama_layanan']); ?></td>
                <td><?= $r['tanggal_booking']; ?> (<?= substr($r['jam_booking'],0,5); ?>)</td>
                <td><?= rupiah($r['total_harga']); ?></td>
                <td>
                    <span class="badge bg-secondary"><?= $r['metode_bayar']; ?></span>
                </td>
                <td>
                    <?php if ($r['status_bayar'] == 'Lunas'): ?>
                        <span class="badge bg-success">Lunas</span>
                    <?php else: ?>
                        <?php if ($r['metode_bayar'] == 'QRIS'): ?>
                            <a href="?bayar_kode=<?= $r['kode_booking']; ?>" class="btn btn-sm btn-warning">Bayar QRIS</a>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Menunggu Konfirmasi Admin</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; endif; ?>
        </tbody>
    </table>
</div></div>
<?php include '../includes/footer.php'; ?>