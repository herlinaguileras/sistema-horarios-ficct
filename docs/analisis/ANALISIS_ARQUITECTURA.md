# CAPÍTULO 3: FLUJO DE TRABAJO - ANÁLISIS

## 3.1. Análisis de Arquitectura

### 3.1.1. Identificar Paquetes

| PAQUETE | DESCRIPCIÓN |
|---------|-------------|
| **Gestión de Usuarios y Seguridad** | Gestiona **autenticación, roles y permisos**, asegurando que solo usuarios autorizados accedan a funciones específicas. Incluye auditoría de acciones críticas para seguridad y trazabilidad. |
| **Gestión de Docentes** | Administra la **información de docentes**, incluyendo datos personales, código institucional, títulos académicos, facultad y estado laboral, permitiendo registrar, consultar y actualizar perfiles. |
| **Gestión de Materias** | Controla el **catálogo de materias/asignaturas**, incluyendo nombre, sigla, nivel semestre, carrera y créditos, facilitando la organización académica. |
| **Gestión de Aulas** | Administra los **espacios físicos** (aulas, laboratorios), registrando nombre, piso, capacidad y tipo, para asignación eficiente de horarios. |
| **Gestión de Semestres** | Controla los **períodos académicos** (gestiones/semestres), definiendo fechas de inicio/fin y estado, permitiendo planificación temporal de la carga académica. |
| **Gestión de Grupos** | Gestiona la **asignación de carga horaria**, vinculando docente + materia + semestre, creando grupos específicos (SA, SB, etc.) para distribución académica. |
| **Gestión de Horarios** | Administra la **programación de clases**, especificando día, hora inicio/fin y aula para cada grupo, evitando conflictos de disponibilidad. |
| **Gestión de Asistencias** | Controla el **registro de asistencia docente**, permitiendo marcado manual, por código QR o formulario, con justificación de ausencias y trazabilidad completa. |
| **Reportes y Exportación** | Genera, consulta y exporta **reportes de horarios y asistencias**, con plantillas PDF y Excel, programación automática, apoyando la toma de decisiones estratégicas. |
| **Dashboard y Estadísticas** | Proporciona **vista general del sistema** con métricas clave, gráficos de asistencia, horarios activos, alertas de ausencias y accesos rápidos a funciones principales. |

---

### 3.1.2. Diagrama de Paquetes

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SISTEMA DE HORARIOS Y ASISTENCIAS FICCT                   │
└─────────────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────────────┐
│                       CAPA DE PRESENTACIÓN                                  │
├────────────────────────────────────────────────────────────────────────────┤
│  • Dashboard y Estadísticas                                                │
│  • Vistas Blade (HTML + Tailwind CSS)                                      │
│  • Componentes Interactivos (Alpine.js)                                    │
└────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────────┐
│                       CAPA DE LÓGICA DE NEGOCIO                             │
├───────────────────────┬────────────────────┬──────────────────────────────┤
│  GESTIÓN DE USUARIOS  │  GESTIÓN ACADÉMICA │  GESTIÓN OPERATIVA           │
│  Y SEGURIDAD          │                    │                              │
├───────────────────────┼────────────────────┼──────────────────────────────┤
│  • Autenticación      │  • Docentes        │  • Grupos                    │
│  • Roles              │  • Materias        │  • Horarios                  │
│  • Permisos           │  • Aulas           │  • Asistencias               │
│  • Auditoría          │  • Semestres       │  • Reportes y Exportación    │
└───────────────────────┴────────────────────┴──────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────────┐
│                       CAPA DE ACCESO A DATOS                                │
├────────────────────────────────────────────────────────────────────────────┤
│  • Eloquent ORM (Models)                                                   │
│  • Migraciones de Base de Datos                                            │
│  • Seeders y Factories                                                     │
└────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────────┐
│                       BASE DE DATOS (PostgreSQL)                            │
├────────────────────────────────────────────────────────────────────────────┤
│  14 Tablas principales + 5 Tablas sistema = 19 Tablas Total                │
└────────────────────────────────────────────────────────────────────────────┘
```

---

### 3.1.3. Detalle de Paquetes Funcionales

#### PAQUETE 1: Gestión de Usuarios y Seguridad

**Componentes:**
- `UserController.php` - CRUD de usuarios
- `RoleController.php` - CRUD de roles
- `PermissionController.php` - CRUD de permisos
- `AuditLog.php` (Model) - Registro de auditoría
- Middleware `CheckRole` - Verificación de permisos

**Funcionalidades:**
- ✅ Registro y autenticación de usuarios (Laravel Breeze)
- ✅ Asignación de roles (Admin, Coordinador, Docente, Secretaria)
- ✅ Gestión de permisos granulares por módulo
- ✅ Auditoría automática de acciones críticas
- ✅ Control de acceso basado en roles (RBAC)
- ✅ Activación/desactivación de usuarios

**Tablas relacionadas:**
- `users`, `roles`, `permissions`, `role_user`, `permission_role`, `audit_logs`

---

#### PAQUETE 2: Gestión de Docentes

**Componentes:**
- `DocenteController.php`
- `Docente.php` (Model)
- `Titulo.php` (Model)

**Funcionalidades:**
- ✅ Registro de docentes con código institucional único
- ✅ Gestión de datos personales (CI, teléfono, facultad)
- ✅ Registro de títulos académicos múltiples
- ✅ Control de estado laboral (Activo/Inactivo)
- ✅ Relación 1:1 con usuarios del sistema
- ✅ Fecha de contratación y antigüedad

**Tablas relacionadas:**
- `docentes`, `titulos`, `users`

---

#### PAQUETE 3: Gestión de Materias

**Componentes:**
- `MateriaController.php`
- `Materia.php` (Model)

**Funcionalidades:**
- ✅ Catálogo de materias/asignaturas
- ✅ Siglas únicas por materia (ej: SIS256)
- ✅ Clasificación por nivel semestre (1-10)
- ✅ Clasificación por carrera
- ✅ CRUD completo (crear, listar, editar, eliminar)

**Tablas relacionadas:**
- `materias`

---

#### PAQUETE 4: Gestión de Aulas

**Componentes:**
- `AulaController.php`
- `Aula.php` (Model)

**Funcionalidades:**
- ✅ Registro de espacios físicos (aulas, laboratorios)
- ✅ Especificación de piso y capacidad
- ✅ Clasificación por tipo de aula
- ✅ Disponibilidad para asignación de horarios
- ✅ Código/nombre único por aula

**Tablas relacionadas:**
- `aulas`

---

#### PAQUETE 5: Gestión de Semestres

**Componentes:**
- `SemestreController.php` (implícito en sistema)
- `Semestre.php` (Model)

**Funcionalidades:**
- ✅ Definición de períodos académicos
- ✅ Fechas de inicio y fin
- ✅ Estados: Planificación, Activo, Finalizado
- ✅ Control de gestiones únicas
- ✅ Base temporal para grupos y horarios

**Tablas relacionadas:**
- `semestres`

---

#### PAQUETE 6: Gestión de Grupos

**Componentes:**
- `GrupoController.php`
- `Grupo.php` (Model)

**Funcionalidades:**
- ✅ Asignación de carga horaria docente
- ✅ Vinculación: Docente + Materia + Semestre
- ✅ Creación de grupos paralelos (SA, SB, SC)
- ✅ Gestión de grupos por semestre activo
- ✅ Validación de disponibilidad docente

**Tablas relacionadas:**
- `grupos`, `docentes`, `materias`, `semestres`

---

#### PAQUETE 7: Gestión de Horarios

**Componentes:**
- `HorarioController.php`
- `Horario.php` (Model)

**Funcionalidades:**
- ✅ Programación de bloques de clase
- ✅ Asignación de día de semana (Lunes-Domingo)
- ✅ Definición de hora inicio y fin
- ✅ Asignación de aula específica
- ✅ Detección de conflictos de horario
- ✅ Validación de disponibilidad de aula

**Tablas relacionadas:**
- `horarios`, `grupos`, `aulas`

---

#### PAQUETE 8: Gestión de Asistencias

**Componentes:**
- `AsistenciaController.php`
- `Asistencia.php` (Model)

**Funcionalidades:**
- ✅ Registro de asistencia docente
- ✅ Múltiples métodos de marcado:
  - Manual (admin/coordinador)
  - Código QR (SimpleSoftwareIO QR)
  - Formulario web
- ✅ Estados: Presente, Ausente, Licencia, Permiso
- ✅ Justificación de ausencias
- ✅ Registro de fecha y hora exacta
- ✅ Trazabilidad completa (método de registro)

**Tablas relacionadas:**
- `asistencias`, `horarios`, `docentes`

**Librerías utilizadas:**
- `simplesoftwareio/simple-qrcode` - Generación de códigos QR

---

#### PAQUETE 9: Reportes y Exportación

**Componentes:**
- `DashboardController.php` (métodos de exportación)
- `AsistenciaExport.php` - Export Excel asistencias
- `HorarioSemanalExport.php` - Export Excel horarios

**Funcionalidades:**
- ✅ Generación de reportes PDF:
  - Horarios semanales por docente
  - Asistencias por período
  - Estadísticas generales
- ✅ Exportación a Excel (.xlsx):
  - Listado de asistencias
  - Horarios completos
  - Reportes personalizados
- ✅ Filtros por fecha, docente, materia
- ✅ Plantillas personalizadas con logo institucional
- ✅ Descarga directa de archivos

**Tablas relacionadas:**
- `asistencias`, `horarios`, `grupos`, `docentes`, `materias`

**Librerías utilizadas:**
- `barryvdh/laravel-dompdf` - Generación de PDFs
- `maatwebsite/excel` - Exportación Excel

---

#### PAQUETE 10: Dashboard y Estadísticas

**Componentes:**
- `DashboardController.php`
- Vistas Blade con componentes Livewire/Alpine.js

**Funcionalidades:**
- ✅ Vista general del sistema
- ✅ Métricas clave en tiempo real:
  - Total de docentes activos
  - Grupos activos del semestre
  - Asistencias del día/semana
  - Tasa de asistencia global
- ✅ Gráficos y visualizaciones:
  - Asistencias por día
  - Ranking de docentes
  - Ocupación de aulas
- ✅ Accesos rápidos a funciones principales
- ✅ Alertas de ausencias sin justificar
- ✅ Horarios del día actual

**Tablas relacionadas:**
- Todas las tablas del sistema (vista consolidada)

---

### 3.1.4. Matriz de Dependencias entre Paquetes

| Paquete Origen | Depende de | Tipo de Dependencia |
|----------------|------------|---------------------|
| Gestión de Usuarios y Seguridad | - | Independiente (base) |
| Gestión de Docentes | Gestión de Usuarios | Requiere usuario autenticado |
| Gestión de Materias | Gestión de Usuarios | Solo lectura de permisos |
| Gestión de Aulas | Gestión de Usuarios | Solo lectura de permisos |
| Gestión de Semestres | Gestión de Usuarios | Solo lectura de permisos |
| Gestión de Grupos | Docentes + Materias + Semestres | Requiere los 3 módulos |
| Gestión de Horarios | Grupos + Aulas | Requiere grupo y aula |
| Gestión de Asistencias | Horarios + Docentes | Requiere horario válido |
| Reportes y Exportación | Todos los anteriores | Lectura de todas las tablas |
| Dashboard | Todos los anteriores | Vista consolidada |

---

### 3.1.5. Flujo de Datos entre Paquetes

```
┌──────────────────────┐
│  USUARIOS            │
│  (Autenticación)     │
└──────────┬───────────┘
           │
           ├──────────────────┬──────────────────┬──────────────────┐
           ▼                  ▼                  ▼                  ▼
    ┌──────────┐      ┌──────────┐      ┌──────────┐      ┌──────────┐
    │ DOCENTES │      │ MATERIAS │      │  AULAS   │      │SEMESTRES │
    └─────┬────┘      └─────┬────┘      └─────┬────┘      └─────┬────┘
          │                 │                 │                 │
          └─────────┬───────┴─────────────────┘                 │
                    ▼                                           │
              ┌──────────┐                                      │
              │  GRUPOS  │◄─────────────────────────────────────┘
              └─────┬────┘
                    │
                    ├──────────────────┐
                    ▼                  ▼
              ┌──────────┐       ┌──────────┐
              │ HORARIOS │       │ (Aulas)  │
              └─────┬────┘       └──────────┘
                    │
                    ├──────────────────┐
                    ▼                  ▼
              ┌──────────┐       ┌──────────┐
              │ASISTENCIAS│      │(Docentes)│
              └─────┬────┘       └──────────┘
                    │
                    ▼
              ┌──────────────┐
              │   REPORTES   │
              │ (PDF + Excel)│
              └──────────────┘
                    │
                    ▼
              ┌──────────────┐
              │  DASHBOARD   │
              │(Estadísticas)│
              └──────────────┘
```

---

### 3.1.6. Tecnologías por Paquete

| Paquete | Backend | Frontend | Librerías Especiales |
|---------|---------|----------|---------------------|
| Usuarios y Seguridad | Laravel Auth, Eloquent | Blade, Tailwind | Laravel Breeze |
| Docentes | Eloquent ORM | Blade, Alpine.js | - |
| Materias | Eloquent ORM | Blade, Alpine.js | - |
| Aulas | Eloquent ORM | Blade, Alpine.js | - |
| Semestres | Eloquent ORM | Blade, Alpine.js | - |
| Grupos | Eloquent ORM | Blade, Alpine.js | - |
| Horarios | Eloquent ORM | Blade, Alpine.js, Axios | - |
| Asistencias | Eloquent ORM | Blade, Alpine.js | SimpleSoftwareIO QR |
| Reportes | Laravel Collections | Blade (PDF templates) | DomPDF, Maatwebsite Excel |
| Dashboard | Eloquent ORM | Blade, Alpine.js, Chart.js | - |

---

### 3.1.7. Resumen Cuantitativo

**Total de Paquetes Funcionales:** 10

**Distribución por categoría:**
- 🔐 Seguridad: 1 paquete (10%)
- 📚 Gestión Académica: 4 paquetes (40%)
- 📅 Gestión Operativa: 3 paquetes (30%)
- 📊 Reportes y Análisis: 2 paquetes (20%)

**Controladores totales:** 13
**Modelos Eloquent:** 13
**Tablas de base de datos:** 19

**Cobertura funcional:**
- ✅ 100% de funcionalidades CRUD implementadas
- ✅ Sistema de roles y permisos completo
- ✅ Auditoría automática de acciones
- ✅ Exportación a múltiples formatos (PDF, Excel)
- ✅ Registro de asistencia con múltiples métodos
- ✅ Dashboard con estadísticas en tiempo real

---

**Fecha de análisis:** 27 de Octubre, 2025  
**Versión del sistema:** 1.0  
**Framework:** Laravel 12.x
