FROM php:8.4-fpm
WORKDIR /sora-back
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get -y install git unzip libzip-dev default-mysql-client && \
    docker-php-ext-install zip pdo pdo_mysql && \
    docker-php-ext-enable pdo_mysql

COPY . .
WORKDIR /sora-back/sora-back
RUN composer install
CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]
EXPOSE 8000