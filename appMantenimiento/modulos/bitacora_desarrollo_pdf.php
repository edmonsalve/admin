<?php
require_once('../includes/initSistema.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

$DB_DCODE = 'adm_dCode';
$tiposCambio = array(
    'MEJORA' => 'Mejora',
    'CORRECCION' => 'Corrección de error',
    'NUEVO' => 'Nueva funcionalidad',
    'TECNICO' => 'Cambio técnico',
);
$tiposAccion = array(
    'AVANCE' => 'Avance',
    'CORRECCION' => 'Corrección',
    'VALIDACION' => 'Validación',
    'DESPLIEGUE' => 'Despliegue',
    'OTRO' => 'Otro',
);
$estadosEntrada = array(
    'ABIERTO' => 'Abierto',
    'CERRADO' => 'Cerrado',
    'DESCARTADO' => 'Descartado',
);
$prioridadesEntrada = array(
    'URGENTE' => 'Urgente',
    'ALTO' => 'Alto',
    'MEDIO' => 'Medio',
    'BAJO' => 'Bajo',
);

$idBitacora = $_GET['id'] ?? '';
if (!ctype_digit((string) $idBitacora) || (int) $idBitacora < 1) {
    http_response_code(400);
    exit('Registro de bitácora no válido.');
}

$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=utf8mb4',
    DB_USER,
    DB_PASSWD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);
$consultaEntrada = $pdo->prepare("SELECT b.idBitacora, b.fechaCambio, b.tipoCambio, b.estado, b.prioridad, b.version,
        b.titulo, b.detalle, b.responsable, s.nombre AS sistema, m.modulo
    FROM adm_bitacora_desarrollo b
    LEFT JOIN adm_sistemas s ON s.id = b.idSistema
    LEFT JOIN adm_modulos m ON m.id = b.idModulo
    WHERE b.idBitacora = ?");
$consultaEntrada->execute(array((int) $idBitacora));
$entrada = $consultaEntrada->fetch(PDO::FETCH_ASSOC);
if (!$entrada) {
    http_response_code(404);
    exit('La entrada de bitácora no existe.');
}

$consultaAdjuntosEntrada = $pdo->prepare('SELECT nombreOriginal, mimeTipo, tamano
    FROM adm_bitacora_desarrollo_imagenes WHERE idBitacora = ? ORDER BY idImagen');
$consultaAdjuntosEntrada->execute(array((int) $idBitacora));
$adjuntosEntrada = $consultaAdjuntosEntrada->fetchAll(PDO::FETCH_ASSOC);

$consultaAcciones = $pdo->prepare('SELECT idAccion, fechaAccion, tipoAccion, detalle, responsable
    FROM adm_bitacora_desarrollo_acciones
    WHERE idBitacora = ?
    ORDER BY fechaAccion ASC, idAccion ASC');
$consultaAcciones->execute(array((int) $idBitacora));
$acciones = $consultaAcciones->fetchAll(PDO::FETCH_ASSOC);
$adjuntosPorAccion = array();
if ($acciones) {
    $idsAcciones = array_map(static fn(array $accion): int => (int) $accion['idAccion'], $acciones);
    $marcadores = implode(',', array_fill(0, count($idsAcciones), '?'));
    $consultaAdjuntosAccion = $pdo->prepare("SELECT idAccion, nombreOriginal, mimeTipo, tamano
        FROM adm_bitacora_desarrollo_acciones_archivos
        WHERE idAccion IN ($marcadores)
        ORDER BY idArchivo");
    $consultaAdjuntosAccion->execute($idsAcciones);
    while ($adjunto = $consultaAdjuntosAccion->fetch(PDO::FETCH_ASSOC)) {
        $adjuntosPorAccion[(int) $adjunto['idAccion']][] = $adjunto;
    }
}

$html = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$fecha = static function ($valor): string {
    $marcaTiempo = strtotime((string) $valor);
    return $marcaTiempo ? date('d-m-Y', $marcaTiempo) : '';
};
$etiquetaArchivo = static function ($mimeTipo): string {
    $tipos = array(
        'application/pdf' => 'PDF',
        'image/jpeg' => 'JPG',
        'image/png' => 'PNG',
        'image/webp' => 'WEBP',
        'text/plain' => 'TXT',
        'application/sql' => 'SQL',
        'text/x-sql' => 'SQL',
        'application/x-sql' => 'SQL',
    );
    return $tipos[$mimeTipo] ?? 'Archivo';
};
class BitacoraDesarrolloPdf extends TCPDF
{
    public function Header()
    {
        $this->SetFillColor(24, 75, 108);
        $this->Rect(0, 0, $this->getPageWidth(), 21, 'F');
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('dejavusans', 'B', 12);
        $this->SetXY(14, 6);
        $this->Cell(0, 5, 'BITÁCORA DEV.DCODE', 0, 1, 'L', false);
        $this->SetFont('dejavusans', '', 7.5);
        $this->SetX(14);
        $this->Cell(0, 4, 'Historial de cambio y seguimiento', 0, 0, 'L', false);
        $this->SetTextColor(40, 72, 93);
    }

    public function Footer()
    {
        $this->SetY(-13);
        $this->SetDrawColor(205, 219, 228);
        $this->Line(14, $this->GetY(), $this->getPageWidth() - 14, $this->GetY());
        $this->SetY(-10);
        $this->SetTextColor(101, 120, 132);
        $this->SetFont('dejavusans', '', 7);
        $this->Cell(0, 4, 'Generado el ' . date('d-m-Y H:i') . '  |  Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    private function aseguraEspacio($altoMinimo): void
    {
        if ($this->GetY() + $altoMinimo > $this->getPageHeight() - 18) {
            $this->AddPage();
        }
    }

    public function seccion($titulo): void
    {
        $this->aseguraEspacio(13);
        $this->Ln(3);
        $this->SetFillColor(237, 244, 247);
        $this->SetTextColor(23, 76, 113);
        $this->SetFont('dejavusans', 'B', 10);
        $this->Cell(0, 7, $titulo, 0, 1, 'L', true);
        $this->SetTextColor(51, 78, 95);
    }

    public function metadatos(array $celdas): void
    {
        $anchos = array(36, 64, 46, 36);
        $alto = 12;
        $xInicio = $this->GetX();
        foreach (array_chunk($celdas, 4) as $fila) {
            $this->aseguraEspacio($alto + 2);
            $y = $this->GetY();
            $x = $xInicio;
            foreach ($anchos as $indice => $ancho) {
                $celda = $fila[$indice] ?? array('', '');
                $valor = mb_strimwidth((string) $celda[1], 0, 58, '...', 'UTF-8');
                $this->SetFillColor($indice % 2 === 0 ? 237 : 248, $indice % 2 === 0 ? 244 : 250, $indice % 2 === 0 ? 247 : 251);
                $this->SetDrawColor(205, 220, 229);
                $this->Rect($x, $y, $ancho, $alto, 'DF');
                $this->SetXY($x + 2.5, $y + 1.3);
                $this->SetTextColor(91, 116, 131);
                $this->SetFont('dejavusans', '', 6.8);
                $this->Cell($ancho - 5, 2.5, (string) $celda[0], 0, 0, 'L');
                $this->SetXY($x + 2.5, $y + 4.5);
                $this->SetTextColor(36, 77, 104);
                $this->SetFont('dejavusans', 'B', 8.5);
                $this->MultiCell($ancho - 5, 4, $valor, 0, 'L', false, 0);
                $x += $ancho;
            }
            $this->SetY($y + $alto);
        }
        $this->SetTextColor(51, 78, 95);
    }

    public function bloqueTexto($texto): void
    {
        $this->aseguraEspacio(22);
        $this->SetDrawColor(213, 225, 231);
        $this->SetFillColor(247, 250, 251);
        $this->SetFont('dejavusans', '', 9.3);
        $this->MultiCell(0, 5.6, (string) $texto, 1, 'L', true, 1);
    }

    public function listaAdjuntos(array $adjuntos, callable $formato): void
    {
        if (!$adjuntos) {
            $this->SetTextColor(104, 122, 132);
            $this->SetFont('dejavusans', 'I', 8.5);
            $this->MultiCell(0, 5, 'Sin adjuntos.', 0, 'L', false, 1);
            $this->SetTextColor(51, 78, 95);
            return;
        }
        $this->SetFont('dejavusans', '', 8.5);
        foreach ($adjuntos as $adjunto) {
            $this->aseguraEspacio(7);
            $this->MultiCell(0, 5, '- ' . $formato($adjunto), 0, 'L', false, 1);
        }
    }

    public function accion($fecha, $tipo, $detalle, $responsable, array $adjuntos, callable $formato): void
    {
        $this->aseguraEspacio(32);
        $this->SetFillColor(230, 240, 247);
        $this->SetDrawColor(190, 211, 224);
        $this->SetTextColor(35, 90, 125);
        $this->SetFont('dejavusans', 'B', 8.8);
        $this->MultiCell(0, 6, $fecha . '  |  ' . $tipo, 1, 'L', true, 1);

        $adjuntosTexto = 'Sin adjuntos.';
        if ($adjuntos) {
            $nombres = array();
            foreach ($adjuntos as $adjunto) {
                $nombres[] = '- ' . $formato($adjunto);
            }
            $adjuntosTexto = implode("\n", $nombres);
        }
        $contenido = "Detalle\n" . $detalle
            . "\n\nResponsable: " . $responsable
            . "\n\nAdjuntos de la acción\n" . $adjuntosTexto;
        $this->SetTextColor(51, 78, 95);
        $this->SetFont('dejavusans', '', 8.8);
        $this->SetFillColor(255, 255, 255);
        $this->MultiCell(0, 5.2, $contenido, 1, 'L', true, 1);
        $this->Ln(2);
    }
}

$alcance = $entrada['sistema'] ?: 'Proyecto municipal';
if ($entrada['modulo']) {
    $alcance .= ' / ' . $entrada['modulo'];
}
$tipoCambio = $tiposCambio[$entrada['tipoCambio']] ?? $entrada['tipoCambio'];
$estado = $estadosEntrada[$entrada['estado']] ?? $entrada['estado'];
$prioridad = $prioridadesEntrada[$entrada['prioridad']] ?? $entrada['prioridad'];
$version = trim((string) $entrada['version']) !== '' ? $entrada['version'] : 'Sin versión';

while (ob_get_level() > 0) {
    ob_end_clean();
}
$pdf = new BitacoraDesarrolloPdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('dMuni');
$pdf->SetAuthor('dMuni');
$pdf->SetTitle('Bitácora - ' . $entrada['titulo']);
$pdf->SetSubject('Historial de desarrollo municipal');
$pdf->SetMargins(14, 29, 14);
$pdf->SetAutoPageBreak(true, 17);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetFont('dejavusans', '', 9.5);
$pdf->AddPage();
$formatoAdjunto = static function (array $adjunto) use ($etiquetaArchivo): string {
    $tamanoKb = max(1, (int) $adjunto['tamano'] / 1024);
    return (string) $adjunto['nombreOriginal'] . ' [' . $etiquetaArchivo($adjunto['mimeTipo'])
        . ' · ' . number_format($tamanoKb, 1, ',', '.') . ' KB]';
};

$pdf->SetTextColor(23, 76, 113);
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->MultiCell(0, 8, (string) $entrada['titulo'], 0, 'L', false, 1);
$pdf->SetTextColor(82, 105, 119);
$pdf->SetFont('dejavusans', '', 8.5);
$pdf->MultiCell(0, 5, 'Entrada #' . (int) $entrada['idBitacora'] . '  |  ' . $tipoCambio . '  |  Prioridad: ' . $prioridad . '  |  Estado: ' . $estado, 0, 'L', false, 1);
$pdf->Ln(2);
$pdf->metadatos(array(
    array('Fecha del cambio', $fecha($entrada['fechaCambio'])),
    array('Sistema / módulo', $alcance),
    array('Responsable', $entrada['responsable']),
    array('Versión', $version),
    array('Tipo', $tipoCambio),
    array('Prioridad', $prioridad),
    array('Estado', $estado),
    array('Acciones', count($acciones)),
));
$pdf->seccion('Detalle del cambio');
$pdf->bloqueTexto($entrada['detalle']);
$pdf->seccion('Adjuntos de la entrada (' . count($adjuntosEntrada) . ')');
$pdf->listaAdjuntos($adjuntosEntrada, $formatoAdjunto);
$pdf->seccion('Hilo de acciones (' . count($acciones) . ')');
if (!$acciones) {
    $pdf->SetTextColor(104, 122, 132);
    $pdf->SetFont('dejavusans', 'I', 8.5);
    $pdf->MultiCell(0, 5, 'No hay acciones registradas para esta entrada.', 0, 'L', false, 1);
    $pdf->SetTextColor(51, 78, 95);
}
foreach ($acciones as $accion) {
    $pdf->accion(
        $fecha($accion['fechaAccion']),
        $tiposAccion[$accion['tipoAccion']] ?? $accion['tipoAccion'],
        $accion['detalle'],
        $accion['responsable'],
        $adjuntosPorAccion[(int) $accion['idAccion']] ?? array(),
        $formatoAdjunto
    );
}
$pdf->Output('bitacora_desarrollo_' . (int) $entrada['idBitacora'] . '.pdf', 'I');
exit;
?>
