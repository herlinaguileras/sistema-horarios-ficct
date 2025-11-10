# Análisis UC-001 — Login

Referencia caso de uso: `docs/requerimientos/usecases/login.md`

Diagramas relacionados:

- Caso de uso: `docs/requerimientos/usecases/usecases.puml`
- Diagrama de clases: `docs/diagrams/classes/user-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/login-comm.puml`
- Diagrama de secuencia: `docs/diagrams/seq-login.puml` and `docs/diagrams/login-seq.puml`

Descripción técnica corta:

- Controller: `App\Http\Controllers\Auth\AuthenticatedSessionController`
- Modelo: `App\Models\User`
- Rutas: `routes/auth.php` (GET/POST `login`)

Puntos a documentar en detalle:

1. Validaciones de input y mensajes de error.
2. Manejo de throttle (limitación de intentos).
3. Flujo cuando el email no está verificado.
