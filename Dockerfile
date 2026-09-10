FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    git \
    unzip

RUN docker-php-ext-install \
    pdo_pgsql \
    intl \
    opcache \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-interaction

COPY . .
RUN composer dump-autoload --optimize

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /var/www/html/var

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]