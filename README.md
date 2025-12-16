# Peerie

Web application for generating personalized marketing plans based on user questionnaires. Features include monthly task generation, image search, hashtag suggestions, content ideas calendar, and community integration.

## Architecture

The project is split into independent **backend** and **frontend** applications:

```
peerie/
├── backend/                 # Laravel 12 API
│   ├── app/
│   │   ├── Models/          # Eloquent models
│   │   ├── Http/Controllers/API/  # API controllers
│   │   ├── Services/        # Business logic
│   │   └── Filament/        # Admin panel
│   ├── database/
│   │   ├── migrations/      # Database migrations
│   │   └── seeders/         # Database seeders
│   └── routes/web.php       # API routes (mounted under /api)
│
├── frontend/                # Vue 3 SPA
│   ├── src/
│   │   ├── components/      # Reusable components
│   │   ├── pages/          # Application pages
│   │   ├── router/         # Vue Router
│   │   └── stores/         # Pinia stores
│   └── package.json
│
└── docker/                  # Docker configuration
    ├── php/
    ├── nginx/
    └── postgres/
```

## Tech Stack

### Backend
- **Laravel 12** - PHP framework
- **PHP 8.4** - Runtime (Docker)
- **PostgreSQL 15** - Database (Docker)
- **Redis** - Cache and queues
- **Laravel Sanctum 4** - API authentication
- **Filament 3** - Admin panel

### Frontend
- **Vue 3** - JavaScript framework
- **Vue Router** - Routing
- **Pinia** - State management
- **Tailwind CSS** - Styling
- **Axios** - HTTP client
- **Vite 6** - Build tool

### DevOps
- **Docker** - Containerization
- **Nginx** - Web server
- **Node.js 24** - Frontend runtime (Docker)

## Quick Start

### 1. Clone and Setup

```bash
git clone <repository-url>
cd peerie

# Backend setup
cp backend/.env.example backend/.env
# Edit backend/.env file with your configuration

# Frontend setup (if needed)
cp frontend/.env.example frontend/.env
```

### 2. Run with Docker

```bash
# Build and start all services
docker compose up -d --build

# Backend setup
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --class=TaskSeeder

# Create admin user for Filament
docker compose exec app php artisan make:filament-user

# Optional: public storage symlink
docker compose exec app php artisan storage:link
```

### 3. Access Applications

- **Frontend (Vue SPA):** http://localhost:3000
- **Backend API:** http://localhost:8000/api
- **Filament Admin Panel:** http://localhost:8000/admin

## API Endpoints

### Authentication
```
POST /api/register                    # User registration
POST /api/login                       # User login
POST /api/logout                      # User logout
GET  /api/user                        # Get current authenticated user
POST /api/email/verify-registration   # Verify email with code
POST /api/email/resend-registration-code  # Resend verification code
```

### Plans
```
POST /api/questionnaire               # Create marketing plan from questionnaire
GET  /api/plans                       # Get list of user's plans
GET  /api/plan/{id}                   # Get plan details
GET  /api/plans/available-months      # Get available months for plan
POST /api/plans/generate-month        # Generate tasks for specific month
PUT  /api/plan/{planId}/plan-task/{planTaskId}  # Update task status
```

### Tasks
```
GET  /api/tasks                       # Get list of all tasks (with filters)
GET  /api/tasks/{id}                  # Get task details
```

### Images
```
POST /api/images/search               # Search images by query
POST /api/images/search/category      # Search images by category
GET  /api/images/popular              # Get popular images
GET  /api/images/{id}                 # Get image details
GET  /api/images/{id}/download        # Download image
POST /api/images/download-proxy      # Download image via proxy
```

### Hashtags
```
GET  /api/hashtags                    # Get hashtags for user's plan (by industry/country)
```

### Content Ideas
```
GET  /api/content-ideas/available-months     # Current and next month
GET  /api/content-ideas/by-date?date=YYYY-MM-DD  # Get idea for a specific date
```

### Profile
```
GET  /api/user                        # Get current user profile
PUT  /api/profile                     # Update user profile
PUT  /api/profile/password            # Update password
POST /api/profile/email/request-change    # Request email change
POST /api/profile/email/confirm-change     # Confirm email change with code
POST /api/profile/password/request-change  # Request password reset
```

### Community
```
GET  /api/discord/invite              # Get Discord community invite URL
```

## Features

- **Questionnaire-based Plan Generation**: Create personalized marketing plans from user questionnaires
- **Monthly Task Generation**: Automatically generate tasks for each month based on plan configuration
- **Image Search**: Search and download images from multiple sources (Unsplash, Pexels, Pixabay)
- **Hashtag Suggestions**: Get industry and country-specific hashtag recommendations
- **Content Ideas Calendar**: Daily content ideas with copyable caption and hashtags (managed via admin panel)
- **Task Management**: Track and complete marketing tasks with notes and completion status
- **Email Verification**: Secure email verification system for user registration
- **Profile Management**: Update profile, change email/password with verification
- **Community Integration**: Discord community access

## Development

### Backend Development

```bash
cd backend

# Install dependencies
composer install

# Run development server
php artisan serve

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Process queues
php artisan queue:work
```

### Frontend Development

```bash
cd frontend

# Install dependencies
npm install

# Run development server
npm run dev

# Build for production
npm run build
```

### Adding New Tasks

1. Update `backend/database/seeders/TaskSeeder.php`
2. Run `php artisan db:seed --class=TaskSeeder`

### Monthly Task Generation

Tasks are generated monthly for active plans. To generate tasks for a new month:

```bash
# Generate tasks for all active plans
php artisan tasks:generate-monthly

# Test monthly task generation for a specific plan
php artisan test:monthly-tasks {plan_id} --month={month} --year={year}
```

### Modifying Planning Algorithm

Main logic is located in `backend/app/Services/PlanGeneratorService.php`

### Image Services

The application supports multiple image services (Unsplash, Pexels, Pixabay) managed by `ImageServiceManager`. Configuration is in `backend/app/Services/ImageServiceManager.php`

## Data Models

### User
- Standard Laravel model
- Relationship: `hasMany(Plan)`

### Plan
- User's marketing plan
- Relationships: `belongsTo(User)`, `belongsToMany(Task)`

### Task  
- Marketing plan task
- Relationship: `belongsToMany(Plan)`

### PlanTask (pivot)
- Plan and task relationship
- Fields: `plan_id`, `task_id`, `week`, `year`, `month`, `completed`, `last_completed_at`, `notes`

### Hashtag
- Hashtag collections for industries and countries
- Fields: `industry`, `country`, `language`, `title`, `intro_title`, `intro_description`, `guidelines`, `tags`, `hashtag_blocks`
- Relationships: Used by Plan model to provide hashtag suggestions

### VerificationCode
- Email verification codes for user registration and email changes
- Fields: `user_id`, `code`, `type`, `expires_at`

## Configuration

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

## Demo Deployment (Railway)

For global project demonstration, use Railway - a free cloud hosting platform.

**Quick Start:**
1. Sign up at https://railway.app (via GitHub)
2. Create a new project and connect your repository
3. Add PostgreSQL and Redis plugins
4. Configure environment variables (see `DEPLOY.md`)
5. Railway will automatically deploy the project and provide a domain

Detailed instructions: see [`DEPLOY.md`](DEPLOY.md)

**Alternative demo platforms:**
- **Render.com** - Similar to Railway, free tier available
- **Fly.io** - Docker support, free tier available
- **VPS** (DigitalOcean, Hetzner) - For full control

## Production Deployment

For full production deployment:
1. Configure production environment variables
2. Build frontend: `npm run build`
3. Optimize backend: `php artisan optimize`
4. Configure web server (Nginx/Apache)
5. Set up SSL certificates
6. Configure monitoring and backups

## License

MIT License
