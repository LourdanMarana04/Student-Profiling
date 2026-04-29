#!/bin/bash

echo "=== Laravel Startup ==="

# Clear old cache
php artisan config:clear || true
php artisan cache:clear || true
rm -f .env bootstrap/cache/config.php bootstrap/cache/services.php

# Generate proper APP_KEY
php artisan key:generate --no-interaction --force

# Cache AFTER env vars
php artisan config:cache || echo "CONFIG FAILED"
php artisan route:cache || echo "ROUTE FAILED"
php artisan view:cache || echo "VIEW FAILED"

# Migrate
php artisan migrate --force || echo "MIGRATE FAILED - check DB creds"

echo "=== Startup complete ==="
exec apache2-foreground
