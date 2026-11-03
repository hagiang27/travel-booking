FROM php:8.3-fpm-alpine

RUN apk add --no-cache git unzip libzip-dev icu-dev oniguruma-dev \
 && docker-php-ext-install pdo_mysql mbstring zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
 && chown -R www-data:www-data storage bootstrap/cache
