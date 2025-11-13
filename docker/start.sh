#!/bin/sh

# Railway устанавливает PORT переменную, используем её или 80 по умолчанию
export PORT=${PORT:-80}
echo "Starting on port: $PORT"

# Ожидание PostgreSQL (если DB_HOST установлен)
if [ -n "$DB_HOST" ]; then
  echo "Waiting for PostgreSQL at $DB_HOST:5432..."
  until nc -z $DB_HOST 5432; do
    echo "PostgreSQL is unavailable - sleeping"
    sleep 1
  done
  echo "PostgreSQL is up!"
else
  echo "WARNING: DB_HOST not set, skipping database connection check"
fi

# Запуск миграций (если есть подключение к БД)
if [ -n "$DB_HOST" ]; then
  echo "Running migrations..."
  php artisan migrate --force || echo "Migration failed, continuing..."
else
  echo "Skipping migrations - no DB_HOST"
fi

# Оптимизация Laravel (только если APP_KEY установлен)
if [ -n "$APP_KEY" ]; then
  echo "Caching configuration..."
  php artisan config:cache || echo "Config cache failed, continuing..."
  php artisan route:cache || echo "Route cache failed, continuing..."
  php artisan view:cache || echo "View cache failed, continuing..."
else
  echo "WARNING: APP_KEY not set, skipping cache operations"
fi

# Запуск PHP-FPM в фоне
php-fpm -D

# Обновление Nginx конфига для использования правильного порта
sed -i "s/listen 80/listen $PORT/g" /etc/nginx/http.d/default.conf

# Запуск Nginx
nginx -g 'daemon off;'

