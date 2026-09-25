-- Capturas de pantalla asociadas a registros de la bitácora de desarrollo.
-- Ejecutar en la base de datos adm_dCode.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `adm_bitacora_desarrollo_imagenes` (
    `idImagen` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `idBitacora` INT UNSIGNED NOT NULL,
    `archivo` VARCHAR(100) NOT NULL,
    `nombreOriginal` VARCHAR(255) NOT NULL DEFAULT '',
    `mimeTipo` VARCHAR(40) NOT NULL,
    `tamano` INT UNSIGNED NOT NULL DEFAULT 0,
    `fechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idImagen`),
    KEY `idx_adm_bitacora_imagenes_registro` (`idBitacora`),
    CONSTRAINT `fk_adm_bitacora_imagenes_registro`
        FOREIGN KEY (`idBitacora`) REFERENCES `adm_bitacora_desarrollo` (`idBitacora`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
