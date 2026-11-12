<?php
require '../config/koneksi.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../auth/login.php"); exit; }

$admin_id = $_SESSION['user_id'];
$pesan = "";
$tipe_pesan = "";

if (isset($_POST['update_admin'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass_baru = $_POST['password_baru'];

    $data_lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $admin_id"));
    $nama_file_foto = $data_lama['foto'] ?? '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ekstensi_diperbolehkan = ['png', 'jpg', 'jpeg', 'webp'];
        $nama_file = $_FILES['foto']['name'];
        $x = explode('.', $nama_file);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['foto']['size'];
        $file_tmp = $_FILES['foto']['tmp_name'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan)) {
            if ($ukuran < 2048000) {
                $nama_file_baru = 'admin_' . $admin_id . '_' . time() . '.' . $ekstensi;
                if (!is_dir('../uploads')) {
                    mkdir('../uploads', 0777, true);
                }
                if (move_uploaded_file($file_tmp, '../uploads/' . $nama_file_baru)) {
                    if (!empty($data_lama['foto']) && file_exists('../uploads/' . $data_lama['foto'])) {
                        unlink('../uploads/' . $data_lama['foto']);
                    }
                    $nama_file_foto = $nama_file_baru;
                }
            } else {
                $pesan = "Ukuran foto terlalu besar! Maksimal 2MB.";
                $tipe_pesan = "danger";
            }
        } else {
            $pesan = "Ekstensi file foto tidak diizinkan.";
            $tipe_pesan = "danger";
        }
    }

    if (empty($pesan)) {
        if (!empty($pass_baru)) {
            $password_hash = password_hash($pass_baru, PASSWORD_DEFAULT);
            $query = "UPDATE users SET nama = '$nama', email = '$email', password = '$password_hash', foto = '$nama_file_foto' WHERE id = $admin_id";
        } else {
            $query = "UPDATE users SET nama = '$nama', email = '$email', foto = '$nama_file_foto' WHERE id = $admin_id";
        }

        if (mysqli_query($conn, $query)) {
            $_SESSION['nama'] = $nama;
            $_SESSION['foto_profil'] = $nama_file_foto;
            $pesan = "Profil admin berhasil diperbarui!";
            $tipe_pesan = "success";
        } else {
            $pesan = "Gagal memperbarui profil: " . mysqli_error($conn);
            $tipe_pesan = "danger";
        }
    }
}

$admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $admin_id"));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<h4 class="fw-bold mb-4">Edit Profil Pengelola / Admin</h4>

<?php if (!empty($pesan)): ?>
    <div class="alert alert-<?= $tipe_pesan; ?> alert-dismissible fade show">
        <?= $pesan; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm p-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="text-center mb-4">
                    <img src="<?= !empty($admin['foto']) ? '../uploads/' . $admin['foto'] : 'https://github.com/mdo.png'; ?>" class="rounded-circle object-fit-cover shadow-sm mb-3" width="100" height="100" alt="Avatar">
                    <div>
                        <label class="form-label fw-semibold small text-muted">Ganti Foto Profil Admin</label>
                        <input type="file" name="foto" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Admin</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($admin['nama']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Admin</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']); ?>" required>
                </div>
                <hr class="my-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru (Opsional)</label>
                    <input type="password" name="password_baru" class="form-control" placeholder="Isi jika ingin mengganti password baru">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                </div>
                <button type="submit" name="update_admin" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan Admin
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>