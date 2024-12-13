<?php
	session_start();
	if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }
	
	/* :::::::::::::::::::::: VARIABLES GENERALES  ::::::::::::::::::::::: */
	require_once('../../defines/variables_path.php');
	require_once(PATH_DEFINES . '/variables.php');
	
	/* ::::::::::::::::::::::: VARIABLES SISTEMA  :::::::::::::::::::::::: */
	require_once('../includes/variablesSistema.php');
	require_once('../../includes/controlAcceso.php');
	if (!$permiteAcceso) { header("Location:main.php"); }

	require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQL.php'); 
	
	/* ::::::::::::: Diccionario General y de Sistema :::::::::::::::::::: */
	$red_text = new Context();
	$red_text->init();
	
	$sistema_txt = new Context();
	$sistema_txt->init();
	
    require_once(PATH_INCLUDES . 'top.php');
	require_once(PATH_INCLUDES . 'informacionUser.php');
    require_once(PATH_INCLUDES . 'funciones.php');
	require_once(PATH_CLASSES_SISTEMA .  'Class.Plantilla.php');   
	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_LANGUAGE_SISTEMA . 'spanish.php');
?>
