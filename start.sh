#!/bin/bash

echo "Starting Laravel..."

if [ ! -f .env ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --no-interaction --force
fi
echo "=== Laravel Startup ==="
php artisan key:generate --force --no-interaction || true
php artisan config:cache || echo "Config cache failed"
php artisan route:cache || echo "Route cache failed"
php artisan view:cache || echo "View cache failed"
php artisan migrate --force || echo "Migration failed - manual run needed"
echo "=== Startup complete ==="
exec apache2-foreground
