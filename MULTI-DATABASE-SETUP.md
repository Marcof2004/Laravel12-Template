# Multi-Database Setup for Laravel

## Overview
This Laravel application uses 3 separate databases:
1. **db** - Stores the migrations table only
2. **laravel-db** - Stores all application tables (cache, jobs, sessions, etc.)
3. **telescope-db** - Stores all Laravel Telescope monitoring tables

## SQL Commands to Create Databases

Run these commands on your MySQL server:

```sql
-- Create the databases
CREATE DATABASE IF NOT EXISTS `db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `laravel-db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `telescope-db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges to app-dev user
GRANT ALL PRIVILEGES ON `db`.* TO 'app-dev'@'%';
GRANT ALL PRIVILEGES ON `laravel-db`.* TO 'app-dev'@'%';
GRANT ALL PRIVILEGES ON `telescope-db`.* TO 'app-dev'@'%';
FLUSH PRIVILEGES;

-- Verify databases were created
SHOW DATABASES LIKE '%db%';
```

## Configuration Summary

### .env Configuration
```env
DB_CONNECTION=mysql                    # Default connection (for migrations table)
DB_HOST=
DB_PORT=
DB_DATABASE=db                         # migrations table goes here
DB_LARAVEL_DATABASE=laravel-db         # app tables go here
DB_TELESCOPE_DATABASE=telescope-db     # telescope tables go here
DB_USERNAME=app-dev
DB_PASSWORD=jxdz2J81tlM2DA
TELESCOPE_DB_CONNECTION=telescope
```

### Database Connections (config/database.php)
- **mysql** - Main connection → `db` database (migrations table)
- **laravel** - Application connection → `laravel-db` database (cache, jobs, etc.)
- **telescope** - Telescope connection → `telescope-db` database (telescope tables)

### Migration Setup
- **migrations table** - Uses default `mysql` connection → stored in `db`
- **cache & cache_locks** - Uses `laravel` connection → stored in `laravel-db`
- **jobs, job_batches, failed_jobs** - Uses `laravel` connection → stored in `laravel-db`
- **telescope_* tables** - Uses `telescope` connection → stored in `telescope-db`

## How to Run Migrations

```bash
# Clear config cache
docker compose exec app php artisan config:clear

# Run migrations (will create tables in appropriate databases)
docker compose exec app php artisan migrate

# The migrations will be distributed as follows:
# - migrations table → db
# - cache, cache_locks, jobs, job_batches, failed_jobs → laravel-db
# - telescope_entries, telescope_entries_tags, telescope_monitoring → telescope-db
```

## Database Table Distribution

### Database: `db`
- migrations

### Database: `laravel-db`
- cache
- cache_locks
- jobs
- job_batches
- failed_jobs
- sessions (if created)
- Any future app tables

### Database: `telescope-db`
- telescope_entries
- telescope_entries_tags
- telescope_monitoring

## Verification

After running migrations, verify the tables are in the correct databases:

```bash
# Check db database
docker compose exec app php artisan tinker
>>> DB::connection('mysql')->table('migrations')->count();

# Check laravel-db database
>>> DB::connection('laravel')->table('cache')->count();
>>> DB::connection('laravel')->table('jobs')->count();

# Check telescope-db database
>>> DB::connection('telescope')->table('telescope_entries')->count();
```

Or using SQL:

```sql
-- Check tables in db
USE db;
SHOW TABLES;

-- Check tables in laravel-db
USE `laravel-db`;
SHOW TABLES;

-- Check tables in telescope-db
USE `telescope-db`;
SHOW TABLES;
```

## Important Notes

1. **The migrations table** always stays in the default connection (`db`)
2. **Application migrations** need `protected $connection = 'laravel';` to use `laravel-db`
3. **Telescope migrations** automatically use the connection from `config/telescope.php`
4. **Future migrations** should specify `protected $connection = 'laravel';` to use `laravel-db`

## Creating New Migrations

When creating new migrations for your app, add the connection property:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     */
    protected $connection = 'laravel';

    public function up(): void
    {
        Schema::connection($this->connection)->create('your_table', function (Blueprint $table) {
            // your columns
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('your_table');
    }
};
```
