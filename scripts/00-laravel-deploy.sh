#!/usr/bin/env bash
echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

echo "Shutdown the server"
php artisan down --retry=30 --render=maintenance

echo "Clearing old cache"
composer dump-autoload
php artisan optimize:clear

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

echo "Starting job queue..."
php artisan queue:listen --timeout=60 --sleep=3 --tries=3 &

echo "Laravel deployment completed."

echo "Active server"
php artisan up