ID: CU-01

Título: Registrar asistencia

Actor(es): Docente

Prioridad: Alta

Precondiciones:
- Docente autenticado.
- Existe un horario y grupo activo.

Postcondiciones:
- Se crean registros en `asistencias` con referencias a `docente`, `grupo`, `horario`.

Trigger: Docente abre la pantalla de asistencias y marca presentes.

Flujo principal:
1. Docente accede a la pantalla "Asistencias".
2. El sistema carga alumnos del grupo y horario.
3. Docente marca estado (presente/ausente).
4. Docente pulsa "Guardar".
5. Sistema persiste registros y muestra confirmación.

Flujos alternativos:
- A1: No existe horario activo —> mostrar mensaje y opción para crear horario.
- A2: Error de validación —> mostrar errores y permitir corrección.

Reglas de negocio:
- Solo docentes autorizados pueden registrar.
- No duplicar registros para la misma sesión y alumno.

Datos principales:
- entrada: grupo_id, horario_id, docente_id, lista {alumno_id, estado}
- salida: confirmación y conteo de registros creados

Interfaces:
- UI: /asistencias
- API: POST /api/asistencias

Diagramas asociados:
- `docs/diagrams/uc_registrar_asistencia.puml`

Criterios de aceptación:
1. Al enviar la lista, se crean N registros en `asistencias`.
2. Usuarios sin permiso reciben 403.
