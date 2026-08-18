#!/bin/bash
set -e

# ============================================================
# WhatsStore SaaS - Docker Entrypoint
# Runs on every container start
# ============================================================

echo "========================================"
echo "  WhatsStore SaaS - Starting Container  "
echo "========================================"

# Create log directories
mkdir -p /var/log/php /var/log/supervisor /var/log/nginx

# Wait for DB to be ready (useful for docker-compose)
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "⏳ Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
    for i in $(seq 1 30); do
        if php -r "new PDO('mysql:host=$DB_HOST;port=${DB_PORT:-3306};dbname=$DB_DATABASE', '$DB_USERNAME', '$DB_PASSWORD');" 2>/dev/null; then
            echo "✅ Database connected!"
            break
        fi
        echo "   Attempt $i/30 - waiting 2s..."
        sleep 2
    done
fi

# Go to app directory
cd /var/www/html

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Clear & cache configs for production performance
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction

# Storage symlink
echo "🔗 Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

# Fix permissions (again after volume mounts)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "🚀 Starting Supervisor (Nginx + PHP-FPM)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
