# 🎨 INSTRUCCIONES PARA CAMBIAR EL LOGO

## ✅ Pasos Rápidos

### 1️⃣ Preparar tu Imagen
- **Formatos soportados**: PNG, JPG, SVG, WebP
- **Tamaño recomendado**: 
  - Ancho: 200-400px
  - Alto: 200-400px
  - Proporciones: Preferiblemente cuadrado o rectangular horizontal
- **Fondo**: Transparente (PNG) para mejor resultado

### 2️⃣ Subir la Imagen
Copia tu imagen del logo a:
```
public/images/logo.png
```

**Ejemplo en Windows:**
```
C:\laragon\www\materia\public\images\logo.png
```

### 3️⃣ (Opcional) Cambiar el Nombre del Archivo
Si tu logo tiene otro nombre (ej: `mi-logo.jpg`), edita el archivo:
```
resources/views/components/application-logo.blade.php
```

Cambia la línea:
```php
$logoPath = 'images/logo.png';
```

Por:
```php
$logoPath = 'images/mi-logo.jpg';
```

### 4️⃣ Limpiar Caché (si es necesario)
Ejecuta en la terminal:
```powershell
php artisan view:clear
php artisan config:clear
```

---

## 🔍 Dónde se Mostrará el Logo

El logo aparecerá en:
- ✅ **Navegación principal** (barra superior)
- ✅ **Páginas de login/registro**
- ✅ **Todas las páginas** donde se use `<x-application-logo />`

---

## 🎨 Ajustar Tamaño del Logo

El tamaño se controla desde los archivos que usan el componente:

### Navegación Principal
**Archivo**: `resources/views/layouts/navigation.blade.php` (línea ~7)
```blade
<x-application-logo class="block w-auto h-9" />
```

Cambiar `h-9` por:
- `h-8` = 32px (más pequeño)
- `h-10` = 40px (más grande)
- `h-12` = 48px (mucho más grande)
- `h-16` = 64px (extra grande)

### Página de Login
**Archivo**: `resources/views/layouts/guest.blade.php` (línea ~21)
```blade
<x-application-logo class="w-20 h-20" />
```

Cambiar `w-20 h-20` por el tamaño deseado:
- `w-24 h-24` = 96px
- `w-32 h-32` = 128px

---

## 🛠️ Opciones Avanzadas

### Opción 1: Usar Configuración desde .env
1. Edita `config/app.php` y agrega:
```php
'logo_path' => env('LOGO_PATH', 'images/logo.png'),
```

2. Edita `.env` y agrega:
```
LOGO_PATH=images/mi-logo-personalizado.png
```

3. En `application-logo.blade.php` cambia:
```php
$logoPath = config('app.logo_path', 'images/logo.png');
```

### Opción 2: Logos Diferentes para Cada Tema
```blade
@php
    $logoPath = auth()->check() 
        ? 'images/logo-interno.png' 
        : 'images/logo-publico.png';
@endphp
```

### Opción 3: Logo Responsivo (SVG recomendado)
Si usas SVG, el logo se escalará perfectamente en todos los tamaños.

---

## 📋 Checklist de Verificación

- [ ] Imagen guardada en `public/images/`
- [ ] Formato correcto (PNG/JPG/SVG)
- [ ] Ruta actualizada en `application-logo.blade.php`
- [ ] Caché limpiado
- [ ] Logo visible en navegación
- [ ] Logo visible en login
- [ ] Tamaño ajustado correctamente

---

## 🔧 Solución de Problemas

### El logo no se ve
1. Verifica que la imagen existe en `public/images/`
2. Verifica los permisos del archivo
3. Limpia caché: `php artisan view:clear`
4. Revisa la consola del navegador (F12) por errores

### El logo se ve distorsionado
- Usa `object-contain` en vez de `object-cover`
- Ajusta las proporciones de la imagen original
- Considera usar SVG para mejor calidad

### El logo es muy grande/pequeño
- Ajusta las clases `h-X` y `w-X` en los archivos de layout
- Usa `max-h-X` o `max-w-X` para limitar el tamaño máximo

---

## 📱 Ejemplo Completo

**Tu imagen**: `logo-ficct.png` (400x400px, fondo transparente)

1. Copiar a: `public/images/logo-ficct.png`

2. Editar `application-logo.blade.php`:
```blade
@php
    $logoPath = 'images/logo-ficct.png';
@endphp
```

3. Ejecutar:
```powershell
php artisan view:clear
```

4. Actualizar navegador (Ctrl+F5)

✅ ¡Listo! Tu logo personalizado está configurado.
