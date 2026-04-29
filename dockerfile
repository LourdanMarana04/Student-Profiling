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
# WORK DIR
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
# LARAVEL FIX (THIS IS CRITICAL)
# =========================
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && php artisan view:clear

RUN php artisan config:cache

# =========================
# PERMISSIONS (RENDER SAFE)
# =========================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# =========================
# DEBUG (REMOVE LATER IF YOU WANT)
# =========================
RUN php -v && php artisan --version

# =========================
# EXPOSE PORT
# =========================
EXPOSE 80

# =========================
# START APACHE
# =========================
CMD ["apache2-foreground"]
