# syntax=docker/dockerfile:1.7

FROM --platform=$BUILDPLATFORM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources resources
RUN npm run build

FROM dunglas/frankenphp:1-php8.4-bookworm

# PHP2Shell (CVE-2026-109618, CVE-2026-109619) is fixed in 8.4.27. The tag above
# floats so later patch releases flow in; this fails the build if a stale base
# image is ever resolved instead.
RUN php -r 'exit(version_compare(PHP_VERSION, "8.4.27", ">=") ? 0 : 1);' \
    || (echo "PHP $(php -r 'echo PHP_VERSION;') is vulnerable to PHP2Shell; need >= 8.4.27" >&2 && exit 1)

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        git \
        unzip \
        libsqlite3-dev \
        tini \
    && docker-php-ext-install pdo_sqlite pcntl opcache \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /app

# Dependencies first, so a change to app code does not re-resolve composer.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --prefer-dist

COPY . .
COPY --from=assets /app/public/build public/build
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && composer dump-autoload --optimize --no-dev \
    && php artisan config:clear \
    && php artisan route:clear

EXPOSE 8080
ENTRYPOINT ["/usr/bin/tini", "--"]
CMD ["frankenphp", "php-server", "--listen", ":8080", "--root", "public/"]
