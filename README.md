# Маркетинг-Планер

Веб-приложение для генерации персонализированного маркетингового плана на основе анкеты пользователя.

## Архитектура

Проект разделен на независимые **backend** и **frontend** приложения:

```
marketing-planner/
├── backend/                 # Laravel 10 API
│   ├── app/
│   │   ├── Models/          # Eloquent модели
│   │   ├── Http/Controllers/API/  # API контроллеры
│   │   ├── Services/        # Бизнес-логика
│   │   └── Filament/        # Админка
│   ├── database/
│   │   ├── migrations/      # Миграции БД
│   │   └── seeders/         # Сидеры
│   └── routes/api.php       # API маршруты
│
├── frontend/                # Vue 3 SPA
│   ├── src/
│   │   ├── components/      # Переиспользуемые компоненты
│   │   ├── pages/          # Страницы приложения
│   │   ├── router/         # Vue Router
│   │   └── stores/         # Pinia хранилища
│   └── package.json
│
└── docker/                  # Docker конфигурация
    ├── php/
    ├── nginx/
    └── postgres/
```

## Стек технологий

### Backend
- **Laravel 10** - PHP фреймворк
- **PostgreSQL** - База данных
- **Redis** - Кэш и очереди
- **Laravel Sanctum** - API аутентификация
- **Filament 3** - Админ панель

### Frontend
- **Vue 3** - JavaScript фреймворк
- **Vue Router** - Маршрутизация
- **Pinia** - Управление состоянием
- **Tailwind CSS** - Стилизация
- **Axios** - HTTP клиент

### DevOps
- **Docker** - Контейнеризация
- **Nginx** - Веб-сервер
- **Vite** - Сборщик фронтенда

## Быстрый старт

### 1. Клонирование и настройка

```bash
git clone <repository-url>
cd marketing-planner

# Настройка backend
cp backend/.env.example backend/.env
# Отредактируйте backend/.env файл

# Настройка frontend (при необходимости)
cp frontend/.env.example frontend/.env
```

### 2. Запуск с Docker

```bash
# Сборка и запуск всех сервисов
docker-compose up -d --build

# Настройка backend
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed --class=TaskSeeder

# Создание админа для Filament
docker-compose exec app php artisan make:filament-user
```

### 3. Доступ к приложениям

- **Frontend (Vue SPA):** http://localhost:3000
- **Backend API:** http://localhost:8000/api
- **Админка Filament:** http://localhost:8000/admin

## API Endpoints

### Аутентификация
```
POST /api/register          # Регистрация
POST /api/login             # Вход
POST /api/logout            # Выход
GET  /api/user              # Текущий пользователь
```

### Планы
```
POST /api/questionnaire     # Создание плана из анкеты
GET  /api/plans             # Список планов пользователя
GET  /api/plan/{id}         # Детали плана
PUT  /api/plan/{planId}/plan-task/{planTaskId}  # Обновление статуса задачи
```

### Задачи
```
GET  /api/tasks             # Список всех задач (с фильтрами)
GET  /api/tasks/{id}        # Детали задачи
```

## Разработка

### Backend разработка

```bash
cd backend

# Установка зависимостей
composer install

# Запуск сервера разработки
php artisan serve

# Миграции
php artisan migrate

# Сидеры
php artisan db:seed

# Очереди
php artisan queue:work
```

### Frontend разработка

```bash
cd frontend

# Установка зависимостей
npm install

# Запуск dev сервера
npm run dev

# Сборка для продакшена
npm run build
```

### Добавление новых задач

1. Обновите `backend/database/seeders/TaskSeeder.php`
2. Выполните `php artisan db:seed --class=TaskSeeder`

#### Импорт Hashtag-чеклистов

```
php artisan import:hashtags /absolute/path/to/Hashtags
```

- Принимает путь к директории или конкретному `.txt` файлу из экспортируемых шпаргалок.
- Имя файла должно содержать индустрию и страну, например `Hashtag List - Beauty Salon - UK.txt`.
- Для каждой пары индустрия/страна создаются блоки Local/Broad/Industry/Niche/Branded, хранящиеся в таблице `hashtags`.

### Изменение алгоритма планирования

Основная логика в `backend/app/Services/PlanGeneratorService.php`

## Модели данных

### User
- Стандартная модель Laravel
- Связь: `hasMany(Plan)`

### Plan
- Маркетинговый план пользователя
- Связи: `belongsTo(User)`, `belongsToMany(Task)`

### Task  
- Задача маркетингового плана
- Связь: `belongsToMany(Plan)`

### PlanTask (pivot)
- Связь плана и задачи
- Поля: `plan_id`, `task_id`, `week`, `completed`, `notes`

## Конфигурация

### Backend (.env)
```env
APP_URL=http://localhost:8000
DB_CONNECTION=pgsql
DB_HOST=postgres
SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

### Frontend (vite.config.js)
```js
server: {
  proxy: {
    '/api': 'http://localhost:8000'
  }
}
```

## Демонстрация проекта (деплой на Railway)

Для демонстрации проекта глобально используйте Railway - бесплатный облачный хостинг.

**Быстрый старт:**
1. Зарегистрируйтесь на https://railway.app (через GitHub)
2. Создайте новый проект и подключите ваш репозиторий
3. Добавьте PostgreSQL и Redis плагины
4. Настройте переменные окружения (см. `DEPLOY.md`)
5. Railway автоматически задеплоит проект и выдаст домен

Подробная инструкция: см. [`DEPLOY.md`](DEPLOY.md)

**Альтернативы для демонстрации:**
- **Render.com** - аналогично Railway, бесплатный тариф
- **Fly.io** - поддерживает Docker, бесплатный тариф
- **VPS** (DigitalOcean, Hetzner) - для полного контроля

## Производственное развертывание

Для полноценного продакшена:
1. Настройте переменные окружения для продакшена
2. Соберите фронтенд: `npm run build`
3. Оптимизируйте backend: `php artisan optimize`
4. Настройте веб-сервер (Nginx/Apache)
5. Настройте SSL сертификаты
6. Настройте мониторинг и резервное копирование

## Лицензия

MIT License