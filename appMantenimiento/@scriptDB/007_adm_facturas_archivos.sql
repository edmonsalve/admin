-- PDF asociado a una factura de cliente.
-- Ejecutar en la base de datos adm_dCode.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `adm_facturas_archivos` (
    `idArchivoFactura` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `idFactura` INT NOT NULL,
    `archivo` VARCHAR(100) NOT NULL,
    `nombreOriginal` VARCHAR(255) NOT NULL,
    `mimeTipo` VARCHAR(40) NOT NULL,
    `tamano` INT UNSIGNED NOT NULL,
    `fechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idArchivoFactura`),
    UNIQUE KEY `uk_factura_archivo` (`idFactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
