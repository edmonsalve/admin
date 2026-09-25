<?php 
	session_start();
	
	// :::::::::::::::::::::: VARIABLES GENERALES  ::::::::::::::::::::::: //
	require_once('../../defines/variables_path.php');	
	require_once(PATH_DEFINES . '/variables.php');

	$href = RUTA_ABSOLUTA."index.php"; 
	if (!isset($_SESSION['idUser'])) { header("Location:$href"); }    


	// ::::::::::::::::::::::: VARIABLES SISTEMA  :::::::::::::::::::::::: //
	require_once('../includes/variablesSistema.php');
	require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

	// ::::::::::::: Diccionario General y de Sistema :::::::::::::::::::: //
	$dg_txt = new Context();
	$dg_txt->init();

	$sistema_txt = new Context();
	$sistema_txt->init();

	require_once(PATH_INCLUDES . 'controlAcceso.php');  	
    require_once(PATH_INCLUDES . 'top.php');
	require_once(PATH_INCLUDES . 'barraLateral.php');
    require_once(PATH_INCLUDES . 'funciones.php');
	require_once(PATH_CLASSES_SISTEMA .  'Class.Plantilla.php');
	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_LANGUAGE_SISTEMA . 'spanish.php');

	// :: Menu
	include(PATH_INCLUDES.'leeModulosIco.php'); 
?>
