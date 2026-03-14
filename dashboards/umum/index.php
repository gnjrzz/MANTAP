<?php
$page_title = 'Dashboard Personal';
include_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../../koneksi.php';

checkRole(['User Umum']);

// Fetch TKPI for autocomplete
$stmt_tkpi = $conn->query("SELECT * FROM tkpi_data ORDER BY food_name");
$tkpi_data = $stmt_tkpi->fetch_all(MYSQLI_ASSOC);

// Fetch History for Chart (last 10 calc)
$stmt_hist = $conn->prepare("SELECT calculated_at, calories, protein FROM calculation_history WHERE user_id = ? ORDER BY calculated_at ASC LIMIT 10");
$stmt_hist->bind_param("i", $_SESSION['user_id']);
$stmt_hist->execute();
$hist_data = $stmt_hist->get_result()->fetch_all(MYSQLI_ASSOC);

$labels = json_encode(array_map(fn($row) => date('d M', strtotime($row['calculated_at'])), $hist_data));
$calories_data = json_encode(array_column($hist_data, 'calories'));
$protein_data = json_encode(array_column($hist_data, 'protein'));
?>

<div class="row g-3">
    <!-- Chart Section -->
    <div class="col-xl-9 col-lg-8 mb-4">
        <div class="card card-compact h-100">
            <h6 class="card-header border-0 bg-transparent mb-1 d-flex align-items-center">
                <i class="bi bi-graph-up text-primary me-2"></i> Tren Gizi Personal
            </h6>
            <div style="height: 180px;">
                <canvas id="nutritionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 mb-4">
        <div class="card card-compact bg-primary text-white h-100 border-0 shadow-lg overflow-hidden position-relative"
            style="background: linear-gradient(135deg, #4361ee 0%, #4895ef 100%) !important;">
            <div class="position-absolute end-0 bottom-0 p-2 opacity-20">
                <i class="bi bi-balloon-heart-fill fs-1"></i>
            </div>
            <h6 class="fw-bold mb-2">Status Hari Ini</h6>
            <div class="my-auto py-2 text-center">
                <p class="h4 fw-bold mb-0">Mantap!</p>
                <i class="bi bi-check-circle-fill fs-2 mt-1 d-block"></i>
            </div>
            <p class="small opacity-75 mt-2 mb-0" style="font-size: 0.75rem;">Pola makan seimbang. Pertahankan!</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Calculator -->
    <div class="col-md-12 mb-4">
        <div class="card card-compact shadow-sm border-0">
            <h6 class="mb-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calculator-fill text-info me-2"></i> Kalkulator Gizi & Laporan</span>
                <span class="badge bg-light text-dark fw-normal" style="font-size: 0.65rem;">Standar 100g/porsi</span>
            </h6>

            <form id="dashboardCalcForm">
                <div id="calcInputs" style="max-height: 150px; overflow-y: auto; padding-right: 5px;">
                    <div class="row g-2 mb-2 calc-row">
                        <div class="col-10">
                            <input type="text" class="form-control form-control-sm food-input" list="tkpiList"
                                placeholder="Nama makanan (misal: Nasi Putih)" required>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove" disabled><i
                                    class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-xs btn-outline-secondary py-1" onclick="addRow()"><i
                            class="bi bi-plus-lg"></i> Tambah</button>
                    <button type="button" class="btn btn-sm btn-info text-white px-3 fw-bold ms-auto"
                        id="btnHitungGizi">Hitung</button>
                </div>
            </form>

            <!-- Result Box (Hidden by default) -->
            <div id="calcResultBox" class="mt-4 card p-4 border-2 shadow-none"
                style="display:none; border-radius: 20px;">
                <div class="card-body p-0">
                    <div class="text-center mb-4">
                        <span class="badge rounded-pill px-4 py-2 mb-2" id="resStatusBadge"
                            style="font-size: 0.9rem;">STATUS</span>
                        <h3 class="fw-bold mb-0" id="resStatusText">Status Gizi</h3>
                    </div>

                    <div id="resNutritionTable">
                        <!-- 3-Column UI injected here -->
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <button type="button" id="btnSaveCalc" class="btn btn-primary px-5 py-2 fw-bold rounded-pill">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Simpan ke Riwayat Saya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- History Table -->
    <div class="col-12 mb-4">
        <div class="card card-compact shadow-sm border-0">
            <h6 class="mb-2"><i class="bi bi-clock-history text-secondary me-2"></i> Riwayat Terakhir</h6>
            <div class="table-scrollable">
                <table class="table table-hover table-sm mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Waktu</th>
                            <th>Energi</th>
                            <th>Prot</th>
                            <th>Lemak</th>
                            <th>Karbo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($hist_data) as $row): ?>
                            <tr>
                                <td><?= date('d/m H:i', strtotime($row['calculated_at'])) ?></td>
                                <td class="fw-bold text-primary"><?= $row['calories'] ?></td>
                                <td><?= $row['protein'] ?></td>
                                <td class="text-muted"><?= $row['fat'] ?? '-' ?></td>
                                <td class="text-muted"><?= $row['carbs'] ?? '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($hist_data)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat.</td>
                            </tr>
                        <?php endif; ?>
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
    const tkpiDictionary = <?= json_encode($tkpi_data) ?>;
    let currentCalculatedData = null;

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

    document.getElementById('btnHitungGizi').addEventListener('click', function () {
        let totals = {
            energy: 0, protein: 0, fat: 0, carbohydrate: 0,
            water: 0, fiber: 0, ash: 0, calcium: 0, phosphorus: 0, iron: 0,
            sodium: 0, potassium: 0, copper: 0, zinc: 0, retinol: 0,
            beta_carotene: 0, total_carotene: 0, thiamin: 0, riboflavin: 0,
            niacin: 0, vitamin_c: 0
        };
        let validItems = [];
        let hasInput = false;

        document.querySelectorAll('.calc-row').forEach(row => {
            const val = row.querySelector('input').value.trim();
            if (!val) return;
            hasInput = true;
            const item = tkpiDictionary.find(x => x.food_name.toLowerCase() === val.toLowerCase());
            if (item) {
                validItems.push(item.food_name);
                const factor = (parseFloat(item.bdd_percentage) || 100) / 100;
                totals.energy += factor * parseFloat(item.calories || 0);
                totals.protein += factor * parseFloat(item.protein || 0);
                totals.fat += factor * parseFloat(item.fat || 0);
                totals.carbohydrate += factor * parseFloat(item.carbohydrate || 0);

                // Other micros
                ['water', 'fiber', 'ash', 'calcium', 'phosphorus', 'iron', 'sodium', 'potassium', 'copper', 'zinc', 'retinol', 'beta_carotene', 'total_carotene', 'thiamin', 'riboflavin', 'niacin', 'vitamin_c'].forEach(k => {
                    totals[k] += factor * parseFloat(item[k] || 0);
                });
            }
        });

        if (!hasInput) return alert("Pilih minimal 1 bahan makanan!");

        currentCalculatedData = totals;
        renderResults(totals, validItems);
    });

    function renderResults(totals, validItems) {
        const macroLabels = {
            energy: { label: "Energi", unit: "Kkal", target: 700 },
            protein: { label: "Protein", unit: "g", target: 20 }
        };

        const microLabels = {
            fat: "Lemak (g)",
            carbohydrate: "Karbohidrat (g)",
            fiber: "Serat (g)",
            water: "Air (g)",
            zinc: "Seng (mg)",
            iron: "Besi (mg)",
            vitamin_c: "Vit C (mg)",
            calcium: "Kalsium (mg)",
            sodium: "Natrium (mg)"
        };

        // 3-Column UI logic
        let menuHtml = `<div class="col-md-4 mb-3"><div class="p-3 bg-white border rounded h-100 shadow-sm"><h6 class="fw-bold mb-3 border-bottom pb-2">Daftar Menu</h6><ul class="list-group list-group-flush small">`;
        validItems.forEach(i => menuHtml += `<li class="list-group-item px-0 py-1 border-0">• ${i}</li>`);
        menuHtml += `</ul></div></div>`;

        let macroHtml = `<div class="col-md-4 mb-3"><div class="p-3 bg-light border rounded h-100 shadow-sm"><h6 class="fw-bold mb-3 border-bottom pb-2">Zat Gizi Makro</h6>`;
        for (let k in macroLabels) {
            const info = macroLabels[k];
            const val = totals[k];
            const percent = Math.min((val / info.target) * 100, 100);
            const color = percent >= 100 ? "bg-success" : (percent > 50 ? "bg-info" : "bg-warning");
            macroHtml += `<div class="d-flex justify-content-between small mb-1"><strong>${info.label}</strong><span>${val.toFixed(1)}</span></div>
            <div class="progress mb-1" style="height: 6px;"><div class="progress-bar ${color}" style="width: ${percent}%"></div></div>
            <small class="text-muted d-block mb-2" style="font-size: 0.65rem;">Target: ${info.target} ${info.unit}</small>`;
        }
        macroHtml += `</div></div>`;

        let microHtml = `<div class="col-md-4 mb-3"><div class="p-3 bg-white border rounded h-100 shadow-sm"><h6 class="fw-bold mb-3 border-bottom pb-2">Zat Gizi Mikro</h6><table class="table table-sm table-striped mb-0" style="font-size:0.7rem;"><tbody>`;
        Object.entries(microLabels).forEach(([k, l]) => {
            microHtml += `<tr><td>${l}</td><td class="text-end fw-bold">${totals[k].toFixed(2)}</td></tr>`;
        });
        microHtml += `</tbody></table></div></div>`;

        document.getElementById('resNutritionTable').innerHTML = `<div class="row">${menuHtml}${macroHtml}${microHtml}</div>`;

        const resStatusBadge = document.getElementById('resStatusBadge');
        const resStatusText = document.getElementById('resStatusText');
        const box = document.getElementById('calcResultBox');

        box.className = "mt-4 card p-4 border-2 shadow-none";
        box.style.display = "block";

        if (totals.energy >= 700 && totals.protein >= 20) {
            resStatusText.innerText = "HIJAU (Terpenuhi)";
            resStatusBadge.className = "badge rounded-pill px-4 py-2 mb-2 bg-success text-white";
            box.classList.add("border-success", "bg-success-subtle");
        } else if (totals.energy < 500 || totals.protein < 10) {
            resStatusText.innerText = "MERAH (Kurang)";
            resStatusBadge.className = "badge rounded-pill px-4 py-2 mb-2 bg-danger text-white";
            box.classList.add("border-danger", "bg-danger-subtle");
        } else {
            resStatusText.innerText = "KUNING (Cukup)";
            resStatusBadge.className = "badge rounded-pill px-4 py-2 mb-2 bg-warning text-dark";
            box.classList.add("border-warning", "bg-warning-subtle");
        }
    }

    document.getElementById('btnSaveCalc').addEventListener('click', function () {
        if (!currentCalculatedData) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;

        const formData = new FormData();
        formData.append('action', 'save_history');
        formData.append('calories', currentCalculatedData.energy);
        formData.append('protein', currentCalculatedData.protein);
        formData.append('fat', currentCalculatedData.fat);
        formData.append('carbs', currentCalculatedData.carbohydrate);

        fetch('save_history.php', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) {
                alert("Berhasil disimpan ke riwayat!");
                window.location.reload();
            }
        });
    });

    // Chart.js
    const ctx = document.getElementById('nutritionChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= $labels ?>,
            datasets: [{
                label: 'Energi (Kcal)',
                data: <?= $calories_data ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Protein (g)',
                data: <?= $protein_data ?>,
                borderColor: '#28a745',
                borderDash: [5, 5],
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php include_once __DIR__ . '/../footer.php'; ?>