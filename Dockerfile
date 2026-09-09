# ---------------------------------------------------------------------------
# TurneroPro - imagen de producción (nginx + php-fpm) para Render (Docker)
# ---------------------------------------------------------------------------

# --- Etapa 1: compila los assets con Vite -----------------------------------
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# --- Etapa 2: imagen de ejecución --------------------------------------------
FROM php:8.5-fpm-alpine

WORKDIR /var/www/html

# Dependencias del sistema + extensiones de PHP
# opcache ya viene compilado en estático en PHP 8.5: se habilita con ini.
RUN apk add --no-cache nginx oniguruma-dev libzip-dev \
    && docker-php-ext-install mbstring zip \
    && { \
        printf 'zend_extension=opcache\nopcache.enable=1\nopcache.validate_timestamps=0\n'; \
    } > "$PHP_INI_DIR/conf.d/zz-opcache.ini"

# Composer global (imagen oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dependencias de PHP en capa cacheable (se reusa si composer.lock no cambia)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copia el código de la aplicación (ver .dockerignore)
COPY . .

# Assets ya compilados (public/build) desde la etapa de build
COPY --from=assets /app/public/build public/build

# Configuración de nginx para Laravel
RUN rm -f /etc/nginx/http.d/default.conf
COPY deploy/nginx.conf /etc/nginx/http.d/laravel.conf

# Nunca empaquetar el .env local
RUN rm -f .env \
    && chmod +x deploy.sh \
    && mkdir -p storage/logs storage/framework/cache/data \
        storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chown -R www-data:www-data database

EXPOSE 80

STOPSIGNAL SIGTERM

CMD ["/bin/bash", "/var/www/html/deploy.sh"]