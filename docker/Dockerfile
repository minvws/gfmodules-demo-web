FROM php:8.3-apache AS builder

RUN apt-get update && apt-get install -y \
    libzip-dev libmcrypt-dev libgmp3-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql bcmath gmp zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .

FROM php:8.3-apache

ARG PORT=8080
ENV PORT=$PORT

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev libmcrypt-dev libgmp3-dev \
    && docker-php-ext-install pdo_mysql bcmath gmp zip \
    && a2enmod rewrite \
    && sed -ri -e 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -ri -e 's/:80>/:${PORT}>/g' /etc/apache2/sites-available/*.conf \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

COPY --from=builder --chown=www-data:www-data /var/www/html /var/www/html

EXPOSE ${PORT}

USER www-data
