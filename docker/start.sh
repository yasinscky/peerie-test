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

# Проверка существования файлов фронтенда
echo "Checking frontend files..."
if [ -f "/var/www/html/public/dist/index.html" ]; then
  echo "Frontend index.html found"
else
  echo "ERROR: Frontend index.html NOT found at /var/www/html/public/dist/index.html"
  echo "Checking public directory:"
  ls -la /var/www/html/public/ || echo "public directory not found"
fi

# Проверка прав доступа
echo "Checking permissions..."
ls -la /var/www/html/public/dist/ 2>/dev/null || echo "dist directory not found"

# Исправление прав доступа для фронтенда и родительских директорий
if [ -d "/var/www/html/public/dist" ]; then
  echo "Fixing permissions for frontend files..."
  # Убеждаемся что все родительские директории доступны
  chmod 755 /var/www/html
  chmod 755 /var/www/html/public
  chmod 755 /var/www/html/public/dist
  # Права на файлы и директории
  find /var/www/html/public/dist -type d -exec chmod 755 {} \;
  find /var/www/html/public/dist -type f -exec chmod 644 {} \;
  # Владелец
  chown -R www-data:www-data /var/www/html/public/dist
  echo "Permissions fixed"
  echo "Verifying access to index.html:"
  ls -la /var/www/html/public/dist/index.html
fi

# Настройка Laravel для показа ошибок в production (для отладки)
if [ "$APP_DEBUG" = "true" ]; then
  echo "APP_DEBUG is true - errors will be shown"
else
  echo "APP_DEBUG is false - checking log file location"
  echo "Laravel log file: /var/www/html/storage/logs/laravel.log"
fi

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

# Проверяем что порт свободен
if netstat -tuln | grep -q ":$PORT "; then
  echo "WARNING: Port $PORT is already in use!"
  netstat -tuln | grep ":$PORT "
fi

# Запуск Nginx с логированием
echo "Starting Nginx in foreground mode..."
echo "Nginx will listen on 0.0.0.0:$PORT"

# Показываем последние строки логов Laravel перед запуском (если есть)
if [ -f "/var/www/html/storage/logs/laravel.log" ]; then
  echo "Last 20 lines of Laravel log:"
  tail -20 /var/www/html/storage/logs/laravel.log
fi

# Запуск Nginx (daemon off - запускает в foreground, exec заменяет shell процесс)
exec nginx -g 'daemon off;' 2>&1

