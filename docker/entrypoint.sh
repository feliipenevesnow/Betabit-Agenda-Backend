#!/bin/bash

# Este script executa comandos de setup antes de iniciar o PHP-FPM (CMD)

# Define o diretório de trabalho
cd /app

# Verifica e cria o arquivo database.sqlite se não existir
if [ ! -f database/database.sqlite ]; then
    echo "Criando o arquivo database/database.sqlite..."
    touch database/database.sqlite
    chmod 664 database/database.sqlite
fi

# 1. Gera a chave da aplicação (Apenas se o .env for montado e a chave não existir)
# A variável APP_KEY deve ser definida no seu arquivo .env
if [ ! -f .env ] || ! grep -q "APP_KEY=" .env; then
    echo "Gerando chave da aplicação..."
    # Se o .env não for montado corretamente, este comando pode falhar ou ter efeito limitado.
    # É melhor garantir que a chave seja definida localmente no .env antes de rodar o Docker.
    # Caso contrário, tente: php artisan key:generate --force
fi

# 2. Executa as migrações do banco de dados (Apenas se o banco de dados estiver acessível)
echo "Executando migrações do banco de dados..."
# O Laravel ignora as migrações já executadas.
php artisan migrate --force

# 3. Executa o comando principal do contêiner (o CMD original do Dockerfile)
echo "Iniciando PHP-FPM..."
exec "$@"