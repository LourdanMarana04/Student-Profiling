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
# COPY PROJECT FILES
# =========================
COPY . .
# Aiven SSL via env MYSQL_ATTR_SSL_CA (no system cert needed)

# =========================
# COMPOSER
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# =========================
# PERMISSIONS (RENDER FIX)
# =========================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# =========================
# LARAVEL SAFE CACHE (NO BOOT CRASH)
# =========================
RUN php artisan config:clear || true \
    && php artisan cache:clear || true \
    && php artisan route:clear || true \
    && php artisan view:clear || true

#  ONLY CACHE IF APP BOOTS SUCCESSFULLY
RUN php artisan config:cache || true

# =========================
# EXPOSE PORT
# =========================
EXPOSE 80

# =========================
# START APACHE
# =========================
RUN chmod +x start.sh

CMD ["bash", "start.sh"]
