# Análisis UC-002 — Gestión de Perfil

Referencia caso de uso: `docs/requerimientos/usecases/profile-management.md`

Diagramas relacionados:

- Diagrama de clases: `docs/diagrams/classes/profile-class.puml` and `docs/diagrams/classes/user-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/profile-comm.puml`
- Secuencia: `docs/diagrams/seq-login.puml` (relacionado con autenticación)

Descripción técnica corta:

- Controller: `App\Http\Controllers\ProfileController`
- Request: `App\Http\Requests\ProfileUpdateRequest` (validaciones)
- Rutas: `routes/web.php` (profile routes)

Puntos a documentar:
 - Campos permitidos en edición
 - Efecto de cambiar email (reset de `email_verified_at`)
 - Eliminación segura de cuenta (validación de contraseña)
