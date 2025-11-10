# 📚 Documentación Completa - Estructura de Base de Datos

## 🎯 Documentos Disponibles

### 1. **DATABASE_STRUCTURE.md** (Principal)
**Descripción:** Documentación técnica completa con todas las tablas, relaciones y normalización.

**Contenido:**
- ✅ Diagrama Entidad-Relación completo
- ✅ Modelo Relacional detallado
- ✅ Catálogo de 13 tablas
- ✅ Relaciones y cardinalidad
- ✅ Análisis de normalización (1FN, 2FN, 3FN)
- ✅ Mapeo ORM (Laravel Eloquent)
- ✅ Índices y optimizaciones
- ✅ Reglas de integridad referencial

**Usar cuando:** Necesites entender la estructura completa, relaciones o normalización.

---

### 2. **ER_DIAGRAM.md** (Visual)
**Descripción:** Diagramas visuales en PlantUML y flujos de datos.

**Contenido:**
- ✅ Código PlantUML para diagrama ER
- ✅ Diagrama de flujo de datos simplificado
- ✅ Diagrama de casos de uso por módulo
- ✅ Diagramas de estados (semestre, asistencia)
- ✅ Instrucciones para exportar a PNG/PDF

**Usar cuando:** Necesites crear diagramas visuales o presentaciones.

---

### 3. **GUIA_DISEÑO_BD.md** (Tutorial)
**Descripción:** Guía práctica paso a paso para diseñar bases de datos.

**Contenido:**
- ✅ Proceso completo de diseño (6 pasos)
- ✅ Análisis de requerimientos
- ✅ Modelo conceptual (ER)
- ✅ Modelo lógico
- ✅ Normalización con ejemplos
- ✅ Modelo físico e implementación
- ✅ Herramientas recomendadas
- ✅ Checklist final
- ✅ Ejemplo completo (Entidad Grupo)

**Usar cuando:** Necesites aprender o enseñar diseño de bases de datos.

---

### 4. **schema.sql** (Implementación)
**Descripción:** Script SQL completo para PostgreSQL.

**Contenido:**
- ✅ DDL completo (CREATE TABLE)
- ✅ Todas las llaves foráneas
- ✅ Todos los índices
- ✅ Constraints y validaciones
- ✅ Vistas (views) útiles
- ✅ Triggers automáticos
- ✅ Datos iniciales (seeders)

**Usar cuando:** Necesites crear la BD desde cero en PostgreSQL.

---

## 📊 Resumen Ejecutivo del Proyecto

### Estadísticas

| Métrica | Cantidad |
|---------|----------|
| **Tablas principales** | 11 |
| **Tablas pivot** | 2 |
| **Tablas del sistema** | 5+ |
| **Total tablas** | 18+ |
| **Relaciones 1:1** | 1 |
| **Relaciones 1:N** | 11 |
| **Relaciones M:N** | 2 |
| **Índices** | 40+ |
| **Vistas (views)** | 2 |

---

### Módulos del Sistema

```
┌─────────────────────────────────────────┐
│  1. AUTENTICACIÓN Y USUARIOS            │
│     • users                             │
│     • roles                             │
│     • permissions                       │
│     • role_user (pivot)                 │
│     • permission_role (pivot)           │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  2. DOCENTES                            │
│     • docentes                          │
│     • titulos                           │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  3. ACADÉMICO                           │
│     • semestres                         │
│     • materias                          │
│     • aulas                             │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  4. CARGA HORARIA                       │
│     • grupos                            │
│     • horarios                          │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  5. ASISTENCIAS                         │
│     • asistencias                       │
└─────────────────────────────────────────┘
```

---

## 🔗 Cadenas de Relaciones Importantes

### Cadena 1: Usuario → Docente → Grupo → Horario

```
users (1) ──┬── (1) docentes (1) ───┬── (N) grupos (1) ───┬── (N) horarios
            │                       │                      │
            │                       │                      └── (N) asistencias
            │                       │
            └── (N) titulos         └── (N) asistencias
```

### Cadena 2: Semestre → Grupos → Horarios

```
semestres (1) ───┬── (N) grupos (1) ───┬── (N) horarios
                 │                      │
                 └── materia (1)        └── aula (1)
                     docente (1)
```

### Cadena 3: Usuario → Roles → Permisos

```
users (M) ───┬── role_user ──┬── (N) roles (M) ───┬── permission_role ──┬── (N) permissions
             └───────────────┘                     └─────────────────────┘
```

---

## 📈 Casos de Uso Cubiertos

### ✅ Gestión de Usuarios
- Crear/editar/eliminar usuarios
- Asignar múltiples roles
- Verificación de email
- Autenticación segura

### ✅ Gestión de Docentes
- Registro completo de docentes
- Vinculación con usuario
- Múltiples títulos académicos
- Estado de actividad

### ✅ Carga Horaria
- Crear semestres académicos
- Definir materias por carrera
- Registrar aulas disponibles
- Asignar docente a materia
- Definir horarios semanales

### ✅ Asistencias
- Registro de asistencia (QR, manual)
- Justificaciones
- Reportes por docente
- Exportación a Excel/PDF

### ✅ Permisos y Roles
- Sistema RBAC completo
- Permisos granulares por módulo
- Niveles jerárquicos
- Gestión dinámica de roles

---

## 🎨 Ejemplo Visual: Flujo de Creación de Grupo

```
PASO 1: Crear Semestre
┌─────────────────┐
│  semestres      │
│  • nombre       │
│  • fecha_inicio │
│  • fecha_fin    │
└─────────────────┘
        ↓
PASO 2: Crear Materia
┌─────────────────┐
│  materias       │
│  • nombre       │
│  • sigla        │
│  • carrera      │
└─────────────────┘
        ↓
PASO 3: Registrar Docente
┌─────────────────┐      ┌─────────────────┐
│  users          │──1:1─│  docentes       │
│  • email        │      │  • codigo       │
│  • password     │      │  • CI           │
└─────────────────┘      └─────────────────┘
        ↓
PASO 4: Crear Grupo
┌─────────────────┐
│  grupos         │
│  • semestre_id ─┼────→ semestres
│  • materia_id ──┼────→ materias
│  • docente_id ──┼────→ docentes
│  • nombre (SA)  │
└─────────────────┘
        ↓
PASO 5: Asignar Horarios
┌─────────────────┐
│  horarios       │
│  • grupo_id ────┼────→ grupos
│  • aula_id ─────┼────→ aulas
│  • dia_semana   │
│  • hora_inicio  │
│  • hora_fin     │
└─────────────────┘
```

---

## 🔍 Consultas SQL Comunes

### 1. Carga Horaria de un Docente

```sql
SELECT 
    d.codigo_docente,
    u.name AS docente,
    m.sigla AS materia,
    g.nombre AS grupo,
    h.dia_semana,
    h.hora_inicio,
    h.hora_fin,
    a.nombre AS aula
FROM docentes d
JOIN users u ON d.user_id = u.id
JOIN grupos g ON d.id = g.docente_id
JOIN materias m ON g.materia_id = m.id
JOIN horarios h ON g.id = h.grupo_id
JOIN aulas a ON h.aula_id = a.id
WHERE d.codigo_docente = 'DOC001'
ORDER BY h.dia_semana, h.hora_inicio;
```

### 2. Asistencias del Mes

```sql
SELECT 
    u.name AS docente,
    COUNT(*) AS total_clases,
    COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) AS presentes,
    COUNT(CASE WHEN a.estado = 'Ausente' THEN 1 END) AS ausentes,
    ROUND(
        COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) * 100.0 / COUNT(*), 
        2
    ) AS porcentaje
FROM asistencias a
JOIN docentes d ON a.docente_id = d.id
JOIN users u ON d.user_id = u.id
WHERE EXTRACT(MONTH FROM a.fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
GROUP BY u.name;
```

### 3. Ocupación de Aulas

```sql
SELECT 
    a.nombre AS aula,
    COUNT(h.id) AS bloques_ocupados,
    a.capacidad,
    ROUND(COUNT(h.id) * 100.0 / 35, 2) AS porcentaje_uso
    -- 35 = 7 días * 5 bloques horarios promedio
FROM aulas a
LEFT JOIN horarios h ON a.id = h.aula_id
GROUP BY a.id, a.nombre, a.capacidad
ORDER BY porcentaje_uso DESC;
```

---

## 🛠️ Herramientas de Visualización

### Opción 1: dbdiagram.io (Recomendado)

**Pasos:**
1. Ir a https://dbdiagram.io/
2. Copiar código de `schema.sql`
3. Adaptar a sintaxis DBML
4. Exportar como PNG/PDF

**Ejemplo de código DBML:**
```dbml
Table users {
  id bigint [pk, increment]
  name varchar
  email varchar [unique]
}

Table docentes {
  id bigint [pk, increment]
  user_id bigint [ref: - users.id]
}
```

### Opción 2: MySQL Workbench

**Pasos:**
1. Abrir MySQL Workbench
2. Database → Reverse Engineer
3. Seleccionar tu BD PostgreSQL
4. Genera diagrama ER automáticamente

### Opción 3: DBeaver (Gratis)

**Pasos:**
1. Instalar DBeaver (https://dbeaver.io/)
2. Conectar a tu BD PostgreSQL
3. Click derecho en BD → ER Diagram
4. Exportar como imagen

---

## 📝 Checklist de Implementación

### Base de Datos
- [ ] Crear base de datos en PostgreSQL
- [ ] Ejecutar script `schema.sql`
- [ ] Verificar todas las tablas creadas
- [ ] Verificar integridad referencial

### Laravel
- [ ] Crear todas las migraciones
- [ ] Ejecutar `php artisan migrate`
- [ ] Crear todos los modelos Eloquent
- [ ] Definir relaciones en modelos
- [ ] Crear seeders para datos iniciales
- [ ] Ejecutar `php artisan db:seed`

### Validación
- [ ] Probar inserción de datos
- [ ] Probar eliminación en cascada
- [ ] Verificar constraints funcionando
- [ ] Probar consultas complejas
- [ ] Verificar índices mejorando performance

---

## 🚀 Próximos Pasos

1. **Revisar documentación creada**
   - Leer `DATABASE_STRUCTURE.md`
   - Estudiar diagrama en `ER_DIAGRAM.md`
   - Practicar con `GUIA_DISEÑO_BD.md`

2. **Crear diagramas visuales**
   - Usar dbdiagram.io
   - Exportar a PNG para presentación
   - Documentar en carpeta `docs/diagrams/`

3. **Implementar mejoras**
   - Agregar índices adicionales si es necesario
   - Crear vistas (views) para reportes
   - Implementar triggers útiles

4. **Documentar casos de uso**
   - Crear documento con flujos de trabajo
   - Documentar consultas frecuentes
   - Crear guía de optimización

---

## 📧 Contacto y Soporte

**Documentación creada por:** GitHub Copilot  
**Fecha:** 27 de Octubre, 2025  
**Versión del sistema:** Laravel 11.x + PostgreSQL 14+

**Archivos relacionados:**
- `docs/diagrams/DATABASE_STRUCTURE.md`
- `docs/diagrams/ER_DIAGRAM.md`
- `docs/diagrams/GUIA_DISEÑO_BD.md`
- `docs/diagrams/schema.sql`

---

## 📚 Referencias

- PostgreSQL Documentation: https://www.postgresql.org/docs/
- Laravel Migrations: https://laravel.com/docs/migrations
- Database Design Principles: https://www.vertabelo.com/blog/
- PlantUML: https://plantuml.com/
- dbdiagram.io: https://dbdiagram.io/

---

**¡Toda la documentación está lista para ser utilizada!** 🎉
