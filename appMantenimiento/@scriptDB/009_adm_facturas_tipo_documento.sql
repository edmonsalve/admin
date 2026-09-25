-- Tipos de documento y referencia para notas de crédito.
-- Ejecutar en la base de datos adm_dCode.
ALTER TABLE `adm_facturas`
    ADD COLUMN `tipoDocumento` ENUM('FACTURA','NOTA_CREDITO') NOT NULL DEFAULT 'FACTURA' AFTER `id`,
    ADD COLUMN `idFacturaReferencia` INT NULL AFTER `rutCliente`,
    ADD KEY `idx_facturas_tipo_referencia` (`tipoDocumento`, `idFacturaReferencia`),
    ADD KEY `idx_facturas_cliente_tipo_estado` (`rutCliente`, `tipoDocumento`, `estadoFactura`);
