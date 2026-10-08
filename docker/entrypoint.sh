#!/bin/sh
set -e

PORT="${PORT:-10000}"
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
export PORT

cd /var/www/html

if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set" >&2
    exit 1
fi

# Wait for MySQL to accept connections (private service may still be booting)
i=0
until php -r 'new PDO("mysql:host=".getenv("DB_HOST").";port=".(getenv("DB_PORT")?:3306).";dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));' 2>/dev/null; do
    i=$((i+1))
    [ "$i" -ge 30 ] && { echo "MySQL not reachable" >&2; break; }
    sleep 2
done

php artisan migrate --force --no-interaction
[ "${RUN_SEEDERS:-false}" = "true" ] && php artisan db:seed --class=VacancySeeder --force --no-interaction || true

php artisan storage:link --no-interaction 2>/dev/null || true
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

chown -R www-data:www-data storage bootstrap/cache
exec docker-php-entrypoint "$@"
