FROM php:8.0-cli-alpine

RUN apk add --no-cache --virtual bash git

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer clear-cache

COPY . /app
COPY docker/google_client_credentials.json /app/var
COPY docker/catalog_sample.xml /app

RUN composer install
