-- Seleccionar la base del proyecto y hacer una copia de seguridad antes de importar.
-- Requiere la tabla aula_devoluciones creada por actualizar_aula_2026.sql.
SET @aula_sql = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'aula_devoluciones' AND column_name = 'errores') = 0,
  'ALTER TABLE aula_devoluciones ADD COLUMN errores TEXT NULL', 'SELECT 1');
PREPARE aula_stmt FROM @aula_sql; EXECUTE aula_stmt; DEALLOCATE PREPARE aula_stmt;
SET @aula_sql = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'aula_devoluciones' AND column_name = 'calificacion') = 0,
  'ALTER TABLE aula_devoluciones ADD COLUMN calificacion VARCHAR(100) NULL', 'SELECT 1');
PREPARE aula_stmt FROM @aula_sql; EXECUTE aula_stmt; DEALLOCATE PREPARE aula_stmt;
