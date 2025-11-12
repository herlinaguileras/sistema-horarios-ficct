# 🚀 Guía de Despliegue en Railway

## 📋 Índice
1. [Pre-requisitos](#pre-requisitos)
2. [Configuración Inicial en Railway](#configuración-inicial)
3. [Variables de Entorno](#variables-de-entorno)
4. [Configurar Base de Datos PostgreSQL](#base-de-datos)
5. [Desplegar la Aplicación](#desplegar)
6. [Configurar Dominio Propio](#dominio-propio)
7. [Verificación Post-Despliegue](#verificación)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Pre-requisitos

### En tu Máquina Local

- [x] Cuenta en [Railway.app](https://railway.app)
- [x] Código subido a GitHub (✅ Ya hecho)
- [x] Dominio propio configurado
- [x] Railway CLI instalado (opcional pero recomendado)

### Instalación de Railway CLI (Opcional)

```powershell
# Windows con npm
npm install -g @railway/cli

# Verificar instalación
railway version
```

---

## ⚙️ Configuración Inicial en Railway

### Paso 1: Crear Nuevo Proyecto

1. Ve a [Railway.app](https://railway.app)
2. Haz clic en **"New Project"**
3. Selecciona **"Deploy from GitHub repo"**
4. Autoriza Railway a acceder a tu GitHub
5. Selecciona el repositorio: `herlinaguileras/sistema-horarios-ficct`
6. Railway detectará automáticamente el `Dockerfile`

### Paso 2: Agregar PostgreSQL

1. En tu proyecto de Railway, haz clic en **"+ New"**
2. Selecciona **"Database"**
3. Elige **"PostgreSQL"**
4. Railway creará una base de datos automáticamente

---

## 🔐 Variables de Entorno

### En Railway Dashboard

1. Ve a tu servicio (la aplicación Laravel)
2. Haz clic en la pestaña **"Variables"**
3. Agrega las siguientes variables:

```bash
# ====================================
# CONFIGURACIÓN BÁSICA
# ====================================
APP_NAME="Sistema de Horarios FICCT"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com  # Cambiar después de configurar dominio

# ====================================
# SEGURIDAD
# ====================================
APP_KEY=  # Railway lo generará automáticamente en el primer deploy

# ====================================
# BASE DE DATOS (Railway las configura automáticamente)
# ====================================
# DATABASE_URL se establece automáticamente al conectar PostgreSQL
# Si necesitas configurarlas manualmente:
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
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# ====================================
# CORREO (Opcional - para notificaciones)
# ====================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# ====================================
# LOGGING
# ====================================
LOG_CHANNEL=stack
LOG_LEVEL=info

# ====================================
# BROADCASTING (Para futuras funcionalidades)
# ====================================
BROADCAST_DRIVER=log

# ====================================
# FILESYSTEM
# ====================================
FILESYSTEM_DISK=public

# ====================================
# TIMEZONE
# ====================================
APP_TIMEZONE=America/La_Paz
```

### Conectar PostgreSQL a tu Aplicación

1. En Railway, ve a tu servicio de aplicación
2. Haz clic en **"Variables"**
3. Haz clic en **"+ New Variable"**
4. Selecciona **"Add Reference"**
5. Elige la base de datos PostgreSQL
6. Selecciona `DATABASE_URL`
7. Railway automáticamente configurará la conexión

---

## 🗄️ Base de Datos

### Configuración Automática

Railway configurará automáticamente:
- ✅ PostgreSQL 16
- ✅ Credenciales seguras
- ✅ Conexión automática vía `DATABASE_URL`
- ✅ Backups diarios (en plan Pro)

### Migraciones y Seeders

El script `docker/start.sh` ejecutará automáticamente:

```bash
1. php artisan migrate --force
2. php artisan db:seed --class=ProductionDataSeeder --force
3. php artisan config:cache
4. php artisan route:cache
5. php artisan view:cache
```

**Nota**: Si necesitas ejecutar migraciones manualmente:

```powershell
# Usando Railway CLI
railway run php artisan migrate --force
```

---

## 🚢 Desplegar la Aplicación

### Deploy Automático desde GitHub

1. Railway detecta cambios en `main` automáticamente
2. Cada push a GitHub activará un nuevo deploy
3. Puedes ver el progreso en la pestaña **"Deployments"**

### Deploy Manual (con Railway CLI)

```powershell
# Login
railway login

# Link al proyecto
railway link

# Deploy
railway up
```

### Verificar el Deploy

1. Ve a la pestaña **"Deployments"**
2. Espera a que el estado sea **"Success"** (puede tardar 5-10 minutos)
3. Haz clic en **"View Logs"** para ver el proceso

---

## 🌐 Configurar Dominio Propio

### Paso 1: Agregar Dominio en Railway

1. Ve a tu servicio de aplicación
2. Haz clic en **"Settings"**
3. Scroll hasta **"Domains"**
4. Haz clic en **"+ Custom Domain"**
5. Ingresa tu dominio: `tudominio.com`
6. Railway te mostrará los registros DNS a configurar

### Paso 2: Configurar DNS

Railway te dará algo como:

```
CNAME Record:
Nombre: @  (o www)
Valor: railway-production-xxxx.railway.app
```

#### Si usas Cloudflare:

1. Ve a **DNS** > **Records**
2. Agrega un registro **CNAME**:
   - **Type**: CNAME
   - **Name**: @ (para dominio raíz) o www (para subdominio)
   - **Target**: `railway-production-xxxx.railway.app`
   - **Proxy status**: ⚠️ **DESACTIVADO** (Gris, no naranja) - Importante
   - **TTL**: Auto

3. Si quieres ambos (con y sin www):
   ```
   CNAME @ -> railway-production-xxxx.railway.app
   CNAME www -> railway-production-xxxx.railway.app
   ```

#### Si usas otro proveedor DNS:

1. Agrega un registro CNAME apuntando a la URL de Railway
2. Espera la propagación (puede tardar hasta 48 horas)

### Paso 3: Actualizar APP_URL

1. En Railway, ve a **Variables**
2. Actualiza `APP_URL` a tu dominio real:
   ```
   APP_URL=https://tudominio.com
   ```
3. Haz clic en **"Redeploy"**

### Paso 4: Configurar SSL (Automático)

Railway configura **SSL automáticamente** para dominios personalizados:
- ✅ Certificado Let's Encrypt
- ✅ Renovación automática
- ✅ HTTPS forzado

---

## ✅ Verificación Post-Despliegue

### Checklist de Verificación

```bash
# 1. Aplicación accesible
✓ https://tudominio.com carga correctamente

# 2. Login funcional
✓ Puedes iniciar sesión con credenciales

# 3. Base de datos conectada
✓ Dashboard muestra datos correctamente

# 4. Assets compilados
✓ CSS y JS cargan correctamente (sin errores 404)

# 5. Imágenes y archivos
✓ Logos e íconos se visualizan

# 6. Módulos funcionando
✓ Usuarios, Roles, Docentes, Materias, etc.

# 7. Redirección HTTPS
✓ http:// redirige automáticamente a https://

# 8. Rendimiento
✓ Tiempo de carga < 3 segundos
```

### Comandos de Verificación (Railway CLI)

```powershell
# Ver logs en tiempo real
railway logs

# Conectar a la base de datos
railway connect Postgres

# Ejecutar comandos artisan
railway run php artisan --version
railway run php artisan route:list
railway run php artisan db:show

# Ver variables de entorno
railway variables
```

### Acceso Inicial

**Credenciales por defecto** (configuradas en el seeder):

```
Email: admin@ficct.edu.bo
Password: admin123
```

⚠️ **IMPORTANTE**: Cambia estas credenciales inmediatamente después del primer acceso.

---

## 🐛 Troubleshooting

### Error: "500 Internal Server Error"

**Causa**: APP_KEY no configurada

**Solución**:
```powershell
railway run php artisan key:generate --force
railway restart
```

### Error: "Database connection failed"

**Causa**: PostgreSQL no conectado correctamente

**Solución**:
1. Verifica que PostgreSQL esté en el mismo proyecto
2. Asegúrate de haber agregado la referencia `DATABASE_URL`
3. Redeploy:
   ```powershell
   railway up
   ```

### Error: "Mix manifest not found"

**Causa**: Assets no compilados

**Solución**:
```powershell
# Verifica que el Dockerfile compile assets
# El archivo ya lo hace con: npm run build
# Si persiste, verifica los logs del build
railway logs --deployment
```

### Error: 404 en rutas

**Causa**: Cache de rutas desactualizada

**Solución**:
```powershell
railway run php artisan config:clear
railway run php artisan route:clear
railway run php artisan cache:clear
railway run php artisan config:cache
railway run php artisan route:cache
railway restart
```

### Error: "Storage not writable"

**Causa**: Permisos incorrectos

**Solución**: El `start.sh` ya configura permisos. Si persiste:
```bash
# Railway no persiste archivos en storage/
# Usa S3 o servicios externos para archivos persistentes
```

### Error: "Session expired constantly"

**Causa**: SESSION_DOMAIN incorrecto

**Solución**:
1. Ve a Variables en Railway
2. Agrega:
   ```
   SESSION_DOMAIN=.tudominio.com
   SESSION_SECURE_COOKIE=true
   ```
3. Redeploy

### Logs no aparecen / App no inicia

**Solución**:
```powershell
# Ver logs detallados
railway logs --deployment

# Ver logs de build
railway logs --filter=build

# Ver logs de runtime
railway logs --filter=runtime
```

### Dominio no resuelve

**Checklist**:
1. ✓ Registro CNAME agregado correctamente
2. ✓ Proxy de Cloudflare DESACTIVADO (si aplica)
3. ✓ Propagación DNS completada (usa https://dnschecker.org)
4. ✓ Dominio verificado en Railway
5. ✓ APP_URL actualizado en variables

**Tiempo de espera**: 5 minutos - 48 horas para propagación DNS

---

## 📊 Monitoreo y Mantenimiento

### Ver Métricas

1. En Railway Dashboard > **"Metrics"**
   - CPU usage
   - Memory usage
   - Network traffic
   - Response times

### Logs

```powershell
# Ver logs en tiempo real
railway logs

# Filtrar por error
railway logs --filter=error

# Ver últimas 100 líneas
railway logs -n 100
```

### Backups de Base de Datos

**Plan Hobby** (Free):
- No backups automáticos
- Usa Railway CLI para backups manuales:

```powershell
railway run pg_dump $DATABASE_URL > backup.sql
```

**Plan Pro**:
- Backups automáticos diarios
- Retención de 7 días

### Escalado

Railway escala automáticamente según el plan:
- **Hobby**: 512 MB RAM, 1 vCPU
- **Pro**: Hasta 8 GB RAM, 8 vCPU

---

## 💰 Costos Estimados

### Plan Hobby (Gratis)

```
✓ $5 USD de crédito gratis/mes
✓ Hasta 500 horas de ejecución
✓ 1 GB de ancho de banda
✓ Dominios personalizados
✓ SSL automático
```

**Estimado para este proyecto**: $0-5 USD/mes

### Plan Pro ($20/mes)

```
✓ $20 USD de crédito incluido
✓ Ejecución ilimitada
✓ 100 GB ancho de banda
✓ Backups automáticos
✓ Soporte prioritario
```

**Estimado para este proyecto**: $20 USD/mes

---

## 🔄 Workflow de Actualización

### Para actualizar el sistema en producción:

```bash
# 1. Hacer cambios localmente
git add .
git commit -m "descripción de cambios"

# 2. Subir a GitHub
git push origin main

# 3. Railway detecta cambios y despliega automáticamente
# Ver progreso en Railway Dashboard

# 4. (Opcional) Ejecutar migraciones si es necesario
railway run php artisan migrate --force

# 5. (Opcional) Limpiar caché
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan config:cache
```

---

## 📞 Soporte

### Recursos

- **Railway Docs**: https://docs.railway.app
- **Railway Discord**: https://discord.gg/railway
- **Railway Status**: https://status.railway.app

### Comandos Útiles

```powershell
# Ver información del proyecto
railway status

# Ver variables
railway variables

# Abrir dashboard
railway open

# Conectar a servicio
railway shell

# Ver uso de recursos
railway metrics
```

---

## ✅ Checklist Final de Despliegue

```
Pre-Despliegue:
□ Código subido a GitHub
□ Dockerfile verificado
□ railway.json configurado
□ Variables de entorno documentadas

Configuración Railway:
□ Proyecto creado en Railway
□ PostgreSQL agregado
□ Variables de entorno configuradas
□ DATABASE_URL referenciada

Deploy:
□ Primer deploy exitoso
□ Migraciones ejecutadas
□ Seeders ejecutados
□ Assets compilados

Dominio:
□ Dominio agregado en Railway
□ DNS configurado correctamente
□ SSL activo (candado verde)
□ APP_URL actualizado

Verificación:
□ Login funcional
□ Dashboard carga correctamente
□ Módulos funcionan
□ Sin errores en console
□ Performance aceptable

Seguridad:
□ Credenciales por defecto cambiadas
□ APP_DEBUG=false
□ Backups configurados
□ Logs monitoreados
```

---

**🎉 ¡Tu aplicación está lista para producción!**

Para cualquier duda, revisa los logs con `railway logs` o contacta al equipo de soporte.

---

*Última actualización: 11 de noviembre de 2024*
