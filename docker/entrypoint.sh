#!/bin/sh
set -e

# Ensure storage and bootstrap directories exist with full write permissions
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public/avatars \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Link storage for public file access
php artisan storage:link || true

# Clear stale caches then optimize for production
echo "Optimizing Laravel for production..."
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || true

# Seed required initial users
echo "Seeding users..."
php artisan db:seed --class=UserSeeder --force || true

# Start supervisor
echo "Starting Supervisor (Nginx + PHP-FPM)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
