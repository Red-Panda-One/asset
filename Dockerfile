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
COPY --from=js-builder /var/www/html/public/build/ ./public/build/

# Configure NGINX with custom configuration
RUN echo ' \
server { \
    listen 80; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
    charset utf-8; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
        add_header Access-Control-Allow-Origin *; \
        add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"; \
        add_header Access-Control-Allow-Headers "Origin, X-Requested-With, Content-Type, Accept, Authorization"; \
    } \
    location ~ \.php$ { \
        fastcgi_pass unix:/run/php/php8.3-fpm.sock; \
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
} \
' > /etc/nginx/conf.d/default.conf

# Configure PHP and directories
RUN mkdir -p /run/php /var/log/nginx /var/lib/nginx /var/run/nginx && \
    chown -R www-data:www-data /var/log/nginx /var/lib/nginx /var/run/nginx /run/php

# Install dependencies with retry
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions
RUN chmod -R 775 ./storage/ ./bootstrap/cache/ && \
    chown -R www-data:www-data ./storage/ ./bootstrap/cache/

# Create startup script
RUN echo '#!/bin/bash \n\
/usr/local/bin/inject.sh \n\
php artisan migrate --force \n\
php artisan config:cache \n\
php artisan route:cache \n\
php artisan view:cache \n\
php artisan storage:link \n\
supervisord -c /etc/supervisor/supervisord.conf \n\
' > /usr/local/bin/startup.sh && \
chmod +x /usr/local/bin/startup.sh

ENV DOCUMENT_ROOT=/var/www/html/public

# Use the startup script
CMD ["/usr/local/bin/startup.sh"]
