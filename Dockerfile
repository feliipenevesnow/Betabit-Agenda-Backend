FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    git \
    curl \
    unzip \
    sqlite \
    sqlite-dev \
    libxml2-dev \
    oniguruma-dev \
    zlib-dev \
    libzip-dev \
    curl-dev \
    autoconf \
    build-base \
    pkgconf \
    re2c

RUN docker-php-ext-install -j$(nproc) \
    pdo_sqlite \
    dom \
    mbstring \
    curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . /app

RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache \
    && touch database/database.sqlite \
    && chmod 664 database/database.sqlite

RUN composer install --no-dev --prefer-dist --optimize-autoloader

CMD ["php-fpm"]

EXPOSE 8080
