<?php
require '../config/koneksi.php';

$error = "";

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        
        // Cek password (mendukung password_hash atau teks biasa)
        if (password_verify($password, $user['password']) || $password == $user['password']) {
            $_SESSION['user_id']     = $user['id'];
            $_SESSION['nama']        = $user['nama'];
            $_SESSION['role']        = $user['role']; // 'admin' atau 'konsumen'
            $_SESSION['foto_profil'] = $user['foto']; // Menyimpan nama file foto profil ke sesi

            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
                exit;
            } else {
                header("Location: ../customer/index.php");
                exit;
            }
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Email tidak terdaftar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BarberPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="d-flex align-items-center py-5" style="min-height: 100vh; background-color: #0d1b2a;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold"><i class="bi bi-scissors"></i> BarberPro</h3>
                        <p class="text-muted small">Masuk ke sistem</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2 small">
                            <?= $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="nama@email.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                        <button type="submit" name="login" class="btn btn-dark w-100 py-2 fw-semibold">
                            Masuk
                        </button>
                    </form>
                    
                    <div class="text-center mt-3 small">
                        <span class="text-muted">Belum punya akun?</span> <a href="register.php" class="text-decoration-none fw-semibold">Daftar di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>