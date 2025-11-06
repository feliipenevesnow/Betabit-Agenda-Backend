FROM php:8.2-fpm-alpine AS base
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    sqlite \
    libxml2-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) \
    pdo_sqlite \
    dom \
    xml \
    mbstring \
    tokenizer \
    curl \
    && rm -rf /var/cache/apk/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
FROM base AS final
COPY . /app
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache \
    && touch database/database.sqlite \
    && chmod 664 database/database.sqlite
CMD ["php-fpm"] 
EXPOSE 8080