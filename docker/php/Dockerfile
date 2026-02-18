FROM php:8.4-fpm 


# 1. On définit le dossier de travail en PREMIER
WORKDIR /var/www/backend

# 2. Installateur d'extensions PHP 
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip opcache apcu

# 3. Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Copier les fichiers de dépendances
COPY backend/composer.json backend/composer.lock ./

# 5. Lancer l'installation (Docker créera le dossier /var/www/backend/vendor ici)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 6. Copier TOUT le reste du projet
COPY backend/ .

# 7. Ajuster les permissions pour que PHP puisse écrire dans les dossiers
RUN chown -R www-data:www-data /var/www/backend