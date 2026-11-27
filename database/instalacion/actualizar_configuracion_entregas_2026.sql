-- Seleccionar la base del proyecto y hacer una copia de seguridad antes de importar.
-- No elimina las tareas, entregas ni devoluciones existentes.
CREATE TABLE IF NOT EXISTS aula_configuracion (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  entregas_parciales TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO aula_configuracion (id, entregas_parciales, created_at, updated_at)
VALUES (1, 1, NOW(), NOW());
