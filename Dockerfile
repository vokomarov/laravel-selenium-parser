# Build backend packages
FROM composer:latest AS backend

WORKDIR /var/www

ADD . .

RUN composer install --ignore-platform-reqs --no-scripts


# Build frontend static resources
FROM node:12.2.0-alpine AS frontend

WORKDIR /var/www

ADD resources/js resources/js
ADD resources/sass resources/sass
ADD package.json .
ADD package-lock.json .
ADD webpack.mix.js .

RUN npm install --global cross-env && \
    npm install && \
    npm run prod


# Build main image
FROM php:7.3-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        libzip-dev \
        libz-dev \
        libpq-dev \
        zlib1g-dev \
        libjpeg-dev \
        libpng-dev \
        libicu-dev \
        libfreetype6-dev \
        libssl-dev \
        libmcrypt-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mysqli \
    && docker-php-ext-configure gd \
        --with-jpeg-dir=/usr/lib \
        --with-freetype-dir=/usr/include/freetype2 \
    && docker-php-ext-install gd \
    && pecl install -o -f redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip \
    && docker-php-ext-install tokenizer \
    && docker-php-ext-install bcmath \
    && rm -rf /tmp/pear

RUN usermod -u 1000 www-data

ADD . /var/www

WORKDIR /var/www

COPY --from=frontend /var/www/public/dist ./public/dist
COPY --from=frontend /var/www/public/mix-manifest.json ./public/mix-manifest.json
COPY --from=backend /var/www/vendor ./vendor

CMD ["php-fpm"]

EXPOSE 9000
