FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip git unzip \
    && docker-php-ext-install pdo pdo_pgsql sockets zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY docker/run-tests.sh /usr/local/bin/run-tests
RUN chmod +x /usr/local/bin/run-tests

ENTRYPOINT ["entrypoint.sh"]


CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
