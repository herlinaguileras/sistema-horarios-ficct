# 📋 PLAN DE IMPLEMENTACIÓN: CORRECCIÓN ASISTENCIA DOCENTE

**Fecha:** 12 de Noviembre, 2025  
**Objetivo:** Implementar el flujo correcto de asistencia exclusivamente para docentes, eliminando cualquier referencia a estudiantes.

---

## 🔍 ANÁLISIS DEL SISTEMA ACTUAL

### ✅ Estado Actual - Lo que está bien:

1. **Migración de Base de Datos:** ✅ CORRECTO
   - `horario_id`: Relación con el horario específico
   - `docente_id`: Relación con el docente que registra
   - `fecha`: Día de la clase
   - `hora_registro`: Hora exacta del registro
   - `estado`: Estado de la asistencia (Presente, Ausente, Licencia)
   - `metodo_registro`: Método usado (QR, Manual, Formulario)
   - **NO hay campos de estudiantes** ✅

2. **Modelo Asistencia:** ✅ CORRECTO
   - Relaciones: `horario()`, `docente()`
   - **NO tiene relación con estudiantes** ✅

3. **AsistenciaController:** ⚠️ REVISAR
   - **Método `generarQR()`**: ✅ Existe
   - **Método `escanearQR()`**: ⚠️ NO EXISTE (necesita implementarse)
   - **Validaciones de seguridad**: ⚠️ INCOMPLETAS

### ❌ Problemas Identificados:

1. **Falta el método `escanearQR()` completo** según el diagrama de secuencia
2. **Falta validación de ventana de tiempo** (±15 minutos del horario)
3. **Faltan vistas de error específicas:**
   - `errors.qr-expired`
   - `errors.qr-unauthorized`
   - `errors.qr-time-window`
4. **Falta vista de éxito:**
   - `docente.qr-success`

---

## 🎯 PLAN DE IMPLEMENTACIÓN

### **FASE 1: Verificación de Métodos Existentes** ⏱️ 10 min

#### 1.1. Revisar AsistenciaController
- [x] ✅ `generarQR()` existe
- [ ] ❌ `escanearQR()` NO existe - **NECESITA IMPLEMENTARSE**
- [ ] ⚠️ Validaciones de seguridad incompletas

#### 1.2. Verificar Rutas
```php
// Rutas actuales encontradas:
Route::get('/asistencias/generar-qr/{horario}', 'generarQR')
    ->name('asistencias.qr.generar');

// Ruta faltante:
Route::get('/asistencias/qr-scan/{horario}/{token}', 'escanearQR')
    ->name('asistencias.qr.scan');
```

---

### **FASE 2: Implementar Método escanearQR()** ⏱️ 45 min

#### 2.1. Crear el método completo en AsistenciaController

**Ubicación:** `app/Http/Controllers/AsistenciaController.php`

**Requisitos del método:**

```php
public function escanearQR(Request $request, Horario $horario, string $token)
{
    // 1️⃣ VALIDAR FIRMA TEMPORAL (hasValidSignature)
    // 2️⃣ VALIDAR TOKEN ENCRIPTADO (decrypt)
    // 3️⃣ VALIDAR DOCENTE AUTORIZADO (docenteId == horario->docente_id)
    // 4️⃣ VALIDAR VENTANA DE TIEMPO (±15 minutos)
    // 5️⃣ VERIFICAR SI YA EXISTE REGISTRO HOY
    // 6️⃣ CREAR NUEVA ASISTENCIA
    // 7️⃣ RETORNAR VISTA DE ÉXITO/ERROR
}
```

**Validaciones específicas:**

1. **Firma de URL firmada:**
   ```php
   if (!$request->hasValidSignature()) {
       return view('errors.qr-expired');
   }
   ```

2. **Desencriptar token:**
   ```php
   try {
       $docenteId = decrypt($token);
   } catch (\Exception $e) {
       return view('errors.qr-unauthorized');
   }
   ```

3. **Validar docente autorizado:**
   ```php
   if ($docenteId != $horario->docente_id) {
       return view('errors.qr-unauthorized');
   }
   ```

4. **Validar ventana de tiempo (±15 minutos):**
   ```php
   $now = Carbon::now();
   $horarioInicio = Carbon::parse($horario->hora_inicio);
   $horarioFin = Carbon::parse($horario->hora_fin);
   
   if ($now->lt($horarioInicio->subMinutes(15)) || 
       $now->gt($horarioFin->addMinutes(15))) {
       return view('errors.qr-time-window', [
           'horario' => $horario,
           'horaActual' => $now->format('H:i'),
       ]);
   }
   ```

5. **Verificar registro duplicado:**
   ```php
   $existe = Asistencia::where('horario_id', $horario->id)
       ->where('docente_id', $docenteId)
       ->where('fecha', now()->toDateString())
       ->exists();
   
   if ($existe) {
       return view('docente.qr-success', [
           'mensaje' => 'Asistencia ya registrada anteriormente',
           'tipo' => 'info',
       ]);
   }
   ```

6. **Crear nueva asistencia:**
   ```php
   Asistencia::create([
       'horario_id' => $horario->id,
       'docente_id' => $docenteId,
       'fecha' => now()->toDateString(),
       'hora_registro' => now()->toTimeString(),
       'estado' => 'Presente',
       'metodo_registro' => 'QR',
   ]);
   
   return view('docente.qr-success', [
       'mensaje' => 'Asistencia registrada exitosamente',
       'tipo' => 'success',
   ]);
   ```

---

### **FASE 3: Actualizar Método generarQR()** ⏱️ 20 min

#### 3.1. Mejorar validaciones de seguridad

**Agregar:**

1. **Validar que el docente autenticado es dueño del horario:**
   ```php
   $docente = Auth::user()->docente;
   
   if (!$docente || $horario->docente_id !== $docente->id) {
       abort(403, 'No autorizado para generar QR de este horario');
   }
   ```

2. **Encriptar el ID del docente en el token:**
   ```php
   $token = encrypt($docente->id);
   ```

3. **Generar URL firmada temporal (1 hora):**
   ```php
   $signedUrl = URL::temporarySignedRoute(
       'asistencias.qr.scan',
       now()->addHour(),
       [
           'horario' => $horario->id,
           'token' => $token,
       ]
   );
   ```

4. **Generar código QR SVG:**
   ```php
   $qrCode = QrCode::format('svg')
       ->size(300)
       ->errorCorrection('H')
       ->generate($signedUrl);
   ```

---

### **FASE 4: Crear Vistas de Error** ⏱️ 30 min

#### 4.1. Vista: `errors/qr-expired.blade.php`

**Ubicación:** `resources/views/errors/qr-expired.blade.php`

**Contenido:**
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <div class="text-red-600 text-6xl mb-4">
                <i class="fas fa-clock"></i>
            </div>
            <h2 class="text-2xl font-bold text-red-700 mb-2">
                Código QR Expirado
            </h2>
            <p class="text-gray-600 mb-4">
                Este código QR ha expirado. Los códigos QR tienen una validez de 1 hora.
            </p>
            <p class="text-sm text-gray-500 mb-6">
                Por favor, solicita al docente que genere un nuevo código QR.
            </p>
            <a href="{{ route('docente.horarios.index') }}" 
               class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Volver a Horarios
            </a>
        </div>
    </div>
</div>
@endsection
```

#### 4.2. Vista: `errors/qr-unauthorized.blade.php`

**Ubicación:** `resources/views/errors/qr-unauthorized.blade.php`

**Contenido:**
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <div class="text-red-600 text-6xl mb-4">
                <i class="fas fa-ban"></i>
            </div>
            <h2 class="text-2xl font-bold text-red-700 mb-2">
                No Autorizado
            </h2>
            <p class="text-gray-600 mb-4">
                No tienes autorización para registrar asistencia en este horario.
            </p>
            <p class="text-sm text-gray-500 mb-6">
                Este código QR pertenece a otro docente.
            </p>
            <a href="{{ route('dashboard') }}" 
               class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Volver al Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
```

#### 4.3. Vista: `errors/qr-time-window.blade.php`

**Ubicación:** `resources/views/errors/qr-time-window.blade.php`

**Contenido:**
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <div class="text-yellow-600 text-6xl mb-4">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2 class="text-2xl font-bold text-yellow-700 mb-2">
                Fuera de Horario
            </h2>
            <p class="text-gray-600 mb-4">
                No puedes registrar asistencia en este momento.
            </p>
            <div class="bg-white rounded-lg p-4 mb-4">
                <p class="text-sm text-gray-600 mb-2">
                    <strong>Hora actual:</strong> {{ $horaActual ?? now()->format('H:i') }}
                </p>
                <p class="text-sm text-gray-600">
                    <strong>Ventana permitida:</strong> 
                    {{ Carbon\Carbon::parse($horario->hora_inicio)->subMinutes(15)->format('H:i') }} - 
                    {{ Carbon\Carbon::parse($horario->hora_fin)->addMinutes(15)->format('H:i') }}
                </p>
            </div>
            <p class="text-xs text-gray-500 mb-6">
                Puedes registrar asistencia desde 15 minutos antes hasta 15 minutos después del horario.
            </p>
            <a href="{{ route('docente.horarios.index') }}" 
               class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Volver a Horarios
            </a>
        </div>
    </div>
</div>
@endsection
```

---

### **FASE 5: Crear Vista de Éxito** ⏱️ 20 min

#### 5.1. Vista: `docente/qr-success.blade.php`

**Ubicación:** `resources/views/docente/qr-success.blade.php`

**Contenido:**
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-{{ $tipo === 'success' ? 'green' : 'blue' }}-50 border border-{{ $tipo === 'success' ? 'green' : 'blue' }}-200 rounded-lg p-6 text-center">
            <div class="text-{{ $tipo === 'success' ? 'green' : 'blue' }}-600 text-6xl mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="text-2xl font-bold text-{{ $tipo === 'success' ? 'green' : 'blue' }}-700 mb-2">
                {{ $tipo === 'success' ? '¡Asistencia Registrada!' : 'Registro Existente' }}
            </h2>
            <p class="text-gray-600 mb-4">
                {{ $mensaje }}
            </p>
            <div class="bg-white rounded-lg p-4 mb-4">
                <p class="text-sm text-gray-600 mb-1">
                    <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
                </p>
                <p class="text-sm text-gray-600">
                    <strong>Hora:</strong> {{ now()->format('H:i:s') }}
                </p>
            </div>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('docente.horarios.index') }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Mis Horarios
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="inline-block bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                    Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

### **FASE 6: Agregar Rutas Faltantes** ⏱️ 10 min

#### 6.1. Agregar ruta de escaneo QR

**Archivo:** `routes/web.php`

**Agregar después de la ruta `asistencias.qr.generar`:**

```php
Route::get('/asistencias/qr-scan/{horario}/{token}', [AsistenciaController::class, 'escanearQR'])
    ->name('asistencias.qr.scan');
```

---

### **FASE 7: Testing y Validación** ⏱️ 30 min

#### 7.1. Tests Unitarios

**Crear:** `tests/Feature/AsistenciaDocenteTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Docente;
use App\Models\Horario;
use App\Models\Asistencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

class AsistenciaDocenteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function docente_puede_generar_qr_de_su_horario()
    {
        $user = User::factory()->create();
        $docente = Docente::factory()->create(['user_id' => $user->id]);
        $horario = Horario::factory()->create(['docente_id' => $docente->id]);

        $response = $this->actingAs($user)
            ->get(route('asistencias.qr.generar', $horario));

        $response->assertStatus(200);
        $response->assertViewHas('qrCode');
    }

    /** @test */
    public function docente_no_puede_generar_qr_de_horario_ajeno()
    {
        $user = User::factory()->create();
        $docente = Docente::factory()->create(['user_id' => $user->id]);
        $otroDocente = Docente::factory()->create();
        $horario = Horario::factory()->create(['docente_id' => $otroDocente->id]);

        $response = $this->actingAs($user)
            ->get(route('asistencias.qr.generar', $horario));

        $response->assertStatus(403);
    }

    /** @test */
    public function qr_expirado_muestra_vista_de_error()
    {
        $docente = Docente::factory()->create();
        $horario = Horario::factory()->create(['docente_id' => $docente->id]);
        $token = encrypt($docente->id);

        // URL sin firma (simulando expiración)
        $response = $this->get(route('asistencias.qr.scan', [
            'horario' => $horario->id,
            'token' => $token,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('errors.qr-expired');
    }

    /** @test */
    public function asistencia_se_registra_correctamente_con_qr_valido()
    {
        $docente = Docente::factory()->create();
        $horario = Horario::factory()->create([
            'docente_id' => $docente->id,
            'hora_inicio' => now()->subMinutes(5)->format('H:i:s'),
            'hora_fin' => now()->addMinutes(45)->format('H:i:s'),
        ]);
        $token = encrypt($docente->id);

        $signedUrl = URL::temporarySignedRoute(
            'asistencias.qr.scan',
            now()->addHour(),
            ['horario' => $horario->id, 'token' => $token]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(200);
        $response->assertViewIs('docente.qr-success');

        $this->assertDatabaseHas('asistencias', [
            'horario_id' => $horario->id,
            'docente_id' => $docente->id,
            'fecha' => now()->toDateString(),
            'estado' => 'Presente',
            'metodo_registro' => 'QR',
        ]);
    }

    /** @test */
    public function no_se_permite_registro_duplicado_mismo_dia()
    {
        $docente = Docente::factory()->create();
        $horario = Horario::factory()->create([
            'docente_id' => $docente->id,
            'hora_inicio' => now()->subMinutes(5)->format('H:i:s'),
            'hora_fin' => now()->addMinutes(45)->format('H:i:s'),
        ]);

        // Crear asistencia previa
        Asistencia::create([
            'horario_id' => $horario->id,
            'docente_id' => $docente->id,
            'fecha' => now()->toDateString(),
            'hora_registro' => now()->toTimeString(),
            'estado' => 'Presente',
            'metodo_registro' => 'Manual',
        ]);

        $token = encrypt($docente->id);
        $signedUrl = URL::temporarySignedRoute(
            'asistencias.qr.scan',
            now()->addHour(),
            ['horario' => $horario->id, 'token' => $token]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(200);
        $response->assertViewIs('docente.qr-success');
        $response->assertSee('Ya registrada');

        // Solo debe haber 1 registro
        $this->assertEquals(1, Asistencia::count());
    }

    /** @test */
    public function no_se_permite_registro_fuera_de_ventana_tiempo()
    {
        $docente = Docente::factory()->create();
        $horario = Horario::factory()->create([
            'docente_id' => $docente->id,
            'hora_inicio' => now()->addHours(2)->format('H:i:s'),
            'hora_fin' => now()->addHours(3)->format('H:i:s'),
        ]);
        $token = encrypt($docente->id);

        $signedUrl = URL::temporarySignedRoute(
            'asistencias.qr.scan',
            now()->addHour(),
            ['horario' => $horario->id, 'token' => $token]
        );

        $response = $this->get($signedUrl);

        $response->assertStatus(200);
        $response->assertViewIs('errors.qr-time-window');

        $this->assertEquals(0, Asistencia::count());
    }
}
```

#### 7.2. Tests Manuales

**Checklist de pruebas:**

- [ ] Generar QR desde web como docente propietario
- [ ] Intentar generar QR de horario ajeno (debe fallar)
- [ ] Escanear QR válido desde móvil
- [ ] Escanear QR expirado (después de 1 hora)
- [ ] Escanear QR con token manipulado
- [ ] Escanear QR fuera de ventana de tiempo (±15 min)
- [ ] Escanear QR cuando ya hay asistencia registrada
- [ ] Verificar que NO se registran estudiantes

---

## 📊 RESUMEN DE ARCHIVOS A MODIFICAR/CREAR

### Modificar:
1. ✏️ `app/Http/Controllers/AsistenciaController.php`
   - Mejorar `generarQR()`
   - Crear `escanearQR()`

2. ✏️ `routes/web.php`
   - Agregar ruta `asistencias.qr.scan`

### Crear:
3. ➕ `resources/views/errors/qr-expired.blade.php`
4. ➕ `resources/views/errors/qr-unauthorized.blade.php`
5. ➕ `resources/views/errors/qr-time-window.blade.php`
6. ➕ `resources/views/docente/qr-success.blade.php`
7. ➕ `tests/Feature/AsistenciaDocenteTest.php`

---

## ⏱️ TIEMPO ESTIMADO TOTAL

- **Fase 1:** 10 min
- **Fase 2:** 45 min
- **Fase 3:** 20 min
- **Fase 4:** 30 min
- **Fase 5:** 20 min
- **Fase 6:** 10 min
- **Fase 7:** 30 min

**TOTAL:** ~2 horas 45 minutos

---

## 🎯 CRITERIOS DE ÉXITO

### Funcionales:
- ✅ Docente puede generar QR solo de sus horarios
- ✅ QR tiene validez de 1 hora
- ✅ Solo el docente propietario puede escanear
- ✅ Validación de ventana de tiempo (±15 min)
- ✅ No permite duplicados mismo día
- ✅ Registro correcto con estado "Presente" y método "QR"

### No Funcionales:
- ✅ **Sin referencias a estudiantes en ningún lugar**
- ✅ Vistas de error amigables y claras
- ✅ Seguridad: URLs firmadas y tokens encriptados
- ✅ Tests automatizados que cubren todos los casos
- ✅ Código limpio y bien documentado

---

## 🚀 PRÓXIMOS PASOS

1. **Revisar y aprobar el plan**
2. **Comenzar implementación Fase por Fase**
3. **Ejecutar tests después de cada fase**
4. **Documentar cualquier cambio adicional**

---

## 📝 NOTAS ADICIONALES

### Confirmación: NO hay estudiantes en el sistema
✅ La tabla `asistencias` **NO tiene** `estudiante_id` ni `alumno_id`  
✅ El modelo `Asistencia` **NO tiene** relación con estudiantes  
✅ El sistema es **exclusivamente para docentes**  

### Seguridad implementada:
- 🔒 URLs firmadas temporales (1 hora)
- 🔒 Tokens encriptados con ID de docente
- 🔒 Validación de propiedad del horario
- 🔒 Validación de ventana de tiempo
- 🔒 Prevención de duplicados

---

**Estado:** 📋 Plan listo para implementación  
**Prioridad:** 🔴 Alta  
**Complejidad:** 🟡 Media
