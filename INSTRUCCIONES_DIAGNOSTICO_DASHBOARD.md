# 🔧 DIAGNÓSTICO COMPLETO - Botones Dashboard

## ✅ SERVIDOR CORRIENDO

- **Laravel**: http://127.0.0.1:8000 ✅
- **Vite**: Puerto 5174 ✅

---

## 🎯 PRUEBAS A REALIZAR AHORA

### PRUEBA 1: Verificar Dashboard

1. **Abre el dashboard**:
   ```
   http://127.0.0.1:8000/dashboard?tab=horarios
   ```

2. **Abre DevTools** (F12)

3. **Ve a la pestaña Console**

4. **Ejecuta este comando**:
   ```javascript
   console.log('Formulario:', !!document.getElementById('dashboardHorarioExportForm'));
   console.log('submitExportForm:', typeof submitExportForm);
   console.log('exportPdfWithFilters:', typeof exportPdfWithFilters);
   ```

5. **Resultado esperado**:
   ```
   Formulario: true
   submitExportForm: function
   exportPdfWithFilters: function
   ```

---

### PRUEBA 2: Test con Diagnóstico Visual

1. **Abre esta página**:
   ```
   http://127.0.0.1:8000/diagnostico-dashboard.html
   ```

2. **Sigue las instrucciones** en la página

3. **Ejecuta el test completo** haciendo clic en el botón azul

---

### PRUEBA 3: Hacer Click en los Botones

1. **En el dashboard** (http://127.0.0.1:8000/dashboard?tab=horarios)

2. **Haz click en botón "Excel"**
   - ¿Qué pasa?
   - ¿Aparece "Exportando..."?
   - ¿Se descarga el archivo?
   - ¿Hay errores en consola?

3. **Haz click en botón "PDF"**
   - ¿Se abre nueva ventana?
   - ¿Se descarga el archivo?
   - ¿Hay errores en consola?

---

## 🐛 POSIBLES PROBLEMAS Y SOLUCIONES

### Problema A: "submitExportForm is not defined"

**Causa**: Las funciones no se están cargando desde app.blade.php

**Solución**:
```powershell
# Limpiar cache de vistas
php artisan view:clear
php artisan config:clear

# Refrescar navegador con Ctrl+Shift+R
```

---

### Problema B: Formulario es "null"

**Causa**: El formulario no existe en el DOM (puede estar oculto por Alpine.js)

**Solución en consola**:
```javascript
// Verificar si está oculto
const container = document.querySelector('[x-show="activeTab === \'horarios\'"]');
console.log('Display:', container?.style.display);

// Si es "none", cambia de tab manualmente
```

---

### Problema C: Botones no responden

**Causa**: Evento onclick no está vinculado

**Solución en consola**:
```javascript
// Forzar click manual
const btn = document.querySelector('button[onclick*="submitExportForm"]');
if (btn) {
    const onclick = btn.getAttribute('onclick');
    console.log('onclick:', onclick);
    eval(onclick); // Ejecutar manualmente
}
```

---

### Problema D: Nada pasa al hacer click

**Causa**: JavaScript no está compilado o Vite no está sirviendo los assets

**Verificar**:
```powershell
# Ver si hay errores de compilación
npm run dev
```

**En el navegador**, verifica en Network tab si se cargan:
- `/build/assets/app-*.js`
- `/build/assets/app-*.css`

Si **no se cargan**, el problema es que Vite no está compilando.

---

## 🔍 CHECKLIST DE DEPURACIÓN

Marca cada punto que verifiques:

**En Terminal**:
- [✅] Servidor Laravel corriendo (`php artisan serve`)
- [✅] Vite corriendo (`npm run dev`)
- [ ] Sin errores en consola de Laravel
- [ ] Sin errores en consola de Vite

**En Navegador (Dashboard)**:
- [ ] Dashboard carga correctamente
- [ ] Pestaña "Horario Semanal" visible
- [ ] Botones Excel y PDF visibles
- [ ] Console sin errores (F12 → Console)
- [ ] Network carga assets de Vite

**Tests JavaScript (en Console)**:
- [ ] `submitExportForm` es función
- [ ] `exportPdfWithFilters` es función
- [ ] Formulario existe (`dashboardHorarioExportForm`)
- [ ] Contenedor filtros existe (`dashboardHorarioPdfFilters`)

**Tests de Click**:
- [ ] Click en Excel muestra "Exportando..."
- [ ] Click en Excel descarga archivo
- [ ] Click en PDF abre nueva ventana
- [ ] Click en PDF descarga archivo

---

## 📊 REPORTA LOS RESULTADOS

**Copia esto y complétalo**:

```
PRUEBA 1: Verificar Dashboard
- Formulario existe: [SI/NO]
- submitExportForm existe: [SI/NO]
- exportPdfWithFilters existe: [SI/NO]
- Errores en consola: [SI/NO - descripción]

PRUEBA 2: Diagnóstico Visual
- Test completo pasó: [SI/NO]
- Elementos faltantes: [lista]

PRUEBA 3: Click en Botones
- Click Excel funciona: [SI/NO]
- Click PDF funciona: [SI/NO]
- Errores al hacer click: [descripción]

OBSERVACIONES ADICIONALES:
[Escribe aquí cualquier comportamiento extraño]
```

---

## 🚀 INSTRUCCIONES RÁPIDAS

**EJECUTA ESTO EN ORDEN**:

```powershell
# 1. Limpiar cache
php artisan view:clear
php artisan config:clear
php artisan route:clear

# 2. Verificar que los servidores estén corriendo
# Ya están corriendo según el diagnóstico

# 3. Abrir dashboard en navegador
start http://127.0.0.1:8000/dashboard?tab=horarios

# 4. Abrir diagnóstico en otra pestaña
start http://127.0.0.1:8000/diagnostico-dashboard.html
```

**En el navegador**:
1. Ve al dashboard
2. Presiona F12
3. Ve a Console
4. Ejecuta: `typeof submitExportForm`
5. ¿Qué dice?

---

## ⚡ SIGUIENTE PASO

**Dime el resultado de**:

```javascript
// Ejecuta esto en la consola del dashboard
typeof submitExportForm
```

- Si dice `"function"` → El problema es otra cosa
- Si dice `"undefined"` → Las funciones no se están cargando

**¿Qué resultado obtuviste?**
