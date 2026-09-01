#!/usr/bin/env bash

set -e

echo "Running Laravel deployment..."

bash /var/www/html/scripts/00-laravel-deploy.sh

echo "Starting PHP-FPM..."

php-fpm -D

echo "Starting Nginx..."

nginx -g "daemon off;"