FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exts;

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

CMD php artisan serve --host=0.0.0.0 --port=10000
EXPOSE 10000