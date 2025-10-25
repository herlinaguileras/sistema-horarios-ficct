#!/bin/bash
set -e

echo "🚀 Starting FICCT Horarios Application..."

# Esperar a que PostgreSQL esté disponible
echo "⏳ Waiting for PostgreSQL..."
until pg_isready -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USERNAME}"; do
  echo "PostgreSQL is unavailable - sleeping"
  sleep 2
done
echo "✅ PostgreSQL is ready!"

# Generar clave si no existe
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Ejecutar migraciones
echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction

# Cachear configuración
echo "⚙️  Caching configuration..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

# Crear storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Dar permisos finales
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "✅ Application ready!"

# Iniciar Supervisor
echo "🎬 Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
