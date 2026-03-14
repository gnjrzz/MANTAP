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
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
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
    } elseif (isset($_POST['action']) && $_POST['action'] == 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role_id = $_POST['role_id'];

        $phone = '';
        $relationship = 'User';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role_slug = ($role_id == 3) ? 'user_instansi' : 'user_umum';

        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, relationship, password, role, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $name, $email, $phone, $relationship, $hashed_password, $role_slug, $role_id);
            if ($stmt->execute()) {
                $success = 'Pendaftaran berhasil! Silakan Login.';
            } else {
                $error = 'Terjadi kesalahan sistem.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentikasi - MANTAP</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>

    <div class="auth-container <?php echo ($success || (isset($_POST['action']) && $_POST['action'] == 'register' && $error)) ? 'active' : ''; ?>"
        id="container">

        <!-- Register Form -->
        <div class="form-box register">
            <form action="" method="POST">
                <input type="hidden" name="action" value="register">
                <h1>Registration</h1>

                <?php if ($error && $_POST['action'] == 'register'): ?>
                    <p style="color: red; text-align: center; margin-bottom: 10px;"><?php echo $error; ?></p>
                <?php endif; ?>
                <?php if ($success): ?>
                    <p style="color: green; text-align: center; margin-bottom: 10px;"><?php echo $success; ?></p>
                <?php endif; ?>

                <div class="input-box">
                    <input type="text" name="name" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <!-- Role Selection Styled to match -->
                <div class="input-box">
                    <select name="role_id" required>
                        <option value="4">User Umum</option>
                        <option value="3">Instansi (Sekolah)</option>
                    </select>
                    <i class='bx bxs-briefcase'></i>
                </div>
                <button type="submit" class="btn">Register</button>
                <p class="social-text">or register with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                    <a href="#"><i class='bx bxl-github'></i></a>
                    <a href="#"><i class='bx bxl-linkedin'></i></a>
                </div>
                <div style="margin-top: 15px; text-align: center;">
                    <a href="index.php"
                        style="color: #5c7cdb; font-size: 13px; text-decoration: none; font-weight: 500;">
                        <i class='bx bx-home-alt'></i> Kembali ke Beranda
                    </a>
                </div>
            </form>
        </div>

        <!-- Login Form -->
        <div class="form-box login">
            <form action="" method="POST">
                <input type="hidden" name="action" value="login">
                <h1>Login</h1>

                <?php if ($error && $_POST['action'] == 'login'): ?>
                    <p style="color: red; text-align: center; margin-bottom: 10px;"><?php echo $error; ?></p>
                <?php endif; ?>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <a href="#">Forgot Password?</a>
                <button type="submit" class="btn">Login</button>
                <p class="social-text">or login with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                    <a href="#"><i class='bx bxl-github'></i></a>
                    <a href="#"><i class='bx bxl-linkedin'></i></a>
                </div>
                <div style="margin-top: 15px; text-align: center;">
                    <a href="index.php"
                        style="color: #5c7cdb; font-size: 13px; text-decoration: none; font-weight: 500;">
                        <i class='bx bx-home-alt'></i> Kembali ke Beranda
                    </a>
                </div>
            </form>
        </div>

        <!-- Toggle Panel Container -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Already have an account?</p>
                    <button class="btn-toggle" id="login">Login</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Welcome!</h1>
                    <p>Don't have an account?</p>
                    <button class="btn-toggle" id="register">Register</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JS -->
    <script src="assets/js/auth.js"></script>
</body>

</html>