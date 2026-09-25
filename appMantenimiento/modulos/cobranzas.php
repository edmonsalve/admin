<?php
require_once('../includes/initSistema.php');

$DB_DCODE = 'adm_dCode';
$tiposAccion = array(
    'LLAMADO' => 'Llamado telefónico',
    'CORREO' => 'Correo enviado',
    'REUNION' => 'Reunión',
    'COMPROMISO' => 'Compromiso de pago',
    'PAGO' => 'Confirmación de pago',
    'OTRO' => 'Otra gestión',
);
$html = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$fechaValida = static function (string $fecha): bool {
    $fechaObjeto = DateTime::createFromFormat('Y-m-d', $fecha);
    return $fechaObjeto && $fechaObjeto->format('Y-m-d') === $fecha;
};
$fechaFactura = static function ($fecha): string {
    $fecha = (string) $fecha;
    return preg_match('/^\d{8}$/', $fecha)
        ? substr($fecha, 6, 2) . '-' . substr($fecha, 4, 2) . '-' . substr($fecha, 0, 4)
        : '';
};

$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=utf8mb4',
    DB_USER,
    DB_PASSWD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);

// Customers with a usable tax id are available in the selector.
$clientes = $pdo->query("SELECT rut, cliente,
        contactoCobranza, emailCobranza, telefonoCobranza, estadoContactoCobranza,
        contactoCobranza2, emailCobranza2, telefonoCobranza2, estadoContactoCobranza2,
        contactoCobranza3, emailCobranza3, telefonoCobranza3, estadoContactoCobranza3
    FROM adm_clientes
    WHERE estadoCliente = 'ACTIVO' AND rut IS NOT NULL AND rut <> ''
    ORDER BY cliente")
    ->fetchAll(PDO::FETCH_ASSOC);
$clientesPorRut = array();
$contactosCobranzaPorRut = array();
$camposContactoCobranza = array(
    array('contactoCobranza', 'emailCobranza', 'telefonoCobranza', 'estadoContactoCobranza'),
    array('contactoCobranza2', 'emailCobranza2', 'telefonoCobranza2', 'estadoContactoCobranza2'),
    array('contactoCobranza3', 'emailCobranza3', 'telefonoCobranza3', 'estadoContactoCobranza3'),
);
foreach ($clientes as $cliente) {
    $rut = preg_replace('/\D.*/', '', (string) $cliente['rut']);
    if ($rut === '') { continue; }
    $rut = (int) $rut;
    $clientesPorRut[$rut] = $cliente['cliente'];
    $contactosCobranzaPorRut[$rut] = array();
    foreach ($camposContactoCobranza as $camposContacto) {
        list($campoNombre, $campoCorreo, $campoTelefono, $campoEstado) = $camposContacto;
        $nombre = trim((string) ($cliente[$campoNombre] ?? ''));
        $correo = trim((string) ($cliente[$campoCorreo] ?? ''));
        $telefono = trim((string) ($cliente[$campoTelefono] ?? ''));
        $estado = (string) ($cliente[$campoEstado] ?? 'ACTIVO');
        if ($estado !== 'INACTIVO' && ($nombre !== '' || $correo !== '' || $telefono !== '')) {
            $contactosCobranzaPorRut[$rut][] = array('nombre' => $nombre, 'correo' => $correo, 'telefono' => $telefono);
        }
    }
}

$rutCliente = isset($_GET['cliente']) && ctype_digit((string) $_GET['cliente'])
    ? (int) $_GET['cliente']
    : 0;
if (!isset($clientesPorRut[$rutCliente])) {
    $rutCliente = 0;
}
$filtroEstado = isset($_GET['estado']) && in_array($_GET['estado'], array('P', 'C'), true)
    ? $_GET['estado']
    : '';

$datosAccion = array(
    'idFactura' => '',
    'fechaAccion' => date('Y-m-d'),
    'tipoAccion' => 'LLAMADO',
    'detalle' => '',
    'fechaCompromiso' => '',
    'compromisoCliente' => '',
    'responsable' => trim((string) ($_SESSION['nomUsuario'] ?? $_SESSION['idUser'] ?? '')),
);
$mensajeError = '';
$mensajeExito = isset($_GET['gestion'])
    ? 'La acción de cobro fue registrada.'
    : (isset($_GET['facturaPagada']) ? 'La factura fue marcada como pagada.' : '');

if (isset($_POST['marcarFacturaPagada'])) {
    $rutPost = $_POST['rutCliente'] ?? '';
    $idFactura = $_POST['idFactura'] ?? '';
    $fechaPago = trim((string) ($_POST['fechaPago'] ?? ''));
    $estadoRetorno = $_POST['estadoFiltro'] ?? '';
    $rutPost = ctype_digit((string) $rutPost) ? (int) $rutPost : 0;
    $estadoRetorno = in_array($estadoRetorno, array('P', 'C'), true) ? $estadoRetorno : '';

    if (!isset($clientesPorRut[$rutPost]) || !ctype_digit((string) $idFactura)) {
        $mensajeError = 'La factura o el cliente indicado no es válido.';
    } elseif (!$fechaValida($fechaPago)) {
        $mensajeError = 'La fecha de pago no es válida.';
    } elseif (empty($_SESSION['save'])) {
        $mensajeError = 'No dispone de permisos para actualizar facturas.';
    } else {
        $facturaPendiente = $pdo->prepare("SELECT f.estadoFactura,
            GREATEST(f.montoFactura - COALESCE(SUM(nc.montoFactura), 0), 0) AS saldoPendiente
            FROM adm_facturas f
            LEFT JOIN adm_facturas nc ON nc.idFacturaReferencia = f.id
                AND nc.tipoDocumento = 'NOTA_CREDITO' AND nc.estadoFactura <> 'A'
            WHERE f.id = ? AND f.rutCliente = ? AND f.tipoDocumento = 'FACTURA'
            GROUP BY f.id, f.estadoFactura, f.montoFactura");
        $facturaPendiente->execute(array((int) $idFactura, $rutPost));
        $facturaPendiente = $facturaPendiente->fetch(PDO::FETCH_ASSOC);
        if (!$facturaPendiente || $facturaPendiente['estadoFactura'] !== 'P' || (int) $facturaPendiente['saldoPendiente'] <= 0) {
            $mensajeError = 'La factura no tiene saldo pendiente de pago o no corresponde al cliente.';
        } else {
            $marcarPagada = $pdo->prepare("UPDATE adm_facturas SET estadoFactura = 'C', fechaPago = ? WHERE id = ? AND estadoFactura = 'P'");
            $marcarPagada->execute(array((int) str_replace('-', '', $fechaPago), (int) $idFactura));
            header('Location: cobranzas.php?cliente=' . $rutPost . '&estado=' . $estadoRetorno . '&facturaPagada=1');
            exit;
        }
    }

    $rutCliente = $rutPost;
    $filtroEstado = $estadoRetorno;
} elseif (isset($_POST['guardarAccionCobranza'])) {
    foreach (array_keys($datosAccion) as $campo) {
        $datosAccion[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }
    $rutPost = $_POST['rutCliente'] ?? '';
    $estadoRetorno = $_POST['estadoFiltro'] ?? '';
    $rutPost = ctype_digit((string) $rutPost) ? (int) $rutPost : 0;
    $estadoRetorno = in_array($estadoRetorno, array('P', 'C'), true) ? $estadoRetorno : '';

    if (!isset($clientesPorRut[$rutPost])) {
        $mensajeError = 'Seleccione un cliente válido antes de registrar una acción.';
    } elseif (!$fechaValida($datosAccion['fechaAccion'])) {
        $mensajeError = 'La fecha de la acción no es válida.';
    } elseif (!array_key_exists($datosAccion['tipoAccion'], $tiposAccion)) {
        $mensajeError = 'Seleccione un tipo de acción válido.';
    } elseif (mb_strlen($datosAccion['detalle']) < 3 || mb_strlen($datosAccion['detalle']) > 4000) {
        $mensajeError = 'El detalle de la gestión debe tener entre 3 y 4.000 caracteres.';
    } elseif ($datosAccion['fechaCompromiso'] !== '' && !$fechaValida($datosAccion['fechaCompromiso'])) {
        $mensajeError = 'La fecha comprometida no es válida.';
    } elseif (mb_strlen($datosAccion['compromisoCliente']) > 4000 || $datosAccion['responsable'] === '' || mb_strlen($datosAccion['responsable']) > 100) {
        $mensajeError = 'Revise el compromiso y responsable indicados.';
    } elseif (empty($_SESSION['save'])) {
        $mensajeError = 'No dispone de permisos para registrar acciones de cobro.';
    }

    $idFactura = $datosAccion['idFactura'] === '' ? null : (ctype_digit($datosAccion['idFactura']) ? (int) $datosAccion['idFactura'] : -1);
    if ($mensajeError === '' && $idFactura !== null) {
        $facturaValida = $pdo->prepare("SELECT 1 FROM adm_facturas WHERE id = ? AND rutCliente = ? AND tipoDocumento = 'FACTURA'");
        $facturaValida->execute(array($idFactura, $rutPost));
        if (!$facturaValida->fetchColumn()) {
            $mensajeError = 'La factura seleccionada no corresponde al cliente.';
        }
    }

    if ($mensajeError === '') {
        $guardar = $pdo->prepare('INSERT INTO adm_cobranzas_acciones
            (idFactura, rutCliente, fechaAccion, tipoAccion, detalle, fechaCompromiso, compromisoCliente, responsable)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $guardar->execute(array(
            $idFactura,
            $rutPost,
            $datosAccion['fechaAccion'],
            $datosAccion['tipoAccion'],
            $datosAccion['detalle'],
            $datosAccion['fechaCompromiso'] === '' ? null : $datosAccion['fechaCompromiso'],
            $datosAccion['compromisoCliente'] === '' ? null : $datosAccion['compromisoCliente'],
            $datosAccion['responsable'],
        ));
        header('Location: cobranzas.php?cliente=' . $rutPost . '&estado=' . $estadoRetorno . '&gestion=1');
        exit;
    }

    $rutCliente = $rutPost;
    $filtroEstado = $estadoRetorno;
}

$opcionesClientes = '<option value="">Seleccione un cliente</option>';
foreach ($clientesPorRut as $rut => $cliente) {
    $seleccionado = $rut === $rutCliente ? ' selected' : '';
    $opcionesClientes .= '<option value="' . $rut . '"' . $seleccionado . '>'
        . $html($cliente) . '</option>';
}

$contactosCobranzaHTML = '<p class="cobranzas-contacto__vacio">No hay contactos activos de cobranza registrados.</p>';
if ($rutCliente > 0 && !empty($contactosCobranzaPorRut[$rutCliente])) {
    $contactosCobranzaHTML = '';
    foreach ($contactosCobranzaPorRut[$rutCliente] as $contacto) {
        $nombreContacto = $contacto['nombre'] !== '' ? $contacto['nombre'] : 'Contacto de cobranza';
        $mediosContacto = '';
        if ($contacto['correo'] !== '') {
            $mediosContacto .= '<a href="mailto:' . $html($contacto['correo']) . '">' . $html($contacto['correo']) . '</a>';
        }
        if ($contacto['telefono'] !== '') {
            $telefonoEnlace = preg_replace('/[^0-9+]/', '', $contacto['telefono']);
            $mediosContacto .= '<a href="tel:' . $html($telefonoEnlace) . '">' . $html($contacto['telefono']) . '</a>';
        }
        $contactosCobranzaHTML .= '<article class="cobranzas-contacto__persona"><strong>' . $html($nombreContacto)
            . '</strong><div class="cobranzas-contacto__medios">' . $mediosContacto . '</div></article>';
    }
}

$dashboard = array(
    'montoPendiente' => 0,
    'facturasPendientes' => 0,
    'clientesPendientes' => 0,
    'fechaMasAntigua' => '',
    'mesesAtrasoMaximo' => 0,
    'mayorDeudor' => 'Sin saldos pendientes',
    'montoMayorDeudor' => 0,
);
$filasDashboardClientes = '';
$filasDashboardAntiguedad = '';
$tarjetasDashboardGestiones = '';
if ($rutCliente === 0) {
    $origenPendientes = "FROM adm_facturas f
        LEFT JOIN (
            SELECT idFacturaReferencia, SUM(montoFactura) AS montoNotasCredito
            FROM adm_facturas
            WHERE tipoDocumento = 'NOTA_CREDITO' AND estadoFactura <> 'A'
            GROUP BY idFacturaReferencia
        ) nc ON nc.idFacturaReferencia = f.id
        LEFT JOIN adm_clientes c ON CAST(SUBSTRING_INDEX(c.rut, '-', 1) AS UNSIGNED) = f.rutCliente
        WHERE f.tipoDocumento = 'FACTURA' AND f.estadoFactura = 'P'";

    $consultaPendientePorCliente = $pdo->query("SELECT f.rutCliente,
            COALESCE(NULLIF(MAX(c.cliente), ''), CONCAT('RUT ', f.rutCliente)) AS cliente,
            COUNT(*) AS facturasPendientes, MIN(f.fechaFactura) AS fechaMasAntigua,
            SUM(GREATEST(f.montoFactura - COALESCE(nc.montoNotasCredito, 0), 0)) AS saldoPendiente
        $origenPendientes
        GROUP BY f.rutCliente
        HAVING saldoPendiente > 0
        ORDER BY saldoPendiente DESC, cliente");
    $pendientesPorCliente = $consultaPendientePorCliente->fetchAll(PDO::FETCH_ASSOC);

    $antiguedadPendiente = array(
        'Menos de 1 mes' => array('facturas' => 0, 'monto' => 0),
        '1 a 2 meses' => array('facturas' => 0, 'monto' => 0),
        '3 a 5 meses' => array('facturas' => 0, 'monto' => 0),
        '6 meses o más' => array('facturas' => 0, 'monto' => 0),
    );
    $hoy = new DateTime('today');
    foreach ($pendientesPorCliente as $pendienteCliente) {
        $saldoPendiente = (int) $pendienteCliente['saldoPendiente'];
        $dashboard['montoPendiente'] += $saldoPendiente;
        $dashboard['facturasPendientes'] += (int) $pendienteCliente['facturasPendientes'];
        $dashboard['clientesPendientes']++;
        if ($dashboard['montoMayorDeudor'] < $saldoPendiente) {
            $dashboard['montoMayorDeudor'] = $saldoPendiente;
            $dashboard['mayorDeudor'] = (string) $pendienteCliente['cliente'];
        }

        $fechaAntigua = DateTime::createFromFormat('Ymd', (string) $pendienteCliente['fechaMasAntigua']);
        if ($fechaAntigua && ($dashboard['fechaMasAntigua'] === '' || $fechaAntigua->format('Ymd') < $dashboard['fechaMasAntigua'])) {
            $dashboard['fechaMasAntigua'] = $fechaAntigua->format('Ymd');
        }

        $consultaAntiguedadCliente = $pdo->prepare("SELECT f.fechaFactura,
                COUNT(*) AS facturasPendientes,
                SUM(GREATEST(f.montoFactura - COALESCE(nc.montoNotasCredito, 0), 0)) AS saldoPendiente
            $origenPendientes AND f.rutCliente = ?
            GROUP BY f.fechaFactura
            HAVING saldoPendiente > 0");
        $consultaAntiguedadCliente->execute(array((int) $pendienteCliente['rutCliente']));
        while ($pendienteAntiguedad = $consultaAntiguedadCliente->fetch(PDO::FETCH_ASSOC)) {
            $fechaEmision = DateTime::createFromFormat('Ymd', (string) $pendienteAntiguedad['fechaFactura']);
            $mesesAtraso = 0;
            if ($fechaEmision && $fechaEmision <= $hoy) {
                $diferencia = $fechaEmision->diff($hoy);
                $mesesAtraso = $diferencia->y * 12 + $diferencia->m;
            }
            $dashboard['mesesAtrasoMaximo'] = max($dashboard['mesesAtrasoMaximo'], $mesesAtraso);
            $tramo = $mesesAtraso === 0 ? 'Menos de 1 mes' : ($mesesAtraso <= 2 ? '1 a 2 meses' : ($mesesAtraso <= 5 ? '3 a 5 meses' : '6 meses o más'));
            $antiguedadPendiente[$tramo]['facturas'] += (int) $pendienteAntiguedad['facturasPendientes'];
            $antiguedadPendiente[$tramo]['monto'] += (int) $pendienteAntiguedad['saldoPendiente'];
        }

        $filasDashboardClientes .= '<tr><td><a class="cobranzas-dashboard__enlace" href="cobranzas.php?cliente='
            . (int) $pendienteCliente['rutCliente'] . '&amp;estado=P">' . $html($pendienteCliente['cliente']) . '</a></td>'
            . '<td class="cobranzas-monto">' . (int) $pendienteCliente['facturasPendientes'] . '</td>'
            . '<td>' . $html($fechaFactura($pendienteCliente['fechaMasAntigua'])) . '</td>'
            . '<td class="cobranzas-monto">$' . number_format($saldoPendiente, 0, ',', '.') . '</td></tr>';
    }
    foreach ($antiguedadPendiente as $tramo => $datosAntiguedad) {
        $filasDashboardAntiguedad .= '<tr><td>' . $tramo . '</td><td class="cobranzas-monto">'
            . $datosAntiguedad['facturas'] . '</td><td class="cobranzas-monto">$'
            . number_format($datosAntiguedad['monto'], 0, ',', '.') . '</td></tr>';
    }
    if ($filasDashboardClientes === '') {
        $filasDashboardClientes = '<tr><td colspan="4" class="cobranzas-vacio">No hay facturas pendientes de cobro.</td></tr>';
    }

    $resumenGestiones = array();
    foreach ($tiposAccion as $codigoAccion => $nombreAccion) {
        $resumenGestiones[$codigoAccion] = array('cantidad' => 0, 'ultimaFecha' => '');
    }
    $consultaGestiones = $pdo->query('SELECT tipoAccion, COUNT(*) AS cantidad, MAX(fechaAccion) AS ultimaFecha FROM adm_cobranzas_acciones GROUP BY tipoAccion');
    while ($gestion = $consultaGestiones->fetch(PDO::FETCH_ASSOC)) {
        $codigoAccion = (string) $gestion['tipoAccion'];
        if (isset($resumenGestiones[$codigoAccion])) {
            $resumenGestiones[$codigoAccion] = array('cantidad' => (int) $gestion['cantidad'], 'ultimaFecha' => (string) $gestion['ultimaFecha']);
        }
    }
    foreach ($tiposAccion as $codigoAccion => $nombreAccion) {
        $datosGestion = $resumenGestiones[$codigoAccion];
        $ultimaGestion = $datosGestion['ultimaFecha'] === '' ? 'Sin registros' : date('d-m-Y', strtotime($datosGestion['ultimaFecha']));
        $tarjetasDashboardGestiones .= '<article class="cobranzas-gestion-resumen cobranzas-gestion-resumen--' . strtolower($codigoAccion) . '">'
            . '<span>' . $html($nombreAccion) . '</span><strong>' . $datosGestion['cantidad'] . '</strong><small>Última: ' . $html($ultimaGestion) . '</small></article>';
    }
}

$facturas = array();
$acciones = array();
$resumen = array('pendientes' => 0, 'montoPendiente' => 0, 'pagadas' => 0, 'montoPagado' => 0, 'creditadas' => 0, 'montoCreditado' => 0);
if ($rutCliente > 0) {
    $condicionEstado = $filtroEstado === '' ? "AND f.estadoFactura IN ('P', 'C')" : 'AND f.estadoFactura = ?';
    $parametrosFactura = array($rutCliente);
    if ($filtroEstado !== '') {
        $parametrosFactura[] = $filtroEstado;
    }
    $consultaFacturas = $pdo->prepare("SELECT f.id, f.nroFactura, f.fechaFactura, f.montoFactura, f.estadoFactura, f.fechaPago,
        COALESCE(nc.montoNotasCredito, 0) AS montoNotasCredito,
        GREATEST(f.montoFactura - COALESCE(nc.montoNotasCredito, 0), 0) AS saldoPendiente
        FROM adm_facturas f
        LEFT JOIN (
            SELECT idFacturaReferencia, SUM(montoFactura) AS montoNotasCredito
            FROM adm_facturas
            WHERE tipoDocumento = 'NOTA_CREDITO' AND estadoFactura <> 'A'
            GROUP BY idFacturaReferencia
        ) nc ON nc.idFacturaReferencia = f.id
        WHERE f.rutCliente = ? AND f.tipoDocumento = 'FACTURA' $condicionEstado
        ORDER BY FIELD(f.estadoFactura, 'P', 'C'), f.fechaFactura ASC, f.nroFactura ASC");
    $consultaFacturas->execute($parametrosFactura);
    $facturas = $consultaFacturas->fetchAll(PDO::FETCH_ASSOC);

    $consultaResumen = $pdo->prepare("SELECT f.estadoFactura, f.montoFactura,
        COALESCE(nc.montoNotasCredito, 0) AS montoNotasCredito,
        GREATEST(f.montoFactura - COALESCE(nc.montoNotasCredito, 0), 0) AS saldoPendiente
        FROM adm_facturas f
        LEFT JOIN (
            SELECT idFacturaReferencia, SUM(montoFactura) AS montoNotasCredito
            FROM adm_facturas
            WHERE tipoDocumento = 'NOTA_CREDITO' AND estadoFactura <> 'A'
            GROUP BY idFacturaReferencia
        ) nc ON nc.idFacturaReferencia = f.id
        WHERE f.rutCliente = ? AND f.tipoDocumento = 'FACTURA' AND f.estadoFactura IN ('P', 'C')");
    $consultaResumen->execute(array($rutCliente));
    while ($filaResumen = $consultaResumen->fetch(PDO::FETCH_ASSOC)) {
        $creditoAplicado = min((int) $filaResumen['montoNotasCredito'], (int) $filaResumen['montoFactura']);
        if ($creditoAplicado > 0) {
            $resumen['creditadas']++;
            $resumen['montoCreditado'] += $creditoAplicado;
        }
        if ($filaResumen['estadoFactura'] === 'P' && (int) $filaResumen['saldoPendiente'] > 0) {
            $resumen['pendientes']++;
            $resumen['montoPendiente'] += (int) $filaResumen['saldoPendiente'];
        } elseif ($filaResumen['estadoFactura'] === 'C') {
            $resumen['pagadas']++;
            $resumen['montoPagado'] += (int) $filaResumen['saldoPendiente'];
        }
    }

    $consultaAcciones = $pdo->prepare("SELECT a.idAccionCobranza, a.idFactura, a.fechaAccion, a.tipoAccion,
        a.detalle, a.fechaCompromiso, a.compromisoCliente, a.responsable, f.nroFactura
        FROM adm_cobranzas_acciones a
        LEFT JOIN adm_facturas f ON f.id = a.idFactura AND f.rutCliente = a.rutCliente
        WHERE a.rutCliente = ?
        ORDER BY a.fechaAccion DESC, a.idAccionCobranza DESC");
    $consultaAcciones->execute(array($rutCliente));
    $acciones = $consultaAcciones->fetchAll(PDO::FETCH_ASSOC);
}

$opcionesFacturas = '<option value="">Gestión general del cliente</option>';
foreach ($facturas as $factura) {
    $seleccionado = (string) $datosAccion['idFactura'] === (string) $factura['id'] ? ' selected' : '';
    $opcionesFacturas .= '<option value="' . (int) $factura['id'] . '"' . $seleccionado . '>'
        . 'Factura ' . $html($factura['nroFactura']) . ' · saldo $' . number_format((int) $factura['saldoPendiente'], 0, ',', '.')
        . '</option>';
}
$opcionesTipoAccion = '';
foreach ($tiposAccion as $codigo => $nombre) {
    $seleccionado = $datosAccion['tipoAccion'] === $codigo ? ' selected' : '';
    $opcionesTipoAccion .= '<option value="' . $codigo . '"' . $seleccionado . '>' . $nombre . '</option>';
}

$filasFacturas = '';
foreach ($facturas as $factura) {
    $creditoAplicado = (int) $factura['montoNotasCredito'];
    $saldoPendiente = (int) $factura['saldoPendiente'];
    $estado = $factura['estadoFactura'] === 'P' ? 'Pendiente de pago' : 'Pagada';
    $claseEstado = $factura['estadoFactura'] === 'P' ? 'pendiente' : 'pagada';
    $accionFactura = '<span class="cobranzas-sin-accion">Pagada</span>';
    if ($factura['estadoFactura'] === 'P' && $saldoPendiente <= 0) {
        $estado = 'Regularizada con nota de crédito';
        $claseEstado = 'creditada';
        $accionFactura = '<span class="cobranzas-sin-accion">Sin saldo pendiente</span>';
    } elseif ($factura['estadoFactura'] === 'P') {
        $accionFactura = '<form class="cobranzas-pago" method="post" action="cobranzas.php">'
            . '<input name="rutCliente" type="hidden" value="' . $rutCliente . '">'
            . '<input name="idFactura" type="hidden" value="' . (int) $factura['id'] . '">'
            . '<input name="estadoFiltro" type="hidden" value="' . $html($filtroEstado) . '">'
            . '<input name="fechaPago" type="date" value="' . date('Y-m-d') . '" aria-label="Fecha de pago" required>'
            . '<button class="boton boton--guardar" name="marcarFacturaPagada" type="submit">Marcar pagada</button>'
            . '</form>';
    }
    $filasFacturas .= '<tr><td>' . $html($factura['nroFactura']) . '</td>'
        . '<td>' . $html($fechaFactura($factura['fechaFactura'])) . '</td>'
        . '<td class="cobranzas-monto">$' . number_format((int) $factura['montoFactura'], 0, ',', '.') . '</td>'
        . '<td class="cobranzas-monto">$' . number_format($creditoAplicado, 0, ',', '.') . '</td>'
        . '<td class="cobranzas-monto">$' . number_format($saldoPendiente, 0, ',', '.') . '</td>'
        . '<td><span class="cobranzas-estado cobranzas-estado--' . $claseEstado . '">' . $estado . '</span></td>'
        . '<td>' . $html($fechaFactura($factura['fechaPago'])) . '</td>'
        . '<td>' . $accionFactura . '</td></tr>';
}
if ($filasFacturas === '') {
    $filasFacturas = '<tr><td colspan="8" class="cobranzas-vacio">No hay facturas para el estado seleccionado.</td></tr>';
}

$hiloAcciones = '';
foreach ($acciones as $accion) {
    $referencia = $accion['nroFactura'] ? 'Factura ' . $html($accion['nroFactura']) : 'Gestión general';
    $compromiso = '';
    if ($accion['compromisoCliente'] !== '' && $accion['compromisoCliente'] !== null) {
        $compromiso = '<div class="cobranzas-accion__compromiso"><strong>Compromiso:</strong> '
            . nl2br($html($accion['compromisoCliente']))
            . ($accion['fechaCompromiso'] ? ' <span>para ' . $html(date('d-m-Y', strtotime($accion['fechaCompromiso']))) . '</span>' : '')
            . '</div>';
    }
    $hiloAcciones .= '<article class="cobranzas-accion">'
        . '<div class="cobranzas-accion__fecha">' . $html(date('d-m-Y', strtotime($accion['fechaAccion']))) . '</div>'
        . '<div><div><span class="cobranzas-tipo">' . $html($tiposAccion[$accion['tipoAccion']]) . '</span>'
        . '<span class="cobranzas-referencia">' . $referencia . '</span></div>'
        . '<p>' . nl2br($html($accion['detalle'])) . '</p>' . $compromiso
        . '<small>Responsable: ' . $html($accion['responsable']) . '</small></div></article>';
}
if ($hiloAcciones === '') {
    $hiloAcciones = '<p class="cobranzas-vacio">Aún no hay acciones de cobro para este cliente.</p>';
}

$contenido = new plantilla('cobranzas');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'),
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'),
    'topbar' => $topbar,
    'barraLateral' => $barraLateral,
    'headerMenu' => $headerMenu,
    'opcionesClientes' => $opcionesClientes,
    'opcionesFacturas' => $opcionesFacturas,
    'opcionesTipoAccion' => $opcionesTipoAccion,
    'rutCliente' => $rutCliente,
    'estadoFiltro' => $html($filtroEstado),
    'clienteSeleccionado' => $html($clientesPorRut[$rutCliente] ?? ''),
    'contactosCobranzaHTML' => $contactosCobranzaHTML,
    'estadoTodos' => $filtroEstado === '' ? ' selected' : '',
    'estadoPendientes' => $filtroEstado === 'P' ? ' selected' : '',
    'estadoPagadas' => $filtroEstado === 'C' ? ' selected' : '',
    'filasFacturas' => $filasFacturas,
    'hiloAcciones' => $hiloAcciones,
    'pendientes' => $resumen['pendientes'],
    'montoPendiente' => number_format($resumen['montoPendiente'], 0, ',', '.'),
    'pagadas' => $resumen['pagadas'],
    'montoPagado' => number_format($resumen['montoPagado'], 0, ',', '.'),
    'creditadas' => $resumen['creditadas'],
    'montoCreditado' => number_format($resumen['montoCreditado'], 0, ',', '.'),
    'fechaAccion' => $html($datosAccion['fechaAccion']),
    'detalleAccion' => $html($datosAccion['detalle']),
    'fechaCompromiso' => $html($datosAccion['fechaCompromiso']),
    'compromisoCliente' => $html($datosAccion['compromisoCliente']),
    'responsable' => $html($datosAccion['responsable']),
    'mensajeError' => $html($mensajeError),
    'mensajeExito' => $html($mensajeExito),
    'mostrarContenido' => $rutCliente > 0 ? 'block' : 'none',
    'mostrarSeleccion' => $rutCliente > 0 ? 'none' : 'block',
    'mostrarDashboard' => $rutCliente === 0 ? 'block' : 'none',
    'dashboardMontoPendiente' => number_format($dashboard['montoPendiente'], 0, ',', '.'),
    'dashboardFacturasPendientes' => $dashboard['facturasPendientes'],
    'dashboardClientesPendientes' => $dashboard['clientesPendientes'],
    'dashboardFechaMasAntigua' => $dashboard['fechaMasAntigua'] === '' ? 'Sin facturas pendientes' : $fechaFactura($dashboard['fechaMasAntigua']),
    'dashboardMesesAtrasoMaximo' => $dashboard['mesesAtrasoMaximo'],
    'dashboardMayorDeudor' => $html($dashboard['mayorDeudor']),
    'dashboardMontoMayorDeudor' => number_format($dashboard['montoMayorDeudor'], 0, ',', '.'),
    'filasDashboardClientes' => $filasDashboardClientes,
    'filasDashboardAntiguedad' => $filasDashboardAntiguedad,
    'tarjetasDashboardGestiones' => $tarjetasDashboardGestiones,
));
echo $contenido->muestra();
?>
