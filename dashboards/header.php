<?php
include_once __DIR__ . '/../includes/auth_middleware.php';
checkLogin();

$role_name = $_SESSION['role_name'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
$base_url = "/MANTAP/dashboards";

// Define menu items for each role
$menus = [
    'Master Admin' => [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'link' => "$base_url/master/index.php"],
        ['label' => 'Manajemen User', 'icon' => 'bi-people', 'link' => '#'],
        ['label' => 'Laporan Global', 'icon' => 'bi-file-earmark-bar-graph', 'link' => '#'],
        ['label' => 'Log Sistem', 'icon' => 'bi-terminal', 'link' => '#'],
    ],
    'Admin' => [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'link' => "$base_url/admin/index.php"],
        ['label' => 'Kelola TKPI', 'icon' => 'bi-database-fill-gear', 'link' => '#'],
        ['label' => 'Verifikasi Keluhan', 'icon' => 'bi-check2-square', 'link' => '#'],
        ['label' => 'Laporan Masuk', 'icon' => 'bi-journal-text', 'link' => '#'],
    ],
    'Instansi' => [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'link' => "$base_url/instansi/index.php"],
        ['label' => 'Input Menu Harian', 'icon' => 'bi-plus-circle', 'link' => '#'],
        ['label' => 'Riwayat Laporan', 'icon' => 'bi-history', 'link' => '#'],
        ['label' => 'Profil Instansi', 'icon' => 'bi-building', 'link' => '#'],
    ],
    'User Umum' => [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'link' => "$base_url/umum/index.php"],
        ['label' => 'Kalkulator Gizi', 'icon' => 'bi-calculator', 'link' => '#'],
        ['label' => 'Riwayat Saya', 'icon' => 'bi-clock-history', 'link' => '#'],
    ],
];

$role_menu = $menus[$role_name] ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> - MANTAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #4895ef;
            --secondary-color: #3f37c9;
            --sidebar-bg: #1a1c23;
            --sidebar-hover: #2d2f39;
            --bg-body: #f8f9fc;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: #2d3436;
        }

        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            color: #fff;
            padding: 24px 0;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 0 24px 32px 24px;
            display: flex;
            align-items: center;
        }

        .sidebar-brand i {
            font-size: 24px;
            margin-right: 12px;
            color: var(--primary-light);
        }

        .sidebar-brand span {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(45deg, #fff, #adb5bd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .main-content {
            margin-left: 260px;
            padding: 32px;
            min-height: 100vh;
        }

        .nav-label {
            padding: 0 24px 12px 24px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6c757d;
            font-weight: 600;
        }

        .sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 24px;
            margin: 4px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar .nav-link i {
            font-size: 20px;
            margin-right: 14px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: var(--sidebar-hover);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .sidebar .nav-link.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e9ecef;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .card-compact {
            padding: 1rem !important;
        }

        .card-compact .card-header {
            padding: 0 0 1rem 0 !important;
            margin-bottom: 0.5rem !important;
        }

        .table-scrollable {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .small-text {
            font-size: 0.85rem;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                padding: 24px 0;
            }

            .sidebar-brand span,
            .nav-label,
            .sidebar .nav-link span {
                display: none;
            }

            .sidebar .nav-link {
                margin: 4px 8px;
                padding: 12px;
                justify-content: center;
            }

            .sidebar .nav-link i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 80px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-activity"></i>
            <span>MANTAP</span>
        </div>

        <div class="nav-label">Menu Utama</div>
        <nav class="nav flex-column">
            <?php foreach ($role_menu as $item):
                $active = ($current_page == basename($item['link'])) ? 'active' : '';
                ?>
                <a class="nav-link <?= $active ?>" href="<?= $item['link'] ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>

            <div class="mt-4 nav-label">Akun</div>
            <a class="nav-link" href="#"><i class="bi bi-person-circle"></i> <span>Profil</span></a>
            <a class="nav-link text-danger" href="/MANTAP/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </a>
        </nav>
    </div>

    <div class="main-content">
        <header class="top-header">
            <div>
                <h4 class="fw-bold mb-1">Selamat Datang, <?= explode(' ', $_SESSION['name'])[0] ?>! 👋</h4>
                <p class="text-muted small mb-0">Role: <span
                        class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= $role_name ?></span>
                </p>
            </div>
            <div class="d-none d-md-block text-end">
                <div class="fw-semibold"><?= date('l, d M Y') ?></div>
                <div class="text-muted small">Waktu Sistem MANTAP</div>
            </div>
        </header>