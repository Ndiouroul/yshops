# Dockerfile pour Render
FROM php:8.4-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libonig-dev libzip-dev zip curl npm nodejs mariadb-client

# Installer extensions PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le projet
WORKDIR /var/www/html
COPY . .

# Installer dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Installer dépendances Node et builder les assets
RUN npm install
RUN npm run build

# Exposer le port Laravel
EXPOSE 8000

# Commande pour lancer Laravel
CMD php artisan serve --host 0.0.0.0 --port 8000

