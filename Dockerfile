FROM php:8.5.0-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    nginx \
    bash \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev

RUN docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    bcmath \
    intl \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

ENV COMPOSER_ALLOW_SUPERUSER 1

ENV TMPDIR=/tmp

RUN mkdir -p /tmp && chmod 1777 /tmp

RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    /tmp && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chmod 1777 /tmp

RUN chmod +x /var/www/html/scripts/*.sh

COPY conf/nginx/nginx-site.conf /etc/nginx/http.d/default.conf

CMD ["/var/www/html/scripts/start.sh"]