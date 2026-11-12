<?php
require '../config/koneksi.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../auth/login.php"); exit; }

// Proses Tambah Alat
if (isset($_POST['tambah_alat'])) {
    $nama_alat    = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $jumlah       = $_POST['jumlah'];
    $kondisi      = $_POST['kondisi'];
    $tanggal_beli = $_POST['tanggal_beli'];

    mysqli_query($conn, "INSERT INTO alat (nama_alat, jumlah, kondisi, tanggal_beli) VALUES ('$nama_alat', '$jumlah', '$kondisi', '$tanggal_beli')");
    header("Location: alat.php?pesan=sukses_tambah");
    exit;
}

// Proses Edit Alat
if (isset($_POST['edit_alat'])) {
    $id_alat      = $_POST['id_alat'];
    $nama_alat    = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $jumlah       = $_POST['jumlah'];
    $kondisi      = $_POST['kondisi'];
    $tanggal_beli = $_POST['tanggal_beli'];

    mysqli_query($conn, "UPDATE alat SET nama_alat = '$nama_alat', jumlah = '$jumlah', kondisi = '$kondisi', tanggal_beli = '$tanggal_beli' WHERE id = $id_alat");
    header("Location: alat.php?pesan=sukses_edit");
    exit;
}

// Proses Hapus Alat
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM alat WHERE id = $id");
    header("Location: alat.php?pesan=sukses_hapus");
    exit;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Manajemen Inventaris Alat Barbershop</h4>
    <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Alat
    </button>
</div>

<?php if (isset($_GET['pesan'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_GET['pesan'] == 'sukses_tambah' ? 'Alat berhasil ditambahkan!' : ($_GET['pesan'] == 'sukses_edit' ? 'Alat berhasil diperbarui!' : 'Alat berhasil dihapus!'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Alat</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Tgl Beli</th>
                    <th width="18%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $query = mysqli_query($conn, "SELECT * FROM alat");
                if (mysqli_num_rows($query) == 0):
                ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data alat inventaris.</td></tr>
                <?php else: while ($a = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($a['nama_alat']); ?></td>
                        <td><?= $a['jumlah']; ?> Pcs</td>
                        <td>
                            <span class="badge bg-<?= $a['kondisi'] == 'Baik' ? 'success' : ($a['kondisi'] == 'Perlu Perbaikan' ? 'warning text-dark' : 'danger'); ?>">
                                <?= $a['kondisi']; ?>
                            </span>
                        </td>
                        <td><?= $a['tanggal_beli'] ? date('d/m/Y', strtotime($a['tanggal_beli'])) : '-'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $a['id']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <a href="alat.php?hapus=<?= $a['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus alat ini?')">
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
                <h5 class="modal-title fw-bold">Tambah Alat Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Alat</label>
                    <input type="text" name="nama_alat" class="form-control" required placeholder="Contoh: Clipper Wahl Magic Clip">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" required placeholder="Contoh: 3">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kondisi</label>
                    <select name="kondisi" class="form-select" required>
                        <option value="Baik">Baik</option>
                        <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Beli</label>
                    <input type="date" name="tanggal_beli" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="tambah_alat" class="btn btn-primary">Simpan Alat</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit untuk Setiap Data -->
<?php 
$q_edit = mysqli_query($conn, "SELECT * FROM alat");
while ($a = mysqli_fetch_assoc($q_edit)): 
?>
<div class="modal fade" id="modalEdit<?= $a['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Alat Barbershop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_alat" value="<?= $a['id']; ?>">
                <div class="mb-3">
                    <label class="form-label">Nama Alat</label>
                    <input type="text" name="nama_alat" class="form-control" value="<?= htmlspecialchars($a['nama_alat']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" value="<?= $a['jumlah']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kondisi</label>
                    <select name="kondisi" class="form-select" required>
                        <option value="Baik" <?= $a['kondisi'] == 'Baik' ? 'selected' : ''; ?>>Baik</option>
                        <option value="Perlu Perbaikan" <?= $a['kondisi'] == 'Perlu Perbaikan' ? 'selected' : ''; ?>>Perlu Perbaikan</option>
                        <option value="Rusak" <?= $a['kondisi'] == 'Rusak' ? 'selected' : ''; ?>>Rusak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Beli</label>
                    <input type="date" name="tanggal_beli" class="form-control" value="<?= $a['tanggal_beli']; ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="edit_alat" class="btn btn-warning text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?php endwhile; ?>

<?php include '../includes/footer.php'; ?>