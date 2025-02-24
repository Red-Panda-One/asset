# Stage 1: Build JavaScript assets
# Use Node.js 22 as the base image for building frontend assets
FROM node:22 AS js-builder

WORKDIR /var/www/html

# Copy package files for npm dependencies
COPY package.json ./
COPY package-lock.json ./

# Install npm dependencies with clean install (ci is more strict than install)
RUN npm ci

# Copy all project files to the container
COPY . .

# Build frontend assets (compiles and minifies JS/CSS)
RUN npm run build

# Copy custom scripts
COPY scripts/inject.sh /usr/local/bin/inject.sh
RUN chmod +x /usr/local/bin/inject.sh

COPY scripts/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh && \
    if [ ! -e /run/php ] ; then mkdir /run/php ; fi

# Stage 2: Production PHP environment
# Use Nginx + PHP-FPM 8.3 as the base image for the application
FROM jkaninda/nginx-php-fpm:8.3

WORKDIR /var/www/html

# Copy project files from the builder stage
COPY --from=js-builder /var/www/html/public/build/ ./public/build/
COPY . .

# Configure NGINX with custom configuration
COPY nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Configure directories and permissions
RUN mkdir -p /run/php /var/log/nginx /var/lib/nginx /var/run/nginx storage/framework/cache && \
    chown -R www-data:www-data /var/www/html /var/log/nginx /var/lib/nginx /var/run/nginx /run/php && \
    chmod -R 775 storage bootstrap/cache

# Define storage volume
VOLUME /var/www/html/storage

# Switch to www-data user for better security
USER www-data

# Use the startup script as the entrypoint
# ... existing code ...

# Use both commands in a single CMD instruction
ENTRYPOINT [ "/entrypoint.sh" ]
