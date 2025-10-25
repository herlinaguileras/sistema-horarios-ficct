# 🚀 GUÍA RÁPIDA DE DEPLOYMENT - 5 MINUTOS

## ✅ PRE-REQUISITOS

Antes de empezar, asegúrate de tener:

- ✅ Cuenta de GitHub (gratis)
- ✅ Cuenta de Railway (gratis - $5 de crédito)
- ✅ Git instalado

---

## 📝 PASO 1: PREPARAR PROYECTO (2 minutos)

### 1.1 Ejecutar script de validación

**Windows (PowerShell):**
```powershell
.\pre-deploy-check.ps1
```

**Linux/Mac:**
```bash
bash pre-deploy-check.sh
```

### 1.2 Corregir errores (si los hay)

Si el script muestra errores, corrígelos antes de continuar.

### 1.3 Compilar assets para producción

```bash
npm run build
```

---

## 🔐 PASO 2: GENERAR APP_KEY PARA PRODUCCIÓN

```bash
php artisan key:generate --show
```

**Copia el resultado** (ejemplo: `base64:abc123...`), lo necesitarás en Railway.

---

## 📤 PASO 3: SUBIR A GITHUB (1 minuto)

```bash
git add .
git commit -m "Preparar para deploy en Railway"
git push origin main
```

---

## 🚂 PASO 4: DEPLOYMENT EN RAILWAY (2 minutos)

### 4.1 Ir a Railway

1. Ve a https://railway.app
2. Login con GitHub
3. Click en **"New Project"**
4. Selecciona **"Deploy from GitHub repo"**
5. Busca y selecciona tu repositorio `sistema-horarios-ficct`

### 4.2 Agregar PostgreSQL

1. En tu proyecto, click **"+ New"**
2. Selecciona **"Database"**
3. Click en **"Add PostgreSQL"**
4. Espera 30 segundos

### 4.3 Configurar Variables de Entorno

1. Click en tu servicio Laravel (no la base de datos)
2. Ve a **"Variables"**
3. Click en **"RAW Editor"**
4. Pega esto (reemplaza APP_KEY con la que generaste):

```bash
APP_NAME=Sistema Horarios FICCT
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:TU_KEY_AQUI
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
LOG_LEVEL=error
DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@ficct.edu.bo
```

5. Click **"Update Variables"**

### 4.4 Generar Dominio

1. En tu servicio, ve a **"Settings"**
2. Scroll hasta **"Networking"**
3. Click en **"Generate Domain"**
4. Copia el dominio (ej: `tu-app.up.railway.app`)

### 4.5 Actualizar APP_URL

1. Vuelve a **"Variables"**
2. Edita `APP_URL` con el dominio:
   ```
   APP_URL=https://tu-app.up.railway.app
   ```
3. Click **"Update Variables"**

### 4.6 Esperar el Deploy

- Ve a **"Deployments"**
- Espera 2-3 minutos
- Verifica que el estado sea **"SUCCESS"** ✅

---

## 👤 PASO 5: CREAR USUARIO ADMIN

### Opción A: Usando Railway CLI (Recomendado)

```bash
# Instalar CLI
npm install -g @railway/cli

# Login
railway login

# Conectar al proyecto
railway link

# Ejecutar tinker
railway run php artisan tinker
```

Luego en tinker:

```php
$user = \App\Models\User::create([
    'name' => 'Admin FICCT',
    'email' => 'admin@ficct.edu.bo',
    'password' => \Hash::make('Admin2025!'),
    'email_verified_at' => now()
]);

$role = \App\Models\Role::where('name', 'admin')->first();
$user->roles()->attach($role->id);

echo "✅ Usuario creado: " . $user->email;
```

Presiona `Ctrl+D` para salir.

---

## 🎉 PASO 6: VERIFICAR

1. Abre tu app: `https://tu-app.up.railway.app`
2. Click en **"Login"**
3. Ingresa:
   - Email: `admin@ficct.edu.bo`
   - Password: `Admin2025!`
4. ✅ ¡Deberías ver el dashboard!

---

## ❓ TROUBLESHOOTING RÁPIDO

### Error 500
```bash
# Ver logs
railway logs

# Verificar que APP_KEY esté configurada
# Verificar que las migraciones corrieron
railway run php artisan migrate:status
```

### Assets no cargan (sin CSS)
```bash
# Localmente, asegúrate de haber ejecutado:
npm run build

# Luego haz push:
git add public/build
git commit -m "Agregar assets compilados"
git push
```

### Base de datos vacía
```bash
# Ejecutar migraciones
railway run php artisan migrate --force

# Ejecutar seeders
railway run php artisan db:seed --class=RoleSeeder
```

---

## 📊 VERIFICACIÓN FINAL

Verifica que funcione:

- ✅ Login
- ✅ Dashboard admin
- ✅ Crear docente
- ✅ Crear materia
- ✅ Crear horario
- ✅ Registrar asistencia
- ✅ Exportar Excel/PDF

---

## 🎯 PRÓXIMOS PASOS

1. **Dominio personalizado** (opcional):
   - Settings → Networking → Custom Domain
   - Agrega: `horarios.ficct.edu.bo`
   - Configura CNAME en tu DNS

2. **Configurar email real**:
   - Usa Gmail, SendGrid, Mailgun, etc.
   - Actualiza variables MAIL_* en Railway

3. **Monitoreo**:
   - Dashboard → Metrics
   - Revisa uso de CPU/RAM
   - Configura alertas

---

## 💰 COSTOS ESTIMADOS

- **PostgreSQL**: ~$5/mes
- **App (512MB RAM)**: ~$5/mes
- **Total**: ~$10-15/mes

**Primeros $5 gratis** 🎉

---

## 🆘 AYUDA

- **Docs Railway**: https://docs.railway.app
- **Docs Laravel**: https://laravel.com/docs
- **Discord Railway**: https://discord.gg/railway

---

**¿Listo? ¡Empieza con el PASO 1!** 🚀
