# Arsitektur Aplikasi

PortfolioHub memakai PHP native dan MySQL. Tidak ada framework atau Composer;
hal ini membuat proyek ringan, tetapi disiplin pemisahan tanggung jawab tetap
penting supaya kode tidak berubah menjadi satu file besar.

## Dua sisi aplikasi

Aplikasi memisahkan **route** (yang diakses browser) dari **backend** dan
**tampilan**:

- **`public/`** — satu-satunya folder yang diakses browser (web root). Berisi
  berkas route tipis: landing, login, studio, dan portofolio.
- **`app/`** — seluruh kelas backend (controller, repository, service,
  middleware, helper).
- **`views/`** — tampilan dipisah per audiens: `public/` (end-user),
  `creator/` (creator studio), `superadmin/`, dan `layout/`.

Karena backend ada di luar `public/`, berkas seperti `app/bootstrap.php` atau
`database/*.sql` tidak bisa dibuka langsung dari browser.

- **End-user (publik):** landing page, halaman masuk/daftar, dan portofolio
  publik. Tampilannya ada di `views/public/` dan `views/layout/`.
- **Creator studio (backend):** dashboard, proyek, media, dan pengaturan.
  Tampilannya ada di `views/creator/`. Superadmin ada di `views/superadmin/`.

## Struktur inti

```text
public/             WEB ROOT — hanya folder ini yang diakses browser
├── .htaccess       Rewrite URL cantik + alias route
├── index.php       Landing page
├── login/          Masuk, register, dan superadmin
├── studio/         Route creator studio (files tipis)
└── portfolio/      Portofolio publik satu creator

app/                Backend (tidak diakses browser)
├── bootstrap.php   Titik muat: session, helper, koneksi, repository, middleware
├── config/         Konfigurasi database, admin, media, dan situs
├── controllers/    Orkestrasi request creator dan auth
├── core/           Koneksi, JSON validator, dan helper per topik
│   └── helpers/    Helper dipisah: env, output, csrf, url, flash, auth, format
├── data/           Kontrak validasi form dalam JSON
├── middleware/     AuthMiddleware: pintu tunggal validasi login creator
├── repositories/   Semua query dan akses database
└── services/       Layanan reusable: autentikasi dan upload media

views/              Tampilan (tidak diakses browser)
├── layout/         Header dan footer landing page
├── public/         Tampilan end-user
│   ├── auth/       Template login dan register
│   ├── portfolio.php
│   └── sections/   Bagian-bagian landing page
├── creator/        Tampilan creator studio dan layout bersama
└── superadmin/     Tampilan monitoring terpisah

assets/             Aset statis (dipetakan ke URL /assets/...)
├── css/base/       Token warna, reset, dan tipografi
├── css/components/ Tombol, form, kartu, alert
├── css/layout/     Header, footer, kerangka studio
├── css/pages/      CSS per halaman
├── css/auth/       CSS auth yang sengaja masih polos
├── js/             Interaksi kecil di browser
└── uploads/        Berkas media creator; tidak untuk source code
```

## Alur request creator studio

1. File route seperti `public/studio/projects.php` hanya menyiapkan dependensi
   dan data halaman.
2. Baris paling atas memanggil `requireCreatorLogin()` yang didelegasikan ke
   `AuthMiddleware::requireSessionUserId()`.
3. `StudioController` menangani keputusan request POST: simpan/hapus proyek,
   upload/hapus media, atau perubahan profil.
4. Controller memanggil `StudioRepository` untuk query data.
5. `StudioRepository` adalah satu tempat untuk SQL tabel creator (`users`,
   `studio_projects`, dan `media_assets`).
6. Setelah selesai, route merender view di `views/creator/`.

Jadi, aturan praktisnya cukup sederhana: **jangan taruh SQL di view**, dan
**jangan taruh markup HTML besar di controller**.

## Bootstrap

`app/bootstrap.php` adalah entry point internal. File ini:

- memastikan session aktif dengan cookie `HttpOnly` dan `SameSite=Lax`;
- memuat helper, konfigurasi, dan kelas yang digunakan aplikasi;
- membaca `.env` lalu membuka koneksi PDO melalui `App\Core\Connection`;
- membuat repository utama;
- menyiapkan `AuthMiddleware` dengan `StudioRepository`;
- memeriksa keberadaan tabel creator dengan
  `StudioRepository::hasRequiredSchema()`.

Pemeriksaan tersebut hanya membaca `information_schema`. Kode PHP **tidak lagi
membuat tabel**. Semua DDL berada di folder `database/` supaya struktur
database mudah ditinjau dan versinya terlihat jelas:

- `database/portfoliohub.sql` — tabel landing page dan data contoh;
- `database/creator-studio.sql` — tabel creator studio (users, studio_projects, media_assets);
- `database/migrations/` — ALTER tambahan untuk instalasi yang sudah ada, misalnya kolom CV.

Bila tabel creator belum ada, aplikasi menghentikan request dengan status
`503` dan pesan agar file `database/creator-studio.sql` diimport lebih dulu.

## Perubahan struktur database

Karena DDL tidak dijalankan otomatis oleh PHP, setiap perubahan skema harus
dilakukan dengan rapi:

1. Ubah file di `database/`.
2. Tambahkan file migrasi baru bila instalasi lama tetap harus didukung,
   misalnya `database/migrations/2026-02-tambah-kolom.sql`.
3. Sesuaikan repository setelah skema berubah.

Dengan pola ini, database menjadi sumber kebenaran yang eksplisit, dan kode
aplikasi hanya perlu memastikan tabelnya sudah ada sebelum digunakan.

## Struktur CSS

CSS memakai satu lapisan bersama lalu CSS spesifik per area. Setiap berkas
ditulis satu deklarasi per baris agar mudah dibaca.

**Lapisan bersama (`assets/css/app.css`)**

- `base/tokens.css` — variabel warna, tipografi, dan ukuran;
- `base/reset.css` — reset minimal dan aksesibilitas dasar;
- `base/typography.css` — heading, eyebrow, dan container;
- `components/` — tombol, form, kartu panel, tabel, alert, empty state;
- `layout/header.css` dan `layout/footer.css` — kerangka landing page.

**Area publik (`assets/css/public.css`)**

- `pages/landing/hero.css`, `work-grid.css`, `creator-feature.css`, `cta.css`;
- `pages/public-portfolio.css`.

**Creator studio (`assets/css/studio.css`)**

- `layout/studio-shell.css` — sidebar, topbar, dan area konten;
- `pages/creator/` — dashboard, projects, media, settings, analytics, responsive;
- `pages/superadmin.css` — monitoring superadmin.

Saat menambahkan tampilan baru, pilih berkas berdasarkan tanggung jawabnya.
Contohnya, badge yang dipakai lebih dari satu halaman masuk `components/`;
aturan yang khusus halaman media masuk `pages/creator/media.css`.
