# Caso de Uso: Gestión de Perfil (Editar / Eliminar cuenta)

- ID: UC-002
- Nombre: Gestión de Perfil
- Actor(es): Usuario autenticado
- Prioridad: Alta
- Precondición: Usuario autenticado
- Postcondición: Perfil actualizado o cuenta eliminada
- Flujo principal (Editar):
  1. Usuario accede a /profile.
  2. Modifica campos permitidos y envía formulario.
  3. Sistema valida y guarda cambios. Si cambia el email, resetea verificación.
  4. Redirige con mensaje de éxito.
- Flujo principal (Eliminar):
  1. Usuario solicita eliminación y confirma contraseña.
  2. Sistema valida contraseña, borra usuario, cierra sesión y redirige a /.
- Reglas de negocio:
  - Eliminar cuenta requiere confirmación con contraseña.
- Criterios de aceptación:
  - Los cambios persisten y se valida la lógica de email_verified_at cuando aplica.
