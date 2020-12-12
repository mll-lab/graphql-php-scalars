FROM php:8

RUN apt-get update \
    && apt-get install -y \
        curl \
        git \
        libzip-dev \
        zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install \
        zip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER="1"

WORKDIR /workdir
