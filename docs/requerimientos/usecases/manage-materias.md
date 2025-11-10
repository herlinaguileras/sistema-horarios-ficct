# Caso de Uso: Gestión de Materias

- ID: UC-005
- Nombre: Gestión de Materias (CRUD)
- Actor(es): Admin
- Prioridad: Alta
- Precondición: Admin autenticado
- Postcondición: Materia creada/actualizada/eliminada
- Flujo principal (Crear):
  1. Admin accede a /materias/create.
  2. Completa formulario: nombre, sigla, nivel_semestre, carrera.
  3. Sistema valida y crea la materia.
  4. Redirige a la lista.
- Reglas de negocio:
  - Sigla debe ser única.
- Criterios de aceptación:
  - Materia listada y accesible para asignación en grupos.
