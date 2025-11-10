# Caso de Uso: Marcar Asistencia (Botón)

- ID: UC-010
- Nombre: Marcar Asistencia (Botón)
- Actor(es): Docente autenticado
- Prioridad: Alta
- Precondición: Docente autenticado y asociado al grupo del horario; dentro de la ventana de tiempo
- Postcondición: Registro de asistencia creado con metodo_registro='Boton'
- Flujo principal:
  1. Docente pulsa botón 'Marcar asistencia' en su dashboard.
  2. Sistema verifica identidad del docente y ventana de tiempo.
  3. Si válido y no existe registro, crea Asistencia y redirige con mensaje.
- Reglas de negocio:
  - Validar que el docente pertenece al horario.
  - Sólo una marca por día por horario por docente.
- Criterios de aceptación:
  - Botón crea registro si se cumplen condiciones; caso contrario muestra error.
