<h2>Dashboard Admin 1 (Keamanan & Registrasi Instansi)</h2>
<p class="text-muted">Kelola pendaftaran akun Instansi Sekolah/Vendor secara aman.</p>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_instansi') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = 'user_instansi';

        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            echo "<div class='alert alert-danger'>Email sudah digunakan akun lain.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $role);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Akun Instansi Berhasil Dibuat.</div>";
            } else {
                echo "<div class='alert alert-danger'>Gagal menambahkan akun.</div>";
            }
        }
    } elseif ($_POST['action'] == 'delete_instansi') {
        $user_id = $_POST['user_id'];
        // Ensure we only delete user_instansi
        $stmt_del = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user_instansi'");
        $stmt_del->bind_param("i", $user_id);
        if ($stmt_del->execute()) {
            echo "<div class='alert alert-success'>Akun Instansi Berhasil Dihapus.</div>";
        }
    }
}
?>

<div class="row">
    <!-- Add Instansi -->
    <div class="col-md-5 mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Daftarkan Instansi Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add_instansi">
                    <div class="mb-3">
                        <label>Nama Pengurus / PIC Instansi</label>
                        <input type="text" name="name" class="form-control" required
                            placeholder="Contoh: Kepala Dapur SDN 1">
                    </div>
                    <div class="mb-3">
                        <label>Email Instansi (Untuk Login)</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password Akun</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Buat Akun Instansi</button>
                    <small class="text-muted d-block mt-3 text-center">Hanya Admin 1 yang dapat mendaftarkan akun
                        Instansi untuk menjaga keamanan data.</small>
                </form>
            </div>
        </div>
    </div>

    <!-- List Instansi -->
    <div class="col-md-7">
        <div class="card shadow border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Daftar Akun Instansi Terdaftar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="table-dark">
                                <th>#</th>
                                <th>Nama PIC</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt_users = $conn->prepare("SELECT * FROM users WHERE role = 'user_instansi' ORDER BY created_at DESC");
                            $stmt_users->execute();
                            $res_users = $stmt_users->get_result();
                            $no = 1;

                            while ($row = $res_users->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?= $no++ ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($row['email']) ?>
                                    </td>
                                    <td>
                                        <form method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus akun instansi ini?');">
                                            <input type="hidden" name="action" value="delete_instansi">
                                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($res_users->num_rows == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada instansi terdaftar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>