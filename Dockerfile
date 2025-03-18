# FROM node:alpine3.19 AS build

# WORKDIR /app
# RUN npm install -g pnpm

# COPY package.json .
# COPY package-lock.json .
# # COPY pnpm-lock.yaml .
# RUN pnpm install

# COPY . .

# RUN pnpm config set allow-build-scripts true && pnpm install

# # RUN pnpm run build
# RUN pnpm run build && ls -la /app

# FROM node:alpine3.19 AS deploy

# WORKDIR /app
# RUN npm install -g pnpm

# # COPY --from=build /app/.next ./.next
# COPY --from=build /app/public ./public
# COPY --from=build /app/package.json .
# COPY --from=build /app/pnpm-lock.yaml .

# RUN pnpm install --prod

# CMD ["pnpm", "run", "start"]

# Gunakan FrankenPHP sebagai base image
FROM shinsenter/frankenphp

# Set working directory
WORKDIR /var/www/html

# Tentukan domain untuk server
ENV SERVER_NAME=simpel-dev.student.stis.ac.id

# Copy composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install ekstensi PHP yang dibutuhkan Laravel
RUN install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache

# Copy file konfigurasi utama
COPY package.json package-lock.json ./
COPY composer.json composer.lock ./

# Copy seluruh file proyek
COPY . .

# Install dependensi backend Laravel
RUN composer install --no-dev --optimize-autoloader || composer install --ignore-platform-reqs

# Install dependensi frontend
RUN npm install
RUN npm run build

# Generate Laravel key
RUN php artisan key:generate

# Pastikan storage bisa diakses
RUN chmod -R 777 storage bootstrap/cache

# Jalankan migrasi database
RUN touch database/database.sqlite
RUN php artisan migrate --force

# Jalankan FrankenPHP server
CMD ["frankenphp", "php-server", "-r", "/var/www/html/public/"]