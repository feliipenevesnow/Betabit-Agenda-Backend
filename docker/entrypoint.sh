






if [ ! -f .env ] || ! grep -q "APP_KEY=" .env; then
    echo "Gerando chave da aplicação..."
    php artisan key:generate
fi


echo "Executando migrações do banco de dados..."
php artisan migrate --force


exec "$@"