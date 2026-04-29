#!/bin/bash

echo "Starting Laravel..."

if [ ! -f .env ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --no-interaction --force
fi
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force || echo "Migration failed - check DB connection"

apache2-foreground
