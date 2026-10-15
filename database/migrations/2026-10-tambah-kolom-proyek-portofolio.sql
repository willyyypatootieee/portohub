-- PortfolioHub migration: kolom pengelolaan proyek portofolio.
--
-- Menambahkan teknologi yang dipakai, tautan demo & repository, serta urutan
-- tampil proyek. Jalankan sekali pada database yang SUDAH ada (hasil import
-- creator-studio.sql versi lama). Instalasi baru tidak perlu file ini karena
-- creator-studio.sql sudah memuat kolomnya.
--
--   mysql -u root portfolio_hub < database/migrations/2026-10-tambah-kolom-proyek-portofolio.sql

USE portfolio_hub;

ALTER TABLE studio_projects
    ADD COLUMN technologies VARCHAR(255) NOT NULL DEFAULT '' AFTER description,
    ADD COLUMN demo_url VARCHAR(255) NULL AFTER technologies,
    ADD COLUMN repo_url VARCHAR(255) NULL AFTER demo_url,
    ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER featured,
    ADD INDEX idx_studio_projects_order (user_id, sort_order);
