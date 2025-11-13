# 🚀 GUÍA RÁPIDA - Despliegue en Railway
## EMPIEZA AQUÍ - 5 Pasos Simples

---

## ⏱️ TIEMPO TOTAL: ~30 minutos

---

## 📋 PASO 1: PREPARACIÓN (2 minutos)

### ✅ Ya tienes todo listo:
- ✅ Código en GitHub actualizado
- ✅ Dockerfile configurado
- ✅ APP_KEY generado: `base64:pSnzfPY1QRO2MVWlmwj13TAoEAKYsKNUmqs9k0Bzw6w=`
- ✅ Variables de producción en `.env.production`

### 📝 COPIA ESTE APP_KEY (lo necesitarás en Railway):

```
base64:pSnzfPY1QRO2MVWlmwj13TAoEAKYsKNUmqs9k0Bzw6w=
```

---

## 🔧 PASO 2: CREAR PROYECTO EN RAILWAY (5 minutos)

### 2.1 - Ir a Railway
1. Abre: **https://railway.app**
2. Click **"Login"** → Usa tu cuenta de GitHub
3. Click **"New Project"**
4. Selecciona **"Deploy from GitHub repo"**
5. Busca: `herlinaguileras/sistema-horarios-ficct`
6. Click en el repositorio

✅ Railway detectará automáticamente el `Dockerfile`

---

### 2.2 - Agregar PostgreSQL
1. En tu proyecto, click **"+ New"** (arriba a la derecha)
2. Selecciona **"Database"**
3. Elige **"PostgreSQL"**
4. Espera 30 segundos (Railway lo crea automáticamente)

✅ Verás un nuevo servicio llamado "Postgres"

---

## ⚙️ PASO 3: CONFIGURAR VARIABLES (5 minutos)

### 3.1 - Ir a Variables de Entorno
1. Click en tu **servicio de la aplicación** (NO en Postgres)
2. Click en pestaña **"Variables"**
3. Click en **"RAW Editor"** (esquina superior derecha)

---

### 3.2 - Copiar y Pegar Configuración

**COPIA TODO ESTO** y pégalo en el editor:

```env
APP_NAME="Sistema de Horarios FICCT"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sistema-horarios-ficct-production.up.railway.app
APP_KEY=base64:pSnzfPY1QRO2MVWlmwj13TAoEAKYsKNUmqs9k0Bzw6w=

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_TIMEZONE=America/La_Paz

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_DRIVER=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

LOG_CHANNEL=stack
LOG_LEVEL=error

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@ficct.edu.bo"
MAIL_FROM_NAME="${APP_NAME}"

BROADCAST_CONNECTION=log
VITE_APP_NAME="${APP_NAME}"
```

---

### 3.3 - Guardar
1. Click en **"Add"** o **"Update Variables"**
2. Railway preguntará si quieres redeploy → Click **"Deploy"**

✅ Railway guardará las variables y empezará a desplegar

---

## 🚢 PASO 4: DESPLEGAR (10-15 minutos)

### 4.1 - Ver el Deploy en Progreso
1. Ve a pestaña **"Deployments"**
2. Click en el deployment activo (verde/amarillo)
3. Click en **"View Logs"**

---

### 4.2 - Monitorear Logs

**Verás algo como esto**:

```
🚀 Starting build...
✓ Building Docker image...
✓ Installing dependencies...
✓ Building assets...
✓ Starting application...
✓ Migrations running...
✓ Seeders running...
✅ Application ready!
```

**ESPERA**: ~10 minutos (es normal)

---

### 4.3 - Verificar Éxito

**Señales de que todo está bien**:
- ✅ Status: **"Success"** (verde)
- ✅ Logs muestran: `✅ Application ready!`
- ✅ URL visible en la parte superior del servicio

---

## ✅ PASO 5: VERIFICAR LA APLICACIÓN (5 minutos)

### 5.1 - Obtener la URL

**En Railway**:
1. Ve a tu servicio
2. Busca la URL en la parte superior:
   ```
   https://sistema-horarios-ficct-production.up.railway.app
   ```
3. Click en la URL para abrir

---

### 5.2 - Checklist de Verificación

Verifica que todo funcione:

- [ ] **Página carga** (sin error 500) ✅
- [ ] **CSS aplicado** (colores y estilos visibles) ✅
- [ ] **Logo FICCT visible** ✅
- [ ] **HTTPS activo** (candado verde) ✅

---

### 5.3 - Probar Login

1. **Ir a**: `https://tu-url.up.railway.app/login`

2. **Usar credenciales por defecto**:
   ```
   Email: admin@ficct.edu.bo
   Password: admin123
   ```

3. **Verificar**:
   - [ ] Login exitoso ✅
   - [ ] Dashboard carga ✅
   - [ ] Menú visible ✅
   - [ ] Módulos accesibles ✅

---

### 5.4 - Actualizar APP_URL

**IMPORTANTE**: Ahora que conoces tu URL real:

1. Ve a **Variables** en Railway
2. Actualiza:
   ```
   APP_URL=https://TU-URL-REAL.up.railway.app
   ```
3. Click **"Restart"** en el servicio

---

## 🎉 ¡FELICIDADES! Tu aplicación está en producción

### 📊 Resumen:
- ✅ Aplicación desplegada en Railway
- ✅ PostgreSQL configurado
- ✅ SSL/HTTPS activo
- ✅ Despliegues automáticos desde GitHub

---

## 📱 ACCESO RÁPIDO

**Tu aplicación**:
```
https://sistema-horarios-ficct-production.up.railway.app
```

**Credenciales iniciales**:
```
Email: admin@ficct.edu.bo
Password: admin123
```

⚠️ **Cambia la contraseña inmediatamente** después del primer login

---

## 🔄 PRÓXIMOS PASOS

### Inmediatos:
1. [ ] Cambiar contraseña del admin
2. [ ] Explorar todos los módulos
3. [ ] Crear usuarios de prueba

### Opcionales:
1. [ ] Configurar dominio personalizado (ver `PLAN_DESPLIEGUE_RAILWAY.md` - Fase 5)
2. [ ] Instalar Railway CLI para comandos avanzados
3. [ ] Configurar correo SMTP (para notificaciones)

---

## 🆘 ¿PROBLEMAS?

### Error 500 - App no carga

**Solución**:
```powershell
# Instalar Railway CLI
npm install -g @railway/cli
railway login

# Ver logs
railway logs

# Verificar APP_KEY
railway run php artisan key:generate --show
```

---

### Login no funciona

**Solución**:
```powershell
# Verificar que las migraciones se ejecutaron
railway run php artisan migrate --force

# Ejecutar seeders manualmente
railway run php artisan db:seed --class=ProductionDataSeeder --force
```

---

### CSS no carga

**Solución**:
```powershell
# Limpiar cache
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan config:cache
```

---

## 📚 DOCUMENTACIÓN COMPLETA

Si necesitas más detalles:

- **Plan Completo**: `PLAN_DESPLIEGUE_RAILWAY.md`
- **Guía Detallada**: `DESPLIEGUE_RAILWAY.md`
- **Checklist**: `CHECKLIST_RAILWAY.md`

---

## 📞 COMANDOS ÚTILES

```powershell
# Instalar Railway CLI
npm install -g @railway/cli

# Login
railway login

# Ver logs
railway logs

# Ejecutar comandos artisan
railway run php artisan migrate --force
railway run php artisan cache:clear
railway run php artisan db:show

# Reiniciar aplicación
railway restart
```

---

## ✅ CHECKLIST FINAL

- [ ] ✅ Proyecto creado en Railway
- [ ] ✅ PostgreSQL agregado
- [ ] ✅ Variables configuradas
- [ ] ✅ Deploy exitoso
- [ ] ✅ URL accesible
- [ ] ✅ Login funciona
- [ ] ✅ Dashboard carga
- [ ] ✅ HTTPS activo
- [ ] ⚠️ Contraseña admin cambiada

---

**🚀 Todo listo para usar el sistema en producción!**

*Si tienes dudas, consulta el plan completo o contacta soporte de Railway*

---

Última actualización: 13 de noviembre de 2024
