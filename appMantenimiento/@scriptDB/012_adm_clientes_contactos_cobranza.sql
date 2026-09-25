-- Segundo y tercer contacto de cobranza, con estado individual.
-- Ejecutar en la base de datos adm_dCode.
ALTER TABLE `adm_clientes`
    ADD COLUMN `estadoContactoCobranza` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO' AFTER `telefonoCobranza`,
    ADD COLUMN `contactoCobranza2` VARCHAR(100) NULL AFTER `estadoContactoCobranza`,
    ADD COLUMN `emailCobranza2` VARCHAR(100) NULL AFTER `contactoCobranza2`,
    ADD COLUMN `telefonoCobranza2` VARCHAR(50) NULL AFTER `emailCobranza2`,
    ADD COLUMN `estadoContactoCobranza2` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO' AFTER `telefonoCobranza2`,
    ADD COLUMN `contactoCobranza3` VARCHAR(100) NULL AFTER `estadoContactoCobranza2`,
    ADD COLUMN `emailCobranza3` VARCHAR(100) NULL AFTER `contactoCobranza3`,
    ADD COLUMN `telefonoCobranza3` VARCHAR(50) NULL AFTER `emailCobranza3`,
    ADD COLUMN `estadoContactoCobranza3` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO' AFTER `telefonoCobranza3`;
