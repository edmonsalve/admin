<?php 
    $error      = '';  
    
    if ($permiteGuardar) {
		$_IdProvee         = $_POST['IdProvee'];
		$_rut              = $_POST['rut'];
		$_dv               = $_POST['dv'];
		$_razonSocial      = $_POST['razonSocial'];
        $_direccion        = $_POST['direccion'];
        $_sector           = $_POST['sector'];
        $_comunaId         = $_POST['comunaId'];
		$_telefono1        = $_POST['telefono1'];
        $_telefono2        = $_POST['telefono2'];
        $_fax              = $_POST['fax']; 
        $_email            = $_POST['email'];
        $_contacto         = $_POST['contacto'];
        $_condPago         = $_POST['condPago'];  
        $_limiteCredito    = $_POST['limiteCredito']; 
        $_giroComercial    = $_POST['giroComercial'];  
        
		
		/* ::::::::::::::::::::::::::::::::::::::  guardar datos  :::::::::::::::::::::::::::::::: */
		if ($_IdProvee == 'new') {
			// Guarda Nuevo Registro
			$consulta = "REPLACE INTO `comun_tabProveedores` (`IdProvee`,`rut`,`dv`,`razonSocial`,  `direccion`,  `sector`,`comunaId`, `telefono1`, `telefono2`,`fax`, `email`,  `contacto`,  `condPago`,  `limiteCredito`, `giroComercial`  ) 
										         VALUES    ( $_rut,   $_rut,'$_dv','$_razonSocial','$_direccion','$_sector','$_comunaId','$_telefono1','$_telefono2','$_fax','$_email','$_contacto', '$_condPago', '$_limiteCredito','$_giroComercial' )";
			} else {
			// Actualiza  Registro	
            $consulta = "UPDATE  `comun_tabProveedores`	SET 
                                                `rut`           =  $_rut, 
												`dv`            = '$_dv',
												`razonSocial`   = '$_razonSocial',
												`direccion`     = '$_direccion',
												`sector`        = '$_sector',
                                                `comunaId`      = '$_comunaId',
                                                `telefono1`     = '$_telefono1',
                                                `telefono2`     = '$_telefono2',
                                                `fax`           = '$_fax',
												`email`         = '$_email',
                                                `contacto`      = '$_contacto',  
                                                `condPago`      = '$_condPago',
                                                `limiteCredito` = '$_limiteCredito',    
                                                `giroComercial` = '$_giroComercial'
			                              WHERE `IdProvee`      = $_IdProvee";
		}
		
		if (!$conexionDB->consulta($consulta)) { $error .= mysql_error().'Error al guardar datos  <br />'; } 
            else {
			if ($_IdProvee == 'new' ) { $IdRegistro = $conexionDB->ultimoId(); } else { $IdRegistro = $_IdProvee; }
		}
	}
	echo $error;
?>
