#!/bin/bash
set -e

echo "🚀 Starting FICCT Horarios Application..."
echo "================================"
echo "Environment Check:"
echo "APP_ENV: ${APP_ENV}"
echo "APP_KEY: ${APP_KEY:0:20}..."
echo "APP_URL: ${APP_URL}"
echo "DB_HOST: ${DB_HOST}"
echo "DB_DATABASE: ${DB_DATABASE}"
echo "================================"

# Esperar a que PostgreSQL esté disponible
echo "⏳ Waiting for PostgreSQL..."
until pg_isready -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USERNAME}" 2>/dev/null; do
  echo "PostgreSQL is unavailable - sleeping"
  sleep 2
done
echo "✅ PostgreSQL is ready!"

# Verificar APP_KEY
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
    echo "✅ APP_KEY generated"
else
    echo "✅ APP_KEY already set"
fi

# Limpiar caché antes de migrar
echo "🧹 Clearing cache..."
php artisan config:clear || true
php artisan cache:clear || true

# Ejecutar migraciones
echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction
echo "✅ Migrations completed"

# Cachear configuración
echo "⚙️  Caching configuration..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction
echo "✅ Cache completed"

# Crear storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true
echo "✅ Storage link created"

# Dar permisos finales
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
echo "✅ Permissions set"

# Verificar que Laravel puede conectarse a la BD
echo "🔍 Testing database connection..."
php artisan db:show || echo "⚠️  Could not show database info"

echo "✅ Application ready!"
echo "================================"

# Iniciar Supervisor
echo "🎬 Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
