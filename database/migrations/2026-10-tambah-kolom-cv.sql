-- PortfolioHub migration: kolom CV pada tabel users.
--
-- Jalankan sekali pada database yang SUDAH ada (hasil import creator-studio.sql
-- versi lama) sebelum memakai fitur CV. Instalasi baru tidak perlu file ini
-- karena creator-studio.sql sudah memuat kolomnya.
--
--   mysql -u root portfolio_hub < database/migrations/2026-10-tambah-kolom-cv.sql

USE portfolio_hub;

ALTER TABLE users
    ADD COLUMN cv_filename VARCHAR(255) NULL AFTER avatar_filename,
    ADD COLUMN cv_original_name VARCHAR(255) NULL AFTER cv_filename,
    ADD COLUMN cv_uploaded_at TIMESTAMP NULL DEFAULT NULL AFTER cv_original_name;
