<?php 
	require_once('../includes/initSistema.php');
	
	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_COMUN, DB_SERVER, DB_USER, DB_PASSWD );
    
	$dBase  	  		= DB_COMUN;
    $tablaDB	  		= "comun_tabCajas";
	$IdCampo	  		= "id";
	$phpFile 			= "comunCajas";
	$orderBy			= "entidad";
    
    $tabMaster          =  TAB_MASTER;
	$indMaster          = 'id';
	
	$campoId 		= 'new';
	$_entidad 	    = '';
    $_factor     	= '';
    $_cuenta      	= '';
    $_cod_previred  = '';
	
	if (isset($_POST['entidad'])) { require_once(PATH_INCLUDES.'tab@guardar.php'); }  
	
	if (isset($_GET['ope'])) { $operacion = $_GET['ope']; }  else { $operacion =''; }
	switch ($operacion) {
		case 'Update': 	$campoId  = $_GET['IdRegistro'];  
						$consulta = "SELECT *   FROM `$tablaDB` WHERE `$IdCampo` = $campoId ";
						$salida   = $conexionDB->consulta($consulta);
						$row = mysql_fetch_array($salida);
						$_entidad	    = $row ['entidad'];
                        $_factor        = $row ['factor'];
                        $_cuenta        = $row ['cuenta'];
                        $_cod_previred  = $row ['cod_previred'];
                        
						break;
						
		case 'Delete': 	$locationFile = $_SERVER['SCRIPT_NAME'];
						$errorMensaje = "ERROR_borrarTabCajas";
						require_once(PATH_INCLUDES.'tab@borrar2.php');  
						break;
	}

	/* :::::::::::::::::::::::::::::::::::::::::::: INICIO Lee Grilla ::::::::::::::::::::::::::::::::::::: */
	$titulos[1] = array("tit" => $sistema_txt->GetDefinition('id'),        "ancho" => "span-1 enc_mensajes");
	$titulos[2] = array("tit" => $sistema_txt->GetDefinition('entidad'), "ancho" => "span-7 enc_mensajes last");
	
	$fila[1] = array("campo" => "$IdCampo",   "ancho" => "span-1");
	$fila[2] = array("campo" => "$orderBy",   "ancho" => "span-7 last");
   
	require_once(PATH_INCLUDES.'tab@lee.php');
	/* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Grilla :::::::::::::::::::::::::::::::::::::::: */
	
	
	$contenido=new plantilla("comunCajas");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
			"lateral"			  => $lateral,
            "topbar"		      => $topbar,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
            "H2Titulo"		      => $sistema_txt->GetDefinition('H2Cajas'),
            "TInforme"		      => $sistema_txt->GetDefinition('TINFCajas'),
            "salir"		          => $sistema_txt->GetDefinition('salir'),
            "buscarpor"		      => $sistema_txt->GetDefinition('buscarpor'),
            "dBase"               => $dBase,
            "tablaDB"             => $tablaDB,
			"phpFile"		 	  => $phpFile,
			"IdCampo"		 	  => $IdCampo,
            "campoOrd"            => $orderBy,
			
			"id"            => $sistema_txt->GetDefinition('id'),
			"entidad"       => $sistema_txt->GetDefinition('entidad'),
            "factor"        => $sistema_txt->GetDefinition('factor'),
            "cuenta"        => $sistema_txt->GetDefinition('cuenta'),
            "cod_previred"  => $sistema_txt->GetDefinition('cod_previred'),
			"borrar"        => $sistema_txt->GetDefinition('borrar'),
			"enviar"        => $sistema_txt->GetDefinition('enviar'),
			"limpiar"       => $sistema_txt->GetDefinition('limpiar'),
			"tablaHTML"	    => $tablaHTML,

			"campoId"	     => $campoId,   
			"_entidad"       => $_entidad,
            "_factor"        => $_factor,
            "_cuenta"        => $_cuenta,
            "_cod_previred"  => $_cod_previred
			));
			
	echo $contenido->muestra();
?>
