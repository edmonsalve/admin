<?php 
	require_once('../includes/initSistema.php');

	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_COMUN, DB_SERVER, DB_USER, DB_PASSWD );
	
	$dBase  	  		= DB_COMUN;
    $tablaDB	  		= "comun_tabUtm";
	$IdCampo	  		= "id";
	$phpFile 			= "comunUtm";
	$orderBy			= "aaaa";
	
	$campoId = 'new';
    $DESC    ='DESC';
	$_aaaa   = '';
    $_utm_01 = 0;
    $_utm_02 = 0;
    $_utm_03 = 0;
    $_utm_04 = 0;
    $_utm_05 = 0;
    $_utm_06 = 0;
    $_utm_07 = 0;
    $_utm_08 = 0;
    $_utm_09 = 0;
    $_utm_10 = 0;
    $_utm_11 = 0;
    $_utm_12 = 0;
    $_ipc_01  = '';
    $_ipc_02  = '';
    $_ipc_03  = '';
    $_ipc_04  = '';
    $_ipc_05  = '';
    $_ipc_06  = '';
    $_ipc_07  = '';
    $_ipc_08  = '';
    $_ipc_09  = '';
    $_ipc_10 = '';
    $_ipc_11 = '';
    $_ipc_12 = '';
    
    
	
	if (isset($_POST['aaaa'])) { require_once(PATH_INCLUDES.'tab@guardar.php'); } 
	
	if (isset($_GET['ope'])) { $operacion = $_GET['ope']; }  else { $operacion =''; }
	switch ($operacion) {
		case 'Update': 	$campoId  = $_GET['IdRegistro'];  
						$consulta = "SELECT *   FROM `$tablaDB` WHERE `$IdCampo` = $campoId ";
						$salida   = $conexionDB->consulta($consulta);
						$row = mysql_fetch_array($salida);
						$_aaaa  = $row ['aaaa'];
                        $_utm_01 = $row ['utm_01'];
                        $_utm_02 = $row ['utm_02'];
                        $_utm_03 = $row ['utm_03'];
                        $_utm_04 = $row ['utm_04'];
                        $_utm_05 = $row ['utm_05'];
                        $_utm_06 = $row ['utm_06'];
                        $_utm_07 = $row ['utm_07'];
                        $_utm_08 = $row ['utm_08'];
                        $_utm_09 = $row ['utm_09'];
                        $_utm_10 = $row ['utm_10'];
                        $_utm_11 = $row ['utm_11'];
                        $_utm_12 = $row ['utm_12'];
                        $_utm_12 = $row ['utm_12'];
                        
                        $_ipc_01 = $row ['ipc_01'];                      
                        $_ipc_02 = $row ['ipc_02'];
                        $_ipc_03 = $row ['ipc_03'];
                        $_ipc_04 = $row ['ipc_04'];
                        $_ipc_05 = $row ['ipc_05'];
                        $_ipc_06 = $row ['ipc_06'];
                        $_ipc_07 = $row ['ipc_07'];
                        $_ipc_08 = $row ['ipc_08'];
                        $_ipc_09 = $row ['ipc_09'];
                        $_ipc_10 = $row ['ipc_10'];
                        $_ipc_11 = $row ['ipc_11'];
                        $_ipc_12 = $row ['ipc_12'];
						break;
						
		case 'Delete': 	$locationFile = $_SERVER['SCRIPT_NAME'];
						$errorMensaje = "ERROR_borrarTabUtm";
						require_once(PATH_INCLUDES.'tab@borrar.php');  
						break;
	}

	/* :::::::::::::::::::::::::::::::::::::::::::: INICIO Lee Grilla ::::::::::::::::::::::::::::::::::::: */
	//*$titulos[1] = array("tit" => $sistema_txt->GetDefinition('id'),   "ancho" => "span-1 enc_mensajes");
    $titulos[1] = array("tit" => $sistema_txt->GetDefinition('aaaa'), "ancho" => "span-8 centro enc_mensajes");
    	
	$fila[1] = array("campo" => "aaaa","ancho" => "span-3", "pre" => 'AÑO ', "post" => "" );
   //* $fila[2] = array("campo" => "$orderBy","ancho" => "span-7 last");
    
   
   
	require_once(PATH_INCLUDES.'tab@lee.php');
	/* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Grilla :::::::::::::::::::::::::::::::::::::::: */
	
	
	$contenido=new plantilla("comunUtm");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
			"lateral"			  => $lateral,
            "topbar"		      => $topbar,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
            "H2Titulo"		      => $sistema_txt->GetDefinition('H2Utm'),
            "TInforme"		      => $sistema_txt->GetDefinition('TINFUtm'),
            "salir"		          => $sistema_txt->GetDefinition('salir'),
            "buscarpor"		      => $sistema_txt->GetDefinition('buscarpor'),
            "dBase"               => $dBase,
            "tablaDB"             => $tablaDB,
			"phpFile"		 	  => $phpFile,
			"IdCampo"		 	  => $IdCampo,
            "campoOrd"            => $orderBy,
			
			"id"          => $sistema_txt->GetDefinition('id'),
            "aaaa"        => $sistema_txt->GetDefinition('aaaa'),
			"ene"         => $sistema_txt->GetDefinition('ene'),
            "feb"         => $sistema_txt->GetDefinition('feb'),
            "mar"         => $sistema_txt->GetDefinition('mar'),
            "abr"         => $sistema_txt->GetDefinition('abr'),
            "may"         => $sistema_txt->GetDefinition('may'),
            "jun"         => $sistema_txt->GetDefinition('jun'),
            "jul"         => $sistema_txt->GetDefinition('jul'),
            "ago"         => $sistema_txt->GetDefinition('ago'),
            "sep"         => $sistema_txt->GetDefinition('sep'),
            "oct"         => $sistema_txt->GetDefinition('oct'),
            "nov"         => $sistema_txt->GetDefinition('nov'),
            "dic"         => $sistema_txt->GetDefinition('dic'),
            "utm"         => $sistema_txt->GetDefinition('utm'),
            "ipc"         => $sistema_txt->GetDefinition('ipc'),
            "borrar"      => $sistema_txt->GetDefinition('borrar'),
			"enviar"      => $sistema_txt->GetDefinition('enviar'),
			"limpiar"     => $sistema_txt->GetDefinition('limpiar'),
			"tablaHTML"	  => $tablaHTML,

			"campoId"	  => $campoId,   
			"_aaaa"       => $_aaaa,
            "_utm_01"     => $_utm_01,
            "_utm_02"     => $_utm_02,
            "_utm_03"     => $_utm_03,
            "_utm_04"     => $_utm_04,
            "_utm_05"     => $_utm_05,
            "_utm_06"     => $_utm_06,
            "_utm_07"     => $_utm_07,
            "_utm_08"     => $_utm_08,
            "_utm_09"     => $_utm_09,
            "_utm_10"     => $_utm_10,
            "_utm_11"     => $_utm_11,
            "_utm_12"     => $_utm_12,
            "_ipc_01"     => $_ipc_01,
            "_ipc_02"     => $_ipc_02,
            "_ipc_03"     => $_ipc_03,
            "_ipc_04"     => $_ipc_04,
            "_ipc_05"     => $_ipc_05,
            "_ipc_06"     => $_ipc_06,
            "_ipc_07"     => $_ipc_07,
            "_ipc_08"     => $_ipc_08,
            "_ipc_09"     => $_ipc_09,
            "_ipc_10"     => $_ipc_10,
            "_ipc_11"     => $_ipc_11,
            "_ipc_12"     => $_ipc_12
            ));
			
	echo $contenido->muestra();
?>
