#!/bin/sh
set -e

WORKDIR="/var/www/html"

cd "$WORKDIR"

# Prefer host-provided env file when available
if [ -f "$WORKDIR/.env.host" ]; then
  ln -sf .env.host .env
elif [ ! -f "$WORKDIR/.env" ] && [ -f "$WORKDIR/.env.example" ]; then
  echo "[entrypoint] No .env found. Copying from .env.example..."
  cp .env.example .env
fi

# Ensure storage directories exist
mkdir -p storage/logs
touch storage/logs/laravel.log

# Fix permissions for runtime directories
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache

# Auto-generate app key if empty
if [ -f .env ] && grep -Eq '^APP_KEY=[[:space:]]*$' .env; then
  echo "[entrypoint] APP_KEY empty. Generating new key..."
  php artisan key:generate --force --no-interaction || true
fi

exec php-fpm "$@"
