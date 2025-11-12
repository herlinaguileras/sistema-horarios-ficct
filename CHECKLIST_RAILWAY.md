# ✅ Checklist Rápido - Despliegue en Railway

## 📋 Preparación Local (5 minutos)

- [ ] **Verificar archivos necesarios**
  ```bash
  ✓ Dockerfile
  ✓ railway.json
  ✓ .env.example
  ✓ .env.production
  ✓ docker/nginx.conf
  ✓ docker/supervisord.conf
  ✓ docker/start.sh
  ```

- [ ] **Generar APP_KEY**
  ```bash
  php artisan key:generate --show
  ```
  📝 Guardar key: `_______________________________________`

- [ ] **Commit final**
  ```bash
  git add .
  git commit -m "chore: Preparar para despliegue en Railway"
  git push origin main
  ```

---

## 🚀 Railway Setup (10 minutos)

### Paso 1: Crear Proyecto
- [ ] Ir a https://railway.app
- [ ] Login con GitHub
- [ ] Click **"New Project"**
- [ ] Seleccionar **"Deploy from GitHub repo"**
- [ ] Elegir: `herlinaguileras/sistema-horarios-ficct`

### Paso 2: Agregar PostgreSQL
- [ ] En el proyecto, click **"+ New"**
- [ ] Seleccionar **"Database"** → **"PostgreSQL"**
- [ ] Esperar a que se cree

### Paso 3: Variables de Entorno
- [ ] Click en tu servicio (no en PostgreSQL)
- [ ] Ir a **"Variables"** → **"RAW Editor"**
- [ ] Copiar contenido de `.env.production`
- [ ] **Actualizar estos valores:**

```bash
APP_KEY=base64:LA_KEY_GENERADA_ANTES

# Después del primer deploy, actualizar con la URL real:
APP_URL=https://tu-proyecto.up.railway.app
ASSET_URL=https://tu-proyecto.up.railway.app
```

### Paso 4: Desplegar
- [ ] Click en **"Deploy"** (o esperar deploy automático)
- [ ] Ver logs en tiempo real
- [ ] Esperar ~5-10 minutos

---

## 🔧 Post-Despliegue (5 minutos)

### Ejecutar Migraciones
- [ ] Instalar Railway CLI:
  ```bash
  npm i -g @railway/cli
  railway login
  railway link
  ```

- [ ] Ejecutar comandos:
  ```bash
  railway run php artisan migrate --force
  railway run php artisan storage:link
  railway run php artisan config:cache
  railway run php artisan route:cache
  railway run php artisan view:cache
  ```

### Crear Usuario Admin
- [ ] Opción 1 - Con script:
  ```bash
  railway run php scripts/create-superadmin.php
  ```

- [ ] Opción 2 - Con Tinker:
  ```bash
  railway run php artisan tinker
  
  # En Tinker:
  $user = \App\Models\User::create([
      'name' => 'Administrador',
      'email' => 'admin@ficct.edu.bo',
      'password' => bcrypt('TU_CONTRASEÑA_SEGURA'),
      'email_verified_at' => now(),
  ]);
  
  $adminRole = \App\Models\Role::where('name', 'admin')->first();
  $user->roles()->attach($adminRole->id);
  exit
  ```

---

## 🌐 Dominio Personalizado (Opcional)

### En Railway:
- [ ] Service → **Settings** → **Networking**
- [ ] Click **"Custom Domain"**
- [ ] Ingresar: `horarios.ficct.edu.bo`
- [ ] Copiar el **CNAME** que Railway proporciona

### En tu proveedor DNS:
- [ ] Agregar registro CNAME:
  ```
  Tipo: CNAME
  Nombre: horarios (o www)
  Valor: tu-proyecto.up.railway.app
  TTL: 3600
  ```

### Actualizar Variables:
- [ ] En Railway Variables, actualizar:
  ```bash
  APP_URL=https://horarios.ficct.edu.bo
  ASSET_URL=https://horarios.ficct.edu.bo
  SESSION_DOMAIN=.ficct.edu.bo
  ```

- [ ] Esperar propagación DNS (5-30 minutos)

---

## ✅ Verificación Final

### Tests Básicos:
- [ ] Abrir: `https://tu-proyecto.up.railway.app`
- [ ] Página principal carga ✅
- [ ] CSS/JS cargan correctamente ✅
- [ ] Login funciona ✅
- [ ] Dashboard aparece ✅
- [ ] Módulos visibles ✅
- [ ] HTTPS activo (candado verde) ✅

### Test de Base de Datos:
- [ ] Crear un registro de prueba
- [ ] Editar el registro
- [ ] Eliminar el registro
- [ ] Todo funciona ✅

---

## 🐛 Problemas Comunes

### Error: "Application Key Not Set"
```bash
# Generar key y actualizar en Railway Variables
php artisan key:generate --show
# Copiar y pegar en APP_KEY
# Redeploy
```

### Error: "500 Internal Server Error"
```bash
# Ver logs
railway logs

# Verificar permisos
railway run ls -la storage/
railway run chmod -R 775 storage/
```

### Assets no cargan (404)
```bash
# Ejecutar
railway run php artisan storage:link
railway run npm run build

# Verificar ASSET_URL en variables
```

### Base de datos no conecta
```bash
# Verificar variables PostgreSQL
railway run env | grep PG

# Deben aparecer: PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD
```

---

## 📊 Tiempo Estimado Total

- ✅ Preparación: **5 minutos**
- ✅ Railway Setup: **10 minutos**
- ✅ Post-Despliegue: **5 minutos**
- ✅ Dominio (opcional): **30 minutos** (propagación DNS)

**Total: ~20-50 minutos** 🎉

---

## 📞 Recursos

- **Documentación Completa**: `docs/DESPLIEGUE_RAILWAY.md`
- **Railway Docs**: https://docs.railway.app
- **Railway Status**: https://railway.app/status
- **Soporte Railway**: https://discord.gg/railway

---

## 🎉 ¡Felicidades!

Tu aplicación está en producción y accesible desde internet.

**Credenciales por defecto:**
- Email: `admin@ficct.edu.bo`
- Password: La que configuraste

**¡No olvides cambiar la contraseña después del primer login!**
