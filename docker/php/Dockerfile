FROM php:8.4-fpm 


# 1. On définit le dossier de travail en PREMIER
WORKDIR /var/www/backend

# Installateur d'extensions PHP 
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip opcache
RUN install-php-extensions opcache apcu

# 3. Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend
