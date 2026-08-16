#!/bin/sh
set -eu

DB_PATH="${DB_DATABASE:-/data/database.sqlite}"
mkdir -p "$(dirname "$DB_PATH")"
touch "$DB_PATH"

if grep -q '^DB_DATABASE=' /app/.env; then
    sed -i "s#^DB_DATABASE=.*#DB_DATABASE=$DB_PATH#" /app/.env
else
    printf '\nDB_DATABASE=%s\n' "$DB_PATH" >> /app/.env
fi

php artisan config:clear
php artisan migrate --force
php artisan db:seed --force

exec php artisan serve --host=0.0.0.0 --port=8000
