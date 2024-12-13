<?php 
	$menuTrabajoHTML = '
		<div class="span-3">
			<a href="/@admin/modulos/clientes.php" target="_top" style="text-decoration:none; ">
			  <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="/images/icon-clientes.png" width="80" height="96" alt="Clientes"/>
			</a>
		</div>
		<div class="span-3 prepend-1">
			<a href="/@admin/modulos/sistemas.php" target="_top" style="text-decoration:none; ">
			  <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="/images/icon-sistemas.png" width="80" height="96" alt="Sistemas"/>
			</a>
		</div>
		<div class="span-3">
			<a href="/@admin/modulos/compMenus.php" target="_top" style="text-decoration:none; ">
			  <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="/images/ico_menus.png" width="80" height="96" alt="Menus"/>
			</a>
		</div>
    ';
	
	ini_set('Display_errors', 'On'); 
	session_start();
    $id_session = session_id();
    
	require_once('defines/variables_path.php');
	// require_once(PATH_INCLUDES . 'redm.php');
    require_once(PATH_DEFINES .  'variables.php');
	require_once(PATH_INCLUDES . 'crypt.php'); 
    
	require_once(PATH_CLASSES . 'Class.Plantilla.php');   
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php'); 
	require_once(PATH_CLASSES . 'Class.Context.php');
	
	$dg_txt = new Context();
	$dg_txt->init();
	require_once(PATH_LANGUAGE . 'spanish.php');

	require_once(PATH_INCLUDES . 'top.php');
	
	$enlace = "http://admin.red-m.cl/index_main.php";
	require_once(PATH_INCLUDES . 'userInformacion.php');

    $hoy = date('Y-m-d');

    // ::::::::::::::::::::::::::::::::::::::::: Carga Plantilla :::::::::::::::::::::::::::::::::::::: //
	$contenido=new plantilla("index_main");
	$contenido->asigna_variables(
					array(
					"titulo"  			=> TITULO_NAVEGADOR,
					"enviar"  			=> 'enviar',
                    "msgMora" 			=> '',
					"topbar"			=> $topbar,
					"menuTrabajoHTML"	=> $menuTrabajoHTML,
					"lateral"			=> $lateral,
					));
			
	echo $contenido->muestra();  
?>