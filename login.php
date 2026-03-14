<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.id WHERE users.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];

            // Redirect based on role
            switch ($user['role_name']) {
                case 'Master Admin':
                    header("Location: dashboards/master/index.php");
                    break;
                case 'Admin':
                    header("Location: dashboards/admin/index.php");
                    break;
                case 'Instansi':
                    header("Location: dashboards/instansi/index.php");
                    break;
                default:
                    header("Location: dashboards/umum/index.php");
                    break;
            }
            exit;
        } else {
            $error = 'Email atau Password salah!';
        }
    } else {
        $error = 'Email atau Password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MANTAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-card {
            max-width: 400px;
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
        <div class="login-card">
            <h2 class="text-center text-primary fw-bold mb-4">MANTAP</h2>
            <h5 class="text-center mb-4">Masuk ke Akun Anda</h5>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="mt-3 text-center">
                <p>Belum punya akun? <a href="daftar.php">Daftar sekarang</a></p>
                <p><a href="index.php">Kembali ke Beranda</a></p>
            </div>
        </div>
    </div>
</body>

</html>