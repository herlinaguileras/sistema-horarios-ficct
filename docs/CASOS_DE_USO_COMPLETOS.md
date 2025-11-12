# 📋 CASOS DE USO COMPLETOS - Sistema de Horarios FICCT

**Fecha**: 11 de Noviembre de 2025  
**Versión**: 1.0  
**Proyecto**: Sistema de Gestión de Horarios y Asistencias FICCT

---

## 📊 RESUMEN EJECUTIVO

**Total de Casos de Uso Identificados**: **87 casos de uso**

**Distribución por Tipo de Usuario**:
- 👤 **Administrador (Admin)**: 57 casos de uso
- 👨‍🏫 **Docente**: 18 casos de uso
- 👥 **Roles Personalizados**: 12 casos de uso (variable según configuración)

**Módulos del Sistema**: 11 módulos principales

---

## 🔐 AUTENTICACIÓN Y PERFIL (TODOS LOS USUARIOS)

### CU-001: Sistema de Autenticación
**Actor**: Usuario no autenticado  
**Casos de uso**:

1. **Iniciar Sesión**
   - Campo: Email
   - Campo: Contraseña
   - Botón: "Iniciar Sesión"
   - Link: "¿Olvidaste tu contraseña?"

2. **Registrar Nuevo Usuario** (si está habilitado)
   - Campo: Nombre
   - Campo: Email
   - Campo: Contraseña
   - Campo: Confirmar Contraseña
   - Botón: "Registrar"

3. **Recuperar Contraseña**
   - Campo: Email
   - Botón: "Enviar enlace de restablecimiento"

4. **Restablecer Contraseña**
   - Campo: Nueva Contraseña
   - Campo: Confirmar Contraseña
   - Botón: "Restablecer Contraseña"

5. **Verificar Email**
   - Botón: "Reenviar Email de Verificación"

**Total CU-001**: 5 casos de uso

---

### CU-002: Gestión de Perfil
**Actor**: Usuario autenticado  
**Casos de uso**:

1. **Ver Perfil**
   - Visualizar información personal
   - Ver rol asignado
   - Ver módulos disponibles

2. **Editar Información de Perfil**
   - Campo: Nombre
   - Campo: Email
   - Botón: "Actualizar Información"

3. **Cambiar Contraseña**
   - Campo: Contraseña Actual
   - Campo: Nueva Contraseña
   - Campo: Confirmar Nueva Contraseña
   - Botón: "Actualizar Contraseña"

4. **Eliminar Cuenta**
   - Campo: Confirmación de Contraseña
   - Botón: "Eliminar Cuenta"

5. **Cerrar Sesión**
   - Botón: "Cerrar Sesión"

**Total CU-002**: 5 casos de uso

---

## 👤 MÓDULO: ADMINISTRADOR

### CU-003: Gestión de Usuarios (module:usuarios)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Usuarios**
   - Ver tabla de usuarios paginada
   - Campo: Búsqueda por nombre/email
   - Ver rol de cada usuario
   - Ver estado (Activo/Inactivo)

2. **Crear Nuevo Usuario**
   - Botón: "Crear Usuario"
   - Campo: Nombre
   - Campo: Email
   - Campo: Contraseña
   - Select: Rol
   - Checkbox: Vincular con Docente
   - Select: Docente (si checkbox marcado)
   - Botón: "Guardar Usuario"

3. **Editar Usuario**
   - Botón: "Editar" (en cada fila)
   - Campo: Nombre
   - Campo: Email
   - Select: Rol
   - Checkbox: Cambiar Contraseña
   - Campo: Nueva Contraseña (si checkbox marcado)
   - Botón: "Actualizar Usuario"

4. **Activar/Desactivar Usuario**
   - Toggle: Estado (Activo/Inactivo)
   - Confirmación automática

5. **Eliminar Usuario**
   - Botón: "Eliminar" (en cada fila)
   - Confirmación: "¿Estás seguro?"
   - Mensaje de éxito/error

**Total CU-003**: 5 casos de uso

---

### CU-004: Gestión de Roles (module:roles)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Roles**
   - Ver tabla de roles
   - Ver nombre, descripción, nivel
   - Ver estado (Activo/Inactivo)
   - Ver cantidad de módulos asignados

2. **Crear Nuevo Rol**
   - Botón: "Crear Rol"
   - Campo: Nombre del Rol
   - Campo: Descripción
   - Campo: Nivel (1-100)
   - Select: Estado
   - Checkbox: "Seleccionar Todo" por módulo
   - Checkboxes: Módulos individuales (11 módulos disponibles)
   - Botón: "Crear Rol"

3. **Editar Rol**
   - Botón: "Editar" (en cada fila)
   - Campo: Nombre del Rol
   - Campo: Descripción
   - Campo: Nivel
   - Select: Estado
   - Checkboxes: Módulos (modificar permisos)
   - Botón: "Actualizar Rol"

4. **Activar/Desactivar Rol**
   - Toggle: Estado
   - Confirmación automática

5. **Eliminar Rol**
   - Botón: "Eliminar" (en cada fila)
   - Validación: No puede eliminar si tiene usuarios asignados
   - Confirmación: "¿Estás seguro?"

**Total CU-004**: 5 casos de uso

---

### CU-005: Gestión de Docentes (module:docentes)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Docentes**
   - Ver tabla de docentes paginada
   - Ver código, nombre, email
   - Ver usuario vinculado
   - Ver cantidad de grupos asignados
   - Campo: Búsqueda por código/nombre

2. **Crear Nuevo Docente**
   - Botón: "Crear Docente"
   - Campo: Código de Docente (auto-generado o manual)
   - Select: Usuario (vinculación)
   - Botón: Crear Usuario Nuevo (modal)
   - Botón: "Guardar Docente"

3. **Editar Docente**
   - Botón: "Editar" (en cada fila)
   - Campo: Código de Docente
   - Select: Usuario
   - Botón: "Actualizar Docente"

4. **Ver Grupos del Docente**
   - Ver lista de grupos asignados
   - Ver materias que imparte

5. **Eliminar Docente**
   - Botón: "Eliminar" (en cada fila)
   - Validación: No puede eliminar si tiene grupos asignados
   - Mensaje: "El docente tiene X grupos asignados"
   - Confirmación: "¿Estás seguro?"
   - Opciones: Reasignar grupos o eliminar grupos primero

**Total CU-005**: 5 casos de uso

---

### CU-006: Gestión de Materias (module:materias)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Materias**
   - Ver tabla de materias paginada
   - Ver sigla, nombre, nivel
   - Ver carreras asociadas
   - Campo: Búsqueda por sigla/nombre
   - Filtro: Por nivel de semestre

2. **Crear Nueva Materia**
   - Botón: "Crear Materia"
   - Campo: Sigla
   - Campo: Nombre
   - Select: Nivel de Semestre (1-10)
   - Checkboxes: Carreras (múltiple selección)
   - Botón: "Guardar Materia"

3. **Editar Materia**
   - Botón: "Editar" (en cada fila)
   - Campo: Sigla
   - Campo: Nombre
   - Select: Nivel de Semestre
   - Checkboxes: Carreras
   - Botón: "Actualizar Materia"

4. **Ver Grupos de la Materia**
   - Ver grupos que usan esta materia
   - Ver docentes asignados

5. **Eliminar Materia**
   - Botón: "Eliminar" (en cada fila)
   - Validación: No puede eliminar si tiene grupos
   - Confirmación: "¿Estás seguro?"

**Total CU-006**: 5 casos de uso

---

### CU-007: Gestión de Aulas (module:aulas)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Aulas**
   - Ver tabla de aulas
   - Ver nombre, capacidad, piso
   - Ver tipo de aula
   - Ver cantidad de horarios asignados

2. **Crear Nueva Aula**
   - Botón: "Crear Aula"
   - Campo: Nombre/Número
   - Campo: Capacidad
   - Campo: Piso
   - Select: Tipo (Teórica, Laboratorio, Taller)
   - Botón: "Guardar Aula"

3. **Editar Aula**
   - Botón: "Editar" (en cada fila)
   - Campo: Nombre
   - Campo: Capacidad
   - Campo: Piso
   - Select: Tipo
   - Botón: "Actualizar Aula"

4. **Ver Horarios del Aula**
   - Ver calendario de ocupación
   - Ver grupos que usan el aula

5. **Eliminar Aula**
   - Botón: "Eliminar" (en cada fila)
   - Validación: No puede eliminar si tiene horarios
   - Confirmación: "¿Estás seguro?"

**Total CU-007**: 5 casos de uso

---

### CU-008: Gestión de Grupos (module:grupos)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Grupos**
   - Ver tabla de grupos paginada
   - Ver nombre, materia, docente
   - Ver semestre asignado
   - Ver cantidad de horarios
   - Filtros: Por semestre, materia, docente

2. **Crear Nuevo Grupo**
   - Botón: "Crear Grupo"
   - Campo: Nombre del Grupo
   - Select: Materia
   - Select: Docente
   - Select: Semestre
   - Botón: "Guardar Grupo"

3. **Editar Grupo**
   - Botón: "Editar" (en cada fila)
   - Campo: Nombre
   - Select: Materia
   - Select: Docente
   - Select: Semestre
   - Botón: "Actualizar Grupo"

4. **Ver Horarios del Grupo**
   - Ver todos los horarios del grupo
   - Ver días y horas
   - Ver aulas asignadas

5. **Eliminar Grupo**
   - Botón: "Eliminar" (en cada fila)
   - Confirmación: "¿Estás seguro?"
   - Elimina automáticamente los horarios asociados

**Total CU-008**: 5 casos de uso

---

### CU-009: Gestión de Semestres (module:semestres)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Semestres**
   - Ver tabla de semestres
   - Ver nombre, fechas, estado
   - Ver cantidad de grupos
   - Indicador: Semestre activo (verde)

2. **Crear Nuevo Semestre**
   - Botón: "Crear Semestre"
   - Campo: Nombre
   - Campo: Fecha de Inicio
   - Campo: Fecha de Fin
   - Select: Estado (Planificación, Activo, Terminado)
   - Botón: "Guardar Semestre"

3. **Editar Semestre**
   - Botón: "Editar" (en cada fila)
   - Campo: Nombre
   - Campo: Fecha de Inicio
   - Campo: Fecha de Fin
   - Select: Estado
   - Botón: "Actualizar Semestre"

4. **Activar/Desactivar Semestre**
   - Botón: "Activar" (cambia estado a Activo)
   - Validación: Solo puede haber 1 semestre activo
   - Desactiva automáticamente el anterior

5. **Eliminar Semestre**
   - Botón: "Eliminar" (en cada fila)
   - Estados del botón:
     - Deshabilitado (gris): Si es activo o tiene grupos
     - Habilitado (rojo): Si puede eliminarse
   - Validación: No puede ser activo
   - Validación: No puede tener grupos asignados
   - Tooltip: Muestra razón si está deshabilitado
   - Confirmación: "¿Estás seguro?"

**Total CU-009**: 5 casos de uso

---

### CU-010: Gestión de Horarios (module:horarios)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Horarios**
   - Ver tabla de horarios
   - Ver grupo, materia, docente
   - Ver día, hora, aula
   - Filtros: Por semestre, docente, aula
   - Ver horarios en formato de calendario

2. **Crear Nuevo Horario (Manual)**
   - Botón: "Crear Horario"
   - Select: Grupo
   - Select: Día de la Semana
   - Campo: Hora de Inicio
   - Campo: Hora de Fin
   - Select: Aula
   - Validaciones en tiempo real:
     - Choque de docente
     - Choque de aula
     - Choque de grupo
   - Botón: "Guardar Horario"

3. **Importar Horarios Masivamente**
   - Botón: "Importar Horarios"
   - Botón: "Descargar Plantilla Excel"
   - Campo: Seleccionar archivo (.xlsx, .xls, .csv)
   - Botón: "Importar"
   - Ver progreso de importación
   - Ver reporte de resultados:
     - Horarios creados exitosamente
     - Errores encontrados
     - Docentes/Materias/Aulas creados automáticamente
   - Validaciones:
     - Formato de archivo
     - Choques de horarios
     - Datos requeridos

4. **Editar Horario**
   - Botón: "Editar" (en cada fila)
   - Select: Grupo
   - Select: Día
   - Campo: Hora Inicio
   - Campo: Hora Fin
   - Select: Aula
   - Validaciones de choques
   - Botón: "Actualizar Horario"

5. **Eliminar Horario**
   - Botón: "Eliminar" (en cada fila)
   - Confirmación: "¿Estás seguro?"

**Total CU-010**: 5 casos de uso

---

### CU-011: Gestión de Estadísticas (module:estadisticas)
**Actor**: Administrador  
**Casos de uso**:

1. **Listar Docentes para Estadísticas**
   - Ver lista de todos los docentes
   - Ver código, nombre
   - Ver cantidad de grupos
   - Ver total de asistencias registradas
   - Botón: "Ver Estadísticas" (por cada docente)

2. **Ver Estadísticas Detalladas de Docente**
   - Resumen General:
     - Total de grupos
     - Asistencias registradas
     - Total de horarios
     - Promedio de asistencia
   - Gráfico: Asistencias por mes (últimos 6 meses)
   - Historial completo por materia/grupo:
     - Tabla de todas las asistencias
     - Filtros por fecha
     - Ver método de registro (QR, Manual)
     - Ver cantidad de estudiantes
   - Ver materias impartidas

3. **Exportar Estadísticas** (futuro)
   - Botón: "Exportar a PDF"
   - Botón: "Exportar a Excel"

**Total CU-011**: 3 casos de uso (2 activos + 1 futuro)

---

## 👨‍🏫 MÓDULO: DOCENTE

### CU-012: Dashboard de Docente
**Actor**: Docente  
**Casos de uso**:

1. **Ver Horario Semanal Personal**
   - Visualizar calendario semanal
   - Ver materias asignadas
   - Ver aulas y horarios
   - Ver grupos

2. **Ver Grupos Asignados**
   - Lista de grupos donde es docente
   - Ver materia de cada grupo
   - Ver semestre activo

3. **Ver Próximas Clases**
   - Calendario de clases próximas
   - Indicador de hora actual
   - Botón: "Marcar Asistencia" (si clase próxima)

4. **Exportar Horario Personal**
   - Botón: "Exportar Horario a Excel"
   - Botón: "Exportar Horario a PDF"

**Total CU-012**: 4 casos de uso

---

### CU-013: Marcar Asistencia (Docente)
**Actor**: Docente  
**Casos de uso**:

1. **Ver Clases del Día**
   - Ver horarios del día actual
   - Indicador: Clase en curso (verde)
   - Indicador: Clase próxima (amarillo)
   - Indicador: Clase pasada (gris)

2. **Marcar Asistencia Manualmente**
   - Botón: "Marcar Asistencia" (en cada clase)
   - Validación: Solo dentro de ventana de tiempo (±15 min)
   - Formulario:
     - Campo: Cantidad de Estudiantes
     - Confirmación automática
   - Mensaje de éxito

3. **Generar Código QR para Asistencia**
   - Botón: "Generar QR"
   - Modal con código QR
   - Información: Válido por 1 hora
   - Botón: "Copiar Enlace"
   - Botón: "Descargar QR"
   - Estudiantes escanean con móvil

4. **Ver Historial de Asistencias Marcadas**
   - Ver lista de asistencias registradas
   - Ver fecha, hora, cantidad de estudiantes
   - Ver método (Manual, QR)

**Total CU-013**: 4 casos de uso

---

### CU-014: Ver Estadísticas Personales (Docente)
**Actor**: Docente  
**Casos de uso**:

1. **Acceder a Mis Estadísticas**
   - Menú: "Mis Estadísticas"
   - Redirección automática a sus propias estadísticas
   - Restricción: No puede ver estadísticas de otros

2. **Ver Resumen Personal**
   - Total de grupos asignados
   - Asistencias registradas
   - Total de horarios
   - Promedio de asistencia

3. **Ver Gráfico de Asistencias**
   - Gráfico de barras: Asistencias por mes
   - Últimos 6 meses

4. **Ver Historial por Materia**
   - Tabla de asistencias por grupo
   - Filtros por fecha
   - Ver método de registro
   - Ver cantidad de estudiantes

**Total CU-014**: 4 casos de uso

---

### CU-015: QR de Asistencia (Estudiantes vía móvil)
**Actor**: Estudiante (no autenticado)  
**Casos de uso**:

1. **Escanear Código QR**
   - Escanear QR con móvil
   - Redirección a URL del sistema

2. **Marcar Asistencia vía QR**
   - Validaciones automáticas:
     - QR no expirado (< 1 hora)
     - Dentro de ventana de tiempo (±15 min)
     - QR válido y no manipulado
   - Página de éxito
   - Mensaje: "Asistencia registrada"

3. **Ver Errores de QR**
   - Página: QR Expirado
   - Página: QR Inválido
   - Página: Fuera de Horario
   - Página: No Autorizado
   - Cada página con instrucciones

**Total CU-015**: 3 casos de uso

---

### CU-016: Exportaciones (Docente)
**Actor**: Docente  
**Casos de uso**:

1. **Exportar Horario Semanal a Excel**
   - Botón: "Exportar a Excel"
   - Descarga automática

2. **Exportar Horario Semanal a PDF**
   - Botón: "Exportar a PDF"
   - Descarga automática

3. **Exportar Asistencias a Excel** (futuro)
   - Botón: "Exportar Asistencias"

**Total CU-016**: 3 casos de uso (2 activos + 1 futuro)

---

## 👥 MÓDULO: ROLES PERSONALIZADOS

### CU-017: Dashboard Dinámico por Rol
**Actor**: Usuario con rol personalizado  
**Casos de uso**:

1. **Ver Dashboard según Módulos Asignados**
   - El dashboard muestra solo los módulos habilitados
   - Widgets dinámicos según permisos

2. **Acceder a Módulos Permitidos**
   - Menú de navegación dinámico
   - Solo muestra módulos habilitados para el rol

3. **Restricción de Acceso**
   - Middleware verifica módulos
   - Redirección si intenta acceder a módulo no permitido
   - Mensaje: "No tienes acceso a este módulo"

**Total CU-017**: 3 casos de uso

---

## 📊 DASHBOARD GENERAL

### CU-018: Dashboard Principal
**Actor**: Todos los usuarios autenticados  
**Casos de uso**:

1. **Dashboard Admin**
   - Widgets de estadísticas generales
   - Total de docentes, materias, aulas
   - Gráficos de asistencias
   - Horarios del semestre activo
   - Acceso rápido a módulos

2. **Dashboard Docente**
   - Horario semanal personal
   - Próximas clases
   - Acceso rápido a marcar asistencia
   - Estadísticas personales
   - Exportar horario

3. **Dashboard Rol Personalizado**
   - Widgets según módulos habilitados
   - Navegación dinámica
   - Información relevante al rol

4. **Exportar Datos del Dashboard**
   - Botón: "Exportar Horario Semanal (Excel)"
   - Botón: "Exportar Horario Semanal (PDF)"
   - Botón: "Exportar Asistencias (Excel)"
   - Botón: "Exportar Asistencias (PDF)"

**Total CU-018**: 4 casos de uso

---

## 🔍 BÚSQUEDAS Y FILTROS

### CU-019: Sistema de Búsquedas
**Actor**: Administrador  
**Casos de uso incluidos en cada módulo**:

1. **Búsqueda de Usuarios** - Por nombre/email
2. **Búsqueda de Docentes** - Por código/nombre
3. **Búsqueda de Materias** - Por sigla/nombre
4. **Filtro de Horarios** - Por semestre/docente/aula
5. **Filtro de Grupos** - Por semestre/materia/docente

**Total CU-019**: 5 casos de uso

---

## 📈 REPORTES Y EXPORTACIONES

### CU-020: Sistema de Reportes
**Actor**: Administrador, Docente  
**Casos de uso**:

1. **Reporte de Horario Semanal (Excel)**
2. **Reporte de Horario Semanal (PDF)**
3. **Reporte de Asistencias (Excel)**
4. **Reporte de Asistencias (PDF)**
5. **Estadísticas de Docente (Futuro: PDF/Excel)**

**Total CU-020**: 5 casos de uso (4 activos + 1 futuro)

---

## 📋 RESUMEN TOTAL POR CATEGORÍA

### Por Módulo:

| Módulo | Casos de Uso |
|--------|--------------|
| Autenticación | 5 |
| Perfil de Usuario | 5 |
| Gestión de Usuarios | 5 |
| Gestión de Roles | 5 |
| Gestión de Docentes | 5 |
| Gestión de Materias | 5 |
| Gestión de Aulas | 5 |
| Gestión de Grupos | 5 |
| Gestión de Semestres | 5 |
| Gestión de Horarios | 5 |
| Gestión de Estadísticas | 3 |
| Dashboard Docente | 4 |
| Marcar Asistencia | 4 |
| Estadísticas Personales | 4 |
| QR de Asistencia | 3 |
| Exportaciones Docente | 3 |
| Roles Personalizados | 3 |
| Dashboard General | 4 |
| Búsquedas y Filtros | 5 |
| Reportes | 5 |

---

### Por Tipo de Usuario:

| Usuario | Casos de Uso Totales |
|---------|---------------------|
| 👤 **Administrador** | **57 casos de uso** |
| - Autenticación | 5 |
| - Perfil | 5 |
| - Usuarios | 5 |
| - Roles | 5 |
| - Docentes | 5 |
| - Materias | 5 |
| - Aulas | 5 |
| - Grupos | 5 |
| - Semestres | 5 |
| - Horarios | 5 |
| - Estadísticas | 3 |
| - Dashboard | 4 |
| - Búsquedas | 5 |
| - Reportes | 5 |
| | |
| 👨‍🏫 **Docente** | **18 casos de uso** |
| - Autenticación | 5 |
| - Perfil | 5 |
| - Dashboard Personal | 4 |
| - Marcar Asistencia | 4 |
| - Ver Estadísticas | 4 |
| - Exportaciones | 3 |
| - Acceso a QR (indirecto) | 3 |
| | |
| 👥 **Rol Personalizado** | **12+ casos de uso** |
| - Autenticación | 5 |
| - Perfil | 5 |
| - Dashboard Dinámico | 3 |
| - Módulos según configuración | Variable |
| | |
| 📱 **Estudiante (No Auth)** | **3 casos de uso** |
| - Escanear QR | 3 |

---

## 🎯 TOTAL GLOBAL

### **TOTAL DE CASOS DE USO DEL SISTEMA: 87 CASOS**

Desglose:
- **Casos de uso activos**: 84
- **Casos de uso futuros/planeados**: 3
- **Validaciones automáticas**: 15+
- **Acciones CRUD completas**: 10 módulos × 5 operaciones = 50
- **Exportaciones**: 5 tipos
- **Dashboard dinámicos**: 3 tipos
- **Sistema de QR**: 6 casos relacionados

---

## 📊 MATRIZ DE ACCESO POR ROL

| Módulo/Función | Admin | Docente | Personalizado |
|----------------|-------|---------|---------------|
| Dashboard | ✅ | ✅ | ✅ (dinámico) |
| Usuarios | ✅ | ❌ | ⚙️ (si módulo) |
| Roles | ✅ | ❌ | ⚙️ (si módulo) |
| Docentes | ✅ | ❌ | ⚙️ (si módulo) |
| Materias | ✅ | ❌ | ⚙️ (si módulo) |
| Aulas | ✅ | ❌ | ⚙️ (si módulo) |
| Grupos | ✅ | ❌ | ⚙️ (si módulo) |
| Semestres | ✅ | ❌ | ⚙️ (si módulo) |
| Horarios | ✅ | ❌ | ⚙️ (si módulo) |
| Importación | ✅ | ❌ | ⚙️ (si módulo) |
| Estadísticas Global | ✅ | ❌ | ⚙️ (si módulo) |
| Marcar Asistencia | ❌ | ✅ | ❌ |
| Generar QR | ❌ | ✅ | ❌ |
| Estadísticas Propias | ❌ | ✅ | ❌ |
| Exportar Horario | ✅ | ✅ | ⚙️ (si módulo) |
| Perfil | ✅ | ✅ | ✅ |

**Leyenda**:
- ✅ = Acceso completo
- ❌ = Sin acceso
- ⚙️ = Según configuración de módulos del rol

---

## 🔄 FLUJOS DE TRABAJO PRINCIPALES

### Flujo 1: Configuración Inicial del Sistema (Admin)
1. Crear Roles → Asignar Módulos
2. Crear Usuarios → Asignar Roles
3. Crear Docentes → Vincular con Usuarios
4. Crear Materias
5. Crear Aulas
6. Crear Semestre → Activar
7. Crear Grupos → Asignar Materia, Docente, Semestre
8. Crear Horarios (Manual o Importación Masiva)

**Total de pasos**: 8 pasos principales

---

### Flujo 2: Uso Diario del Docente
1. Iniciar Sesión
2. Ver Dashboard Personal
3. Ver Próximas Clases
4. Marcar Asistencia (Manual o QR)
5. Ver Estadísticas Personales
6. Exportar Horario (opcional)
7. Cerrar Sesión

**Total de pasos**: 7 pasos principales

---

### Flujo 3: Importación Masiva de Horarios (Admin)
1. Descargar Plantilla Excel
2. Completar Datos en Excel
3. Ir a Módulo de Horarios
4. Click en "Importar"
5. Seleccionar Archivo
6. Ver Progreso y Validaciones
7. Revisar Reporte de Importación
8. Verificar Horarios Creados

**Total de pasos**: 8 pasos principales

---

## 🛡️ VALIDACIONES Y RESTRICCIONES

### Validaciones Implementadas:

1. **Validación de Roles**: Middleware verifica rol
2. **Validación de Módulos**: Middleware verifica acceso a módulo
3. **Validación de Eliminación de Docentes**: Verifica grupos asignados
4. **Validación de Eliminación de Semestres**: Verifica estado activo y grupos
5. **Validación de Eliminación de Materias**: Verifica grupos
6. **Validación de Eliminación de Aulas**: Verifica horarios
7. **Validación de Choques de Horarios**: Al crear/editar horarios
8. **Validación de QR**: Tiempo, expiración, autenticidad
9. **Validación de Ventana de Asistencia**: ±15 minutos
10. **Validación de Semestre Activo**: Solo uno activo a la vez
11. **Validación de Importación**: Formato, datos requeridos, choques
12. **Validación de Estados de Asistencia**: Solo valores permitidos
13. **Validación de APP_URL**: Rutas correctas
14. **Validación de Foreign Keys**: Prevención de errores de BD
15. **Validación de Datos Únicos**: Email, código docente, etc.

**Total de Validaciones**: 15+ validaciones críticas

---

## 📱 INTERFACES DE USUARIO

### Tipos de Interfaces:

1. **Tablas Paginadas**: 10 módulos
2. **Formularios de Creación**: 10 módulos
3. **Formularios de Edición**: 10 módulos
4. **Modales de Confirmación**: 15+ acciones
5. **Dashboards Dinámicos**: 3 tipos
6. **Calendarios/Horarios**: 3 vistas
7. **Gráficos Estadísticos**: 2 tipos
8. **Páginas de Error**: 4 tipos (QR)
9. **Exportaciones**: 4 formatos
10. **Búsquedas y Filtros**: 5 módulos

---

## 🎨 EXPERIENCIA DE USUARIO

### Elementos de UX Implementados:

1. **Feedback Visual**:
   - Mensajes de éxito (verde)
   - Mensajes de error (rojo)
   - Mensajes informativos (azul)
   - Mensajes de advertencia (amarillo)

2. **Indicadores de Estado**:
   - Botones deshabilitados con tooltips
   - Estados de semestre (Activo/Planificación/Terminado)
   - Estados de usuario (Activo/Inactivo)
   - Estados de clase (Próxima/En curso/Pasada)

3. **Confirmaciones**:
   - Confirmación antes de eliminar
   - Confirmación de cambios críticos
   - Validaciones en tiempo real

4. **Ayuda Contextual**:
   - Tooltips en botones
   - Secciones de ayuda en formularios
   - Mensajes de error descriptivos

5. **Navegación Intuitiva**:
   - Menú dinámico según rol
   - Breadcrumbs (futuro)
   - Links de retorno

---

## 📝 NOTAS FINALES

### Funcionalidades Destacadas:

1. **Sistema de Módulos Dinámico**: Asignación flexible de permisos
2. **Importación Masiva**: Ahorra tiempo en configuración inicial
3. **QR de Asistencia**: Moderna y sin contacto
4. **Validaciones Inteligentes**: Previene errores de datos
5. **Dashboard Personalizado**: Cada rol ve lo que necesita
6. **Estadísticas Completas**: Para administración y docentes
7. **Exportaciones**: Datos disponibles en múltiples formatos
8. **Restricciones de Seguridad**: Protección de datos sensibles

### Próximas Mejoras Sugeridas:

1. **Soft Deletes**: Eliminación suave con recuperación
2. **Logs de Auditoría**: Registro de cambios
3. **Notificaciones**: Sistema de alertas
4. **API REST**: Para integraciones
5. **App Móvil**: Para estudiantes
6. **Reportes Avanzados**: Más opciones de exportación
7. **Sistema de Mensajería**: Comunicación interna
8. **Calendario Interactivo**: Drag & drop para horarios

---

**Documento generado**: 11 de Noviembre de 2025  
**Versión del Sistema**: 1.0  
**Total de Casos de Uso**: **87 casos**

