ID: CU-03

Título: Registrar docente

Actor(es): Administrador

Prioridad: Media

Precondiciones:
- Usuario administrador autenticado.

Postcondiciones:
- Nuevo registro en `docentes` y `users` (si aplica) creado.

Trigger: Administrador selecciona "Agregar docente".

Flujo principal:
1. Administrador abre formulario "Nuevo docente".
2. Completa datos personales y académicos.
3. Pulsa "Guardar".
4. Sistema valida y crea `docentes` y opcionalmente cuenta de usuario.

Flujos alternativos:
- A1: Email ya registrado —> mostrar error y opción para reusar cuenta existente.

Reglas de negocio:
- Validar unicidad de documento/email.

Interfaces:
- UI: /docentes/create
- API: POST /api/docentes

Diagramas asociados:
- `docs/diagrams/uc_registrar_docente.puml`

Criterios de aceptación:
1. Nuevo docente aparece en listado y se vincula a usuario si se creó.
