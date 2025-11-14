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

# Запуск миграций и сидеров (если есть подключение к БД)
if [ -n "$DB_HOST" ]; then
  echo "Running migrations..."
  php artisan migrate --force || echo "Migration failed, continuing..."
  
  # Запуск сидеров для заполнения начальными данными (хештеги, задачи)
  # Проверяем, есть ли уже данные - чтобы не заполнять повторно
  echo "Checking if database needs seeding..."
  # Используем прямой SQL запрос для проверки (быстрее и надежнее чем tinker)
  HASHTAG_COUNT=$(php artisan db:show --counts 2>/dev/null | grep -i hashtags | awk '{print $NF}' || echo "0")
  if [ "$HASHTAG_COUNT" = "0" ] || [ -z "$HASHTAG_COUNT" ] || [ "$HASHTAG_COUNT" = "0" ]; then
    echo "Database is empty, running seeders..."
    php artisan db:seed --force || echo "Seeding failed, continuing..."
  else
    echo "Database already has data ($HASHTAG_COUNT hashtags), skipping seeders"
  fi
else
  echo "Skipping migrations and seeders - no DB_HOST"
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

# Выполнение composer scripts после настройки окружения (только если APP_KEY установлен)
if [ -n "$APP_KEY" ]; then
  echo "Running composer scripts (package:discover)..."
  composer dump-autoload --optimize --no-interaction 2>&1 || echo "Autoload dump failed, continuing..."
  php artisan package:discover --ansi 2>&1 || echo "Package discover failed, continuing..."
  
  echo "Caching configuration..."
  php artisan config:cache || echo "Config cache failed, continuing..."
  php artisan route:cache || echo "Route cache failed, continuing..."
  php artisan view:cache || echo "View cache failed, continuing..."
else
  echo "WARNING: APP_KEY not set, skipping composer scripts and cache operations"
fi

# Проверка существования файлов фронтенда
echo "Checking frontend files..."
if [ -f "/var/www/html/public/dist/index.html" ]; then
  echo "Frontend index.html found"
  echo "Checking assets directory:"
  ls -la /var/www/html/public/dist/assets/ 2>/dev/null | head -10 || echo "assets directory not found"
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
         LOG_SIZE=$(stat -c%s /var/www/html/storage/logs/laravel.log 2>/dev/null || echo "0")
         if [ "$LOG_SIZE" -gt 0 ]; then
           echo "Last 50 lines of Laravel log:"
           tail -50 /var/www/html/storage/logs/laravel.log
         else
           echo "Laravel log file exists but is empty (no errors logged yet)"
         fi
       else
         echo "WARNING: Laravel log file not found at /var/www/html/storage/logs/laravel.log"
         echo "Checking if directory exists:"
         ls -la /var/www/html/storage/logs/ 2>/dev/null || echo "logs directory not found"
       fi
       
       # Функция для периодической проверки логов (будет вызвана через некоторое время после запуска)
       echo "Note: If errors occur, check Laravel logs with: tail -f /var/www/html/storage/logs/laravel.log"
       
       # Показываем логи PHP если есть
       if [ -f "/var/www/html/storage/logs/php-errors.log" ]; then
         echo "Last 20 lines of PHP errors:"
         tail -20 /var/www/html/storage/logs/php-errors.log
       else
         echo "No PHP errors log found yet"
       fi
       
       # Проверяем права на storage/logs
       echo "Checking storage/logs permissions:"
       ls -la /var/www/html/storage/logs/ 2>/dev/null || echo "logs directory not found"
       
       # Проверяем важные переменные окружения
       echo "Checking environment variables:"
       echo "APP_KEY is set: $([ -n "$APP_KEY" ] && echo 'YES' || echo 'NO')"
       echo "DB_HOST is set: $([ -n "$DB_HOST" ] && echo 'YES' || echo 'NO')"
       echo "SESSION_DRIVER: ${SESSION_DRIVER:-not set}"

# Проверка что файл index.html действительно доступен для чтения
echo "Testing file access as www-data user:"
su -s /bin/sh -c "test -r /var/www/html/public/dist/index.html && echo 'File is readable' || echo 'File is NOT readable'" www-data || echo "Could not test as www-data"

# Показываем текущую конфигурацию Nginx для отладки
echo "Current Nginx config for location /:"
grep -A 10 "location /" /etc/nginx/http.d/default.conf | head -15

# Запуск Nginx (daemon off - запускает в foreground, exec заменяет shell процесс)
# Логи Nginx будут видны в Railway через stderr/stdout
echo "Starting Nginx - logs will appear below:"
# Запускаем Nginx с выводом ошибок
exec nginx -g 'daemon off;' 2>&1

