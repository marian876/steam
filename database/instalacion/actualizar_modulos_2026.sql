-- Seleccionar la base del proyecto y hacer una copia de seguridad antes de importar.
-- Las tablas existentes se conservan; comprobar compatibilidad si ya existen.
CREATE TABLE IF NOT EXISTS prompts_sitio_web (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  clave VARCHAR(255) NOT NULL, titulo VARCHAR(180) NOT NULL,
  descripcion TEXT NOT NULL, texto LONGTEXT NOT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE KEY prompts_sitio_web_clave_unique (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS archivos_modulos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  modulo VARCHAR(60) NOT NULL, titulo VARCHAR(180) NOT NULL,
  archivo VARCHAR(255) NOT NULL, nombre_original VARCHAR(255) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1, user_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  KEY archivos_modulos_modulo_index (modulo),
  CONSTRAINT archivos_modulos_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
