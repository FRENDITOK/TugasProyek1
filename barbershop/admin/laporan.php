<?php
require '../config/koneksi.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../auth/login.php"); exit; }

// Proses Konfirmasi Pembayaran (Ubah Status ke Lunas)
if (isset($_GET['konfirmasi_id'])) {
    $id_trx = intval($_GET['konfirmasi_id']);
    mysqli_query($conn, "UPDATE transaksi SET status_bayar = 'Lunas' WHERE id = $id_trx");
    header("Location: laporan.php?pesan=sukses_konfirmasi");
    exit;
}

// Proses Simpan Pencatatan Manual (Walk-in) dengan Validasi Hari
$pesan_error = "";
if (isset($_POST['tambah_manual'])) {
    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $layanan_id     = $_POST['layanan_id'];
    $tanggal        = $_POST['tanggal_booking'];
    $jam            = $_POST['jam_booking'];
    $metode         = $_POST['metode_bayar'];

    $hari_pilih = date('N', strtotime($tanggal));

    if (!in_array($hari_pilih, [1, 3, 4, 5])) {
        $pesan_error = "Gagal! Barbershop hanya buka pada hari Senin, Rabu, Kamis, dan Jumat.";
    } else {
        $kode           = "MNL-" . time();
        $q_layanan      = mysqli_query($conn, "SELECT harga FROM layanan WHERE id = $layanan_id");
        $harga          = mysqli_fetch_assoc($q_layanan)['harga'];
        $qris_ref       = ($metode == 'QRIS') ? "QRIS-MNL-" . rand(1000,9999) : NULL;

        $query_insert = "INSERT INTO transaksi (kode_booking, nama_pelanggan, layanan_id, tanggal_booking, jam_booking, total_harga, metode_bayar, status_bayar, sumber_transaksi, qris_ref) 
                         VALUES ('$kode', '$nama_pelanggan', '$layanan_id', '$tanggal', '$jam', '$harga', '$metode', 'Lunas', 'Manual', '$qris_ref')";
        
        mysqli_query($conn, $query_insert);
        header("Location: laporan.php?pesan=sukses_manual");
        exit;
    }
}

// Filter Waktu
$tanggal_pilih = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$bulan_pilih   = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// 0. QUERY TRANSAKSI PENDING (BELUM LUNAS)
$q_pending = mysqli_query($conn, "
    SELECT t.*, l.nama_layanan, u.nama AS nama_user 
    FROM transaksi t 
    JOIN layanan l ON t.layanan_id = l.id 
    LEFT JOIN users u ON t.user_id = u.id 
    WHERE t.status_bayar != 'Lunas'
    ORDER BY t.tanggal_booking DESC, t.jam_booking DESC
");
$data_pending = [];
while ($r = mysqli_fetch_assoc($q_pending)) {
    $data_pending[] = $r;
}

// 1. QUERY PENCATATAN HARIAN
$q_harian = mysqli_query($conn, "
    SELECT t.*, l.nama_layanan, u.nama AS nama_user 
    FROM transaksi t 
    JOIN layanan l ON t.layanan_id = l.id 
    LEFT JOIN users u ON t.user_id = u.id 
    WHERE t.tanggal_booking = '$tanggal_pilih' AND t.status_bayar = 'Lunas'
    ORDER BY t.jam_booking DESC
");
$total_harian = 0;
$data_harian  = [];
while ($r = mysqli_fetch_assoc($q_harian)) {
    $data_harian[] = $r;
    $total_harian += $r['total_harga'];
}

// 2. QUERY PENCATATAN MINGGUAN
$q_mingguan = mysqli_query($conn, "
    SELECT 
        CEIL(DAYOFMONTH(tanggal_booking) / 7) AS minggu_ke,
        COUNT(id) AS total_transaksi,
        SUM(total_harga) AS pendapatan_minggu
    FROM transaksi
    WHERE DATE_FORMAT(tanggal_booking, '%Y-%m') = '$bulan_pilih' AND status_bayar = 'Lunas'
    GROUP BY minggu_ke ORDER BY minggu_ke ASC
");
$data_mingguan = [];
while ($r = mysqli_fetch_assoc($q_mingguan)) {
    $data_mingguan[] = $r;
}

// 3. QUERY PENCATATAN BULANAN & BAGI HASIL
$q_bulanan = mysqli_query($conn, "
    SELECT 
        COUNT(id) AS total_trx,
        SUM(CASE WHEN sumber_transaksi = 'Booking' THEN total_harga ELSE 0 END) AS omzet_booking,
        SUM(CASE WHEN sumber_transaksi = 'Manual' THEN total_harga ELSE 0 END) AS omzet_manual,
        SUM(total_harga) AS total_omzet
    FROM transaksi
    WHERE DATE_FORMAT(tanggal_booking, '%Y-%m') = '$bulan_pilih' AND status_bayar = 'Lunas'
");
$rekap_bulanan = mysqli_fetch_assoc($q_bulanan);
$total_bulanan = $rekap_bulanan['total_omzet'] ?: 0;

if ($total_bulanan < 5000000) {
    $persen_pengelola = 50;
    $persen_investor  = 50;
} else {
    $persen_pengelola = 60;
    $persen_investor  = 40;
}
$bagian_pengelola = ($persen_pengelola / 100) * $total_bulanan;
$bagian_investor  = ($persen_investor / 100) * $total_bulanan;

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Pencatatan Keuangan & Laporan</h4>
    <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalManual">
        <i class="bi bi-plus-circle me-1"></i> Catat Transaksi Manual
    </button>
</div>

<?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'sukses_manual'): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Transaksi manual berhasil dicatat!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'sukses_konfirmasi'): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Pembayaran berhasil dikonfirmasi dan status diubah menjadi Lunas!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($pesan_error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $pesan_error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Navigasi Tab -->
<ul class="nav nav-tabs mb-4" id="laporanTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="konfirmasi-tab" data-bs-toggle="tab" data-bs-target="#konfirmasi" type="button" role="tab">
            1. Konfirmasi Pembayaran 
            <?php if(count($data_pending) > 0): ?>
                <span class="badge bg-danger ms-1"><?= count($data_pending); ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="harian-tab" data-bs-toggle="tab" data-bs-target="#harian" type="button" role="tab">
            2. Pencatatan Harian
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="mingguan-tab" data-bs-toggle="tab" data-bs-target="#mingguan" type="button" role="tab">
            3. Rekap Mingguan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="bulanan-tab" data-bs-toggle="tab" data-bs-target="#bulanan" type="button" role="tab">
            4. Bulanan & Bagi Hasil
        </button>
    </li>
</ul>

<div class="tab-content" id="laporanTabContent">
    
    <!-- KONFIRMASI PEMBAYARAN PENDING -->
    <div class="tab-pane fade show active" id="konfirmasi" role="tabpanel" aria-labelledby="konfirmasi-tab">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <span class="fw-bold">Daftar Transaksi Menunggu Konfirmasi (Tunai / Pending)</span>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Booking</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Jadwal</th>
                            <th>Metode</th>
                            <th>Total Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data_pending)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi yang menunggu konfirmasi saat ini.</td></tr>
                        <?php else: foreach ($data_pending as $p): ?>
                            <tr>
                                <td class="fw-semibold"><?= $p['kode_booking']; ?></td>
                                <td><?= htmlspecialchars($p['nama_user'] ? $p['nama_user'] : $p['nama_pelanggan']); ?></td>
                                <td><?= htmlspecialchars($p['nama_layanan']); ?></td>
                                <td><?= $p['tanggal_booking']; ?> (<?= substr($p['jam_booking'], 0, 5); ?>)</td>
                                <td>
                                    <span class="badge bg-secondary"><?= $p['metode_bayar']; ?></span>
                                </td>
                                <td class="fw-bold"><?= rupiah($p['total_harga']); ?></td>
                                <td>
                                    <a href="?konfirmasi_id=<?= $p['id']; ?>" class="btn btn-sm btn-success fw-semibold" onclick="return confirm('Konfirmasi bahwa pembayaran ini sudah diterima / lunas?')">
                                        <i class="bi bi-check-circle me-1"></i> Konfirmasi Lunas
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PENCATATAN HARIAN -->
    <div class="tab-pane fade" id="harian" role="tabpanel" aria-labelledby="harian-tab">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold">Transaksi Tanggal: <?= date('d M Y', strtotime($tanggal_pilih)); ?></span>
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="bulan" value="<?= $bulan_pilih; ?>">
                    <input type="date" name="tanggal" value="<?= $tanggal_pilih; ?>" class="form-control form-control-sm">
                    <button type="submit" class="btn btn-sm btn-dark">Tampilkan</button>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Jam</th>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Sumber</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data_harian)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi lunas pada tanggal ini.</td></tr>
                        <?php else: foreach ($data_harian as $h): ?>
                            <tr>
                                <td><?= substr($h['jam_booking'], 0, 5); ?></td>
                                <td class="fw-semibold"><?= $h['kode_booking']; ?></td>
                                <td><?= htmlspecialchars($h['sumber_transaksi'] == 'Manual' ? $h['nama_pelanggan'] : $h['nama_user']); ?></td>
                                <td><?= htmlspecialchars($h['nama_layanan']); ?></td>
                                <td>
                                    <span class="badge bg-<?= $h['sumber_transaksi'] == 'Booking' ? 'info text-dark' : 'secondary'; ?>">
                                        <?= $h['sumber_transaksi']; ?>
                                    </span>
                                </td>
                                <td><?= $h['metode_bayar']; ?></td>
                                <td class="fw-bold"><?= rupiah($h['total_harga']); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">TOTAL PENDAPATAN HARIAN</td>
                            <td><?= rupiah($total_harian); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- PENCATATAN MINGGUAN -->
    <div class="tab-pane fade" id="mingguan" role="tabpanel" aria-labelledby="mingguan-tab">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold">Rekap Mingguan (Bulan <?= date('F Y', strtotime($bulan_pilih . '-01')); ?>)</span>
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="tanggal" value="<?= $tanggal_pilih; ?>">
                    <input type="month" name="bulan" value="<?= $bulan_pilih; ?>" class="form-control form-control-sm">
                    <button type="submit" class="btn btn-sm btn-dark">Filter Bulan</button>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Periode Minggu</th>
                            <th>Total Transaksi</th>
                            <th>Pendapatan Mingguan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data_mingguan)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data transaksi pada bulan ini.</td></tr>
                        <?php else: foreach ($data_mingguan as $m): ?>
                            <tr>
                                <td class="fw-semibold">Minggu ke-<?= $m['minggu_ke']; ?></td>
                                <td><?= $m['total_transaksi']; ?> Transaksi</td>
                                <td class="fw-bold text-success"><?= rupiah($m['pendapatan_minggu']); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BULANAN & BAGI HASIL -->
    <div class="tab-pane fade" id="bulanan" role="tabpanel" aria-labelledby="bulanan-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Laporan & Bagi Hasil Bulan <?= date('F Y', strtotime($bulan_pilih . '-01')); ?></h5>
            <form method="GET" class="d-flex gap-2">
                <input type="hidden" name="tanggal" value="<?= $tanggal_pilih; ?>">
                <input type="month" name="bulan" value="<?= $bulan_pilih; ?>" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-dark">Filter Bulan</button>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white p-3 h-100">
                    <small>Total Omzet Bulanan (Booking + Manual)</small>
                    <h3 class="fw-bold mt-1"><?= rupiah($total_bulanan); ?></h3>
                    <span class="badge bg-light text-dark w-auto align-self-start mt-2">
                        Skema Bagi Hasil: <?= $persen_pengelola; ?>% : <?= $persen_investor; ?>%
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white p-3 h-100">
                    <small>Bagian Pengelola (<?= $persen_pengelola; ?>%)</small>
                    <h3 class="fw-bold mt-1"><?= rupiah($bagian_pengelola); ?></h3>
                    <small class="mt-2 opacity-75">Dihitung otomatis dari omzet bulanan</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-dark text-white p-3 h-100">
                    <small>Bagian Investor (<?= $persen_investor; ?>%)</small>
                    <h3 class="fw-bold mt-1"><?= rupiah($bagian_investor); ?></h3>
                    <small class="mt-2 opacity-75"><?= $total_bulanan < 5000000 ? 'Omzet di bawah Rp 5 Juta' : 'Omzet Rp 5 Juta ke atas'; ?></small>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Rincian Sumber Pendapatan Bulanan</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Total Pendapatan dari Booking Online</th>
                        <td><?= rupiah($rekap_bulanan['omzet_booking'] ?: 0); ?></td>
                    </tr>
                    <tr>
                        <th>Total Pendapatan dari Pencatatan Manual (Walk-in)</th>
                        <td><?= rupiah($rekap_bulanan['omzet_manual'] ?: 0); ?></td>
                    </tr>
                    <tr class="table-secondary fw-bold">
                        <th>TOTAL KESELURUHAN BULANAN</th>
                        <td><?= rupiah($total_bulanan); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Pencatatan Manual -->
<div class="modal fade" id="modalManual" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Catat Transaksi Manual (Walk-in)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle"></i> Catatan: Barbershop buka setiap hari <strong>Senin, Rabu, Kamis, dan Jumat</strong>. Input di luar hari tersebut akan ditolak sistem.
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-control" required placeholder="Contoh: Andi (Walk-in)">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Layanan</label>
                    <select name="layanan_id" class="form-select" required>
                        <?php 
                        $q_lay = mysqli_query($conn, "SELECT * FROM layanan");
                        while ($l = mysqli_fetch_assoc($q_lay)): 
                        ?>
                            <option value="<?= $l['id']; ?>"><?= $l['nama_layanan']; ?> - <?= rupiah($l['harga']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_booking" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col">
                        <label class="form-label">Jam</label>
                        <input type="time" name="jam_booking" class="form-control" value="<?= date('H:i'); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="metode_bayar" class="form-select">
                        <option value="Tunai">Tunai (Cash)</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="tambah_manual" class="btn btn-primary">Simpan Pencatatan</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>