#!/bin/sh
set -e

echo "Aguardando banco de dados..."
until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
  sleep 1
done

echo "Rodando migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "Iniciando PHP-FPM..."
exec php-fpm