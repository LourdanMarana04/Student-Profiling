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
# PHP EXTENSIONS
# =========================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd pdo pdo_mysql zip mbstring xml

# =========================
# APACHE CONFIG
# =========================
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# =========================
# WORKDIR
# =========================
WORKDIR /var/www/html

# =========================
# COPY PROJECT
# =========================
COPY . .

# =========================
# COMPOSER
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction

# =========================
# PERMISSIONS (RENDER FIX)
# =========================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# =========================
# EXPOSE PORT
# =========================
EXPOSE 80

# =========================
# START SCRIPT
# =========================
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
