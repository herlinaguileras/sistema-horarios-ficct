# Despliegue

Incluir pasos mínimos para desplegar en Railway/Docker:

1. Variables de entorno necesarias (no incluir secretos en este archivo, usar `.env.example`):
   - APP_KEY, APP_ENV, DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, RAILWAY_STATIC_URL
2. Construir imagen Docker (ya definido en `Dockerfile` del repo).
3. Configurar Railway con variables de entorno y persistent storage si es necesario.
4. Comandos útiles en contenedor:

```powershell
php artisan migrate --force
php artisan db:seed --class=ProductionDataSeeder --force
php artisan config:cache
php artisan route:cache
```

5. Monitorización básica: revisa logs de Railway y configurar backups de la base de datos.
