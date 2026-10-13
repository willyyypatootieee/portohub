# Dokumentasi PortfolioHub

Dokumentasi ini menjelaskan struktur dan alur aplikasi setelah penambahan
creator studio. Bahasanya dibuat formal tetapi tetap praktis agar nyaman
dipakai saat onboarding atau maintenance sehari-hari.

## Daftar dokumen

| Dokumen | Kegunaan |
| --- | --- |
| [Arsitektur aplikasi](arsitektur.md) | Peta folder, layer aplikasi, dan alur request. |
| [Panduan pengembangan](pengembangan.md) | Cara menjalankan, menambah fitur, serta menjaga gaya kode. |
| [Form, upload, dan keamanan](validasi-dan-keamanan.md) | Kontrak JSON, validasi upload, CSRF, dan ownership data. |
| [Rute aplikasi](rute.md) | Daftar alamat publik, creator, dan superadmin. |
| [Demo kelas dengan ngrok](demo-ngrok.md) | Menjalankan aplikasi lokal dan membagikannya lewat tunnel ngrok. |

## Mulai cepat

1. Salin `.env.example` menjadi `.env` bila konfigurasi database lokal berbeda.
2. Jalankan Docker dengan `docker compose up --build`, atau import
   `database/portfoliohub.sql` melalui phpMyAdmin untuk XAMPP/Laragon.
3. Buka halaman landing lalu daftar sebagai creator di `/login/register/`.
4. Setelah masuk, creator akan diarahkan ke `/studio/`.

> Catatan kecil: halaman login dan register sengaja belum diberi desain.
> Lokasi CSS-nya sudah disiapkan agar nanti tidak perlu mencari-cari:
> `assets/css/auth/login.css` dan `assets/css/auth/register.css`.
