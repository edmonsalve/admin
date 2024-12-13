<?php 
	require_once('../includes/initSistema.php');
	
	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_COMUN, DB_SERVER, DB_USER, DB_PASSWD );
    
	$dBase  	  		= DB_COMUN;
    $tablaDB	  		= "comun_tabProveedores";
	$IdCampo	  		= "idProvee";
	$phpFile 			= "comunProveedores";
	$orderBy			= "rut";
    
    $tabMaster          =  TAB_MASTER;
	$indMaster          = 'id';
	
	$campoId 	 	 = 'new';
	$_rut 	         = '';
    $_dv     	     = '';
    $_razonSocial    = '';
    $_direccion      = '';
    $_sector         = '';
    $_comuna         = '';
    $_telefono1      = '';
    $_telefono2      = '';
    $_fax            = '';
    $_email          = '';
    $_contacto       = '';
    $_condPago       = '';
    $_limiteCredito  = '';
    $_giroComercial  = '';
   	
	if (isset($_POST['rut'])) { require_once(PATH_INCLUDES.'tab@guardar.php'); }  
	
	if (isset($_GET['ope'])) { $operacion = $_GET['ope']; }  else { $operacion =''; }
	switch ($operacion) {
		case 'Update': 	$campoId  = $_GET['IdRegistro'];  
						$consulta = "SELECT *   FROM `$tablaDB` WHERE `$IdCampo` = $campoId ";
						$salida   = $conexionDB->consulta($consulta);
						$row      = mysql_fetch_array($salida);
						$_rut 	         = $row ['rut'];
                        $_dv             = $row ['dv'];
                        $_razonSocial    = $row ['razonSocial'];
                        $_direccion      = $row ['direccion'];
                        $_sector         =  $row ['sector'];
                        $_comuna         =  $row ['comuna'];
                        $_telefono1      =  $row ['telefono1'];
                        $_telefono2      =  $row ['telefono2'];
                        $_fax            =  $row ['fax'];
                        $_email          =  $row ['email'];
                        $_contacto       =  $row ['contacto'];
                        $_condPago       =  $row ['condPago'];
                        $_limiteCredito  =  $row ['limiteCredito'];
                        $_giroComercial  =  $row ['giroComercial'];
						break;
						
		case 'Delete': 	$locationFile = $_SERVER['SCRIPT_NAME'];
						$errorMensaje = "ERROR_borrarTabAfp";
						require_once(PATH_INCLUDES.'tab@borrar2.php');  
						break;
	}

	/* :::::::::::::::::::::::::::::::::::::::::::: INICIO Lee Grilla ::::::::::::::::::::::::::::::::::::: */
	$titulos[1] = array("tit" => $sistema_txt->GetDefinition('idProvee'),    "ancho" => "span-1 enc_mensajes");
	$titulos[2] = array("tit" => $sistema_txt->GetDefinition('rut'), "ancho" => "span-7 enc_mensajes last");
	
	$fila[1] = array("campo" => "$IdCampo",   "ancho" => "span-1 derecha");
	$fila[2] = array("campo" => "$orderBy",   "ancho" => "span-7 last");
   
	require_once(PATH_INCLUDES.'tab@lee.php');
	/* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Grilla :::::::::::::::::::::::::::::::::::::::: */
	/* :::::::::::::::::::::::::::::::::::::::::::: Tabla Deptos :::::::::::::::::::::::::::::::::::::::: */
	$consulta = "SELECT *   FROM  `red-m_@comun`.`comun_tabComunas`
							ORDER BY comuna, id";
	
	$OptionComunaHTML = "<option value='0' >Ingrese Comuna</option>";
	$salida   = $conexionDB->consulta($consulta);
	while ($row = mysql_fetch_array($salida)) {
		$id     = $row['id'];
		$comuna = $row['comuna'];

	    if ($_comuna == $id) { $selectAut = "selected='selected' "; } else { $selectAut = ""; }
		$OptionComunaHTML .= "<option value='$id' $selectAut >$comuna</option>";
	}
    
    /* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Tabla Deptos :::::::::::::::::::::::::::::::::::::::: */
	
	$contenido=new plantilla("comunProveedores");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
			"lateral"			  => $lateral,
            "topbar"		      => $topbar,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
            "H2Titulo"		      => $sistema_txt->GetDefinition('H2ComunProveedores'),
            "TInforme"		      => $sistema_txt->GetDefinition('TINFComunProveedores'),
            "salir"		          => $sistema_txt->GetDefinition('salir'),
            "buscarpor"		      => $sistema_txt->GetDefinition('buscarpor'),
            "dBase"               => $dBase,
            "tablaDB"             => $tablaDB,
			"phpFile"		 	  => $phpFile,
			"IdCampo"		 	  => $IdCampo,
            "campoOrd"            => $orderBy,
			
			"idProvee"      => $sistema_txt->GetDefinition('idProvee'),
            "rut"           => $sistema_txt->GetDefinition('rut'),
			"dv"            => $sistema_txt->GetDefinition('dv'),
            "razonSocial"   => $sistema_txt->GetDefinition('razonSocial'),
            "direccion"     => $sistema_txt->GetDefinition('direccion'),
            "sector"        => $sistema_txt->GetDefinition('sector'),
            "comuna"        => $sistema_txt->GetDefinition('comuna'),
            "telefono1"     => $sistema_txt->GetDefinition('telefono1'),
            "telefono2"     => $sistema_txt->GetDefinition('telefono2'),
            "fax"           => $sistema_txt->GetDefinition('fax'),
            "email"         => $sistema_txt->GetDefinition('email'),
            "contacto"      => $sistema_txt->GetDefinition('contacto'),
            "condPago"      => $sistema_txt->GetDefinition('condPago'),
            "limiteCredito" => $sistema_txt->GetDefinition('limiteCredito'),
            "giroComercial" => $sistema_txt->GetDefinition('giroComercial'),
            
            "borrar"       => $sistema_txt->GetDefinition('borrar'),
			"enviar"       => $sistema_txt->GetDefinition('enviar'),
			"limpiar"      => $sistema_txt->GetDefinition('limpiar'),
			"tablaHTML"	   => $tablaHTML,

			"campoId"	     => $campoId,   
			"_rut"           => $_rut,
            "_dv"            => $_dv,
            "_razonSocial"   => $_razonSocial,
            "_direccion"     => $_direccion,
            "_sector"        => $_sector,
            "_comuna"        => $_comuna,
            "_telefono1"     => $_telefono1,
            "_telefono2"     => $_telefono2,
            "_fax"           => $_fax,
            "_email"         => $_email,
            "_contacto"      => $_contacto,
            "_condPago"      => $_condPago,
            "_limiteCredito" => $_limiteCredito,
            "_limiteCredito" => $_limiteCredito,
            
            "OptionComunaHTML" => $OptionComunaHTML,
     ));
			
	echo $contenido->muestra();
?>
