-- Seleccionar la base del proyecto y hacer una copia de seguridad antes de importar.
-- No elimina plazos, tareas ni la configuración general anterior.
CREATE TABLE IF NOT EXISTS aula_configuracion_cursos (
  curso VARCHAR(40) NOT NULL PRIMARY KEY,
  entregas_parciales TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
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
