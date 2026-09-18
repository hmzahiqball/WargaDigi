#!/bin/sh
set -e

cd /var/www/html

echo "==> Menyiapkan aplikasi WargaDigi..."

# Pastikan .env ada
if [ ! -f .env ]; then
    echo "==> .env tidak ditemukan, menyalin dari .env.example"
    cp .env.example .env
fi

# Generate APP_KEY jika belum ada
if ! grep -q "^APP_KEY=base64" .env; then
    echo "==> Membuat APP_KEY"
    php artisan key:generate --force
fi

# Sinkronkan nilai environment (dari docker-compose) ke .env agar
# config:cache memakai nilai yang benar. Nilai environment container menang.
set_env() {
    key="$1"; val="$2"
    [ -z "$val" ] && return 0
    if grep -q "^${key}=" .env; then
        # ganti baris yang ada (pakai | sebagai delimiter agar aman untuk URL)
        sed -i "s|^${key}=.*|${key}=${val}|" .env
    else
        printf '%s=%s\n' "$key" "$val" >> .env
    fi
}

echo "==> Sinkronisasi environment ke .env"
set_env APP_ENV "$APP_ENV"
set_env APP_DEBUG "$APP_DEBUG"
set_env APP_URL "$APP_URL"
set_env ASSET_URL "$ASSET_URL"
set_env DB_CONNECTION "$DB_CONNECTION"
set_env DB_HOST "$DB_HOST"
set_env DB_PORT "$DB_PORT"
set_env DB_DATABASE "$DB_DATABASE"
set_env DB_USERNAME "$DB_USERNAME"
set_env DB_PASSWORD "$DB_PASSWORD"
set_env SESSION_DRIVER "$SESSION_DRIVER"
set_env CACHE_STORE "$CACHE_STORE"
set_env QUEUE_CONNECTION "$QUEUE_CONNECTION"

# Tunggu database siap (khusus MySQL). Lewati bila SQLite.
DB_CONN=$(php -r "echo trim(getenv('"'"'DB_CONNECTION'"'"') ?: '"'"''"'"');")
if [ "$DB_CONN" = "mysql" ]; then
    echo "==> Menunggu database MySQL siap..."
    until php -r "new PDO('"'"'mysql:host='"'"'.getenv('"'"'DB_HOST'"'"').'"'"';port='"'"'.(getenv('"'"'DB_PORT'"'"')?:3306), getenv('"'"'DB_USERNAME'"'"'), getenv('"'"'DB_PASSWORD'"'"'));" >/dev/null 2>&1; do
        echo "   ...database belum siap, mencoba lagi dalam 3 detik"
        sleep 3
    done
    echo "==> Database siap."
fi

# Migrasi database
echo "==> Menjalankan migrasi"
php artisan migrate --force

# Symlink storage
if [ ! -L public/storage ]; then
    echo "==> Membuat symlink storage"
    php artisan storage:link || true
fi

# Optimasi cache produksi
if [ "$(printf '"'"'%s'"'"' "$APP_ENV")" = "production" ]; then
    echo "==> Optimasi cache (production)"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan config:clear || true
fi

# Perbaiki kepemilikan folder yang perlu ditulis
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> Menjalankan supervisord (php-fpm, nginx, queue, scheduler)"
exec "$@"
