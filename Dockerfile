# Stage 1: PHP + Composer
FROM php:8.2-apache AS base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
    npm \
    && docker-php-ext-install pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies (without scripts to avoid artisan not found)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Generate Laravel key and cache config
RUN php artisan key:generate
RUN php artisan config:cache

# Stage 2: Node.js for Vue build
FROM node:20 AS node-build

WORKDIR /var/www/html

# Copy only frontend files
COPY package*.json ./
COPY resources/js ./resources/js
COPY resources/css ./resources/css
COPY vite.config.js ./

# Install npm dependencies & build
RUN npm install
RUN npm run build

# Stage 3: Final PHP + Apache container
FROM php:8.2-apache

WORKDIR /var/www/html

# Copy Laravel PHP app
COPY --from=base /var/www/html /var/www/html

# Copy built Vue frontend assets
COPY --from=node-build /var/www/html/dist /var/www/html/public/build

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
