# PortfolioHub

Landing page portofolio kreatif yang dibuat dengan PHP native, MySQL, CSS, dan JavaScript. Proyek ini tidak memakai framework, Composer, atau Node.js sehingga mudah dijalankan di XAMPP, Laragon, maupun Docker.

Data kartu karya pada halaman utama diambil dari proyek creator yang sudah berstatus **published**. Bila belum ada karya yang dipublikasikan, halaman utama menampilkan ajakan "Become The One".

## Fitur

- Landing page responsif dengan hero, koleksi karya, profil kreator, dan call to action
- Grid karya dinamis dari proyek creator yang published (`studio_projects`)
- Halaman eksplorasi `/explore/` untuk menjelajah semua karya published, dengan pencarian proyek **dan** creator serta filter kategori (semuanya di sisi server sehingga URL bisa dibagikan dan tetap jalan tanpa JavaScript)
- Halaman detail proyek `/project/{id}/` berisi galeri media (gambar, GIF, video, PDF), info creator, dan karya lain dari creator yang sama
- Creator studio untuk membuat, mengubah, dan menghapus proyek beserta medianya
- Filter kategori dan pencarian karya pada landing page
- Tema terang/gelap dan menu navigasi untuk perangkat kecil
- Koneksi database menggunakan PDO serta prepared statement
- Proteksi CSRF pada formulir admin
- phpMyAdmin tersedia otomatis pada konfigurasi Docker
- Gambar demo berada secara lokal di `assets/images/`

## Struktur folder

Semua file yang diakses browser berada di dalam satu folder `public/` (web root).
Backend tinggal di `app/`, tampilan dipisah per audiens di `views/`, dan CSS
modular ada di `assets/css/` dengan lapisan base, components, layout, dan pages.

```text
portohub/
├── public/                           WEB ROOT — hanya folder ini yang diakses browser
│   ├── .htaccess                     Rewrite URL cantik + alias route
│   ├── index.php                     Landing page (end-user)
│   ├── login/
│   │   ├── index.php                 Masuk creator
│   │   ├── register/index.php        Daftar creator
│   │   └── superadmin/index.php      Masuk superadmin
│   ├── studio/                       Halaman creator (backend)
│   │   ├── index.php                 Dashboard
│   │   ├── projects.php              CRUD proyek + galeri media
│   │   ├── settings.php              Profil publik + CV
│   │   ├── cv.php                    Preview/unduh CV milik sendiri
│   │   ├── analytics.php             Ringkasan
│   │   └── logout.php
│   ├── explore/index.php             Eksplorasi karya + pencarian & filter
│   ├── project/index.php             Detail satu proyek + galeri media
│   └── portfolio/index.php           Portofolio publik satu creator
├── app/                              Backend aplikasi (tidak diakses browser)
│   ├── bootstrap.php                 Titik muat: session, helper, koneksi, repo
│   ├── config/
│   │   ├── admin.php                 Kredensial superadmin
│   │   ├── database.php              Konfigurasi koneksi MySQL
│   │   ├── media.php                 Aturan upload media dan CV
│   │   └── site.php                  Nama dan informasi situs
│   ├── core/
│   │   ├── Connection.php            Membuka koneksi PDO aplikasi
│   │   ├── Database.php              Pembuat koneksi PDO (singleton)
│   │   ├── JsonFormValidator.php     Validasi form berbasis JSON schema
│   │   ├── helpers.php               Memuat seluruh helper di bawah
│   │   └── helpers/                  Helper dipisah per topik
│   │       ├── env.php               Baca berkas .env
│   │       ├── output.php            escape() untuk HTML
│   │       ├── csrf.php              Token CSRF form POST
│   │       ├── url.php               redirect() dan appUrl()
│   │       ├── flash.php             Pesan flash sekali tampil
│   │       ├── auth.php              Status login tingkat halaman
│   │       └── format.php            Format angka tampilan
│   ├── middleware/
│   │   └── AuthMiddleware.php        Pintu tunggal validasi login creator
│   ├── repositories/                 Semua query database
│   │   ├── PortfolioRepository.php   Karya landing page
│   │   └── StudioRepository.php      Semua data creator studio
│   ├── services/
│   │   ├── AuthenticationService.php Login dan pembuatan session
│   │   └── MediaUploadService.php    Validasi dan simpan upload
│   ├── controllers/
│   │   ├── AuthController.php        Alur login dan registrasi
│   │   └── StudioController.php      Alur proyek, media, dan profil
│   └── data/form-schemas.json        Kontrak validasi form
├── views/                            Tampilan per audiens (tidak diakses browser)
│   ├── layout/                       Header dan footer landing page
│   ├── public/
│   │   ├── portfolio.php             Tampilan portofolio publik
│   │   ├── explore.php               Tampilan halaman eksplorasi
│   │   ├── project.php               Tampilan detail proyek
│   │   ├── not-found.php             Tampilan 404 halaman publik
│   │   ├── auth/                     Tampilan login dan register
│   │   └── sections/                 Bagian-bagian landing page
│   ├── creator/                      Tampilan creator studio
│   └── superadmin/                   Tampilan monitoring superadmin
├── assets/
│   ├── css/
│   │   ├── app.css                   Lapisan bersama: base + components + layout
│   │   ├── public.css                CSS area publik (end-user)
│   │   ├── studio.css                CSS creator studio dan superadmin
│   │   ├── base/                     Token, reset, tipografi
│   │   ├── components/               Tombol, form, kartu, alert, lightbox
│   │   ├── layout/                   Header, footer, kerangka studio
│   │   ├── pages/                    CSS per halaman
│   │   └── auth/                     CSS halaman login dan register (sengaja polos)
│   ├── images/                       Gambar SVG demo
│   └── js/                           Interaksi landing page dan studio
├── database/
│   ├── portfoliohub.sql              Tabel landing page dan data contoh
│   ├── creator-studio.sql            Tabel creator studio (schema penuh)
│   └── migrations/                   ALTER tambahan untuk instalasi lama
├── docs/                             Dokumentasi Bahasa Indonesia
├── Dockerfile
└── docker-compose.yml
```

### Di mana letak backend dan tampilan?

- **Route / URL (web root):** semua file yang diakses browser ada di `public/`.
  Contoh `public/studio/projects.php` melayani `/studio/projects.php`.
- **End-user (pengunjung):** tampilan di `views/public/` + `views/layout/`,
  CSS di `public.css`, logika data lewat `StudioRepository`.
- **Creator studio (backend):** tampilan di `views/creator/`, CSS di
  `studio.css`, logika lewat `StudioController` + `StudioRepository`.
- **Auth:** alur di `AuthController` + `AuthenticationService`, tampilan di
  `views/public/auth/`.
- **Superadmin:** tampilan di `views/superadmin/`.

Semua kelas backend berada di `app/` dan dimuat sekali lewat `app/bootstrap.php`,
jadi setiap halaman cukup `require` bootstrap satu kali. Karena backend ada di
luar `public/`, file seperti `app/bootstrap.php` atau `database/*.sql` tidak
bisa dibuka langsung dari browser.

### Kenapa ada `public/studio/` dan `views/creator/`?

Keduanya bukan duplikat; tugasnya berbeda:

| Lokasi | Isi | Tugas |
| --- | --- | --- |
| `public/studio/` | `projects.php`, `cv.php`, dst. | **URL / route.** File tipis: cek login, ambil data, lalu `require` view. |
| `views/creator/` | `projects.php`, `settings.php`, dst. | **Markup HTML** halaman studio. |
| `assets/css/pages/creator/` | `projects.css`, dst. | **Gaya** khusus tiap halaman studio. |

Folder URL tetap bernama `studio/` karena menentukan alamat `/studio/...`,
sedangkan view dan CSS memakai nama `creator/` supaya perannya jelas dan tidak
tertukar dengan folder URL.

### Kenapa `assets/` ada di luar `public/`?

Folder `assets/` (CSS, JS, gambar, dan upload) sengaja tidak dipindah ke dalam
`public/` agar struktur repo tetap rapi. Agar tetap dapat diakses browser,
konfigurasi server memetakan URL `/assets/...` ke folder tersebut:

- **Docker/Apache:** file `docker/assets-alias.conf` mengaktifkan `Alias /assets`.
- **XAMPP/Laragon:** tambahkan alias serupa pada konfigurasi Apache Anda, atau
  pindahkan folder `assets/` ke dalam `public/`.

## Kebutuhan sistem

- PHP 8.1 atau lebih baru
- Ekstensi PHP `pdo_mysql`
- MySQL 8.0 atau MariaDB 10.4 atau lebih baru
- Apache dari XAMPP atau Laragon, atau Docker Desktop

## Penjelasan MySQL, phpMyAdmin, dan file SQL

phpMyAdmin serta MySQL **tidak hanya digunakan oleh Docker**.

- **XAMPP** sudah menyediakan Apache, MySQL, dan phpMyAdmin dari Control Panel.
- **Laragon** juga menyediakan MySQL dan menu Database/phpMyAdmin.
- **Docker** menjalankan MySQL dan phpMyAdmin sebagai container terpisah secara otomatis.

File `database/portfoliohub.sql` dipakai oleh semua cara instalasi untuk membuat database `portfolio_hub`, tabel `portfolio_items`, dan data contoh.

- Pada **XAMPP/Laragon**, import file SQL satu kali menggunakan phpMyAdmin lokal.
- Pada **Docker**, file tersebut diimport otomatis hanya saat volume database Docker pertama kali dibuat.

## File konfigurasi `.env`

Proyek menyediakan `.env.example` sebagai contoh konfigurasi. Salin menjadi `.env` apabila Anda ingin mengganti koneksi database atau kredensial admin tanpa mengubah file PHP.

```bash
cp .env.example .env
```

Pada Windows, buat salinan `.env.example` lalu ubah namanya menjadi `.env`.

File `.env` sudah masuk `.gitignore`, sehingga tidak akan ikut terkirim ke repository. Bila file `.env` tidak ada, aplikasi memakai nilai bawaan yang sesuai dengan XAMPP/Laragon standar.

## Menjalankan dengan XAMPP

### 1. Salin folder proyek

Salin folder `portohub` ke dalam folder `htdocs` XAMPP.

```text
C:\xampp\htdocs\portohub
```

Untuk Linux XAMPP, lokasi umumnya seperti berikut.

```text
/opt/lampp/htdocs/portohub
```

### 2. Nyalakan layanan

Di XAMPP Control Panel, jalankan Apache dan MySQL.

### 3. Buat database melalui phpMyAdmin

1. Buka `http://localhost/phpmyadmin`.
2. Pilih menu **Import**.
3. Pilih file `database/portfoliohub.sql` dari folder proyek.
4. Klik **Import** atau **Go**.
5. Ulangi langkah import untuk file `database/creator-studio.sql`.

File SQL pertama membuat database `portfolio_hub`, tabel `portfolio_items`, serta enam data contoh. File SQL kedua membuat tabel creator studio: `users`, `studio_projects`, dan `media_assets`, termasuk kolom CV pada `users`. Jika database sudah ada dari versi sebelum fitur CV, jalankan sekali migrasi di `database/migrations/`.

Nama database pada XAMPP dan Laragon sudah otomatis diarahkan ke `portfolio_hub`. Setelah import, Anda tidak perlu mengubah `app/config/database.php` maupun membuat file `.env` selama MySQL lokal memakai konfigurasi standar `root` tanpa kata sandi.

### 4. Arahkan web root ke folder public

Agar kode backend tetap aman, Apache harus melayani folder `public/`, bukan
folder proyek. Ada dua cara:

**Cara A — Virtual Host (disarankan)**

Tambahkan konfigurasi berikut di `httpd-vhosts.conf` XAMPP lalu restart Apache.

```apache
<VirtualHost *:80>
    ServerName portohub.test
    DocumentRoot "C:/xampp/htdocs/portohub/public"

    <Directory "C:/xampp/htdocs/portohub/public">
        AllowOverride All
        Require all granted
    </Directory>

    # Aset berada di luar public/, jadi dipetakan lewat alias.
    Alias /assets "C:/xampp/htdocs/portohub/assets"
    <Directory "C:/xampp/htdocs/portohub/assets">
        Require all granted
        Options -Indexes
    </Directory>
</VirtualHost>
```

Tambahkan `127.0.0.1 portohub.test` ke file `hosts` bila perlu, lalu buka
`http://portohub.test/`.

**Cara B — mod_rewrite dari root proyek**

Cukup buka `http://localhost/portohub/`; file `.htaccess` di root proyek
mengarahkan semua permintaan ke `public/`. Cara ini praktis tetapi kode
backend ikut berada di dalam web root, jadi kurang aman untuk produksi.

### 5. Buka proyek

- Landing page: `http://localhost/portohub/` (Cara B) atau `http://portohub.test/` (Cara A)
- Creator studio: `/studio/` setelah masuk creator
- Monitoring superadmin: `/login/superadmin/`

Konfigurasi bawaan XAMPP memakai host `127.0.0.1`, pengguna `root`, dan kata sandi kosong. Nilai itu sudah tersedia di `app/config/database.php`. Bila konfigurasi MySQL Anda berbeda, salin `.env.example` menjadi `.env` lalu sesuaikan nilai `DB_*`.

## Menjalankan dengan Laragon

### 1. Salin folder proyek

Salin proyek ke folder `www` Laragon.

```text
C:\laragon\www\portohub
```

### 2. Nyalakan layanan dan impor database

1. Klik **Start All** di Laragon.
2. Buka menu **Database** atau phpMyAdmin dari Laragon.
3. Import file `database/portfoliohub.sql`.
4. Import file `database/creator-studio.sql`.

Import tersebut membuat database dengan nama `portfolio_hub`, yang sudah sama dengan konfigurasi default aplikasi, beserta seluruh tabel creator studio.

### 3. Buka proyek

Gunakan salah satu alamat berikut.

- `http://localhost/portohub/`
- `http://portohub.test/` apabila Auto Virtual Hosts Laragon aktif

Dashboard admin berada di `/admin/`, misalnya `http://portohub.test/admin/`.

Laragon standar juga biasanya menggunakan pengguna MySQL `root` tanpa kata sandi. Gunakan file `.env` bila konfigurasi Laragon Anda berbeda.

## Menjalankan dengan Docker

Docker menjalankan tiga layanan: PHP Apache, MySQL, dan phpMyAdmin.

### 1. Jalankan container

Jalankan perintah ini dari folder proyek.

```bash
docker compose up --build
```

### 2. Buka alamat layanan

| Layanan | Alamat |
| --- | --- |
| Landing page | `http://localhost:8088` |
| Dashboard admin | `http://localhost:8088/admin/` |
| phpMyAdmin | `http://localhost:8082` |

Untuk masuk ke phpMyAdmin Docker, gunakan:

```text
Server: database
Username: root
Password: root_password_change_me
```

Database awal diimpor otomatis ketika volume MySQL dibuat pertama kali dari `database/portfoliohub.sql` dan `database/creator-studio.sql`. Untuk menghentikan container gunakan:

```bash
docker compose down
```

Untuk menghapus database Docker dan membuat ulang data awal, jalankan:

```bash
docker compose down -v
docker compose up --build
```

Docker Compose membaca nilai dari file `.env` jika file itu ada. Sebelum menjalankan proyek pada server publik, buat `.env` dari `.env.example` lalu ganti `MYSQL_ROOT_PASSWORD`, `MYSQL_PASSWORD`, dan `ADMIN_PASSWORD`.

## Dashboard admin

Alamat dashboard adalah `/admin/`.

Kredensial bawaan:

```text
Nama pengguna: admin
Kata sandi: admin123
```

Ubah kredensial tersebut sebelum publikasi. Untuk XAMPP atau Laragon, edit nilai `username` dan `password` dalam `app/config/admin.php`. Untuk Docker, ubah `ADMIN_USERNAME` dan `ADMIN_PASSWORD` dalam `docker-compose.yml`.

Melalui dashboard, admin dapat:

- Menambah karya baru
- Mengubah judul, kreator, kategori, angka suka, angka dilihat, urutan, dan status unggulan
- Menghapus karya
- Menentukan gambar dengan memasukkan nama file yang tersimpan di `assets/images/`

## Creator accounts and studio

- Sign in: `/login/`
- Register: `/login/register/`
- Creator studio: `/studio/` (requires a creator account)
- Superadmin monitoring: `/login/superadmin/` (separate monitoring screen without the creator sidebar)

Creator projects, profiles, and uploads are stored in `users`, `studio_projects`, and `media_assets`. All creator-studio tables live in `database/creator-studio.sql`; the application checks at startup that they exist and never creates them by itself.

Account, profile, and project fields are validated on the server using the JSON contracts in `app/data/form-schemas.json`. Project media is uploaded from each project panel and collected in a media gallery on the Projects page; uploads validate MIME types server-side and accept JPEG, PNG, WebP, GIF, MP4, WebM, and PDF, up to 25 MB.

A creator's CV is a single PDF managed in User settings (`/studio/settings.php`). It is validated (PDF only, up to 10 MB) and stored outside the web root in `storage/cv`, then previewed or downloaded through `/studio/cv.php`, which only serves the signed-in owner and restores the original filename on download.

The whole interface — landing page, studio, auth, and monitoring — uses the DM Sans typeface.

The sign-in and registration pages intentionally remain unstyled beyond that font. Their clean modular CSS locations are `assets/css/auth/login.css` and `assets/css/auth/register.css`.

## Demo kelas dengan ngrok

Untuk memperlihatkan aplikasi langsung ke kelas tanpa deploy, jalankan:

```bash
./demo.sh
```

Skrip tersebut menyalakan Docker lalu membuka tunnel ngrok. Bagikan URL HTTPS yang muncul. Selengkapnya ada di `docs/demo-ngrok.md`.

## Mengelola data dengan phpMyAdmin

Selain dashboard, tabel `portfolio_items` dapat diubah dari phpMyAdmin.

Kolom penting tabel:

| Kolom | Keterangan |
| --- | --- |
| `title` | Judul karya |
| `creator_name` | Nama kreator |
| `category` | Kategori untuk filter |
| `image_filename` | Nama file gambar dari `assets/images/` |
| `likes_count` | Jumlah suka dalam angka |
| `views_count` | Jumlah dilihat dalam angka |
| `is_featured` | Isi `1` untuk kartu unggulan, `0` untuk reguler |
| `display_order` | Angka urutan, nilai kecil tampil lebih dulu |

Setelah data diubah, segarkan halaman landing page untuk melihat hasilnya.

## Konfigurasi database lokal

Koneksi database dibuka sekali di `app/bootstrap.php` melalui `App\Core\Connection`. Setelah bootstrap dimuat, variabel `$koneksi` (alias `$pdo`) berisi objek PDO yang siap dipakai.

Contoh pemakaian dari file PHP baru:

```php
<?php

require_once __DIR__ . '/app/bootstrap.php';

$query = $koneksi->query('SELECT * FROM portfolio_items');
$semuaKarya = $query->fetchAll();
```

Gunakan prepared statement untuk query yang menerima data dari formulir pengguna.

### Menguji koneksi database

Tidak ada lagi halaman test di browser. Untuk memastikan koneksi MySQL berjalan, cukup buka halaman utama aplikasi. Bila database belum siap, `Connection` menampilkan pesan yang jelas dan mencatat detailnya ke `error_log`.

Koneksi lokal dikelola dalam `app/config/database.php`.

```php
'host' => environmentValue('DB_HOST', '127.0.0.1'),
'port' => environmentValue('DB_PORT', '3306'),
'name' => environmentValue('DB_NAME', 'portfolio_hub'),
'username' => environmentValue('DB_USER', 'root'),
'password' => environmentValue('DB_PASSWORD', ''),
```

Ubah nilai default melalui file `.env` bila MySQL lokal memakai nama database, pengguna, atau kata sandi yang berbeda. Untuk keamanan, jangan menyimpan kredensial produksi di repository publik.

## Menambah gambar karya

1. Simpan gambar baru di folder `assets/images/`.
2. Gunakan nama file sederhana, contoh `karya-baru.jpg`.
3. Unggah sebagai media pada proyek creator di studio, atau kelola lewat phpMyAdmin.
4. Simpan karya.

Format gambar seperti SVG, JPG, PNG, dan WebP dapat digunakan selama browser mendukungnya.

## Mengubah tampilan

CSS dipisah rapi per lapisan:

- `assets/css/app.css` memuat token, reset, tipografi, komponen, dan layout bersama.
- `assets/css/public.css` untuk area publik (landing dan portofolio).
- `assets/css/studio.css` untuk creator studio dan superadmin.
- `assets/css/base/`, `components/`, `layout/`, dan `pages/` berisi berkas per topik.
- `assets/js/app.js` untuk filter, pencarian, tema, dan navigasi mobile.
- `views/public/sections/` untuk teks dan struktur setiap bagian landing page.
- `assets/css/base/tokens.css` untuk warna dan tipografi.
- `app/config/site.php` untuk nama situs dan informasi dasar.
