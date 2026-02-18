# ... (tes installs d'extensions)

# 1. On se place dans le dossier
WORKDIR /var/www/backend

# 2. On copie le composer.json pour pouvoir installer les dépendances
COPY backend/composer.json backend/composer.lock ./

# 3. On installe (crée le dossier vendor)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 4. ON COPIE TOUT LE RESTE DU CODE (C'est ce qui manque chez toi !)
COPY backend/ .

# 5. On répare les droits
RUN chown -R www-data:www-data /var/www/backend