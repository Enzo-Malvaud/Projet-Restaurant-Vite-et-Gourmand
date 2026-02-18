# OBLIGATOIRE : La base de l'image
FROM php:8.4-fpm 

WORKDIR /var/www/backend

# Extensions PHP
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# On installe les dépendances d'abord (plus rapide au build suivant)
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# On copie tout le code (C'est ici que tes fichiers src/ et public/ arrivent)
COPY backend/ .

RUN chown -R www-data:www-data /var/www/backend