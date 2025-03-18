FROM node:alpine3.19 AS build

WORKDIR /app
RUN npm install -g pnpm

COPY package.json .
COPY package-lock.json .
# COPY pnpm-lock.yaml .
RUN pnpm install

# Install Vite
RUN pnpm add vite

COPY . .

RUN pnpm config set allow-build-scripts true && pnpm install

# RUN pnpm run build
RUN pnpm run build && ls -la /app

FROM node:alpine3.19 AS deploy

WORKDIR /app
RUN npm install -g pnpm

# COPY --from=build /app/.next ./.next
COPY --from=build /app/public ./public
COPY --from=build /app/package.json .
COPY --from=build /app/pnpm-lock.yaml .

RUN pnpm install --prod

CMD ["pnpm", "run", "start"]

# # 2 tidak BISA

# # Gunakan FrankenPHP sebagai base image
# FROM shinsenter/frankenphp

# # Set working directory
# WORKDIR /var/www/html

# # Tentukan domain untuk server
# ENV SERVER_NAME=simpel-dev.student.stis.ac.id

# # Copy composer dari image resmi
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# # Install ekstensi PHP yang dibutuhkan Laravel
# RUN install-php-extensions \
#     pdo_mysql \
#     gd \
#     intl \
#     zip \
#     opcache

# # Copy file konfigurasi utama
# COPY package.json package-lock.json ./
# COPY composer.json composer.lock ./

# # Copy seluruh file proyek
# COPY . .

# # Install dependensi backend Laravel
# RUN composer install --no-dev --optimize-autoloader || composer install --ignore-platform-reqs

# # Install dependensi frontend
# RUN npm install
# RUN npm run build

# # Generate Laravel key
# RUN php artisan key:generate

# # Pastikan storage bisa diakses
# RUN chmod -R 777 storage bootstrap/cache

# # Jalankan migrasi database
# RUN touch database/database.sqlite
# RUN php artisan migrate --force

# # Jalankan FrankenPHP server
# CMD ["frankenphp", "php-server", "-r", "/var/www/html/public/"]

# # 3 
# FROM php:8.2-apache

# # Install dependencies
# RUN apt-get update && apt-get install -y \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     zip unzip git curl \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install gd pdo pdo_mysql

# # Enable Apache mod_rewrite
# RUN a2enmod rewrite

# # Set working directory
# WORKDIR /var/www/html

# # Copy application files
# COPY . .

# # Install Composer dependencies
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# RUN composer install --no-dev --optimize-autoloader

# # Set permissions
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# # Expose port 80
# EXPOSE 80

# # Start Apache
# CMD ["apache2-foreground"]
