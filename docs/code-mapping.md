# Mapeo de código a documentación

Este archivo sirve para mapear artefactos del repo a los diagramas y secciones de la documentación.

1. Modelos (app/Models): listar y asociar a entidades del ERD y diagrama de clases.
   - `User` -> `users` (tabla)
   - `Docente` -> `docentes`
   - `Materia` -> `materias`
   - `Grupo` -> `grupos`
   - `Horario` -> `horarios`
   - `Aula` -> `aulas`

2. Migraciones (database/migrations): fuente de verdad del esquema (usar para DDL y ERD).

3. Controladores (app/Http/Controllers): fuentes de diagramas de secuencia y de casos de uso.

4. Rutas (routes/*.php): endpoints principales que se mapean a casos de uso.

5. Seeders (database/seeders): ejemplos de datos para anexos y tablas de volumen.

Instrucción práctica: el equipo de documentación debe extraer la lista actual de modelos y migraciones y completar las tablas anteriores con columnas y tipos (puedo automatizar este paso si lo deseas).
