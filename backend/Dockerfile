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
    pdo_sqlite 

WORKDIR /var/www

# ===============================
# STAGE 2: Development
# ===============================
FROM base AS development

# Install Composer for dependency management
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create entrypoint script to install dependencies if needed
RUN echo '#!/bin/sh' > /entrypoint.sh && \
    echo 'set -e' >> /entrypoint.sh && \
    echo 'if [ ! -d "vendor" ] && [ -f "composer.json" ]; then' >> /entrypoint.sh && \
    echo '  echo "Installing Composer dependencies..."' >> /entrypoint.sh && \
    echo '  composer install --no-interaction || true' >> /entrypoint.sh && \
    echo 'fi' >> /entrypoint.sh && \
    echo 'if [ ! -f "database/database.sqlite" ]; then' >> /entrypoint.sh && \
    echo '  echo "Creating SQLite database file..."' >> /entrypoint.sh && \
    echo '  mkdir -p database' >> /entrypoint.sh && \
    echo '  touch database/database.sqlite' >> /entrypoint.sh && \
    echo '  chmod 664 database/database.sqlite' >> /entrypoint.sh && \
    echo 'fi' >> /entrypoint.sh && \
    echo 'exec php-fpm' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

# We don't COPY code here because we use Volumes in docker-compose.yml 
# for real-time local development.
ENTRYPOINT ["/entrypoint.sh"]

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