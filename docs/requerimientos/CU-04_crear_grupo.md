ID: CU-04

Título: Crear grupo

Actor(es): Administrador / Coordinador

Prioridad: Media

Precondiciones:
- Usuario autorizado autenticado.

Postcondiciones:
- Nuevo grupo creado y asociado a una materia y semestre.

Trigger: Usuario solicita crear grupo para una materia.

Flujo principal:
1. Usuario abre "Crear grupo".
2. Ingresa nombre, capacidad, materia y horario base.
3. Pulsa "Crear".
4. Sistema valida y persiste `grupos`.

Flujos alternativos:
- A1: Capacidad excede límite —> mostrar advertencia.

Interfaces:
- UI: /grupos/create
- API: POST /api/grupos

Diagramas asociados:
- `docs/diagrams/uc_crear_grupo.puml`

Criterios de aceptación:
1. El grupo aparece en la lista de grupos y puede asociarse a horarios.
