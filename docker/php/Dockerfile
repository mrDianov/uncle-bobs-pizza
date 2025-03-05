FROM composer AS builder
WORKDIR /app/
COPY ./app ./
COPY composer.* ./
RUN composer install
FROM php:8.2-fpm
RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-install pdo_pgsql pgsql
COPY --from=builder /app /var/www/html
COPY --from=builder /app/vendor /var/www/html/vendor
WORKDIR /var/www/html