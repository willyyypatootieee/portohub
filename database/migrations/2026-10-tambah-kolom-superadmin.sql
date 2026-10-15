-- PortfolioHub migration: kolom untuk fitur superadmin.
--
-- Menambahkan penanda status akun (users.is_active) dan penanda proyek yang
-- disembunyikan pengelola (studio_projects.is_hidden). Jalankan pada database
-- yang SUDAH ada sebelum memakai fitur superadmin. Instalasi baru tidak perlu
-- file ini karena creator-studio.sql sudah memuat kolomnya.
--
-- Aman dijalankan berulang kali (idempoten): kolom yang sudah ada dilewati.
--
--   docker exec -i portohub-database-1 mysql -u root -p portfolio_hub \
--     < database/migrations/2026-10-tambah-kolom-superadmin.sql
--
--   # XAMPP/Laragon:
--   mysql -u root portfolio_hub < database/migrations/2026-10-tambah-kolom-superadmin.sql

USE portfolio_hub;

-- studio_projects.is_hidden ------------------------------------------------
SET @ddl = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE studio_projects ADD COLUMN is_hidden TINYINT(1) NOT NULL DEFAULT 0 AFTER featured',
        'DO 0'
    )
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'studio_projects'
      AND column_name = 'is_hidden'
);
PREPARE stmt FROM @ddl;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- users.is_active ----------------------------------------------------------
SET @ddl = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER bio',
        'DO 0'
    )
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'users'
      AND column_name = 'is_active'
);
PREPARE stmt FROM @ddl;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
