DROP DATABASE IF EXISTS mantap;
CREATE DATABASE mantap;
USE mantap;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL, -- added for user validation
    relationship VARCHAR(50) DEFAULT NULL, -- added for user umum validation (e.g parent of)
    password VARCHAR(255) NOT NULL,
    role ENUM('master_admin', 'admin_1', 'admin_2', 'user_instansi', 'user_umum') NOT NULL,
    instansi_name VARCHAR(100) DEFAULT NULL, -- If user is tied to instansi
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tkpi_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_name VARCHAR(100) NOT NULL,
    bdd_percentage DECIMAL(5,2) NOT NULL,
    water DECIMAL(8,2) DEFAULT 0, -- Air (g)
    calories DECIMAL(8,2) NOT NULL, -- Energi (Kal/Kcal)
    protein DECIMAL(8,2) NOT NULL, -- Protein (g)
    fat DECIMAL(8,2) DEFAULT 0, -- Lemak (g)
    carbohydrate DECIMAL(8,2) DEFAULT 0, -- KH (g)
    fiber DECIMAL(8,2) DEFAULT 0, -- Serat (g)
    ash DECIMAL(8,2) DEFAULT 0, -- Abu (g)
    calcium DECIMAL(8,2) DEFAULT 0, -- Kalsium (mg)
    phosphorus DECIMAL(8,2) DEFAULT 0, -- Fosfor (mg)
    iron DECIMAL(8,2) DEFAULT 0, -- Besi (mg)
    sodium DECIMAL(8,2) DEFAULT 0, -- Natrium (mg)
    potassium DECIMAL(8,2) DEFAULT 0, -- Kalium (mg)
    copper DECIMAL(8,2) DEFAULT 0, -- Tembaga (mg)
    zinc DECIMAL(8,2) DEFAULT 0, -- Seng (mg)
    retinol DECIMAL(8,2) DEFAULT 0, -- Retinol (mcg)
    beta_carotene DECIMAL(8,2) DEFAULT 0, -- B-Kar (mcg)
    total_carotene DECIMAL(8,2) DEFAULT 0, -- Kar-Total (mcg)
    thiamin DECIMAL(8,2) DEFAULT 0, -- Thiamin (mg)
    riboflavin DECIMAL(8,2) DEFAULT 0, -- Riboflavin (mg)
    niacin DECIMAL(8,2) DEFAULT 0, -- Niasin (mg)
    vitamin_c DECIMAL(8,2) DEFAULT 0, -- Vit C (mg)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    report_date DATE NOT NULL,
    total_water DECIMAL(8,2) DEFAULT 0,
    total_energy DECIMAL(8,2) NOT NULL,
    total_protein DECIMAL(8,2) NOT NULL,
    total_fat DECIMAL(8,2) DEFAULT 0,
    total_carbohydrate DECIMAL(8,2) DEFAULT 0,
    total_fiber DECIMAL(8,2) DEFAULT 0,
    total_ash DECIMAL(8,2) DEFAULT 0,
    total_calcium DECIMAL(8,2) DEFAULT 0,
    total_phosphorus DECIMAL(8,2) DEFAULT 0,
    total_iron DECIMAL(8,2) DEFAULT 0,
    total_sodium DECIMAL(8,2) DEFAULT 0,
    total_potassium DECIMAL(8,2) DEFAULT 0,
    total_copper DECIMAL(8,2) DEFAULT 0,
    total_zinc DECIMAL(8,2) DEFAULT 0,
    total_retinol DECIMAL(8,2) DEFAULT 0,
    total_beta_carotene DECIMAL(8,2) DEFAULT 0,
    total_carotene DECIMAL(8,2) DEFAULT 0,
    total_thiamin DECIMAL(8,2) DEFAULT 0,
    total_riboflavin DECIMAL(8,2) DEFAULT 0,
    total_niacin DECIMAL(8,2) DEFAULT 0,
    total_vitamin_c DECIMAL(8,2) DEFAULT 0,
    status ENUM('hijau', 'kuning', 'merah') NOT NULL,
    complaint_text TEXT NULL, -- Allow complaint
    proof_image VARCHAR(255) NULL, -- Allow proof upload
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS report_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    tkpi_id INT NULL,
    custom_food_name VARCHAR(100) NULL,
    weight DECIMAL(8,2) NOT NULL, -- in grams
    water DECIMAL(8,2) DEFAULT 0,
    energy DECIMAL(8,2) NOT NULL,
    protein DECIMAL(8,2) NOT NULL,
    fat DECIMAL(8,2) DEFAULT 0,
    carbohydrate DECIMAL(8,2) DEFAULT 0,
    fiber DECIMAL(8,2) DEFAULT 0,
    ash DECIMAL(8,2) DEFAULT 0,
    calcium DECIMAL(8,2) DEFAULT 0,
    phosphorus DECIMAL(8,2) DEFAULT 0,
    iron DECIMAL(8,2) DEFAULT 0,
    sodium DECIMAL(8,2) DEFAULT 0,
    potassium DECIMAL(8,2) DEFAULT 0,
    copper DECIMAL(8,2) DEFAULT 0,
    zinc DECIMAL(8,2) DEFAULT 0,
    retinol DECIMAL(8,2) DEFAULT 0,
    beta_carotene DECIMAL(8,2) DEFAULT 0,
    total_carotene DECIMAL(8,2) DEFAULT 0,
    thiamin DECIMAL(8,2) DEFAULT 0,
    riboflavin DECIMAL(8,2) DEFAULT 0,
    niacin DECIMAL(8,2) DEFAULT 0,
    vitamin_c DECIMAL(8,2) DEFAULT 0,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (tkpi_id) REFERENCES tkpi_data(id) ON DELETE SET NULL
);

-- Insert Default Accounts (password is 'password' hashed)
INSERT IGNORE INTO users (name, email, password, role) VALUES 
('Master Admin', 'master@mantap.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'master_admin'),
('Admin 1 (Keamanan)', 'admin1@mantap.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin_1'),
('Admin 2 (Laporan)', 'admin2@mantap.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin_2'),
('SDN 1 Contoh', 'instansi@mantap.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user_instansi'),
('Wali Murid Budi', 'umum@mantap.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user_umum');

-- Insert Seed Data for TKPI (Updated with representative values from TKPI for demonstration)
INSERT IGNORE INTO tkpi_data (food_name, bdd_percentage, water, calories, protein, fat, carbohydrate, fiber, ash, calcium, phosphorus, iron, sodium, potassium, copper, zinc, retinol, beta_carotene, total_carotene, thiamin, riboflavin, niacin, vitamin_c) VALUES 
('Nasi Putih', 100, 68.3, 130, 2.7, 0.3, 28.1, 0.4, 0.2, 3, 34, 0.4, 0, 38, 0.1, 0.6, 0, 0, 0, 0.05, 0.02, 1.3, 0),
('Telur Ayam', 89, 74.3, 154, 12.4, 10.8, 1.2, 0, 1.3, 86, 258, 3, 142, 158, 0.1, 1.5, 140, 20, 150, 0.12, 0.38, 0.1, 0),
('Daging Ayam', 58, 65.0, 298, 18.2, 25.0, 0, 0, 0.8, 14, 200, 1.5, 109, 385, 0.1, 1.5, 20, 0, 50, 0.08, 0.14, 10.4, 0),
('Tempe', 100, 55.3, 201, 20.8, 8.8, 13.5, 1.4, 1.6, 155, 326, 4, 9, 234, 0.5, 1.7, 0, 0, 0, 0.19, 0.59, 4.9, 0),
('Bayam', 71, 94.6, 16, 0.9, 0.4, 2.9, 0.7, 1.2, 166, 76, 3.5, 16, 456, 0.1, 0.4, 0, 2699, 4498, 0.04, 0.1, 1, 41),
('Susu Sapi', 100, 88.3, 61, 3.2, 3.5, 4.3, 0, 0.7, 143, 60, 1.7, 43, 149, 0.02, 0.34, 30, 0, 0, 0.03, 0.17, 0.2, 1),
('Ikan Segar', 80, 76.0, 113, 17.0, 4.5, 0, 0, 1.5, 20, 200, 1, 55, 250, 0.1, 1, 30, 0, 0, 0.05, 0.1, 2, 0),
('Kacang Hijau', 100, 12.0, 323, 22.0, 1.5, 56.8, 7.5, 3.8, 125, 320, 6.7, 15, 1132, 1, 2.5, 0, 0, 150, 0.64, 0.15, 1.2, 6),
('Wortel', 80, 89.9, 36, 1.0, 0.6, 7.9, 0.9, 0.6, 45, 74, 1, 70, 245, 0.1, 0.3, 0, 0, 12000, 0.06, 0.04, 1, 18),
('Kentang', 85, 83.0, 62, 2.1, 0.2, 13.5, 0.5, 1, 11, 56, 0.7, 7, 396, 0.2, 0.3, 0, 0, 0, 0.11, 0.03, 1.4, 21),
('Pisang Ambon', 75, 72.0, 108, 1.0, 0.2, 24.3, 0.6, 0.8, 20, 30, 0.5, 10, 435, 0.1, 0.2, 0, 0, 100, 0.08, 0.05, 0.8, 9),
('Tahu', 100, 82.2, 80, 10.9, 4.7, 0.8, 0.1, 1.4, 223, 183, 3.4, 2, 50, 0.2, 0.8, 0, 0, 0, 0.06, 0.2, 0.1, 0),
('Daging Sapi', 100, 66.0, 249, 18.0, 18.0, 0, 0, 0.9, 11, 170, 2.8, 93, 375, 0.1, 5, 0, 0, 0, 0.08, 0.15, 3.2, 0),
('Jeruk Manis', 70, 87.3, 44, 0.9, 0.2, 11.0, 0.4, 0.4, 33, 23, 0.4, 2, 162, 0.1, 0.2, 0, 0, 120, 0.08, 0.03, 0.3, 49),
('Pepaya', 75, 86.7, 46, 0.5, 0, 12.2, 0.7, 0.6, 23, 12, 1.7, 4, 221, 0, 0.2, 0, 0, 450, 0.04, 0.06, 0.3, 78),
('Jagung Kuning', 100, 72.7, 108, 3.5, 1.3, 25.0, 0, 1.1, 5, 108, 1, 5, 231, 0, 1, 0, 0, 450, 0.12, 0.05, 0.8, 3),
('Ubi Jalar', 85, 68.5, 119, 1.0, 0.3, 28.0, 1.2, 1.2, 45, 54, 0.7, 24, 385, 0.1, 0.3, 0, 0, 300, 0.09, 0.07, 0.8, 22),
('Kangkung', 70, 91.0, 28, 3.4, 0.7, 3.9, 1, 1.5, 67, 54, 2.3, 65, 250, 0.1, 0.4, 0, 0, 3000, 0.1, 0.1, 0.7, 32),
('Tomat', 100, 93.0, 20, 1.0, 0.3, 4.2, 0.5, 0.5, 5, 27, 0.5, 10, 243, 0.1, 0.2, 0, 0, 1000, 0.06, 0.04, 0.6, 40),
('Apel', 88, 84.1, 58, 0.3, 0.4, 14.9, 0.7, 0.3, 6, 10, 0.3, 2, 130, 0.1, 0.1, 0, 0, 90, 0.04, 0.03, 0.1, 5),
('Hati Ayam', 100, 71.0, 145, 18.0, 7.5, 1.0, 0, 1.5, 15, 250, 15, 100, 280, 0.5, 3, 15000, 0, 0, 0.4, 2.5, 10, 10),
('Labu Kuning', 77, 91.2, 29, 1.1, 0.3, 6.6, 0.7, 0.8, 45, 64, 1.4, 1, 230, 0.1, 0.2, 0, 0, 600, 0.08, 0.05, 0.8, 10),
('Mie Kering', 100, 11.4, 337, 7.9, 3.3, 64.9, 0, 2.5, 49, 148, 1.2, 12, 130, 0.2, 1.2, 0, 0, 0, 0.1, 0.05, 2.5, 0),
('Roti Putih', 100, 34.0, 248, 8.0, 1.2, 50.0, 0, 1.5, 10, 95, 1.5, 530, 115, 0.1, 0.8, 0, 0, 0, 0.1, 0.05, 1.5, 0),
('Hati Sapi', 100, 69.7, 136, 19.7, 3.2, 6.0, 0, 1.4, 7, 358, 6.6, 110, 340, 3.2, 5.2, 13000, 0, 0, 0.2, 3.3, 15, 30),
('Udang Segar', 68, 75.0, 91, 21.0, 0.2, 0.1, 0, 2.2, 136, 170, 8, 418, 210, 0.5, 1.5, 30, 0, 0, 0.01, 0.4, 3, 0),
('Cumi-cumi', 100, 78.0, 75, 16.1, 0.7, 0.1, 0, 1.5, 32, 200, 1.8, 273, 220, 0.9, 1.5, 30, 0, 0, 0.08, 0.1, 2, 0),
('Durian', 25, 65.0, 134, 2.5, 3.0, 28.0, 1.4, 1.5, 7, 44, 1.3, 1, 436, 0.2, 0.4, 0, 0, 175, 0.1, 0.2, 0.7, 53),
('Alpukat', 61, 84.3, 85, 0.9, 6.5, 7.7, 1.4, 0.6, 10, 20, 0.9, 2, 278, 0.2, 0.4, 0, 0, 180, 0.05, 0.02, 0.7, 13),
('Semangka', 46, 92.1, 28, 0.5, 0.2, 6.9, 0.4, 0.3, 7, 12, 0.2, 2, 94, 0.1, 0.1, 0, 0, 590, 0.05, 0.05, 0.3, 6),
('Kacang Tanah', 100, 4.0, 525, 27.0, 44.0, 17.0, 2.5, 2.5, 50, 450, 2.5, 5, 450, 1, 3, 0, 0, 0, 0.3, 0.15, 13, 0),
('Brokoli', 100, 91.0, 25, 2.8, 0.3, 4.0, 2.6, 0.8, 47, 66, 0.7, 33, 316, 0.1, 0.4, 0, 0, 300, 0.07, 0.11, 0.6, 89),
('Kembang Kol', 100, 92.0, 25, 1.9, 0.3, 4.9, 2.0, 0.9, 22, 44, 0.4, 30, 299, 0.1, 0.3, 0, 0, 0, 0.05, 0.06, 0.5, 48),
('Kelapa Muda', 53, 91.5, 17, 0.2, 0.1, 4.0, 0, 0.2, 15, 8, 0.2, 1, 150, 0, 0.1, 0, 0, 0, 0.01, 0.02, 0.1, 1),
('Nasi Goreng', 100, 53.0, 250, 5.0, 9.0, 37.0, 0.5, 1.5, 20, 100, 1.2, 500, 150, 0.1, 0.8, 20, 0, 150, 0.1, 0.05, 1.5, 2),
('Nasi Kuning', 100, 58.0, 220, 4.5, 7.0, 35.0, 0.4, 1.4, 18, 90, 1.1, 450, 140, 0.1, 0.7, 15, 0, 120, 0.09, 0.04, 1.3, 1),
('Bubur Ayam', 100, 85.0, 75, 3.5, 2.5, 10.0, 0.2, 0.8, 12, 60, 0.8, 300, 80, 0.05, 0.5, 10, 0, 80, 0.05, 0.03, 1.0, 1),
('Bakso Sapi', 100, 65.0, 190, 12.0, 12.0, 8.0, 0.2, 2.0, 25, 150, 2.0, 600, 200, 0.1, 2.0, 0, 0, 0, 0.05, 0.1, 2.5, 0),
('Mie Ayam', 100, 60.0, 200, 8.0, 6.0, 30.0, 0.5, 2.0, 30, 120, 1.5, 550, 180, 0.1, 1.2, 10, 0, 100, 0.08, 0.05, 2.0, 1),
('Mangga Harum Manis', 80, 83.0, 65, 0.5, 0.2, 16.5, 1.5, 0.6, 12, 11, 0.2, 2, 150, 0.1, 0.1, 0, 2000, 3500, 0.04, 0.04, 0.5, 30),
('Manggis', 25, 80.0, 63, 0.6, 0.1, 15.5, 1.5, 0.3, 8, 12, 0.8, 1, 60, 0.1, 0.1, 0, 0, 20, 0.03, 0.03, 0.3, 5),
('Kelengkeng', 50, 82.0, 64, 1.0, 0.1, 15.5, 0.5, 0.6, 10, 25, 0.5, 1, 150, 0.1, 0.1, 0, 0, 10, 0.04, 0.04, 0.3, 40),
('Srikaya', 50, 75.0, 100, 1.5, 0.4, 24.5, 1.5, 1.0, 20, 30, 1.2, 3, 250, 0.1, 0.2, 0, 0, 20, 0.08, 0.08, 1.0, 20),
('Sawo', 75, 75.0, 92, 0.5, 1.1, 22.5, 1.5, 0.5, 25, 12, 1.0, 12, 190, 0.1, 0.1, 0, 0, 60, 0.02, 0.02, 0.2, 14),
('Pempek Kapal Selam', 100, 55.0, 180, 10.0, 6.0, 22.0, 0.2, 1.8, 40, 150, 1.5, 450, 120, 0.1, 1.0, 50, 0, 100, 0.05, 0.1, 1.5, 0),
('Siomay Bandung', 100, 60.0, 160, 9.0, 5.0, 20.0, 0.5, 2.0, 35, 130, 1.2, 400, 140, 0.1, 0.8, 30, 0, 150, 0.06, 0.08, 1.2, 2),
('Batagor', 100, 45.0, 280, 8.5, 18.0, 22.0, 0.5, 2.0, 30, 120, 1.2, 500, 150, 0.1, 0.9, 20, 0, 100, 0.05, 0.06, 1.5, 0),
('Martabak Telur', 100, 40.0, 350, 12.0, 25.0, 20.0, 0.5, 2.5, 50, 200, 2.5, 650, 180, 0.1, 1.5, 100, 0, 200, 0.1, 0.15, 2.0, 1),
('Martabak Manis', 100, 30.0, 450, 7.0, 22.0, 58.0, 1.0, 2.0, 60, 150, 1.8, 400, 120, 0.1, 1.0, 50, 0, 100, 0.1, 0.15, 1.5, 0),
('Soto Ayam', 100, 88.0, 50, 4.5, 2.5, 2.5, 0.2, 1.8, 15, 80, 1.0, 350, 120, 0.05, 0.6, 10, 0, 150, 0.05, 0.04, 1.0, 2),
('Sate Ayam', 100, 55.0, 220, 20.0, 14.0, 4.0, 0.1, 1.5, 15, 180, 1.8, 400, 300, 0.1, 1.5, 10, 0, 50, 0.08, 0.1, 8.0, 0),
('Ikan Lele', 80, 76.0, 105, 14.8, 4.8, 0, 0, 1.2, 20, 200, 1.0, 65, 250, 0.1, 0.6, 20, 0, 0, 0.1, 0.05, 2.5, 0),
('Ikan Mas', 80, 75.0, 130, 18.3, 5.8, 0, 0, 1.1, 20, 150, 1.3, 70, 280, 0.1, 0.8, 20, 0, 0, 0.1, 0.05, 2.0, 0),
('Ikan Mujair', 80, 78.0, 89, 18.7, 1.0, 0, 0, 1.3, 96, 209, 1.5, 60, 260, 0.1, 0.7, 10, 0, 0, 0.05, 0.1, 2.0, 0),
('Nasi Merah', 100, 64.0, 149, 2.8, 0.4, 32.5, 0.3, 0.5, 6, 63, 0.4, 5, 43, 0.1, 0.7, 0, 0, 0, 0.06, 0.01, 1.6, 0),
('Bubur Beras', 100, 85.0, 60, 1.2, 0.1, 13.0, 0.1, 0.2, 3, 15, 0.2, 2, 20, 0.05, 0.3, 0, 0, 0, 0.02, 0.01, 0.5, 0),
('Singkong Rebus', 100, 62.5, 146, 1.2, 0.3, 34.0, 1.5, 1.0, 33, 40, 0.7, 1, 394, 0.1, 0.4, 0, 0, 0, 0.06, 0.03, 0.6, 30),
('Jagung Rebus', 100, 70.0, 129, 4.1, 1.1, 30.0, 1, 1, 5, 100, 0.8, 5, 250, 0.1, 1, 0, 100, 400, 0.15, 0.1, 1.5, 5),
('Rendang Daging', 100, 50.0, 195, 22.0, 10.0, 5.0, 0.5, 2.0, 20, 170, 2.5, 500, 350, 0.2, 2.5, 0, 0, 50, 0.08, 0.2, 3.5, 0),
('Opor Ayam', 100, 65.0, 160, 15.0, 10.0, 3.0, 0.2, 1.5, 25, 180, 1.5, 450, 300, 0.1, 1.5, 10, 0, 80, 0.06, 0.1, 2.5, 0),
('Sayur Asem', 100, 92.0, 30, 0.7, 0.5, 6.0, 0.8, 0.5, 20, 30, 0.5, 250, 150, 0.05, 0.2, 0, 200, 500, 0.03, 0.02, 0.5, 10),
('Sayur Lodeh', 100, 85.0, 60, 1.5, 4.5, 5.0, 0.7, 1.2, 30, 50, 0.8, 350, 200, 0.1, 0.4, 10, 150, 400, 0.04, 0.05, 0.6, 5),
('Gado-Gado', 100, 70.0, 130, 5.0, 8.0, 11.0, 1.2, 1.5, 50, 100, 1.5, 400, 250, 0.1, 0.8, 20, 300, 600, 0.08, 0.1, 1.5, 15),
('Sambal Terasi', 100, 60.0, 120, 3.5, 6.0, 15.0, 2.0, 3.5, 50, 100, 2.5, 1200, 200, 0.2, 1.0, 50, 1000, 2500, 0.1, 0.15, 1.0, 25),
('Kerupuk Udang', 100, 2.0, 510, 17.0, 26.0, 52.0, 0, 3.0, 100, 250, 2.5, 800, 150, 0.5, 1.5, 0, 0, 0, 0.05, 0.1, 2.0, 0),
('Sawi Hijau', 80, 92.2, 15, 2.3, 0.3, 2.2, 1.2, 1.3, 102, 54, 1.0, 16, 456, 0.1, 0.4, 0, 1940, 3200, 0.04, 0.1, 1.0, 41),
('Sawi Putih', 85, 95.7, 9, 1.0, 0.1, 1.7, 0.8, 0.5, 22, 26, 0.4, 3, 150, 0.1, 0.2, 0, 0, 10, 0.03, 0.02, 0.3, 2),
('Tempe Goreng', 100, 51.5, 225, 19.0, 14.0, 8.0, 1.1, 3.0, 120, 300, 4.0, 350, 250, 0.5, 1.5, 0, 0, 0, 0.1, 0.4, 4.0, 0),
('Tahu Goreng', 100, 68.0, 115, 9.0, 8.0, 2.5, 0.1, 1.4, 120, 100, 2.5, 300, 150, 0.3, 1.0, 0, 0, 0, 0.05, 0.1, 0.5, 0),
('Perkedel Kentang', 100, 60.0, 150, 3.0, 8.0, 18.0, 0.5, 1.5, 20, 60, 1.0, 450, 300, 0.1, 0.5, 20, 0, 50, 0.05, 0.04, 1.0, 2),
('Bakwan Sayur', 100, 40.0, 280, 4.0, 18.0, 25.0, 1.0, 2.0, 30, 80, 1.2, 500, 150, 1.0, 0.8, 20, 0, 100, 0.05, 0.06, 1.5, 2),
('Sayur Bening Bayam', 100, 93.0, 18, 1.0, 0.2, 3.5, 0.8, 1.0, 50, 40, 1.5, 300, 200, 0.1, 0.3, 0, 1000, 2500, 0.05, 0.08, 0.8, 25),
('Sayur Sop', 100, 91.0, 25, 1.0, 0.1, 5.0, 1.0, 1.0, 20, 30, 0.5, 350, 150, 0.05, 0.2, 0, 200, 800, 0.04, 0.03, 0.5, 15),
('Capcay Sayur', 100, 85.0, 55, 2.5, 3.5, 4.0, 1.2, 1.5, 30, 60, 1.0, 400, 200, 0.1, 0.5, 20, 400, 1200, 0.06, 0.08, 1.0, 20),
('Mie Goreng Jawa', 100, 55.0, 230, 6.0, 10.0, 30.0, 0.5, 2.0, 30, 100, 1.5, 600, 150, 0.1, 1.0, 10, 0, 100, 0.08, 0.05, 2.0, 1),
('Nasi Uduk', 100, 60.0, 160, 3.5, 5.0, 28.0, 0.4, 1.5, 20, 50, 0.8, 400, 120, 0.1, 0.6, 5, 0, 50, 0.06, 0.04, 1.2, 1),
('Lontong Sayur', 100, 85.0, 70, 2.0, 4.5, 6.0, 0.8, 1.2, 30, 40, 0.8, 450, 200, 0.1, 0.5, 10, 150, 400, 0.04, 0.02, 0.5, 5),
('Cah Kangkung', 100, 88.0, 45, 3.0, 3.0, 3.0, 1.0, 1.5, 60, 50, 2.0, 500, 250, 0.1, 0.5, 0, 500, 1500, 0.1, 0.08, 1.0, 25),
('Ayam Goreng', 100, 55.0, 260, 22.0, 18.0, 0, 0.1, 1.5, 15, 200, 1.5, 400, 350, 0.1, 1.5, 20, 0, 50, 0.08, 0.1, 8.0, 0),
('Ikan Goreng', 100, 58.0, 180, 18.0, 12.0, 0, 0.1, 1.8, 30, 200, 1.5, 450, 300, 0.1, 1.0, 30, 0, 50, 0.05, 0.1, 2.5, 0);
