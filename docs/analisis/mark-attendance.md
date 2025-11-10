# Análisis UC-010 — Marcar Asistencia (Botón / QR)

Referencia caso de uso: `docs/requerimientos/usecases/mark-attendance-button.md`

Diagramas relacionados:
- Diagrama de clases: `docs/diagrams/classes/mark-attendance-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/mark-attendance-comm.puml`
- Secuencia: `docs/diagrams/seq-mark-attendance-button.puml`

Descripción técnica corta:

- Controller: `App\Http\Controllers\AsistenciaController` (métodos `marcarAsistencia`, `marcarAsistenciaQr`)
- Modelo: `Asistencia`, `Horario`, `Docente`
- Rutas: `POST /asistencias/marcar/{horario}`, `POST /asistencias/marcar-qr/{horario}`

Puntos a documentar:
 - Autorización (el docente debe pertenecer al grupo)
 - Lógica de ventana de tiempo y duplicados
 - Feedback al usuario desde el dashboard
