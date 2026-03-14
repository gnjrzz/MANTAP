<?php
$page_title = 'Dashboard Instansi';
include_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../../koneksi.php';

checkRole(['Instansi']);

$instansi_name = $_SESSION['name'];

// Fetch aggregate statistics
$sql = "SELECT r.status, COUNT(*) as count 
        FROM reports r 
        JOIN users u ON r.user_id = u.id 
        WHERE u.instansi_name = ? 
        GROUP BY r.status";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $instansi_name);
$stmt->execute();
$stats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$labels = [];
$counts = [];
$colors = [];
$status_map = ['hijau' => 'Sesuai Standar', 'kuning' => 'Kurang Standar', 'merah' => 'Bawah Standar'];
$color_map = ['hijau' => '#28a745', 'kuning' => '#ffc107', 'merah' => '#dc3545'];

foreach ($stats as $row) {
    $labels[] = $status_map[$row['status']] ?? $row['status'];
    $counts[] = $row['count'];
    $colors[] = $color_map[$row['status']] ?? '#6c757d';
}

// Fetch TKPI for Quick Check
$stmt_tkpi = $conn->query("SELECT * FROM tkpi_data ORDER BY food_name");
$tkpi_data = $stmt_tkpi->fetch_all(MYSQLI_ASSOC);
?>

<div class="row g-3 mb-4">
    <!-- Chart Section -->
    <div class="col-xl-7 col-lg-8">
        <div class="card card-compact h-100 shadow-sm border-0">
            <h6 class="mb-3 fw-bold"><i class="bi bi-pie-chart-fill text-primary me-2"></i> Status Gizi Global</h6>
            <div style="height: 220px;">
                <canvas id="instansiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Check Calculator -->
    <div class="col-xl-5 col-lg-4">
        <div class="card card-compact h-100 border-0 shadow-sm transition-none">
            <h6 class="mb-2 fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-2"></i> Cek Cepat Menu</h6>
            <p class="text-muted mb-2" style="font-size: 0.7rem;">Estimasi gizi tanpa simpan laporan.</p>
            <form id="instansiQuickCalc">
                <div id="calcInputs" style="max-height: 80px; overflow-y: auto;">
                    <div class="input-group input-group-sm mb-1 calc-row">
                        <input type="text" class="form-control food-input" list="tkpiList"
                            placeholder="Nama makanan...">
                        <button type="button" class="btn btn-outline-danger btn-remove" disabled>&times;</button>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-xs btn-light border py-0 px-2" onclick="addRow()">+
                        Baris</button>
                    <button type="button" class="btn btn-xs btn-primary py-0 px-2 fw-bold ms-auto"
                        id="btnInstansiCalc">Hitung</button>
                </div>
            </form>

            <div id="calcResultBox" class="mt-2 p-2 border rounded-3 border-2"
                style="display:none; font-size: 0.75rem;">
                <div id="resNutritionTable" class="row g-1"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Summary Boxes -->
    <div class="col-12 mb-4">
        <div class="row g-2 mb-2">
            <?php foreach ($stats as $row):
                $color_class = $row['status'] == 'hijau' ? 'success' : ($row['status'] == 'kuning' ? 'warning' : 'danger');
                ?>
                <div class="col-md-4">
                    <div class="card card-compact border-0 h-100 overflow-hidden position-relative">
                        <h6 class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">
                            <?= $status_map[$row['status']] ?>
                        </h6>
                        <h4 class="fw-bold mb-0"><?= $row['count'] ?> <small class="text-muted fw-normal"
                                style="font-size: 0.7rem;">Siswa</small></h4>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-<?= $color_class ?>" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Detailed Table -->
<div class="col-12">
    <div class="card card-compact shadow-sm border-0">
        <h6 class="mb-3 fw-bold">Laporan Lanjutan Siswa</h6>
        <div class="table-scrollable">
            <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Siswa</th>
                        <th>Tanggal</th>
                        <th>Energi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_recent = "SELECT r.*, u.name as student_name 
                                       FROM reports r 
                                       JOIN users u ON r.user_id = u.id 
                                       WHERE u.instansi_name = ? 
                                       ORDER BY r.created_at DESC LIMIT 10";
                    $stmt_recent = $conn->prepare($sql_recent);
                    $stmt_recent->bind_param("s", $instansi_name);
                    $stmt_recent->execute();
                    $recent = $stmt_recent->get_result();
                    while ($row = $recent->fetch_assoc()):
                        $badge_color = $row['status'] == 'hijau' ? 'success' : ($row['status'] == 'kuning' ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($row['student_name']) ?></td>
                            <td style="font-size: 0.75rem;"><?= date('d/m/y', strtotime($row['report_date'])) ?></td>
                            <td><?= $row['total_energy'] ?></td>
                            <td><span class="badge bg-<?= $badge_color ?>-subtle text-<?= $badge_color ?> text-uppercase"
                                    style="font-size: 0.6rem;"><?= $row['status'] ?></span></td>
                            <td class="text-end"><button class="btn btn-xs btn-outline-primary py-0 px-2">Buka</button></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<datalist id="tkpiList">
    <?php foreach ($tkpi_data as $item): ?>
        <option value="<?= htmlspecialchars($item['food_name']) ?>">
        <?php endforeach; ?>
</datalist>

<script>
    const tkpiDict = <?= json_encode($tkpi_data) ?>;

    function addRow() {
        const container = document.getElementById('calcInputs');
        const firstRow = container.querySelector('.calc-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('input').value = '';
        const removeBtn = newRow.querySelector('.btn-remove');
        removeBtn.disabled = false;
        removeBtn.onclick = function () { newRow.remove(); };
        container.appendChild(newRow);
    }

    document.getElementById('btnInstansiCalc').addEventListener('click', function () {
        let totals = { energy: 0, protein: 0, fat: 0, carbohydrate: 0 };
        let validItems = [];
        document.querySelectorAll('.calc-row').forEach(row => {
            const val = row.querySelector('input').value.trim();
            const item = tkpiDict.find(x => x.food_name.toLowerCase() === val.toLowerCase());
            if (item) {
                validItems.push(item.food_name);
                const factor = (parseFloat(item.bdd_percentage) || 100) / 100;
                totals.energy += factor * parseFloat(item.calories || 0);
                totals.protein += factor * parseFloat(item.protein || 0);
                totals.fat += factor * parseFloat(item.fat || 0);
                totals.carbohydrate += factor * parseFloat(item.carbohydrate || 0);
            }
        });

        if (validItems.length === 0) return alert("Pilih bahan makanan valid.");

        // Minimalist render for sidebar calc
        let html = `
            <div class="col-12 mb-2"><div class="p-2 bg-light border rounded"><h6 class="fw-bold extra-small mb-1">Daftar:</h6><div class="small">${validItems.join(', ')}</div></div></div>
            <div class="col-6"><div class="p-2 border rounded"><small>Energi: <strong>${totals.energy.toFixed(1)}</strong></small></div></div>
            <div class="col-6"><div class="p-2 border rounded"><small>Protein: <strong>${totals.protein.toFixed(1)}</strong></small></div></div>
        `;
        document.getElementById('resNutritionTable').innerHTML = html;

        const resStatus = document.getElementById('resStatus');
        const box = document.getElementById('calcResultBox');
        box.style.display = "block";
        if (totals.energy >= 700 && totals.protein >= 20) {
            resStatus.innerText = "HIJAU"; resStatus.className = "text-center fw-bold small text-success";
            box.className = "mt-3 p-2 border rounded border-success bg-success-subtle";
        } else if (totals.energy < 500 || totals.protein < 10) {
            resStatus.innerText = "MERAH"; resStatus.className = "text-center fw-bold small text-danger";
            box.className = "mt-3 p-2 border rounded border-danger bg-danger-subtle";
        } else {
            resStatus.innerText = "KUNING"; resStatus.className = "text-center fw-bold small text-warning";
            box.className = "mt-3 p-2 border rounded border-warning bg-warning-subtle";
        }
    });

    // Main Chart
    new Chart(document.getElementById('instansiChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                data: <?= json_encode($counts) ?>,
                backgroundColor: <?= json_encode($colors) ?>,
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>

<?php include_once __DIR__ . '/../footer.php'; ?>