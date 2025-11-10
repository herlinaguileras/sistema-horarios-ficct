# Caso de Uso: Login

- ID: UC-001
- Nombre: Login
- Actor(es): Usuario (Admin/Docente/Visitante)
- Prioridad: Alta
- Precondición: El usuario debe existir en la base de datos y tener credenciales.
- Postcondición: Sesión iniciada y redirección al dashboard correspondiente.
- Flujo principal:
  1. Usuario navega a /login.
  2. Ingresa email y contraseña y envía el formulario.
  3. Sistema valida credenciales.
  4. Si son válidas, crea sesión y redirige al dashboard.
- Flujos alternos:
  - Credenciales inválidas: mostrar mensaje de error.
  - Usuario no verificado: solicitar verificación por email según configuración.
- Reglas de negocio:
  - Limitar intentos de login (throttle) y manejar verificación de email cuando corresponda.
- Criterios de aceptación:
  - El usuario puede iniciar sesión con credenciales válidas.
  - Usuarios con rol `docente` ven el dashboard-docente.
