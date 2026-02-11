#!/bin/sh

# Railway устанавливает PORT переменную, используем её или 80 по умолчанию
export PORT=${PORT:-80}
echo "Starting on port: $PORT"

# Ожидание PostgreSQL (если DB_HOST установлен)
if [ -n "$DB_HOST" ]; then
  DB_PORT_VAR="${DB_PORT:-5432}"
  echo "Waiting for PostgreSQL at $DB_HOST:${DB_PORT_VAR}..."
  until nc -z "$DB_HOST" "$DB_PORT_VAR"; do
    echo "PostgreSQL is unavailable - sleeping"
    sleep 1
  done
  echo "PostgreSQL is up!"
  echo "Waiting 5s for PostgreSQL to accept application connections..."
  sleep 5
else
  echo "WARNING: DB_HOST not set, skipping database connection check"
fi

# Запуск миграций с повторами (БД/прокси Railway иногда обрывает первый запрос)
if [ -n "$DB_HOST" ]; then
  echo "Running migrations..."
  MIGRATE_ATTEMPTS=1
  while [ "$MIGRATE_ATTEMPTS" -le 5 ]; do
    if php artisan migrate --force; then
      echo "Migrations completed."
      break
    fi
    echo "Migration attempt $MIGRATE_ATTEMPTS failed, retrying in 10s..."
    sleep 10
    MIGRATE_ATTEMPTS=$((MIGRATE_ATTEMPTS + 1))
  done
  if [ "$MIGRATE_ATTEMPTS" -gt 5 ]; then
    echo "Migration failed after 5 attempts, continuing..."
  fi
else
  echo "Skipping migrations - no DB_HOST"
fi

# Создание файла логов Laravel, если его нет, и установка прав
echo "Ensuring Laravel log file exists and is writable..."
mkdir -p /var/www/html/storage/logs
touch /var/www/html/storage/logs/laravel.log
chmod 666 /var/www/html/storage/logs/laravel.log
chown www-data:www-data /var/www/html/storage/logs/laravel.log
echo "Laravel log file ready"

# Создание директорий для сессий и кеша (для SESSION_DRIVER=file)
echo "Ensuring session and cache directories exist and are writable..."
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/views
chmod -R 775 /var/www/html/storage/framework
chown -R www-data:www-data /var/www/html/storage/framework
echo "Session and cache directories ready"

# Директории для Livewire uploads и ресурсов
echo "Ensuring Livewire upload and resources directories exist and are writable..."
mkdir -p /var/www/html/storage/app/livewire-tmp
mkdir -p /var/www/html/storage/app/resources/en
mkdir -p /var/www/html/storage/app/resources/de
chmod -R 775 /var/www/html/storage/app
chown -R www-data:www-data /var/www/html/storage/app
echo "Livewire upload and resources directories ready"

# Проверка PHP расширения Redis
echo "Checking PHP Redis extension..."
if php -m | grep -i redis > /dev/null 2>&1; then
  echo "✓ PHP Redis extension is loaded"
else
  echo "✗ WARNING: PHP Redis extension is NOT loaded"
  echo "Redis functionality will not work until extension is installed"
fi

# Проверка подключения к Redis (если SESSION_DRIVER=redis или CACHE_DRIVER=redis)
if [ "$SESSION_DRIVER" = "redis" ] || [ "$CACHE_DRIVER" = "redis" ]; then
  REDIS_HOST_VAR="${REDIS_HOST:-$REDISHOST}"
  REDIS_PORT_VAR="${REDIS_PORT:-$REDISPORT:-6379}"
  
  if [ -n "$REDIS_HOST_VAR" ]; then
    echo "Checking Redis connection at ${REDIS_HOST_VAR}:${REDIS_PORT_VAR}..."
    if nc -z ${REDIS_HOST_VAR} ${REDIS_PORT_VAR} 2>/dev/null; then
      echo "✓ Redis server is available at ${REDIS_HOST_VAR}:${REDIS_PORT_VAR}"
    else
      echo "✗ WARNING: Cannot connect to Redis at ${REDIS_HOST_VAR}:${REDIS_PORT_VAR}"
      echo "Redis may not be accessible or variables are incorrect"
    fi
  else
    echo "WARNING: SESSION_DRIVER or CACHE_DRIVER is redis but REDIS_HOST is not set"
    echo "Consider setting SESSION_DRIVER=file or CACHE_DRIVER=file if Redis is not needed"
  fi
fi

# Минимум для старта Laravel (обязательно до приёма трафика)
if [ -n "$APP_KEY" ]; then
  echo "Running composer scripts (package:discover)..."
  composer dump-autoload --optimize --no-interaction 2>&1 || echo "Autoload dump failed, continuing..."
  php artisan package:discover --ansi 2>&1 || echo "Package discover failed, continuing..."
else
  echo "WARNING: APP_KEY not set, skipping composer scripts"
fi

# Настройка Laravel для показа ошибок в production (для отладки)
if [ "$APP_DEBUG" = "true" ]; then
  echo "APP_DEBUG is true - errors will be shown"
else
  echo "APP_DEBUG is false - checking log file location"
  echo "Laravel log file: /var/www/html/storage/logs/laravel.log"
fi

# Настройка PHP-FPM для логирования ошибок в stderr
echo "Configuring PHP-FPM error logging..."
mkdir -p /var/www/html/storage/logs
# Создаем php.ini для PHP-FPM с выводом ошибок в stderr
echo "error_log = /dev/stderr" > /usr/local/etc/php/conf.d/error-log.ini
echo "log_errors = On" >> /usr/local/etc/php/conf.d/error-log.ini
echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/error-log.ini

# Запуск PHP-FPM в фоне
php-fpm -D

# Обновление Nginx конфига для использования правильного порта
echo "Updating Nginx config to use port $PORT..."
sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/http.d/default.conf
sed -i "s/listen \[::\]:80;/listen [::]:$PORT;/g" /etc/nginx/http.d/default.conf 2>/dev/null || true
echo "Nginx config updated. Current config:"
grep "listen" /etc/nginx/http.d/default.conf

# Проверка конфигурации Nginx
echo "Checking Nginx configuration..."
nginx -t || {
  echo "ERROR: Nginx configuration test failed!"
  echo "Full Nginx config:"
  cat /etc/nginx/http.d/default.conf
  exit 1
}

echo "Nginx configuration OK"
echo "Starting Nginx on port $PORT..."

# Проверяем что PHP-FPM работает (проверяем несколькими способами)
PHP_FPM_PID=$(pgrep -f php-fpm || ps aux | grep '[p]hp-fpm: master' | awk '{print $2}' || echo "")
if [ -z "$PHP_FPM_PID" ]; then
  echo "ERROR: PHP-FPM is not running!"
  echo "Checking processes:"
  ps aux | grep php-fpm || echo "No php-fpm processes found"
  exit 1
fi
echo "PHP-FPM is running (PID: $PHP_FPM_PID)"

(
  echo "[background] Post-start tasks started..."
  if [ -n "$DB_HOST" ]; then
    HASHTAG_COUNT=$(php artisan tinker --execute="echo (string)\DB::table('hashtags')->count();" 2>/dev/null | grep -oE '^[0-9]+$' | head -1 || echo "0")
    if [ -z "$HASHTAG_COUNT" ] || [ "$HASHTAG_COUNT" = "0" ]; then
      php artisan db:seed --force 2>&1 || true
    fi
  fi
  if [ -n "$APP_KEY" ]; then
    php artisan filament:upgrade --ansi 2>&1 || true
    php artisan vendor:publish --tag=filament-assets --force 2>&1 || true
    php artisan vendor:publish --tag=livewire:config --force 2>&1 || true
    php artisan vendor:publish --tag=livewire:assets --force 2>&1 || true
    php artisan config:cache 2>&1 || true
    php artisan route:cache 2>&1 || true
    php artisan view:cache 2>&1 || true
  fi
  if [ -d "/var/www/html/public/dist" ]; then
    chown -R www-data:www-data /var/www/html/public/dist 2>/dev/null || true
  fi
  echo "[background] Post-start tasks finished"
) &

echo "Nginx will listen on 0.0.0.0:$PORT"
exec nginx -g 'daemon off;' 2>&1

