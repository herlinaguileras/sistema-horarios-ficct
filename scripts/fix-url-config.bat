@echo off
echo ╔═══════════════════════════════════════════════════════════╗
echo ║  CORRIGIENDO CONFIGURACIÓN DE URL Y RUTAS                ║
echo ╚═══════════════════════════════════════════════════════════╝
echo.

echo 🔧 Paso 1: Limpiando cachés...
call php artisan config:clear
call php artisan route:clear
call php artisan cache:clear
call php artisan view:clear
echo ✓ Cachés limpiadas
echo.

echo 📝 Paso 2: Verificando archivo .env...
echo    APP_URL actual: http://localhost
echo.
echo    💡 IMPORTANTE: Debes cambiar manualmente en .env:
echo       APP_URL=http://localhost
echo       a
echo       APP_URL=http://127.0.0.1:8000
echo.
echo    O la URL donde esté corriendo tu servidor Laravel.
echo.

echo 🔄 Paso 3: Recargando configuración...
call php artisan config:cache
echo ✓ Configuración recargada
echo.

echo ═══════════════════════════════════════════════════════════
echo   ✅ PROCESO COMPLETADO
echo ═══════════════════════════════════════════════════════════
echo.
echo 📌 PRÓXIMOS PASOS:
echo    1. Edita el archivo .env
echo    2. Cambia APP_URL=http://localhost
echo       a APP_URL=http://127.0.0.1:8000
echo    3. Ejecuta: php artisan config:cache
echo    4. Recarga la página en tu navegador
echo.

pause
