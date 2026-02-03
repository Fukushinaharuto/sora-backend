# composer ステージ
FROM --platform=linux/arm64 composer:2.8 AS composer

# php ステージ
FROM --platform=linux/arm64 php:8.4-fpm

WORKDIR /sora-back

# composer を arm64 からコピー
COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/opt/composer
ENV PATH=$PATH:/opt/composer/vendor/bin

# パッケージインストール
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# ソースコピー
COPY . .

# Laravel ディレクトリ
WORKDIR /sora-back/sora-back

RUN composer install

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

EXPOSE 8000
