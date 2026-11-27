-- Seleccionar la base del proyecto y hacer una copia de seguridad antes de importar.
-- Requiere la tabla aula_tareas. No modifica ni elimina entregas anteriores.
SET @aula_sql = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'aula_tareas' AND column_name = 'permite_nueva_entrega') = 0,
  'ALTER TABLE aula_tareas ADD COLUMN permite_nueva_entrega TINYINT(1) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE aula_stmt FROM @aula_sql; EXECUTE aula_stmt; DEALLOCATE PREPARE aula_stmt;
