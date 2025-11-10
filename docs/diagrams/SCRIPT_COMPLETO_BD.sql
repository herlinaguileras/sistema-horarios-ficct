-- ============================================================================
-- SCRIPT COMPLETO DE BASE DE DATOS
-- Sistema de Gestión de Horarios y Asistencias FICCT
-- Base de Datos: PostgreSQL 14+
-- Fecha: 28 de Octubre, 2025
-- Descripción: Script DDL/DML completo con tablas, triggers, procedures y 30 consultas
-- ============================================================================

-- ============================================================================
-- PARTE 1: CONFIGURACIÓN INICIAL Y CREACIÓN DE TABLAS
-- ============================================================================

-- Configuración inicial
SET client_encoding = 'UTF8';
SET timezone = 'America/La_Paz';

-- Eliminar base de datos si existe (CUIDADO: solo en desarrollo)
-- DROP DATABASE IF EXISTS sistema_horarios_ficct;
-- CREATE DATABASE sistema_horarios_ficct;
-- \c sistema_horarios_ficct;

-- ============================================================================
-- MÓDULO: AUTENTICACIÓN Y USUARIOS
-- ============================================================================

-- Tabla: users
CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
COMMENT ON TABLE users IS 'Usuarios del sistema con autenticación';

-- Tabla: roles
CREATE TABLE IF NOT EXISTS roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    level INTEGER NOT NULL DEFAULT 10,
    status VARCHAR(50) NOT NULL DEFAULT 'Activo',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_roles_level CHECK (level BETWEEN 1 AND 100),
    CONSTRAINT chk_roles_status CHECK (status IN ('Activo', 'Inactivo'))
);

CREATE INDEX IF NOT EXISTS idx_roles_name ON roles(name);
CREATE INDEX IF NOT EXISTS idx_roles_status ON roles(status);
COMMENT ON TABLE roles IS 'Roles de usuarios con niveles jerárquicos';

-- Tabla: permissions
CREATE TABLE IF NOT EXISTS permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    module VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_permissions_module ON permissions(module);
COMMENT ON TABLE permissions IS 'Permisos específicos por módulo';

-- Tabla pivot: role_user
CREATE TABLE IF NOT EXISTS role_user (
    user_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,

    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_role_user_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_user_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_role_user_user ON role_user(user_id);
CREATE INDEX IF NOT EXISTS idx_role_user_role ON role_user(role_id);

-- Tabla pivot: permission_role
CREATE TABLE IF NOT EXISTS permission_role (
    id BIGSERIAL PRIMARY KEY,
    permission_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uk_permission_role UNIQUE (permission_id, role_id),
    CONSTRAINT fk_permission_role_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_permission_role_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- ============================================================================
-- MÓDULO: DOCENTES
-- ============================================================================

-- Tabla: docentes
CREATE TABLE IF NOT EXISTS docentes (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE,
    codigo_docente VARCHAR(255) NOT NULL UNIQUE,
    carnet_identidad VARCHAR(255) NOT NULL,
    telefono VARCHAR(255) NULL,
    facultad VARCHAR(255) NOT NULL DEFAULT 'FICCT',
    estado VARCHAR(50) NOT NULL DEFAULT 'Activo',
    fecha_contratacion DATE NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_docentes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_docentes_estado CHECK (estado IN ('Activo', 'Inactivo', 'Licencia'))
);

CREATE INDEX IF NOT EXISTS idx_docentes_user ON docentes(user_id);
CREATE INDEX IF NOT EXISTS idx_docentes_codigo ON docentes(codigo_docente);
CREATE INDEX IF NOT EXISTS idx_docentes_estado ON docentes(estado);

-- Tabla: titulos
CREATE TABLE IF NOT EXISTS titulos (
    id BIGSERIAL PRIMARY KEY,
    docente_id BIGINT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_titulos_docente FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_titulos_docente ON titulos(docente_id);

-- ============================================================================
-- MÓDULO: ACADÉMICO
-- ============================================================================

-- Tabla: semestres
CREATE TABLE IF NOT EXISTS semestres (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'Planificación',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_semestres_fechas CHECK (fecha_fin > fecha_inicio),
    CONSTRAINT chk_semestres_estado CHECK (estado IN ('Planificación', 'Activo', 'Finalizado'))
);

CREATE INDEX IF NOT EXISTS idx_semestres_estado ON semestres(estado);
CREATE INDEX IF NOT EXISTS idx_semestres_fechas ON semestres(fecha_inicio, fecha_fin);

-- Tabla: materias
CREATE TABLE IF NOT EXISTS materias (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    sigla VARCHAR(255) NOT NULL UNIQUE,
    nivel_semestre INTEGER NOT NULL,
    carrera VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_materias_nivel CHECK (nivel_semestre BETWEEN 1 AND 10)
);

CREATE INDEX IF NOT EXISTS idx_materias_sigla ON materias(sigla);
CREATE INDEX IF NOT EXISTS idx_materias_carrera ON materias(carrera);

-- Tabla: aulas
CREATE TABLE IF NOT EXISTS aulas (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    piso INTEGER NOT NULL,
    capacidad INTEGER NULL,
    tipo VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_aulas_piso CHECK (piso BETWEEN -2 AND 10)
);

CREATE INDEX IF NOT EXISTS idx_aulas_nombre ON aulas(nombre);

-- ============================================================================
-- MÓDULO: CARGA HORARIA
-- ============================================================================

-- Tabla: grupos
CREATE TABLE IF NOT EXISTS grupos (
    id BIGSERIAL PRIMARY KEY,
    semestre_id BIGINT NOT NULL,
    materia_id BIGINT NOT NULL,
    docente_id BIGINT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_grupos_semestre FOREIGN KEY (semestre_id) REFERENCES semestres(id),
    CONSTRAINT fk_grupos_materia FOREIGN KEY (materia_id) REFERENCES materias(id),
    CONSTRAINT fk_grupos_docente FOREIGN KEY (docente_id) REFERENCES docentes(id)
);

CREATE INDEX IF NOT EXISTS idx_grupos_semestre ON grupos(semestre_id);
CREATE INDEX IF NOT EXISTS idx_grupos_materia ON grupos(materia_id);
CREATE INDEX IF NOT EXISTS idx_grupos_docente ON grupos(docente_id);

-- Tabla: horarios
CREATE TABLE IF NOT EXISTS horarios (
    id BIGSERIAL PRIMARY KEY,
    grupo_id BIGINT NOT NULL,
    aula_id BIGINT NOT NULL,
    dia_semana SMALLINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_horarios_grupo FOREIGN KEY (grupo_id) REFERENCES grupos(id) ON DELETE CASCADE,
    CONSTRAINT fk_horarios_aula FOREIGN KEY (aula_id) REFERENCES aulas(id),
    CONSTRAINT chk_horarios_dia CHECK (dia_semana BETWEEN 1 AND 7),
    CONSTRAINT chk_horarios_horas CHECK (hora_fin > hora_inicio)
);

CREATE INDEX IF NOT EXISTS idx_horarios_grupo ON horarios(grupo_id);
CREATE INDEX IF NOT EXISTS idx_horarios_aula ON horarios(aula_id);
CREATE INDEX IF NOT EXISTS idx_horarios_dia ON horarios(dia_semana);

-- ============================================================================
-- MÓDULO: ASISTENCIAS
-- ============================================================================

-- Tabla: asistencias
CREATE TABLE IF NOT EXISTS asistencias (
    id BIGSERIAL PRIMARY KEY,
    horario_id BIGINT NOT NULL,
    docente_id BIGINT NOT NULL,
    fecha DATE NOT NULL,
    hora_registro TIME NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'Presente',
    metodo_registro VARCHAR(50) NULL,
    justificacion TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_asistencias_horario FOREIGN KEY (horario_id) REFERENCES horarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_asistencias_docente FOREIGN KEY (docente_id) REFERENCES docentes(id),
    CONSTRAINT chk_asistencias_estado CHECK (estado IN ('Presente', 'Ausente', 'Licencia', 'Tardanza'))
);

CREATE INDEX IF NOT EXISTS idx_asistencias_horario ON asistencias(horario_id);
CREATE INDEX IF NOT EXISTS idx_asistencias_docente ON asistencias(docente_id);
CREATE INDEX IF NOT EXISTS idx_asistencias_fecha ON asistencias(fecha);
CREATE INDEX IF NOT EXISTS idx_asistencias_estado ON asistencias(estado);

-- ============================================================================
-- MÓDULO: AUDITORÍA
-- ============================================================================

-- Tabla: audit_logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NULL,
    action VARCHAR(255) NOT NULL,
    model_type VARCHAR(255) NULL,
    model_id BIGINT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_logs_model ON audit_logs(model_type, model_id);
CREATE INDEX IF NOT EXISTS idx_audit_logs_created ON audit_logs(created_at);

-- ============================================================================
-- PARTE 2: FUNCIONES Y TRIGGERS
-- ============================================================================

-- Función 1: Actualizar updated_at automáticamente
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Aplicar trigger de updated_at a todas las tablas
DROP TRIGGER IF EXISTS trg_users_updated_at ON users;
CREATE TRIGGER trg_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS trg_roles_updated_at ON roles;
CREATE TRIGGER trg_roles_updated_at BEFORE UPDATE ON roles
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS trg_docentes_updated_at ON docentes;
CREATE TRIGGER trg_docentes_updated_at BEFORE UPDATE ON docentes
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS trg_grupos_updated_at ON grupos;
CREATE TRIGGER trg_grupos_updated_at BEFORE UPDATE ON grupos
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS trg_horarios_updated_at ON horarios;
CREATE TRIGGER trg_horarios_updated_at BEFORE UPDATE ON horarios
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS trg_asistencias_updated_at ON asistencias;
CREATE TRIGGER trg_asistencias_updated_at BEFORE UPDATE ON asistencias
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Función 2: Registrar auditoría automática en INSERT
CREATE OR REPLACE FUNCTION audit_log_insert()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO audit_logs (user_id, action, model_type, model_id, details, created_at)
    VALUES (
        CURRENT_USER::VARCHAR::BIGINT, -- Ajustar según el sistema de autenticación
        'create',
        TG_TABLE_NAME,
        NEW.id,
        row_to_json(NEW)::TEXT,
        CURRENT_TIMESTAMP
    );
    RETURN NEW;
EXCEPTION WHEN OTHERS THEN
    -- Ignorar errores de auditoría para no bloquear operaciones
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Función 3: Registrar auditoría automática en UPDATE
CREATE OR REPLACE FUNCTION audit_log_update()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO audit_logs (user_id, action, model_type, model_id, details, created_at)
    VALUES (
        NULL,
        'update',
        TG_TABLE_NAME,
        NEW.id,
        json_build_object('old', row_to_json(OLD), 'new', row_to_json(NEW))::TEXT,
        CURRENT_TIMESTAMP
    );
    RETURN NEW;
EXCEPTION WHEN OTHERS THEN
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Función 4: Registrar auditoría automática en DELETE
CREATE OR REPLACE FUNCTION audit_log_delete()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO audit_logs (user_id, action, model_type, model_id, details, created_at)
    VALUES (
        NULL,
        'delete',
        TG_TABLE_NAME,
        OLD.id,
        row_to_json(OLD)::TEXT,
        CURRENT_TIMESTAMP
    );
    RETURN OLD;
EXCEPTION WHEN OTHERS THEN
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

-- Función 5: Validar disponibilidad de aula antes de asignar horario
CREATE OR REPLACE FUNCTION validar_disponibilidad_aula()
RETURNS TRIGGER AS $$
DECLARE
    conflictos INTEGER;
BEGIN
    -- Verificar si ya existe otro grupo en la misma aula, día y horas solapadas
    SELECT COUNT(*) INTO conflictos
    FROM horarios h
    WHERE h.aula_id = NEW.aula_id
      AND h.dia_semana = NEW.dia_semana
      AND h.id != COALESCE(NEW.id, 0)
      AND (
          (NEW.hora_inicio >= h.hora_inicio AND NEW.hora_inicio < h.hora_fin) OR
          (NEW.hora_fin > h.hora_inicio AND NEW.hora_fin <= h.hora_fin) OR
          (NEW.hora_inicio <= h.hora_inicio AND NEW.hora_fin >= h.hora_fin)
      );

    IF conflictos > 0 THEN
        RAISE EXCEPTION 'Conflicto de horario: El aula % ya está ocupada en ese horario', NEW.aula_id;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_validar_aula ON horarios;
CREATE TRIGGER trg_validar_aula BEFORE INSERT OR UPDATE ON horarios
    FOR EACH ROW EXECUTE FUNCTION validar_disponibilidad_aula();

-- Función 6: Validar que un docente no tenga clases solapadas
CREATE OR REPLACE FUNCTION validar_horario_docente()
RETURNS TRIGGER AS $$
DECLARE
    conflictos INTEGER;
    docente_id_grupo BIGINT;
BEGIN
    -- Obtener el docente_id del grupo
    SELECT g.docente_id INTO docente_id_grupo
    FROM grupos g
    WHERE g.id = NEW.grupo_id;

    -- Verificar conflictos de horario para el mismo docente
    SELECT COUNT(*) INTO conflictos
    FROM horarios h
    JOIN grupos g ON h.grupo_id = g.id
    WHERE g.docente_id = docente_id_grupo
      AND h.dia_semana = NEW.dia_semana
      AND h.id != COALESCE(NEW.id, 0)
      AND (
          (NEW.hora_inicio >= h.hora_inicio AND NEW.hora_inicio < h.hora_fin) OR
          (NEW.hora_fin > h.hora_inicio AND NEW.hora_fin <= h.hora_fin) OR
          (NEW.hora_inicio <= h.hora_inicio AND NEW.hora_fin >= h.hora_fin)
      );

    IF conflictos > 0 THEN
        RAISE EXCEPTION 'Conflicto: El docente ya tiene otra clase en ese horario';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_validar_horario_docente ON horarios;
CREATE TRIGGER trg_validar_horario_docente BEFORE INSERT OR UPDATE ON horarios
    FOR EACH ROW EXECUTE FUNCTION validar_horario_docente();

-- Función 7: Prevenir eliminación de roles del sistema
CREATE OR REPLACE FUNCTION proteger_roles_sistema()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.name IN ('admin', 'docente') THEN
        RAISE EXCEPTION 'No se puede eliminar el rol del sistema: %', OLD.name;
    END IF;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_proteger_roles ON roles;
CREATE TRIGGER trg_proteger_roles BEFORE DELETE ON roles
    FOR EACH ROW EXECUTE FUNCTION proteger_roles_sistema();

-- Función 8: Calcular horas semanales de un docente
CREATE OR REPLACE FUNCTION calcular_horas_semanales(p_docente_id BIGINT, p_semestre_id BIGINT)
RETURNS NUMERIC AS $$
DECLARE
    total_horas NUMERIC;
BEGIN
    SELECT COALESCE(SUM(EXTRACT(EPOCH FROM (h.hora_fin - h.hora_inicio)) / 3600), 0)
    INTO total_horas
    FROM horarios h
    JOIN grupos g ON h.grupo_id = g.id
    WHERE g.docente_id = p_docente_id
      AND g.semestre_id = p_semestre_id;

    RETURN total_horas;
END;
$$ LANGUAGE plpgsql;

-- Función 9: Obtener porcentaje de asistencia de un docente
CREATE OR REPLACE FUNCTION calcular_porcentaje_asistencia(p_docente_id BIGINT, p_fecha_inicio DATE, p_fecha_fin DATE)
RETURNS NUMERIC AS $$
DECLARE
    total_registros INTEGER;
    total_presentes INTEGER;
    porcentaje NUMERIC;
BEGIN
    SELECT
        COUNT(*),
        COUNT(CASE WHEN estado = 'Presente' THEN 1 END)
    INTO total_registros, total_presentes
    FROM asistencias
    WHERE docente_id = p_docente_id
      AND fecha BETWEEN p_fecha_inicio AND p_fecha_fin;

    IF total_registros = 0 THEN
        RETURN 0;
    END IF;

    porcentaje := ROUND((total_presentes::NUMERIC / total_registros) * 100, 2);
    RETURN porcentaje;
END;
$$ LANGUAGE plpgsql;

-- Función 10: Obtener días de la semana de un grupo
CREATE OR REPLACE FUNCTION obtener_dias_semana_grupo(p_grupo_id BIGINT)
RETURNS TEXT AS $$
DECLARE
    dias TEXT;
BEGIN
    SELECT STRING_AGG(
        CASE dia_semana
            WHEN 1 THEN 'Lunes'
            WHEN 2 THEN 'Martes'
            WHEN 3 THEN 'Miércoles'
            WHEN 4 THEN 'Jueves'
            WHEN 5 THEN 'Viernes'
            WHEN 6 THEN 'Sábado'
            WHEN 7 THEN 'Domingo'
        END,
        ', '
        ORDER BY dia_semana
    )
    INTO dias
    FROM horarios
    WHERE grupo_id = p_grupo_id;

    RETURN COALESCE(dias, 'Sin horarios');
END;
$$ LANGUAGE plpgsql;

-- ============================================================================
-- PARTE 3: PROCEDIMIENTOS ALMACENADOS (STORED PROCEDURES)
-- ============================================================================

-- Procedimiento 1: Crear semestre completo con validaciones
CREATE OR REPLACE PROCEDURE crear_semestre(
    p_nombre VARCHAR,
    p_fecha_inicio DATE,
    p_fecha_fin DATE
)
LANGUAGE plpgsql AS $$
BEGIN
    -- Validar fechas
    IF p_fecha_fin <= p_fecha_inicio THEN
        RAISE EXCEPTION 'La fecha de fin debe ser posterior a la fecha de inicio';
    END IF;

    -- Validar que no exista solapamiento con otros semestres
    IF EXISTS (
        SELECT 1 FROM semestres
        WHERE (p_fecha_inicio BETWEEN fecha_inicio AND fecha_fin)
           OR (p_fecha_fin BETWEEN fecha_inicio AND fecha_fin)
    ) THEN
        RAISE EXCEPTION 'El semestre se solapa con otro semestre existente';
    END IF;

    -- Insertar semestre
    INSERT INTO semestres (nombre, fecha_inicio, fecha_fin, estado)
    VALUES (p_nombre, p_fecha_inicio, p_fecha_fin, 'Planificación');

    RAISE NOTICE 'Semestre % creado exitosamente', p_nombre;
END;
$$;

-- Procedimiento 2: Asignar carga horaria completa a un docente
CREATE OR REPLACE PROCEDURE asignar_carga_horaria(
    p_docente_id BIGINT,
    p_semestre_id BIGINT,
    p_materia_id BIGINT,
    p_nombre_grupo VARCHAR,
    p_horarios JSON -- [{"dia": 1, "hora_inicio": "08:00", "hora_fin": "10:00", "aula_id": 1}]
)
LANGUAGE plpgsql AS $$
DECLARE
    v_grupo_id BIGINT;
    horario RECORD;
BEGIN
    -- Crear grupo
    INSERT INTO grupos (semestre_id, materia_id, docente_id, nombre)
    VALUES (p_semestre_id, p_materia_id, p_docente_id, p_nombre_grupo)
    RETURNING id INTO v_grupo_id;

    -- Insertar horarios
    FOR horario IN SELECT * FROM json_array_elements(p_horarios)
    LOOP
        INSERT INTO horarios (grupo_id, aula_id, dia_semana, hora_inicio, hora_fin)
        VALUES (
            v_grupo_id,
            (horario.value->>'aula_id')::BIGINT,
            (horario.value->>'dia')::SMALLINT,
            (horario.value->>'hora_inicio')::TIME,
            (horario.value->>'hora_fin')::TIME
        );
    END LOOP;

    RAISE NOTICE 'Carga horaria asignada exitosamente. Grupo ID: %', v_grupo_id;
END;
$$;

-- Procedimiento 3: Generar reporte de asistencias por período
CREATE OR REPLACE PROCEDURE generar_reporte_asistencias(
    p_fecha_inicio DATE,
    p_fecha_fin DATE,
    OUT resultado REFCURSOR
)
LANGUAGE plpgsql AS $$
BEGIN
    OPEN resultado FOR
    SELECT
        d.codigo_docente,
        u.name AS docente_nombre,
        COUNT(*) AS total_registros,
        COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) AS presentes,
        COUNT(CASE WHEN a.estado = 'Ausente' THEN 1 END) AS ausentes,
        COUNT(CASE WHEN a.estado = 'Licencia' THEN 1 END) AS licencias,
        COUNT(CASE WHEN a.estado = 'Tardanza' THEN 1 END) AS tardanzas,
        ROUND(
            (COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END)::NUMERIC /
             NULLIF(COUNT(*), 0)) * 100,
            2
        ) AS porcentaje_asistencia
    FROM docentes d
    JOIN users u ON d.user_id = u.id
    LEFT JOIN asistencias a ON d.id = a.docente_id
        AND a.fecha BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY d.id, d.codigo_docente, u.name
    ORDER BY porcentaje_asistencia DESC;
END;
$$;

-- Procedimiento 4: Limpiar registros antiguos de auditoría
CREATE OR REPLACE PROCEDURE limpiar_audit_logs(p_dias_antiguedad INTEGER)
LANGUAGE plpgsql AS $$
DECLARE
    registros_eliminados INTEGER;
BEGIN
    DELETE FROM audit_logs
    WHERE created_at < (CURRENT_DATE - p_dias_antiguedad);

    GET DIAGNOSTICS registros_eliminados = ROW_COUNT;

    RAISE NOTICE 'Se eliminaron % registros de auditoría', registros_eliminados;
END;
$$;

-- Procedimiento 5: Activar semestre (desactivar otros)
CREATE OR REPLACE PROCEDURE activar_semestre(p_semestre_id BIGINT)
LANGUAGE plpgsql AS $$
BEGIN
    -- Desactivar todos los semestres
    UPDATE semestres SET estado = 'Finalizado' WHERE estado = 'Activo';

    -- Activar el semestre especificado
    UPDATE semestres SET estado = 'Activo' WHERE id = p_semestre_id;

    RAISE NOTICE 'Semestre % activado correctamente', p_semestre_id;
END;
$$;

-- ============================================================================
-- PARTE 4: VISTAS (VIEWS) ÚTILES
-- ============================================================================

-- Vista 1: Carga horaria de docentes
CREATE OR REPLACE VIEW vista_carga_horaria_docentes AS
SELECT
    d.id AS docente_id,
    d.codigo_docente,
    u.name AS docente_nombre,
    s.nombre AS semestre,
    m.nombre AS materia,
    m.sigla,
    g.nombre AS grupo,
    COUNT(h.id) AS total_bloques_horarios,
    SUM(EXTRACT(EPOCH FROM (h.hora_fin - h.hora_inicio)) / 3600) AS horas_semanales
FROM docentes d
JOIN users u ON d.user_id = u.id
JOIN grupos g ON d.id = g.docente_id
JOIN semestres s ON g.semestre_id = s.id
JOIN materias m ON g.materia_id = m.id
LEFT JOIN horarios h ON g.id = h.grupo_id
GROUP BY d.id, d.codigo_docente, u.name, s.nombre, m.nombre, m.sigla, g.nombre;

-- Vista 2: Resumen de asistencias por docente
CREATE OR REPLACE VIEW vista_resumen_asistencias AS
SELECT
    d.id AS docente_id,
    d.codigo_docente,
    u.name AS docente_nombre,
    COUNT(*) AS total_registros,
    COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) AS presentes,
    COUNT(CASE WHEN a.estado = 'Ausente' THEN 1 END) AS ausentes,
    COUNT(CASE WHEN a.estado = 'Licencia' THEN 1 END) AS licencias,
    COUNT(CASE WHEN a.estado = 'Tardanza' THEN 1 END) AS tardanzas,
    ROUND(
        (COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END)::NUMERIC /
         NULLIF(COUNT(*), 0)) * 100,
        2
    ) AS porcentaje_asistencia
FROM docentes d
JOIN users u ON d.user_id = u.id
LEFT JOIN asistencias a ON d.id = a.docente_id
GROUP BY d.id, d.codigo_docente, u.name;

-- Vista 3: Horarios disponibles por aula
CREATE OR REPLACE VIEW vista_disponibilidad_aulas AS
SELECT
    a.id AS aula_id,
    a.nombre AS aula,
    a.piso,
    a.capacidad,
    h.dia_semana,
    CASE h.dia_semana
        WHEN 1 THEN 'Lunes'
        WHEN 2 THEN 'Martes'
        WHEN 3 THEN 'Miércoles'
        WHEN 4 THEN 'Jueves'
        WHEN 5 THEN 'Viernes'
        WHEN 6 THEN 'Sábado'
        WHEN 7 THEN 'Domingo'
    END AS dia_nombre,
    h.hora_inicio,
    h.hora_fin,
    m.sigla AS materia,
    g.nombre AS grupo,
    u.name AS docente
FROM aulas a
LEFT JOIN horarios h ON a.id = h.aula_id
LEFT JOIN grupos g ON h.grupo_id = g.id
LEFT JOIN materias m ON g.materia_id = m.id
LEFT JOIN docentes d ON g.docente_id = d.id
LEFT JOIN users u ON d.user_id = u.id
ORDER BY a.nombre, h.dia_semana, h.hora_inicio;

-- Vista 4: Grupos activos con información completa
CREATE OR REPLACE VIEW vista_grupos_completos AS
SELECT
    g.id AS grupo_id,
    g.nombre AS grupo,
    s.nombre AS semestre,
    s.estado AS estado_semestre,
    m.nombre AS materia,
    m.sigla AS materia_sigla,
    m.carrera,
    d.codigo_docente,
    u.name AS docente_nombre,
    COUNT(DISTINCT h.id) AS cantidad_horarios,
    STRING_AGG(DISTINCT a.nombre, ', ' ORDER BY a.nombre) AS aulas
FROM grupos g
JOIN semestres s ON g.semestre_id = s.id
JOIN materias m ON g.materia_id = m.id
JOIN docentes d ON g.docente_id = d.id
JOIN users u ON d.user_id = u.id
LEFT JOIN horarios h ON g.id = h.grupo_id
LEFT JOIN aulas a ON h.aula_id = a.id
GROUP BY g.id, g.nombre, s.nombre, s.estado, m.nombre, m.sigla, m.carrera, d.codigo_docente, u.name;

-- Vista 5: Estadísticas generales del sistema
CREATE OR REPLACE VIEW vista_estadisticas_sistema AS
SELECT
    (SELECT COUNT(*) FROM users) AS total_usuarios,
    (SELECT COUNT(*) FROM docentes WHERE estado = 'Activo') AS docentes_activos,
    (SELECT COUNT(*) FROM semestres WHERE estado = 'Activo') AS semestres_activos,
    (SELECT COUNT(*) FROM materias) AS total_materias,
    (SELECT COUNT(*) FROM grupos) AS total_grupos,
    (SELECT COUNT(*) FROM horarios) AS total_horarios,
    (SELECT COUNT(*) FROM asistencias) AS total_asistencias,
    (SELECT COUNT(*) FROM aulas) AS total_aulas;

-- ============================================================================
-- PARTE 5: 30 CONSULTAS SQL ÚTILES
-- ============================================================================

-- ===========================================
-- CATEGORÍA 1: CONSULTAS SIMPLES (SELECT básicas)
-- ===========================================

-- Consulta 1: Listar todos los docentes activos
SELECT
    d.codigo_docente,
    u.name AS nombre,
    d.telefono,
    d.facultad,
    d.fecha_contratacion
FROM docentes d
JOIN users u ON d.user_id = u.id
WHERE d.estado = 'Activo'
ORDER BY u.name;

-- Consulta 2: Listar materias por carrera
SELECT
    carrera,
    sigla,
    nombre,
    nivel_semestre
FROM materias
WHERE carrera = 'Ingeniería de Sistemas'
ORDER BY nivel_semestre, nombre;

-- Consulta 3: Listar aulas disponibles por piso
SELECT
    nombre,
    piso,
    capacidad,
    tipo
FROM aulas
WHERE piso = 2
ORDER BY nombre;

-- Consulta 4: Listar semestres ordenados por fecha
SELECT
    nombre,
    fecha_inicio,
    fecha_fin,
    estado,
    (fecha_fin - fecha_inicio) AS duracion_dias
FROM semestres
ORDER BY fecha_inicio DESC;

-- Consulta 5: Listar roles con su cantidad de usuarios
SELECT
    r.name AS rol,
    r.description,
    r.level,
    r.status,
    COUNT(ru.user_id) AS cantidad_usuarios
FROM roles r
LEFT JOIN role_user ru ON r.id = ru.role_id
GROUP BY r.id, r.name, r.description, r.level, r.status
ORDER BY r.level DESC;

-- ===========================================
-- CATEGORÍA 2: CONSULTAS CON JOINS MÚLTIPLES
-- ===========================================

-- Consulta 6: Obtener carga horaria completa de un docente específico
SELECT
    u.name AS docente,
    s.nombre AS semestre,
    m.nombre AS materia,
    m.sigla,
    g.nombre AS grupo,
    a.nombre AS aula,
    CASE h.dia_semana
        WHEN 1 THEN 'Lunes'
        WHEN 2 THEN 'Martes'
        WHEN 3 THEN 'Miércoles'
        WHEN 4 THEN 'Jueves'
        WHEN 5 THEN 'Viernes'
        WHEN 6 THEN 'Sábado'
        WHEN 7 THEN 'Domingo'
    END AS dia,
    h.hora_inicio,
    h.hora_fin
FROM docentes d
JOIN users u ON d.user_id = u.id
JOIN grupos g ON d.id = g.docente_id
JOIN semestres s ON g.semestre_id = s.id
JOIN materias m ON g.materia_id = m.id
JOIN horarios h ON g.id = h.grupo_id
JOIN aulas a ON h.aula_id = a.id
WHERE d.codigo_docente = 'DOC-001'
ORDER BY h.dia_semana, h.hora_inicio;

-- Consulta 7: Horarios de un aula específica en la semana
SELECT
    CASE h.dia_semana
        WHEN 1 THEN 'Lunes'
        WHEN 2 THEN 'Martes'
        WHEN 3 THEN 'Miércoles'
        WHEN 4 THEN 'Jueves'
        WHEN 5 THEN 'Viernes'
    END AS dia,
    h.hora_inicio,
    h.hora_fin,
    m.sigla AS materia,
    g.nombre AS grupo,
    u.name AS docente
FROM horarios h
JOIN aulas a ON h.aula_id = a.id
JOIN grupos g ON h.grupo_id = g.id
JOIN materias m ON g.materia_id = m.id
JOIN docentes d ON g.docente_id = d.id
JOIN users u ON d.user_id = u.id
WHERE a.nombre = 'LAB-01'
ORDER BY h.dia_semana, h.hora_inicio;

-- Consulta 8: Docentes con sus títulos académicos
SELECT
    u.name AS docente,
    d.codigo_docente,
    STRING_AGG(t.nombre, ', ' ORDER BY t.nombre) AS titulos
FROM docentes d
JOIN users u ON d.user_id = u.id
LEFT JOIN titulos t ON d.id = t.docente_id
GROUP BY u.name, d.codigo_docente
ORDER BY u.name;

-- Consulta 9: Asistencias de un docente en un rango de fechas
SELECT
    a.fecha,
    CASE h.dia_semana
        WHEN 1 THEN 'Lunes'
        WHEN 2 THEN 'Martes'
        WHEN 3 THEN 'Miércoles'
        WHEN 4 THEN 'Jueves'
        WHEN 5 THEN 'Viernes'
    END AS dia,
    m.nombre AS materia,
    g.nombre AS grupo,
    a.hora_registro,
    a.estado,
    a.metodo_registro
FROM asistencias a
JOIN horarios h ON a.horario_id = h.id
JOIN grupos g ON h.grupo_id = g.id
JOIN materias m ON g.materia_id = m.id
JOIN docentes d ON a.docente_id = d.id
WHERE d.codigo_docente = 'DOC-001'
  AND a.fecha BETWEEN '2025-01-01' AND '2025-12-31'
ORDER BY a.fecha DESC;

-- Consulta 10: Grupos por semestre activo
SELECT
    s.nombre AS semestre,
    m.nombre AS materia,
    m.sigla,
    g.nombre AS grupo,
    u.name AS docente,
    COUNT(h.id) AS bloques_horarios
FROM grupos g
JOIN semestres s ON g.semestre_id = s.id
JOIN materias m ON g.materia_id = m.id
JOIN docentes d ON g.docente_id = d.id
JOIN users u ON d.user_id = u.id
LEFT JOIN horarios h ON g.id = h.grupo_id
WHERE s.estado = 'Activo'
GROUP BY s.nombre, m.nombre, m.sigla, g.nombre, u.name
ORDER BY m.nombre, g.nombre;

-- ===========================================
-- CATEGORÍA 3: SUBCONSULTAS
-- ===========================================

-- Consulta 11: Docentes con más carga horaria que el promedio
SELECT
    u.name AS docente,
    d.codigo_docente,
    COUNT(h.id) AS total_horarios
FROM docentes d
JOIN users u ON d.user_id = u.id
JOIN grupos g ON d.id = g.docente_id
JOIN horarios h ON g.id = h.grupo_id
GROUP BY u.name, d.codigo_docente
HAVING COUNT(h.id) > (
    SELECT AVG(horario_count)
    FROM (
        SELECT COUNT(h2.id) AS horario_count
        FROM docentes d2
        JOIN grupos g2 ON d2.id = g2.docente_id
        JOIN horarios h2 ON g2.id = h2.grupo_id
        GROUP BY d2.id
    ) AS subq
)
ORDER BY total_horarios DESC;

-- Consulta 12: Materias sin grupos asignados en semestre activo
SELECT
    m.sigla,
    m.nombre,
    m.carrera,
    m.nivel_semestre
FROM materias m
WHERE NOT EXISTS (
    SELECT 1
    FROM grupos g
    JOIN semestres s ON g.semestre_id = s.id
    WHERE g.materia_id = m.id
      AND s.estado = 'Activo'
)
ORDER BY m.carrera, m.nivel_semestre;

-- Consulta 13: Aulas con mayor ocupación
SELECT
    a.nombre AS aula,
    a.piso,
    (SELECT COUNT(*)
     FROM horarios h
     WHERE h.aula_id = a.id) AS bloques_ocupados,
    a.capacidad
FROM aulas a
ORDER BY bloques_ocupados DESC
LIMIT 10;

-- Consulta 14: Docentes que nunca han faltado
SELECT
    u.name AS docente,
    d.codigo_docente
FROM docentes d
JOIN users u ON d.user_id = u.id
WHERE NOT EXISTS (
    SELECT 1
    FROM asistencias a
    WHERE a.docente_id = d.id
      AND a.estado IN ('Ausente', 'Licencia')
)
  AND EXISTS (
      SELECT 1
      FROM asistencias a2
      WHERE a2.docente_id = d.id
  )
ORDER BY u.name;

-- Consulta 15: Horarios con conflictos potenciales (mismo aula, día y hora)
SELECT
    a.nombre AS aula,
    h1.dia_semana,
    h1.hora_inicio,
    h1.hora_fin,
    m1.sigla AS materia1,
    m2.sigla AS materia2
FROM horarios h1
JOIN horarios h2 ON h1.aula_id = h2.aula_id
    AND h1.dia_semana = h2.dia_semana
    AND h1.id < h2.id
JOIN aulas a ON h1.aula_id = a.id
JOIN grupos g1 ON h1.grupo_id = g1.id
JOIN grupos g2 ON h2.grupo_id = g2.id
JOIN materias m1 ON g1.materia_id = m1.id
JOIN materias m2 ON g2.materia_id = m2.id
WHERE (h1.hora_inicio, h1.hora_fin) OVERLAPS (h2.hora_inicio, h2.hora_fin);

-- ===========================================
-- CATEGORÍA 4: CONSULTAS DE AGREGACIÓN Y ESTADÍSTICAS
-- ===========================================

-- Consulta 16: Estadísticas de asistencias por mes
SELECT
    TO_CHAR(a.fecha, 'YYYY-MM') AS mes,
    COUNT(*) AS total_registros,
    COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) AS presentes,
    COUNT(CASE WHEN a.estado = 'Ausente' THEN 1 END) AS ausentes,
    COUNT(CASE WHEN a.estado = 'Licencia' THEN 1 END) AS licencias,
    ROUND(
        (COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END)::NUMERIC /
         NULLIF(COUNT(*), 0)) * 100,
        2
    ) AS porcentaje_asistencia
FROM asistencias a
GROUP BY mes
ORDER BY mes DESC;

-- Consulta 17: Top 10 docentes con mejor asistencia
SELECT
    u.name AS docente,
    COUNT(*) AS total_registros,
    COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) AS presentes,
    ROUND(
        (COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END)::NUMERIC /
         NULLIF(COUNT(*), 0)) * 100,
        2
    ) AS porcentaje
FROM asistencias a
JOIN docentes d ON a.docente_id = d.id
JOIN users u ON d.user_id = u.id
GROUP BY u.name
HAVING COUNT(*) >= 10
ORDER BY porcentaje DESC
LIMIT 10;

-- Consulta 18: Distribución de grupos por carrera
SELECT
    m.carrera,
    COUNT(DISTINCT g.id) AS total_grupos,
    COUNT(DISTINCT g.docente_id) AS docentes_distintos,
    COUNT(DISTINCT m.id) AS materias_distintas
FROM grupos g
JOIN materias m ON g.materia_id = m.id
JOIN semestres s ON g.semestre_id = s.id
WHERE s.estado = 'Activo'
GROUP BY m.carrera
ORDER BY total_grupos DESC;

-- Consulta 19: Promedio de horas semanales por docente
SELECT
    AVG(horas_semanales) AS promedio_horas,
    MIN(horas_semanales) AS minimo_horas,
    MAX(horas_semanales) AS maximo_horas,
    STDDEV(horas_semanales) AS desviacion_estandar
FROM (
    SELECT
        d.id,
        SUM(EXTRACT(EPOCH FROM (h.hora_fin - h.hora_inicio)) / 3600) AS horas_semanales
    FROM docentes d
    JOIN grupos g ON d.id = g.docente_id
    JOIN horarios h ON g.id = h.grupo_id
    GROUP BY d.id
) AS subq;

-- Consulta 20: Ocupación de aulas por piso
SELECT
    a.piso,
    COUNT(DISTINCT a.id) AS total_aulas,
    COUNT(h.id) AS bloques_ocupados,
    ROUND(
        (COUNT(h.id)::NUMERIC / NULLIF(COUNT(DISTINCT a.id), 0)),
        2
    ) AS promedio_bloques_por_aula
FROM aulas a
LEFT JOIN horarios h ON a.id = h.aula_id
GROUP BY a.piso
ORDER BY a.piso;

-- ===========================================
-- CATEGORÍA 5: CONSULTAS CON WINDOW FUNCTIONS
-- ===========================================

-- Consulta 21: Ranking de docentes por cantidad de grupos
SELECT
    u.name AS docente,
    COUNT(g.id) AS total_grupos,
    RANK() OVER (ORDER BY COUNT(g.id) DESC) AS ranking
FROM docentes d
JOIN users u ON d.user_id = u.id
LEFT JOIN grupos g ON d.id = g.docente_id
GROUP BY u.name
ORDER BY ranking;

-- Consulta 22: Asistencias con registro previo y siguiente
SELECT
    d.codigo_docente AS docente,
    a.fecha,
    a.estado,
    LAG(a.estado) OVER (PARTITION BY a.docente_id ORDER BY a.fecha) AS estado_anterior,
    LEAD(a.estado) OVER (PARTITION BY a.docente_id ORDER BY a.fecha) AS estado_siguiente
FROM asistencias a
JOIN docentes d ON a.docente_id = d.id
ORDER BY a.docente_id, a.fecha;

-- Consulta 23: Suma acumulada de asistencias por docente
SELECT
    u.name AS docente,
    a.fecha,
    a.estado,
    COUNT(*) OVER (
        PARTITION BY a.docente_id
        ORDER BY a.fecha
        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
    ) AS total_acumulado
FROM asistencias a
JOIN docentes d ON a.docente_id = d.id
JOIN users u ON d.user_id = u.id
ORDER BY u.name, a.fecha;

-- ===========================================
-- CATEGORÍA 6: CONSULTAS CON CTE (Common Table Expressions)
-- ===========================================

-- Consulta 24: Carga horaria jerárquica por facultad
WITH carga_docentes AS (
    SELECT
        d.id,
        d.facultad,
        COUNT(h.id) AS total_bloques
    FROM docentes d
    JOIN grupos g ON d.id = g.docente_id
    JOIN horarios h ON g.id = h.grupo_id
    GROUP BY d.id, d.facultad
)
SELECT
    facultad,
    COUNT(*) AS total_docentes,
    SUM(total_bloques) AS bloques_totales,
    ROUND(AVG(total_bloques), 2) AS promedio_bloques
FROM carga_docentes
GROUP BY facultad
ORDER BY bloques_totales DESC;

-- Consulta 25: Análisis de asistencias recursivo (últimos 7 días)
WITH RECURSIVE ultimos_dias AS (
    SELECT CURRENT_DATE AS fecha
    UNION ALL
    SELECT fecha - INTERVAL '1 day'
    FROM ultimos_dias
    WHERE fecha > CURRENT_DATE - INTERVAL '7 days'
)
SELECT
    ud.fecha,
    COUNT(a.id) AS total_asistencias,
    COUNT(CASE WHEN a.estado = 'Presente' THEN 1 END) AS presentes,
    COUNT(CASE WHEN a.estado = 'Ausente' THEN 1 END) AS ausentes
FROM ultimos_dias ud
LEFT JOIN asistencias a ON a.fecha = ud.fecha
GROUP BY ud.fecha
ORDER BY ud.fecha DESC;

-- ===========================================
-- CATEGORÍA 7: CONSULTAS DE AUDITORÍA Y LOGS
-- ===========================================

-- Consulta 26: Actividad reciente del sistema (últimas 50 acciones)
SELECT
    u.name AS usuario,
    al.action AS accion,
    al.model_type AS modelo,
    al.model_id,
    al.created_at AS fecha_hora,
    al.ip_address
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.id
ORDER BY al.created_at DESC
LIMIT 50;

-- Consulta 27: Historial de cambios en un docente específico
SELECT
    u.name AS usuario_responsable,
    al.action AS accion,
    al.details,
    al.created_at AS fecha_hora
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.id
WHERE al.model_type = 'Docente'
  AND al.model_id = 1
ORDER BY al.created_at DESC;

-- Consulta 28: Usuarios más activos en el sistema
SELECT
    u.name AS usuario,
    u.email,
    COUNT(al.id) AS total_acciones,
    MAX(al.created_at) AS ultima_actividad
FROM users u
LEFT JOIN audit_logs al ON u.id = al.user_id
GROUP BY u.id, u.name, u.email
ORDER BY total_acciones DESC
LIMIT 20;

-- ===========================================
-- CATEGORÍA 8: CONSULTAS DE VALIDACIÓN Y CONTROL
-- ===========================================

-- Consulta 29: Verificar integridad de horarios (sin conflictos)
SELECT
    'Horarios sin conflictos' AS verificacion,
    COUNT(*) AS total
FROM horarios h1
WHERE NOT EXISTS (
    SELECT 1
    FROM horarios h2
    WHERE h1.id < h2.id
      AND h1.aula_id = h2.aula_id
      AND h1.dia_semana = h2.dia_semana
      AND (h1.hora_inicio, h1.hora_fin) OVERLAPS (h2.hora_inicio, h2.hora_fin)
);

-- Consulta 30: Resumen completo del sistema
SELECT
    'Usuarios' AS entidad,
    (SELECT COUNT(*) FROM users) AS total,
    (SELECT COUNT(*) FROM users WHERE created_at > CURRENT_DATE - INTERVAL '30 days') AS ultimos_30_dias
UNION ALL
SELECT
    'Docentes Activos',
    (SELECT COUNT(*) FROM docentes WHERE estado = 'Activo'),
    (SELECT COUNT(*) FROM docentes WHERE estado = 'Activo' AND created_at > CURRENT_DATE - INTERVAL '30 days')
UNION ALL
SELECT
    'Grupos',
    (SELECT COUNT(*) FROM grupos),
    (SELECT COUNT(*) FROM grupos WHERE created_at > CURRENT_DATE - INTERVAL '30 days')
UNION ALL
SELECT
    'Horarios',
    (SELECT COUNT(*) FROM horarios),
    (SELECT COUNT(*) FROM horarios WHERE created_at > CURRENT_DATE - INTERVAL '30 days')
UNION ALL
SELECT
    'Asistencias',
    (SELECT COUNT(*) FROM asistencias),
    (SELECT COUNT(*) FROM asistencias WHERE fecha > CURRENT_DATE - INTERVAL '30 days');

-- ============================================================================
-- PARTE 6: DATOS INICIALES (SEEDERS)
-- ============================================================================

-- Insertar roles iniciales
INSERT INTO roles (name, description, level, status) VALUES
('admin', 'Administrador del sistema con acceso total', 100, 'Activo'),
('coordinador', 'Coordinador académico', 75, 'Activo'),
('docente', 'Docente de la facultad', 50, 'Activo'),
('secretaria', 'Secretaria administrativa', 25, 'Activo')
ON CONFLICT (name) DO NOTHING;

-- Insertar permisos iniciales
INSERT INTO permissions (name, description, module) VALUES
-- Módulo Usuarios
('usuarios.ver', 'Ver lista de usuarios', 'Usuarios'),
('usuarios.crear', 'Crear nuevos usuarios', 'Usuarios'),
('usuarios.editar', 'Editar usuarios existentes', 'Usuarios'),
('usuarios.eliminar', 'Eliminar usuarios', 'Usuarios'),
-- Módulo Roles
('roles.ver', 'Ver lista de roles', 'Roles'),
('roles.crear', 'Crear nuevos roles', 'Roles'),
('roles.editar', 'Editar roles existentes', 'Roles'),
('roles.eliminar', 'Eliminar roles', 'Roles'),
-- Módulo Docentes
('docentes.ver', 'Ver lista de docentes', 'Docentes'),
('docentes.crear', 'Crear nuevos docentes', 'Docentes'),
('docentes.editar', 'Editar docentes existentes', 'Docentes'),
('docentes.eliminar', 'Eliminar docentes', 'Docentes'),
-- Módulo Asistencias
('asistencias.ver', 'Ver asistencias', 'Asistencias'),
('asistencias.crear', 'Registrar asistencias', 'Asistencias'),
('asistencias.editar', 'Editar asistencias', 'Asistencias'),
('asistencias.exportar', 'Exportar reportes de asistencias', 'Asistencias'),
-- Módulo Dashboard
('dashboard.ver', 'Ver dashboard y estadísticas', 'Dashboard')
ON CONFLICT (name) DO NOTHING;

-- ============================================================================
-- PARTE 7: INSTRUCCIONES DE USO
-- ============================================================================

/*
INSTRUCCIONES PARA EJECUTAR ESTE SCRIPT:

1. Crear la base de datos:
   createdb sistema_horarios_ficct

2. Conectarse a la base de datos:
   psql -d sistema_horarios_ficct

3. Ejecutar este script:
   \i /ruta/a/SCRIPT_COMPLETO_BD.sql

4. Verificar las tablas creadas:
   \dt

5. Probar las funciones:
   SELECT calcular_horas_semanales(1, 1);
   SELECT calcular_porcentaje_asistencia(1, '2025-01-01', '2025-12-31');

6. Ejecutar procedimientos:
   CALL crear_semestre('Gestión 1-2025', '2025-03-01', '2025-07-31');
   CALL activar_semestre(1);

7. Consultar vistas:
   SELECT * FROM vista_carga_horaria_docentes;
   SELECT * FROM vista_resumen_asistencias;

8. Limpiar auditoría (opcional):
   CALL limpiar_audit_logs(365);

NOTA: Este script está optimizado para PostgreSQL 14+
*/

-- ============================================================================
-- FIN DEL SCRIPT
-- ============================================================================

-- Mensaje de confirmación
DO $$
BEGIN
    RAISE NOTICE '============================================================';
    RAISE NOTICE 'Script ejecutado exitosamente';
    RAISE NOTICE 'Base de datos: Sistema de Horarios FICCT';
    RAISE NOTICE 'Tablas creadas: %', (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public');
    RAISE NOTICE 'Funciones creadas: %', (SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema = 'public' AND routine_type = 'FUNCTION');
    RAISE NOTICE 'Procedimientos creados: %', (SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema = 'public' AND routine_type = 'PROCEDURE');
    RAISE NOTICE 'Vistas creadas: %', (SELECT COUNT(*) FROM information_schema.views WHERE table_schema = 'public');
    RAISE NOTICE '============================================================';
END $$;
