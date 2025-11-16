FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    nginx \
    supervisor \
    redis-server \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Remove default nginx config
RUN rm -f /etc/nginx/sites-enabled/default && \
    rm -f /etc/nginx/sites-available/default

# Copy custom nginx config
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/conf.d/app.conf /etc/nginx/conf.d/app.conf

# Copy supervisor configuration
COPY supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy PHP configuration
COPY php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy existing application directory
COPY --chown=www-data:www-data . /var/www

# Create necessary directories and set permissions
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/lib/nginx

# Configure Redis
RUN mkdir -p /var/run/redis \
    && chown redis:redis /var/run/redis

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose ports
EXPOSE 80 6379

# Use entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
