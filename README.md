<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# CodeIntruder Challenge - Dockerized Laravel Environment

A fully dockerized Laravel 12 application with Laravel Telescope for monitoring and debugging. This project uses a single-container architecture with Supervisor managing multiple services.

## 🚀 Features

- **Laravel 12** with PHP 8.3-FPM
- **Laravel Telescope** with separate environment configuration
- **Multi-Database Architecture**: Three separate databases for migrations, application tables, and Telescope data
- **Redis** for caching and queues
- **Nginx** as web server
- **Supervisor** managing all processes (PHP-FPM, Nginx, Redis, Queue Worker, Scheduler)
- **Docker Compose** for easy setup and deployment

## 📋 Prerequisites

- Docker Engine 20.10 or higher
- Docker Compose 2.0 or higher
- MySQL database (can be external or containerized)

## 🛠️ Project Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd CodeIntruderChallenge
```

### 2. Environment Configuration

Copy the example environment files and configure them:

```bash
cp .env.example .env
cp .env.telescope.example .env.telescope
```

### 3. Configure Database Connection

Edit `.env` and update the database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=db
DB_LARAVEL_DATABASE=laravel-db
DB_TELESCOPE_DATABASE=telescope-db
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

**Note**: This project uses three separate databases:
- `db`: Stores migration records
- `laravel-db`: Stores application tables (cache, jobs, sessions)
- `telescope-db`: Stores Laravel Telescope monitoring data

### 4. Generate Application Key

```bash
docker compose run --rm app php artisan key:generate
```

### 5. Build and Start Containers

```bash
docker compose up -d --build
```

This will:
- Build the Docker image with all dependencies
- Start all services (PHP-FPM, Nginx, Redis, Queue Worker, Scheduler)
- Expose the application on the port configured in `APP_PORT` (default: 8000)

### 6. Run Database Migrations

```bash
docker compose exec app php artisan migrate
```

### 7. Set Permissions

Ensure storage and cache directories have proper permissions:

```bash
docker compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

## 🌐 Accessing the Application

- **Main Application**: http://localhost:8000
- **Laravel Telescope**: http://localhost:8000/telescope

## 🏗️ Architecture

### Services Running in Container

The single container runs multiple services managed by Supervisor:

1. **PHP-FPM**: Processes PHP requests (priority: 5)
2. **Nginx**: Web server listening on configured APP_PORT (priority: 10)
3. **Redis**: In-memory data store for cache and queues (priority: 5)
4. **Laravel Queue Worker**: Processes background jobs
5. **Laravel Scheduler**: Handles scheduled tasks

### Database Architecture

```
┌─────────────────┐
│   db database   │  → Migrations table
└─────────────────┘

┌─────────────────┐
│ laravel-db      │  → Cache, Jobs, Sessions tables
└─────────────────┘

┌─────────────────┐
│ telescope-db    │  → Telescope monitoring tables
└─────────────────┘
```

## 🔧 Common Commands

### View Container Logs

```bash
docker compose logs app -f
```

### Access Container Shell

```bash
docker compose exec app bash
```

### Run Artisan Commands

```bash
docker compose exec app php artisan <command>
```

### Check Service Status

```bash
docker compose exec app supervisorctl status
```

### Clear Caches

```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear
```

### Run Tests

```bash
docker compose exec app php artisan test
```

### Stop Containers

```bash
docker compose down
```

### Rebuild Containers

```bash
docker compose down
docker compose up -d --build
```

## 📊 Laravel Telescope

Laravel Telescope is pre-configured with its own environment file (`.env.telescope`) and database connection. Access the dashboard at:

```
http://localhost:8000/telescope
```

### Telescope Features

- Request monitoring
- Query debugging
- Job tracking
- Cache operations
- Redis commands
- Exception tracking
- Log monitoring
- Schedule tracking

### Telescope Configuration

Modify `.env.telescope` to customize:
- Enable/disable specific watchers
- Configure ignored paths
- Set data retention (pruning hours)
- Toggle queue monitoring

## 🔒 Security Notes

- Never commit `.env` or `.env.telescope` files to version control
- Update `APP_KEY` in production environments
- Use strong database passwords
- Restrict Telescope access in production (configure `gate()` in `TelescopeServiceProvider`)

## 🐛 Troubleshooting

### Container Won't Start

Check logs for errors:
```bash
docker compose logs app
```

### Permission Denied Errors

Reset permissions:
```bash
docker compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

### Database Connection Issues

Verify database credentials in `.env` and ensure all three databases exist on your MySQL server.

### Port Already in Use

Change `APP_PORT` in `.env` to an available port and restart:
```bash
docker compose down
docker compose up -d
```

## 📚 Additional Documentation

- [Multi-Database Setup Guide](MULTI-DATABASE-SETUP.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Telescope Documentation](https://laravel.com/docs/telescope)
- [Docker Documentation](https://docs.docker.com)

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## 📝 Environment Variables Reference

### Application Configuration

- `APP_NAME`: Application name
- `APP_ENV`: Environment (local, production, staging)
- `APP_KEY`: Application encryption key (generated by `php artisan key:generate`)
- `APP_DEBUG`: Enable debug mode (true/false)
- `APP_URL`: Application URL
- `APP_PORT`: Port for the application to listen on (default: 8000)

### Database Configuration

- `DB_CONNECTION`: Database driver (mysql)
- `DB_HOST`: Database host address
- `DB_PORT`: Database port (default: 3306)
- `DB_DATABASE`: Main database name (stores migrations table) for actual data of the application.
- `DB_LARAVEL_DATABASE`: Laravel tables database (stores cache, jobs, sessions)
- `DB_TELESCOPE_DATABASE`: Telescope database (stores monitoring data)
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password

### Session Configuration

- `SESSION_DRIVER`: Session storage driver (database)
- `SESSION_CONNECTION`: Database connection for sessions (laravel)
- `SESSION_LIFETIME`: Session lifetime in minutes

### Cache & Queue Configuration

- `CACHE_STORE`: Cache driver (redis)
- `CACHE_PREFIX`: Cache key prefix
- `QUEUE_CONNECTION`: Queue driver (redis)
- `REDIS_HOST`: Redis server host
- `REDIS_PORT`: Redis server port (default: 6379)

### Telescope Configuration

- `TELESCOPE_ENABLED`: Enable/disable Telescope
- `TELESCOPE_PATH`: Telescope dashboard path (default: telescope)
- `TELESCOPE_DB_CONNECTION`: Database connection for Telescope (telescope)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
