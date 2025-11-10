# Análisis UC-009 — Gestión de Asistencias

Referencia caso de uso: `docs/requerimientos/usecases/manage-asistencias.md`

Diagramas relacionados:
- Diagrama de clases: `docs/diagrams/classes/manage-asistencias-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/manage-asistencias-comm.puml`

Descripción técnica corta:

- Controller: `App\Http\Controllers\AsistenciaController`
- Modelo: `Asistencia` (validaciones de coherencia con `Horario`)
- Rutas: nested resource `horarios.asistencias` en `routes/web.php`

Puntos a documentar:
 - Validación de día y ventana horaria (-15/+15 minutos)
 - Registro de auditoría (`AuditLog`)
 - Prevención de duplicados
