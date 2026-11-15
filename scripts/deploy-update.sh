#!/usr/bin/env bash
#
# deploy-update.sh — Update WargaDigi di server Docker + Caddy yang SUDAH berjalan.
#
# Yang dilakukan otomatis:
#   1. Cek prasyarat (folder repo, .env.docker, Docker, container berjalan, git bersih).
#   2. Backup database (mysqldump) + foto agenda/galeri (public/assets/images).
#   3. git pull branch main (fast-forward saja).
#   4. Build image baru (container lama tetap melayani selama build).
#   5. Jalankan container baru (migrasi otomatis lewat entrypoint), tunggu sampai sehat.
#   6. Pulihkan foto agenda/galeri (tidak tersimpan di volume Docker).
#   7. (Opsional --reset-demo) hapus semua data lalu isi data demo terbaru.
#   8. Siapkan notifikasi browser (kunci VAPID dibuat bila belum ada, notif_push dinyalakan),
#      OCR KK/KTP (API key OCR.space di .env.docker), dan muat isi Pusat Bantuan.
#   9. Cek halaman utama. Bila gagal: rollback otomatis (kode + database + foto).
#
# Seeder TIDAK dijalankan pada update biasa. Caddy tidak diubah.
#
# Pemakaian (dari root project, mis. ~/WargaDigi):
#   sudo bash scripts/deploy-update.sh                 # update biasa, data dipertahankan
#   sudo bash scripts/deploy-update.sh --reset-demo    # update + reset ke data demo (HAPUS SEMUA DATA)
#   Opsi lain:
#     --yes               tanpa konfirmasi/pertanyaan
#     --branch <nama>     branch sumber (default main)
#     --ocr-key <KEY>     pakai API key OCR.space milik sendiri
#     --no-ocr            matikan OCR (form KK/KTP selalu diisi manual)
#     --no-push           jangan menyalakan notifikasi browser
#
# Pertama kali (server masih kode lama yang belum punya skrip ini):
#   git fetch origin main && git show origin/main:scripts/deploy-update.sh > /tmp/deploy-update.sh
#   sudo bash /tmp/deploy-update.sh
#
set -euo pipefail

# Jalankan dari salinan di /tmp agar `git pull` yang mengganti file ini tidak mengacaukan eksekusi.
if [ "${WARGADIGI_DEPLOY_COPY:-}" != "1" ]; then
    SALINAN="$(mktemp /tmp/wargadigi-deploy.XXXXXX.sh)"
    cp "$0" "$SALINAN"
    export WARGADIGI_DEPLOY_COPY=1 WARGADIGI_DEPLOY_DIR="$(pwd)"
    exec bash "$SALINAN" "$@"
fi
cd "${WARGADIGI_DEPLOY_DIR:-$(pwd)}"
trap 'rm -f "$0"' EXIT

ENV_FILE=".env.docker"
COMPOSE_FILE="docker-compose.prod.yml"
BRANCH="main"
RESET_DEMO=0
ASSUME_YES=0
OCR_KEY_BARU=""
NO_OCR=0
NO_PUSH=0
BACKUP_DIR="${BACKUP_DIR:-$HOME/wargadigi-backup}"
SIMPAN_BACKUP=10   # jumlah set backup terakhir yang disimpan

info() { printf "\033[1;34m==>\033[0m %s\n" "$1"; }
warn() { printf "\033[1;33m[!]\033[0m %s\n" "$1"; }
ok()   { printf "\033[1;32m[OK]\033[0m %s\n" "$1"; }
die()  { printf "\033[1;31m[ERROR]\033[0m %s\n" "$1"; exit 1; }

while [ $# -gt 0 ]; do
    case "$1" in
        --reset-demo) RESET_DEMO=1 ;;
        --yes|-y)     ASSUME_YES=1 ;;
        --branch)     shift; BRANCH="${1:?--branch butuh nama branch}" ;;
        --ocr-key)    shift; OCR_KEY_BARU="${1:?--ocr-key butuh API key}" ;;
        --no-ocr)     NO_OCR=1 ;;
        --no-push)    NO_PUSH=1 ;;
        -h|--help)    sed -n '2,33p' "$0"; exit 0 ;;
        *)            die "Opsi tidak dikenal: $1 (lihat --help)" ;;
    esac
    shift
done

DC=(docker compose --env-file "$ENV_FILE" -f "$COMPOSE_FILE")
# Pengaturan khusus server (mis. network eksternal) disimpan di file override yang tidak ikut Git.
SERVER_OVERRIDE="docker-compose.server.yml"
if [ -f "$SERVER_OVERRIDE" ]; then
    DC+=(-f "$SERVER_OVERRIDE")
fi
# safe.directory: hindari error 'dubious ownership' saat dijalankan dengan sudo (tanpa mengubah git config).
G=(git -c safe.directory="$(pwd)")

# Baca/tulis nilai di .env.docker (nilai terakhir menang; kutip dan CR dibuang).
env_get() { grep -E "^$1=" "$ENV_FILE" 2>/dev/null | tail -n 1 | cut -d= -f2- | tr -d '\r' | sed -e 's/^"//' -e 's/"$//' || true; }
env_set() {
    local key="$1" val="$2"
    if grep -qE "^${key}=" "$ENV_FILE"; then
        sed -i "s|^${key}=.*|${key}=${val}|" "$ENV_FILE"
    else
        # Pastikan file diakhiri baris baru agar baris baru tidak tersambung ke baris terakhir.
        [ -s "$ENV_FILE" ] && [ -n "$(tail -c 1 "$ENV_FILE")" ] && printf '\n' >> "$ENV_FILE"
        printf '%s=%s\n' "$key" "$val" >> "$ENV_FILE"
    fi
}
# Perbaiki .env.docker yang rusak oleh versi skrip sebelumnya: baris kunci yang tersambung
# ke akhir baris lain (mis. "APP_HOST_PORT=8080WEBPUSH_SUBJECT=...") dipisah kembali.
perbaiki_env() {
    local k
    for k in WEBPUSH_SUBJECT WEBPUSH_PUBLIC_KEY WEBPUSH_PRIVATE_KEY OCR_PROVIDER OCR_SPACE_API_KEY OCR_SPACE_LANGUAGE OCR_SPACE_ENGINE; do
        if grep -qE "^[A-Z][A-Z0-9_]*=.+${k}=" "$ENV_FILE"; then
            sed -i -E "s/^([A-Z][A-Z0-9_]*=.+)(${k}=)/\1\n\2/" "$ENV_FILE"
            warn "Memperbaiki baris $k yang tersambung di $ENV_FILE."
        fi
    done
}

# ------------------------------------------------------------------ 1. Prasyarat
info "Memeriksa prasyarat..."
[ -f "$COMPOSE_FILE" ] || die "Jalankan dari root project ($COMPOSE_FILE tidak ditemukan)."
[ -f "$ENV_FILE" ]     || die "$ENV_FILE tidak ditemukan. Server ini belum pernah di-setup (pakai scripts/server-setup.sh)."
command -v docker >/dev/null 2>&1          || die "Docker tidak ditemukan."
docker compose version >/dev/null 2>&1     || die "Docker Compose v2 tidak ditemukan."
command -v curl >/dev/null 2>&1            || die "curl tidak ditemukan."
"${G[@]}" rev-parse --is-inside-work-tree >/dev/null 2>&1 || die "Folder ini bukan repository git."

if [ -n "$("${G[@]}" status --porcelain --untracked-files=no)" ]; then
    "${G[@]}" status --short --untracked-files=no
    if "${G[@]}" status --porcelain --untracked-files=no | grep -q "$COMPOSE_FILE"; then
        warn "Pengaturan khusus server untuk Docker taruh di $SERVER_OVERRIDE (tidak ikut Git),"
        warn "lalu kembalikan $COMPOSE_FILE: git checkout -- $COMPOSE_FILE (lihat docs/Deploy-Server-Caddy.md)."
    fi
    die "Ada perubahan lokal pada file ter-track. Rapikan dulu agar git pull aman."
fi
[ -f "$SERVER_OVERRIDE" ] && info "Memakai pengaturan khusus server: $SERVER_OVERRIDE"

perbaiki_env
"${DC[@]}" config --quiet 2>/dev/null || die "Konfigurasi Docker Compose tidak valid (cek $ENV_FILE dan file compose): ${DC[*]} config"

"${DC[@]}" ps --status running --services 2>/dev/null | grep -qx db  || die "Container db tidak berjalan. Jalankan: ${DC[*]} up -d"
"${DC[@]}" ps --status running --services 2>/dev/null | grep -qx app || warn "Container app tidak berjalan; backup foto agenda/galeri akan dilewati."

APP_PORT="$(grep -E '^APP_HOST_PORT=' "$ENV_FILE" | cut -d= -f2 | tr -d '\r"' || true)"
APP_PORT="${APP_PORT:-8080}"
BASE_URL="http://127.0.0.1:${APP_PORT}"

COMMIT_LAMA="$("${G[@]}" rev-parse HEAD)"
info "Mengambil info branch ${BRANCH}..."
"${G[@]}" fetch --quiet origin "$BRANCH"
COMMIT_BARU="$("${G[@]}" rev-parse "origin/${BRANCH}")"

echo "    Commit berjalan : $("${G[@]}" log -1 --format='%h %s' "$COMMIT_LAMA")"
echo "    Commit tujuan   : $("${G[@]}" log -1 --format='%h %s' "$COMMIT_BARU")"
if [ "$COMMIT_LAMA" = "$COMMIT_BARU" ]; then
    warn "Kode sudah versi terbaru; image tetap dibangun ulang dan container dijalankan ulang."
fi

if [ "$RESET_DEMO" = "1" ]; then
    warn "MODE --reset-demo: SEMUA DATA di database akan DIHAPUS lalu diganti data demo (password akun: 'password')."
    if [ "$ASSUME_YES" != "1" ]; then
        read -r -p "Ketik RESET untuk melanjutkan: " JAWAB
        [ "$JAWAB" = "RESET" ] || die "Dibatalkan."
    fi
elif [ "$ASSUME_YES" != "1" ]; then
    read -r -p "Lanjutkan update (data dipertahankan)? [y/N] " JAWAB
    case "$JAWAB" in y|Y|ya|Ya) ;; *) die "Dibatalkan." ;; esac
fi

# ------------------------------------------------------------------ Keputusan OCR (ditanya di awal)
OCR_PROVIDER_LAMA="$(env_get OCR_PROVIDER)"
OCR_KEY_LAMA="$(env_get OCR_SPACE_API_KEY)"
OCR_PROVIDER_SET=""; OCR_KEY_SET=""
if [ "$NO_OCR" = "1" ]; then
    OCR_PROVIDER_SET="null"
elif [ -n "$OCR_KEY_BARU" ]; then
    OCR_PROVIDER_SET="ocrspace"; OCR_KEY_SET="$OCR_KEY_BARU"
elif [ "$OCR_PROVIDER_LAMA" = "null" ]; then
    :   # OCR sengaja dimatikan sebelumnya; biarkan.
elif [ -z "$OCR_KEY_LAMA" ] || [ "$OCR_KEY_LAMA" = "helloworld" ]; then
    if [ "$ASSUME_YES" != "1" ] && [ -t 0 ]; then
        echo
        echo "OCR baca KK/KTP masih memakai kunci demo publik OCR.space (batas pemakaian ketat)."
        echo "Daftar gratis untuk kunci sendiri: https://ocr.space/ocrapi"
        read -r -p "API key OCR.space (Enter = tetap kunci demo, ketik null = matikan OCR): " JAWAB_OCR
        case "$JAWAB_OCR" in
            "")   OCR_PROVIDER_SET="ocrspace"; OCR_KEY_SET="helloworld" ;;
            null) OCR_PROVIDER_SET="null" ;;
            *)    OCR_PROVIDER_SET="ocrspace"; OCR_KEY_SET="$JAWAB_OCR" ;;
        esac
    else
        OCR_PROVIDER_SET="ocrspace"; OCR_KEY_SET="helloworld"
    fi
fi
case "$OCR_KEY_SET" in *"|"*|*" "*) die "API key OCR tidak valid (mengandung spasi atau '|')." ;; esac

# ------------------------------------------------------------------ 2. Backup
TGL="$(date +%F-%H%M%S)"
mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"
cp "$ENV_FILE" "$BACKUP_DIR/env-docker-$TGL" && chmod 600 "$BACKUP_DIR/env-docker-$TGL"
DB_BACKUP="$BACKUP_DIR/db-$TGL.sql.gz"
IMG_BACKUP="$BACKUP_DIR/images-$TGL.tar.gz"

info "Backup database -> $DB_BACKUP"
if ! "${DC[@]}" exec -T db sh -c 'mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" --single-transaction --routines --triggers "$MYSQL_DATABASE" 2>/dev/null' | gzip > "$DB_BACKUP"; then
    die "mysqldump gagal. Deploy dihentikan, tidak ada yang diubah."
fi
JUMLAH_TABEL="$(gzip -dc "$DB_BACKUP" | grep -c '^CREATE TABLE' || true)"
[ "${JUMLAH_TABEL:-0}" -gt 0 ] || die "Backup database kosong. Deploy dihentikan, tidak ada yang diubah."
ok "Backup database selesai ($JUMLAH_TABEL tabel, $(du -h "$DB_BACKUP" | cut -f1))."

if "${DC[@]}" ps --status running --services 2>/dev/null | grep -qx app; then
    info "Backup foto agenda/galeri -> $IMG_BACKUP"
    "${DC[@]}" exec -T app tar -czf - public/assets/images > "$IMG_BACKUP"
    ok "Backup foto selesai ($(du -h "$IMG_BACKUP" | cut -f1))."
else
    IMG_BACKUP=""
fi
echo "$COMMIT_LAMA" > "$BACKUP_DIR/commit-$TGL.txt"

# Simpan hanya beberapa set backup terakhir.
ls -1t "$BACKUP_DIR"/db-*.sql.gz 2>/dev/null | tail -n +$((SIMPAN_BACKUP + 1)) | while read -r f; do
    t="${f##*/db-}"; t="${t%.sql.gz}"
    rm -f "$f" "$BACKUP_DIR/images-$t.tar.gz" "$BACKUP_DIR/commit-$t.txt" "$BACKUP_DIR/env-docker-$t"
done

# ------------------------------------------------------------------ Fungsi bantu
tunggu_sehat() {
    # Entrypoint menjalankan migrasi dulu, baru web server; tunggu sampai /login merespons 200.
    local i kode
    for i in $(seq 1 60); do
        kode="$(curl -s -o /dev/null -w '%{http_code}' "$BASE_URL/login" || true)"
        [ "$kode" = "200" ] && return 0
        sleep 3
    done
    return 1
}

pulihkan_foto() {
    [ -n "$IMG_BACKUP" ] && [ -s "$IMG_BACKUP" ] || return 0
    "${DC[@]}" exec -T app tar -xzf - < "$IMG_BACKUP"
    "${DC[@]}" exec -T app chown -R www-data:www-data public/assets/images
}

cek_halaman() {
    local gagal=0 url kode
    for url in / /login /berita /agenda /pojok-umkm /layanan-mandiri; do
        kode="$(curl -s -o /dev/null -w '%{http_code}' "$BASE_URL$url" || true)"
        if [ "$kode" = "200" ]; then echo "    $url -> $kode"; else echo "    $url -> $kode  (GAGAL)"; gagal=1; fi
    done
    # Halaman yang wajib login: harus mengarahkan ke /login (302), bukan error 500.
    for url in /pusat-bantuan; do
        kode="$(curl -s -o /dev/null -w '%{http_code}' "$BASE_URL$url" || true)"
        if [ "$kode" = "302" ]; then echo "    $url -> $kode (wajib login)"; else echo "    $url -> $kode  (GAGAL)"; gagal=1; fi
    done
    return $gagal
}

rollback() {
    set +e   # rollback harus berjalan sampai akhir walau ada langkah yang gagal
    warn "ROLLBACK: mengembalikan kode, database, foto, dan $ENV_FILE ke kondisi sebelum deploy..."
    "${G[@]}" reset --quiet --keep "$COMMIT_LAMA"
    [ -f "$BACKUP_DIR/env-docker-$TGL" ] && cp "$BACKUP_DIR/env-docker-$TGL" "$ENV_FILE"

    info "Menghentikan app lalu memulihkan database dari $DB_BACKUP"
    "${DC[@]}" stop app >/dev/null 2>&1
    "${DC[@]}" exec -T db sh -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "DROP DATABASE IF EXISTS \`$MYSQL_DATABASE\`; CREATE DATABASE \`$MYSQL_DATABASE\`;" 2>/dev/null'
    gzip -dc "$DB_BACKUP" | "${DC[@]}" exec -T db sh -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" 2>/dev/null' \
        || warn "Restore database gagal. Pulihkan manual dari $DB_BACKUP"

    info "Build dan jalankan kode lama..."
    "${DC[@]}" build app && "${DC[@]}" up -d app
    if tunggu_sehat; then
        pulihkan_foto
        ok "Aplikasi lama kembali berjalan."
    else
        warn "Aplikasi lama belum merespons; cek manual."
    fi
    die "Deploy GAGAL dan sudah di-rollback ke $("${G[@]}" rev-parse --short "$COMMIT_LAMA"). Lihat log: ${DC[*]} logs --tail=200 app"
}

# ------------------------------------------------------------------ 3. Ambil kode
info "Mengambil kode terbaru (git pull --ff-only origin $BRANCH)..."
"${G[@]}" checkout --quiet "$BRANCH"
"${G[@]}" pull --ff-only --quiet origin "$BRANCH" || die "git pull gagal (riwayat bercabang?). Tidak ada yang diubah pada container."
ok "Kode sekarang: $("${G[@]}" log -1 --format='%h %s')"

# ------------------------------------------------------------------ 4. Build
info "Build image aplikasi (container lama tetap melayani)..."
if ! "${DC[@]}" build app; then
    "${G[@]}" reset --quiet --keep "$COMMIT_LAMA" || true
    die "Build image GAGAL. Container lama tetap berjalan, kode dikembalikan ke $("${G[@]}" rev-parse --short "$COMMIT_LAMA")."
fi
ok "Build selesai."

# ------------------------------------------------------------------ 4b. Konfigurasi Web Push + OCR
# Ditulis ke .env.docker sebelum container baru dijalankan agar langsung terbaca.
info "Menyiapkan konfigurasi notifikasi browser dan OCR..."
[ -n "$(env_get WEBPUSH_SUBJECT)" ] || env_set WEBPUSH_SUBJECT "$(env_get APP_URL)"
PUSH_BARU=0
if [ "$NO_PUSH" != "1" ] && { [ -z "$(env_get WEBPUSH_PUBLIC_KEY)" ] || [ -z "$(env_get WEBPUSH_PRIVATE_KEY)" ]; }; then
    info "Membuat kunci VAPID (sekali saja)..."
    # Pakai image baru tanpa menjalankan entrypoint (tanpa migrasi).
    VAPID_OUT="$("${DC[@]}" run --rm --no-deps -T --entrypoint php app artisan webpush:vapid 2>/dev/null || true)"
    PUB="$(printf '%s\n' "$VAPID_OUT" | sed -n 's/^WEBPUSH_PUBLIC_KEY=\([A-Za-z0-9_-]*\).*/\1/p' | head -n 1)"
    PRIV="$(printf '%s\n' "$VAPID_OUT" | sed -n 's/^WEBPUSH_PRIVATE_KEY=\([A-Za-z0-9_-]*\).*/\1/p' | head -n 1)"
    if [ ${#PUB} -ge 80 ] && [ ${#PRIV} -ge 40 ]; then
        env_set WEBPUSH_PUBLIC_KEY "$PUB"
        env_set WEBPUSH_PRIVATE_KEY "$PRIV"
        PUSH_BARU=1
        ok "Kunci VAPID dibuat dan disimpan di $ENV_FILE (cadangan .env lama: $BACKUP_DIR/env-docker-$TGL)."
    else
        warn "Gagal membuat kunci VAPID; notifikasi browser dilewati (notifikasi di aplikasi tetap jalan)."
    fi
fi
if [ -n "$OCR_PROVIDER_SET" ]; then
    env_set OCR_PROVIDER "$OCR_PROVIDER_SET"
    [ -n "$OCR_KEY_SET" ] && env_set OCR_SPACE_API_KEY "$OCR_KEY_SET"
    [ -n "$(env_get OCR_SPACE_LANGUAGE)" ] || env_set OCR_SPACE_LANGUAGE eng
    [ -n "$(env_get OCR_SPACE_ENGINE)" ]   || env_set OCR_SPACE_ENGINE 2
fi
chmod 600 "$ENV_FILE" 2>/dev/null || true
if ! "${DC[@]}" config --quiet 2>/dev/null; then
    cp "$BACKUP_DIR/env-docker-$TGL" "$ENV_FILE"
    "${G[@]}" reset --quiet --keep "$COMMIT_LAMA" || true
    die "Konfigurasi menjadi tidak valid setelah mengubah $ENV_FILE; file dan kode dikembalikan. Container lama tetap berjalan."
fi

# ------------------------------------------------------------------ 5. Jalankan
info "Menjalankan container baru (migrasi berjalan otomatis)..."
if ! "${DC[@]}" up -d app; then
    cp "$BACKUP_DIR/env-docker-$TGL" "$ENV_FILE"
    rollback
fi
info "Menunggu aplikasi siap di $BASE_URL ..."
if ! tunggu_sehat; then
    "${DC[@]}" logs --tail=60 app || true
    rollback
fi
ok "Aplikasi merespons."

# ------------------------------------------------------------------ 6. Foto agenda/galeri
if [ -n "$IMG_BACKUP" ]; then
    info "Memulihkan foto agenda/galeri..."
    pulihkan_foto && ok "Foto dipulihkan."
fi

# ------------------------------------------------------------------ 7. Reset demo (opsional)
if [ "$RESET_DEMO" = "1" ]; then
    info "Reset database dan isi data demo terbaru..."
    "${DC[@]}" exec -T app php artisan migrate:fresh --force
    "${DC[@]}" exec -T app php artisan db:seed --class=DemoSeeder --force
    "${DC[@]}" exec -T app php artisan optimize:clear >/dev/null
    "${DC[@]}" exec -T app php artisan optimize >/dev/null
    "${DC[@]}" exec -T app chown -R www-data:www-data storage/app public/assets/images
    ok "Data demo terpasang."
    # Foto demo (agenda/galeri) juga hilang saat rebuild berikutnya; backup sekarang.
    "${DC[@]}" exec -T app tar -czf - public/assets/images > "$BACKUP_DIR/images-demo-$TGL.tar.gz"
fi

# ------------------------------------------------------------------ 8. Aktifkan notifikasi + cek fitur
# notif_push tidak bisa diubah dari menu (bagian Notifikasi di Pengaturan Sistem disembunyikan),
# dan seeder demo mengembalikannya ke false; jadi dinyalakan di sini bila kunci VAPID ada.
if [ "$NO_PUSH" != "1" ] && [ -n "$(env_get WEBPUSH_PUBLIC_KEY)" ]; then
    "${DC[@]}" exec -T app php artisan tinker --execute="App\Models\SystemSetting::setMany(['notif_push' => true]); App\Models\SystemSetting::flushCache();" >/dev/null 2>&1 \
        || warn "Gagal menyalakan pengaturan notif_push."
fi
STATUS_FITUR="$("${DC[@]}" exec -T app php artisan tinker --execute="echo 'PUSH=' . (App\Services\NotificationService::isWebPushEnabled() ? 'aktif' : 'nonaktif') . ' OCR=' . config('ocr.provider') . ' KUNCI=' . (config('ocr.ocrspace.api_key') === 'helloworld' ? 'demo' : (config('ocr.ocrspace.api_key') ? 'sendiri' : 'kosong'));" 2>/dev/null | tr -d '\r' | grep -o 'PUSH=.*' | tail -n 1 || true)"
STATUS_PUSH="$(printf '%s' "$STATUS_FITUR" | sed -n 's/.*PUSH=\([a-z]*\).*/\1/p')"
STATUS_OCR="$(printf '%s' "$STATUS_FITUR" | sed -n 's/.*OCR=\([a-z]*\).*/\1/p')"
STATUS_KUNCI="$(printf '%s' "$STATUS_FITUR" | sed -n 's/.*KUNCI=\([a-z]*\).*/\1/p')"

# Pusat Bantuan: muat isi panduan versi terbaru (sebenarnya juga dimuat otomatis saat dibuka).
JUMLAH_PANDUAN="$("${DC[@]}" exec -T app php artisan panduan:muat 2>/dev/null | tr -d '\r' | grep -o '[0-9]\+ topik' | tail -n 1 || true)"
[ -n "$JUMLAH_PANDUAN" ] || warn "Isi Pusat Bantuan belum termuat; akan dimuat otomatis saat halaman dibuka."

# ------------------------------------------------------------------ 9. Cek akhir
info "Cek halaman utama..."
if ! cek_halaman; then
    if [ "$RESET_DEMO" = "1" ]; then
        die "Ada halaman gagal setelah reset demo. Cek log: ${DC[*]} logs --tail=200 app"
    fi
    "${DC[@]}" logs --tail=60 app || true
    rollback
fi

docker image prune -f >/dev/null 2>&1 || true

echo
ok "DEPLOY SELESAI: $("${G[@]}" log -1 --format='%h %s')"
echo "    Backup   : $BACKUP_DIR (db-$TGL.sql.gz${IMG_BACKUP:+, images-$TGL.tar.gz})"
echo "    Log app  : ${DC[*]} logs -f app"
echo "    Notifikasi browser : ${STATUS_PUSH:-tidak diketahui}$( [ "$PUSH_BARU" = "1" ] && echo ' (kunci VAPID baru dibuat)')"
case "$STATUS_OCR" in
    null)     echo "    OCR KK/KTP         : dimatikan (isi manual)" ;;
    ocrspace) echo "    OCR KK/KTP         : aktif, kunci ${STATUS_KUNCI:-?}$( [ "$STATUS_KUNCI" = "demo" ] && echo ' (batas ketat; ganti: --ocr-key <KEY>)')" ;;
    *)        echo "    OCR KK/KTP         : ${STATUS_OCR:-tidak diketahui}" ;;
esac
echo "    Pusat Bantuan      : ${JUMLAH_PANDUAN:-belum termuat (dimuat saat dibuka)}"
if [ "$RESET_DEMO" = "1" ]; then
    echo "    Akun demo: superadmin, adminrw21, pimpinan, opkontenrw21, opkeuanganrw21, dkm, ketuart01rw21,"
    echo "               opkontenrt01rw21, opkeuanganrt01rw21, opkonten_3, operator_rt3, budisampurno, budisantoso"
    echo "               (password semua: 'password')"
else
    echo "    Catatan  : atur Pengaturan Halaman Utama lewat menu Admin Aplikasi bila belum."
fi
