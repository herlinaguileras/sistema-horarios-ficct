# Análisis UC-007 — Gestión de Grupos

Referencia caso de uso: `docs/requerimientos/usecases/manage-grupos.md`

Diagramas relacionados:
- Diagrama de clases: `docs/diagrams/classes/manage-grupos-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/manage-grupos-comm.puml`

Descripción técnica corta:

- Controller: `App\Http\Controllers\GrupoController`
- Modelo: `Grupo` (relaciones con `Semestre`, `Materia`, `Docente`)
- Rutas: resource `grupos` en `routes/web.php`

Puntos a documentar:
 - Integridad referencial (existencia de semestre/materia/docente)
 - Preparación de datos para crear horarios (listas desplegables)
