<?php
	session_start();
	if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }
	$idsistema = 999;
    
	/* :::::::::::::::::::::: VARIABLES GENERALES  ::::::::::::::::::::::: */
	require_once('../defines/variables_path.php');
	require_once(PATH_DEFINES . 'variables.php');
	
	/* ::::::::::::::::::::::: VARIABLES SISTEMA  :::::::::::::::::::::::: */
	require_once('includes/variablesSistema.php');
	require_once('../includes/controlAcceso.php');
	if (!$permiteAcceso) { header("Location:index_main.php"); }

	require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQL.php'); 
	require_once(PATH_CLASSES_SISTEMA .  'Class.Plantilla.php');
    
	/* ::::::::::::: Diccionario General y de Sistema :::::::::::::::::::: */
	$red_text = new Context();
	$red_text->init();
	
	$sistema_txt = new Context();
	$sistema_txt->init();
	
	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_LANGUAGE_SISTEMA . 'spanish.php');
	
	/* ::::::::::::::::::::::::::::::: Identificación de Sistema :::::::::::::::::::::::::::: */
	if	(isset($_GET['idsistema'])) {
		$sistema 					= $idsistema;
		$areasistema 				= 'A';
		$_SESSION['idsistema'] 		= $idsistema;
		$_SESSION['areasistema']	= 'A';
		} else {
		$sistema 		= $idsistema;
		$areasistema 	= 'A';
	}
	
	$user 		= $_SESSION['idUser'];
	$nombre		= $_SESSION['usuario'];
	
	
	/* ============================= Lee desde Base Principal =============================== */
	$conexionReD = new DB_MySQL;
	$conexionReD->conectar(DB_NAME, DB_SERVER, DB_USER, DB_PASSWD );
	require_once(PATH_INCLUDES . 'leeModulos.php');
	
	/* ============================= Selecciona Base del Sistema  ============================ */
	$conexionSistemaDB = new DB_MySQL;
	$conexionSistemaDB->conectar(DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD );
	
    
    /* ::::::::::::::::::::::   Barra Lateral, Superior e Inferior   :::::::::::::::::::::::: */
	require_once(PATH_INCLUDES . 'top.php');
	require_once(PATH_INCLUDES . 'informacionUser.php');
    
    $contenido=new plantilla("index_main");
	$contenido->asigna_variables(
					array(
                    "tituloNav"     => TITULO_NAV,
                    "topbar"		=> $topbar,
                    "bottombar"		=> $bottombar,
					"sistema"		=> $nsistema, 
					"lateral"		=> $lateral,
					"H2titulo"		=> $sistema_txt->GetDefinition('H2titulo'),
					"menuSistema"   => $menusistemaHTML,
                    "cerrarSesion"	=> $red_text->GetDefinition('cerrarSesion'),

					));
				
	echo $contenido->muestra();
?>
