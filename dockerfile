FROM php:8.2-apache

# Install system dependencies FIRST (IMPORTANT)
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip mbstring xml

# Enable Apache rewrite (Laravel requirement)
RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions fix
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
