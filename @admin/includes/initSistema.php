<?php  
	session_start();
	// if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }
	
	/* :::::::::::::::::::::: VARIABLES GENERALES  ::::::::::::::::::::::: */
	require_once(PATH_DEFINES . 'variables.php');
	
	/* ::::::::::::::::::::::: VARIABLES SISTEMA  :::::::::::::::::::::::: */
	require_once('variablesSistema.php');


	require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php'); 
	
	/* ::::::::::::: Diccionario General y de Sistema :::::::::::::::::::: */
	$dg_txt = new Context();
	$dg_txt->init(); 
	
	$sistema_txt = new Context();
	$sistema_txt->init();
	
    require_once(PATH_INCLUDES . 'top.php');
	require_once(PATH_INCLUDES . 'userInformacion.php');
	require_once(PATH_CLASSES_SISTEMA .  'Class.Plantilla.php');   
	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_LANGUAGE_SISTEMA . 'spanish.php');
?>