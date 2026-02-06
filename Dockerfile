FROM php:8.4-cli

WORKDIR /app

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get install -y --no-install-recommends git unzip libzip-dev default-mysql-client && \
    docker-php-ext-install zip pdo pdo_mysql pcntl && \
    pecl install redis && docker-php-ext-enable redis && \
    rm -rf /var/lib/apt/lists/*

# プロジェクトを全部コピー
COPY sora-back/ ./

# ★ スクリプト無効で composer install
RUN composer install --no-scripts --no-interaction --no-progress --optimize-autoloader

# オートロード再生成
RUN composer dump-autoload

# package:discover は一応失敗してもビルド落とさない
RUN php artisan package:discover || true
