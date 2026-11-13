# syntax=docker/dockerfile:1

############################
# Stage 1: Build aset (Vite)
############################
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY resources ./resources
COPY vite.config.* ./
# public dibutuhkan sebagian plugin; salin bila ada
COPY public ./public
RUN npm run build

############################
# Stage 2: Dependensi PHP (Composer)
############################
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Install tanpa dev & tanpa menjalankan skript yang butuh artisan (belum ada source penuh)
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction --optimize-autoloader

############################
# Stage 3: Image aplikasi
############################
FROM php:8.3-fpm-bookworm AS app

# Paket sistem + ekstensi PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx supervisor git unzip libzip-dev libpng-dev libjpeg-dev \
        libfreetype6-dev libonig-dev libxml2-dev default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring zip exif bcmath gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Composer (disalin dari image resmi) untuk dump-autoload dengan source lengkap
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Konfigurasi PHP, PHP-FPM, Nginx, Supervisor
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-www.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# Hapus site default bawaan Debian agar tidak bentrok dengan conf.d,
# dan bersihkan BOM UTF-8 dari file konfigurasi (ditulis di host Windows)
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/sites-available/default \
    && sed -i "1s/^\xEF\xBB\xBF//" /etc/supervisor/conf.d/supervisord.conf \
    && sed -i "1s/^\xEF\xBB\xBF//" /etc/nginx/conf.d/default.conf \
    && sed -i "1s/^\xEF\xBB\xBF//" /usr/local/etc/php/conf.d/zz-custom.ini \
    && sed -i "1s/^\xEF\xBB\xBF//" /usr/local/etc/php-fpm.d/zz-www.conf

# Salin source aplikasi
COPY . /var/www/html

# Salin hasil build & vendor dari stage sebelumnya
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Selesaikan autoload & discovery paket (butuh source lengkap)
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint (hapus BOM UTF-8 dan CR agar shebang terbaca kernel Linux)
COPY docker/scripts/entrypoint.sh /usr/local/bin/entrypoint
RUN sed -i "1s/^\xEF\xBB\xBF//" /usr/local/bin/entrypoint \
    && sed -i "s/\r$//" /usr/local/bin/entrypoint \
    && chmod +x /usr/local/bin/entrypoint

# Nginx perlu direktori runtime
RUN mkdir -p /var/log/supervisor /run/nginx

EXPOSE 80

ENTRYPOINT ["entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
