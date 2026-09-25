<?php
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	session_start();

	if (isset($idModuloIco)) { $_SESSION['idModuloIco'] = $idModuloIco; } 
    if (isset($_GET['mod'])) { $_SESSION['modulo'] = $_GET['mod']; }

	// :::::::::::::::::::::: VARIABLES GENERALES  ::::::::::::::::::::::: //
	require_once('../../defines/variables_path.php');
	require_once(PATH_DEFINES . '/variables.php');

	$href = '/index.php';
	if (!isset($_SESSION['idUser'])) { header("Location:$href"); exit; }

	/*
	 * Perfiles del administrador central:
	 * D = Dios (todos los mantenedores), T = Técnico y A = Administrativo.
	 * Se valida aquí, antes de cargar cada módulo, para impedir acceso por URL directa.
	 */
	$tipoUsuario = strtoupper(trim((string) ($_SESSION['tipo'] ?? '')));
	$archivoActual = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
	$modulosPorPerfil = array(
		'T' => array('sistemas.php', 'sistemas_ficha.php', 'sistemas_modulos.php', 'sistemas_archivo.php', 'servidores.php', 'sincronizar.php', 'comparar_estructuras.php', 'bitacora_desarrollo.php', 'bitacora_desarrollo_pdf.php'),
		'A' => array('clientes.php', 'clientes_ficha.php', 'licencia_calculo.php', 'facturas.php', 'facturas_ficha.php', 'facturas_archivo.php', 'facturas_informe_tecnico.php', 'cobranzas.php', 'cobranzas_pendientes_pdf.php'),
	);
	if (!in_array($tipoUsuario, array('D', 'T', 'A'), true) ||
		($tipoUsuario !== 'D' && !in_array($archivoActual, $modulosPorPerfil[$tipoUsuario], true))) {
		header('Location: /appMantenimiento/index_main.php');
		exit;
	}
	

	// ::::::::::::::::::::::: VARIABLES SISTEMA  :::::::::::::::::::::::: //
	require_once('../includes/variablesSistema.php');
	require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

	// ::::::::::::: Diccionario General y de Sistema :::::::::::::::::::: ///
	$dg_txt = new Context();
	$dg_txt->init();

	$sistema_txt = new Context();
	$sistema_txt->init();

	require_once(PATH_INCLUDES . 'controlAcceso.php');
    require_once(PATH_INCLUDES . 'top.php');
    require_once(PATH_INCLUDES . 'funciones.php');
	require_once(PATH_CLASSES_SISTEMA .  'Class.Plantilla.php');
	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_LANGUAGE_SISTEMA . 'spanish.php');

	if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }

	// El menú se muestra en la barra superior común del módulo.
	$headerMenu = '';
	$barraLateral = '';
?>
