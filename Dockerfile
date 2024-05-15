FROM php:8.3-apache

WORKDIR /var/www/app

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
    libpq-dev && \
    docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql && \
    a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

