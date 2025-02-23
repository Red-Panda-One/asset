#!/bin/bash
function replace_or_insert() {
    grep -q "^${1}=" /var/www/html/.env && sed "s|^${1}=.*|${1}=${2}|" -i /var/www/html/.env || sed "$ a\\${1}=${2}" -i /var/www/html/.env
}
if [ "$APP_NAME" != '' ]; then
  replace_or_insert "APP_NAME" "$APP_NAME"
fi
if [ "$APP_ENV" != '' ]; then
  replace_or_insert "APP_ENV" "$APP_ENV"
fi
if [ "$APP_KEY" != '' ]; then
  replace_or_insert "APP_KEY" "$APP_KEY"
fi
if [ "$APP_DEBUG" != '' ]; then
  replace_or_insert "APP_DEBUG" "$APP_DEBUG"
fi
if [ "$APP_URL" != '' ]; then
  replace_or_insert "APP_URL" "$APP_URL"
fi
if [ "$DB_CONNECTION" != '' ]; then
  replace_or_insert "DB_CONNECTION" "$DB_CONNECTION"
fi
if [ "$DB_HOST" != '' ]; then
  replace_or_insert "DB_HOST" "$DB_HOST"
fi
if [ "$DB_PORT" != '' ]; then
  replace_or_insert "DB_PORT" "$DB_PORT"
fi
if [ "$DB_DATABASE" != '' ]; then
  replace_or_insert "DB_DATABASE" "$DB_DATABASE"
fi
if [ "$DB_USERNAME" != '' ]; then
  replace_or_insert "DB_USERNAME" "$DB_USERNAME"
fi
if [ "$DB_PASSWORD" != '' ]; then
  replace_or_insert "DB_PASSWORD" "$DB_PASSWORD"
elif [ "$DB_PASSWORD_FILE" != '' ]; then
  value=$(<$DB_PASSWORD_FILE)
  replace_or_insert "DB_PASSWORD" "$value"
fi
if [ "$SESSION_DRIVER" != '' ]; then
  replace_or_insert "SESSION_DRIVER" "$SESSION_DRIVER"
fi
if [ "$QUEUE_CONNECTION" != '' ]; then
  replace_or_insert "QUEUE_CONNECTION" "$QUEUE_CONNECTION"
fi
if [ "$CACHE_STORE" != '' ]; then
  replace_or_insert "CACHE_STORE" "$CACHE_STORE"
fi
if [ "$MAIL_MAILER" != '' ]; then
  replace_or_insert "MAIL_MAILER" "$MAIL_MAILER"
fi
if [ "$MAIL_HOST" != '' ]; then
  replace_or_insert "MAIL_HOST" "$MAIL_HOST"
fi
if [ "$MAIL_PORT" != '' ]; then
  replace_or_insert "MAIL_PORT" "$MAIL_PORT"
fi
if [ "$MAIL_USERNAME" != '' ]; then
  replace_or_insert "MAIL_USERNAME" "$MAIL_USERNAME"
fi
if [ "$MAIL_PASSWORD" != '' ]; then
  replace_or_insert "MAIL_PASSWORD" "$MAIL_PASSWORD"
elif [ "$MAIL_PASSWORD_FILE" != '' ]; then
  value=$(<$MAIL_PASSWORD_FILE)
  replace_or_insert "MAIL_PASSWORD" "$value"
fi
if [ "$MAIL_ENCRYPTION" != '' ]; then
  replace_or_insert "MAIL_ENCRYPTION" "$MAIL_ENCRYPTION"
fi
if [ "$MAIL_FROM_ADDRESS" != '' ]; then
  replace_or_insert "MAIL_FROM_ADDRESS" "$MAIL_FROM_ADDRESS"
fi
if [ "$MAIL_FROM_NAME" != '' ]; then
  replace_or_insert "MAIL_FROM_NAME" "$MAIL_FROM_NAME"
fi
if [ "$PHP_TZ" != '' ]; then
  sed -i "s|;*date.timezone =.*|date.timezone = ${PHP_TZ}|i" /etc/php/8.2/cli/php.ini
  sed -i "s|;*date.timezone =.*|date.timezone = ${PHP_TZ}|i" /etc/php/8.2/fpm/php.ini
fi
