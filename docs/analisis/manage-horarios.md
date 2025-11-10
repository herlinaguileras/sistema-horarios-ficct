# Análisis UC-008 — Gestión de Horarios

Referencia caso de uso: `docs/requerimientos/usecases/manage-horarios.md`

Diagramas relacionados:
- Diagrama de clases: `docs/diagrams/classes/manage-horarios-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/manage-horarios-comm.puml`
- Secuencia: `docs/diagrams/seq-create-horario.puml`

Descripción técnica corta:

- Controller: `App\Http\Controllers\HorarioController`
- Modelo: `Horario` (validaciones anti-conflicto), `Grupo`, `Aula`, `Docente`
- Rutas: nested resource `grupos.horarios` en `routes/web.php`

Puntos a documentar:
 - Lógica de detección de solapamientos (aula, docente, grupo)
 - Validaciones de formato de hora y rango
 - Consideraciones de concurrencia si varios admins crean horarios simultáneamente
