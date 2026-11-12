<?php
require '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'konsumen') { 
    header("Location: ../auth/login.php"); 
    exit; 
}

$pesan_error = "";

if (isset($_POST['booking'])) {
    $user_id      = $_SESSION['user_id'];
    $layanan_id   = $_POST['layanan_id'];
    $tanggal      = $_POST['tanggal_booking'];
    $jam          = $_POST['jam_booking'];
    $metode_bayar = $_POST['metode_bayar'];

    // Cek hari dari tanggal yang dipilih (1 = Senin, 3 = Rabu, 4 = Kamis, 5 = Jumat)
    $hari_pilih = date('N', strtotime($tanggal));

    // Validasi hari operasional: Senin(1), Rabu(3), Kamis(4), Jumat(5)
    if (!in_array($hari_pilih, [1, 3, 4, 5])) {
        $pesan_error = "Maaf, Barbershop tutup pada hari tersebut. Kami hanya buka pada hari Senin, Rabu, Kamis, dan Jumat.";
    } else {
        // Ambil harga layanan
        $q_layanan  = mysqli_query($conn, "SELECT harga FROM layanan WHERE id = $layanan_id");
        $data_lay   = mysqli_fetch_assoc($q_layanan);
        $harga      = $data_lay['harga'];

        $kode       = "BKG-" . strtoupper(uniqid());
        
        // Atur referensi dan status berdasarkan metode pembayaran
        if ($metode_bayar == 'QRIS') {
            $qris_ref        = "QRIS-" . rand(1000, 9999);
            $status_bayar    = 'Pending';
            $target_redirect = "riwayat.php?bayar_kode=$kode"; // Jika QRIS, arahkan ke pembayaran QRIS
        } else {
            $qris_ref        = NULL;
            $status_bayar    = 'Pending'; // Menunggu konfirmasi admin jika Tunai
            $target_redirect = "riwayat.php"; // Jika Tunai, arahkan langsung ke riwayat bersih
        }

        mysqli_query($conn, "INSERT INTO transaksi (kode_booking, user_id, layanan_id, tanggal_booking, jam_booking, total_harga, metode_bayar, status_bayar, sumber_transaksi, qris_ref) 
                             VALUES ('$kode', '$user_id', '$layanan_id', '$tanggal', '$jam', '$harga', '$metode_bayar', '$status_bayar', 'Booking', '$qris_ref')");
        
        header("Location: " . $target_redirect);
        exit;
    }
}

include '../includes/header.php';
include '../includes/sidebar.php'; 
?>

<div class="container-fluid px-4">
    <h4 class="fw-bold mb-4">Booking Layanan Barbershop</h4>

    <!-- Informasi Hari Operasional -->
    <div class="alert alert-info py-2 small mb-4">
        <i class="bi bi-info-circle"></i> Barbershop buka setiap hari <strong>Senin, Rabu, Kamis, dan Jumat</strong>. Hari Selasa, Sabtu, dan Minggu libur.
    </div>

    <?php if (!empty($pesan_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $pesan_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Layanan</label>
                        <select name="layanan_id" class="form-select" required>
                            <option value="">-- Pilih Layanan --</option>
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
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="tanggal_booking" class="form-control" value="<?= date('Y-m-d'); ?>" min="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Jam</label>
                            <input type="time" name="jam_booking" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran</label>
                        <select name="metode_bayar" class="form-select" required>
                            <option value="Tunai">Bayar di Tempat (Tunai)</option>
                            <option value="QRIS">QRIS (Digital Payment)</option>
                        </select>
                    </div>

                    <button type="submit" name="booking" class="btn btn-dark w-100 py-2 fw-semibold">
                        <i class="bi bi-check-circle me-2"></i> Konfirmasi & Buat Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>