FROM php:8.5-fpm

WORKDIR /var/www/backend
# Installateur d'extensions PHP 
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip opcache apcu


# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

