#!/bin/bash

echo "**** Startup file ****"

if [ -n "$STARTUP_DELAY" ]
	then echo "**** Delaying startup ($STARTUP_DELAY seconds)... ****"
	sleep $STARTUP_DELAY
fi

# Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL to become available..."
until pg_isready -h db -U assetuser -d asset; do
  sleep 5
done

echo "PostgreSQL is ready!"

# Run inject script
/usr/local/bin/inject.sh

echo "**** Setting up artisan permissions ****"
chmod +x artisan

if ! grep -q "APP_KEY" /conf/.env
then
    echo "**** Creating empty APP_KEY variable ****"
    echo "$(printf "APP_KEY=\n"; cat /conf/.env)" > /conf/.env
fi
if ! grep -q '^APP_KEY=[^[:space:]]' /conf/.env; then
  echo "**** Generating new APP_KEY variable **** " && \
  ./artisan key:generate -n
fi

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "
=================================================
STARTING MIGRATIONS
=================================================
"
php artisan migrate --force

# Create storage symlink and set permissions if not exists
if [ ! -L "/var/www/html/public/storage" ]; then
    php artisan storage:link
fi
chmod -R 775 /var/www/html/storage/app/public
chown -R www-data:www-data /var/www/html/storage/app/public

# Start supervisord
exec supervisord -c /etc/supervisor/supervisord.conf

echo "Current .env file contents:"
cat /conf/.env

echo "Testing database connection..."
php artisan tinker <<EOF
DB::connection()->getPdo();
EOF
