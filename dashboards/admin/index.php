<?php
$page_title = 'Dashboard Admin';
include_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../../koneksi.php';

checkRole(['Admin', 'Master Admin']);

// Statistics
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_reports = $conn->query("SELECT COUNT(*) FROM reports")->fetch_row()[0];
$total_complaints = $conn->query("SELECT COUNT(*) FROM reports WHERE complaint_text IS NOT NULL")->fetch_row()[0];

// Pie Chart Data: Nutrition Status Global
$sql_status = "SELECT status, COUNT(*) as count FROM reports GROUP BY status";
$res_status = $conn->query($sql_status)->fetch_all(MYSQLI_ASSOC);

$pie_labels = [];
$pie_counts = [];
$pie_colors = ['hijau' => '#28a745', 'kuning' => '#ffc107', 'merah' => '#dc3545'];
$current_colors = [];

foreach ($res_status as $row) {
    $pie_labels[] = strtoupper($row['status']);
    $pie_counts[] = $row['count'];
    $current_colors[] = $pie_colors[$row['status']] ?? '#6c757d';
}
?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-compact border-0 h-100 bg-white shadow-sm overflow-hidden position-relative">
            <div class="position-absolute end-0 top-0 p-2 opacity-10 text-info">
                <i class="bi bi-people-fill fs-1"></i>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Pengguna</h6>
            <h2 class="fw-bold mb-0"><?= $total_users ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-compact border-0 h-100 bg-white shadow-sm overflow-hidden position-relative">
            <div class="position-absolute end-0 top-0 p-2 opacity-10 text-success">
                <i class="bi bi-file-earmark-text-fill fs-1"></i>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Laporan</h6>
            <h2 class="fw-bold mb-0"><?= $total_reports ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-compact border-0 h-100 bg-white shadow-sm overflow-hidden position-relative">
            <div class="position-absolute end-0 top-0 p-2 opacity-10 text-warning">
                <i class="bi bi-exclamation-triangle-fill fs-1"></i>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Keluhan Masuk</h6>
            <h2 class="fw-bold mb-0"><?= $total_complaints ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-compact border-0 h-100 bg-white shadow-sm overflow-hidden position-relative">
            <div class="position-absolute end-0 top-0 p-2 opacity-10 text-dark">
                <i class="bi bi-database-fill fs-1"></i>
            </div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Data TKPI</h6>
            <h2 class="fw-bold mb-0"><?= $conn->query("SELECT COUNT(*) FROM tkpi_data")->fetch_row()[0] ?></h2>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card card-compact h-100">
            <h6 class="mb-3 fw-bold">Status Gizi Global</h6>
            <div style="height: 200px;">
                <canvas id="globalStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Complaints / Verification -->
    <div class="col-xl-8 col-lg-7">
        <div class="card card-compact h-100">
            <h6 class="mb-2 fw-bold d-flex justify-content-between">
                <span>Verifikasi Keluhan</span>
                <a href="#" class="btn btn-link btn-xs p-0 text-decoration-none">Semua &raquo;</a>
            </h6>
            <div class="table-scrollable" style="max-height: 200px;">
                <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>User</th>
                            <th>Keluhan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $complaints = $conn->query("SELECT r.*, u.name FROM reports r JOIN users u ON r.user_id = u.id WHERE complaint_text IS NOT NULL ORDER BY created_at DESC LIMIT 5");
                        while ($row = $complaints->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                                <td><small
                                        class="text-muted"><?= substr(htmlspecialchars($row['complaint_text']), 0, 40) ?>...</small>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-xs btn-primary py-0 px-2">Cek</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-compact">
            <h6 class="fw-bold mb-1">Manajemen Konten Gizi (TKPI)</h6>
            <p class="text-muted" style="font-size: 0.75rem;">Update data nutrisi bahan makanan secara berkala.</p>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Tambah</button>
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i> Ekspor</button>
            </div>
        </div>
    </div>
</div>

<script>
    new Chart(document.getElementById('globalStatusChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($pie_labels) ?>,
            datasets: [{
                data: <?= json_encode($pie_counts) ?>,
                backgroundColor: <?= json_encode($current_colors) ?>
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

<?php include_once __DIR__ . '/../footer.php'; ?>