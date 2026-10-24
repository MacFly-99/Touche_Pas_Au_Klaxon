# On part de l'image officielle PHP 8.3 avec Apache
FROM php:8.3-apache

# On active le module de réécriture d'URL
RUN a2enmod rewrite

# =========================================================================
# INSTALLATION DES OUTILS SYSTÈME INDISPENSABLES POUR COMPOSER
# (unzip et git sont requis pour télécharger les paquets proprement)
# =========================================================================
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# =========================================================================
# INSTALLATION DES EXTENSIONS PHP
# =========================================================================
RUN docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd

# =========================================================================
# ON AUTORISE COMPOSER À TOURNER EN ROOT DANS LE CONTAINER
# =========================================================================
ENV COMPOSER_ALLOW_SUPERUSER=1

# On définit le dossier de travail
WORKDIR /var/www/html

# On copie tout le projet dans le conteneur
COPY . .

# On installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# On installe les dépendances (maintenant ça va marcher !)
RUN composer install --no-dev --optimize-autoloader

# =========================================================================
# LA LIGNE MAGIQUE POUR LA SÉCURITÉ (DocumentRoot)
# =========================================================================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# On expose le port 80
EXPOSE 80