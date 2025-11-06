# === FASE 1: BUILD (Compilação) ===
FROM php:8.2-fpm-alpine AS base

# 1. Instalar dependências do sistema e extensões PHP
# Removemos $PHPIZE_DEPS da lista do apk add
RUN apk update && apk add --no-cache \
    git \
    curl \
    unzip \
    sqlite \
    libxml2-dev \
    # Instalar as extensões PHP necessárias (pdo_sqlite, dom/xml, mbstring, curl)
    && docker-php-ext-install -j$(nproc) \
    pdo_sqlite \
    dom \
    xml \
    mbstring \
    tokenizer \
    curl \
    && rm -rf /var/cache/apk/*

# 2. Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 3. Define o diretório de trabalho padrão
WORKDIR /app

# === FASE 2: PRODUÇÃO (Runtime) ===
FROM base AS final

# 1. Copia os arquivos da aplicação
COPY . /app

# 2. Configura permissões de cache e logs (necessário para Laravel)
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache \
    # Cria o arquivo SQLite se ele não existir
    && touch database/database.sqlite \
    && chmod 664 database/database.sqlite

# 3. Executa as dependências do Composer (se o vendor/ não estiver no .gitignore)
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# 4. Comando para rodar o PHP-FPM
CMD ["php-fpm"] 

EXPOSE 8080
