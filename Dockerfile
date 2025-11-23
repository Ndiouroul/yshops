FROM php:8.2-fpm

# Dépendances système
RUN apt-get update && apt-get install -y git unzip libzip-dev zip curl npm nodejs

# Extensions PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring xml zip curl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Projet
WORKDIR /var/www/html
COPY . .

# Dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Dépendances Node / build
RUN npm install
RUN npm run build

EXPOSE 8080

CMD php artisan serve --host 0.0.0.0 --port 8080

