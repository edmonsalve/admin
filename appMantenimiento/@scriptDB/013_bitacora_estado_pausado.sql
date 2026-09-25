-- Agrega el estado PAUSADO a la bitácora de desarrollo.
ALTER TABLE `adm_bitacora_desarrollo`
    MODIFY COLUMN `estado` ENUM('ABIERTO','PAUSADO','CERRADO','DESCARTADO') NOT NULL DEFAULT 'ABIERTO';
