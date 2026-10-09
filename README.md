# PortfolioHub

Landing page portofolio kreatif yang dibuat dengan PHP native, MySQL, CSS, dan JavaScript. Proyek ini tidak memakai framework, Composer, atau Node.js sehingga mudah dijalankan di XAMPP, Laragon, maupun Docker.

Data kartu karya pada halaman utama disimpan di database MySQL. Data tersebut dapat dikelola dari halaman admin atau langsung melalui phpMyAdmin.

## Fitur

- Landing page responsif dengan hero, koleksi karya, profil kreator, dan call to action
- Data karya dinamis dari tabel MySQL `portfolio_items`
- Dashboard admin untuk tambah, ubah, dan hapus karya
- Filter kategori dan pencarian karya pada landing page
- Tema terang/gelap dan menu navigasi untuk perangkat kecil
- Koneksi database menggunakan PDO serta prepared statement
- Proteksi CSRF pada formulir admin
- phpMyAdmin tersedia otomatis pada konfigurasi Docker
- Gambar demo berada secara lokal di `assets/images/`

## Struktur folder

```text
portohub/
├── index.php                         Halaman utama
├── koneksi.php                       Satu titik koneksi PDO ke MySQL
├── admin/
│   └── index.php                     Login dan dashboard admin
├── app/
│   ├── bootstrap.php                 Memuat konfigurasi dan koneksi aplikasi
│   ├── config/
│   │   ├── admin.php                 Kredensial dashboard admin
│   │   ├── database.php              Konfigurasi koneksi MySQL
│   │   └── site.php                  Nama dan informasi situs
│   ├── core/
│   │   ├── Database.php              Pembuat koneksi PDO
│   │   └── helpers.php               Fungsi bantuan umum
│   └── repositories/
│       └── PortfolioRepository.php   Semua query karya portofolio
├── assets/
│   ├── css/
│   │   ├── style.css                 Style landing page
│   │   └── admin.css                 Style dashboard admin
│   ├── images/                       Gambar SVG demo
│   └── js/app.js                     Interaksi landing page
├── database/
│   └── portfoliohub.sql              Tabel dan data awal MySQL
├── views/
│   ├── admin/                        Tampilan login dan dashboard
│   ├── layout/                       Header dan footer landing page
│   └── sections/                     Bagian-bagian landing page
├── Dockerfile
└── docker-compose.yml
```

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

File SQL akan membuat database `portfolio_hub`, tabel `portfolio_items`, serta enam data contoh.

Nama database pada XAMPP dan Laragon sudah otomatis diarahkan ke `portfolio_hub`. Setelah import, Anda tidak perlu mengubah `app/config/database.php` maupun membuat file `.env` selama MySQL lokal memakai konfigurasi standar `root` tanpa kata sandi.

### 4. Buka proyek

- Landing page: `http://localhost/portohub/`
- Dashboard admin: `http://localhost/portohub/admin/`

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

Import tersebut membuat database dengan nama `portfolio_hub`, yang sudah sama dengan konfigurasi default aplikasi.

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

Database awal diimpor otomatis ketika volume MySQL dibuat pertama kali. Untuk menghentikan container gunakan:

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

File `koneksi.php` adalah pintu utama koneksi database yang dapat dipakai dari file PHP baru. File ini membaca `.env`, memakai PDO, dan menampilkan pesan yang jelas bila MySQL atau database belum siap.

Contoh pemakaian dari file PHP baru:

```php
<?php

require_once __DIR__ . '/koneksi.php';

$query = $koneksi->query('SELECT * FROM portfolio_items');
$semuaKarya = $query->fetchAll();
```

Gunakan prepared statement untuk query yang menerima data dari formulir pengguna.

### Menguji koneksi di browser

Gunakan file `koneksi.php` langsung. File ini memakai `echo` untuk menampilkan hasil test **hanya jika dibuka langsung dari browser**. Saat file dipanggil menggunakan `require_once` oleh halaman aplikasi, tidak ada output test yang ditampilkan.

- XAMPP: `http://localhost/portohub/koneksi.php`
- Laragon: `http://localhost/portohub/koneksi.php` atau `http://portohub.test/koneksi.php`
- Docker: `http://localhost:8088/koneksi.php`

Halaman akan menampilkan pesan **Koneksi berhasil**, nama database aktif `portfolio_hub`, serta jumlah data karya. Untuk production, sebaiknya batasi akses ke file ini melalui konfigurasi Apache atau hapus mode test setelah aplikasi selesai diperiksa.

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
3. Pada dashboard, masukkan `karya-baru.jpg` ke kolom **Nama file gambar**.
4. Simpan karya.

Format gambar seperti SVG, JPG, PNG, dan WebP dapat digunakan selama browser mendukungnya.

## Mengubah tampilan

- `assets/css/style.css` untuk tampilan landing page
- `assets/css/admin.css` untuk tampilan dashboard admin
- `assets/js/app.js` untuk filter, pencarian, tema, dan navigasi mobile
- `views/sections/` untuk teks dan struktur setiap bagian landing page
- `app/config/site.php` untuk nama situs dan informasi dasar
