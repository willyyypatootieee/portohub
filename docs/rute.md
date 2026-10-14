# Daftar Rute

Semua route dilayani dari folder `public/`. Tabel di bawah memetakan URL publik
ke berkas route-nya.

| Route | Berkas route | Akses | Fungsi |
| --- | --- | --- | --- |
| `/` | `public/index.php` | Publik | Landing page dengan grid karya creator yang published. |
| `/login/` | `public/login/index.php` | Publik | Masuk sebagai creator. |
| `/login/register/` | `public/login/register/index.php` | Publik | Registrasi creator. |
| `/studio/` | `public/studio/index.php` | Creator | Ringkasan creator studio. |
| `/studio/projects.php` | `public/studio/projects.php` | Creator | CRUD proyek, upload media per proyek, dan galeri semua media. |
| `/studio/settings.php` | `public/studio/settings.php` | Creator | Ubah profil publik dan kelola CV (PDF). |
| `/studio/cv.php` | `public/studio/cv.php` | Creator | Preview (`inline`) dan unduh (`?download=1`) CV milik creator yang sedang masuk. |
| `/studio/analytics.php` | `public/studio/analytics.php` | Creator | Ringkasan perkembangan portofolio. |
| `/studio/logout.php` | `public/studio/logout.php` | Creator | Keluar dari session creator. |
| `/portfolio/?user={username}` | `public/portfolio/index.php` | Publik | Portofolio creator: proyek published beserta galeri gambar, GIF, dan video. |
| `/login/superadmin/` | `public/login/superadmin/index.php` | Superadmin | Monitoring platform terpisah. |

## Catatan URL portfolio

Route query string `/portfolio/?user={username}` dipakai sebagai fallback yang
langsung bekerja tanpa konfigurasi rewrite tambahan. Folder `public/portfolio/`
juga memiliki `.htaccess` untuk instalasi Apache yang mengizinkan URL cantik
seperti `/portfolio/{username}/`.
