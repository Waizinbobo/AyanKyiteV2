# ---------------------------
# Stage 1: PHP + Composer
# ---------------------------
FROM php:8.2-apache AS base

WORKDIR /var/www/html

# Install PHP extensions
RUN apt-get update && apt-get install -y unzip git curl libpng-dev zip libonig-dev \
    && docker-php-ext-install pdo pdo_mysql gd mbstring bcmath

# Enable Apache rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files and install PHP dependencies (skip artisan scripts)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy Laravel source
COPY . .

# ---------------------------
# Stage 2: Node for Vue/Vite Build
# ---------------------------
FROM node:20 AS node-build

WORKDIR /var/www/html

# Copy Node / Vue files
COPY package*.json ./
COPY vite.config.js ./
COPY resources/ ./resources
COPY public/ ./public

# Install dependencies & build
RUN npm install
RUN npm run build   # generates /public/build

# ---------------------------
# Stage 3: Final Image
# ---------------------------
FROM php:8.2-apache

WORKDIR /var/www/html

# Enable Apache rewrite
RUN a2enmod rewrite

# Change Apache DocumentRoot to Laravel public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

# Copy Laravel app
COPY --from=base /var/www/html /var/www/html

# Copy built Vue assets
COPY --from=node-build /var/www/html/public/build /var/www/html/public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Startup command
CMD bash -c "\
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan storage:link && \
    apache2-foreground"
