#!/bin/bash

echo "Esperando a que la base de datos conecte..."

composer install --no-interaction --prefer-dist --optimize-autoloader
php artisan config:clear
php artisan cache:clear

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Ejecutando seeds..."
php artisan db:seed --force

# Generar documentación de Swagger
echo "Generando Swagger Docs..."
php artisan l5-swagger:generate

exec "$@"
