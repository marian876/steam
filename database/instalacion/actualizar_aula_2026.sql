-- Aula STEAM: actualizar una base MySQL/MariaDB existente.
-- IMPORTANTE: seleccionar primero la base correcta y hacer una copia de seguridad.
-- Requiere la tabla users con id BIGINT UNSIGNED; no elimina datos existentes.
-- Si una tabla ya existe, no se modifica su estructura: comprobar compatibilidad.

SET @aula_sql = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'apellido') = 0,
  'ALTER TABLE users ADD COLUMN apellido VARCHAR(120) NOT NULL DEFAULT ''''', 'SELECT 1');
PREPARE aula_stmt FROM @aula_sql; EXECUTE aula_stmt; DEALLOCATE PREPARE aula_stmt;

SET @aula_sql = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'activo') = 0,
  'ALTER TABLE users ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE aula_stmt FROM @aula_sql; EXECUTE aula_stmt; DEALLOCATE PREPARE aula_stmt;

SET @aula_sql = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'curso') = 0,
  'ALTER TABLE users ADD COLUMN curso VARCHAR(40) NULL', 'SELECT 1');
PREPARE aula_stmt FROM @aula_sql; EXECUTE aula_stmt; DEALLOCATE PREPARE aula_stmt;

CREATE TABLE IF NOT EXISTS grupos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  curso VARCHAR(255) NOT NULL, seccion VARCHAR(255) NOT NULL,
  especialidad VARCHAR(255) NOT NULL, numero INT UNSIGNED NOT NULL,
  anio INT UNSIGNED NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE KEY grupos_curso_seccion_especialidad_numero_anio_unique (curso, seccion, especialidad, numero, anio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS grupo_user (
  grupo_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  PRIMARY KEY (grupo_id, user_id),
  CONSTRAINT grupo_user_grupo_id_foreign FOREIGN KEY (grupo_id) REFERENCES grupos(id),
  CONSTRAINT grupo_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aula_integrantes_autorizados (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL, grupo_id BIGINT UNSIGNED NOT NULL,
  autorizado_por BIGINT UNSIGNED NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE KEY aula_integrantes_autorizados_user_id_grupo_id_unique (user_id, grupo_id),
  CONSTRAINT aula_integrantes_autorizados_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT aula_integrantes_autorizados_autorizado_por_foreign FOREIGN KEY (autorizado_por) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aula_tareas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  grupo_id BIGINT UNSIGNED NOT NULL, docente_id BIGINT UNSIGNED NOT NULL,
  titulo VARCHAR(180) NOT NULL, materia VARCHAR(100) NOT NULL, consigna TEXT NOT NULL,
  vence_en DATETIME NULL, permite_nueva_entrega TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  KEY aula_tareas_grupo_id_index (grupo_id),
  CONSTRAINT aula_tareas_docente_id_foreign FOREIGN KEY (docente_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aula_entregas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  tarea_id BIGINT UNSIGNED NOT NULL, alumno_id BIGINT UNSIGNED NOT NULL,
  archivo VARCHAR(255) NOT NULL, nombre_original VARCHAR(255) NOT NULL, comentario TEXT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  CONSTRAINT aula_entregas_tarea_id_foreign FOREIGN KEY (tarea_id) REFERENCES aula_tareas(id),
  CONSTRAINT aula_entregas_alumno_id_foreign FOREIGN KEY (alumno_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aula_devoluciones (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entrega_id BIGINT UNSIGNED NOT NULL, docente_id BIGINT UNSIGNED NOT NULL,
  comentario TEXT NOT NULL, errores TEXT NULL, calificacion VARCHAR(100) NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  CONSTRAINT aula_devoluciones_entrega_id_foreign FOREIGN KEY (entrega_id) REFERENCES aula_entregas(id),
  CONSTRAINT aula_devoluciones_docente_id_foreign FOREIGN KEY (docente_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aula_plazos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  docente_id BIGINT UNSIGNED NOT NULL,
  curso VARCHAR(40) NOT NULL, materia VARCHAR(100) NOT NULL, etapa VARCHAR(180) NOT NULL,
  descripcion TEXT NULL, vence_en DATETIME NOT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  KEY aula_plazos_curso_vence_en_index (curso, vence_en),
  CONSTRAINT aula_plazos_docente_id_foreign FOREIGN KEY (docente_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aula_configuracion (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  entregas_parciales TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO aula_configuracion (id, entregas_parciales, created_at, updated_at)
VALUES (1, 1, NOW(), NOW());
