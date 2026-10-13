# Panduan Pengembangan dan Maintenance

## Menjalankan aplikasi

### Docker

```bash
docker compose up --build
```

- Landing: `http://localhost:8088/`
- Studio: `http://localhost:8088/studio/`
- phpMyAdmin: `http://localhost:8082/`

### XAMPP atau Laragon

1. Import `database/portfoliohub.sql`.
2. Import `database/creator-studio.sql`.
3. Pastikan nilai database pada `.env` atau `app/config/database.php` sesuai.
4. Buka direktori proyek melalui localhost atau virtual host Laragon.

### Demo kelas dengan ngrok

Untuk memperlihatkan aplikasi ke kelas tanpa deploy, lihat
[Demo kelas dengan ngrok](demo-ngrok.md). Cara tercepat:

```bash
./demo.sh
```

## Konvensi kode

Supaya tetap nyaman dibaca, gunakan beberapa aturan berikut.

- Pakai `declare(strict_types=1);` di setiap file PHP baru.
- Gunakan empat spasi untuk indentasi PHP dan HTML.
- Hindari statement panjang satu baris, terutama `if`, query SQL, dan markup.
- Berikan nama yang menjelaskan tujuan: `$availableFilters` lebih baik dari
  `$filters`, `uploadMedia()` lebih baik dari `upload()`.
- Route boleh tipis. Pindahkan logika POST ke controller atau service.
- Query SQL baru hanya ditaruh dalam repository.
- Gunakan `escape()` untuk output dari database atau request.
- Selalu gunakan prepared statement jika query menerima input pengguna.

## Konfigurasi Apache dan batas upload

Proyek menyertakan `public/.htaccess` agar alias URL seperti `/login/`,
`/studio/`, dan `/portfolio/{username}/` bekerja di Apache XAMPP
atau Laragon. DocumentRoot Apache harus diarahkan ke folder `public/`. Bila
masih muncul halaman 404, cek tiga hal berikut:

1. `DocumentRoot` menunjuk ke folder `public/`.
2. `mod_rewrite` Apache aktif.
3. Konfigurasi Apache untuk folder proyek mengizinkan `AllowOverride All`.

Folder `assets/` berada di luar `public/`, jadi perlu alias. Konfigurasi
contoh ada di `docker/assets-alias.conf` (Docker) dan README (XAMPP/Laragon).

Untuk kemudahan, `.htaccess` di root proyek mengarahkan semua permintaan ke
`public/` sehingga `http://localhost/portohub/` tetap bisa dibuka tanpa vhost.
Cara ini praktis untuk pengembangan lokal, tetapi untuk produksi sebaiknya
DocumentRoot diarahkan langsung ke `public/`.

Untuk upload hingga 25 MB, proyek juga menyertakan `.user.ini`. Pada beberapa
instalasi XAMPP yang memakai Apache module PHP, `.user.ini` tidak dibaca. Ubah
`upload_max_filesize` dan `post_max_size` pada `php.ini`, kemudian restart
Apache. Berkas `php/uploads.ini` digunakan otomatis oleh Docker.

## Menambah form baru

1. Tambahkan schema di `app/data/form-schemas.json`.
2. Panggil `JsonFormValidator` dari controller yang relevan.
3. Validasi aturan bisnis tambahan di controller, misalnya cek username unik.
4. Tampilkan pesan error per field di view.

Contoh schema singkat:

```json
{
  "contact": {
    "fields": {
      "subject": {
        "label": "Subjek",
        "required": true,
        "minLength": 4,
        "maxLength": 120
      }
    }
  }
}
```

## Menambah halaman studio

1. Buat route tipis di `public/studio/nama-halaman.php`.
2. Lindungi halaman dengan `requireCreatorLogin()`.
3. Ambil data menggunakan `StudioRepository`, atau tambahkan method repository
   yang khusus dan bernama jelas.
4. Buat view di `views/creator/` dan gunakan `layout.php` / `layout-end.php`.
5. Tambahkan item navigasi di `views/creator/layout.php` bila halaman harus
   terlihat di sidebar.
6. Tambahkan CSS pada modul yang tepat, bukan menumpuk semuanya di satu file.

## Menambah kode baru: taruh di mana?

- **Query database** → tambahkan method di `app/repositories/`.
- **Logika request (POST, redirect)** → `app/controllers/`.
- **Layanan reusable** → `app/services/`.
- **Validasi form** → schema di `app/data/form-schemas.json`, dijalankan
  `App\Core\JsonFormValidator`.
- **Aturan autentikasi** → hanya di `app/middleware/AuthMiddleware.php` supaya
  tidak ada pengecekan login yang tersebar.
- **Fungsi bantu kecil** → berkas yang sesuai di `app/core/helpers/`
  (`env`, `output`, `csrf`, `url`, `flash`, `auth`, `format`).
- **Koneksi database** → `app/core/Connection.php` (jangan buat koneksi baru).
- **CSS bersama** → `assets/css/base/`, `components/`, atau `layout/`.
- **CSS khusus halaman** → `assets/css/pages/`.

Prinsipnya satu: satu tanggung jawab, satu tempat. Kalau ragu, lihat modul
terdekat yang punya tugas mirip dan ikuti polanya.

## Pemeriksaan sebelum commit

Jalankan setidaknya ini:

```bash
find . -name '*.php' -not -path './.git/*' -print0 | xargs -0 -n1 php -l
php -r 'json_decode(file_get_contents("app/data/form-schemas.json"), true, 512, JSON_THROW_ON_ERROR);'
git diff --check
```

Lalu uji manual: daftar, masuk, buat proyek, ubah proyek, hapus proyek, upload
satu gambar/PDF, hapus media, ubah profil, dan cek portfolio publik. Sedikit
berulang memang, tapi jauh lebih murah daripada memperbaiki bug setelah rilis.
