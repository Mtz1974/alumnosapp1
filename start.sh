#!/bin/sh
set -e

# Iniciar PHP-FPM en background
php-fpm -D

# Variables mínimas
: "${APP_KEY:?Falta APP_KEY}"
: "${APP_ENV:=production}"
: "${APP_DEBUG:=false}"

# Tareas Laravel
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

php artisan storage:link || true

# Migraciones (seed opcional)
php artisan migrate --force
# php artisan db:seed --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar Nginx en foreground
nginx -g 'daemon off;'
