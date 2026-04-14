FROM php:8.4-fpm 

WORKDIR /var/www/backend

# Installateur d'extensions PHP magique
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# On ajoute simplement 'mongodb' à la liste
RUN install-php-extensions pdo_mysql intl zip opcache mongodb

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer