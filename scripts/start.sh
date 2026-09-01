#!/usr/bin/env bash

set -e

echo "Starting Laravel deployment..."

if [ "${RUN_SCRIPTS}" = "1" ]; then
    for script in /var/www/html/scripts/*.sh; do
        if [ -f "$script" ]; then
            echo "Running $script"
            bash "$script"
        fi
    done
fi

echo "Starting PHP-FPM..."

php-fpm -D

echo "Starting Nginx..."

nginx -g "daemon off;"