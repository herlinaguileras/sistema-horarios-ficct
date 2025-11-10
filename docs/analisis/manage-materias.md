# Análisis UC-005 — Gestión de Materias

Referencia caso de uso: `docs/requerimientos/usecases/manage-materias.md`

Diagramas relacionados:
- Diagrama de clases: `docs/diagrams/classes/manage-materias-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/manage-materias-comm.puml`

Descripción técnica corta:

- Controller: `App\Http\Controllers\MateriaController`
- Modelo: `Materia`
- Rutas: resource `materias` en `routes/web.php`

Puntos a documentar:
 - Reglas de validación (sigla única)
 - Uso de `fillable` y persistencia
 - Operaciones CRUD y vistas asociadas
