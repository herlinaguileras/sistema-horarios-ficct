# Guía para casos de uso

Propósito: Esta guía explica cómo identificar, documentar y evaluar casos de uso del sistema. Está pensada para que los integrantes del equipo rellenen rápidamente plantillas uniformes y produzcan artefactos trazables (texto, diagramas UML, criterios de aceptación).

Lenguaje: español. Mantener título claro y números de versión en cada caso de uso.

## ¿Qué es un caso de uso?

Un caso de uso describe una interacción entre uno o más actores (usuarios u otros sistemas) y el sistema para lograr un objetivo observable y con valor para el actor.

## Componentes mínimos de un caso de uso

- Identificador: CU-XXX (p.ej. CU-01)
- Título: nombre corto y descriptivo
- Actor(es): quien(es) inicia(n) o participa(n)
- Prioridad: Alta / Media / Baja
- Precondiciones: condiciones que deben cumplirse antes de iniciar
- Postcondiciones / Resultado esperado: estado después de completar el caso
- Disparador (trigger): evento que inicia el caso
- Flujo principal (pasos): pasos numerados del escenario feliz
- Flujos alternativos / Excepciones: variaciones, errores y cómo se resuelven
- Reglas de negocio relevantes
- Datos de entrada / salida: campos principales usados
- Interfaces afectadas: pantallas o endpoints REST
- Diagramas asociados: diagrama de casos de uso, diagrama de secuencia, diagrama de clases (si aplica)
- Criterios de aceptación: criterios concretos y verificables para considerar el caso como implementado

## Buenas prácticas

- Redactar en presente y con verbos centrados en el usuario (p.ej. "Docente registra asistencia").
- Mantener un flujo principal simple (5–15 pasos). Si es muy largo, dividir en sub-casos.
- Numerar alternativas (A1, A2) y referenciar en el flujo principal.
- Incluir IDs de historias relacionadas si usan gestión ágil (p.ej. backlog ticket).
- Añadir notas técnicas cuando la implementación tenga restricciones (concurrency, validaciones, integraciones externas).

## Checklist de calidad para cada caso de uso

- [ ] Tiene identificador y título
- [ ] Actor(es) claro(s)
- [ ] Flujo principal y al menos 1 alternativo
- [ ] Criterios de aceptación definidos y medibles
- [ ] Diagrama(s) asociado(s) cuando aplique
- [ ] Mapeo a modelos/migraciones o endpoints (referencia a archivo / ruta)

## Ejemplo aplicado (proyecto: sistema de horarios)

ID: CU-01

Título: Registrar asistencia

Actor principal: Docente

Prioridad: Alta

Precondiciones:
- El docente está autenticado.
- Existe un horario y grupo activo para la hora actual.

Postcondiciones:
- Se crea un registro `Asistencia` con timestamp y referencias a `Docente`, `Grupo`, `Horario`.

Trigger: El docente abre la pantalla de asistencia y confirma los presentes.

Flujo principal:
1. El docente accede a la pantalla "Asistencias".
2. El sistema obtiene el horario activo (consulta `horarios` y `grupos`).
3. El docente marca los estudiantes presentes/ausentes.
4. El docente pulsa "Guardar".
5. El sistema valida los datos y crea los registros en la tabla `asistencias`.
6. El sistema muestra confirmación.

Flujos alternativos:
- A1: Si no existe horario activo -> mostrar mensaje "No hay horario para la sesión actual" y ofrecer crear horario o volver.
- A2: Si hay error en validación (campo obligatorio) -> mostrar errores y permitir corrección.

Reglas de negocio:
- Solo docentes con rol autorizado pueden registrar asistencias.
- No se permiten registros duplicados para la misma sesión y alumno.

Datos principales:
- entrada: grupo_id, horario_id, docente_id, lista {alumno_id, estado}
- salida: confirmación y número de registros creados

Interfaces afectadas:
- UI: /asistencias (vista docente)
- API: POST /api/asistencias

Diagramas asociados:
- `docs/diagrams/uc_registrar_asistencia.puml` (diagrama de casos de uso)
- `docs/diagrams/seq_registrar_asistencia.puml` (diagrama de secuencia)

Criterios de aceptación (ejemplos verificables):
- Al enviar la lista de asistencias, se persisten N registros en `asistencias` (N = número de alumnos marcados).
- Si el docente no está autorizado, la acción devuelve HTTP 403 (cuando aplica API).
- La pantalla muestra un mensaje de éxito y el timestamp de grabación.

---

Si quieres, puedo generar automáticamente 6 casos de uso para este proyecto (rellenos con información extraída del código: actores, rutas y modelos), y crear los diagramas PlantUML base. ¿Quieres que genere esos casos ahora? Indica si prefieres que use títulos y actores predeterminados o me das una lista de casos prioritarios.
