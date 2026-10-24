# On part de l'image officielle PHP 8.3 avec Apache
FROM php:8.3-apache

# On active le module de réécriture d'URL (indispensable pour le .htaccess)
RUN a2enmod rewrite

# On installe les extensions PHP pour MySQL
RUN docker-php-ext-install pdo pdo_mysql

# On installe les certificats CA du système (indispensable pour que le fichier .pem soit reconnu)
RUN apt-get update && apt-get install -y ca-certificates && update-ca-certificates && rm -rf /var/lib/apt/lists/*

# On définit le dossier de travail
WORKDIR /var/www/html

# On copie tout le projet dans le conteneur (y compris ton fichier isrgrootx1.pem)
COPY . .

# On installe Composer dans le conteneur
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# On installe les dépendances (sans les outils de dev pour alléger l'image)
RUN composer install --no-dev --optimize-autoloader

# =========================================================================
# LA LIGNE MAGIQUE POUR LA SÉCURITÉ :
# On force Apache à pointer directement dans le dossier /public
# Comme ça, on ne peut pas accéder au dossier /app ou au fichier /.env
# =========================================================================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# On expose le port 80
EXPOSE 80