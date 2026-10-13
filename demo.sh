#!/usr/bin/env bash
#
# Helper demo PortfolioHub untuk kelas.
#
# Skrip ini menyiapkan aplikasi, memastikan container berjalan, lalu membuka
# tunnel ngrok supaya teman atau dosen bisa mengakses dari jaringan lain.
#
# Pemakaian:
#   ./demo.sh              # jalankan Docker + tunnel ngrok publik
#   ./demo.sh --local      # hanya Docker, tanpa ngrok
#   PORT=8088 ./demo.sh    # ubah port lokal bila perlu

set -euo pipefail

PORT="${PORT:-8088}"
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
USE_NGROK=1

if [[ "${1:-}" == "--local" ]]; then
    USE_NGROK=0
fi

cd "$PROJECT_DIR"

echo "==> 1. Menyalakan database, web, dan phpMyAdmin"
docker compose up -d --build

echo "==> 2. Menunggu web siap di http://localhost:${PORT}"
for attempt in $(seq 1 30); do
    if curl -fsS -o /dev/null "http://localhost:${PORT}/login/"; then
        echo "    Web sudah siap."
        break
    fi

    if [[ "$attempt" -eq 30 ]]; then
        echo "    Web belum merespons. Cek 'docker compose logs web'." >&2
        exit 1
    fi

    sleep 1
done

if [[ "$USE_NGROK" -eq 0 ]]; then
    echo "==> Selesai (mode lokal)."
    echo "    Landing    : http://localhost:${PORT}/"
    echo "    Studio     : http://localhost:${PORT}/studio/"
    echo "    phpMyAdmin : http://localhost:8082/"
    exit 0
fi

if ! command -v ngrok >/dev/null 2>&1; then
    echo "==> ngrok belum terpasang." >&2
    echo "    Unduh dari https://ngrok.com/download lalu jalankan 'ngrok config add-authtoken <TOKEN>'." >&2
    echo "    Sementara aplikasi tetap berjalan di http://localhost:${PORT}/" >&2
    exit 1
fi

echo "==> 3. Membuka tunnel ngrok"
echo "    Salin URL https://... di bawah ini dan bagikan ke kelas."
echo

exec ngrok http "${PORT}" --log=stdout
