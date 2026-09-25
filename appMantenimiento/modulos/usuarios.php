<?php
require_once('../includes/initSistema.php');
$DB_DCODE = 'adm_dCode';
$filename = str_replace(__DIR__ . '/', '', __FILE__);
$moduloPHP = str_replace('.php', '', $filename);
$conexionDB = new DB_MySQLi;
$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);
$input = json_decode(file_get_contents('php://input'), true);
$input = is_array($input) ? $input : array();
$_pag = $input['pag'] ?? 1; $auxiliares = $input['auxiliares'] ?? array(); $arrayCampos = array(); $arrayValores = array();
foreach ($auxiliares as $filtro) { $arrayCampos[$filtro['idaux']] = $filtro['campo']; $arrayValores[$filtro['idaux']] = $filtro['valor']; }
$ordenarPorOptions = array('u.username', 'u.nombre', 'u.tipoUser', 'u.estado');
$_ordenarPor = $input['ordenarPor'] ?? ($_SESSION['ordenarPor'] ?? 'u.username'); if (!in_array($_ordenarPor, $ordenarPorOptions, true)) { $_ordenarPor = 'u.username'; }
$_sentido = ($input['sentido'] ?? ($_SESSION['sentido'] ?? 'ASC')) === 'DESC' ? 'DESC' : 'ASC'; if (!isset($_SESSION['filas'])) { $_SESSION['filas'] = 15; }
$_filas = $input['filas'] ?? $_SESSION['filas']; $_filas = in_array((int) $_filas, array(8,10,12,15,20), true) ? (int) $_filas : 15;
foreach ($ordenarPorOptions as $indice => $campoOrden) { $_ordSel[$indice] = $campoOrden === $_ordenarPor ? 'selected' : ''; }
$arrayCriterios = array(1 => array('campo' => 'L,u.username', 'descripcion' => 'Usuario'), 2 => array('campo' => 'L,u.nombre', 'descripcion' => 'Nombre'), 3 => array('campo' => 'L,u.email', 'descripcion' => 'Correo'));
$criterio = $input['iguala'] ?? ($_SESSION['iguala'] ?? ''); $buscarpor = $input['buscarpor'] ?? ($_SESSION['buscarpor'] ?? 'L,u.username'); $_SESSION['iguala'] = $criterio; $_SESSION['buscarpor'] = $buscarpor;
include(PATH_INCLUDES . '@grillaSentidoFilas.php'); include(PATH_INCLUDES . '@grillaCriterios.php');
$filtroEstado = $arrayValores['aux1'] ?? ($_SESSION['aux1Valor'] ?? '');
$tablaDB = 'adm_users'; $IdCampo = 'username';
$tablaDatos = array(
    'consulta' => "SELECT u.username, u.nombre, u.email, u.tipoUser, u.estado,
        CASE u.tipoUser WHEN 'D' THEN 'D - Dios' WHEN 'T' THEN 'T - Tecnico' WHEN 'A' THEN 'A - Administrativo' ELSE u.tipoUser END AS tipoUsuarioTexto
        FROM `$DB_DCODE`.adm_users u",
    'ordenarPor' => "$_ordenarPor $_sentido",
    'columnas' => array(array('campo' => 'colorFila'), array('campo' => 'username', 'ancho' => '3', 'titulo' => 'Usuario', 'alin' => 'L'), array('campo' => 'nombre', 'ancho' => '5', 'titulo' => 'Nombre', 'alin' => 'L'), array('campo' => 'email', 'ancho' => '5', 'titulo' => 'Correo', 'alin' => 'L'), array('campo' => 'tipoUsuarioTexto', 'ancho' => '3', 'titulo' => 'Tipo', 'alin' => 'L'), array('campo' => 'estado', 'ancho' => '2', 'titulo' => 'Estado', 'alin' => 'C')),
    'accionesG1' => array(array('funcion' => 'editarUsuario', 'icono' => 'btn_editar.png', 'titulo' => 'Editar usuario', 'parametros' => 'username', 'paramEstaticos' => '', 'modPHP' => '')),
    'setupTabla' => array('funcionBusqueda' => 'filtrarJSON', 'conPaginacion' => true, 'lineasPorPagina' => $_filas, 'soloConFiltro' => false, 'conFiltro' => true, 'ignorarWhere' => false, 'accionesGrupo1' => true, 'anchoAccGrupo1' => '1', 'tituloAccGrupo1' => 'Acc.', 'accionesGrupo2' => false, 'anchoAccGrupo2' => '0', 'tituloAccGrupo2' => '', 'grillaPeq' => false, 'colorEncabezado' => '', 'manuscrito' => false, 'verConsulta' => false),
);
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));
if ($isAjax) { header('Content-Type: text/html; charset=UTF-8'); $_async = 1; include(PATH_INCLUDES . 'grillaLeeRes3.php'); exit; }
$_async = 0; include(PATH_INCLUDES . 'grillaLeeRes3.php');
$contenido = new plantilla('usuarios');
$contenido->asigna_variables(array('lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR, 'moduloPHP' => $moduloPHP, 'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu, 'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'H2Titulo' => 'Mantenedor de usuarios', 'filtrarpor' => $dg_txt->GetDefinition('filtrarpor'), 'htmlCriterios' => $htmlCriterios, 'pagina' => $_pag, 'iguala' => htmlspecialchars((string) $criterio, ENT_QUOTES, 'UTF-8'), 'grillaHTMLTit' => $encabezadoHTML, 'grillaHTML' => $filasHTML, 'paginacionHTML' => $paginacionHTML, 'estadoTodos' => $filtroEstado === '' ? 'selected' : '', 'estadoA' => $filtroEstado === 'A' ? 'selected' : '', 'estadoI' => $filtroEstado === 'I' ? 'selected' : '', '_async' => $_async, '_ord0' => $_ordSel[0], '_ord1' => $_ordSel[1], '_ord2' => $_ordSel[2], '_ord3' => $_ordSel[3], 'sentASC' => $sentASC, 'sentDESC' => $sentDESC, 'fl8' => $fl8, 'fl10' => $fl10, 'fl12' => $fl12, 'fl15' => $fl15, 'fl20' => $fl20));
echo $contenido->muestra();
?>
