FROM php:8.3.11-fpm

# Update package list and install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    postgresql-client \
    libpq-dev \
    nodejs \
    npm \
    nginx \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

# Configure NGINX with custom configuration
RUN rm -f /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/conf.d/default.conf
COPY nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Install required packages
RUN docker-php-ext-install pdo pgsql pdo_pgsql gd bcmath zip \
    && pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /usr/share/nginx/html/

# Copy the codebase
COPY . ./

# Run composer install for production and give permissions
RUN sed 's_@php artisan package:discover_/bin/true_;' -i composer.json \
    && composer install --ignore-platform-req=php --no-dev --optimize-autoloader \
    && composer clear-cache \
    && php artisan package:discover --ansi \
    && chmod -R 775 storage \
    && chmod -R 775 public \
    && chown -R www-data:www-data storage \
    && chown -R www-data:www-data public \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache

# Copy entrypoint
COPY ./scripts/php-fpm-entrypoint /usr/local/bin/php-entrypoint

# Give permissions to everything in bin/
RUN chmod a+x /usr/local/bin/* \
    && mkdir -p /run/php    # Added directory for PHP-FPM

ENTRYPOINT ["/usr/local/bin/php-entrypoint"]

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

CMD ["php-fpm"]
