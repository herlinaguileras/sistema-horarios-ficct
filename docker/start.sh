#!/bin/bash

# Generar clave si no existe
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Ejecutar migraciones
php artisan migrate --force

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear storage link
php artisan storage:link || true

# Iniciar Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
