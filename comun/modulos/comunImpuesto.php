<?php 
	require_once('../includes/initSistema.php');
	
	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_COMUN, DB_SERVER, DB_USER, DB_PASSWD );
    
	$dBase  	  		= DB_COMUN;
    $tablaDB	  		= "comun_tabImpuesto";
	$IdCampo	  		= "id";
	$phpFile 			= "comunImpuesto";
	$orderBy			= "id";
    
    $tabMaster          =  TAB_MASTER;
	$indMaster          = 'id';
	
	$campoId 		= 'new';
	$_desde      	= '';
    $_hasta      	= '';
    $_factor     	= '';
    $_rebaja     	= '';
	
	if (isset($_POST['id'])) { require_once(PATH_INCLUDES.'tab@guardar.php'); }  
	
	if (isset($_GET['ope'])) { $operacion = $_GET['ope']; }  else { $operacion =''; }
	switch ($operacion) {
		case 'Update': 	$campoId  = $_GET['IdRegistro'];  
						$consulta = "SELECT *   FROM `$tablaDB` WHERE `$IdCampo` = $campoId ";
						$salida   = $conexionDB->consulta($consulta);
						$row = mysql_fetch_array($salida);
						$_desde = $row ['desde'];
                        $_hasta = $row ['hasta'];
                        $_factor = $row ['factor'];
                        $_rebaja = $row ['rebaja'];
						break;
						
		case 'Delete': 	$locationFile = $_SERVER['SCRIPT_NAME'];
						$errorMensaje = "ERROR_borrarTabImpuesto";
						require_once(PATH_INCLUDES.'tab@borrar2.php');  
						break;
	}

	/* :::::::::::::::::::::::::::::::::::::::::::: INICIO Lee Grilla ::::::::::::::::::::::::::::::::::::: */
	$titulos[1] = array("tit" => $sistema_txt->GetDefinition('id'),    "ancho" => "span-8 centro enc_mensajes");
	

	$fila[1] = array("campo" => "$IdCampo",   "ancho" => "span-8", "pre" => 'TRAMO ', "post" => "");
   
    
   
	require_once(PATH_INCLUDES.'tab@lee.php');
	/* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Grilla :::::::::::::::::::::::::::::::::::::::: */
	
	
	$contenido=new plantilla("comunImpuesto");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
			"lateral"			  => $lateral,
            "topbar"		      => $topbar,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
            "H2Titulo"		      => $sistema_txt->GetDefinition('H2Impuesto'),
            "TInforme"		      => $sistema_txt->GetDefinition('TINFImpuesto'),
            "salir"		          => $sistema_txt->GetDefinition('salir'),
            "buscarpor"		      => $sistema_txt->GetDefinition('buscarpor'),
            "dBase"               => $dBase,
            "tablaDB"             => $tablaDB,
			"phpFile"		 	  => $phpFile,
			"IdCampo"		 	  => $IdCampo,
            "campoOrd"            => $orderBy,
			
			"id"         => $sistema_txt->GetDefinition('id'),
			"desde"      => $sistema_txt->GetDefinition('desde'),
            "hasta"      => $sistema_txt->GetDefinition('hasta'),
            "factor"     => $sistema_txt->GetDefinition('factor'),
            "rebaja"     => $sistema_txt->GetDefinition('rebaja'),
			"borrar"     => $sistema_txt->GetDefinition('borrar'),
			"enviar"     => $sistema_txt->GetDefinition('enviar'),
			"limpiar"    => $sistema_txt->GetDefinition('limpiar'),
			"tablaHTML"	 => $tablaHTML,

			"campoId"	 => $campoId,   
		    "_desde"     => $_desde,
            "_hasta"     => $_hasta,
            "_factor"    => $_factor,
            "_rebaja"    => $_rebaja
			));
			
	echo $contenido->muestra();
?>
