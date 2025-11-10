# Caso de Uso: Gestión de Grupos (Carga Horaria)

- ID: UC-007
- Nombre: Gestión de Grupos (CRUD)
- Actor(es): Admin
- Prioridad: Alta
- Precondición: Admin autenticado
- Postcondición: Grupo creado/actualizado/eliminado y asignado a materia, docente y semestre
- Flujo principal (Crear):
  1. Admin accede a /grupos/create.
  2. Selecciona semestre, materia, docente y nombre del grupo.
  3. Sistema valida IDs y crea el grupo.
  4. Redirige a la lista de grupos.
- Reglas de negocio:
  - docente_id, materia_id y semestre_id deben existir.
- Criterios de aceptación:
  - Grupo disponible para crear horarios (ruta anidada).
