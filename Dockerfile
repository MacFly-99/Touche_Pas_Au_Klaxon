FROM php:8.3-apache

RUN a2enmod rewrite

# Libs système (gd a besoin de libpng/libjpeg/libfreetype, zip de libzip)
RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP (mbstring est déjà inclus dans l'image officielle)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_mysql zip exif pcntl bcmath gd

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

# DocumentRoot -> /public + autoriser le .htaccess de public/ (réécriture d'URL)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
       > /etc/apache2/conf-available/allow-override.conf \
    && a2enconf allow-override

EXPOSE 80