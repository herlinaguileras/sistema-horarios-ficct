-- ============================================================================
-- ESQUEMA COMPLETO - Sistema de Horarios y Asistencias FICCT
-- Base de Datos: PostgreSQL
-- Fecha: 27 de Octubre, 2025
-- Descripción: Script DDL completo con todas las tablas, relaciones e índices
-- ============================================================================

-- Configuración inicial
SET client_encoding = 'UTF8';
SET timezone = 'America/La_Paz';

-- ============================================================================
-- MÓDULO: AUTENTICACIÓN Y USUARIOS
-- ============================================================================

-- Tabla: users
-- Descripción: Usuarios del sistema (administradores, docentes)
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE INDEX idx_users_email ON users(email);
COMMENT ON TABLE users IS 'Usuarios del sistema con autenticación';

-- Tabla: roles
-- Descripción: Roles del sistema (admin, docente, coordinador, etc.)
CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    level INTEGER NOT NULL DEFAULT 10,
    status VARCHAR(50) NOT NULL DEFAULT 'Activo',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT chk_roles_level CHECK (level BETWEEN 1 AND 100),
    CONSTRAINT chk_roles_status CHECK (status IN ('Activo', 'Inactivo'))
);

CREATE INDEX idx_roles_name ON roles(name);
CREATE INDEX idx_roles_status ON roles(status);
COMMENT ON TABLE roles IS 'Roles de usuarios con niveles jerárquicos';

-- Tabla: permissions
-- Descripción: Permisos granulares del sistema
CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    module VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE INDEX idx_permissions_module ON permissions(module);
COMMENT ON TABLE permissions IS 'Permisos específicos por módulo';

-- Tabla pivot: role_user
-- Descripción: Relación M:N entre usuarios y roles
CREATE TABLE role_user (
    user_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,

    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_role_user_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_user_role FOREIGN KEY (role_id)
        REFERENCES roles(id) ON DELETE CASCADE
);

CREATE INDEX idx_role_user_user ON role_user(user_id);
CREATE INDEX idx_role_user_role ON role_user(role_id);
COMMENT ON TABLE role_user IS 'Tabla pivot para asignación de roles a usuarios';

-- Tabla pivot: permission_role
-- Descripción: Relación M:N entre roles y permisos
CREATE TABLE permission_role (
    id BIGSERIAL PRIMARY KEY,
    permission_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT uk_permission_role UNIQUE (permission_id, role_id),
    CONSTRAINT fk_permission_role_permission FOREIGN KEY (permission_id)
        REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_permission_role_role FOREIGN KEY (role_id)
        REFERENCES roles(id) ON DELETE CASCADE
);

CREATE INDEX idx_permission_role_permission ON permission_role(permission_id);
CREATE INDEX idx_permission_role_role ON permission_role(role_id);
COMMENT ON TABLE permission_role IS 'Tabla pivot para asignación de permisos a roles';

-- ============================================================================
-- MÓDULO: DOCENTES
-- ============================================================================

-- Tabla: docentes
-- Descripción: Información específica de docentes (extensión de users)
CREATE TABLE docentes (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE,
    codigo_docente VARCHAR(255) NOT NULL UNIQUE,
    carnet_identidad VARCHAR(255) NOT NULL,
    telefono VARCHAR(255) NULL,
    facultad VARCHAR(255) NOT NULL DEFAULT 'FICCT',
    estado VARCHAR(50) NOT NULL DEFAULT 'Activo',
    fecha_contratacion DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_docentes_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_docentes_estado CHECK (estado IN ('Activo', 'Inactivo', 'Licencia'))
);

CREATE INDEX idx_docentes_user ON docentes(user_id);
CREATE INDEX idx_docentes_codigo ON docentes(codigo_docente);
CREATE INDEX idx_docentes_estado ON docentes(estado);
COMMENT ON TABLE docentes IS 'Perfil extendido de docentes vinculado a users';

-- Tabla: titulos
-- Descripción: Títulos académicos de docentes
CREATE TABLE titulos (
    id BIGSERIAL PRIMARY KEY,
    docente_id BIGINT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_titulos_docente FOREIGN KEY (docente_id)
        REFERENCES docentes(id) ON DELETE CASCADE
);

CREATE INDEX idx_titulos_docente ON titulos(docente_id);
COMMENT ON TABLE titulos IS 'Títulos académicos y certificaciones de docentes';

-- ============================================================================
-- MÓDULO: ACADÉMICO (SEMESTRES, MATERIAS, AULAS)
-- ============================================================================

-- Tabla: semestres
-- Descripción: Períodos académicos (semestres o gestiones)
CREATE TABLE semestres (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'Planificación',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT chk_semestres_fechas CHECK (fecha_fin > fecha_inicio),
    CONSTRAINT chk_semestres_estado CHECK (estado IN ('Planificación', 'Activo', 'Finalizado'))
);

CREATE INDEX idx_semestres_estado ON semestres(estado);
CREATE INDEX idx_semestres_fechas ON semestres(fecha_inicio, fecha_fin);
COMMENT ON TABLE semestres IS 'Períodos académicos con fechas y estado';

-- Tabla: materias
-- Descripción: Asignaturas de las carreras
CREATE TABLE materias (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    sigla VARCHAR(255) NOT NULL UNIQUE,
    nivel_semestre INTEGER NOT NULL,
    carrera VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT chk_materias_nivel CHECK (nivel_semestre BETWEEN 1 AND 10)
);

CREATE INDEX idx_materias_sigla ON materias(sigla);
CREATE INDEX idx_materias_carrera ON materias(carrera);
CREATE INDEX idx_materias_nivel ON materias(nivel_semestre);
COMMENT ON TABLE materias IS 'Asignaturas de las diferentes carreras';

-- Tabla: aulas
-- Descripción: Espacios físicos para clases
CREATE TABLE aulas (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    piso INTEGER NOT NULL,
    capacidad INTEGER NULL,
    tipo VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT chk_aulas_piso CHECK (piso BETWEEN -2 AND 10)
);

CREATE INDEX idx_aulas_nombre ON aulas(nombre);
CREATE INDEX idx_aulas_piso ON aulas(piso);
COMMENT ON TABLE aulas IS 'Aulas y laboratorios disponibles para clases';

-- ============================================================================
-- MÓDULO: CARGA HORARIA (GRUPOS Y HORARIOS)
-- ============================================================================

-- Tabla: grupos
-- Descripción: Grupos de carga horaria (asignación docente-materia-semestre)
CREATE TABLE grupos (
    id BIGSERIAL PRIMARY KEY,
    semestre_id BIGINT NOT NULL,
    materia_id BIGINT NOT NULL,
    docente_id BIGINT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_grupos_semestre FOREIGN KEY (semestre_id)
        REFERENCES semestres(id),
    CONSTRAINT fk_grupos_materia FOREIGN KEY (materia_id)
        REFERENCES materias(id),
    CONSTRAINT fk_grupos_docente FOREIGN KEY (docente_id)
        REFERENCES docentes(id)
);

CREATE INDEX idx_grupos_semestre ON grupos(semestre_id);
CREATE INDEX idx_grupos_materia ON grupos(materia_id);
CREATE INDEX idx_grupos_docente ON grupos(docente_id);
CREATE INDEX idx_grupos_lookup ON grupos(semestre_id, materia_id, docente_id);
COMMENT ON TABLE grupos IS 'Asignación de docentes a materias en un semestre';

-- Tabla: horarios
-- Descripción: Bloques de horario específicos (día, hora, aula)
CREATE TABLE horarios (
    id BIGSERIAL PRIMARY KEY,
    grupo_id BIGINT NOT NULL,
    aula_id BIGINT NOT NULL,
    dia_semana SMALLINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_horarios_grupo FOREIGN KEY (grupo_id)
        REFERENCES grupos(id) ON DELETE CASCADE,
    CONSTRAINT fk_horarios_aula FOREIGN KEY (aula_id)
        REFERENCES aulas(id),
    CONSTRAINT chk_horarios_dia CHECK (dia_semana BETWEEN 1 AND 7),
    CONSTRAINT chk_horarios_horas CHECK (hora_fin > hora_inicio)
);

CREATE INDEX idx_horarios_grupo ON horarios(grupo_id);
CREATE INDEX idx_horarios_aula ON horarios(aula_id);
CREATE INDEX idx_horarios_dia ON horarios(dia_semana);
CREATE INDEX idx_horarios_aula_dia ON horarios(aula_id, dia_semana);
COMMENT ON TABLE horarios IS 'Bloques horarios semanales de cada grupo';

-- ============================================================================
-- MÓDULO: ASISTENCIAS
-- ============================================================================

-- Tabla: asistencias
-- Descripción: Registro de asistencias de docentes
CREATE TABLE asistencias (
    id BIGSERIAL PRIMARY KEY,
    horario_id BIGINT NOT NULL,
    docente_id BIGINT NOT NULL,
    fecha DATE NOT NULL,
    hora_registro TIME NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'Presente',
    metodo_registro VARCHAR(50) NULL,
    justificacion TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_asistencias_horario FOREIGN KEY (horario_id)
        REFERENCES horarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_asistencias_docente FOREIGN KEY (docente_id)
        REFERENCES docentes(id),
    CONSTRAINT chk_asistencias_estado CHECK (estado IN ('Presente', 'Ausente', 'Licencia', 'Tardanza'))
);

CREATE INDEX idx_asistencias_horario ON asistencias(horario_id);
CREATE INDEX idx_asistencias_docente ON asistencias(docente_id);
CREATE INDEX idx_asistencias_fecha ON asistencias(fecha);
CREATE INDEX idx_asistencias_estado ON asistencias(estado);
CREATE INDEX idx_asistencias_reportes ON asistencias(fecha, estado, docente_id);
COMMENT ON TABLE asistencias IS 'Registro de asistencias de docentes por horario';

-- ============================================================================
-- TABLAS DEL SISTEMA (CACHÉ, JOBS, LOGS)
-- ============================================================================

-- Tabla: cache
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

-- Tabla: cache_locks
CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- Tabla: jobs
CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX idx_jobs_queue ON jobs(queue);

-- Tabla: failed_jobs
CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: audit_logs
CREATE TABLE audit_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NULL,
    action VARCHAR(255) NOT NULL,
    model VARCHAR(255) NULL,
    model_id BIGINT NULL,
    old_values TEXT NULL,
    new_values TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL,

    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_model ON audit_logs(model, model_id);
CREATE INDEX idx_audit_logs_action ON audit_logs(action);
COMMENT ON TABLE audit_logs IS 'Registro de auditoría de acciones en el sistema';

-- ============================================================================
-- VISTAS (VIEWS) PARA CONSULTAS COMUNES
-- ============================================================================

-- Vista: vista_carga_horaria_docentes
-- Descripción: Muestra la carga horaria completa de cada docente
CREATE OR REPLACE VIEW vista_carga_horaria_docentes AS
SELECT
    d.id AS docente_id,
    d.codigo_docente,
    u.name AS docente_nombre,
    s.nombre AS semestre,
    m.nombre AS materia,
    m.sigla AS materia_sigla,
    g.nombre AS grupo,
    COUNT(h.id) AS total_horarios,
    SUM(
        EXTRACT(EPOCH FROM (h.hora_fin - h.hora_inicio)) / 3600
    ) AS horas_semanales
FROM docentes d
JOIN users u ON d.user_id = u.id
JOIN grupos g ON d.id = g.docente_id
JOIN semestres s ON g.semestre_id = s.id
JOIN materias m ON g.materia_id = m.id
LEFT JOIN horarios h ON g.id = h.grupo_id
GROUP BY d.id, d.codigo_docente, u.name, s.nombre, m.nombre, m.sigla, g.nombre;

-- Vista: vista_asistencias_resumen
-- Descripción: Resumen de asistencias por docente
CREATE OR REPLACE VIEW vista_asistencias_resumen AS
SELECT
    d.id AS docente_id,
    d.codigo_docente,
    u.name AS docente_nombre,
    COUNT(*) AS total_registros,
    COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) AS presentes,
    COUNT(CASE WHEN a.estado = 'Ausente' THEN 1 END) AS ausentes,
    COUNT(CASE WHEN a.estado = 'Licencia' THEN 1 END) AS licencias,
    ROUND(
        (COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) * 100.0) /
        NULLIF(COUNT(*), 0),
        2
    ) AS porcentaje_asistencia
FROM docentes d
JOIN users u ON d.user_id = u.id
LEFT JOIN asistencias a ON d.id = a.docente_id
GROUP BY d.id, d.codigo_docente, u.name;

-- ============================================================================
-- DATOS INICIALES (SEEDERS)
-- ============================================================================

-- Insertar roles iniciales
INSERT INTO roles (name, description, level, status) VALUES
('admin', 'Administrador del sistema con acceso total', 100, 'Activo'),
('docente', 'Docente con acceso a horarios y asistencias', 50, 'Activo')
ON CONFLICT (name) DO NOTHING;

-- Insertar permisos iniciales (ejemplo)
INSERT INTO permissions (name, description, module) VALUES
-- Usuarios
('usuarios.ver', 'Ver lista de usuarios', 'Usuarios'),
('usuarios.crear', 'Crear nuevos usuarios', 'Usuarios'),
('usuarios.editar', 'Editar usuarios existentes', 'Usuarios'),
('usuarios.eliminar', 'Eliminar usuarios', 'Usuarios'),

-- Roles
('roles.ver', 'Ver lista de roles', 'Roles'),
('roles.crear', 'Crear nuevos roles', 'Roles'),
('roles.editar', 'Editar roles existentes', 'Roles'),
('roles.eliminar', 'Eliminar roles', 'Roles'),

-- Docentes
('docentes.ver', 'Ver lista de docentes', 'Docentes'),
('docentes.crear', 'Crear nuevos docentes', 'Docentes'),
('docentes.editar', 'Editar docentes existentes', 'Docentes'),
('docentes.eliminar', 'Eliminar docentes', 'Docentes'),

-- Asistencias
('asistencias.ver', 'Ver asistencias', 'Asistencias'),
('asistencias.crear', 'Registrar asistencias', 'Asistencias'),
('asistencias.exportar', 'Exportar reportes', 'Asistencias')
ON CONFLICT (name) DO NOTHING;

-- ============================================================================
-- FUNCIONES Y TRIGGERS (OPCIONALES)
-- ============================================================================

-- Función: actualizar updated_at automáticamente
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Aplicar trigger a todas las tablas relevantes
CREATE TRIGGER trg_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER trg_roles_updated_at BEFORE UPDATE ON roles
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER trg_docentes_updated_at BEFORE UPDATE ON docentes
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER trg_grupos_updated_at BEFORE UPDATE ON grupos
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- ============================================================================
-- COMENTARIOS FINALES
-- ============================================================================

COMMENT ON DATABASE current_database() IS 'Sistema de Gestión de Horarios y Asistencias - FICCT';

-- ============================================================================
-- FIN DEL SCRIPT
-- ============================================================================

-- Verificar integridad
DO $$
BEGIN
    RAISE NOTICE 'Esquema de base de datos creado exitosamente';
    RAISE NOTICE 'Total de tablas: %', (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public');
END $$;
