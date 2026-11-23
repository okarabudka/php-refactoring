FROM php:8.5-cli

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

COPY php.ini /usr/local/etc/php/conf.d/zz-app.ini

# Composer runs as the host user (see Makefile), so it needs a writable home.
ENV COMPOSER_HOME=/tmp/composer

WORKDIR /app
