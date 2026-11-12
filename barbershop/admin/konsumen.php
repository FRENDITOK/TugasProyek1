<?php
require '../config/koneksi.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../auth/login.php"); exit; }

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Kelola Akun & Riwayat Cukur Konsumen</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <span class="fw-bold">Daftar Konsumen Terdaftar & Statistik Cukur</span>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Konsumen</th>
                    <th>Email / Kontak</th>
                    <th>Tanggal Bergabung</th>
                    <th class="text-center">Total Cukur (Booking Lunas)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Query untuk mengambil data users dengan role konsumen
                // Sekaligus menghitung total transaksi (booking) yang statusnya 'Lunas' milik user tersebut
                $q_konsumen = mysqli_query($conn, "
                    SELECT u.*, 
                    (SELECT COUNT(t.id) FROM transaksi t WHERE t.user_id = u.id AND t.status_bayar = 'Lunas') AS total_cukur
                    FROM users u 
                    WHERE u.role = 'konsumen' 
                    ORDER BY u.id DESC
                ");

                if (mysqli_num_rows($q_konsumen) == 0): 
                ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada akun konsumen yang terdaftar.</td></tr>
                <?php else: 
                    $no = 1;
                    while ($k = mysqli_fetch_assoc($q_konsumen)): 
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($k['nama']); ?></td>
                        <td><?= htmlspecialchars($k['email']); ?></td>
                        <td><?= isset($k['created_at']) ? $k['created_at'] : '-'; ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary px-3 py-2 fs-6">
                                <?= $k['total_cukur']; ?> Kali Cukur
                            </span>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>