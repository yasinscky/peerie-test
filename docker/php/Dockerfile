FROM php:8.3-fpm-alpine

# Установка системных зависимостей
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    nodejs \
    npm \
    icu-dev \
    libzip-dev

# Установка PHP расширений
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd intl zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/html

# Копирование файлов приложения
COPY . .

# Установка зависимостей
RUN composer install --no-dev --optimize-autoloader

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Настройка PHP-FPM для прослушивания на всех интерфейсах (для Docker)
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf || \
    sed -i 's/listen = \/run\/php\/php-fpm.sock/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

# Экспозиция порта
EXPOSE 9000

# Запуск PHP-FPM
CMD ["php-fpm"]
