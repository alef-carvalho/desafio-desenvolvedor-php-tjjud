#!/bin/bash

set -e

cd /var/www

echo "📦 Fixing permissions..."
chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

echo "📦 Installing dependencies..."
composer install --ignore-platform-reqs && \
    npm install

echo "⚙️ Running migrations..."
php artisan migrate
php artisan db:seed

echo "⚙️ Cleanup cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🚀 Starting Server..."
exec php-fpm
