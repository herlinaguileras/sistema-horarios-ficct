ID: CU-06

Título: Administrar usuarios

Actor(es): Administrador

Prioridad: Alta

Precondiciones:
- Administrador autenticado.

Postcondiciones:
- Usuarios creados/actualizados/roles asignados.

Trigger: Administrador abre panel de usuarios.

Flujo principal:
1. Administrador visualiza lista de usuarios.
2. Crea/edita/elimna usuario o asigna roles.
3. Sistema valida y persiste cambios en `users` y `roles`.

Flujos alternativos:
- A1: Intento de eliminar usuario con datos dependientes —> bloquear o mostrar advertencia.

Interfaces:
- UI: /usuarios
- API: CRUD /api/users

Diagramas asociados:
- `docs/diagrams/uc_administrar_usuarios.puml`

Criterios de aceptación:
1. Los roles se aplican correctamente y controlan el acceso a funciones.
