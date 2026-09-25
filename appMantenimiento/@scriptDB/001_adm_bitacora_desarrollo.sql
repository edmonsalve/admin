-- Bitácora de desarrollo del proyecto municipal.
-- Ejecutar en la base de datos adm_dCode.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `adm_bitacora_desarrollo` (
    `idBitacora` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `fechaCambio` DATE NOT NULL,
    `idSistema` INT UNSIGNED NULL,
    `idModulo` INT UNSIGNED NULL,
    `tipoCambio` ENUM('MEJORA', 'CORRECCION', 'NUEVO', 'TECNICO') NOT NULL,
    `version` VARCHAR(40) NOT NULL DEFAULT '',
    `titulo` VARCHAR(160) NOT NULL,
    `detalle` TEXT NOT NULL,
    `responsable` VARCHAR(100) NOT NULL DEFAULT '',
    `fechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fechaActualizacion` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`idBitacora`),
    KEY `idx_adm_bitacora_fecha` (`fechaCambio`),
    KEY `idx_adm_bitacora_sistema_modulo_fecha` (`idSistema`, `idModulo`, `fechaCambio`),
    KEY `idx_adm_bitacora_tipo_fecha` (`tipoCambio`, `fechaCambio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
