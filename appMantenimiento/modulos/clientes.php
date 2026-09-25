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
$optionsTPL = $input['ordenarPorOptions'] ?? array();
$arrayCampos = array();
$arrayValores = array();
foreach ($auxiliares as $filtro) {
    $arrayCampos[$filtro['idaux']] = $filtro['campo'];
    $arrayValores[$filtro['idaux']] = $filtro['valor'];
}

$ordenarPorOptions = array('c.cliente', 'c.rut', 'c.vencimientoLic', 'c.prefijoBD', 'c.estadoCliente');
$_ordenarPor = $input['ordenarPor'] ?? ($_SESSION['ordenarPor'] ?? 'c.cliente');
if (!in_array($_ordenarPor, $ordenarPorOptions, true)) { $_ordenarPor = 'c.cliente'; }
$_sentido = $input['sentido'] ?? ($_SESSION['sentido'] ?? 'ASC');
$_sentido = $_sentido === 'DESC' ? 'DESC' : 'ASC';
if (!isset($_SESSION['filas'])) { $_SESSION['filas'] = 15; }
$_filas = $input['filas'] ?? ($_SESSION['filas'] ?? PAGINACION);
$_filas = in_array((int) $_filas, array(8, 10, 12, 15, 20), true) ? (int) $_filas : PAGINACION;
foreach ($ordenarPorOptions as $indice => $campoOrden) { $_ordSel[$indice] = $campoOrden === $_ordenarPor ? 'selected' : ''; }

$arrayCriterios = array(
    1 => array('campo' => 'L,c.rut', 'descripcion' => 'RUT'),
    2 => array('campo' => 'L,c.cliente', 'descripcion' => 'Cliente'),
    3 => array('campo' => 'L,c.prefijoBD', 'descripcion' => 'Prefijo BD'),
);
$criterio = $input['iguala'] ?? ($_SESSION['iguala'] ?? '');
$buscarpor = $input['buscarpor'] ?? ($_SESSION['buscarpor'] ?? 'L,c.cliente');
$_SESSION['iguala'] = $criterio;
$_SESSION['buscarpor'] = $buscarpor;
include(PATH_INCLUDES . '@grillaSentidoFilas.php');
include(PATH_INCLUDES . '@grillaCriterios.php');

$tablaDB = 'adm_clientes';
$IdCampo = 'idCliente';
$esAdministrativo = strtoupper(trim((string) ($_SESSION['tipo'] ?? ''))) === 'A';
$accionesG1 = array(
    array('funcion' => 'editarCliente', 'icono' => 'btn_editar.png', 'titulo' => 'Editar cliente', 'parametros' => 'idCliente', 'paramEstaticos' => '', 'modPHP' => ''),
);
if (!$esAdministrativo) {
    $accionesG1[] = array('funcion' => 'generarLicencia', 'icono' => 'btn_licencia.png', 'titulo' => 'Generar licencia', 'parametros' => 'idCliente', 'paramEstaticos' => '', 'modPHP' => '');
}
$tablaDatos = array(
    'consulta' => "SELECT '' AS colorFila,
        CASE c.estadoCliente
            WHEN 'ACTIVO' THEN '#dff3e3'
            WHEN 'INACTIVO' THEN '#f9dfdf'
            ELSE '#eef2f4'
        END AS colorEstado,
        c.idCliente, c.rut, c.cliente, c.estadoCliente, c.vencimientoLic,
        c.prefijoBD, c.contactoAdmin, c.telefonoContacto
        FROM `$DB_DCODE`.adm_clientes c",
    'ordenarPor' => "$_ordenarPor $_sentido",
    'columnas' => array(
        array('campo' => 'colorFila'),
        array('campo' => 'rut', 'ancho' => '2', 'titulo' => 'RUT', 'alin' => 'R'),
        array('campo' => 'cliente', 'ancho' => '6', 'titulo' => 'Cliente', 'alin' => 'L'),
        array('campo' => 'estadoCliente', 'ancho' => '2', 'titulo' => 'Estado', 'alin' => 'C', 'estado' => true, 'colorEstado' => true),
        array('campo' => 'vencimientoLic', 'ancho' => '2', 'titulo' => 'Vencimiento', 'alin' => 'C', 'date' => true),
        array('campo' => 'prefijoBD', 'ancho' => '2', 'titulo' => 'Prefijo BD', 'alin' => 'L'),
        array('campo' => 'contactoAdmin', 'ancho' => '5', 'titulo' => 'Contacto Admin', 'alin' => 'L'),
        array('campo' => 'telefonoContacto', 'ancho' => '2', 'titulo' => 'Teléfono Contacto', 'alin' => 'L'),
    ),
    'accionesG1' => $accionesG1,
    'setupTabla' => array(
        'funcionBusqueda' => 'filtrarJSON', 'conPaginacion' => true, 'lineasPorPagina' => $_filas,
        'soloConFiltro' => false, 'conFiltro' => true, 'ignorarWhere' => false,
        'accionesGrupo1' => true, 'anchoAccGrupo1' => '2', 'tituloAccGrupo1' => 'Acc.',
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

$contenido = new plantilla('clientes');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR,
    'moduloPHP' => $moduloPHP, 'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'H2Titulo' => 'Mantenedor de clientes',
    'filtrarpor' => $dg_txt->GetDefinition('filtrarpor'), 'htmlCriterios' => $htmlCriterios,
    'pagina' => $_pag, 'iguala' => htmlspecialchars((string) $criterio, ENT_QUOTES, 'UTF-8'),
    'grillaHTMLTit' => $encabezadoHTML, 'grillaHTML' => $filasHTML, 'paginacionHTML' => $paginacionHTML,
    '_async' => $_async, '_ord0' => $_ordSel[0], '_ord1' => $_ordSel[1], '_ord2' => $_ordSel[2], '_ord3' => $_ordSel[3], '_ord4' => $_ordSel[4],
    'sentASC' => $sentASC, 'sentDESC' => $sentDESC, 'fl8' => $fl8, 'fl10' => $fl10, 'fl12' => $fl12, 'fl15' => $fl15, 'fl20' => $fl20,
));
echo $contenido->muestra();
?>
