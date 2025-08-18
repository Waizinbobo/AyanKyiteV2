# ---------------------------
# Stage 1: PHP + Composer
# ---------------------------
FROM php:8.2-apache AS base

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y unzip git curl libpng-dev zip libonig-dev \
    && docker-php-ext-install pdo pdo_mysql gd mbstring

# Enable Apache rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files and install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copy Laravel source
COPY . .

# ---------------------------
# Stage 2: Node for Vue/Vite Build
# ---------------------------
FROM node:20 AS node-build

WORKDIR /var/www/html

# Copy Node/Vite files
COPY package*.json ./
COPY vite.config.js ./
COPY resources/ ./resources
COPY public/ ./public

# Install and build Vue assets
RUN npm install
RUN npm run build   # generates /public/build

# ---------------------------
# Stage 3: Final Image
# ---------------------------
FROM php:8.2-apache

WORKDIR /var/www/html

# Enable Apache rewrite
RUN a2enmod rewrite

# Copy Laravel app
COPY --from=base /var/www/html /var/www/html

# Copy built Vue assets
COPY --from=node-build /var/www/html/public/build /var/www/html/public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
