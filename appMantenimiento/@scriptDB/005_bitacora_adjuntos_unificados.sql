CREATE TABLE IF NOT EXISTS `adm_bitacora_desarrollo_adjuntos` (
 `idAdjunto` INT UNSIGNED NOT NULL AUTO_INCREMENT, `idBitacora` INT UNSIGNED NULL,
 `idAccion` INT UNSIGNED NULL, `archivo` VARCHAR(100) NOT NULL, `nombreOriginal` VARCHAR(255) NOT NULL,
 `mimeTipo` VARCHAR(40) NOT NULL, `tamano` INT UNSIGNED NOT NULL, `fechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`idAdjunto`), KEY `idx_bit_adj_entrada` (`idBitacora`), KEY `idx_bit_adj_accion` (`idAccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
