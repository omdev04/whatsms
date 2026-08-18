# ============================================================
# WhatsStore SaaS - Dokploy Dockerfile (Self-Contained)
# PHP 8.2 + Nginx + FPM + Supervisor
# All configs embedded inline — no external COPY needed
# ============================================================

FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    openssl-dev \
    bash

# Install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        intl \
        mbstring \
        xml \
        bcmath \
        opcache \
        pcntl \
        exif

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

COPY . .

# Create minimal build-time .env (APP_NAME with space causes dotenv parse error)
RUN echo "APP_NAME=WhatsStoreSaaS" > .env \
    && echo "APP_KEY=base64:$(head -c 32 /dev/urandom | base64)" >> .env \
    && echo "APP_ENV=local" >> .env \
    && echo "DB_CONNECTION=mysql" >> .env

RUN composer dump-autoload --optimize --no-dev

# Build frontend assets
RUN npm ci 2>/dev/null || npm install
RUN npm run build

# Fix permissions
RUN mkdir -p /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/logs /var/www/html/resources/lang \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html/resources/lang

# ============================================================
# Write Nginx config inline
# ============================================================
RUN printf '%s\n' \
    'user nginx;' \
    'worker_processes auto;' \
    'error_log /var/log/nginx/error.log warn;' \
    'pid /var/run/nginx.pid;' \
    'events { worker_connections 1024; multi_accept on; }' \
    'http {' \
    '    include /etc/nginx/mime.types;' \
    '    default_type application/octet-stream;' \
    '    sendfile on;' \
    '    keepalive_timeout 65;' \
    '    client_max_body_size 100M;' \
    '    gzip on;' \
    '    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript;' \
    '    include /etc/nginx/http.d/*.conf;' \
    '}' > /etc/nginx/nginx.conf

RUN printf '%s\n' \
    'server {' \
    '    listen 80;' \
    '    server_name _;' \
    '    root /var/www/html/public;' \
    '    index index.php index.html;' \
    '    location / { try_files $uri $uri/ /index.php?$query_string; }' \
    '    location ~ \.php$ {' \
    '        try_files $uri =404;' \
    '        fastcgi_split_path_info ^(.+\.php)(/.+)$;' \
    '        fastcgi_pass 127.0.0.1:9000;' \
    '        fastcgi_index index.php;' \
    '        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;' \
    '        include fastcgi_params;' \
    '        fastcgi_read_timeout 300;' \
    '    }' \
    '    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {' \
    '        expires 1y;' \
    '        add_header Cache-Control "public, immutable";' \
    '        access_log off;' \
    '    }' \
    '    add_header X-Frame-Options "SAMEORIGIN" always;' \
    '    add_header X-Content-Type-Options "nosniff" always;' \
    '    location ~ /\.env { deny all; }' \
    '    location ~ /\. { deny all; }' \
    '    error_page 404 /index.php;' \
    '}' > /etc/nginx/http.d/default.conf

# ============================================================
# Write PHP config inline
# ============================================================
RUN printf '%s\n' \
    'display_errors = Off' \
    'log_errors = On' \
    'max_execution_time = 300' \
    'max_input_time = 300' \
    'memory_limit = 512M' \
    'post_max_size = 100M' \
    'upload_max_filesize = 100M' \
    'opcache.enable = 1' \
    'opcache.memory_consumption = 128' \
    'opcache.max_accelerated_files = 10000' \
    'opcache.revalidate_freq = 60' \
    'date.timezone = UTC' > /usr/local/etc/php/conf.d/custom.ini

# ============================================================
# Write PHP-FPM pool config inline
# ============================================================
RUN printf '%s\n' \
    '[www]' \
    'user = www-data' \
    'group = www-data' \
    'listen = 127.0.0.1:9000' \
    'listen.owner = www-data' \
    'listen.group = www-data' \
    'pm = dynamic' \
    'pm.max_children = 20' \
    'pm.start_servers = 4' \
    'pm.min_spare_servers = 2' \
    'pm.max_spare_servers = 8' \
    'pm.max_requests = 500' \
    'request_terminate_timeout = 300' > /usr/local/etc/php-fpm.d/www.conf

# ============================================================
# Write Supervisor config inline
# ============================================================
RUN mkdir -p /etc/supervisor/conf.d /var/log/supervisor \
    && printf '%s\n' \
    '[supervisord]' \
    'nodaemon=true' \
    'user=root' \
    'logfile=/var/log/supervisor/supervisord.log' \
    'pidfile=/var/run/supervisord.pid' \
    '[program:php-fpm]' \
    'command=php-fpm -F' \
    'autostart=true' \
    'autorestart=true' \
    'stderr_logfile=/var/log/supervisor/php-fpm.err.log' \
    'stdout_logfile=/var/log/supervisor/php-fpm.out.log' \
    'priority=10' \
    '[program:nginx]' \
    'command=nginx -g "daemon off;"' \
    'autostart=true' \
    'autorestart=true' \
    'stderr_logfile=/var/log/supervisor/nginx.err.log' \
    'stdout_logfile=/var/log/supervisor/nginx.out.log' \
    'priority=20' > /etc/supervisor/conf.d/supervisord.conf

# ============================================================
# Write Entrypoint script inline
# ============================================================
RUN printf '%s\n' \
    '#!/bin/bash' \
    'set -e' \
    'mkdir -p /var/log/php /var/log/supervisor /var/log/nginx /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/logs' \
    'cd /var/www/html' \
    'touch storage/installed 2>/dev/null || true' \
    'echo "Optimizing Laravel..."' \
    'php artisan route:clear || true' \
    'php artisan config:clear || true' \
    'php artisan config:cache || true' \
    'php artisan route:cache || true' \
    'php artisan view:cache || true' \
    'php artisan event:cache || true' \
    'echo "Running migrations & seeders..."' \
    'php artisan migrate --force --no-interaction || true' \
    'php artisan db:seed --force --no-interaction || true' \
    'echo "Creating storage & uploads symlinks..."' \
    'php artisan storage:link 2>/dev/null || true' \
    'ln -sfn /var/www/html/storage/uploads /var/www/html/public/storage/uploads 2>/dev/null || true' \
    'ln -sfn /var/www/html/storage/uploads /var/www/html/public/uploads 2>/dev/null || true' \
    'chown -R www-data:www-data /var/www/html/storage /var/www/html/public /var/www/html/bootstrap/cache /var/www/html/resources/lang' \
    'chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/resources/lang' \
    'echo "Starting Supervisor..."' \
    'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' \
    > /entrypoint.sh \
    && chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
