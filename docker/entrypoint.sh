#!/bin/sh
# =============================================================================
# Entrypoint container web PortfolioHub.
#
# Tugas utama: memastikan folder upload dapat ditulis oleh Apache (www-data).
# Volume bind-mount dari host biasanya dimiliki oleh user host, sehingga
# www-data tidak punya izin tulis. Kita samakan kepemilikannya setiap start.
# =============================================================================
set -e

# Semua folder tempat aplikasi menulis berkas upload. Tambahkan direktori baru
# di sini ketika ada fitur upload baru (lihat app/config/media.php).
UPLOAD_DIRS="/var/www/html/assets/uploads
/var/www/html/storage/cv"

for dir in $UPLOAD_DIRS; do
    mkdir -p "$dir"
    chown -R www-data:www-data "$dir"
    chmod -R 775 "$dir"
done

# Lanjutkan ke command bawaan image (apache2-foreground).
exec "$@"
