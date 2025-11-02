FROM node:20-alpine AS frontend

WORKDIR /app

RUN corepack enable

COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile

COPY resources ./resources
COPY postcss.config.js tailwind.config.js vite.config.js ./
RUN pnpm build

FROM php:8.3-fpm-bookworm AS php-base

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

FROM php-base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock artisan ./
COPY .env.example .env.example
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY storage ./storage

# Salin .env contoh lalu pakai SQLite sementara agar artisan tidak memerlukan MySQL
RUN cp .env.example .env && \
    sed -i 's/^APP_ENV=.*/APP_ENV=local/' .env && \
    sed -i 's/^APP_DEBUG=.*/APP_DEBUG=true/' .env && \
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env && \
    sed -i 's/^DB_DATABASE=.*/DB_DATABASE=database\/database.sqlite/' .env && \
    mkdir -p database && touch database/database.sqlite

RUN composer install --no-dev --no-progress --no-interaction --prefer-dist

FROM php-base AS runtime

WORKDIR /var/www/html

COPY --from=vendor /var/www/html/vendor ./vendor
COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env.example

RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/app-entrypoint.sh /usr/local/bin/docker-app-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-app-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-app-entrypoint.sh"]