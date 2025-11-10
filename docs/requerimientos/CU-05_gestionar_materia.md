ID: CU-05

Título: Gestionar materia

Actor(es): Administrador

Prioridad: Media

Precondiciones:
- Usuario administrador autenticado.

Postcondiciones:
- Materia creada/actualizada/eliminada en `materias`.

Trigger: Administrador accede a la gestión de materias.

Flujo principal:
1. Administrador accede a la lista de materias.
2. Crea/edita/elimina una materia.
3. Sistema valida y persiste cambios.

Flujos alternativos:
- A1: No se puede eliminar por dependencia (tiene horarios) —> mostrar advertencia.

Interfaces:
- UI: /materias
- API: CRUD /api/materias

Diagramas asociados:
- `docs/diagrams/uc_gestionar_materia.puml`

Criterios de aceptación:
1. CRUD de materias funciona y respeta dependencias.
