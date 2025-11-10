# Caso de Uso: Gestión de Horarios (CRUD dentro de Grupo)

- ID: UC-008
- Nombre: Gestión de Horarios
- Actor(es): Admin
- Prioridad: Alta
- Precondición: Admin autenticado y grupo existente
- Postcondición: Horario creado/actualizado/eliminado para un grupo concreto
- Flujo principal (Crear):
  1. Admin accede a /grupos/{grupo}/horarios/create.
  2. Selecciona día de la semana, aula, hora_inicio y hora_fin.
  3. Sistema valida formato y aplica detección de conflictos (aula, docente, grupo).
  4. Si no hay conflictos, crea horario y redirige a la lista.
- Reglas de negocio:
  - No permitir solapamiento: (inicio_nuevo < fin_existente) y (fin_nuevo > inicio_existente).
  - Validar que docente no tenga otra clase a la misma hora.
- Criterios de aceptación:
  - El horario se crea sólo si no hay conflictos detectados.
