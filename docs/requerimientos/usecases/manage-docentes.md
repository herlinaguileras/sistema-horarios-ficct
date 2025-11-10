# Caso de Uso: Gestión de Docentes

- ID: UC-004
- Nombre: Gestión de Docentes (CRUD)
- Actor(es): Admin
- Prioridad: Alta
- Precondición: Admin autenticado
- Postcondición: Docente creado/actualizado/eliminado y usuario asociado creado/actualizado
- Flujo principal (Crear):
  1. Admin accede a /docentes/create.
  2. Completa formulario: name, email, password, codigo_docente, carnet_identidad, telefono, titulo.
  3. Sistema valida datos, crea User, crea Docente, crea Titulo y asigna rol 'docente'.
  4. Redirige a lista de docentes.
- Flujo principal (Editar):
  1. Admin edita datos y envía formulario.
  2. Sistema actualiza usuario y perfil docente dentro de una transacción.
  3. Redirige a la lista con mensaje.
- Reglas de negocio:
  - Email debe ser único.
  - Se crea la relación user->docente y se asigna rol automaticamente.
- Criterios de aceptación:
  - Docente aparece en la lista con su nombre y email.
