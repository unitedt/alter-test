# Dockerfile
FROM php:7.4-alpine

RUN apk add --no-cache autoconf g++ make openssl-dev pcre-dev icu-dev zlib-dev libzip-dev postgresql-dev && \
    docker-php-ext-install bcmath intl opcache zip sockets pdo pdo_pgsql && \
    apk del --purge autoconf g++ make

WORKDIR /usr/src/app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-plugins --prefer-dist --no-progress --no-interaction

RUN ./vendor/bin/rr get-binary --location /usr/local/bin

COPY . .

ENV APP_ENV=prod

RUN php bin/console cache:warmup

EXPOSE 8080
EXPOSE 2112

CMD ["rr", "serve"]