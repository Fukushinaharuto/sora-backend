FROM php:8.4-cli
WORKDIR /sora-back
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN apt-get update
ENV COMPOSER_HOME "/opt/composer"
ENV PATH "$PATH:/opt/composer/vendor/bin"
RUN apt-get update && \
    apt-get install -y --no-install-recommends git unzip libzip-dev default-mysql-client && \
    docker-php-ext-install zip pdo pdo_mysql pcntl && \
    pecl install redis && docker-php-ext-enable redis && \
    rm -rf /var/lib/apt/lists/*

COPY . .
WORKDIR /sora-back/sora-back
RUN composer install