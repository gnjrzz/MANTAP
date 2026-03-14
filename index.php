<?php
session_start();
require 'koneksi.php';

// Fetch TKPI data for the calculator
$tkpi_query = mysqli_query($conn, "SELECT * FROM tkpi_data ORDER BY food_name ASC");
$tkpi_data = [];
while ($row = mysqli_fetch_assoc($tkpi_query)) {
    $tkpi_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MANTAP - Monitoring Asupan Nutrisi Tepat Anak & Pelajar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hero {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
            padding: 60px 0;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }

        .indicator-green {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .indicator-yellow {
            background-color: #fff3cd;
            color: #664d03;
        }

        .indicator-red {
            background-color: #f8d7da;
            color: #842029;
        }

        #calcResultBox {
            display: none;
            margin-top: 20px;
            transition: 0.3s;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="bg-blue-600 sticky top-0 z-50 py-3 shadow-lg">
        <div class="container mx-auto px-4 flex justify-between items-center text-white">
            <a class="text-2xl font-bold no-underline text-white" href="index.php">MANTAP</a>

            <div class="flex items-center gap-3">
                <a class="hidden md:block no-underline text-white opacity-90 hover:opacity-100 px-3"
                    href="#tentang">Tentang Program</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="bg-white text-blue-600 px-4 py-2 rounded-lg font-bold no-underline hover:bg-slate-100 transition-colors"
                        href="dashboard.php">Dashboard (<?= $_SESSION['name'] ?>)</a>
                <?php else: ?>
                    <a class="border border-white px-4 py-2 rounded-lg font-semibold no-underline hover:bg-white hover:text-blue-600 transition-colors"
                        href="auth_baru.php">Masuk</a>
                    <a class="bg-yellow-400 text-slate-900 px-4 py-2 rounded-lg font-bold no-underline hover:bg-yellow-300 transition-colors"
                        href="auth_baru.php">Daftar</a>
                <?php endif; ?>
                </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero text-center text-md-start">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h1 class="display-4 fw-bold">Pastikan Nutrisi Anak Tercukupi</h1>
                    <p class="lead">Monitoring Asupan Nutrisi Tepat Anak & Pelajar (MANTAP). Solusi cerdas memantau gizi
                        menu harian sesuai standar Kemenkes RI.</p>
                    <a href="auth_baru.php" class="btn btn-light btn-lg text-primary fw-bold px-4">Coba Kalkulator</a>
                </div>
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80&w=1000"
                        class="img-fluid rounded-4 shadow-lg" alt="Healthy Food">
                </div>
            </div>
        </div>
    </header>

    <!-- Tentang Program -->
    <section id="tentang" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Mengapa MANTAP?</h2>
                <p class="text-muted">Indikator nutrisi ini berdasarkan 1/3 Angka Kecukupan Gizi (AKG) harian anak
                    sekolah.</p>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm indicator-green">
                        <div class="card-body">
                            <h4 class="card-title fw-bold">HIJAU (Terpenuhi)</h4>
                            <p class="card-text">&ge; 700 kkal &amp; &ge; 20g Protein</p>
                            <small>Memenuhi kebutuhan energi dan protein untuk aktivitas belajar.</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm indicator-yellow">
                        <div class="card-body">
                            <h4 class="card-title fw-bold">KUNING (Cukup)</h4>
                            <p class="card-text">Di antara Hijau dan Merah</p>
                            <small>Kebutuhan nutrisi rata-rata, namun perlu peningkatan variasi menu.</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm indicator-red">
                        <div class="card-body">
                            <h4 class="card-title fw-bold">MERAH (Kurang)</h4>
                            <p class="card-text">&lt; 500 kkal atau &lt; 10g Protein</p>
                            <small>Perhatian khusus, asupan nutrisi di bawah standar harian.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- Column 1: Logo & Mission -->
                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-white tracking-tight">MANTAP</h2>
                    <p class="text-sm leading-relaxed text-slate-400">Platform monitoring gizi terpadu untuk anak dan
                        pelajar Indonesia. Bersama mewujudkan generasi emas melalui asupan nutrisi yang tepat dan
                        terpantau.</p>
                </div>
                <!-- Column 2: Navigasi -->
                <div>
                    <h3 class="text-white font-semibold mb-6">Navigasi</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>
                <!-- Column 3: Fitur -->
                <div>
                    <h3 class="text-white font-semibold mb-6">Fitur Utama</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="auth_baru.php" class="hover:text-white transition-colors">Kalkulator Gizi</a></li>
                        <li><a href="auth_baru.php" class="hover:text-white transition-colors">Riwayat Statistik</a></li>
                        <li><a href="auth_baru.php" class="hover:text-white transition-colors">Monitoring Sekolah</a></li>
                    </ul>
                </div>
                <!-- Column 4: Kontak & Sosmed -->
                <div>
                    <h3 class="text-white font-semibold mb-6">Sosial Media</h3>
                    <div class="flex gap-3 mb-6">
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center bg-slate-800 rounded-full hover:bg-blue-600 transition-all text-white"><i
                                class="bi bi-instagram"></i></a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center bg-slate-800 rounded-full hover:bg-blue-700 transition-all text-white"><i
                                class="bi bi-linkedin"></i></a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center bg-slate-800 rounded-full hover:bg-slate-700 transition-all text-white"><i
                                class="bi bi-github"></i></a>
                    </div>
                    <p class="text-sm">Email: <a href="mailto:halo@mantap.id"
                            class="text-white hover:underline">halo@mantap.id</a></p>
                </div>
            </div>
            <!-- Bottom Section -->
            <div
                class="mt-16 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center text-xs space-y-4 md:space-y-0 text-slate-500">
                <p>&copy; <?= date("Y") ?> MANTAP - Monitoring Asupan Nutrisi Tepat Anak & Pelajar. Seluruh Hak Cipta
                    Dilindungi.</p>
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-slate-300 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-300 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const foodList = document.getElementById("foodList");
            const btnAddRow = document.getElementById("btnAddRow");
            const btnCalculate = document.getElementById("btnCalculate");
            const calcResultBox = document.getElementById("calcResultBox");

            const tkpiDictionary = <?= json_encode($tkpi_data) ?>;

            // Add new row
            btnAddRow.addEventListener("click", function () {
                const firstRow = document.querySelector(".food-row");
                const newRow = firstRow.cloneNode(true);
                newRow.querySelector(".food-input").value = "";
                newRow.querySelector(".btn-remove").disabled = false;

                // Remove row logic
                newRow.querySelector(".btn-remove").addEventListener("click", function () {
                    newRow.remove();
                });

                foodList.appendChild(newRow);
            });

            // Calculate logic
            btnCalculate.addEventListener("click", function () {
                let totals = {
                    water: 0, calories: 0, protein: 0, fat: 0, carbohydrate: 0,
                    fiber: 0, ash: 0, calcium: 0, phosphorus: 0, iron: 0,
                    sodium: 0, potassium: 0, copper: 0, zinc: 0, retinol: 0,
                    beta_carotene: 0, total_carotene: 0, thiamin: 0, riboflavin: 0,
                    niacin: 0, vitamin_c: 0
                };
                let hasValidInput = false;
                let unknownItems = [];

                let foodItemsList = [];
                document.querySelectorAll(".food-row").forEach(row => {
                    const inputName = row.querySelector(".food-input").value.trim();
                    if (inputName === "") return;

                    hasValidInput = true;
                    const item = tkpiDictionary.find(x => x.food_name.toLowerCase() === inputName.toLowerCase());

                    if (item) {
                        foodItemsList.push(item.food_name);
                        const bddRatio = (parseFloat(item.bdd_percentage) || 100) / 100;
                        const factor = bddRatio; // Fixed 100g portion

                        for (let key in totals) {
                            if (item[key] !== undefined) {
                                totals[key] += factor * parseFloat(item[key] || 0);
                            }
                        }
                    } else {
                        unknownItems.push(inputName);
                    }
                });

                if (!hasValidInput) {
                    alert("Masukkan minimal 1 nama bahan makanan.");
                    return;
                }

                if (unknownItems.length > 0) {
                    const warningDiv = `<div class="alert alert-warning py-2 mb-3 shadow-sm">
                        <small><strong>Perhatian:</strong> Makanan berikut tidak ditemukan: <em>${unknownItems.join(", ")}</em></small>
                    </div>`;
                    document.getElementById("resNutritionTable").innerHTML = warningDiv;
                } else {
                    document.getElementById("resNutritionTable").innerHTML = "";
                }

                // Update UI Categorized (3-Column)
                const macroLabels = {
                    calories: { label: "Energi", unit: "Kkal", target: 700 },
                    protein: { label: "Protein", unit: "g", target: 20 }
                };

                const microLabels = {
                    fat: "Lemak (g)",
                    carbohydrate: "Karbohidrat (g)",
                    fiber: "Serat (g)",
                    water: "Air (g)", ash: "Abu (g)",
                    calcium: "Kalsium (mg)", phosphorus: "Fosfor (mg)",
                    iron: "Besi (mg)", sodium: "Natrium (mg)", potassium: "Kalium (mg)",
                    copper: "Tembaga (mg)", zinc: "Seng (mg)", retinol: "Retinol (mcg)",
                    beta_carotene: "B-Karoten (mcg)", total_carotene: "Karoten Total (mcg)",
                    thiamin: "Thiamin (mg)", riboflavin: "Riboflavin (mg)",
                    niacin: "Niasin (mg)", vitamin_c: "Vit C (mg)"
                };

                // Column 1: Daftar Menu
                let menuHtml = `
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white border rounded h-100 shadow-sm">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Daftar Menu (Valid)</h6>
                            <ul class="list-group list-group-flush" style="font-size: 0.85rem;">`;
                foodItemsList.forEach(name => {
                    menuHtml += `<li class="list-group-item px-0 py-1 border-0">• ${name}</li>`;
                });
                menuHtml += `</ul></div></div>`;

                // Column 2: Makro
                let macroHtml = `
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-light border rounded h-100 shadow-sm">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Zat Gizi Makro</h6>`;

                for (let key in macroLabels) {
                    const info = macroLabels[key];
                    const val = totals[key];
                    let progressHtml = "";

                    if (info.target) {
                        const percent = Math.min((val / info.target) * 100, 100);
                        const color = percent >= 100 ? "bg-success" : (percent > 50 ? "bg-info" : "bg-warning");
                        progressHtml = `
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar ${color}" role="progressbar" style="width: ${percent}%"></div>
                            </div>
                            <small class="text-muted mb-2 d-block" style="font-size: 0.7rem;">Target: ${info.target} ${info.unit}</small>
                        `;
                    }

                    macroHtml += `
                        <div class="row align-items-center mb-1" style="font-size: 0.85rem;">
                            <div class="col-8"><strong>${info.label}</strong></div>
                            <div class="col-4 text-end font-monospace">${val.toFixed(2)}</div>
                        </div>
                        ${progressHtml}
                    `;
                }
                macroHtml += `</div></div>`;

                // Column 3: Mikro
                let microHtml = `
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-white border rounded h-100 shadow-sm">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Zat Gizi Mikro</h6>
                            <table class="table table-sm table-striped mb-0" style="font-size: 0.75rem;">
                                <tbody>`;

                let microItems = Object.entries(microLabels);
                for (let i = 0; i < 3; i++) {
                    const [key, label] = microItems[i];
                    microHtml += `<tr><td>${label}</td><td class="text-end fw-bold">${totals[key].toFixed(2)}</td></tr>`;
                }
                microHtml += `</tbody></table>
                    <div class="accordion accordion-flush mt-1" id="microAccordion">
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-1 px-0 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#microMore" style="font-size: 0.7rem;">
                                    <strong>Detail Lainnya (${microItems.length - 3})</strong>
                                </button>
                            </h2>
                            <div id="microMore" class="accordion-collapse collapse" data-bs-parent="#microAccordion">
                                <div class="accordion-body p-0">
                                    <table class="table table-sm table-striped mb-0" style="font-size: 0.75rem;">
                                        <tbody>`;
                for (let i = 3; i < microItems.length; i++) {
                    const [key, label] = microItems[i];
                    microHtml += `<tr><td>${label}</td><td class="text-end fw-bold">${totals[key].toFixed(2)}</td></tr>`;
                }
                microHtml += `</tbody></table></div></div></div></div></div></div>`;

                document.getElementById("resNutritionTable").innerHTML = `<div class="row g-2">${menuHtml}${macroHtml}${microHtml}</div>`;

                const resStatus = document.getElementById("resStatus");
                const reportAction = document.getElementById("reportAction");
                calcResultBox.className = "card rounded border-2"; // reset
                reportAction.style.display = "none"; // reset

                // Logic Kemenkes indikator (Still based on Energy & Protein minimum thresholds)
                if (totals.calories >= 700 && totals.protein >= 20) {
                    resStatus.innerText = "HIJAU (Terpenuhi)";
                    calcResultBox.classList.add("indicator-green", "border-success");
                } else if (totals.calories < 500 || totals.protein < 10) {
                    resStatus.innerText = "MERAH (Kurang)";
                    calcResultBox.classList.add("indicator-red", "border-danger");
                    reportAction.style.display = "block";
                } else {
                    resStatus.innerText = "KUNING (Cukup / Rata-rata)";
                    calcResultBox.classList.add("indicator-yellow", "border-warning");
                    reportAction.style.display = "block";
                }

                calcResultBox.style.display = "block";
            });
        });
    </script>
</body>

</html>