# ---------------------------
# Stage 1: PHP + Composer
# ---------------------------
FROM php:8.2-apache AS base

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# System & PHP extensions
RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libzip-dev \
 && docker-php-ext-install pdo pdo_mysql gd zip \
 && a2enmod rewrite

# Install Composer (from official image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP deps WITHOUT scripts (avoid artisan during build)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy the full Laravel app
COPY . .

# ---------------------------
# Stage 2: Node for Vite Build
# ---------------------------
FROM node:20 AS node-build

WORKDIR /var/www/html

# Copy only what's needed for Vite build
COPY package*.json ./
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

# Reproducible install & build (uses package-lock.json)
RUN npm ci --no-audit --no-fund
RUN npm run build   # produces public/build

# ---------------------------
# Stage 3: Final Image
# ---------------------------
FROM php:8.2-apache

WORKDIR /var/www/html

# Apache: point docroot to Laravel's public and allow .htaccess
RUN a2enmod rewrite \
 && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && printf "<Directory /var/www/html/public>\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n" > /etc/apache2/conf-available/laravel.conf \
 && a2enconf laravel

# Bring in the app (with vendor already installed)
COPY --from=base /var/www/html /var/www/html

# Bring in built Vite assets
COPY --from=node-build /var/www/html/public/build /var/www/html/public/build

# Permissions for cache & logs
RUN chown -R www-data:www-data storage bootstrap/cache || true

EXPOSE 80
CMD ["apache2-foreground"]
