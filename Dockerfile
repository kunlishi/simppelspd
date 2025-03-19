FROM node:alpine3.19 AS build

WORKDIR /app
RUN npm install -g pnpm

COPY package.json .
COPY pnpm-lock.yaml .
RUN pnpm install

COPY . .

RUN pnpm config set allow-build-scripts true && pnpm install
RUN pnpm run build

FROM shinsenter/frankenphp AS composer-install
WORKDIR /var/www/html
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN install-php-extensions \
	pdo_mysql \
	gd \
	intl \
	zip \
	opcache
COPY . .
RUN touch database/database.sqlite
RUN composer install

FROM node:alpine3.19 AS node-install

WORKDIR /app
RUN npm install -g pnpm

COPY --from=build /app/package.json .
COPY --from=build /app/pnpm-lock.yaml .

# Install semua dependencies tanpa menghapus Vite
RUN pnpm install --frozen-lockfile
RUN pnpm install --prod

FROM shinsenter/frankenphp AS deploy

WORKDIR /var/www/html
RUN install-php-extensions \
	pdo_mysql \
	gd \
	intl \
	zip \
	opcache

COPY --from=build /app/public ./public
COPY --from=composer-install /var/www/html/vendor ./vendor
COPY . .
RUN touch database/database.sqlite

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]