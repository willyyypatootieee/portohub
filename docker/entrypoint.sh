#!/bin/sh
# =============================================================================
# Entrypoint container web PortfolioHub.
#
# Tugas utama: memastikan folder upload dapat ditulis oleh Apache (www-data).
# Volume bind-mount dari host biasanya dimiliki oleh user host, sehingga
# www-data tidak punya izin tulis. Kita samakan kepemilikannya setiap start.
# =============================================================================
set -e

UPLOAD_DIR="/var/www/html/assets/uploads"

mkdir -p "$UPLOAD_DIR"
chown -R www-data:www-data "$UPLOAD_DIR"
chmod -R 775 "$UPLOAD_DIR"

# Lanjutkan ke command bawaan image (apache2-foreground).
exec "$@"
