# Análisis UC-003 — Dashboard

Referencia caso de uso: `docs/requerimientos/usecases/dashboard.md`

Diagramas relacionados:
- Diagrama de clases: `docs/diagrams/classes/dashboard-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/dashboard-comm.puml`
- Secuencias relevantes: `docs/diagrams/seq-login.puml` (login -> dashboard), `docs/diagrams/seq-mark-attendance-button.puml` (acciones desde dashboard)

Descripción técnica corta:

- Controller: `App\Http\Controllers\DashboardController`
- Modelos implicados: `Semestre`, `Horario`, `Asistencia`, `Aula`, `Grupo`, `Materia`
- Rutas: `/dashboard` y endpoints de export (`dashboard.export.*`)

Puntos a documentar:
 - Lógica por rol (admin vs docente)
 - Carga de datos y queries (con `with()` y agrupamientos)
 - Puntos de performance (consultas N+1, índices para `horarios` y `asistencias`)
