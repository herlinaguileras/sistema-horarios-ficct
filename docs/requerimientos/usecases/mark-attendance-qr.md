# Caso de Uso: Marcar Asistencia (QR)

- ID: UC-011
- Nombre: Marcar Asistencia (QR)
- Actor(es): Docente autenticado
- Prioridad: Alta
- Precondición: Docente autenticado y asociado al grupo del horario; dentro de la ventana de tiempo
- Postcondición: Registro de asistencia creado con metodo_registro='QR'
- Flujo principal:
  1. Docente escanea QR o accede a /horarios/{horario}/qr y pulsa marcar.
  2. Sistema verifica identidad y ventana de tiempo.
  3. Si válido y no existe registro, crea Asistencia y redirige con mensaje.
- Reglas de negocio:
  - Mismas reglas que el marcado por botón.
- Criterios de aceptación:
  - QR permite marcar siempre y cuando la identidad y ventana sean correctas.
