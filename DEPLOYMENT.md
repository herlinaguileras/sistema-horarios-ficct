# 🚀 Guía Completa de Deployment - Railway

## 📋 Pre-requisitos

Antes de comenzar, asegúrate de tener:

- ✅ Cuenta de GitHub (gratis)
- ✅ Código del proyecto en GitHub
- ✅ Cuenta de Railway (gratis - $5 de crédito)
- ✅ Git instalado localmente

---

## PASO 1: Preparar el Proyecto

### 1.1 Verificar archivos necesarios

Asegúrate de que existen estos archivos:

```bash
✅ Procfile
✅ nixpacks.toml
✅ .railwayignore
✅ .env.production.example
✅ composer.json
✅ package.json
```

### 1.2 Optimizar composer.json

Verifica que `composer.json` tenga:

```json
{
  "scripts": {
    "post-install-cmd": [
      "php artisan storage:link --force",
      "php artisan config:cache",
      "php artisan route:cache"
    ]
  },
  "platform": {
    "php": "8.4"
  }
}
```

### 1.3 Actualizar .gitignore

Asegúrate que `.gitignore` incluya:

```
/vendor
/node_modules
.env
.env.backup
.env.production
/storage/*.key
/public/hot
/public/storage
npm-debug.log
yarn-error.log
```

---

## PASO 2: Subir Código a GitHub

### 2.1 Inicializar Git (si no lo has hecho)

```bash
git init
git add .
git commit -m "Preparar proyecto para deployment"
```

### 2.2 Crear repositorio en GitHub

1. Ve a https://github.com/new
2. Nombre: `sistema-horarios-ficct`
3. Privacidad: Público o Privado (tu elección)
4. NO inicialices con README

### 2.3 Conectar y subir

```bash
git remote add origin https://github.com/TU_USUARIO/sistema-horarios-ficct.git
git branch -M main
git push -u origin main
```

---

## PASO 3: Configurar Railway

### 3.1 Crear cuenta en Railway

1. Ve a https://railway.app
2. Click en "Login with GitHub"
3. Autoriza Railway

### 3.2 Crear nuevo proyecto

1. Click en "New Project"
2. Selecciona "Deploy from GitHub repo"
3. Busca y selecciona `sistema-horarios-ficct`

### 3.3 Agregar PostgreSQL

1. En tu proyecto, click en "+ New"
2. Selecciona "Database"
3. Click en "Add PostgreSQL"
4. Espera 30 segundos a que se provisione

---

## PASO 4: Configurar Variables de Entorno

### 4.1 Ir a la configuración del servicio

1. Click en tu servicio Laravel (no la base de datos)
2. Ve a la pestaña "Variables"

### 4.2 Agregar variables una por una

**IMPORTANTE**: Agrega TODAS estas variables:

```bash
# --- APLICACIÓN ---
APP_NAME="Sistema Horarios FICCT"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}

# --- GENERAR NUEVA KEY ---
# Genera localmente con: php artisan key:generate --show
APP_KEY=base64:TU_NUEVA_KEY_AQUI

# --- LOCALIZACIÓN ---
APP_LOCALE=es
APP_FALLBACK_LOCALE=es

# --- LOGGING ---
LOG_CHANNEL=stack
LOG_LEVEL=error

# --- BASE DE DATOS ---
# Railway las genera automáticamente, pero puedes referirlas:
DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

# --- SESIONES Y CACHE ---
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# --- EMAIL ---
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@ficct.edu.bo"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4.3 Variables especiales de Railway

Railway también necesita:

```bash
# Para que funcionen los assets de Vite
ASSET_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
```

---

## PASO 5: Configurar Dominio y Deploy

### 5.1 Generar dominio público

1. En tu servicio Laravel, ve a "Settings"
2. Scroll hasta "Networking"
3. Click en "Generate Domain"
4. Te dará algo como: `sistema-horarios-ficct-production.up.railway.app`

### 5.2 Actualizar APP_URL

1. Vuelve a "Variables"
2. Edita `APP_URL` con el dominio generado

### 5.3 Forzar nuevo deployment

1. Ve a la pestaña "Deployments"
2. Click en los 3 puntos del último deployment
3. Selecciona "Redeploy"

---

## PASO 6: Ejecutar Migraciones

### 6.1 Una vez deployado exitosamente

Railway ejecutará automáticamente las migraciones por el comando en `Procfile`:

```
php artisan migrate --force
```

### 6.2 Si necesitas ejecutar comandos manualmente

1. Ve a tu proyecto en Railway
2. Click en tu servicio Laravel
3. Ve a la pestaña "Deployments"
4. Click en el deployment activo
5. En la consola, puedes ejecutar:

```bash
php artisan migrate:status
php artisan db:seed --class=RoleSeeder
```

---

## PASO 7: Crear Usuario Administrador

### 7.1 Usar Railway CLI (Recomendado)

Instala Railway CLI:

```bash
npm install -g @railway/cli
```

Login:

```bash
railway login
```

Conecta al proyecto:

```bash
railway link
```

Ejecuta tinker:

```bash
railway run php artisan tinker
```

Crea el admin:

```php
$user = \App\Models\User::create([
    'name' => 'Administrador FICCT',
    'email' => 'admin@ficct.edu.bo',
    'password' => \Hash::make('admin123456'),
    'email_verified_at' => now()
]);

$adminRole = \App\Models\Role::where('name', 'admin')->first();
$user->roles()->attach($adminRole->id);
```

---

## PASO 8: Verificación Post-Deploy

### 8.1 Checklist de verificación

Visita tu aplicación y verifica:

```bash
✅ La página de inicio carga correctamente
✅ Puedes hacer login con el usuario administrador
✅ Los estilos se ven correctamente (Tailwind)
✅ Las imágenes/assets cargan
✅ Puedes crear un docente
✅ Puedes crear un horario
✅ El sistema de roles funciona
✅ Los exports (Excel/PDF) funcionan
✅ El código QR se genera
```

### 8.2 Ver logs en tiempo real

```bash
railway logs
```

---

## 🔧 TROUBLESHOOTING

### Problema: "500 Server Error"

**Solución:**

1. Verifica que `APP_KEY` esté configurada
2. Revisa los logs: `railway logs`
3. Asegúrate que las migraciones corrieron: `railway run php artisan migrate:status`

### Problema: "Database connection failed"

**Solución:**

1. Verifica que las variables de DB estén correctas
2. Asegúrate de usar `${{Postgres.PGHOST}}` en lugar de valores hardcodeados
3. Reinicia el deployment

### Problema: Los assets no cargan (CSS/JS)

**Solución:**

1. Verifica que `npm run build` se ejecutó
2. Agrega `ASSET_URL` en variables de entorno
3. Verifica que `public/build/manifest.json` exista

### Problema: "Permission denied" en storage

**Solución:**

Agrega al `Procfile` (antes del serve):

```
chmod -R 775 storage bootstrap/cache
```

---

## 📊 Monitoreo y Mantenimiento

### Ver uso de recursos

1. Dashboard de Railway
2. Pestaña "Metrics"
3. Revisa CPU, RAM, Network

### Backups de Base de Datos

**Opción 1: Manual via Railway CLI**

```bash
railway run pg_dump $DATABASE_URL > backup.sql
```

**Opción 2: Automatizado**

Considera usar servicios como:
- Railway Database Backups (próximamente)
- Cron job con script

### Actualizar la aplicación

Cada vez que hagas `git push` a main:

```bash
git add .
git commit -m "Descripción de cambios"
git push origin main
```

Railway detectará automáticamente los cambios y hará redeploy.

---

## 🎯 Siguiente: Configuración de Dominio Personalizado

Si quieres usar `horarios.ficct.edu.bo` en lugar del dominio de Railway:

1. Ve a "Settings" → "Networking"
2. En "Custom Domains", click "Add Domain"
3. Ingresa tu dominio
4. Configura un CNAME en tu DNS apuntando al dominio de Railway

---

## 💰 Gestión de Costos

### Costo estimado mensual:

```
PostgreSQL: ~$5/mes
App (512MB RAM): ~$5/mes
Total: ~$10-15/mes
```

### Optimizar costos:

1. ✅ Usa caché agresivamente
2. ✅ Optimiza queries (N+1)
3. ✅ Implementa queue para tareas pesadas
4. ✅ Monitorea uso de recursos

---

## ✅ Checklist Final

```bash
☐ Proyecto en GitHub
☐ Railway project creado
☐ PostgreSQL agregado
☐ Variables de entorno configuradas
☐ Dominio generado
☐ Deployment exitoso
☐ Migraciones ejecutadas
☐ Seeders ejecutados
☐ Usuario admin creado
☐ Aplicación accesible y funcional
☐ SSL/HTTPS activo (automático)
☐ Logs monitoreados
```

---

## 🆘 Soporte

- Railway Discord: https://discord.gg/railway
- Documentación: https://docs.railway.app
- Laravel Docs: https://laravel.com/docs

---

**¡Felicidades! Tu aplicación está en producción** 🎉
