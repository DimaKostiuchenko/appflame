# ===============================
# STAGE 1: Base (Minimal)
# ===============================
FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache \
    oniguruma-dev \
    sqlite-dev \
    && docker-php-ext-install \
    mbstring \
    pdo_sqlite 

WORKDIR /var/www

# ===============================
# STAGE 2: Development
# ===============================
FROM base AS development
# In Dev, we mount the code via volumes, so we just run the process.
CMD ["php-fpm"]

# ===============================
# STAGE 3: Vendor (Build Helper)
# ===============================
FROM composer:2 AS vendor
WORKDIR /app

# We only install the code-base dependencies here
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

# ===============================
# STAGE 4: Production Runtime
# ===============================
FROM base AS production
ENV APP_ENV=production

# 1. Copy dependencies from vendor stage
COPY --from=vendor /app/vendor ./vendor

# 2. Copy the actual application code
COPY . .

# 3. FIX: Regenerate Autoloader
# This maps your App/ namespaces to the vendor folder
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev

# 4. Set up directory permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data storage bootstrap/cache

# 5. FIX: Production optimizations (MOCKING THE DB)
# We use :memory: so Laravel doesn't look for a physical file during the build.
# We use a dummy APP_KEY so the config can be encrypted/cached.
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    env APP_KEY=base64:$(openssl rand -base64 32) \
    DB_CONNECTION=sqlite \
    DB_DATABASE=:memory: 

EXPOSE 9000
CMD ["php-fpm"]