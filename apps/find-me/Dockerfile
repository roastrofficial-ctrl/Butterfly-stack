FROM php:8.3-cli-alpine

RUN apk add --no-cache libxml2-dev oniguruma-dev python3 py3-pip \
    && docker-php-ext-install dom mbstring
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# MailWeb is consumed as a package. Its demo application is not part of this image.
COPY systems/mailweb/packages/laravel-mailweb /systems/mailweb/packages/laravel-mailweb
COPY systems/harmonicdb/packages/laravel-harmonicdb /systems/harmonicdb/packages/laravel-harmonicdb
COPY systems/porter/clients/php /systems/porter/clients/php
COPY systems/porter /systems/porter
RUN pip install --break-system-packages --no-cache-dir /systems/porter

WORKDIR /app
COPY apps/find-me/composer.json ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts

COPY apps/find-me .
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --no-dev --no-interaction --optimize \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data
CMD ["porter-host-runtime", "--host", "find-me", "--kind", "mailweb.request", "--batch-size", "10", "--idle-ms", "250", "--adapter", "php artisan mailweb:adapter"]
