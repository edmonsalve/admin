<?php  
	/* ============================= Lee Sistemas  ======================================== */
    $menuTrabajoHTML = '
		<div class="span-4">
			<a href="modulos/sistemas.php" target="_top" style="text-decoration:none; ">**
			  <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="../images/icon-sistemas.png" width="80" height="96" alt="Sistemas"/>
			</a>
		</div>
		<div class="span-3">
			<a href="modulos/clientes.php" target="_top" style="text-decoration:none; ">
			  <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="../images/icon-clientes.png" width="80" height="96" alt="Clientes"/>
			</a>
		</div>
		<div class="span-3">
			<a href="modulos/compMenus.php" target="_top" style="text-decoration:none; ">
			  <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="../images/ico_menus.png" width="80" height="96" alt="Menus"/>
			</a>
		</div>
    ';
	
	session_start();
	if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }


	/* :::::::::::::::::::::: VARIABLES GENERALES  ::::::::::::::::::::::: */
	require_once('../defines/variables_path.php');
	require_once(PATH_DEFINES . 'variables.php');

	/* ::::::::::::::::::::::: VARIABLES SISTEMA  :::::::::::::::::::::::: */
	require_once('includes/variablesSistema.php');
	
	require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');
	require_once(PATH_CLASSES_SISTEMA .  'Class.Plantilla.php');

	/* ::::::::::::: Diccionario General y de Sistema :::::::::::::::::::: */
	$dg_txt = new Context();
	$dg_txt->init();

	$sistema_txt = new Context();
	$sistema_txt->init();

	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_LANGUAGE_SISTEMA . 'spanish.php');
	
	/* ::::::::::::::::::::::   Barra Lateral, Superior e Inferior   :::::::::::::::::::::::: */
	require_once(PATH_INCLUDES . 'top.php');
	
	$enlace = "../index_main.php";
	require_once(PATH_INCLUDES . 'userInformacion.php');
	
	
	$contenido=new plantilla("index");
	$contenido->asigna_variables(
					array(
                    "tituloNav"     	=> TITULO_ADMIN." **",
                    "topbar"			=> $topbar,
                    "bottombar"			=> $bottombar,
					"sistema"			=> $nsistema,
					"lateral"			=> $lateral,
					"H2titulo"			=> $sistema_txt->GetDefinition('H2titulo'),
					"menuTrabajoHTML"   => $menuTrabajoHTML,
                    "cerrarSesion"		=> $dg_txt->GetDefinition('cerrarSesion'),
					));

	echo $contenido->muestra();
?>
