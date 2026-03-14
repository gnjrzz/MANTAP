-- Migration Script for MANTAP RBAC
USE mantap;

-- 1. Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- 2. Insert roles
INSERT IGNORE INTO roles (id, role_name) VALUES 
(1, 'Master Admin'), 
(2, 'Admin'), 
(3, 'Instansi'), 
(4, 'User Umum');

-- 3. Update users table structure
-- Add role_id column first
ALTER TABLE users ADD COLUMN role_id INT AFTER password;

-- Map existing roles to role_id
UPDATE users SET role_id = 1 WHERE role = 'master_admin';
UPDATE users SET role_id = 2 WHERE role IN ('admin_1', 'admin_2');
UPDATE users SET role_id = 3 WHERE role = 'user_instansi';
UPDATE users SET role_id = 4 WHERE role = 'user_umum';

-- 4. Create calculation_history table
CREATE TABLE IF NOT EXISTS calculation_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    calories DECIMAL(8,2),
    protein DECIMAL(8,2),
    fat DECIMAL(8,2),
    carbs DECIMAL(8,2),
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Note: We keep the old 'role' column for now to prevent breaking existing code until middleware is updated.
