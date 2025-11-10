## Análisis por Caso de Uso

Esta carpeta contiene una página por cada Caso de Uso implementado en el sistema. Cada página enlaza al caso de uso (texto), al diagrama de casos de uso, al diagrama de clases implicadas y a los diagramas de secuencia/comunicación.

---

## 📋 Casos de Uso Disponibles

### Módulo de Autenticación y Seguridad

- [`login.md`](login.md) - **UC-001** Login
- [`profile-management.md`](profile-management.md) - **UC-002** Gestión de Perfil
- `manage-users.md` - **UC-011** Gestión de Usuarios *(pendiente de documentar)*
- `manage-roles.md` - **UC-012** Gestión de Roles *(pendiente de documentar)*
- `manage-permissions.md` - **UC-013** Gestión de Permisos *(pendiente de documentar)*

### Módulo de Dashboard

- [`dashboard.md`](dashboard.md) - **UC-003** Dashboard y Estadísticas
- `export-reports.md` - **UC-014** Exportar Reportes (PDF/Excel) *(pendiente de documentar)*

### Módulo de Gestión Académica

- [`manage-docentes.md`](manage-docentes.md) - **UC-004** Gestión de Docentes
- [`manage-materias.md`](manage-materias.md) - **UC-005** Gestión de Materias
- [`manage-aulas.md`](manage-aulas.md) - **UC-006** Gestión de Aulas
- `manage-semestres.md` - **UC-015** Gestión de Semestres *(pendiente de documentar)*

### Módulo de Gestión Operativa

- [`manage-grupos.md`](manage-grupos.md) - **UC-007** Gestión de Grupos
- [`manage-horarios.md`](manage-horarios.md) - **UC-008** Gestión de Horarios
- [`manage-asistencias.md`](manage-asistencias.md) - **UC-009** Gestión de Asistencias
- [`mark-attendance.md`](mark-attendance.md) - **UC-010** Marcar Asistencia (Botón/QR)

---

## 📊 Resumen de Casos de Uso

| ID | Caso de Uso | Módulo | Actor Principal | Estado |
|----|-------------|--------|----------------|--------|
| UC-001 | Login | Autenticación | Usuario | ✅ Documentado |
| UC-002 | Gestión de Perfil | Autenticación | Usuario Autenticado | ✅ Documentado |
| UC-003 | Dashboard y Estadísticas | Dashboard | Admin/Coordinador | ✅ Documentado |
| UC-004 | Gestión de Docentes | Académica | Admin | ✅ Documentado |
| UC-005 | Gestión de Materias | Académica | Admin | ✅ Documentado |
| UC-006 | Gestión de Aulas | Académica | Admin | ✅ Documentado |
| UC-007 | Gestión de Grupos | Operativa | Admin/Coordinador | ✅ Documentado |
| UC-008 | Gestión de Horarios | Operativa | Admin/Coordinador | ✅ Documentado |
| UC-009 | Gestión de Asistencias | Operativa | Admin/Coordinador | ✅ Documentado |
| UC-010 | Marcar Asistencia | Operativa | Docente | ✅ Documentado |
| UC-011 | Gestión de Usuarios | Seguridad | Admin | ⚠️ Implementado, sin documentar |
| UC-012 | Gestión de Roles | Seguridad | Admin | ⚠️ Implementado, sin documentar |
| UC-013 | Gestión de Permisos | Seguridad | Admin | ⚠️ Implementado, sin documentar |
| UC-014 | Exportar Reportes | Dashboard | Admin/Coordinador | ⚠️ Implementado, sin documentar |
| UC-015 | Gestión de Semestres | Académica | Admin | ⚠️ Implementado, sin documentar |

**Total:** 15 casos de uso  
**Documentados:** 10 (67%)  
**Pendientes de documentar:** 5 (33%)

---

## 📁 Estructura de Archivos

Cada archivo de caso de uso contiene:

- **Descripción general** del caso de uso
- **Actores** involucrados
- **Precondiciones** y **postcondiciones**
- **Flujo principal** de eventos
- **Flujos alternativos** (si aplica)
- **Referencias** a requerimientos funcionales
- **Diagramas relacionados:**
  - Diagrama de casos de uso (`docs/diagrams/usecases.puml`)
  - Diagrama de clases (`docs/diagrams/classes/*.puml`)
  - Diagramas de secuencia (`docs/diagrams/sequence/*.puml`)
  - Diagramas de comunicación (`docs/diagrams/comm/*.puml`)

---

## 🔗 Archivos de Diagramas Principales

- [`class-diagram.puml`](class-diagram.puml) - Diagrama de clases general del sistema
- [`package-diagram.puml`](package-diagram.puml) - Diagrama de paquetes/módulos
- [`ANALISIS_ARQUITECTURA.md`](ANALISIS_ARQUITECTURA.md) - Análisis detallado de arquitectura

---

## 🎯 Casos de Uso por Actor

### Administrator
- UC-001, UC-002, UC-003, UC-004, UC-005, UC-006, UC-007, UC-008, UC-009, UC-011, UC-012, UC-013, UC-014, UC-015

### Coordinador
- UC-001, UC-002, UC-003, UC-007, UC-008, UC-009, UC-014

### Docente
- UC-001, UC-002, UC-010

### Secretaria
- UC-001, UC-002, UC-003 (lectura), UC-009 (lectura)

---

## 📝 Notas

- Los casos de uso marcados como **"pendientes de documentar"** están **implementados y funcionando** en el sistema, pero aún no tienen su archivo de análisis individual en esta carpeta.
- Para agregar documentación de un nuevo caso de uso, crear un archivo `.md` siguiendo la estructura de los existentes.
- Actualizar esta página cada vez que se añada o modifique un caso de uso.

---

**Última actualización:** 27 de Octubre, 2025  
**Sistema:** Gestión de Horarios y Asistencias FICCT  
**Versión:** 1.0
