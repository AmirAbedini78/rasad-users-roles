FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libonig-dev \
        libsqlite3-dev \
        libxml2-dev \
    && docker-php-ext-install \
        pdo_sqlite \
        mbstring \
        dom \
        xml \
        xmlwriter \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --no-scripts

COPY . .

RUN composer dump-autoload --optimize \
    && chmod +x /app/docker-entrypoint.sh /app/run-tests.sh

EXPOSE 8000

ENTRYPOINT ["/app/docker-entrypoint.sh"]
