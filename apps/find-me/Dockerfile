FROM php:8.3-cli-alpine

RUN apk add --no-cache libxml2-dev oniguruma-dev \
    && docker-php-ext-install dom mbstring
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# MailWeb is consumed as a package. Its demo application is not part of this image.
COPY systems/mailweb/packages/laravel-mailweb /systems/mailweb/packages/laravel-mailweb
COPY systems/harmonicdb/packages/laravel-harmonicdb /systems/harmonicdb/packages/laravel-harmonicdb

WORKDIR /app
COPY apps/find-me/composer.json ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts

COPY apps/find-me .
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --no-dev --no-interaction --optimize \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
