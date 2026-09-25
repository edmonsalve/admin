-- Prioridad operativa de cada entrada de bitácora.
-- Ejecutar en la base de datos adm_dCode.
ALTER TABLE `adm_bitacora_desarrollo`
    ADD COLUMN `prioridad` ENUM('URGENTE','ALTO','MEDIO','BAJO') NOT NULL DEFAULT 'MEDIO' AFTER `estado`;
