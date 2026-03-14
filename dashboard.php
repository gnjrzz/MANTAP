<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role_name = $_SESSION['role_name'] ?? '';

switch ($role_name) {
    case 'Master Admin':
        header("Location: dashboards/master/index.php");
        break;
    case 'Admin':
        header("Location: dashboards/admin/index.php");
        break;
    case 'Instansi':
        header("Location: dashboards/instansi/index.php");
        break;
    case 'User Umum':
        header("Location: dashboards/umum/index.php");
        break;
    default:
        die("Role tidak dikenali atau belum diset.");
}
exit;
?>