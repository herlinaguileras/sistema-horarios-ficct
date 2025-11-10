# 📚 Guía Práctica - Cómo Estructurar tu Base de Datos

**Objetivo:** Aprender a diseñar, mapear y normalizar bases de datos relacionales  
**Nivel:** Intermedio  
**Tiempo estimado:** 2-3 horas

---

## 📑 Índice

1. [Proceso de Diseño](#proceso-de-diseño)
2. [Paso 1: Análisis de Requerimientos](#paso-1-análisis)
3. [Paso 2: Modelo Conceptual (ER)](#paso-2-modelo-conceptual)
4. [Paso 3: Modelo Lógico](#paso-3-modelo-lógico)
5. [Paso 4: Normalización](#paso-4-normalización)
6. [Paso 5: Modelo Físico](#paso-5-modelo-físico)
7. [Paso 6: Implementación](#paso-6-implementación)
8. [Herramientas Recomendadas](#herramientas)
9. [Checklist Final](#checklist)

---

## 1. Proceso de Diseño

```
┌─────────────────────────────────────────────────────────┐
│           PROCESO DE DISEÑO DE BASE DE DATOS            │
└─────────────────────────────────────────────────────────┘

1. ANÁLISIS DE REQUERIMIENTOS
   ↓ ¿Qué datos necesito almacenar?
   ↓ ¿Qué operaciones se realizarán?
   
2. MODELO CONCEPTUAL (ER)
   ↓ Identificar entidades
   ↓ Identificar relaciones
   ↓ Definir cardinalidad
   
3. MODELO LÓGICO
   ↓ Convertir a tablas
   ↓ Definir llaves primarias
   ↓ Definir llaves foráneas
   
4. NORMALIZACIÓN
   ↓ Aplicar 1FN, 2FN, 3FN
   ↓ Eliminar redundancias
   
5. MODELO FÍSICO
   ↓ Definir tipos de datos
   ↓ Crear índices
   ↓ Optimizar consultas
   
6. IMPLEMENTACIÓN
   ↓ Crear migraciones
   ↓ Crear modelos ORM
   ↓ Probar integridad
```

---

## Paso 1: Análisis de Requerimientos

### 1.1. Identificar Actores

**Pregunta:** ¿Quiénes usarán el sistema?

**En nuestro proyecto:**
- ✅ **Administrador:** Gestiona todo el sistema
- ✅ **Docente:** Marca asistencias, consulta horarios
- ✅ **Sistema:** Genera reportes automáticos

### 1.2. Identificar Procesos

**Pregunta:** ¿Qué hace cada actor?

| Actor | Procesos |
|-------|----------|
| **Administrador** | • Crear usuarios<br>• Asignar roles<br>• Registrar docentes<br>• Crear semestres/materias<br>• Asignar carga horaria<br>• Generar reportes |
| **Docente** | • Ver horario<br>• Marcar asistencia<br>• Consultar historial |
| **Sistema** | • Enviar notificaciones<br>• Generar estadísticas<br>• Exportar reportes |

### 1.3. Identificar Datos Necesarios

**Método:** Hacer preguntas sobre cada proceso

**Ejemplo - Registrar Docente:**
```
¿Qué datos necesito?
├─ Datos personales: nombre, email, CI, teléfono
├─ Datos institucionales: código docente, facultad
├─ Datos laborales: fecha contratación, estado
└─ Datos académicos: títulos obtenidos
```

**Ejemplo - Asignar Carga Horaria:**
```
¿Qué datos necesito?
├─ Semestre: nombre, fechas, estado
├─ Materia: nombre, sigla, nivel, carrera
├─ Docente: quien dicta
├─ Grupo: nombre del grupo (SA, SB, etc.)
└─ Horario: día, hora inicio, hora fin, aula
```

---

## Paso 2: Modelo Conceptual (ER)

### 2.1. Identificar Entidades

**Pregunta:** ¿Qué "cosas" existen en el sistema?

**Técnica:** Buscar sustantivos en los requerimientos.

**En nuestro proyecto:**
```
✅ Usuario
✅ Docente
✅ Rol
✅ Permiso
✅ Semestre
✅ Materia
✅ Aula
✅ Grupo
✅ Horario
✅ Asistencia
✅ Título
```

### 2.2. Identificar Atributos

**Pregunta:** ¿Qué características tiene cada entidad?

**Ejemplo - Entidad DOCENTE:**

| Atributo | Tipo | ¿Es clave? |
|----------|------|-----------|
| id | Número | Sí (PK) |
| codigo_docente | Texto | Único |
| carnet_identidad | Texto | No |
| telefono | Texto | No |
| facultad | Texto | No |
| estado | Texto | No |
| fecha_contratacion | Fecha | No |

**Ejemplo - Entidad MATERIA:**

| Atributo | Tipo | ¿Es clave? |
|----------|------|-----------|
| id | Número | Sí (PK) |
| nombre | Texto | No |
| sigla | Texto | Único |
| nivel_semestre | Número | No |
| carrera | Texto | No |

### 2.3. Identificar Relaciones

**Pregunta:** ¿Cómo se relacionan las entidades?

**Técnica:** Usar verbos que conecten entidades.

**Ejemplos:**

```
USUARIO ─── tiene ─── DOCENTE
        (1:1)

DOCENTE ─── posee ─── TÍTULOS
        (1:N)

DOCENTE ─── dicta ─── GRUPOS
        (1:N)

GRUPO ─── pertenece a ─── SEMESTRE
      (N:1)

GRUPO ─── tiene ─── HORARIOS
      (1:N)

HORARIO ─── genera ─── ASISTENCIAS
        (1:N)

USUARIO ─── tiene ─── ROLES
        (M:N)
```

### 2.4. Definir Cardinalidad

**Tipos de Relaciones:**

#### 1:1 (Uno a Uno)
```
USUARIO ────── DOCENTE
  (1)            (1)

Ejemplo: Un usuario puede tener UN docente.
         Un docente pertenece a UN usuario.
```

#### 1:N (Uno a Muchos)
```
DOCENTE ────── TÍTULOS
  (1)          (muchos)

Ejemplo: Un docente puede tener MUCHOS títulos.
         Un título pertenece a UN docente.
```

#### N:1 (Muchos a Uno)
```
GRUPOS ────── SEMESTRE
(muchos)        (1)

Ejemplo: Muchos grupos pertenecen a UN semestre.
         Un semestre tiene MUCHOS grupos.
```

#### M:N (Muchos a Muchos)
```
USUARIOS ────── ROLES
(muchos)      (muchos)

Ejemplo: Un usuario puede tener MUCHOS roles.
         Un rol puede ser asignado a MUCHOS usuarios.

SOLUCIÓN: Tabla pivot "role_user"
```

---

## Paso 3: Modelo Lógico

### 3.1. Convertir Entidades a Tablas

**Regla:** Cada entidad = Una tabla

```
ENTIDAD: Docente

TABLA: docentes
┌────────────────────────┬──────────┬─────────┐
│ Campo                  │ Tipo     │ Clave   │
├────────────────────────┼──────────┼─────────┤
│ id                     │ BIGINT   │ PK      │
│ user_id                │ BIGINT   │ FK      │
│ codigo_docente         │ VARCHAR  │ UNIQUE  │
│ carnet_identidad       │ VARCHAR  │         │
│ telefono               │ VARCHAR  │         │
│ facultad               │ VARCHAR  │         │
│ estado                 │ VARCHAR  │         │
│ fecha_contratacion     │ DATE     │         │
│ created_at             │ TIMESTAMP│         │
│ updated_at             │ TIMESTAMP│         │
└────────────────────────┴──────────┴─────────┘
```

### 3.2. Definir Llaves Primarias (PK)

**Regla:** Toda tabla debe tener una PK única.

**Opciones:**
- ✅ **ID Auto-incremental** (recomendado): `id BIGINT AUTO_INCREMENT`
- ❌ Llaves naturales (ej: email): Pueden cambiar
- ✅ **Llaves compuestas** (solo en tablas pivot): `PRIMARY KEY (user_id, role_id)`

**Ejemplo:**
```sql
CREATE TABLE docentes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    -- otros campos...
);
```

### 3.3. Definir Llaves Foráneas (FK)

**Regla:** Las relaciones se implementan con FKs.

**Ejemplo - Relación 1:1 (users → docentes):**
```sql
CREATE TABLE docentes (
    id BIGINT PRIMARY KEY,
    user_id BIGINT UNIQUE, -- UNIQUE asegura 1:1
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Ejemplo - Relación 1:N (docente → títulos):**
```sql
CREATE TABLE titulos (
    id BIGINT PRIMARY KEY,
    docente_id BIGINT,
    nombre VARCHAR(255),
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE
);
```

**Ejemplo - Relación M:N (users ↔ roles):**
```sql
-- Tabla pivot
CREATE TABLE role_user (
    user_id BIGINT,
    role_id BIGINT,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

### 3.4. Definir Acciones de Integridad

**ON DELETE:**
- `CASCADE`: Elimina en cascada (ej: si borro user, borro docente)
- `RESTRICT`: Previene eliminación si hay referencias
- `SET NULL`: Pone NULL en la FK
- `NO ACTION`: No hace nada (default)

**ON UPDATE:**
- Similar a ON DELETE

**Ejemplo:**
```sql
FOREIGN KEY (user_id) REFERENCES users(id) 
    ON DELETE CASCADE    -- Si borro user, borro docente
    ON UPDATE CASCADE    -- Si cambio id de user, actualiza FK
```

---

## Paso 4: Normalización

### 4.1. Primera Forma Normal (1FN)

**Regla:** Eliminar valores multivaluados.

**❌ NO Normalizado:**
```sql
CREATE TABLE docentes (
    id INT,
    nombre VARCHAR(255),
    titulos VARCHAR(500)  -- "Ing. Sistemas, Maestría, PhD"
);
```

**✅ Normalizado (1FN):**
```sql
CREATE TABLE docentes (
    id INT,
    nombre VARCHAR(255)
);

CREATE TABLE titulos (
    id INT,
    docente_id INT,
    nombre VARCHAR(255),
    FOREIGN KEY (docente_id) REFERENCES docentes(id)
);
```

---

### 4.2. Segunda Forma Normal (2FN)

**Regla:** Eliminar dependencias parciales (aplica a PKs compuestas).

**❌ NO Normalizado:**
```sql
CREATE TABLE grupos (
    semestre_id INT,
    materia_id INT,
    docente_id INT,
    nombre_materia VARCHAR(255),  -- Depende solo de materia_id
    PRIMARY KEY (semestre_id, materia_id, docente_id)
);
```

**✅ Normalizado (2FN):**
```sql
CREATE TABLE grupos (
    id INT PRIMARY KEY,
    semestre_id INT,
    materia_id INT,
    docente_id INT
);

CREATE TABLE materias (
    id INT PRIMARY KEY,
    nombre VARCHAR(255)
);
```

---

### 4.3. Tercera Forma Normal (3FN)

**Regla:** Eliminar dependencias transitivas.

**❌ NO Normalizado:**
```sql
CREATE TABLE grupos (
    id INT,
    docente_id INT,
    facultad_docente VARCHAR(255)  -- Depende de docente_id, no de id
);
```

**✅ Normalizado (3FN):**
```sql
CREATE TABLE grupos (
    id INT,
    docente_id INT
);

CREATE TABLE docentes (
    id INT,
    facultad VARCHAR(255)
);
```

---

### 4.4. Checklist de Normalización

- [ ] ¿Todos los campos son atómicos? (1FN)
- [ ] ¿No hay dependencias parciales? (2FN)
- [ ] ¿No hay dependencias transitivas? (3FN)
- [ ] ¿Las llaves foráneas están bien definidas?
- [ ] ¿No hay redundancia innecesaria?

---

## Paso 5: Modelo Físico

### 5.1. Elegir Tipos de Datos

**Guía de Tipos (PostgreSQL/MySQL):**

| Dato | Tipo Recomendado | Ejemplo |
|------|------------------|---------|
| ID único | BIGINT AUTO_INCREMENT | `id BIGINT` |
| Texto corto | VARCHAR(255) | `nombre VARCHAR(255)` |
| Texto largo | TEXT | `descripcion TEXT` |
| Número entero | INTEGER | `nivel INTEGER` |
| Decimal | DECIMAL(10,2) | `precio DECIMAL(10,2)` |
| Fecha | DATE | `fecha_inicio DATE` |
| Hora | TIME | `hora_inicio TIME` |
| Fecha y hora | TIMESTAMP | `created_at TIMESTAMP` |
| Booleano | BOOLEAN | `activo BOOLEAN` |
| Enum | ENUM() o VARCHAR | `estado ENUM('Activo','Inactivo')` |

### 5.2. Crear Índices

**¿Cuándo crear índices?**
- ✅ En llaves primarias (automático)
- ✅ En llaves foráneas (recomendado)
- ✅ En columnas de búsqueda frecuente
- ✅ En columnas de filtrado (WHERE, JOIN)

**Ejemplo:**
```sql
-- Índice en FK
CREATE INDEX idx_grupos_docente ON grupos(docente_id);

-- Índice en campo de búsqueda
CREATE INDEX idx_asistencias_fecha ON asistencias(fecha);

-- Índice compuesto
CREATE INDEX idx_horarios_lookup ON horarios(dia_semana, hora_inicio);
```

### 5.3. Definir Constraints

```sql
CREATE TABLE horarios (
    id BIGINT PRIMARY KEY,
    dia_semana TINYINT,
    hora_inicio TIME,
    hora_fin TIME,
    
    -- Constraint de validación
    CONSTRAINT chk_dia CHECK (dia_semana BETWEEN 1 AND 7),
    CONSTRAINT chk_horario CHECK (hora_fin > hora_inicio)
);
```

---

## Paso 6: Implementación

### 6.1. Crear Migraciones (Laravel)

```bash
# Crear migración
php artisan make:migration create_docentes_table
```

```php
// database/migrations/2025_XX_XX_create_docentes_table.php
public function up()
{
    Schema::create('docentes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('codigo_docente')->unique();
        $table->string('carnet_identidad');
        $table->string('telefono')->nullable();
        $table->string('facultad')->default('FICCT');
        $table->string('estado')->default('Activo');
        $table->date('fecha_contratacion')->nullable();
        $table->timestamps();
    });
}
```

### 6.2. Crear Modelos ORM

```php
// app/Models/Docente.php
class Docente extends Model
{
    protected $fillable = [
        'user_id', 'codigo_docente', 'carnet_identidad',
        'telefono', 'facultad', 'estado', 'fecha_contratacion'
    ];

    // Relación 1:1
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación 1:N
    public function titulos()
    {
        return $this->hasMany(Titulo::class);
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
```

### 6.3. Ejecutar Migraciones

```bash
# Ejecutar todas las migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Limpiar y recrear BD
php artisan migrate:fresh

# Con seeders
php artisan migrate:fresh --seed
```

---

## 7. Herramientas Recomendadas

### Diseño de Diagramas

1. **Draw.io** (Gratis, Online)
   - URL: https://app.diagrams.net/
   - Plantillas ER incluidas
   - Export a PNG, PDF, SVG

2. **dbdiagram.io** (Gratis, Online)
   - URL: https://dbdiagram.io/
   - Sintaxis simple tipo código
   - Genera SQL automáticamente

3. **PlantUML** (Gratis, CLI/Online)
   - URL: https://plantuml.com/
   - Diagrama como código
   - Integración con VSCode

4. **MySQL Workbench** (Gratis, Desktop)
   - Ingeniería inversa desde BD
   - Genera migraciones

5. **Lucidchart** (Pago, Online)
   - Muy profesional
   - Colaboración en tiempo real

### Ejemplo dbdiagram.io

```dbml
Table users {
  id bigint [pk, increment]
  name varchar
  email varchar [unique]
  password varchar
  created_at timestamp
}

Table docentes {
  id bigint [pk, increment]
  user_id bigint [ref: - users.id, unique]
  codigo_docente varchar [unique]
  estado varchar
  created_at timestamp
}

Table titulos {
  id bigint [pk, increment]
  docente_id bigint [ref: > docentes.id]
  nombre varchar
}
```

---

## 8. Checklist Final

### Diseño Conceptual
- [ ] Identificadas todas las entidades
- [ ] Identificados todos los atributos
- [ ] Definidas todas las relaciones
- [ ] Definida cardinalidad de cada relación
- [ ] Diagrama ER creado

### Diseño Lógico
- [ ] Convertidas entidades a tablas
- [ ] Definidas llaves primarias
- [ ] Definidas llaves foráneas
- [ ] Definidas restricciones de integridad
- [ ] Aplicada normalización (1FN, 2FN, 3FN)

### Diseño Físico
- [ ] Elegidos tipos de datos apropiados
- [ ] Creados índices necesarios
- [ ] Definidos constraints
- [ ] Optimizadas consultas frecuentes

### Implementación
- [ ] Migraciones creadas
- [ ] Modelos ORM creados
- [ ] Relaciones Eloquent definidas
- [ ] Seeders creados (datos de prueba)
- [ ] Probada integridad referencial

### Documentación
- [ ] Diagrama ER actualizado
- [ ] Diccionario de datos creado
- [ ] Ejemplos de consultas documentados
- [ ] Guía de uso creada

---

## 9. Ejemplo Completo: Entidad "Grupo"

### Paso 1: Identificar Requerimiento

```
El sistema debe permitir asignar un docente a una materia 
en un semestre específico, formando grupos (ej: SA, SB).
```

### Paso 2: Modelo Conceptual

```
ENTIDAD: Grupo
ATRIBUTOS: 
  - id (PK)
  - nombre (ej: "SA", "SB")
  
RELACIONES:
  - Pertenece a un SEMESTRE (N:1)
  - Pertenece a una MATERIA (N:1)
  - Es dictado por un DOCENTE (N:1)
  - Tiene múltiples HORARIOS (1:N)
```

### Paso 3: Modelo Lógico

```sql
CREATE TABLE grupos (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    semestre_id BIGINT NOT NULL,
    materia_id BIGINT NOT NULL,
    docente_id BIGINT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (semestre_id) REFERENCES semestres(id),
    FOREIGN KEY (materia_id) REFERENCES materias(id),
    FOREIGN KEY (docente_id) REFERENCES docentes(id)
);
```

### Paso 4: Normalización

✅ **1FN:** Todos los campos son atómicos  
✅ **2FN:** No hay PKs compuestas  
✅ **3FN:** No hay dependencias transitivas (nombre_materia estaría mal)

### Paso 5: Migración Laravel

```php
Schema::create('grupos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('semestre_id')->constrained('semestres');
    $table->foreignId('materia_id')->constrained('materias');
    $table->foreignId('docente_id')->constrained('docentes');
    $table->string('nombre');
    $table->timestamps();
});
```

### Paso 6: Modelo Eloquent

```php
class Grupo extends Model
{
    protected $fillable = ['semestre_id', 'materia_id', 'docente_id', 'nombre'];

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
```

---

## 10. Errores Comunes a Evitar

❌ **No normalizar**
```sql
-- MAL: Datos duplicados
CREATE TABLE grupos (
    id INT,
    materia_nombre VARCHAR,  -- Duplicado para cada grupo
    materia_sigla VARCHAR    -- Duplicado
);
```

❌ **PKs mal elegidas**
```sql
-- MAL: Email puede cambiar
CREATE TABLE users (
    email VARCHAR PRIMARY KEY  -- ❌
);

-- BIEN: ID auto-incremental
CREATE TABLE users (
    id BIGINT PRIMARY KEY,     -- ✅
    email VARCHAR UNIQUE
);
```

❌ **FKs sin índices**
```sql
-- MAL: FK sin índice (lento)
CREATE TABLE grupos (
    docente_id BIGINT  -- ❌ Sin INDEX
);

-- BIEN: FK con índice
CREATE INDEX idx_grupos_docente ON grupos(docente_id);
```

❌ **No definir ON DELETE**
```sql
-- MAL: No se sabe qué pasa al borrar
FOREIGN KEY (user_id) REFERENCES users(id)  -- ❌

-- BIEN: Acción definida
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE  -- ✅
```

---

## 11. Recursos Adicionales

### Libros
- 📖 "Database Design for Mere Mortals" - Michael Hernandez
- 📖 "SQL Antipatterns" - Bill Karwin

### Cursos Online
- 🎓 "Database Design" - Udemy
- 🎓 "SQL and PostgreSQL" - The Complete Developer's Guide

### Documentación Oficial
- 📚 PostgreSQL: https://www.postgresql.org/docs/
- 📚 MySQL: https://dev.mysql.com/doc/
- 📚 Laravel Migrations: https://laravel.com/docs/migrations

---

**¡Ahora tienes todas las herramientas para estructurar tu base de datos profesionalmente!** 🚀

**Fecha:** 27 de Octubre, 2025  
**Autor:** GitHub Copilot
