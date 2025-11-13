# 🚀 PLAN DE IMPLEMENTACIÓN - Despliegue en Railway
## Sistema de Horarios FICCT

> **Fecha**: 13 de noviembre de 2024  
> **Estado**: ✅ Código en GitHub - Listo para desplegar  
> **Repositorio**: `herlinaguileras/sistema-horarios-ficct`

---

## 📊 RESUMEN EJECUTIVO

### Situación Actual
- ✅ Código completo y funcionando localmente
- ✅ Repositorio actualizado en GitHub (commit: 90d7521)
- ✅ Dockerfile configurado y optimizado
- ✅ Scripts de despliegue listos
- 🟡 Pendiente: Configurar Railway y desplegar

### Objetivo
Desplegar el **Sistema de Horarios FICCT** en Railway con PostgreSQL, asegurando:
- ✅ Base de datos funcional con datos iniciales
- ✅ SSL automático con HTTPS
- ✅ Dominio personalizado (opcional)
- ✅ Despliegues automáticos desde GitHub

### Tiempo Estimado
- **Configuración Railway**: 15 minutos
- **Primer despliegue**: 10-15 minutos
- **Verificación y ajustes**: 10 minutos
- **Total**: ~40 minutos

---

## 🎯 FASE 1: PRE-REQUISITOS Y PREPARACIÓN
**Duración**: 5 minutos

### ✅ Ya Completado

- [x] Código en GitHub actualizado
- [x] Dockerfile configurado
- [x] Scripts de inicio (`docker/start.sh`)
- [x] Configuración Nginx (`docker/nginx.conf`)
- [x] Supervisor configurado
- [x] railway.json presente

### 🔍 Verificación Pre-Despliegue

**Paso 1.1**: Verificar archivos críticos

```powershell
# Ejecuta este comando para verificar que todo está listo
Test-Path "Dockerfile"; Test-Path "railway.json"; Test-Path "docker/start.sh"; Test-Path "docker/nginx.conf"; Test-Path ".env.example"
```

**Resultado esperado**: Todos deben mostrar `True`

---

**Paso 1.2**: Generar APP_KEY para producción

```powershell
php artisan key:generate --show
```

**📝 IMPORTANTE**: Guarda este key, lo necesitarás en Railway:

```
APP_KEY generado: _____________________________________________
```

---

**Paso 1.3**: Verificar último commit

```powershell
git log --oneline -1
git status
```

**Resultado esperado**: 
- Último commit visible
- Working tree limpio (no cambios pendientes)

---

## 🚀 FASE 2: CONFIGURAR RAILWAY
**Duración**: 15 minutos

### Paso 2.1: Crear Proyecto en Railway

1. **Ir a Railway**
   - Abre: https://railway.app
   - Click en **"Login"** → Usa tu GitHub

2. **Crear Nuevo Proyecto**
   - Click en **"New Project"**
   - Selecciona **"Deploy from GitHub repo"**
   - Busca y selecciona: `herlinaguileras/sistema-horarios-ficct`
   - Railway detectará automáticamente el `Dockerfile`

3. **Configuración Inicial**
   - Railway creará el servicio automáticamente
   - **NO inicies el deploy todavía**

---

### Paso 2.2: Agregar PostgreSQL

1. **En tu proyecto de Railway**:
   - Click en **"+ New"** (botón superior derecho)
   - Selecciona **"Database"**
   - Elige **"PostgreSQL"**
   
2. **Esperar creación**:
   - Railway creará PostgreSQL automáticamente
   - Verás un nuevo servicio "Postgres" en tu proyecto

---

### Paso 2.3: Configurar Variables de Entorno

1. **Ir al servicio de la aplicación** (NO PostgreSQL):
   - Click en tu servicio principal (el que tiene el código)
   - Click en pestaña **"Variables"**

2. **Click en "RAW Editor"** (esquina superior derecha)

3. **Copiar y pegar esta configuración**:

```bash
# ====================================
# CONFIGURACIÓN BÁSICA
# ====================================
APP_NAME="Sistema de Horarios FICCT"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sistema-horarios-ficct-production.up.railway.app

# ====================================
# SEGURIDAD
# ====================================
APP_KEY=PEGAR_AQUI_EL_KEY_GENERADO_EN_PASO_1.2

# ====================================
# BASE DE DATOS
# ====================================
DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

# ====================================
# SESIONES Y CACHÉ
# ====================================
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_DRIVER=database
CACHE_PREFIX=

# ====================================
# QUEUE Y JOBS
# ====================================
QUEUE_CONNECTION=database

# ====================================
# FILESYSTEM
# ====================================
FILESYSTEM_DISK=public

# ====================================
# LOGGING
# ====================================
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# ====================================
# CORREO (Opcional)
# ====================================
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@ficct.edu.bo"
MAIL_FROM_NAME="${APP_NAME}"

# ====================================
# BROADCASTING
# ====================================
BROADCAST_CONNECTION=log

# ====================================
# TIMEZONE
# ====================================
APP_TIMEZONE=America/La_Paz
APP_LOCALE=es
APP_FALLBACK_LOCALE=es

# ====================================
# VITE
# ====================================
VITE_APP_NAME="${APP_NAME}"
```

4. **Click en "Add"** o "Save"

---

### Paso 2.4: Conectar PostgreSQL a la Aplicación

**IMPORTANTE**: Railway usa referencias para conectar servicios.

1. **Verificar referencias de PostgreSQL**:
   - Las variables que pusiste `${{Postgres.PGHOST}}` son **referencias**
   - Railway las reemplazará automáticamente con los valores reales
   - **NO necesitas hacer nada más**, ya están conectadas

---

## 🚢 FASE 3: PRIMER DESPLIEGUE
**Duración**: 10-15 minutos

### Paso 3.1: Iniciar Deploy

1. **En Railway Dashboard**:
   - Ve a tu servicio de aplicación
   - Click en pestaña **"Deployments"**
   - Click en **"Deploy"** (si no se desplegó automáticamente)

2. **Ver Logs en Tiempo Real**:
   - Click en el deployment activo
   - Click en **"View Logs"**
   - Verás el proceso completo

---

### Paso 3.2: Monitorear el Build

**Lo que verás en los logs**:

```
[1/5] Building Docker image...
✓ FROM php:8.4-fpm
✓ Installing system dependencies...
✓ Installing PHP extensions...
✓ Installing Composer...
✓ Installing Node.js 20...

[2/5] Installing dependencies...
✓ composer install
✓ npm ci

[3/5] Building assets...
✓ npm run build

[4/5] Optimizing...
✓ composer dump-autoload

[5/5] Starting application...
✓ nginx configured
✓ PostgreSQL ready
✓ Migrations running...
✓ Seeders running...
✓ Cache cleared
✓ Configuration cached
```

**Tiempo estimado**: 8-12 minutos

---

### Paso 3.3: Verificar Deploy Exitoso

**Señales de éxito**:
- ✅ Status: **"Success"** (verde)
- ✅ Logs muestran: `✅ Application ready!`
- ✅ URL disponible en la parte superior

**Si hay errores**, ve a [Fase 5: Troubleshooting](#fase-5-troubleshooting)

---

## ✅ FASE 4: VERIFICACIÓN Y CONFIGURACIÓN POST-DEPLOY
**Duración**: 10 minutos

### Paso 4.1: Obtener URL de la Aplicación

1. **En Railway**:
   - Ve a tu servicio
   - En la parte superior verás una URL como:
     ```
     https://sistema-horarios-ficct-production.up.railway.app
     ```
   - Click para abrir

---

### Paso 4.2: Verificación Visual

**Checklist de Verificación**:

- [ ] **Página principal carga** (sin error 500)
- [ ] **CSS aplicado correctamente** (colores, estilos visibles)
- [ ] **Imágenes cargan** (logo FICCT visible)
- [ ] **No errores en consola** (F12 → Console)
- [ ] **HTTPS activo** (candado verde en navegador)

---

### Paso 4.3: Probar Login

1. **Ir a la página de login**:
   ```
   https://tu-url.up.railway.app/login
   ```

2. **Credenciales por defecto**:
   ```
   Email: admin@ficct.edu.bo
   Password: admin123
   ```

3. **Verificar acceso**:
   - [ ] Login exitoso
   - [ ] Dashboard carga
   - [ ] Menú de navegación visible
   - [ ] Módulos accesibles

---

### Paso 4.4: Actualizar APP_URL (Importante)

Ahora que conoces la URL real:

1. **En Railway Variables**:
   - Reemplaza:
     ```bash
     APP_URL=https://TU-URL-REAL.up.railway.app
     ```

2. **Click en el servicio → "Restart"**

---

### Paso 4.5: Ejecutar Comandos Post-Deploy

**Instalar Railway CLI** (opcional pero recomendado):

```powershell
npm install -g @railway/cli
railway login
```

**Comandos útiles**:

```powershell
# Ver logs en tiempo real
railway logs

# Limpiar cache
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan config:cache
railway run php artisan route:cache

# Ver estado de la base de datos
railway run php artisan db:show

# Listar rutas
railway run php artisan route:list

# Conectar a PostgreSQL
railway run php artisan tinker
```

---

## 🌐 FASE 5: CONFIGURAR DOMINIO PERSONALIZADO (OPCIONAL)
**Duración**: 5 minutos + propagación DNS

### Paso 5.1: Agregar Dominio en Railway

1. **En Railway**:
   - Service → **Settings** → **Domains**
   - Click **"Custom Domain"**
   - Ingresar: `horarios.ficct.edu.bo` (o tu dominio)

2. **Copiar el CNAME proporcionado**:
   ```
   CNAME: tu-proyecto-production.up.railway.app
   ```

---

### Paso 5.2: Configurar DNS

**En tu proveedor DNS** (Cloudflare, GoDaddy, etc.):

```
Tipo: CNAME
Nombre: horarios (o @)
Destino: tu-proyecto-production.up.railway.app
TTL: 3600
Proxy: DESACTIVADO (importante)
```

---

### Paso 5.3: Actualizar Variables para Dominio

```bash
APP_URL=https://horarios.ficct.edu.bo
SESSION_DOMAIN=.ficct.edu.bo
```

**Esperar**: 5-30 minutos para propagación DNS

---

## 🐛 FASE 6: TROUBLESHOOTING

### Error 1: "Application Key Not Set"

**Solución**:
```powershell
# Generar nuevo key
php artisan key:generate --show

# Copiar el resultado y actualizar en Railway Variables:
APP_KEY=base64:el-key-generado

# Redeploy
```

---

### Error 2: "500 Internal Server Error"

**Diagnóstico**:
```powershell
railway logs
```

**Soluciones comunes**:

1. **Verificar APP_KEY configurado**
2. **Verificar permisos**:
   ```powershell
   railway run ls -la storage/
   railway run chmod -R 775 storage/
   ```
3. **Limpiar cache**:
   ```powershell
   railway run php artisan config:clear
   railway run php artisan cache:clear
   ```

---

### Error 3: "Database connection failed"

**Solución**:

1. **Verificar que PostgreSQL está en el mismo proyecto**
2. **Verificar variables**:
   ```powershell
   railway run env | grep PG
   ```
   Deben aparecer: `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`

3. **Verificar referencias en Variables**:
   ```bash
   DB_HOST=${{Postgres.PGHOST}}  # Debe tener exactamente este formato
   ```

---

### Error 4: "CSS no carga (404)"

**Solución**:
```powershell
# Verificar que el build compiló assets
railway logs --filter=build | grep "npm run build"

# Limpiar cache
railway run php artisan storage:link
railway run php artisan config:cache

# Verificar APP_URL y ASSET_URL
```

---

### Error 5: "Migraciones no se ejecutan"

**Solución manual**:
```powershell
railway run php artisan migrate --force
railway run php artisan db:seed --class=ProductionDataSeeder --force
```

---

## 📊 CHECKLIST FINAL DE VERIFICACIÓN

### Pre-Despliegue
- [ ] Código actualizado en GitHub
- [ ] APP_KEY generado
- [ ] Variables documentadas

### Configuración Railway
- [ ] Proyecto creado
- [ ] PostgreSQL agregado
- [ ] Variables configuradas
- [ ] Referencias de DB conectadas

### Post-Despliegue
- [ ] Deploy exitoso (status: Success)
- [ ] URL accesible
- [ ] Login funciona
- [ ] Dashboard carga
- [ ] CSS/JS cargan correctamente
- [ ] Sin errores en consola
- [ ] HTTPS activo

### Seguridad
- [ ] APP_DEBUG=false
- [ ] Contraseña admin cambiada
- [ ] APP_KEY único y seguro

### Opcional
- [ ] Dominio personalizado configurado
- [ ] DNS propagado
- [ ] Railway CLI instalado

---

## 📈 MONITOREO CONTINUO

### Métricas a Monitorear

**En Railway Dashboard → Metrics**:
- CPU Usage (debe estar < 50% en promedio)
- Memory Usage (debe estar < 400 MB)
- Response Time (debe ser < 1 segundo)
- Request Count

### Logs

```powershell
# Ver logs en tiempo real
railway logs

# Ver solo errores
railway logs --filter=error

# Ver últimas 100 líneas
railway logs -n 100
```

### Alertas Recomendadas

1. **CPU > 80%**: Considerar upgrade de plan
2. **Memory > 450 MB**: Optimizar queries
3. **Response time > 2s**: Revisar cache
4. **Error rate > 5%**: Revisar logs

---

## 💰 COSTOS ESTIMADOS

### Plan Hobby (Gratis)
```
✓ $5 USD de crédito gratis/mes
✓ Hasta 500 horas de ejecución/mes
✓ 1 GB de red/mes
✓ Dominios personalizados
✓ SSL incluido
```

**Uso estimado para este proyecto**: $2-4 USD/mes

### Si superas el límite gratis

Railway cobra $0.000231/GB-hora para memoria y CPU.

**Estimado mensual**:
- 500 MB RAM × 730 horas = $84 USD (sin plan)
- **Con Plan Pro ($20/mes)**: $20 + uso adicional

**Recomendación**: Empieza con plan Hobby (gratis) y monitorea uso.

---

## 🔄 WORKFLOW DE ACTUALIZACIÓN

### Para actualizar el sistema en el futuro:

```bash
# 1. Hacer cambios localmente y probar
npm run dev
php artisan serve

# 2. Commit y push
git add .
git commit -m "feat: descripción del cambio"
git push origin main

# 3. Railway desplegará automáticamente
# Ver progreso en Railway Dashboard

# 4. Si hay migraciones nuevas
railway run php artisan migrate --force

# 5. Limpiar cache si es necesario
railway run php artisan config:cache
railway run php artisan route:cache
```

---

## 📞 RECURSOS Y SOPORTE

### Documentación
- **Railway Docs**: https://docs.railway.app
- **Laravel Deployment**: https://laravel.com/docs/deployment
- **PostgreSQL en Railway**: https://docs.railway.app/databases/postgresql

### Comunidad
- **Railway Discord**: https://discord.gg/railway
- **Railway Status**: https://status.railway.app

### Comandos Útiles Railway CLI

```powershell
railway login              # Iniciar sesión
railway link               # Conectar proyecto local
railway status             # Ver estado del proyecto
railway logs               # Ver logs
railway run <cmd>          # Ejecutar comando en Railway
railway connect Postgres   # Conectar a base de datos
railway open               # Abrir dashboard
railway variables          # Ver variables de entorno
railway restart            # Reiniciar servicio
```

---

## 🎯 PRÓXIMOS PASOS RECOMENDADOS

### Inmediatamente después del despliegue

1. **Cambiar contraseña admin**
   - Login con credenciales por defecto
   - Ir a perfil → cambiar contraseña

2. **Crear usuarios reales**
   - Agregar docentes
   - Configurar roles y permisos

3. **Configurar dominio personalizado** (si aplica)

### Dentro de la primera semana

1. **Monitorear logs diariamente**
   ```powershell
   railway logs --filter=error
   ```

2. **Verificar métricas de rendimiento**
   - CPU usage
   - Memory usage
   - Response times

3. **Configurar backups** (opcional)
   ```powershell
   # Script de backup manual
   railway run pg_dump $DATABASE_URL > backup-$(date +%Y%m%d).sql
   ```

### Optimizaciones futuras

1. **CDN para assets** (Cloudflare)
2. **Redis para cache** (si el uso crece)
3. **Queue workers** para tareas pesadas
4. **Monitoring externo** (UptimeRobot, Pingdom)

---

## ✅ CONCLUSIÓN

Este plan te guía paso a paso para desplegar tu Sistema de Horarios FICCT en Railway.

**Tiempo total estimado**: ~40 minutos

**Resultado final**:
- ✅ Aplicación en producción
- ✅ Base de datos PostgreSQL funcional
- ✅ SSL automático (HTTPS)
- ✅ Despliegues automáticos desde GitHub
- ✅ Dominio personalizado (opcional)

---

**🚀 ¡Estás listo para comenzar!**

**Siguiente acción**: Ir a [FASE 1](#fase-1-pre-requisitos-y-preparación)

---

*Última actualización: 13 de noviembre de 2024*
