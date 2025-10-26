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
echo "PORT: ${PORT:-8080}"
echo "================================"

# Configurar Nginx con el puerto correcto
echo "🔧 Configuring Nginx port..."
if [ -f /docker/nginx.conf.template ]; then
    envsubst '${PORT}' < /docker/nginx.conf.template > /etc/nginx/sites-available/default
else
    sed -i "s/listen 8080;/listen ${PORT:-8080};/g" /etc/nginx/sites-available/default
fi

# Esperar a que PostgreSQL esté disponible
echo "⏳ Waiting for PostgreSQL..."
for i in {1..30}; do
    if pg_isready -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USERNAME}" 2>/dev/null; then
        echo "✅ PostgreSQL is ready!"
        break
    fi
    echo "PostgreSQL is unavailable - attempt $i/30"
    sleep 2
done

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

# Fix column names in asistencias table if needed (one-time fix)
echo "🔧 Checking asistencias table columns..."
psql "${DATABASE_URL}" -c "DO \$\$
BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='asistencias' AND column_name='hsora_registro') THEN
        ALTER TABLE asistencias RENAME COLUMN hsora_registro TO hora_registro;
        RAISE NOTICE 'Column hsora_registro renamed to hora_registro';
    END IF;
    IF EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='asistencias' AND column_name='etado') THEN
        ALTER TABLE asistencias RENAME COLUMN etado TO estado;
        RAISE NOTICE 'Column etado renamed to estado';
    END IF;
END \$\$;" || echo "⚠️  Could not check/fix column names (table may not exist yet)"
echo "✅ Column names verified"

# Ejecutar seeders (datos de producción completos)
echo "🌱 Running seeders..."
php artisan db:seed --class=ProductionDataSeeder --force --no-interaction || echo "⚠️  Seeders already run or failed"
echo "✅ Seeders completed"

# Limpiar caché antes de cachear
echo "🧹 Clearing cache..."
php artisan config:clear || true
php artisan cache:clear || true

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
