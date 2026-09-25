-- Estado del cliente y contacto para gestiones de cobranza.
-- Ejecutar en la base de datos adm_dCode.
ALTER TABLE `adm_clientes`
    ADD COLUMN `estadoCliente` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO' AFTER `cliente`,
    ADD COLUMN `contactoCobranza` VARCHAR(100) NULL AFTER `telefonoContacto`,
    ADD COLUMN `emailCobranza` VARCHAR(100) NULL AFTER `contactoCobranza`,
    ADD COLUMN `telefonoCobranza` VARCHAR(50) NULL AFTER `emailCobranza`;
