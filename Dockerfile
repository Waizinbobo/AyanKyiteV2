# Stage 1: PHP + Composer
FROM php:8.2-apache AS base

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN apt-get update && apt-get install -y unzip git \
    && docker-php-ext-install pdo pdo_mysql
RUN composer install --no-dev --optimize-autoloader

COPY . .

# Stage 2: Node for Vite Build
FROM node:20 AS node-build

WORKDIR /var/www/html

COPY package*.json ./
COPY vite.config.js ./
COPY resources/ ./resources
COPY public/ ./public

RUN npm install
RUN npm run build   # <-- This creates "public/build" not "dist"

# Stage 3: Final PHP + Apache
FROM php:8.2-apache

WORKDIR /var/www/html

# Copy Laravel app
COPY --from=base /var/www/html /var/www/html

# Copy Vue build output to Laravel public folder
COPY --from=node-build /var/www/html/public/build /var/www/html/public/build
