#!/bin/bash

echo "=== Laravel Startup ==="

# Clear old cache
php artisan config:clear
php artisan cache:clear
rm -f .env

# Generate proper APP_KEY
php artisan key:generate --no-interaction --force

# Cache AFTER env vars
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrate
php artisan migrate --force || echo "Migration failed"

echo "=== Startup complete ==="
exec apache2-foreground
