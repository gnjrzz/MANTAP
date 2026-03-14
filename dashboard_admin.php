<h2>Dashboard Admin Operasional</h2>
<p class="text-muted">Kelola Database Makanan (TKPI) dan Validasi.</p>

<?php
// Handle Add TKPI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_tkpi') {
    $name = $_POST['name'];
    $bdd = $_POST['bdd'];
    $energy = $_POST['energy'];
    $protein = $_POST['protein'];
    $fat = $_POST['fat'];
    $carb = $_POST['carb'];

    $stmt = $conn->prepare("INSERT INTO tkpi_data (food_name, bdd_percentage, calories, protein, fat, carbohydrate) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sddddd", $name, $bdd, $energy, $protein, $fat, $carb);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Data Bahan Makanan berhasil ditambahkan!</div>";
        // Refresh local array
        $tkpi_query = mysqli_query($conn, "SELECT * FROM tkpi_data ORDER BY food_name ASC");
        $tkpi_data = [];
        while ($row = mysqli_fetch_assoc($tkpi_query)) {
            $tkpi_data[] = $row;
        }
    } else {
        echo "<div class='alert alert-danger'>Gagal menambah data.</div>";
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tambah Bahan Makanan Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add_tkpi">
                    <div class="mb-2">
                        <label>Nama Makanan</label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Pisang Ambon">
                    </div>
                    <div class="mb-2">
                        <label>BDD (%)</label>
                        <input type="number" step="0.01" name="bdd" class="form-control" required
                            placeholder="Contoh: 75">
                    </div>
                    <div class="mb-2">
                        <label>Energi (kkal per 100g)</label>
                        <input type="number" step="0.01" name="energy" class="form-control" required
                            placeholder="Contoh: 108">
                    </div>
                    <div class="mb-2">
                        <label>Protein (g per 100g)</label>
                        <input type="number" step="0.01" name="protein" class="form-control" required
                            placeholder="Contoh: 1">
                    </div>
                    <div class="mb-2">
                        <label>Lemak (g per 100g)</label>
                        <input type="number" step="0.01" name="fat" class="form-control" required
                            placeholder="Contoh: 0.2">
                    </div>
                    <div class="mb-3">
                        <label>Karbohidrat (g per 100g)</label>
                        <input type="number" step="0.01" name="carb" class="form-control" required
                            placeholder="Contoh: 25.8">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Data</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Daftar Bahan Makanan (TKPI)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Makanan</th>
                                <th>BDD</th>
                                <th>Energi</th>
                                <th>Protein</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($tkpi_data as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($item['food_name']) ?></td>
                                    <td><?= $item['bdd_percentage'] ?> %</td>
                                    <td><?= $item['calories'] ?> kkal</td>
                                    <td><?= $item['protein'] ?> g</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>