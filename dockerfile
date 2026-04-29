FROM php:8.2-apache

# Install system dependencies + GD (FIX FOR YOUR ERROR)
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Enable Apache rewrite (Laravel requirement)
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy project
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Fix Laravel permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
