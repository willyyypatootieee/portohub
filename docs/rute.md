# Daftar Rute

Semua route dilayani dari folder `public/`. Tabel di bawah memetakan URL publik
ke berkas route-nya.

| Route | Berkas route | Akses | Fungsi |
| --- | --- | --- | --- |
| `/` | `public/index.php` | Publik | Landing page dengan grid karya creator yang published. |
| `/explore/` | `public/explore/index.php` | Publik | Eksplorasi semua karya published: pencarian (`?q=`) untuk proyek **dan** creator, filter kategori (`?category=`), serta tab `?tab=projects` / `?tab=creators`. |
| `/project/{id}/` | `public/project/index.php` | Publik | Detail satu proyek: cover, deskripsi, galeri media (gambar/GIF/video/PDF), info creator, dan karya lain dari creator yang sama. Fallback: `/project/?id={id}`. |
| `/login/` | `public/login/index.php` | Publik | Masuk sebagai creator. |
| `/login/register/` | `public/login/register/index.php` | Publik | Registrasi creator. |
| `/studio/` | `public/studio/index.php` | Creator | Ringkasan creator studio. |
| `/studio/projects.php` | `public/studio/projects.php` | Creator | CRUD proyek, upload media saat membuat proyek atau dari panel media, dan galeri semua media. |
| `/studio/settings.php` | `public/studio/settings.php` | Creator | Ubah profil publik dan kelola CV (PDF). |
| `/studio/cv.php` | `public/studio/cv.php` | Creator | Preview (`inline`) dan unduh (`?download=1`) CV milik creator yang sedang masuk. |
| `/studio/analytics.php` | `public/studio/analytics.php` | Creator | Ringkasan perkembangan portofolio. |
| `/studio/logout.php` | `public/studio/logout.php` | Creator | Keluar dari session creator. |
| `/portfolio/{username}/` | `public/portfolio/index.php` | Publik | Portofolio creator: proyek published beserta galeri gambar, GIF, dan video. Fallback: `/portfolio/?user={username}`. |
| `/login/superadmin/` | `public/login/superadmin/index.php` | Superadmin | Dashboard monitoring platform: ringkasan angka, aktivitas creator terbaru, dan aksi cepat. |
| `/login/superadmin/users.php` | `public/login/superadmin/users.php` | Superadmin | Daftar dan pencarian pengguna, serta aksi mengaktifkan atau menonaktifkan akun. |
| `/login/superadmin/projects.php` | `public/login/superadmin/projects.php` | Superadmin | Daftar seluruh proyek dan portofolio, pencarian/filter konten, dan aksi menyembunyikan proyek. |

## Catatan URL publik

Beberapa route publik punya dua bentuk URL:

- **URL cantik** (`/portfolio/{username}/`, `/project/{id}/`) diarahkan lewat
  aturan `mod_rewrite` di `public/.htaccess` dan `public/<route>/.htaccess`,
  serta di `.htaccess` root untuk instalasi subfolder XAMPP/Laragon.
- **Fallback query string** (`/portfolio/?user={username}`, `/project/?id={id}`)
  selalu bekerja tanpa konfigurasi rewrite tambahan. Keduanya didukung oleh
  berkas route yang sama.

`/explore` (tanpa garis miring di akhir) otomatis dialihkan ke `/explore/` oleh
`mod_dir`.
