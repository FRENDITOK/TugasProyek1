<?php
require '../config/koneksi.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../auth/login.php"); exit; }

// Proses Tambah Layanan
if (isset($_POST['tambah_layanan'])) {
    $nama_layanan = mysqli_real_escape_string($conn, $_POST['nama_layanan']);
    $harga        = $_POST['harga'];
    $deskripsi    = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    mysqli_query($conn, "INSERT INTO layanan (nama_layanan, harga, deskripsi) VALUES ('$nama_layanan', '$harga', '$deskripsi')");
    header("Location: layanan.php?pesan=sukses_tambah");
    exit;
}

// Proses Edit Layanan
if (isset($_POST['edit_layanan'])) {
    $id_layanan   = $_POST['id_layanan'];
    $nama_layanan = mysqli_real_escape_string($conn, $_POST['nama_layanan']);
    $harga        = $_POST['harga'];
    $deskripsi    = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    mysqli_query($conn, "UPDATE layanan SET nama_layanan = '$nama_layanan', harga = '$harga', deskripsi = '$deskripsi' WHERE id = $id_layanan");
    header("Location: layanan.php?pesan=sukses_edit");
    exit;
}

// Proses Hapus Layanan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM layanan WHERE id = $id");
    header("Location: layanan.php?pesan=sukses_hapus");
    exit;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Manajemen Layanan Barbershop</h4>
    <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Layanan
    </button>
</div>

<?php if (isset($_GET['pesan'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_GET['pesan'] == 'sukses_tambah' ? 'Layanan berhasil ditambahkan!' : ($_GET['pesan'] == 'sukses_edit' ? 'Layanan berhasil diperbarui!' : 'Layanan berhasil dihapus!'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Layanan</th>
                    <th>Harga</th>
                    <th>Deskripsi</th>
                    <th width="18%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $query = mysqli_query($conn, "SELECT * FROM layanan");
                if (mysqli_num_rows($query) == 0):
                ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data layanan.</td></tr>
                <?php else: while ($l = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($l['nama_layanan']); ?></td>
                        <td class="text-success fw-bold"><?= rupiah($l['harga']); ?></td>
                        <td><?= htmlspecialchars($l['deskripsi']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $l['id']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <a href="layanan.php?hapus=<?= $l['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus layanan ini?')">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Layanan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Layanan</label>
                    <input type="text" name="nama_layanan" class="form-control" required placeholder="Contoh: Haircut & Styling">
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" required placeholder="Contoh: 35000">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required placeholder="Deskripsi singkat layanan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="tambah_layanan" class="btn btn-primary">Simpan Layanan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit untuk Setiap Data -->
<?php 
$q_edit = mysqli_query($conn, "SELECT * FROM layanan");
while ($l = mysqli_fetch_assoc($q_edit)): 
?>
<div class="modal fade" id="modalEdit<?= $l['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_layanan" value="<?= $l['id']; ?>">
                <div class="mb-3">
                    <label class="form-label">Nama Layanan</label>
                    <input type="text" name="nama_layanan" class="form-control" value="<?= htmlspecialchars($l['nama_layanan']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" value="<?= $l['harga']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($l['deskripsi']); ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="edit_layanan" class="btn btn-warning text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?php endwhile; ?>

<?php include '../includes/footer.php'; ?>