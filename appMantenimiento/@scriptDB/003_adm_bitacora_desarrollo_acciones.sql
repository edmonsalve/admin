-- Hilo de acciones y avances para las entradas de la bitácora de desarrollo.
-- Ejecutar en la base de datos adm_dCode.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `adm_bitacora_desarrollo_acciones` (
    `idAccion` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `idBitacora` INT UNSIGNED NOT NULL,
    `fechaAccion` DATE NOT NULL,
    `tipoAccion` ENUM('AVANCE', 'CORRECCION', 'VALIDACION', 'DESPLIEGUE', 'OTRO') NOT NULL DEFAULT 'AVANCE',
    `detalle` TEXT NOT NULL,
    `responsable` VARCHAR(100) NOT NULL DEFAULT '',
    `fechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idAccion`),
    KEY `idx_adm_bitacora_acciones_hilo` (`idBitacora`, `fechaAccion`),
    CONSTRAINT `fk_adm_bitacora_acciones_registro`
        FOREIGN KEY (`idBitacora`) REFERENCES `adm_bitacora_desarrollo` (`idBitacora`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
