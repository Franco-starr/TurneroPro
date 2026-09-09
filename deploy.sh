#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

echo "[deploy] preparando almacenamiento"
mkdir -p storage/logs storage/framework/cache/data \
    storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache database

echo "[deploy] creando base de datos sqlite"
touch database/database.sqlite

echo "[deploy] verificando APP_KEY"
if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY="$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')"
    echo "[deploy] APP_KEY generada en env (no venía seteada)"
elif ! php -r '
    $key = getenv("APP_KEY") ?: "";
    $ok = ($key !== "" && strlen($key) === 32) ||
        (str_starts_with($key, "base64:") && in_array(strlen(base64_decode(substr($key, 7), true)), [16, 24, 32], true));
    exit($ok ? 0 : 1);
'; then
    export APP_KEY="$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')"
    echo "[deploy] APP_KEY con formato inválido -> regenerada"
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