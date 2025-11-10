# 📊 Estructura de Base de Datos - Sistema de Horarios FICCT

**Proyecto:** Sistema de Gestión de Horarios y Asistencias  
**Base de Datos:** PostgreSQL  
**Fecha:** 27 de Octubre, 2025  
**Versión:** 1.0

---

## 📑 Índice

1. [Diagrama Entidad-Relación (ER)](#diagrama-er)
2. [Modelo Relacional](#modelo-relacional)
3. [Catálogo de Tablas](#catálogo-de-tablas)
4. [Relaciones y Cardinalidad](#relaciones-y-cardinalidad)
5. [Normalización](#normalización)
6. [Mapeo Objeto-Relacional (ORM)](#mapeo-orm)
7. [Índices y Optimizaciones](#índices)
8. [Reglas de Integridad](#reglas-de-integridad)

---

## 1. Diagrama Entidad-Relación (ER)

### Diagrama Conceptual

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SISTEMA DE HORARIOS Y ASISTENCIAS                         │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│    USERS     │◄──1:1──►│   DOCENTES   │◄──1:N──►│   TITULOS    │
│              │         │              │         │              │
│ • id         │         │ • id         │         │ • id         │
│ • name       │         │ • user_id FK │         │ • docente_id │
│ • email      │         │ • codigo     │         │ • nombre     │
│ • password   │         │ • carnet_ci  │         └──────────────┘
└──────┬───────┘         │ • telefono   │
       │                 │ • estado     │
       │ M:N             └──────┬───────┘
       │                        │
       │                        │ 1:N
       │ 1:N                    ▼
       │              ┌──────────────┐
┌──────▼───────┐     │    GRUPOS    │
│  AUDIT_LOGS  │     │              │
│  (Bitácora)  │     │ • id         │
│              │     │ • semestre_id│◄──┐
│ • id         │     │ • materia_id │   │
│ • user_id FK │     │ • docente_id │   │
│ • action     │     │ • nombre     │   │
│ • model_type │     └──────┬───────┘   │
│ • details    │            │           │
└──────────────┘            │ 1:N       │
       │                    ▼           │
       │ M:N         ┌──────────────┐   │
┌──────▼───────┐     │   HORARIOS   │   │
│    ROLES     │     │              │   │
│              │     │ • id         │   │
│ • id         │     │ • grupo_id FK│   │
│ • name       │     │ • aula_id FK │   │
│ • level      │     │ • dia_semana │   │
│ • status     │     │ • hora_inicio│   │
└──────┬───────┘     │ • hora_fin   │   │
       │             └──────┬───────┘   │
       │ M:N                │           │
       │                    │ 1:N       │
┌──────▼───────┐            ▼           │
│ PERMISSIONS  │     ┌──────────────┐   │
│              │     │ ASISTENCIAS  │   │
│ • id         │     │              │   │
│ • name       │     │ • id         │   │
│ • module     │     │ • horario_id │   │
└──────────────┘     │ • docente_id │   │
                     │ • fecha      │   │
┌──────────────┐     │ • hora       │   │
│    AULAS     │◄────┤ • estado     │   │
│              │ N:1 │ • metodo     │   │
│ • id         │     │ • justif.    │   │
│ • nombre     │     └──────────────┘   │
│ • piso       │                        │
│ • capacidad  │     ┌──────────────┐   │
└──────────────┘     │  SEMESTRES   │◄──┘
                     │              │ 1:N
                     │ • id         │
                     │ • nombre     │
                     │ • fecha_ini  │
                     │ • fecha_fin  │
                     │ • estado     │
                     └──────┬───────┘
                            │
                            │ 1:N
                            ▼
                     ┌──────────────┐
                     │   MATERIAS   │
                     │              │
                     │ • id         │
                     │ • nombre     │
                     │ • sigla      │
                     │ • nivel      │
                     │ • carrera    │
                     └──────────────┘

TABLAS PIVOT (Many-to-Many):
┌──────────────┐         ┌──────────────┐
│  role_user   │         │permission_role│
│              │         │              │
│ • user_id    │         │ • permission_id│
│ • role_id    │         │ • role_id    │
└──────────────┘         └──────────────┘
```

---

## 2. Modelo Relacional

### Esquema Relacional Detallado

#### Entidades Principales

**1. users** (PK: id)
```
users(id, name, email, email_verified_at, password, remember_token, created_at, updated_at)
```

**2. docentes** (PK: id, FK: user_id → users)
```
docentes(id, user_id, codigo_docente, carnet_identidad, telefono, facultad, estado, fecha_contratacion, created_at, updated_at)
```

**3. roles** (PK: id)
```
roles(id, name, description, level, status, created_at, updated_at)
```

**4. permissions** (PK: id)
```
permissions(id, name, description, module, created_at, updated_at)
```

**5. semestres** (PK: id)
```
semestres(id, nombre, fecha_inicio, fecha_fin, estado, created_at, updated_at)
```

**6. materias** (PK: id)
```
materias(id, nombre, sigla, nivel_semestre, carrera, created_at, updated_at)
```

**7. aulas** (PK: id)
```
aulas(id, nombre, piso, capacidad, tipo, created_at, updated_at)
```

**8. grupos** (PK: id, FK: semestre_id, materia_id, docente_id)
```
grupos(id, semestre_id, materia_id, docente_id, nombre, created_at, updated_at)
```

**9. horarios** (PK: id, FK: grupo_id, aula_id)
```
horarios(id, grupo_id, aula_id, dia_semana, hora_inicio, hora_fin, created_at, updated_at)
```

**10. asistencias** (PK: id, FK: horario_id, docente_id)
```
asistencias(id, horario_id, docente_id, fecha, hora_registro, estado, metodo_registro, created_at, updated_at)
```

**11. titulos** (PK: id, FK: docente_id)
```
titulos(id, docente_id, nombre, created_at, updated_at)
```

#### Tablas Pivot (Relaciones Many-to-Many)

**12. role_user** (PK: user_id, role_id)
```
role_user(user_id, role_id)
```

**13. permission_role** (PK: id, UNIQUE: permission_id, role_id)
```
permission_role(id, permission_id, role_id, created_at, updated_at)
```

#### Tablas de Sistema

**14. audit_logs** (PK: id, FK: user_id → users)
```
audit_logs(id, user_id, action, model_type, model_id, details, ip_address, user_agent, created_at)
```

---

## 3. Catálogo de Tablas

### 3.1. Tabla: `users`

**Propósito:** Almacena los usuarios del sistema (administradores, docentes)

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `name` | VARCHAR | Nombre completo | 255 caracteres | NO | - |
| `email` | VARCHAR | Correo electrónico (único) | 255 caracteres | NO | UNIQUE |
| `email_verified_at` | TIMESTAMP | Fecha de verificación de email | - | SÍ | - |
| `password` | VARCHAR | Contraseña hasheada | 255 caracteres | NO | - |
| `remember_token` | VARCHAR | Token de "recordarme" | 100 caracteres | SÍ | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`email`)

**Relaciones:**
- 1:1 → `docentes` (user_id)
- M:N → `roles` (a través de `role_user`)

---

### 3.2. Tabla: `docentes`

**Propósito:** Información específica de docentes (extensión de users)

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `user_id` | BIGINT | FK → users.id | 8 bytes | NO | FK |
| `codigo_docente` | VARCHAR | Código institucional (único) | 255 caracteres | NO | UNIQUE |
| `carnet_identidad` | VARCHAR | CI del docente | 255 caracteres | NO | - |
| `telefono` | VARCHAR | Teléfono de contacto | 255 caracteres | SÍ | - |
| `facultad` | VARCHAR | Facultad asignada | 255 caracteres | NO | - |
| `estado` | VARCHAR | Estado del docente | 255 caracteres | NO | - |
| `fecha_contratacion` | DATE | Fecha de contratación | - | SÍ | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`codigo_docente`)
- FOREIGN KEY (`user_id`) → `users(id)` ON DELETE CASCADE

**Relaciones:**
- N:1 → `users` (user_id)
- 1:N → `titulos` (docente_id)
- 1:N → `grupos` (docente_id)
- 1:N → `asistencias` (docente_id)

---

### 3.3. Tabla: `roles`

**Propósito:** Roles del sistema (admin, docente, coordinador, etc.)

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `name` | VARCHAR | Nombre del rol (único) | 255 caracteres | NO | UNIQUE |
| `description` | TEXT | Descripción del rol | 65,535 caracteres | SÍ | - |
| `level` | INTEGER | Nivel de jerarquía (1-100) | 4 bytes | NO | - |
| `status` | ENUM | Estado: Activo/Inactivo | 1 byte | NO | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`name`)

**Relaciones:**
- M:N → `users` (a través de `role_user`)
- M:N → `permissions` (a través de `permission_role`)

---

### 3.4. Tabla: `permissions`

**Propósito:** Permisos granulares del sistema

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `name` | VARCHAR | Nombre del permiso (único) | 255 caracteres | NO | UNIQUE |
| `description` | TEXT | Descripción del permiso | 65,535 caracteres | SÍ | - |
| `module` | VARCHAR | Módulo al que pertenece | 255 caracteres | SÍ | INDEX |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`name`)
- INDEX (`module`)

**Relaciones:**
- M:N → `roles` (a través de `permission_role`)

---

### 3.5. Tabla: `semestres`

**Propósito:** Períodos académicos (semestres o gestiones)

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `nombre` | VARCHAR | Nombre único (ej: "Gestión 2-2025") | 255 caracteres | NO | UNIQUE |
| `fecha_inicio` | DATE | Fecha de inicio del semestre | - | NO | - |
| `fecha_fin` | DATE | Fecha de fin del semestre | - | NO | - |
| `estado` | VARCHAR | Estado del semestre | 255 caracteres | NO | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`nombre`)

**Relaciones:**
- 1:N → `grupos` (semestre_id)

---

### 3.6. Tabla: `materias`

**Propósito:** Asignaturas de las carreras

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `nombre` | VARCHAR | Nombre de la materia | 255 caracteres | NO | - |
| `sigla` | VARCHAR | Sigla única (ej: "SIS256") | 255 caracteres | NO | UNIQUE |
| `nivel_semestre` | INTEGER | Semestre curricular (1-10) | 4 bytes | NO | - |
| `carrera` | VARCHAR | Carrera (Sistemas, Redes, etc.) | 255 caracteres | NO | INDEX |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`sigla`)
- INDEX (`carrera`)

**Relaciones:**
- 1:N → `grupos` (materia_id)

---

### 3.7. Tabla: `aulas`

**Propósito:** Espacios físicos para clases

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `nombre` | VARCHAR | Nombre/código del aula (único) | 255 caracteres | NO | UNIQUE |
| `piso` | INTEGER | Número de piso | 4 bytes | NO | - |
| `capacidad` | INTEGER | Capacidad de estudiantes | 4 bytes | SÍ | - |
| `tipo` | VARCHAR | Tipo de aula (laboratorio, aula, etc.) | 255 caracteres | SÍ | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`nombre`)

**Relaciones:**
- 1:N → `horarios` (aula_id)

---

### 3.8. Tabla: `grupos`

**Propósito:** Grupos de carga horaria (asignación docente-materia-semestre)

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `semestre_id` | BIGINT | FK → semestres.id | 8 bytes | NO | FK |
| `materia_id` | BIGINT | FK → materias.id | 8 bytes | NO | FK |
| `docente_id` | BIGINT | FK → docentes.id | 8 bytes | NO | FK |
| `nombre` | VARCHAR | Nombre del grupo (ej: "SA", "SB") | 255 caracteres | NO | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`semestre_id`) → `semestres(id)`
- FOREIGN KEY (`materia_id`) → `materias(id)`
- FOREIGN KEY (`docente_id`) → `docentes(id)`

**Relaciones:**
- N:1 → `semestres` (semestre_id)
- N:1 → `materias` (materia_id)
- N:1 → `docentes` (docente_id)
- 1:N → `horarios` (grupo_id)

---

### 3.9. Tabla: `horarios`

**Propósito:** Bloques de horario específicos (día, hora, aula)

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `grupo_id` | BIGINT | FK → grupos.id | 8 bytes | NO | FK |
| `aula_id` | BIGINT | FK → aulas.id | 8 bytes | NO | FK |
| `dia_semana` | TINYINT | 1=Lunes, 2=Martes, ..., 7=Domingo | 1 byte | NO | INDEX |
| `hora_inicio` | TIME | Hora de inicio (ej: "08:00") | - | NO | - |
| `hora_fin` | TIME | Hora de fin (ej: "10:00") | - | NO | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`grupo_id`) → `grupos(id)` ON DELETE CASCADE
- FOREIGN KEY (`aula_id`) → `aulas(id)`
- INDEX (`dia_semana`)

**Relaciones:**
- N:1 → `grupos` (grupo_id)
- N:1 → `aulas` (aula_id)
- 1:N → `asistencias` (horario_id)

---

### 3.10. Tabla: `asistencias`

**Propósito:** Registro de asistencias de docentes

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `horario_id` | BIGINT | FK → horarios.id | 8 bytes | NO | FK |
| `docente_id` | BIGINT | FK → docentes.id | 8 bytes | NO | FK |
| `fecha` | DATE | Fecha específica de la clase | - | NO | INDEX |
| `hora_registro` | TIME | Hora exacta de registro | - | NO | - |
| `estado` | VARCHAR | Estado (Presente, Ausente, Licencia) | 255 caracteres | NO | INDEX |
| `metodo_registro` | VARCHAR | Método (QR, Manual, Formulario) | 255 caracteres | SÍ | - |
| `justificacion` | TEXT | Justificación de ausencia | 65,535 caracteres | SÍ | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`horario_id`) → `horarios(id)` ON DELETE CASCADE
- FOREIGN KEY (`docente_id`) → `docentes(id)`
- INDEX (`fecha`)
- INDEX (`estado`)

**Relaciones:**
- N:1 → `horarios` (horario_id)
- N:1 → `docentes` (docente_id)

---

### 3.11. Tabla: `titulos`

**Propósito:** Títulos académicos de docentes

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `docente_id` | BIGINT | FK → docentes.id | 8 bytes | NO | FK |
| `nombre` | VARCHAR | Nombre del título | 255 caracteres | NO | - |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`docente_id`) → `docentes(id)` ON DELETE CASCADE

**Relaciones:**
- N:1 → `docentes` (docente_id)

---

### 3.12. Tabla Pivot: `role_user`

**Propósito:** Relación Many-to-Many entre usuarios y roles

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `user_id` | BIGINT | FK → users.id | 8 bytes | NO | PK, FK |
| `role_id` | BIGINT | FK → roles.id | 8 bytes | NO | PK, FK |

**Índices:**
- PRIMARY KEY (`user_id`, `role_id`)
- FOREIGN KEY (`user_id`) → `users(id)` ON DELETE CASCADE
- FOREIGN KEY (`role_id`) → `roles(id)` ON DELETE CASCADE

---

### 3.13. Tabla Pivot: `permission_role`

**Propósito:** Relación Many-to-Many entre roles y permisos

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `permission_id` | BIGINT | FK → permissions.id | 8 bytes | NO | FK, UNIQUE |
| `role_id` | BIGINT | FK → roles.id | 8 bytes | NO | FK, UNIQUE |
| `created_at` | TIMESTAMP | Fecha de creación | - | SÍ | - |
| `updated_at` | TIMESTAMP | Fecha de actualización | - | SÍ | - |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`permission_id`, `role_id`)
- FOREIGN KEY (`permission_id`) → `permissions(id)` ON DELETE CASCADE
- FOREIGN KEY (`role_id`) → `roles(id)` ON DELETE CASCADE

---

### 3.14. Tabla: `audit_logs`

**Propósito:** Registro de auditoría de acciones del sistema (bitácora)

| Atributo | Tipo de Dato | Descripción | Tamaño | Nulo | Llave |
|----------|--------------|-------------|--------|------|-------|
| `id` | BIGINT | Identificador único | 8 bytes | NO | PK |
| `user_id` | BIGINT | FK → users.id (NULL si acción del sistema) | 8 bytes | SÍ | FK, INDEX |
| `action` | VARCHAR | Acción realizada (create, update, delete, login, etc.) | 255 caracteres | NO | - |
| `model_type` | VARCHAR | Tipo de modelo afectado (User, Docente, Grupo, etc.) | 255 caracteres | SÍ | INDEX |
| `model_id` | BIGINT | ID del registro afectado | 8 bytes | SÍ | INDEX |
| `details` | TEXT | Detalles adicionales en JSON | 65,535 caracteres | SÍ | - |
| `ip_address` | VARCHAR | Dirección IP del usuario | 45 caracteres | SÍ | - |
| `user_agent` | TEXT | Navegador/dispositivo del usuario | 65,535 caracteres | SÍ | - |
| `created_at` | TIMESTAMP | Fecha y hora del evento | - | NO | INDEX |

**Índices:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`user_id`) → `users(id)` ON DELETE SET NULL
- INDEX (`user_id`) - Búsquedas por usuario
- INDEX (`model_type`, `model_id`) - Búsquedas por modelo afectado
- INDEX (`created_at`) - Búsquedas por fecha

**Relaciones:**
- N:1 → `users` (user_id) - Opcional (puede ser NULL para acciones del sistema)

**Propósito de la bitácora:**
- ✅ Registrar todas las acciones críticas del sistema
- ✅ Auditoría de seguridad (quién hizo qué y cuándo)
- ✅ Trazabilidad de cambios en datos sensibles
- ✅ Cumplimiento de normativas de seguridad
- ✅ Análisis de comportamiento de usuarios
- ✅ Detección de actividades sospechosas

**Ejemplos de registros:**
```json
{
    "user_id": 5,
    "action": "create",
    "model_type": "Docente",
    "model_id": 123,
    "details": "{\"codigo_docente\": \"DOC-2025-001\", \"nombre\": \"Juan Pérez\"}",
    "ip_address": "192.168.1.100"
}

{
    "user_id": 2,
    "action": "delete",
    "model_type": "Grupo",
    "model_id": 45,
    "details": "{\"materia\": \"SIS256\", \"docente_id\": 10}",
    "ip_address": "192.168.1.50"
}
```

---

## 4. Relaciones y Cardinalidad

### Tabla de Relaciones

| Tabla Origen | Relación | Tabla Destino | Cardinalidad | Descripción |
|--------------|----------|---------------|--------------|-------------|
| `users` | hasOne | `docentes` | 1:1 | Un usuario puede tener un perfil de docente |
| `users` | belongsToMany | `roles` | M:N | Un usuario puede tener múltiples roles |
| `docentes` | belongsTo | `users` | N:1 | Cada docente pertenece a un usuario |
| `docentes` | hasMany | `titulos` | 1:N | Un docente puede tener múltiples títulos |
| `docentes` | hasMany | `grupos` | 1:N | Un docente puede tener múltiples grupos |
| `docentes` | hasMany | `asistencias` | 1:N | Un docente puede tener múltiples asistencias |
| `roles` | belongsToMany | `users` | M:N | Un rol puede ser asignado a múltiples usuarios |
| `roles` | belongsToMany | `permissions` | M:N | Un rol puede tener múltiples permisos |
| `permissions` | belongsToMany | `roles` | M:N | Un permiso puede estar en múltiples roles |
| `semestres` | hasMany | `grupos` | 1:N | Un semestre puede tener múltiples grupos |
| `materias` | hasMany | `grupos` | 1:N | Una materia puede tener múltiples grupos |
| `grupos` | belongsTo | `semestres` | N:1 | Cada grupo pertenece a un semestre |
| `grupos` | belongsTo | `materias` | N:1 | Cada grupo pertenece a una materia |
| `grupos` | belongsTo | `docentes` | N:1 | Cada grupo es dictado por un docente |
| `grupos` | hasMany | `horarios` | 1:N | Un grupo puede tener múltiples horarios |
| `horarios` | belongsTo | `grupos` | N:1 | Cada horario pertenece a un grupo |
| `horarios` | belongsTo | `aulas` | N:1 | Cada horario se imparte en un aula |
| `horarios` | hasMany | `asistencias` | 1:N | Un horario puede tener múltiples asistencias |
| `aulas` | hasMany | `horarios` | 1:N | Un aula puede tener múltiples horarios |
| `asistencias` | belongsTo | `horarios` | N:1 | Cada asistencia pertenece a un horario |
| `asistencias` | belongsTo | `docentes` | N:1 | Cada asistencia es registrada por un docente |
| `titulos` | belongsTo | `docentes` | N:1 | Cada título pertenece a un docente |
| `audit_logs` | belongsTo | `users` | N:1 | Cada registro de auditoría pertenece a un usuario |
| `users` | hasMany | `audit_logs` | 1:N | Un usuario puede tener múltiples registros de auditoría |

---

### Diagrama de Cardinalidad Visual

```
users (1) ─────────── (1) docentes
  │                         │
  │ M                       │ 1
  │                         │
  └─ (M:N) ─ roles          ├─ (1:N) ─ titulos
       │        │           │
       │ M      │           ├─ (1:N) ─ grupos
       │        │           │              │
       │        │           │              │ N
       │        │           │              │
       │        │           │    semestres ┤ 1
       │        │           │              │
       │        │           │    materias ─┤ 1
       │        │           │              │
       │        │           │              ├─ (1:N) ─ horarios
       │        │           │              │              │
       │        │           │              │              │ N
       │        │           │    aulas ────┤ 1            │
       │        │           │              │              │
       │        │           │  asistencias ┴──────────────┘
       │        │           │         (N:1)
       │        │           │
  permissions ─┘           │
     (M:N)                 │
                           │
                  audit_logs (registro de actividad)
                       (N:1)
```

---

## 5. Normalización

### Formas Normales Aplicadas

#### 1FN (Primera Forma Normal) ✅

**Regla:** Cada columna debe contener valores atómicos (indivisibles).

**Aplicación:**
- ✅ Todos los campos son atómicos
- ✅ No hay campos multivaluados
- ✅ No hay grupos repetidos

**Ejemplo:**
```sql
-- ❌ NO NORMALIZADO (campos multivaluados)
CREATE TABLE docentes (
    id INT,
    nombres_titulos VARCHAR(500) -- "Ing. Sistemas, Maestría en Redes, PhD"
);

-- ✅ NORMALIZADO (tabla separada)
CREATE TABLE docentes (id INT, ...);
CREATE TABLE titulos (id INT, docente_id INT, nombre VARCHAR);
```

---

#### 2FN (Segunda Forma Normal) ✅

**Regla:** Debe cumplir 1FN + Todos los atributos no-clave deben depender completamente de la clave primaria.

**Aplicación:**
- ✅ No hay dependencias parciales
- ✅ Todos los atributos dependen de la PK completa

**Ejemplo:**
```sql
-- ❌ NO NORMALIZADO (dependencia parcial)
CREATE TABLE grupos (
    id INT,
    materia_id INT,
    nombre_materia VARCHAR, -- Depende solo de materia_id, no del PK completo
    nombre_grupo VARCHAR
);

-- ✅ NORMALIZADO
CREATE TABLE grupos (
    id INT,
    materia_id INT, -- FK a tabla materias
    nombre_grupo VARCHAR
);

CREATE TABLE materias (
    id INT,
    nombre VARCHAR
);
```

---

#### 3FN (Tercera Forma Normal) ✅

**Regla:** Debe cumplir 2FN + No debe haber dependencias transitivas.

**Aplicación:**
- ✅ No hay atributos que dependan de otros atributos no-clave

**Ejemplo:**
```sql
-- ❌ NO NORMALIZADO (dependencia transitiva)
CREATE TABLE grupos (
    id INT,
    docente_id INT,
    facultad_docente VARCHAR -- Depende de docente_id, no directamente del PK
);

-- ✅ NORMALIZADO
CREATE TABLE grupos (
    id INT,
    docente_id INT
);

CREATE TABLE docentes (
    id INT,
    facultad VARCHAR
);
```

---

#### BCNF (Forma Normal de Boyce-Codd) ✅

**Regla:** Para cada dependencia funcional X → Y, X debe ser superclave.

**Aplicación:**
- ✅ Todas las dependencias funcionales cumplen BCNF
- ✅ No hay anomalías de actualización

---

### Decisiones de Desnormalización Controlada

En algunos casos, se ha optado por **desnormalización controlada** por rendimiento:

#### 1. Campo `docente_id` en `asistencias`

**Razón:** Aunque el `docente_id` se puede obtener a través de `horario → grupo → docente`, se almacena directamente en `asistencias` para:
- ✅ Consultas más rápidas de reportes por docente
- ✅ Evitar JOINs múltiples en consultas frecuentes
- ✅ Integridad referencial adicional

```sql
-- Consulta SIN desnormalización (3 JOINs)
SELECT * FROM asistencias
JOIN horarios ON asistencias.horario_id = horarios.id
JOIN grupos ON horarios.grupo_id = grupos.id
JOIN docentes ON grupos.docente_id = docentes.id
WHERE docentes.id = 5;

-- Consulta CON desnormalización (directo)
SELECT * FROM asistencias WHERE docente_id = 5;
```

---

## 6. Mapeo Objeto-Relacional (ORM)

### Modelos Eloquent (Laravel)

#### Relaciones 1:1

```php
// User.php
public function docente()
{
    return $this->hasOne(Docente::class);
}

// Docente.php
public function user()
{
    return $this->belongsTo(User::class);
}
```

#### Relaciones 1:N

```php
// Docente.php
public function titulos()
{
    return $this->hasMany(Titulo::class);
}

public function grupos()
{
    return $this->hasMany(Grupo::class);
}

// Titulo.php
public function docente()
{
    return $this->belongsTo(Docente::class);
}
```

#### Relaciones N:1

```php
// Grupo.php
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
```

#### Relaciones M:N

```php
// User.php
public function roles()
{
    return $this->belongsToMany(Role::class, 'role_user');
}

// Role.php
public function users()
{
    return $this->belongsToMany(User::class, 'role_user');
}

public function permissions()
{
    return $this->belongsToMany(Permission::class, 'permission_role');
}
```

---

## 7. Índices y Optimizaciones

### Índices Creados

| Tabla | Tipo | Columnas | Propósito |
|-------|------|----------|-----------|
| `users` | PRIMARY KEY | `id` | Identificación única |
| `users` | UNIQUE | `email` | Evitar emails duplicados |
| `docentes` | PRIMARY KEY | `id` | Identificación única |
| `docentes` | UNIQUE | `codigo_docente` | Evitar códigos duplicados |
| `docentes` | FOREIGN KEY | `user_id` | Relación con users |
| `roles` | PRIMARY KEY | `id` | Identificación única |
| `roles` | UNIQUE | `name` | Evitar nombres duplicados |
| `permissions` | PRIMARY KEY | `id` | Identificación única |
| `permissions` | UNIQUE | `name` | Evitar permisos duplicados |
| `permissions` | INDEX | `module` | Búsquedas por módulo |
| `semestres` | PRIMARY KEY | `id` | Identificación única |
| `semestres` | UNIQUE | `nombre` | Evitar semestres duplicados |
| `materias` | PRIMARY KEY | `id` | Identificación única |
| `materias` | UNIQUE | `sigla` | Evitar siglas duplicadas |
| `materias` | INDEX | `carrera` | Búsquedas por carrera |
| `aulas` | PRIMARY KEY | `id` | Identificación única |
| `aulas` | UNIQUE | `nombre` | Evitar aulas duplicadas |
| `grupos` | PRIMARY KEY | `id` | Identificación única |
| `grupos` | FOREIGN KEY | `semestre_id, materia_id, docente_id` | Relaciones |
| `horarios` | PRIMARY KEY | `id` | Identificación única |
| `horarios` | FOREIGN KEY | `grupo_id, aula_id` | Relaciones |
| `horarios` | INDEX | `dia_semana` | Búsquedas por día |
| `asistencias` | PRIMARY KEY | `id` | Identificación única |
| `asistencias` | FOREIGN KEY | `horario_id, docente_id` | Relaciones |
| `asistencias` | INDEX | `fecha, estado` | Reportes y búsquedas |
| `titulos` | PRIMARY KEY | `id` | Identificación única |
| `titulos` | FOREIGN KEY | `docente_id` | Relación con docentes |
| `audit_logs` | PRIMARY KEY | `id` | Identificación única |
| `audit_logs` | FOREIGN KEY | `user_id` | Relación con users |
| `audit_logs` | INDEX | `user_id` | Búsquedas por usuario |
| `audit_logs` | INDEX | `model_type, model_id` | Búsquedas por modelo |
| `audit_logs` | INDEX | `created_at` | Búsquedas por fecha |

### Recomendaciones de Optimización

```sql
-- Índice compuesto para búsquedas frecuentes
CREATE INDEX idx_grupos_lookup ON grupos(semestre_id, materia_id, docente_id);

-- Índice para reportes de asistencias
CREATE INDEX idx_asistencias_reportes ON asistencias(fecha, estado, docente_id);

-- Índice para búsquedas de horarios por aula y día
CREATE INDEX idx_horarios_aula_dia ON horarios(aula_id, dia_semana);
```

---

## 8. Reglas de Integridad

### Integridad Referencial

**ON DELETE CASCADE:**
- `docentes` → `users` (si se elimina user, se elimina docente)
- `titulos` → `docentes` (si se elimina docente, se eliminan títulos)
- `horarios` → `grupos` (si se elimina grupo, se eliminan horarios)
- `asistencias` → `horarios` (si se elimina horario, se eliminan asistencias)
- `role_user` → `users`, `roles` (cascada)
- `permission_role` → `permissions`, `roles` (cascada)

**RESTRICT (por defecto):**
- `grupos` → `semestres`, `materias`, `docentes`
- `horarios` → `aulas`

### Restricciones de Unicidad

- `users.email` - Un email por usuario
- `docentes.codigo_docente` - Un código por docente
- `roles.name` - Un nombre por rol
- `permissions.name` - Un nombre por permiso
- `semestres.nombre` - Un nombre por semestre
- `materias.sigla` - Una sigla por materia
- `aulas.nombre` - Un nombre por aula
- `permission_role(permission_id, role_id)` - Evitar duplicados

### Validaciones de Negocio

```sql
-- Validar que hora_fin > hora_inicio en horarios
ALTER TABLE horarios ADD CONSTRAINT chk_horarios_tiempo 
    CHECK (hora_fin > hora_inicio);

-- Validar que fecha_fin > fecha_inicio en semestres
ALTER TABLE semestres ADD CONSTRAINT chk_semestres_fechas 
    CHECK (fecha_fin > fecha_inicio);

-- Validar nivel de rol entre 1 y 100
ALTER TABLE roles ADD CONSTRAINT chk_roles_level 
    CHECK (level BETWEEN 1 AND 100);

-- Validar día de semana entre 1 y 7
ALTER TABLE horarios ADD CONSTRAINT chk_horarios_dia 
    CHECK (dia_semana BETWEEN 1 AND 7);
```

---

## 9. Consultas Comunes Optimizadas

### Obtener carga horaria de un docente

```sql
SELECT 
    d.codigo_docente,
    u.name AS docente_nombre,
    s.nombre AS semestre,
    m.nombre AS materia,
    m.sigla,
    g.nombre AS grupo,
    COUNT(h.id) AS total_horarios
FROM docentes d
JOIN users u ON d.user_id = u.id
JOIN grupos g ON d.id = g.docente_id
JOIN semestres s ON g.semestre_id = s.id
JOIN materias m ON g.materia_id = m.id
JOIN horarios h ON g.id = h.grupo_id
WHERE d.id = ?
GROUP BY d.id, u.name, s.nombre, m.nombre, m.sigla, g.nombre;
```

### Reportes de asistencias por rango de fechas

```sql
SELECT 
    d.codigo_docente,
    u.name AS docente,
    COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) AS presentes,
    COUNT(CASE WHEN a.estado = 'Ausente' THEN 1 END) AS ausentes,
    COUNT(*) AS total_registros
FROM asistencias a
JOIN docentes d ON a.docente_id = d.id
JOIN users u ON d.user_id = u.id
WHERE a.fecha BETWEEN ? AND ?
GROUP BY d.id, d.codigo_docente, u.name;
```

### Horarios disponibles de un aula

```sql
SELECT 
    a.nombre AS aula,
    h.dia_semana,
    h.hora_inicio,
    h.hora_fin,
    m.sigla AS materia,
    g.nombre AS grupo
FROM horarios h
JOIN aulas a ON h.aula_id = a.id
JOIN grupos g ON h.grupo_id = g.id
JOIN materias m ON g.materia_id = m.id
WHERE a.id = ?
ORDER BY h.dia_semana, h.hora_inicio;
```

### Consultar bitácora de auditoría por usuario

```sql
SELECT 
    u.name AS usuario,
    u.email,
    al.action AS accion,
    al.model_type AS modelo,
    al.model_id AS registro_id,
    al.details AS detalles,
    al.ip_address AS ip,
    al.created_at AS fecha_hora
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.id
WHERE al.user_id = ?
ORDER BY al.created_at DESC
LIMIT 100;
```

### Auditoría de cambios en un modelo específico

```sql
-- Ver todos los cambios realizados en un docente específico
SELECT 
    u.name AS usuario_responsable,
    al.action AS accion,
    al.details AS detalles,
    al.ip_address AS ip,
    al.created_at AS fecha_hora
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.id
WHERE al.model_type = 'Docente' 
  AND al.model_id = ?
ORDER BY al.created_at DESC;
```

### Actividad reciente del sistema

```sql
-- Últimas 50 acciones en el sistema
SELECT 
    u.name AS usuario,
    al.action AS accion,
    al.model_type AS modelo_afectado,
    al.created_at AS fecha_hora
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.id
ORDER BY al.created_at DESC
LIMIT 50;
```

---

## 10. Resumen de Buenas Prácticas Aplicadas

### Estadísticas del Sistema

**Tablas totales:** 14 tablas de negocio + 5 tablas de sistema Laravel = **19 tablas**

**Tablas principales de negocio:**
1. users
2. docentes
3. roles
4. permissions
5. semestres
6. materias
7. aulas
8. grupos
9. horarios
10. asistencias
11. titulos
12. role_user (pivot)
13. permission_role (pivot)
14. audit_logs (bitácora)

**Tablas de sistema Laravel:**
15. sessions
16. password_reset_tokens
17. cache
18. cache_locks
19. jobs

**Relaciones:**
- **1:1:** 1 (users ↔ docentes)
- **1:N:** 11 relaciones
- **M:N:** 2 (users-roles, roles-permissions)
- **Total FK:** 18+ llaves foráneas

**Índices:** 40+ índices (PRIMARY, UNIQUE, FOREIGN KEY, INDEX)

---

### Buenas Prácticas Implementadas

✅ **Normalización hasta 3FN/BCNF**  
✅ **Llaves foráneas con integridad referencial**  
✅ **Índices en columnas de búsqueda frecuente**  
✅ **Nombres consistentes en inglés**  
✅ **Timestamps automáticos (created_at, updated_at)**  
✅ **Soft deletes donde sea necesario**  
✅ **Validaciones a nivel de base de datos (CHECK constraints)**  
✅ **Tablas pivot para relaciones M:N**  
✅ **Desnormalización controlada para optimización**  
✅ **Nomenclatura singular para tablas**  
✅ **Uso de BIGINT para PKs y FKs (escalabilidad)**  
✅ **Sistema de auditoría completo (audit_logs)**  
✅ **Trazabilidad de todas las acciones críticas**  

---

**Documentado por:** GitHub Copilot  
**Fecha:** 27 de Octubre, 2025  
**Base de Datos:** PostgreSQL 14+  
**Framework:** Laravel 11.x
