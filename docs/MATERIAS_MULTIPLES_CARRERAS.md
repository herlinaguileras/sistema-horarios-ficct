# Sistema de Materias con Múltiples Carreras

## 📋 Descripción

Se ha implementado un sistema que permite que una materia pueda pertenecer a una o más carreras (relación many-to-many).

## 🗄️ Estructura de Base de Datos

### Tablas Involucradas

1. **materias** - Tabla principal de materias
   - `id`
   - `nombre`
   - `sigla`
   - `nivel_semestre`
   - `created_at`
   - `updated_at`

2. **carreras** - Catálogo de carreras
   - `id`
   - `nombre`
   - `codigo` (SIS, INF, RED, ROB)
   - `descripcion`
   - `activa` (boolean)
   - `created_at`
   - `updated_at`

3. **carrera_materia** - Tabla pivot (relación many-to-many)
   - `id`
   - `carrera_id`
   - `materia_id`
   - `created_at`
   - `updated_at`

## 🎯 Carreras Disponibles

1. **Ingeniería de Sistemas** (SIS) - Color: Azul
2. **Ingeniería Informática** (INF) - Color: Verde
3. **Ingeniería de Redes y Telecomunicaciones** (RED) - Color: Morado
4. **Ingeniería en Robótica y Mecatrónica** (ROB) - Color: Naranja

## 💻 Implementación

### Modelos

#### Materia.php
```php
public function carreras()
{
    return $this->belongsToMany(Carrera::class, 'carrera_materia');
}
```

#### Carrera.php
```php
public function materias()
{
    return $this->belongsToMany(Materia::class, 'carrera_materia');
}
```

### Controlador (MateriaController.php)

#### Crear Materia
- Se validan las carreras como array de IDs
- Se crea la materia
- Se asocian las carreras usando `attach()`

#### Actualizar Materia
- Se actualiza la información de la materia
- Se sincronizan las carreras usando `sync()` (elimina antiguas y agrega nuevas)

### Vistas

#### create.blade.php y edit.blade.php
- Checkboxes dinámicos que se generan desde la base de datos
- Permite seleccionar una o más carreras
- Validación: mínimo 1 carrera requerida

#### index.blade.php
- Muestra badges de colores con el código de cada carrera (SIS, INF, RED, ROB)
- Búsqueda en tiempo real incluye nombres de carreras
- Vista responsive con flex-wrap para múltiples badges

## ✅ Funcionalidades

1. **Crear Materia**: Seleccionar una o más carreras mediante checkboxes
2. **Editar Materia**: Modificar las carreras asignadas
3. **Visualización**: Ver todas las carreras de una materia con badges de colores
4. **Búsqueda**: Filtrar materias por nombre de carrera
5. **Validación**: No se permite crear/actualizar sin al menos una carrera

## 🎨 Interfaz de Usuario

- **Checkboxes** en lugar de select múltiple (más intuitivo)
- **Badges de colores** para identificar rápidamente las carreras
- **Búsqueda en tiempo real** funciona con nombres de carreras
- **Mensajes de validación** claros y específicos

## 📝 Ejemplo de Uso

### Materia Solo para una Carrera
```
Materia: Arquitectura de Computadoras
Carreras: [SIS]
```

### Materia Compartida entre Todas las Carreras
```
Materia: Base de Datos II
Carreras: [SIS, INF, RED, ROB]
```

### Materia para Algunas Carreras
```
Materia: Programación Web
Carreras: [SIS, INF]
```

## 🔧 Mantenimiento

### Agregar Nueva Carrera
1. Insertar en tabla `carreras` con código único
2. La carrera aparecerá automáticamente en los formularios
3. Asignar color en `index.blade.php` si se desea

### Desactivar Carrera
```php
Carrera::where('codigo', 'XXX')->update(['activa' => false]);
```

## 🚀 Ventajas del Sistema

1. **Flexibilidad**: Una materia puede ser de 1 a 4 carreras
2. **Escalabilidad**: Fácil agregar nuevas carreras
3. **Mantenibilidad**: Cambios centralizados en tabla `carreras`
4. **Performance**: Eager loading con `with('carreras')`
5. **Búsqueda**: Integrada en el sistema de búsqueda existente

## 📌 Notas Importantes

- Las carreras ya existían en la base de datos desde migraciones anteriores
- Se reutilizó la estructura existente en lugar de crear una nueva
- La migración se adaptó para usar la relación many-to-many existente
- Los datos anteriores se mantienen mediante `sync()` en lugar de `attach()`
