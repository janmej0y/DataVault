FROM node:20-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./

RUN npm run build


FROM php:8.2-apache-bookworm

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && install-php-extensions \
        bcmath \
        curl \
        dom \
        gd \
        intl \
        mbstring \
        mongodb \
        opcache \
        simplexml \
        xml \
        xmlreader \
        xmlwriter \
        zip \
    && a2enmod rewrite headers \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

# Create Laravel folders
RUN mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs

# Install dependencies
RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --optimize-autoloader

# Fix permissions
RUN chown -R www-data:www-data bootstrap/cache storage \
    && chmod -R ug+rwx bootstrap/cache storage

EXPOSE 10000

CMD ["apache2-foreground"]