<?php 
	require_once('../includes/initSistema.php');
	
	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD );
	
    /* :::::::::::::::::::::::::::::::::: Lee parametros $_POST  para guardar  :::::::::::::::::::::::::::::::: */
	if (isset($_POST['razonSocial']) ) 
        { require_once('comunProveedores_guardar.php'); }
        else
        {
        /* :::::::::::::::::::::: Lee $_GETS si no existe regresa a pagina de busqueda :::::::::::::::::::::: */ 
        if (isset($_GET['IdRegistro'])) { $IdRegistro = $_GET['IdRegistro']; } else { header("Location:comunProveedores.php"); }   
    }
  
    
	/* :::::::::::::::::::::::::::::::::::::::::: Lee proveedores ::::::::::::::::::::::::::::::::::::::: */
	$consulta = "SELECT *   FROM  `comun_tabProveedores`
							WHERE  idProvee = $IdRegistro";
	
	if ( $IdRegistro != "new" ) {
		$salida = $conexionDB->consulta($consulta);
		$row    = mysql_fetch_array($salida);
		
		$_Idprovee        = $row['idProvee'];
		$_rut             = $row['rut'];
		$_dv              = $row['dv'];
		$_razonSocial     = $row['razonSocial'];
		$_direccion       = $row['direccion'];
        $_sector          = $row['sector'];        
        $_comunaId        = $row['comunaId'];
        $_telefono1       = $row['telefono1'];
        $_telefono2       = $row['telefono2'];
        $_fax             = $row['fax'];
        $_email           = $row['email'];
        $_condPago        = $row['condPago'];
        $_contacto        = $row['contacto'];
        $_limiteCredito   = $row['limiteCredito'];
        $_giroComercial   = $row['giroComercial'];
		       
        $readonly             = 'readonly';
		} else {
		$_Idprovee        = 'new';
		$_rut             = '';
		$_dv              = '';
		$_razonSocial     = '';
		$_direccion       = '';
        $_sector          = '';
        $_comunaId             = '';
        $_telefono1       = '';
        $_telefono2       = '';
        $_fax             = '';
        $_email           = '';
        $_condPago        = '';
        $_contacto        = '';
        $_limiteCredito        = '';
        $_giroComercial   = '';

      $readonly             = '';
	}
    
	/* :::::::::::::::::::::::::::::::::::::::::::: Carga Plantilla ::::::::::::::::::::::::::::: */
    
    /* :::::::::::::::::::::::::::::::::::::::::::: Tabla Comunas :::::::::::::::::::::::::::::::::::::::: */
	$consulta = "SELECT *   FROM  `red-m_@comun`.`comun_tabComunas`
							ORDER BY comuna, id";
	
	$OptionComunaHTML = "<option value='0' >Ingrese Comuna</option>";
	$salida   = $conexionDB->consulta($consulta);
	while ($row = mysql_fetch_array($salida)) {
		$id     = $row['id'];
		$comuna = $row['comuna'];

	    if ($_comunaId == $id) { $selectAut = "selected='selected' "; } else { $selectAut = ""; }
		$OptionComunaHTML .= "<option value='$id' $selectAut >$comuna</option>";
	}
    
    /* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Tabla Comunas :::::::::::::::::::::::::::::::::::::::: */
    
    /* :::::::::::::::::::::::::::::::::::::::::::: Tabla Condiciones de Pago :::::::::::::::::::::::::::::::::::::::: */
	$consulta = "SELECT *   FROM  `red-m_@comun`.`comun_tabCpago`
							ORDER BY condPago, IdCpago";
	
	$OptionCpagoHTML = "<option value='0' >Ingrese Cond.Pago</option>";
	$salida   = $conexionDB->consulta($consulta);
	while ($row = mysql_fetch_array($salida)) {
		$IdCpago  = $row['IdCpago'];
		$condPago = $row['condPago'];

	    if ($_condPago == $IdCpago) { $selectAut = "selected='selected' "; } else { $selectAut = ""; }
		$OptionCpagoHTML .= "<option value='$IdCpago' $selectAut >$condPago</option>";
	}
    
    /* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Condiciones de Pago :::::::::::::::::::::::::::::::::::::::: */
    
     
        
    
	$contenido=new plantilla("comunProveedores_ficha");
	$contenido->asigna_variables(
			array(
			"lang"                => $red_text->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
            "topbar"		      => $topbar,
			"lateral"			  => $lateral,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
			"H2Titulo"	          => $sistema_txt->GetDefinition('H2ComunProveedores'),
            
            "readonly"  => $readonly,
			
			"cerrarSesion"        => $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  => $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  => $sistema_txt->GetDefinition('borrar'),
            "enviar"        	  => $sistema_txt->GetDefinition('enviar'),
            "nuevo"        	      => $sistema_txt->GetDefinition('nuevo'),
            "salir"        	      => $sistema_txt->GetDefinition('salir'),
			
			"id"             => $sistema_txt->GetDefinition('id'),
            "rut"            => $sistema_txt->GetDefinition('rut'),
            "dv"             => $sistema_txt->GetDefinition('dv'),
			"razonSocial"    => $sistema_txt->GetDefinition('razonSocial'),
            "direccion"      => $sistema_txt->GetDefinition('direccion'),
            "sector"         => $sistema_txt->GetDefinition('sector'),
            "comunaId"       => $sistema_txt->GetDefinition('comuna'),
            "telefono1"      => $sistema_txt->GetDefinition('telefono1'),
            "telefono2"      => $sistema_txt->GetDefinition('telefono2'),
            "fax"            => $sistema_txt->GetDefinition('fax'),
			"email"          => $sistema_txt->GetDefinition('email'),
			"contacto"       => $sistema_txt->GetDefinition('contacto'),
            "condPago"       => $sistema_txt->GetDefinition('condPago'),
            "limiteCredito"  => $sistema_txt->GetDefinition('limiteCredito'),
            "giroComercial"  => $sistema_txt->GetDefinition('giroComercial'),
            
            
            "_Idprovee"         => $_Idprovee,
            "_rut"              => $_rut,
            "_dv"               => $_dv,
            "_razonSocial"      => $_razonSocial,
            "_direccion"        => $_direccion,
            "_sector"           => $_sector,
            "_comunaId"         => $_comunaId,
            "_telefono1"        => $_telefono1,
            "_telefono2"        => $_telefono2,
            "_fax"              => $_fax,
            "_email"            => $_email,
            "_contacto"         => $_contacto,
            "_limiteCredito"    => $_limiteCredito,
    		"_condPago"         => $_condPago,
            "_giroComercial"    => $_giroComercial,
            
           "OptionComunaHTML" => $OptionComunaHTML,
           "OptionCpagoHTML" => $OptionCpagoHTML,
                     
            ));
			
	echo $contenido->muestra();
?>
