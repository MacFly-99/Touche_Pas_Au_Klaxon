# 1. L'image de base
FROM php:8.3-apache

# 2. Activation des modules Apache
RUN a2enmod rewrite

# 3. Installation des extensions PHP
RUN docker-php-ext-install pdo pdo_mysql

# 4. Définition du dossier de travail
WORKDIR /var/www/html

# 5. Copie des fichiers du projet
COPY . .

# 6. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 7. La sécurité : Redirection du DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 8. Ouverture du port
EXPOSE 80