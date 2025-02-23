# Stage 1: Build JavaScript assets
# Use Node.js 22 as the base image for building frontend assets
FROM node:22 AS js-builder

# Set working directory for the build process
WORKDIR /var/www/html

# Copy package files for npm dependencies
COPY package.json ./
COPY package-lock.json ./

# Install npm dependencies with clean install (ci is more strict than install)
RUN npm ci

# Copy all project files to the container
COPY . .

# Copy the inject.sh script
COPY scripts/inject.sh /usr/local/bin/inject.sh
RUN chmod +x /usr/local/bin/inject.sh

# Build frontend assets (compiles and minifies JS/CSS)
RUN npm run build

# Stage 2: Production PHP environment
# Use Nginx + PHP-FPM 8.3 as the base image for the application
FROM jkaninda/nginx-php-fpm:8.3

WORKDIR /var/www/html

# Ensure version.md is copied first
COPY version.md ./
COPY . .

# Configure PHP-FPM and Nginx
RUN mkdir -p /run/php && \
    mkdir -p /var/log/nginx /var/lib/nginx /var/run/nginx && \
    chown -R www-data:www-data /var/log/nginx /var/lib/nginx /var/run/nginx /run/php

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions
RUN chmod -R 775 ./storage/ ./bootstrap/cache/ && \
    chown -R www-data:www-data ./storage/ ./bootstrap/cache/

# Copy assets from builder
COPY --from=js-builder /var/www/html/public/build/ ./public/build/



ENV DOCUMENT_ROOT=/var/www/html/public


# Run the inject.sh script before starting the application
CMD ["sh", "-c", "inject.sh && php-fpm"]
