# Multi-stage build для production
FROM node:18-alpine AS frontend-builder
WORKDIR /app/frontend
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ ./
RUN npm run build

FROM php:8.3-fpm-alpine AS php-builder
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    nginx \
    netcat-openbsd \
    autoconf \
    g++ \
    make \
    hiredis-dev

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd intl zip

# Установка Redis расширения для PHP
# Используем hiredis-dev для компиляции Redis расширения
RUN set -eux; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    php -r "if (!extension_loaded('redis')) { exit(1); }"; \
    php -m | grep -i redis

# Удаление временных зависимостей для уменьшения размера образа
RUN apk del autoconf g++ make hiredis-dev || true
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копирование backend
COPY backend/ ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Копирование собранного фронтенда
COPY --from=frontend-builder /app/frontend/dist /var/www/html/public/dist

# Копирование Nginx конфига
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Настройка прав
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Экспозиция порта
EXPOSE 80

# Скрипт запуска для production
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]

