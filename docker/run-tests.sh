#!/bin/bash
set -e

echo "Ejecutando pruebas..."
php artisan test "$@"
