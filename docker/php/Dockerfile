# LA LIGNE MANQUANTE :
FROM php:8.4-fpm 

# 1. On se place dans le dossier
WORKDIR /var/www/backend

# 2. On installe les extensions nécessaires
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip opcache

# 3. On installe Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. On copie les fichiers de config Composer
COPY backend/composer.json backend/composer.lock ./

# 5. On installe les dépendances (sans les fichiers de dev pour la prod)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 6. On copie le reste du code
COPY backend/ .

# 7. Permissions
RUN chown -R www-data:www-data /var/www/backend