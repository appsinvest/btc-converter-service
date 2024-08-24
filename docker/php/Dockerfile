FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
        curl \
        wget \
        zip \
        unzip \
        git

RUN docker-php-ext-install pdo pdo_mysql  \
    && pecl install redis && docker-php-ext-enable redis \
    && docker-php-ext-install bcmath \
    && docker-php-ext-enable bcmath

WORKDIR /www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
