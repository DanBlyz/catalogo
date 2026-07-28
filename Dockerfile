# Stage 1: Build Frontend Assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
COPY resources ./resources
COPY public ./public
COPY tailwind.config.js ./
COPY postcss.config.js ./
RUN npm ci && npx tailwindcss -i ./resources/css/app.css -o ./public/css/app.css --minify

# Stage 2: Install PHP Composer Dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Stage 3: Production Runtime Image
FROM php:8.3-fpm-alpine

# Install system packages & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    bash \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        bcmath \
        intl \
        opcache \
        mbstring

# Working directory
WORKDIR /var/www/html

# Copy server configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy application source code
COPY . .

# Copy compiled vendor & frontend assets
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/css/app.css ./public/css/app.css

# Entrypoint script setup
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set directory permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose application port
EXPOSE 80

# Environment variables defaults for Dokploy
ENV PORT=80
ENV RUN_MIGRATIONS=true

# Container Entrypoint & Default Command
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
