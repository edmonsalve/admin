<?php
require_once('../includes/initSistema.php');

$DB_DCODE = 'adm_dCode';
$filename = str_replace(__DIR__ . '/', '', __FILE__);
$moduloPHP = str_replace('.php', '', $filename);
$conexionDB = new DB_MySQLi;
$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);

$input = json_decode(file_get_contents('php://input'), true);
$input = is_array($input) ? $input : array();
$_pag = $input['pag'] ?? 1;
$auxiliares = $input['auxiliares'] ?? array();
$arrayCampos = array();
$arrayValores = array();
foreach ($auxiliares as $filtro) {
    $arrayCampos[$filtro['idaux']] = $filtro['campo'];
    $arrayValores[$filtro['idaux']] = $filtro['valor'];
}

$ordenarPorOptions = array('f.nroFactura', 'f.fechaFactura', 'c.cliente', 'f.montoFactura', 'f.tipoDocumento', 'f.estadoFactura');
$_ordenarPor = $input['ordenarPor'] ?? ($_SESSION['ordenarPor'] ?? 'f.fechaFactura');
if (!in_array($_ordenarPor, $ordenarPorOptions, true)) { $_ordenarPor = 'f.fechaFactura'; }
$_sentido = ($input['sentido'] ?? ($_SESSION['sentido'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';
if (!isset($_SESSION['filas'])) { $_SESSION['filas'] = 15; }
$_filas = $input['filas'] ?? $_SESSION['filas'];
$_filas = in_array((int) $_filas, array(8, 10, 12, 15, 20), true) ? (int) $_filas : 15;
foreach ($ordenarPorOptions as $indice => $campoOrden) { $_ordSel[$indice] = $campoOrden === $_ordenarPor ? 'selected' : ''; }

$criterio = $input['iguala'] ?? ($_SESSION['iguala'] ?? '');
$buscarpor = 'facturas_busqueda';
$_SESSION['iguala'] = $criterio;
$_SESSION['buscarpor'] = $buscarpor;

$filtroEstado = $arrayValores['aux1'] ?? ($_SESSION['aux1Valor'] ?? '');
$filtroCliente = $arrayValores['aux2'] ?? ($_SESSION['aux2Valor'] ?? '');
$filtroTipoDocumento = $arrayValores['aux3'] ?? ($_SESSION['aux3Valor'] ?? '');
$opcionesClientes = '<option value="">Todos</option>';
$salidaClientes = $conexionDB->consulta("SELECT rut, cliente FROM `$DB_DCODE`.adm_clientes ORDER BY cliente");
while ($cliente = mysqli_fetch_assoc($salidaClientes)) {
    $rutNumerico = preg_replace('/\D.*/', '', (string) ($cliente['rut'] ?? ''));
    if ($rutNumerico === '') { continue; }
    $seleccionado = (string) $rutNumerico === (string) $filtroCliente ? ' selected' : '';
    $opcionesClientes .= '<option value="' . htmlspecialchars($rutNumerico, ENT_QUOTES, 'UTF-8') . '"' . $seleccionado . '>'
        . htmlspecialchars((string) $cliente['cliente'], ENT_QUOTES, 'UTF-8') . '</option>';
}

$tablaDB = 'adm_facturas';
$IdCampo = 'id';
$tablaDatos = array(
    'consulta' => "SELECT '' AS colorFila, f.id, f.nroFactura, f.fechaFactura, f.rutCliente, f.montoFactura, f.tipoDocumento,
        f.idFacturaReferencia, f.estadoFactura, f.fechaPago,
        COALESCE(c.cliente, CONCAT('RUT ', f.rutCliente)) AS cliente,
        CASE f.tipoDocumento WHEN 'NOTA_CREDITO' THEN 'Nota de crédito' ELSE 'Factura' END AS tipoDocumentoTexto,
        CASE WHEN f.tipoDocumento = 'NOTA_CREDITO' THEN CONCAT('Factura ', COALESCE(facturaReferencia.nroFactura, 'sin referencia')) ELSE '-' END AS referenciaTexto,
        CASE
            WHEN f.tipoDocumento = 'NOTA_CREDITO' AND f.estadoFactura <> 'A' THEN 'Nota de crédito vigente'
            WHEN f.tipoDocumento = 'NOTA_CREDITO' THEN 'Nota de crédito anulada'
            WHEN f.estadoFactura = 'P' THEN 'Pendiente'
            WHEN f.estadoFactura = 'C' THEN 'Cancelada / pagada'
            WHEN f.estadoFactura = 'A' THEN 'Anulada'
            ELSE f.estadoFactura
        END AS estadoTexto
        FROM `$DB_DCODE`.adm_facturas f
        LEFT JOIN `$DB_DCODE`.adm_clientes c ON CAST(SUBSTRING_INDEX(c.rut, '-', 1) AS UNSIGNED) = f.rutCliente
        LEFT JOIN `$DB_DCODE`.adm_facturas facturaReferencia ON facturaReferencia.id = f.idFacturaReferencia",
    'ordenarPor' => "$_ordenarPor $_sentido",
    'columnas' => array(
        array('campo' => 'colorFila'),
        array('campo' => 'nroFactura', 'ancho' => '2', 'titulo' => 'N° factura', 'alin' => 'R'),
        array('campo' => 'tipoDocumentoTexto', 'ancho' => '2', 'titulo' => 'Tipo', 'alin' => 'L'),
        array('campo' => 'fechaFactura', 'ancho' => '2', 'titulo' => 'Fecha emisión', 'alin' => 'C'),
        array('campo' => 'cliente', 'ancho' => '6', 'titulo' => 'Cliente', 'alin' => 'L'),
        array('campo' => 'referenciaTexto', 'ancho' => '2', 'titulo' => 'Factura afectada', 'alin' => 'L'),
        array('campo' => 'montoFactura', 'ancho' => '2', 'titulo' => 'Monto', 'alin' => 'R', 'numform' => true),
        array('campo' => 'estadoTexto', 'ancho' => '3', 'titulo' => 'Estado', 'alin' => 'L'),
        array('campo' => 'fechaPago', 'ancho' => '2', 'titulo' => 'Fecha pago', 'alin' => 'C'),
    ),
    'accionesG1' => array(
        array('funcion' => 'editarFactura', 'icono' => 'btn_editar.png', 'titulo' => 'Editar factura', 'parametros' => 'id', 'paramEstaticos' => '', 'modPHP' => ''),
    ),
    'setupTabla' => array(
        'funcionBusqueda' => 'filtrarJSON', 'conPaginacion' => true, 'lineasPorPagina' => $_filas,
        'camposBusquedaCalculada' => array(
            'facturas_busqueda' => "CONCAT_WS(' ', f.nroFactura, f.rutCliente, c.cliente)",
        ),
        'soloConFiltro' => false, 'conFiltro' => true, 'ignorarWhere' => false,
        'accionesGrupo1' => true, 'anchoAccGrupo1' => '1', 'tituloAccGrupo1' => 'Acc.',
        'accionesGrupo2' => false, 'anchoAccGrupo2' => '0', 'tituloAccGrupo2' => '',
        'grillaPeq' => false, 'colorEncabezado' => '', 'manuscrito' => false, 'verConsulta' => false,
    ),
);

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));
if ($isAjax) {
    header('Content-Type: text/html; charset=UTF-8');
    $_async = 1;
    include(PATH_INCLUDES . 'grillaLeeRes3.php');
    exit;
}
$_async = 0;
include(PATH_INCLUDES . 'grillaLeeRes3.php');

$contenido = new plantilla('facturas');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR,
    'moduloPHP' => $moduloPHP, 'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'H2Titulo' => 'Consulta de facturas',
    'pagina' => $_pag, 'iguala' => htmlspecialchars((string) $criterio, ENT_QUOTES, 'UTF-8'),
    'grillaHTMLTit' => $encabezadoHTML, 'grillaHTML' => $filasHTML, 'paginacionHTML' => $paginacionHTML,
    'opcionesClientes' => $opcionesClientes,
    'estadoTodos' => $filtroEstado === '' ? 'selected' : '', 'estadoP' => $filtroEstado === 'P' ? 'selected' : '',
    'estadoC' => $filtroEstado === 'C' ? 'selected' : '', 'estadoA' => $filtroEstado === 'A' ? 'selected' : '',
    'tipoTodos' => $filtroTipoDocumento === '' ? 'selected' : '', 'tipoFactura' => $filtroTipoDocumento === 'FACTURA' ? 'selected' : '',
    'tipoNotaCredito' => $filtroTipoDocumento === 'NOTA_CREDITO' ? 'selected' : '',
    '_async' => $_async, '_ord0' => $_ordSel[0], '_ord1' => $_ordSel[1], '_ord2' => $_ordSel[2], '_ord3' => $_ordSel[3], '_ord4' => $_ordSel[4], '_ord5' => $_ordSel[5],
    'sentASC' => $sentASC, 'sentDESC' => $sentDESC, 'fl8' => $fl8, 'fl10' => $fl10, 'fl12' => $fl12, 'fl15' => $fl15, 'fl20' => $fl20,
));
echo $contenido->muestra();
?>
