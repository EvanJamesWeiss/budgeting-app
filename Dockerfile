FROM php:8.3-fpm-alpine AS base

# Install system dependencies and PHP extensions
RUN apk add --update \
    icu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git

RUN docker-php-ext-install \
    intl \
    pdo_mysql \
    zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Set permissions for Symfony
RUN chown -R www-data:www-data /var/www/html