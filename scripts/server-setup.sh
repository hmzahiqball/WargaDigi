#!/usr/bin/env bash
#
# server-setup.sh — Setup sekali-jalan WargaDigi via Docker di server.
# Asumsi: Docker + Docker Compose v2 sudah terpasang, Caddy sudah berjalan
# sebagai reverse proxy untuk domain lain di server ini.
#
# Domain    : wargadigi21.com
# Arsitektur: [Internet] -> Caddy (:443) -> 127.0.0.1:APP_HOST_PORT -> container app
#
# Jalankan dari root project:  sudo bash scripts/server-setup.sh
#
set -euo pipefail

DOMAIN="wargadigi21.com"
ENV_FILE=".env.docker"
CADDYFILE="/etc/caddy/Caddyfile"

# Warna
info()  { printf "\033[1;34m==>\033[0m %s\n" "$1"; }
warn()  { printf "\033[1;33m[!]\033[0m %s\n" "$1"; }
ok()    { printf "\033[1;32m[OK]\033[0m %s\n" "$1"; }
die()   { printf "\033[1;31m[ERROR]\033[0m %s\n" "$1"; exit 1; }

# --- 0. Prasyarat ---
command -v docker >/dev/null 2>&1 || die "Docker tidak ditemukan. Pasang Docker dulu."
docker compose version >/dev/null 2>&1 || die "Docker Compose v2 tidak ditemukan."
[ -f docker-compose.prod.yml ] || die "Jalankan dari root project (docker-compose.prod.yml tidak ada)."

rand() { openssl rand -hex 24 2>/dev/null || head -c 24 /dev/urandom | od -An -tx1 | tr -d " \n"; }

# --- 1. Siapkan .env.docker ---
if [ ! -f "$ENV_FILE" ]; then
    info "Membuat $ENV_FILE dari template dan mengisi rahasia..."
    cp .env.docker.example "$ENV_FILE"

    DB_PASS="$(rand)"
    ROOT_PASS="$(rand)"
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" "$ENV_FILE"
    sed -i "s|^DB_ROOT_PASSWORD=.*|DB_ROOT_PASSWORD=${ROOT_PASS}|" "$ENV_FILE"
    ok "Password database dibuat acak dan disimpan di $ENV_FILE"
else
    warn "$ENV_FILE sudah ada, memakai yang ada (tidak menimpa rahasia)."
fi

# --- 2. Generate APP_KEY bila kosong ---
if grep -q "^APP_KEY=$" "$ENV_FILE" || ! grep -q "^APP_KEY=base64" "$ENV_FILE"; then
    info "Membuat APP_KEY..."
    KEY="base64:$(head -c 32 /dev/urandom | base64)"
    if grep -q "^APP_KEY=" "$ENV_FILE"; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${KEY}|" "$ENV_FILE"
    else
        echo "APP_KEY=${KEY}" >> "$ENV_FILE"
    fi
    ok "APP_KEY dibuat."
fi

# --- 3. Build & jalankan container ---
info "Build image aplikasi..."
docker compose --env-file .env.docker -f docker-compose.prod.yml build

info "Menjalankan container (app + db)..."
docker compose --env-file .env.docker -f docker-compose.prod.yml up -d

# Ambil port host yang dipakai
APP_PORT="$(grep -E "^APP_HOST_PORT=" "$ENV_FILE" | cut -d= -f2)"
APP_PORT="${APP_PORT:-8080}"

info "Menunggu aplikasi siap di 127.0.0.1:${APP_PORT}..."
for i in $(seq 1 30); do
    if curl -fsS "http://127.0.0.1:${APP_PORT}" >/dev/null 2>&1; then
        ok "Aplikasi merespons."
        break
    fi
    sleep 3
    [ "$i" = "30" ] && warn "Aplikasi belum merespons; cek: docker compose --env-file .env.docker -f docker-compose.prod.yml logs app"
done

# --- 4. Seeder DATA DEMO (termasuk akun pengguna) ---
# Mode demo: menjalankan seluruh seeder (Role, Master RT, User, Keluarga,
# Penduduk, UMKM, Keuangan, dll). UserSeeder membuat akun demo berpassword 'password'.
warn "Mode DEMO: menjalankan seluruh seeder termasuk akun pengguna (password: 'password')."
info "Menjalankan db:seed (semua seeder demo)..."
docker compose --env-file .env.docker -f docker-compose.prod.yml exec -T app php artisan db:seed --force || warn "db:seed dilewati/gagal (mungkin sudah pernah di-seed)."
ok "Seeder demo selesai. Akun demo siap dipakai untuk presentasi."

# --- 5. Konfigurasi Caddy ---
CADDY_BLOCK="
${DOMAIN} {
    encode gzip zstd
    reverse_proxy 127.0.0.1:${APP_PORT}
}
"

if [ -f "$CADDYFILE" ]; then
    if grep -q "${DOMAIN}" "$CADDYFILE"; then
        warn "Blok untuk ${DOMAIN} sudah ada di ${CADDYFILE}. Lewati."
    else
        info "Menambahkan blok ${DOMAIN} ke ${CADDYFILE}..."
        printf "%s\n" "$CADDY_BLOCK" >> "$CADDYFILE"
        if command -v caddy >/dev/null 2>&1; then
            caddy validate --config "$CADDYFILE" --adapter caddyfile && \
            { systemctl reload caddy 2>/dev/null || caddy reload --config "$CADDYFILE" 2>/dev/null || true; }
            ok "Caddy dikonfigurasi & di-reload untuk ${DOMAIN}."
        else
            warn "Perintah caddy tidak ditemukan; reload manual diperlukan."
        fi
    fi
else
    warn "Caddyfile tidak ditemukan di ${CADDYFILE}."
    echo "Tambahkan blok berikut ke Caddyfile Anda lalu reload Caddy:"
    echo "----------------------------------------------------------------"
    printf "%s\n" "$CADDY_BLOCK"
    echo "----------------------------------------------------------------"
fi

echo
ok "SETUP SELESAI (MODE DEMO)."
echo "  - Aplikasi     : https://${DOMAIN}  (via Caddy -> 127.0.0.1:${APP_PORT})"
echo "  - Rahasia      : tersimpan di ${ENV_FILE} (jangan commit ke git)"
echo "  - Log app      : docker compose --env-file .env.docker -f docker-compose.prod.yml logs -f app"
echo
echo "  Akun demo (password semua: 'password') — login pakai NIK + Nomor WhatsApp:"
echo "    Admin Aplikasi : NIK 3217010101010001 | WA 082123456789"
echo "    Admin RW       : NIK 3217010101010002 | WA 081234567890"
echo "    Ketua RT       : NIK 3217010101010008 | WA 081398765432"
echo "    Warga          : NIK 3217010101010003 | WA 081987654321"
echo
warn "PERINGATAN: ini data DEMO dengan password lemah. Ganti/hapus sebelum dipakai produksi nyata."
