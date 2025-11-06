# === FASE 1: BUILD (Compilação) ===
FROM php:8.2-fpm-alpine AS base

# 1. Instalar pacotes de sistema e dependências de COMPILAÇÃO
RUN apk update && apk add --no-cache \
    # Ferramentas básicas
    git \
    curl \
    unzip \
    sqlite \
    # Dependências para a biblioteca XML (necessário para 'dom' e 'xml')
    libxml2-dev \
    
    # NOVAS DEPENDÊNCIAS DE COMPILAÇÃO (Essenciais para 'docker-php-ext-install')
    autoconf \
    g++ \
    gcc \
    libc-dev \
    make \
    pkgconf \
    re2c \
    
    # Adicionando o php-dev para garantir que as ferramentas de build PHP estejam lá
    php-dev \
    
    && rm -rf /var/cache/apk/*

# 2. Instalar as extensões PHP (sem precisar de PHPIZE_DEPS)
RUN docker-php-ext-install -j$(nproc) \
    pdo_sqlite \
    dom \
    xml \
    mbstring \
    tokenizer \
    curl

# 3. Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 4. Define o diretório de trabalho padrão
WORKDIR /app

# === FASE 2: PRODUÇÃO (Runtime) ===
FROM base AS final

# ... (restante das etapas de cópia e permissões mantidas) ...

# 1. Copia os arquivos da aplicação
COPY . /app

# 2. Configura permissões de cache e logs (necessário para Laravel)
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache \
    && touch database/database.sqlite \
    && chmod 664 database/database.sqlite

# 3. Executa as dependências do Composer
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# 4. Comando para rodar o PHP-FPM
CMD ["php-fpm"] 

EXPOSE 8080
