#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x run-app.sh`
# Run migrations, process the Nginx configuration template and start Nginx

npm run build

# Clear cache
php artisan optimize:clear

# Cache the various components of the Laravel application
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force && node /assets/scripts/prestart.mjs /assets/nginx.template.conf  /nginx.conf && (php-fpm -y /assets/php-fpm.conf & nginx -c /nginx.conf)