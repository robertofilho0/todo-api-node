FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

FROM php:8.4-cli-alpine
WORKDIR /var/www/html

RUN docker-php-ext-install pdo_mysql

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data
EXPOSE 8000

# Aplica as migrations e sobe o servidor embutido do PHP
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
