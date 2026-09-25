ALTER TABLE `adm_bitacora_desarrollo`
    ADD COLUMN `estado` ENUM('ABIERTO','CERRADO','DESCARTADO') NOT NULL DEFAULT 'ABIERTO' AFTER `tipoCambio`;

CREATE TABLE `adm_bitacora_desarrollo_acciones_archivos` (
    `idArchivo` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `idAccion` INT UNSIGNED NOT NULL,
    `archivo` VARCHAR(100) NOT NULL,
    `nombreOriginal` VARCHAR(255) NOT NULL,
    `mimeTipo` VARCHAR(40) NOT NULL,
    `tamano` INT UNSIGNED NOT NULL,
    `fechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idArchivo`), KEY `idx_bitacora_accion_archivo` (`idAccion`),
    CONSTRAINT `fk_bitacora_accion_archivo` FOREIGN KEY (`idAccion`) REFERENCES `adm_bitacora_desarrollo_acciones` (`idAccion`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
