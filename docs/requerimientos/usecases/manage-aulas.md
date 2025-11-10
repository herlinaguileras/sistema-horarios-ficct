# Caso de Uso: Gestión de Aulas

- ID: UC-006
- Nombre: Gestión de Aulas (CRUD)
- Actor(es): Admin
- Prioridad: Media
- Precondición: Admin autenticado
- Postcondición: Aula creada/actualizada/eliminada
- Flujo principal (Crear):
  1. Admin accede a /aulas/create.
  2. Completa formulario: nombre, piso, tipo, capacidad.
  3. Sistema valida y crea aula.
  4. Redirige a la lista.
- Reglas de negocio:
  - Nombre de aula único.
- Criterios de aceptación:
  - Aula aparece en lista y es seleccionable al crear horarios.
