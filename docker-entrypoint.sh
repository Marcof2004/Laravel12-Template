#!/bin/bash
set -e

# Load environment variables from .env file if it exists
if [ -f /var/www/.env ]; then
    export $(grep -v '^#' /var/www/.env | xargs)
fi

# Set default port if not specified
APP_PORT=${APP_PORT:-8000}

echo "Configuring Nginx to listen on port $APP_PORT..."

# Update nginx configuration with the APP_PORT from .env
sed -i "s/listen [0-9]\+;/listen $APP_PORT;/" /etc/nginx/conf.d/app.conf

echo "Starting services..."

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
