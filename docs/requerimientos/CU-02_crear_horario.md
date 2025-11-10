ID: CU-02

Título: Crear horario

Actor(es): Administrador / Coordinador

Prioridad: Alta

Precondiciones:
- Usuario con rol administrador autenticado.

Postcondiciones:
- Se crea un registro en `horarios` y se asignan grupos y aulas según disponibilidad.

Trigger: Usuario accede a la sección de horarios y crea un nuevo horario.

Flujo principal:
1. Usuario abre pantalla "Crear horario".
2. Completa datos: semestre, materia, docente, grupo, aula, día, hora.
3. Pulsa "Crear".
4. Sistema valida conflictos y persiste `horarios`.
5. Muestra confirmación y notifica a docentes afectados.

Flujos alternativos:
- A1: Conflicto de aula/horario —> mostrar conflicto y sugerir alternativas.

Reglas de negocio:
- No asignar una misma aula a dos grupos en la misma franja.

Interfaces:
- UI: /horarios/create
- API: POST /api/horarios

Diagramas asociados:
- `docs/diagrams/uc_crear_horario.puml`

Criterios de aceptación:
1. El horario creado aparece en la lista de horarios.
2. No se permiten registros con conflictos detectados.
