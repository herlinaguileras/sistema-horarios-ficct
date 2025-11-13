# 🎨 PLAN DE IMPLEMENTACIÓN: CAMBIO DE LOGO

## ✅ ESTADO: COMPLETADO

---

## 📋 CAMBIOS IMPLEMENTADOS

### 1. ✅ Componente de Logo Actualizado
**Archivo**: `resources/views/components/application-logo.blade.php`

**Antes**: SVG de Laravel hardcodeado
**Ahora**: Sistema flexible con imagen personalizable

```blade
<img src="{{ asset('images/logo.png') }}" alt="Logo FICCT" />
```

### 2. ✅ Directorio Creado
**Ubicación**: `public/images/`
- Carpeta lista para recibir tu logo
- Incluye README con instrucciones

### 3. ✅ Documentación Completa
- `INSTRUCCIONES_CAMBIO_LOGO.md` - Guía detallada
- `public/images/README.md` - Referencia rápida

### 4. ✅ Script Automatizado
**Archivo**: `cambiar-logo.ps1`
- Facilita el cambio de logo
- Hace backup automático
- Limpia caché

---

## 🚀 CÓMO USAR (3 MÉTODOS)

### 🔷 MÉTODO 1: Manual (Más Simple)

1. **Prepara tu imagen**
   - Formato: PNG, JPG, o SVG
   - Tamaño recomendado: 300x300px a 500x500px
   - Nombre sugerido: `logo.png`

2. **Copia el archivo**
   ```
   C:\laragon\www\materia\public\images\logo.png
   ```

3. **Limpia caché**
   ```powershell
   php artisan view:clear
   ```

4. **Actualiza navegador**
   - Presiona `Ctrl + F5`

---

### 🔷 MÉTODO 2: Script Automatizado (Recomendado)

```powershell
# Con imagen
.\cambiar-logo.ps1 "C:\ruta\a\tu\logo.png"

# Sin parámetros (abre carpeta)
.\cambiar-logo.ps1
```

**Ventajas**:
- ✅ Hace backup del logo anterior
- ✅ Valida formato y tamaño
- ✅ Limpia caché automáticamente
- ✅ Actualiza configuración

---

### 🔷 MÉTODO 3: Configuración Avanzada (.env)

1. **Edita** `config/app.php`:
```php
'logo_path' => env('LOGO_PATH', 'images/logo.png'),
```

2. **Edita** `.env`:
```
LOGO_PATH=images/mi-logo-personalizado.png
```

3. **Actualiza** `application-logo.blade.php`:
```php
$logoPath = config('app.logo_path', 'images/logo.png');
```

---

## 🎯 UBICACIONES DEL LOGO

El logo aparece en:

| Ubicación | Archivo | Línea | Clase CSS |
|-----------|---------|-------|-----------|
| **Navegación** | `layouts/navigation.blade.php` | ~7 | `h-9` (36px) |
| **Login/Registro** | `layouts/guest.blade.php` | ~21 | `w-20 h-20` (80px) |

---

## 🎨 AJUSTAR TAMAÑO

### Navegación Principal
**Edita**: `resources/views/layouts/navigation.blade.php`

```blade
<!-- Tamaño actual -->
<x-application-logo class="block w-auto h-9" />

<!-- Opciones -->
<x-application-logo class="block w-auto h-8" />  <!-- Más pequeño (32px) -->
<x-application-logo class="block w-auto h-12" /> <!-- Más grande (48px) -->
<x-application-logo class="block w-auto h-16" /> <!-- Extra grande (64px) -->
```

### Página de Login
**Edita**: `resources/views/layouts/guest.blade.php`

```blade
<!-- Tamaño actual -->
<x-application-logo class="w-20 h-20" />

<!-- Opciones -->
<x-application-logo class="w-16 h-16" /> <!-- Más pequeño (64px) -->
<x-application-logo class="w-24 h-24" /> <!-- Más grande (96px) -->
<x-application-logo class="w-32 h-32" /> <!-- Extra grande (128px) -->
```

---

## 🔍 SISTEMA DE FALLBACK

Si la imagen no carga, se muestra automáticamente:
- Texto "FICCT" como respaldo
- No hay errores visuales
- El sistema sigue funcionando

**Código implementado**:
```blade
onerror="this.onerror=null; this.src='data:image/svg+xml,...';"
```

---

## 📊 ESPECIFICACIONES TÉCNICAS

### Formatos Soportados
- ✅ **PNG** (recomendado - soporta transparencia)
- ✅ **SVG** (mejor calidad, escalable)
- ✅ **JPG/JPEG** (sin transparencia)
- ✅ **WebP** (más comprimido)

### Tamaños Recomendados
| Uso | Ancho | Alto | Tamaño |
|-----|-------|------|--------|
| **Óptimo** | 400px | 400px | < 200KB |
| **Mínimo** | 200px | 200px | < 100KB |
| **Máximo** | 800px | 800px | < 500KB |

### Proporciones
- 🟩 **Cuadrado** (1:1) - Recomendado
- 🟨 **Horizontal** (16:9, 4:3) - Aceptable
- 🟥 **Vertical** (9:16) - No recomendado

---

## ✅ CHECKLIST DE VERIFICACIÓN

Antes de implementar:
- [ ] Imagen preparada (PNG/JPG/SVG)
- [ ] Tamaño optimizado (< 500KB)
- [ ] Fondo transparente (si es PNG)
- [ ] Nombre correcto (`logo.png`)

Después de implementar:
- [ ] Archivo en `public/images/`
- [ ] Caché limpiado
- [ ] Logo visible en navegación
- [ ] Logo visible en login
- [ ] Tamaño adecuado
- [ ] Sin distorsión

---

## 🛠️ SOLUCIÓN DE PROBLEMAS

### ❌ El logo no aparece
```powershell
# Verifica que el archivo existe
Test-Path "public\images\logo.png"

# Limpia todos los cachés
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

### ❌ El logo está distorsionado
**Solución**: Usa `object-contain` en lugar de `object-cover`
```blade
{{ $attributes->merge(['class' => 'object-contain']) }}
```

### ❌ El logo es muy grande/pequeño
**Solución**: Ajusta las clases Tailwind (`h-8`, `h-12`, etc.)

### ❌ Error 404 en la imagen
**Causa**: Ruta incorrecta
**Solución**: Verifica que uses `asset('images/logo.png')`

---

## 📚 ARCHIVOS CREADOS

```
materia/
├── cambiar-logo.ps1                        ← Script automatizado
├── INSTRUCCIONES_CAMBIO_LOGO.md           ← Guía completa
└── public/
    └── images/
        ├── README.md                       ← Documentación del directorio
        └── logo.png                        ← TU LOGO AQUÍ
```

---

## 🎯 EJEMPLO COMPLETO

**Escenario**: Tienes un logo llamado `logo-ficct.png`

```powershell
# Paso 1: Usar el script
.\cambiar-logo.ps1 "C:\Downloads\logo-ficct.png"

# Paso 2: El script automáticamente:
# - Hace backup del logo anterior
# - Copia tu imagen a public/images/logo.png
# - Limpia el caché
# - Te pregunta si quieres abrir el navegador

# Paso 3: Verificar
# - Abre http://localhost/materia
# - Ctrl+F5 para recargar
# - ¡Listo! Tu logo está activo
```

---

## 🎉 RESULTADO FINAL

Después de implementar tu logo:

```
┌──────────────────────────────────────┐
│  [TU LOGO]  Dashboard  Docentes ...  │  ← Navegación
└──────────────────────────────────────┘

┌──────────────────────┐
│                      │
│     [TU LOGO]        │  ← Página Login
│                      │
│   Login al Sistema   │
└──────────────────────┘
```

---

## 📞 SOPORTE

Si tienes problemas:
1. Revisa `INSTRUCCIONES_CAMBIO_LOGO.md`
2. Ejecuta `.\cambiar-logo.ps1` sin parámetros
3. Verifica la consola del navegador (F12)

---

## ✨ CARACTERÍSTICAS IMPLEMENTADAS

- [x] Sistema de logo personalizable
- [x] Soporte multi-formato (PNG, JPG, SVG)
- [x] Fallback automático si falla la carga
- [x] Documentación completa
- [x] Script de automatización
- [x] Backup automático
- [x] Validación de archivos
- [x] Responsive design
- [x] Limpieza de caché

---

**Fecha de implementación**: 12 de Noviembre, 2025
**Sistema**: Laravel 11 - Sistema Horarios FICCT
**Estado**: ✅ LISTO PARA USAR
