-- Seguimiento de gestiones de cobranza por cliente y factura.
-- Ejecutar en la base de datos adm_dCode.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `adm_cobranzas_acciones` (
    `idAccionCobranza` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `idFactura` INT NULL,
    `rutCliente` INT NOT NULL,
    `fechaAccion` DATE NOT NULL,
    `tipoAccion` ENUM('LLAMADO','CORREO','REUNION','COMPROMISO','PAGO','OTRO') NOT NULL,
    `detalle` TEXT NOT NULL,
    `fechaCompromiso` DATE NULL,
    `compromisoCliente` TEXT NULL,
    `responsable` VARCHAR(100) NOT NULL,
    `fechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idAccionCobranza`),
    KEY `idx_cobranzas_cliente_fecha` (`rutCliente`, `fechaAccion`),
    KEY `idx_cobranzas_factura` (`idFactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
