<?php
	session_start();
	if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }
	
	/* :::::::::::::::::::::::::::: Elimina Icono Sistema Lateral :::::::::::::::::::::::::*/
    if (isset($_SESSION['iconosis'])) { unset($_SESSION['iconosis']); }
    

	require_once('defines/variables_path.php');
	require_once(PATH_DEFINES . 'variables.php');

	require_once(PATH_CLASSES . 'Class.Context.php');
    $dg_txt = new Context();
	$dg_txt->init();

	require_once(PATH_CLASSES . 'Class.Plantilla.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_INCLUDES . 'menuReD-m.php');
	require_once(PATH_INCLUDES . 'userInformacion.php');
	require_once(PATH_INCLUDES . 'crypt.php');

    $tituloNavegador = TITULO_NAVEGADOR;
?>
