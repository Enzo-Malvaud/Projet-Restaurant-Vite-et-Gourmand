FROM php:8.5-fpm 


# 1. On définit le dossier de travail en PREMIER
WORKDIR /var/www/backend

# 2. Installateur d'extensions PHP 
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip opcache apcu

# 3. Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Copier les fichiers de dépendances
COPY backend/composer.json backend/composer.lock ./

# 6. Copier TOUT le reste du projet
COPY backend/ .

