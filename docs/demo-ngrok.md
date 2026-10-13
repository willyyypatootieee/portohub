# Demo Kelas dengan ngrok

Panduan ini dipakai saat ingin memperlihatkan PortfolioHub ke teman sekelas
atau dosen tanpa harus deploy ke hosting. Cukup jalankan aplikasi secara lokal,
lalu buka tunnel publik memakai ngrok.

## Gambaran alur

```text
Browser teman  ──►  URL ngrok (https)  ──►  ngrok  ──►  localhost:8088  ──►  Docker web (Apache + PHP)
                                                                              └──►  MySQL container
```

Yang perlu disiapkan hanya Docker dan ngrok. Database dan web tetap berjalan
di laptop Anda.

## Langkah singkat (paling cepat)

1. Jalankan seluruh aplikasi dan tunnel dengan satu perintah:

   ```bash
   ./demo.sh
   ```

2. Tunggu sampai ngrok menampilkan baris `Forwarding`, contohnya:

   ```text
   Forwarding   https://namadomain-ngrok.ngrok-free.app -> http://localhost:8088
   ```

3. Bagikan URL `https://...ngrok-free.app` tersebut ke kelas.

4. Buka dan tunjukkan alurnya:

   - `/` untuk landing page dan filter karya.
   - `/login/register/` untuk membuat akun creator.
   - `/studio/` untuk dashboard creator.
   - `/portfolio/?user=nama-username` untuk portofolio publik.
   - `/login/superadmin/` untuk monitoring terpisah.

Untuk mode lokal tanpa ngrok, gunakan:

```bash
./demo.sh --local
```

## Langkah manual

Kalau ingin menjalankan satu per satu, urutannya seperti ini.

### 1. Jalankan aplikasi

```bash
docker compose up --build
```

Pastikan aplikasi bisa diakses di `http://localhost:8088/login/`.

### 2. Siapkan ngrok

Untuk pemakaian pertama, pasang authtoken sekali saja:

```bash
ngrok config add-authtoken TOKEN_ANDA
```

Token diambil dari dashboard ngrok pada menu **Your Authtoken**.

### 3. Buka tunnel

```bash
ngrok http 8088
```

Salin URL HTTPS yang muncul, lalu bagikan ke kelas.

### 4. Opsi memakai file konfigurasi

Proyek menyediakan contoh konfigurasi `ngrok.example.yml`. Salin dan sesuaikan:

```bash
cp ngrok.example.yml ngrok.yml
# edit ngrok.yml: isi authtoken dan pastikan addr = 8088
ngrok start portohub --config ngrok.yml
```

## Hal yang perlu diperhatikan saat demo

- **Session dan cookie.** ngrok memakai HTTPS, jadi cookie session otomatis
  ditandai `Secure`. Login tetap berjalan normal.
- **Upload media.** Batas upload sudah 25 MB melalui `php/uploads.ini` pada
  Docker dan `.user.ini` untuk XAMPP/Laragon. Untuk upload yang sangat besar,
  naikkan `upload_max_filesize` dan `post_max_size`.
- **Alamat portfolio.** Format yang paling aman saat demo adalah
  `/portfolio/?user={username}` karena tidak bergantung pada rewrite Apache.
  Format cantik `/portfolio/{username}/` juga tersedia bila `.htaccess` aktif.
- **Tutup akses setelah demo.** Tekan `Ctrl+C` pada terminal ngrok. URL ngrok
  akan langsung tidak dapat diakses lagi.
- **Jangan pakai kredensial asli.** Superadmin default tetap
  `admin` / `admin123`. Untuk demo di kelas sebaiknya ganti dulu melalui
  environment `ADMIN_USERNAME` dan `ADMIN_PASSWORD`.

## Troubleshooting singkat

| Gejala | Penyebab umum | Solusi |
| --- | --- | --- |
| ngrok menampilkan `ERR_NGROK_3200` | Aplikasi lokal belum jalan | Jalankan `docker compose up -d` lalu cek `http://localhost:8088/login/`. |
| Halaman tampil tetapi CSS polos | Distribusi font sedang lambat | Muat ulang; font DM Sans diambil dari Google Fonts. |
| Muncul pesan import tabel creator | Volume database dibuat sebelum perubahan | Jalankan `docker compose down -v` lalu `docker compose up --build`. |
| Perubahan URL setiap restart (free plan) | Batasan ngrok versi gratis | Bagikan URL baru, atau gunakan domain statis ngrok bila tersedia. |
