# ===============================
# Composer / vendor stage
# ===============================
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./

# Install prod dependencies only
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ===============================
# PHP-FPM stage
# ===============================
FROM php:8.3-fpm-alpine

ARG APP_ENV=production

# Install PHP extensions
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev bash \
    && docker-php-ext-install intl pdo_mysql zip mbstring opcache

WORKDIR /var/www

# Copy code
COPY . .

# Copy vendor from previous stage
COPY --from=vendor /app/vendor ./vendor

# Pre-generate caches for read-only code
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Set permissions for storage
RUN mkdir -p storage/framework/{sessions,views,cache} \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
