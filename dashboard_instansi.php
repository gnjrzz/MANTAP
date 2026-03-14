<h2>Dashboard Instansi Sekolah / Vendor</h2>
<p class="text-muted">Input Menu Harian & Validasi Vendor, Serta Pelaporan Mandiri.</p>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'submit_report_instansi') {
    $report_date = $_POST['report_date'];
    $items_text = $_POST['items_text'];
    $items_text = $_POST['items_text'];

    // Vendor or Instansi can also provide constraints/complaints
    $complaint_text = !empty($_POST['complaint_text']) ? $_POST['complaint_text'] : null;
    $proof_image = null;

    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . time() . "_instansi_" . basename($_FILES["proof_image"]["name"]);
        if (getimagesize($_FILES["proof_image"]["tmp_name"]) !== false) {
            if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $target_file)) {
                $proof_image = $target_file;
            }
        }
    }

    $total_n = [
        'water' => 0,
        'energy' => 0,
        'protein' => 0,
        'fat' => 0,
        'carbohydrate' => 0,
        'fiber' => 0,
        'ash' => 0,
        'calcium' => 0,
        'phosphorus' => 0,
        'iron' => 0,
        'sodium' => 0,
        'potassium' => 0,
        'copper' => 0,
        'zinc' => 0,
        'retinol' => 0,
        'beta_carotene' => 0,
        'total_carotene' => 0,
        'thiamin' => 0,
        'riboflavin' => 0,
        'niacin' => 0,
        'vitamin_c' => 0
    ];
    $report_items = [];

    for ($i = 0; $i < count($items_text); $i++) {
        if (!empty($items_text[$i])) {
            $food_name = $items_text[$i];

            $stmt = $conn->prepare("SELECT * FROM tkpi_data WHERE food_name = ?");
            $stmt->bind_param("s", $food_name);
            $stmt->execute();
            $result = $stmt->get_result();

            $calc_item = ['tkpi_id' => null, 'custom_food_name' => $food_name, 'weight' => 100];
            foreach (array_keys($total_n) as $key) {
                $calc_item[$key] = 0;
            }

            if ($item = $result->fetch_assoc()) {
                $bddRatio = $item['bdd_percentage'] / 100;
                $factor = $bddRatio;
                $calc_item['tkpi_id'] = $item['id'];

                foreach (array_keys($total_n) as $key) {
                    $db_key = $key === 'energy' ? 'calories' : $key;
                    $val = $factor * $item[$db_key];
                    $calc_item[$key] = $val;
                    $total_n[$key] += $val;
                }
            }
            $report_items[] = $calc_item;
        }
    }

    if (count($report_items) > 0) {
        if ($total_n['energy'] >= 700 && $total_n['protein'] >= 20) {
            $status = 'hijau';
        } elseif ($total_n['energy'] < 500 || $total_n['protein'] < 10) {
            $status = 'merah';
        } else {
            $status = 'kuning';
        }

        $user_id = $_SESSION['user_id'];

        $insert_rep_sql = "INSERT INTO reports (user_id, report_date, status, complaint_text, proof_image, 
            total_water, total_energy, total_protein, total_fat, total_carbohydrate, total_fiber, total_ash, 
            total_calcium, total_phosphorus, total_iron, total_sodium, total_potassium, total_copper, 
            total_zinc, total_retinol, total_beta_carotene, total_carotene, total_thiamin, total_riboflavin, 
            total_niacin, total_vitamin_c) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_rep = $conn->prepare($insert_rep_sql);
        $stmt_rep->bind_param(
            "issssddddddddddddddddddddd",
            $user_id,
            $report_date,
            $status,
            $complaint_text,
            $proof_image,
            $total_n['water'],
            $total_n['energy'],
            $total_n['protein'],
            $total_n['fat'],
            $total_n['carbohydrate'],
            $total_n['fiber'],
            $total_n['ash'],
            $total_n['calcium'],
            $total_n['phosphorus'],
            $total_n['iron'],
            $total_n['sodium'],
            $total_n['potassium'],
            $total_n['copper'],
            $total_n['zinc'],
            $total_n['retinol'],
            $total_n['beta_carotene'],
            $total_n['total_carotene'],
            $total_n['thiamin'],
            $total_n['riboflavin'],
            $total_n['niacin'],
            $total_n['vitamin_c']
        );

        if ($stmt_rep->execute()) {
            $report_id = $conn->insert_id;

            $insert_item_sql = "INSERT INTO report_items (report_id, tkpi_id, custom_food_name, weight, 
                water, energy, protein, fat, carbohydrate, fiber, ash, calcium, phosphorus, iron, 
                sodium, potassium, copper, zinc, retinol, beta_carotene, total_carotene, thiamin, 
                riboflavin, niacin, vitamin_c) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_item = $conn->prepare($insert_item_sql);

            foreach ($report_items as $ri) {
                $stmt_item->bind_param(
                    "iisdddddddddddddddddddddd",
                    $report_id,
                    $ri['tkpi_id'],
                    $ri['custom_food_name'],
                    $ri['weight'],
                    $ri['water'],
                    $ri['energy'],
                    $ri['protein'],
                    $ri['fat'],
                    $ri['carbohydrate'],
                    $ri['fiber'],
                    $ri['ash'],
                    $ri['calcium'],
                    $ri['phosphorus'],
                    $ri['iron'],
                    $ri['sodium'],
                    $ri['potassium'],
                    $ri['copper'],
                    $ri['zinc'],
                    $ri['retinol'],
                    $ri['beta_carotene'],
                    $ri['total_carotene'],
                    $ri['thiamin'],
                    $ri['riboflavin'],
                    $ri['niacin'],
                    $ri['vitamin_c']
                );
                $stmt_item->execute();
            }
            echo "<div class='alert alert-success mt-3'>Laporan Menu Harian Berhasil Disimpan. Status Gizi: <strong>" . strtoupper($status) . "</strong></div>";
        }
    } else {
        echo "<div class='alert alert-danger mt-3'>Harap masukkan minimal 1 bahan makanan.</div>";
    }
}
?>

<div class="row">
    <!-- Input Form -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Form Input Daily Menu Eksekusi Praktis</h5>
                <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal"
                    data-bs-target="#infoGiziModal">
                    <i class="bi bi-info-circle"></i> Info Standar
                </button>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="submit_report_instansi">
                    <div class="mb-3">
                        <label>Tanggal Menu Harian</label>
                        <input type="date" name="report_date" class="form-control" value="<?= date('Y-m-d') ?>"
                            required>
                    </div>

                    <div id="foodListInstansi">
                        <div class="row g-2 align-items-center mb-3 food-row-in">
                            <div class="col-md-10">
                                <input type="text" name="items_text[]" class="form-control food-input-in"
                                    list="tkpiOptions" autocomplete="off" placeholder="Ketik bahan..." required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-remove-in w-100"
                                    disabled>&times;</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="btnAddRowIn" class="btn btn-outline-primary w-100 mb-4">+ Tambah Bahan
                        Makanan</button>

                    <hr>
                    <h6 class="fw-bold text-secondary">Catatan Opsional / Laporan Kendala Vendor</h6>
                    <div class="mb-3">
                        <label>Contoh: Stok beras telat datang, terpaksa diganti mie...</label>
                        <textarea name="complaint_text" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Bukti Foto (Opsional)</label>
                        <input type="file" name="proof_image" class="form-control" accept="image/*">
                    </div>

                    <datalist id="tkpiOptions">
                        <?php foreach ($tkpi_data as $item): ?>
                            <option value="<?= htmlspecialchars($item['food_name']) ?>">
                            <?php endforeach; ?>
                    </datalist>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">Kirim Laporan Validasi</button>
                    <small class="text-muted d-block mt-2">Perhitungan otomatis menggunakan standar 100g per porsi bahan
                        makanan. Data ini dilaporkan langsung dan terekam di sistem Pusat (Admin 2).</small>
                </form>
            </div>
        </div>
    </div>

    <!-- Report History -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Riwayat Laporan Logbook Instansi Anda</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mt-3">
                        <thead>
                            <tr class="table-light">
                                <th>Tgl Lapor</th>
                                <th>Makro Gizi</th>
                                <th>Status</th>
                                <th>Kendala</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt_hist = $conn->prepare("SELECT * FROM reports WHERE user_id = ? ORDER BY report_date DESC LIMIT 10");
                            $stmt_hist->bind_param("i", $_SESSION['user_id']);
                            $stmt_hist->execute();
                            $res_hist = $stmt_hist->get_result();

                            while ($row = $res_hist->fetch_assoc()):
                                $badge = "badge-" . $row['status'];
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['report_date']) ?></td>
                                    <td><?= $row['total_energy'] ?> kkal<br><?= $row['total_protein'] ?> g</td>
                                    <td><span class="badge <?= $badge ?> text-uppercase"><?= $row['status'] ?></span></td>
                                    <td>
                                        <?php if (!empty($row['complaint_text'])): ?>
                                            <small><?= htmlspecialchars($row['complaint_text']) ?></small>
                                            <?php if ($row['proof_image']): ?>
                                                <br><a href="<?= $row['proof_image'] ?>" target="_blank"
                                                    class="badge bg-secondary text-decoration-none mt-1">Cek BUKTI</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Info Standar Gizi -->
<div class="modal fade" id="infoGiziModal" tabindex="-1" aria-labelledby="infoGiziModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="infoGiziModalLabel">Standar & Perhitungan Gizi MANTAP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6><strong>1. Data Gizi Paling Penting</strong></h6>
                <p>Dalam menentukan Status Gizi laporan harian, sistem memprioritaskan <strong>Energi (Kalori)</strong>
                    dan <strong>Protein</strong> sebagai tolok ukur utama kecukupan gizi anak per porsi makan.</p>

                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Status</th>
                            <th>Indikator Energi</th>
                            <th>Indikator Protein</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-hijau text-uppercase">Hijau</span></td>
                            <td>≥ 700 Kcal</td>
                            <td>≥ 20 g</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-kuning text-uppercase">Kuning</span></td>
                            <td colspan="2">Di antara Hijau dan Merah</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-merah text-uppercase">Merah</span></td>
                            <td>
                                < 500 Kcal</td>
                            <td>
                                < 10 g</td>
                        </tr>
                    </tbody>
                </table>

                <h6><strong>2. Dasar Pengukuran (Porsi)</strong></h6>
                <p>Aplikasi menggunakan standar porsi <strong>100 gram (Berat Bersih)</strong> untuk setiap bahan
                    makanan yang diinput. Ini adalah standarisasi sistem untuk memudahkan validasi otomatis secara
                    cepat.</p>

                <h6><strong>3. Sumber Data</strong></h6>
                <p>Nilai gizi diambil dari <strong>Tabel Komposisi Pangan Indonesia (TKPI)</strong>, mencakup 21
                    komponen gizi lengkap termasuk Makro (Lemak, Karbohidrat) dan Mikro (Zat Besi, Vitamin, Zinc, dll).
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const foodList = document.getElementById("foodListInstansi");
        const btnAddRow = document.getElementById("btnAddRowIn");

        btnAddRow.addEventListener("click", function () {
            const firstRow = document.querySelector(".food-row-in");
            const newRow = firstRow.cloneNode(true);
            newRow.querySelector(".food-input-in").value = "";
            newRow.querySelector(".btn-remove-in").disabled = false;

            newRow.querySelector(".btn-remove-in").addEventListener("click", function () {
                newRow.remove();
            });

            foodList.appendChild(newRow);
        });
    });
</script>