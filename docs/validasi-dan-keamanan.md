# Validasi, Upload, dan Keamanan

## CSRF

Semua form yang mengubah data harus menyertakan:

```php
<input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
```

Dan route POST harus memanggil `verifyCsrfToken()` sebelum melakukan tindakan.
Jangan menghapus dua bagian ini walau form terlihat sederhana.

## Validasi JSON

Aturan dasar form disimpan di `app/data/form-schemas.json`, lalu dibaca oleh
`App\Core\JsonFormValidator`. Ini membuat aturan tidak tercecer di banyak
file. Validasi di browser boleh ditambahkan untuk UX, tetapi validasi server
tetap menjadi sumber kebenaran.

Validasi yang bergantung pada database tetap berada di controller, misalnya:

- email tidak boleh terdaftar dua kali;
- username harus unik;
- media hanya boleh dihubungkan ke proyek milik creator yang sedang masuk.

## Upload media dan CV

`App\Services\MediaUploadService` memusatkan proses upload untuk media proyek
maupun CV. Service yang sama dipanggil dengan konfigurasi berbeda dari
`app/config/media.php` (`mediaUploadConfig()` dan `cvUploadConfig()`).

Langkah yang dijalankan setiap upload:

1. Mengecek status upload PHP.
2. Mengecek ukuran maksimal (25 MB untuk media, 10 MB untuk CV).
3. Mengecek MIME type menggunakan `finfo` dari file temporer.
4. Memetakan MIME type yang diizinkan ke ekstensi aman.
5. Menyimpan file dengan nama acak, bukan nama kiriman pengguna.
6. Menyimpan metadata di tabel `media_assets` (media) atau kolom `cv_filename`
   pada `users` (CV).

Jenis yang diterima untuk media proyek:

| Kategori | MIME type |
| --- | --- |
| Gambar | `image/jpeg`, `image/png`, `image/webp`, `image/gif` |
| Video | `video/mp4`, `video/webm` |
| Dokumen | `application/pdf` |

CV hanya menerima `application/pdf`. Berkas CV disimpan di luar web root
(`storage/cv`) sehingga tidak bisa dibuka lewat URL langsung; preview dan
unduhan dilayani `/studio/cv.php` milik creator yang sedang masuk, jadi satu
creator tidak dapat membaca CV creator lain.

Konfigurasi ini ada di `app/config/media.php`. Jika perlu menambah format baru,
tambahkan **MIME type yang diverifikasi** di sana; jangan hanya menambah nilai
`accept` pada input HTML.

## Ownership data

Query creator selalu menerima `$userId`. Contohnya, `findProject($userId,
$projectId)` memastikan satu creator tidak dapat mengedit proyek creator lain
hanya dengan mengganti parameter URL. Pertahankan pola ini untuk data baru.

## Password dan session

- Password creator disimpan menggunakan `password_hash()`.
- Login diverifikasi menggunakan `password_verify()`.
- Session ID dibuat ulang sesudah login untuk mengurangi risiko session fixation.
- Logout menghapus data session serta cookie session bila cookie digunakan.

## Superadmin

Superadmin berada di `/login/superadmin/` dan tidak memakai sidebar creator.
Kredensialnya masih memakai konfigurasi environment `ADMIN_USERNAME` dan
`ADMIN_PASSWORD`. Untuk production, gunakan secret environment yang kuat dan
pertimbangkan menambah hash password serta rate limiting.

## Session cookie dan CSRF

Halaman creator memakai **session cookie**. Cookie dikonfigurasi dengan
`HttpOnly` dan `SameSite=Lax` dalam `app/bootstrap.php`, sementara setiap form
POST dilindungi CSRF token. Ini adalah satu-satunya mekanisme autentikasi yang
dipakai aplikasi.

Proteksi terpusat ada di `app/middleware/AuthMiddleware.php` lewat
`requireSessionUserId()`, yang selalu dipanggil pertama pada setiap halaman
`/studio/`. Middleware ini memeriksa bahwa:

1. ada id user yang sah di session;
2. akun tersebut masih ada di database (sesi dihapus bila user sudah dihapus);

Bila gagal, session login dibersihkan, pesan flash ditampilkan, dan pengunjung
diarahkan ke halaman masuk.

Session ID dibuat ulang sesudah login, dan logout menghapus data session
beserta cookie-nya.
