#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

echo "[deploy] preparando almacenamiento"
mkdir -p storage/logs storage/framework/cache/data \
    storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache database

echo "[deploy] creando base de datos sqlite"
touch database/database.sqlite

echo "[deploy] generando APP_KEY si falta"
if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY="$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')"
    echo "[deploy] APP_KEY generada en env (fallback)"
fi

echo "[deploy] cacheando configuración"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[deploy] enlace de almacenamiento público"
[ -L public/storage ] || php artisan storage:link

echo "[deploy] migrando y sembrando datos demo"
php artisan migrate --force --seed

echo "[deploy] iniciando php-fpm y nginx"
php-fpm -D
nginx -g 'daemon off;'