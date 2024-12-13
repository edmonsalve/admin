<?php
    session_start();

	require_once('../defines/variables_path.php');
	require_once(PATH_DEFINES . 'variables.php');
	require_once(PATH_CLASSES . 'Class.Context.php');
    
    $dg_txt = new Context();
	$dg_txt->init();

	require_once(PATH_CLASSES . 'Class.Plantilla.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_INCLUDES . 'menuReD-m.php');
	require_once(PATH_INCLUDES . 'userInformacion.php');
    require_once(PATH_INCLUDES . 'top.php');


    $DB_Sistema   = $_GET['db'];
    $tablaDB      = $_GET['tabla'];
    $campoOrigen  = $_GET['campoOrigen'];
    $criterios    = $_GET['criterios'];
    $titulosCrit  = $_GET['titulosCrit'];
    $camposGrilla = $_GET['camposGrilla'];
    $camposAncho  = $_GET['camposAncho'];
    
    /* ::::::::: Determina Nombre de Sript en Ejecucion ::::::::: */
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);


    /* ::::::::::::::::::: Conexión a la BD ::::::::::::::::::::: */
    $conexionDB = new DB_MySQLi;
	$conexionDB->conectar($DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD );
    
    
    

    /* :::::::::::::::::::::::::::::::::::::::: Campos para Grilla  :::::::::::::::::::::::::::::::::::::: */
    $conFicha   = FALSE;
    $conBorrar  = FALSE;
    $conBotAux1 = TRUE;   $funcionAux1 = "encontrado";  $txtBtn1 = "seleccionar";
    $conBotAux2 = FALSE;  $funcionAux2 = "";  $txtBtn2 = "";


    /* :::::::::::::::::::::::::::::::::::::: Criterios de Busqueda  :::::::::::::::::::::::::::::::::::: */
    $arrayCampos  = explode(',',$criterios);  
    $arrayTitulos = explode(',',$titulosCrit);  
     
    $numeroElem = count($arrayCampos) -1;
    
    for($i=0; $i <= $numeroElem; $i++) {
        $j = $i + 1;
        $campo               = $arrayCampos[$i];
        $titulo              = $arrayTitulos[$i];
       
        $arrayCriterios[$j] = array('campo' => $campo,     'descripcion' => $titulo );
        $arrayGrilla[$j]    = array("campo" => $campo,     "titulo" => $titulo,  "ancho" => '3', "alin" => 'L' );
    }

    /* :::::::::::::: Nombre de Tabla y Campo Indice :::::::::::: */
	$IdCampo      = "rut";
    $orderBy      = $arrayCampos[1];
    
    $arrayCamposGrilla  = explode(',',$camposGrilla);
    $arrayCamposAncho   = explode(',',$camposAncho);
    
    $numeroElem = count($arrayCamposGrilla) -1;
    
    for($i=0; $i <= $numeroElem; $i++) {
        if ($i == $numeroElem) { $last = false; } else { $last = true; }
        
        $j = $i+1;
        // echo "<br>  $j ".$arrayCamposGrilla[$i] ." -- ";
        
        $arrayGrilla[$j] = array("campo" => $arrayCamposGrilla[$i], "titulo" => $arrayCamposGrilla[$i], "ancho" => $arrayCamposAncho[$i], "alin" => 'L', "last" => $last );
    }
    
    /*
    $arrayGrilla[1] = array("campo" => 'rut',     "titulo" => $dg_txt->GetDefinition('rut'),     "ancho" => '2', "alin" => 'R' );
    $arrayGrilla[2] = array("campo" => 'paterno', "titulo" => $dg_txt->GetDefinition('paterno'), "ancho" => '4' );
    $arrayGrilla[3] = array("campo" => 'materno', "titulo" => $dg_txt->GetDefinition('materno'), "ancho" => '4' );
    $arrayGrilla[4] = array("campo" => 'nombre',  "titulo" => $dg_txt->GetDefinition('nombre'),  "ancho" => '4', "last" => true);
    */
    
    /* :::::::::::::::::::::::::::::::::::::::::::: Lee Grilla :::::::::::::::::::::::::::::::::::::::::::: */
    include(PATH_INCLUDES.'grillaLee.php');

 

    /* ::::::::::::::::::::::::::::::::::::::::: Carga Plantilla :::::::::::::::::::::::::::::::::::::: */
	$contenido=new plantilla("buscar");
	$contenido->asigna_variables(
					array(
        			'barra_pie_mensajes'  => date('d-m-Y'),
                    'topbar'		      => $topbar,
        			'H2Sistema'			  => '',
                    'H2Titulo'		      => 'Busqueda',
					"enviar"              => $dg_txt->GetDefinition('enviar'),

                    'grillaHTML'     => $grillaHTML,
                    'htmlCriterios'  => $htmlCriterios,
        			'paginacionHTML' => $paginacionHTML,
        			'iguala'		 => $criterio,
                    
                    'DB_Sistema'     => $DB_Sistema,
                    'tablaDB'        => $tablaDB,
                    'campoOrigen'    => $campoOrigen,
                    'criterios'      => $criterios,
                    'titulosCrit'    => $titulosCrit,
					));

	echo $contenido->muestra();
?>