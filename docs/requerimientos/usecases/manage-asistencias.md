# Caso de Uso: Gestión de Asistencias (Listar, Crear manual, Eliminar)

- ID: UC-009
- Nombre: Gestión de Asistencias
- Actor(es): Admin, Docente (según permisos)
- Prioridad: Alta
- Precondición: Horario existente
- Postcondición: Registro de asistencia agregado o eliminado
- Flujo principal (Crear manual):
  1. Admin/Docente accede a /horarios/{horario}/asistencias/create.
  2. Completa fecha, hora_registro, estado, metodo_registro, justificacion.
  3. Sistema valida coherencia (día coincide y hora en ventana -15/+15 min) y crea registro.
  4. Guarda bitácora de auditoría.
- Flujo principal (Eliminar):
  1. Admin borra registro desde la lista.
  2. Sistema elimina y redirige con mensaje.
- Reglas de negocio:
  - Ventana permitida para marcar asistencia: -15/+15 minutos desde hora_inicio.
  - No permitir marcar asistencia duplicada para el mismo día y horario.
- Criterios de aceptación:
  - Asistencia creada sólo dentro de la ventana y sin duplicados.
