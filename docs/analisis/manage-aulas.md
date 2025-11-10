# Análisis UC-006 — Gestión de Aulas

Referencia caso de uso: `docs/requerimientos/usecases/manage-aulas.md`

Diagramas relacionados:
- Diagrama de clases: `docs/diagrams/classes/manage-aulas-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/manage-aulas-comm.puml`

Descripción técnica corta:

- Controller: `App\Http\Controllers\AulaController`
- Modelo: `Aula`
- Rutas: resource `aulas` en `routes/web.php`

Puntos a documentar:
 - Validaciones (nombre único, capacidad)
 - Relación con `Horario` (selección de aula al crear horario)
