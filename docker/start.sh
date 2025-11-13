#!/bin/sh

# Ожидание PostgreSQL (если DB_HOST установлен)
if [ -n "$DB_HOST" ]; then
  echo "Waiting for PostgreSQL at $DB_HOST:5432..."
  until nc -z $DB_HOST 5432; do
    echo "PostgreSQL is unavailable - sleeping"
    sleep 1
  done
  echo "PostgreSQL is up!"
fi

# Запуск миграций
php artisan migrate --force

# Оптимизация Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Запуск PHP-FPM в фоне
php-fpm -D

# Запуск Nginx
nginx -g 'daemon off;'

