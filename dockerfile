FROM php:8.2-apache

# =========================
# SYSTEM DEPENDENCIES
# =========================
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev

# =========================
# PHP EXTENSIONS (GD FIX INCLUDED)
# =========================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd pdo pdo_mysql zip mbstring xml

# =========================
# APACHE CONFIG
# =========================
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# =========================
# WORKING DIRECTORY
# =========================
WORKDIR /var/www/html

# =========================
# COPY PROJECT FILES
# =========================
COPY . .

# =========================
# COMPOSER INSTALL
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction

# =========================
# PERMISSIONS FIX
# =========================
RUN chmod -R 775 storage bootstrap/cache

# =========================
# DEBUG (IMPORTANT FOR RENDER TROUBLESHOOTING)
# =========================
RUN pwd && ls -la && find . -name artisan

# =========================
# PORT EXPOSURE
# =========================
EXPOSE 80

# =========================
# START SERVER
# =========================
CMD ["apache2-foreground"]
