FROM nginx:alpine

# Копирование конфигурации Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Создание директории для логов
RUN mkdir -p /var/log/nginx

# Экспозиция порта
EXPOSE 80

# Запуск Nginx
CMD ["nginx", "-g", "daemon off;"]
