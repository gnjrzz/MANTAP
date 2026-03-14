<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $relationship = $_POST['relationship'];
    $password = $_POST['password'];
    $role = 'user_umum'; // Hardcoded for public registration

    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $res = $stmt_check->get_result();

    if ($res->num_rows > 0) {
        $error = 'Email sudah terdaftar, silakan gunakan email lain.';
    } else {
        $role_id = 4; // User Umum
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, relationship, password, role, role_id) VALUES (?, ?, ?, ?, ?, 'user_umum', ?)");
        $stmt->bind_param("sssssi", $name, $email, $phone, $relationship, $hashed_password, $role_id);
        if ($stmt->execute()) {
            $success = 'Pendaftaran berhasil. Silakan <a href="login.php">login</a>.';
        } else {
            $error = 'Terjadi kesalahan sistem, coba lagi nanti.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - MANTAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-card {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="register-card">
            <h2 class="text-center text-primary fw-bold mb-4">MANTAP</h2>
            <h5 class="text-center mb-4">Buat Akun Baru</h5>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= $success ?>
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap Wali Murid</label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Hubungan dengan Siswa</label>
                        <select name="relationship" class="form-select" required>
                            <option value="">-- Pilih Hubungan --</option>
                            <option value="Ayah">Ayah</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Wali">Wali Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon / WhatsApp</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="081234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                        <small class="text-muted">Email ini akan digunakan untuk login Anda.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Daftar</button>
                </form>
            <?php endif; ?>
            <div class="mt-3 text-center">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                <p><a href="index.php">Kembali ke Beranda</a></p>
            </div>
        </div>
    </div>
</body>

</html>