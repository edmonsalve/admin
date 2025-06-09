<?php 
    ini_set('Display_errors', 'On'); 
	session_start();
    $id_session = session_id();
    
	require_once('defines/variables_path.php');
	//require_once(PATH_INCLUDES . 'redm.php');
    require_once(PATH_DEFINES .  'variables.php');
	require_once(PATH_INCLUDES . 'crypt.php'); 
    
	require_once(PATH_CLASSES . 'Class.Plantilla.php');   
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php'); 
	require_once(PATH_CLASSES . 'Class.Context.php');
	
	$dg_txt = new Context();
	$dg_txt->init();
	require_once(PATH_LANGUAGE . 'spanish.php');


    $hoy = date('Y-m-d');

    // ::::::::::::::::::::::::::::::::::::::::: Carga Plantilla :::::::::::::::::::::::::::::::::::::: //
	$contenido=new plantilla("index");
	$contenido->asigna_variables(
					array(
					"titulo"  => 'dCode Administración',
					"icono"	  => PATH_ICO.ICONO_NAVEGADOR, 
					"logo"    => LOGOH,
					"enviar"  => 'Enviar',
                    "msgMora" => '',
					));
			
	echo $contenido->muestra();  
	echo "alooo!";
?>