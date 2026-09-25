<?php
require_once('../includes/initSistema.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

$DB_DCODE = 'adm_dCode';
$html = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$fechaFactura = static function ($fecha): string {
    $fecha = (string) $fecha;
    return preg_match('/^\d{8}$/', $fecha)
        ? substr($fecha, 6, 2) . '-' . substr($fecha, 4, 2) . '-' . substr($fecha, 0, 4)
        : 'Sin fecha';
};

$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=utf8mb4',
    DB_USER,
    DB_PASSWD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);
$origenPendientes = "FROM adm_facturas f
    LEFT JOIN (
        SELECT idFacturaReferencia, SUM(montoFactura) AS montoNotasCredito
        FROM adm_facturas
        WHERE tipoDocumento = 'NOTA_CREDITO' AND estadoFactura <> 'A'
        GROUP BY idFacturaReferencia
    ) nc ON nc.idFacturaReferencia = f.id
    LEFT JOIN adm_clientes c ON CAST(SUBSTRING_INDEX(c.rut, '-', 1) AS UNSIGNED) = f.rutCliente
    WHERE f.tipoDocumento = 'FACTURA' AND f.estadoFactura = 'P'";

$consultaClientes = $pdo->query("SELECT f.rutCliente,
        COALESCE(NULLIF(MAX(c.cliente), ''), CONCAT('RUT ', f.rutCliente)) AS cliente,
        COUNT(*) AS facturasPendientes, MIN(f.fechaFactura) AS fechaMasAntigua,
        SUM(GREATEST(f.montoFactura - COALESCE(nc.montoNotasCredito, 0), 0)) AS saldoPendiente
    $origenPendientes
    GROUP BY f.rutCliente
    HAVING saldoPendiente > 0
    ORDER BY saldoPendiente DESC, cliente");
$pendientesPorCliente = $consultaClientes->fetchAll(PDO::FETCH_ASSOC);

$resumen = array('monto' => 0, 'facturas' => 0, 'clientes' => count($pendientesPorCliente));
foreach ($pendientesPorCliente as $pendienteCliente) {
    $resumen['monto'] += (int) $pendienteCliente['saldoPendiente'];
    $resumen['facturas'] += (int) $pendienteCliente['facturasPendientes'];
}

$consultaFacturasPendientes = $pdo->query("SELECT f.rutCliente,
        COALESCE(NULLIF(c.cliente, ''), CONCAT('RUT ', f.rutCliente)) AS cliente,
        f.nroFactura, f.fechaFactura, f.montoFactura,
        COALESCE(nc.montoNotasCredito, 0) AS montoNotasCredito,
        GREATEST(f.montoFactura - COALESCE(nc.montoNotasCredito, 0), 0) AS saldoPendiente
    $origenPendientes
    AND GREATEST(f.montoFactura - COALESCE(nc.montoNotasCredito, 0), 0) > 0
    ORDER BY cliente, f.fechaFactura, f.nroFactura");
$facturasPorCliente = array();
while ($facturaPendiente = $consultaFacturasPendientes->fetch(PDO::FETCH_ASSOC)) {
    $facturasPorCliente[(int) $facturaPendiente['rutCliente']][] = $facturaPendiente;
}

class CobranzasPendientesPdf extends TCPDF
{
    public function Header(): void
    {
        $this->SetFont('dejavusans', 'B', 13);
        $this->SetTextColor(23, 76, 113);
        $this->Cell(0, 9, 'Informe de cuentas pendientes por cobrar', 0, 1, 'L');
        $this->SetDrawColor(185, 207, 220);
        $this->Line(14, 20, $this->getPageWidth() - 14, 20);
    }

    public function Footer(): void
    {
        $this->SetY(-14);
        $this->SetFont('dejavusans', '', 7.5);
        $this->SetTextColor(100, 116, 126);
        $this->Cell(0, 5, 'Generado el ' . date('d-m-Y H:i') . ' · Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new CobranzasPendientesPdf('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('dMuni');
$pdf->SetAuthor('dMuni');
$pdf->SetTitle('Cuentas pendientes por cobrar');
$pdf->SetMargins(10, 28, 10);
$pdf->SetAutoPageBreak(true, 12);
$pdf->SetFont('dejavusans', '', 8);
$pdf->AddPage();
$pdf->SetTextColor(75, 97, 112);
$pdf->writeHTML('<p>Al ' . date('d-m-Y') . '. Los saldos consideran las notas de crédito activas aplicadas a cada factura.</p>', true, false, true, false, '');
$pdf->SetTextColor(40, 77, 101);
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(0, 7, 'Total pendiente: $' . number_format($resumen['monto'], 0, ',', '.') . ' · '
    . $resumen['facturas'] . ' facturas · ' . $resumen['clientes'] . ' clientes', 0, 1, 'L');
$pdf->Ln(2);

$anchosColumna = array(42, 38, 52, 52, 58);
$encabezadoTabla = static function () use ($pdf, $anchosColumna): void {
    $titulos = array('N° factura', 'Emisión', 'Monto', 'NC aplicadas', 'Saldo pendiente');
    $pdf->SetFillColor(223, 236, 239);
    $pdf->SetTextColor(40, 77, 101);
    $pdf->SetFont('dejavusans', 'B', 7.5);
    foreach ($titulos as $indice => $titulo) {
        $pdf->Cell($anchosColumna[$indice], 5.5, $titulo, 1, $indice === count($titulos) - 1 ? 1 : 0, $indice < 2 ? 'L' : 'R', true);
    }
};
$tituloDetalle = static function () use ($pdf): void {
    $pdf->SetTextColor(23, 76, 113);
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(0, 6, 'Detalle de facturas pendientes por cliente', 0, 1, 'L');
};
$tituloDetalle();
if (!$facturasPorCliente) {
    $pdf->SetTextColor(75, 97, 112);
    $pdf->SetFont('dejavusans', '', 8);
    $pdf->Cell(0, 6, 'No hay facturas pendientes de cobro.', 0, 1, 'L');
}
foreach ($facturasPorCliente as $facturasCliente) {
    $cliente = $facturasCliente[0]['cliente'];
    $saldoCliente = array_sum(array_map(static function (array $factura): int { return (int) $factura['saldoPendiente']; }, $facturasCliente));
    if ($pdf->GetY() + 12 + count($facturasCliente) * 5.5 > 196) {
        $pdf->AddPage();
        $tituloDetalle();
    }
    $pdf->SetFillColor(237, 244, 247);
    $pdf->SetTextColor(23, 76, 113);
    $pdf->SetFont('dejavusans', 'B', 8);
    $pdf->Cell(0, 5.5, $cliente . ' · Total pendiente: $' . number_format($saldoCliente, 0, ',', '.'), 1, 1, 'L', true);
    $encabezadoTabla();
    foreach ($facturasCliente as $factura) {
        if ($pdf->GetY() + 5.5 > 196) {
            $pdf->AddPage();
            $tituloDetalle();
            $pdf->SetFillColor(237, 244, 247);
            $pdf->SetTextColor(23, 76, 113);
            $pdf->SetFont('dejavusans', 'B', 8);
            $pdf->Cell(0, 5.5, $cliente . ' · Continuación', 1, 1, 'L', true);
            $encabezadoTabla();
        }
        $valores = array(
            (string) (int) $factura['nroFactura'],
            $fechaFactura($factura['fechaFactura']),
            '$' . number_format((int) $factura['montoFactura'], 0, ',', '.'),
            '$' . number_format((int) $factura['montoNotasCredito'], 0, ',', '.'),
            '$' . number_format((int) $factura['saldoPendiente'], 0, ',', '.'),
        );
        $pdf->SetTextColor(52, 79, 96);
        $pdf->SetFont('dejavusans', '', 7.5);
        foreach ($valores as $indice => $valor) {
            $pdf->Cell($anchosColumna[$indice], 5.5, $valor, 1, $indice === count($valores) - 1 ? 1 : 0, $indice < 2 ? 'L' : 'R');
        }
    }
    $pdf->Ln(2);
}
$pdf->Output('cobranzas_pendientes_' . date('Ymd') . '.pdf', 'I');
?>
