# ===============================
# STAGE 1: Base (Minimal)
# ===============================
FROM php:8.4-fpm-alpine AS base

# Install only system dependencies for mbstring
# pdo_mysql usually has no extra dependencies in this Alpine image
# Optimized for SQLite + Laravel 11/12
RUN apk add --no-cache \
    oniguruma-dev \
    sqlite-dev \
    && docker-php-ext-install \
    mbstring \
    pdo_sqlite \
    sqlite3

WORKDIR /var/www

# ===============================
# STAGE 2: Development
# ===============================
FROM base AS development
# We don't COPY code here because we use Volumes in docker-compose.yml 
# for real-time local development.
CMD ["php-fpm"]

# ===============================
# STAGE 3: Vendor (Build Helper)
# ===============================
FROM composer:2 AS vendor
WORKDIR /app

# Copy only composer files first to leverage Docker layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ===============================
# STAGE 4: Production Runtime
# ===============================
FROM base AS production
ENV APP_ENV=production

# 1. Copy dependencies from vendor stage
COPY --from=vendor /app/vendor ./vendor

# 2. Copy the actual application code
COPY . .

# 3. Set up directory permissions for Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data storage bootstrap/cache

# 4. Production optimizations
# Note: These are safe here because this stage is only triggered by the production target
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 9000
CMD ["php-fpm"]