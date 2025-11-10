# Análisis UC-004 — Gestión de Docentes

Referencia caso de uso: `docs/requerimientos/usecases/manage-docentes.md`

Diagramas relacionados:
- Diagrama de clases: `docs/diagrams/classes/manage-docentes-class.puml`
- Diagrama de comunicación: `docs/diagrams/comm/manage-docentes-comm.puml`
- Secuencia: `docs/diagrams/seq-create-docente.puml`

Descripción técnica corta:

- Controller: `App\Http\Controllers\DocenteController`
- Modelos: `User`, `Docente`, `Titulo`, `Role`
- Rutas: resource `docentes` en `routes/web.php`

Puntos a documentar:
 - Transacción DB para crear usuario+docente+título
 - Asignación de roles (tabla `role_user`)
 - Validaciones y reglas `unique` en email y código docente
