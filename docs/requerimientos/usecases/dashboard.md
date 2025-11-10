# Caso de Uso: Ver Dashboard

- ID: UC-003
- Nombre: Ver Dashboard
- Actor(es): Admin, Docente, Otros usuarios autenticados
- Prioridad: Alta
- Precondición: Usuario autenticado
- Postcondición: Muestra datos relevantes según rol (horarios, asistencias, aulas disponibles)
- Flujo principal:
  1. Usuario accede a /dashboard.
  2. Sistema determina rol del usuario.
  3. Si es admin: carga semestres, horarios por día, asistencias agrupadas y vistas de aulas.
  4. Si es docente: carga horarios del docente para el semestre activo.
  5. Renderiza la vista correspondiente.
- Reglas de negocio:
  - Sólo usuarios con rol `admin` ven las funciones de gestión y exportes.
- Criterios de aceptación:
  - Dashboard muestra datos agrupados correctamente según semestre activo.
