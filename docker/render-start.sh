#!/bin/sh
set -e

PORT="${PORT:-10000}"

mkdir -p \
    /var/www/html/bootstrap/cache \
    /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html/bootstrap/cache /var/www/html/storage
chmod -R ug+rwx /var/www/html/bootstrap/cache /var/www/html/storage

sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

php artisan optimize:clear || true
php artisan package:discover --ansi || true
php artisan storage:link || true

exec apache2-foreground
