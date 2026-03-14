<h2>Dashboard Admin 2 (Monitoring Evaluasi Gizi)</h2>
<p class="text-muted">Pantau khusus keluhan dan laporan menu di bawah standar (MERAH / KUNING).</p>

<div class="card shadow border-danger mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Daftar Keluhan Terbaru (Menunggu Evaluasi)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tgl Laporan</th>
                        <th>Pelapor</th>
                        <th>Peran</th>
                        <th>Status Gizi</th>
                        <th>Keluhan Teks</th>
                        <th>Bukti Foto</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch reports that have complaints, ordered by latest
                    $qry_reports = "
                        SELECT r.*, u.name as user_name, u.role as user_role, u.phone, u.email
                        FROM reports r 
                        JOIN users u ON r.user_id = u.id 
                        WHERE r.complaint_text IS NOT NULL AND r.complaint_text != ''
                        ORDER BY r.report_date DESC
                    ";
                    $res_reports = $conn->query($qry_reports);

                    if ($res_reports->num_rows > 0):
                        while ($row = $res_reports->fetch_assoc()):
                            $statClass = $row['status'] == 'merah' ? 'bg-danger' : 'bg-warning text-dark';
                            ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($row['report_date']) ?>
                                </td>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['user_name']) ?>
                                    </strong><br>
                                    <small>
                                        <?= htmlspecialchars($row['email']) ?> |
                                        <?= htmlspecialchars($row['phone']) ?>
                                    </small>
                                </td>
                                <td>
                                    <?= $row['user_role'] == 'user_instansi' ? 'Instansi' : 'Wali Murid' ?>
                                </td>
                                <td><span class="badge <?= $statClass ?>">
                                        <?= strtoupper($row['status']) ?>
                                    </span></td>
                                <td>
                                    <?= htmlspecialchars($row['complaint_text']) ?>
                                </td>
                                <td>
                                    <?php if ($row['proof_image']): ?>
                                        <a href="<?= htmlspecialchars($row['proof_image']) ?>" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">Lihat Foto</a>
                                    <?php else: ?>
                                        <span class="text-muted text-center">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary"
                                        onclick="alert('Fitur Tindak Lanjut akan menghubungkan Admin ke Kontak Pelapor')">Tindak
                                        Lanjut</button>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada keluhan yang perlu dievaluasi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow border-0">
    <div class="card-header bg-white">
        <h5 class="mb-0">Semua Data Laporan (Seluruh Sistem)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Tgl Laporan</th>
                        <th>Pelapor</th>
                        <th>Total Energi</th>
                        <th>Total Protein</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry_all = "SELECT r.*, u.name as user_name FROM reports r JOIN users u ON r.user_id = u.id ORDER BY r.report_date DESC LIMIT 50";
                    $res_all = $conn->query($qry_all);
                    while ($row = $res_all->fetch_assoc()):
                        $bClass = "badge-" . $row['status'];
                        ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($row['report_date']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['user_name']) ?>
                            </td>
                            <td>
                                <?= $row['total_energy'] ?> kkal
                            </td>
                            <td>
                                <?= $row['total_protein'] ?> g
                            </td>
                            <td><span class="badge <?= $bClass ?> text-uppercase">
                                    <?= $row['status'] ?>
                                </span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>