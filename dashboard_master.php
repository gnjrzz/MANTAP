<h2>Dashboard Master Admin</h2>
<p class="text-muted">Ringkasan nutrisi laporan dari seluruh Instansi dan Umum.</p>

<?php
// Get total reports
$total_reports_query = mysqli_query($conn, "SELECT COUNT(id) as total FROM reports");
$total_reports = mysqli_fetch_assoc($total_reports_query)['total'];

// Get totals per status
$status_query = mysqli_query($conn, "SELECT status, COUNT(id) as count FROM reports GROUP BY status");
$status_counts = ['hijau' => 0, 'kuning' => 0, 'merah' => 0];
while ($row = mysqli_fetch_assoc($status_query)) {
    $status_counts[$row['status']] = $row['count'];
}

// Get total instansi (Admin 1 stats)
$total_ins_query = mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role='user_instansi'");
$total_instansi = mysqli_fetch_assoc($total_ins_query)['total'];

// Get total complaints (Admin 2 stats)
$total_comp_query = mysqli_query($conn, "SELECT COUNT(id) as total FROM reports WHERE complaint_text IS NOT NULL AND complaint_text != ''");
$total_complaints = mysqli_fetch_assoc($total_comp_query)['total'];
?>

<div class="row text-center mt-4 mb-4">
    <div class="col-md-6 mb-3">
        <div class="card bg-dark text-white h-100 shadow-sm">
            <div class="card-body">
                <h5>Total Akun Instansi Terdaftar</h5>
                <h2><?= $total_instansi ?></h2>
                <small>Dikelola oleh Admin 1</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card bg-danger text-white h-100 shadow-sm">
            <div class="card-body">
                <h5>Total Keluhan / Laporan Buruk</h5>
                <h2><?= $total_complaints ?></h2>
                <small>Dipantau oleh Admin 2</small>
            </div>
        </div>
    </div>
</div>

<div class="row text-center mt-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white h-100 shadow-sm">
            <div class="card-body">
                <h5>Total Laporan Validasi</h5>
                <h2>
                    <?= $total_reports ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white h-100 shadow-sm" style="background-color: #198754;">
            <div class="card-body">
                <h5>Status Hijau</h5>
                <h2>
                    <?= $status_counts['hijau'] ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-dark h-100 shadow-sm" style="background-color: #ffc107;">
            <div class="card-body">
                <h5>Status Kuning</h5>
                <h2>
                    <?= $status_counts['kuning'] ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white h-100 shadow-sm" style="background-color: #dc3545;">
            <div class="card-body">
                <h5>Status Merah</h5>
                <h2>
                    <?= $status_counts['merah'] ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Grafik Status Gizi Keseluruhan</h5>
            </div>
            <div class="card-body">
                <canvas id="giziChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('giziChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Terpenuhi (Hijau)', 'Cukup (Kuning)', 'Kurang (Merah)'],
                datasets: [{
                    data: [<?= $status_counts['hijau'] ?>, <?= $status_counts['kuning'] ?>, <?= $status_counts['merah'] ?>],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545']
                }]
            }
        });
    }
</script>