-- PortfolioHub migration: kolom untuk fitur superadmin.
--
-- Menambahkan penanda status akun (users.is_active) dan penanda proyek yang
-- disembunyikan pengelola (studio_projects.is_hidden). Jalankan sekali pada
-- database yang SUDAH ada sebelum memakai fitur superadmin. Instalasi baru
-- tidak perlu file ini karena creator-studio.sql sudah memuat kolomnya.
--
--   mysql -u root portfolio_hub < database/migrations/2026-10-tambah-kolom-superadmin.sql

USE portfolio_hub;

ALTER TABLE users
    ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER bio;

ALTER TABLE studio_projects
    ADD COLUMN is_hidden TINYINT(1) NOT NULL DEFAULT 0 AFTER featured;
