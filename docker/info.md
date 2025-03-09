# Difference between `docker-compose.local` and `docker-compose.prod`

`docker-compose.local`
APP_ENV: local

APP_URL: 'http://localhost'
ASSET_URL: 'http://localhost'
SANCTUM_STATEFUL_DOMAINS: 'localhost'
SESSION_DOMAIN: null

## for production with a domain 
use prod and edit the following

--- production redirects http to https as coded in AppServiceProvider.php

APP_ENV: production
---- For your own Domain
APP_URL: 'https://my_domain.com'
ASSET_URL: 'https://my_domain.com'
SANCTUM_STATEFUL_DOMAINS: 'my_domain.com'
SESSION_DOMAIN: 'my_domain.com'
