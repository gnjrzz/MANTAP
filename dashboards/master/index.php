<?php
$page_title = 'Master Admin Panel';
include_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../../koneksi.php';

checkRole(['Master Admin']);

// Global overview stats
$users_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$instansi_count = $conn->query("SELECT COUNT(*) FROM roles r JOIN users u ON r.id = u.role_id WHERE r.role_name = 'Instansi'")->fetch_row()[0];
$admin_count = $conn->query("SELECT COUNT(*) FROM roles r JOIN users u ON r.id = u.role_id WHERE r.role_name = 'Admin'")->fetch_row()[0];

// Fetch for chart: Average Energy by Instansi
$sql_instansi = "SELECT u.instansi_name, AVG(r.total_energy) as avg_energy 
                 FROM reports r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE u.instansi_name IS NOT NULL 
                 GROUP BY u.instansi_name LIMIT 5";
$instansi_data = $conn->query($sql_instansi)->fetch_all(MYSQLI_ASSOC);

$labels = json_encode(array_column($instansi_data, 'instansi_name'));
$energies = json_encode(array_column($instansi_data, 'avg_energy'));
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-compact border-0 h-100 overflow-hidden position-relative">
            <div class="position-absolute end-0 top-0 p-2 opacity-10">
                <i class="bi bi-people-fill fs-1"></i>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Pengguna</h6>
            <h2 class="fw-bold mb-0"><?= $users_count ?></h2>
            <div class="mt-2 small text-primary fw-semibold" style="font-size: 0.75rem;"><i
                    class="bi bi-arrow-up-right me-1"></i> Detail</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-compact border-0 h-100 overflow-hidden position-relative">
            <div class="position-absolute end-0 top-0 p-2 opacity-10">
                <i class="bi bi-building fs-1"></i>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Instansi</h6>
            <h2 class="fw-bold mb-0"><?= $instansi_count ?></h2>
            <div class="mt-2 small text-success fw-semibold" style="font-size: 0.75rem;"><i
                    class="bi bi-arrow-up-right me-1"></i> Daftar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-compact border-0 h-100 overflow-hidden position-relative">
            <div class="position-absolute end-0 top-0 p-2 opacity-10">
                <i class="bi bi-shield-lock fs-1"></i>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Staff Admin</h6>
            <h2 class="fw-bold mb-0"><?= $admin_count ?></h2>
            <div class="mt-2 small text-info fw-semibold" style="font-size: 0.75rem;"><i
                    class="bi bi-arrow-up-right me-1"></i> Akses</div>
        </div>
    </div>
</div>

<div class="row mb-4 g-3">
    <!-- Global Analytics -->
    <div class="col-xl-8 col-lg-7">
        <div class="card card-compact h-100">
            <h6 class="mb-3 fw-bold">Performa Gizi Antar Instansi (Rerata Kcal)</h6>
            <div style="height: 250px;">
                <canvas id="masterAnalyticsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- System Health / Recent Actions -->
    <div class="col-xl-4 col-lg-5">
        <div class="card card-compact h-100">
            <h6 class="mb-3 fw-bold">Log Sistem Terkini</h6>
            <div class="table-scrollable" style="max-height: 250px;">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between p-2">
                        <span>Admin @anton memverifikasi data TKPI</span>
                        <span class="text-muted" style="font-size: 0.65rem;">2m ago</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-2">
                        <span>Instansi @sdn1 baru saja join</span>
                        <span class="text-muted" style="font-size: 0.65rem;">1h ago</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between p-2">
                        <span>Backup database sukses</span>
                        <span class="text-muted" style="font-size: 0.65rem;">1d ago</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- User Management Section -->
    <div class="col-12">
        <div class="card card-compact">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 fw-bold">Manajemen Pengguna</h6>
                <button class="btn btn-primary btn-xs">+ Tambah</button>
            </div>
            <div class="table-scrollable">
                <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_users = $conn->query("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id DESC LIMIT 10");
                        while ($u = $all_users->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($u['name']) ?></div>
                                    <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($u['email']) ?>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary text-uppercase"
                                        style="font-size: 0.6rem;">
                                        <?= $u['role_name'] ?>
                                    </span></td>
                                <td class="text-end">
                                    <button class="btn btn-xs btn-outline-info py-0">Edit</button>
                                    <button class="btn btn-xs btn-outline-danger py-0">Del</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    new Chart(document.getElementById('masterAnalyticsChart'), {
        type: 'bar',
        data: {
            labels: <?= $labels ?>,
            datasets: [{
                label: 'Rerata Kcal per Laporan',
                data: <?= $energies ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: { x: { beginAtZero: true } }
        }
    });
</script>

<?php include_once __DIR__ . '/../footer.php'; ?>