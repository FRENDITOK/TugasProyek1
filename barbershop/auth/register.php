<?php
require '../config/koneksi.php';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    // Menggunakan password_hash untuk keamanan modern
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = 'konsumen';

    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')");
        header("Location: login.php?pesan=sukses");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - BarberPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: linear-gradient(135deg, #1e293b, #0f172a); height: 100vh; display: flex; align-items: center; }</style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-lg p-4 rounded-4">
                <h3 class="text-center fw-bold mb-1">✂️ BarberPro</h3>
                <p class="text-center text-muted mb-4">Daftar Konsumen Baru</p>
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-dark w-100 py-2">Daftar Sekarang</button>
                </form>
                <div class="mt-3 text-center">
                    <small class="text-muted">Sudah punya akun? <a href="login.php">Login</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>