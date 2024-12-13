<?php 
	require_once('../includes/initSistema.php');
	
	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD );
	
    /* :::::::::::::::::::::::::::::::::: Lee parametros $_GET  para eliminar  :::::::::::::::::::::::::::::::: */
	
	$tablaDB      = "comunProveedores";
	$IdCampo      = "idProvee";
    
	if (isset($_GET['ope'])) {
		if ($_GET['ope'] == 'Delete') {
			$locationFile = $_SERVER['SCRIPT_NAME'];
			$errorMensaje = "ERROR_borrarProveedores";
			require_once('borrar.php');  
		}
        
        if ($_GET['ope'] == 'Tras') {
            include('traspaso_guardar.php');
        }
	}
    
	/* :::::::::::::::::::::::::::::::::::::::::::: Lee Proveedoress ::::::::::::::::::::::::::::::::::::::::::::: */
	
   
    require_once(PATH_MODULOS_SISTEMA . 'comunProveedores_lee.php');
	
	
	$contenido=new plantilla("comunProveedores");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
            "topbar"		      => $topbar,
			"lateral"			  => $lateral,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
			"H2Titulo"	          => $sistema_txt->GetDefinition('H2ComunProveedores'),
			
			"cerrarSesion"        => $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  => $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  => $sistema_txt->GetDefinition('borrar'),
            "salir"        	      => $red_text->GetDefinition('salir'),
            
			
			"id"             => $sistema_txt->GetDefinition('id'),
            "rut"            => $sistema_txt->GetDefinition('rut'),
            "dv"             => $sistema_txt->GetDefinition('dv'),
			"razonSocial"    => $sistema_txt->GetDefinition('razonSocial'),
            "direccion"      => $sistema_txt->GetDefinition('direccion'),
            "sector"     	 => $sistema_txt->GetDefinition('sector'),
            "comunaId"     	 => $sistema_txt->GetDefinition('comunaId'),
            "telefono1"      => $sistema_txt->GetDefinition('telefono1'),
            "telefono2"      => $sistema_txt->GetDefinition('telefono2'),
            "fax"     	     => $sistema_txt->GetDefinition('fax'),
           	"email"          => $sistema_txt->GetDefinition('email'),
            "contacto"     	 => $sistema_txt->GetDefinition('contacto'),
            "condPago"     	 => $sistema_txt->GetDefinition('condPago'),
            "limiteCredito"  => $sistema_txt->GetDefinition('limiteCredito'),
            "giroComercial"  => $sistema_txt->GetDefinition('giroComercial'),
            
		
			

			"grillaHTML"     => $grillaHTML,
			"paginacionHTML" => $paginacionHTML,
           	"iguala"		 => $criterio,
			));
			
	echo $contenido->muestra();
?>
