<?php
    /*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    */
    session_start();

    // ::: Elimina Icono Sistema Lateral
    if (isset($_SESSION['iconosis'])) { unset($_SESSION['iconosis']); }
    
	require_once('defines/variables_path.php');
    require_once(PATH_DEFINES . 'variables.php');
	
	$href = RUTA_ABSOLUTA."index.php"; 
	if (!isset($_SESSION['idUser'])) { header("Location:$href"); }

	require_once(PATH_CLASSES . 'Class.Context.php');
    $dg_txt = new Context();
	$dg_txt->init();

    require_once(PATH_CLASSES . 'Class.Plantilla.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_INCLUDES . 'crypt.php');

    $tituloNavegador = TITULO_NAVEGADOR;
    session_write_close();
    
	if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }
?>