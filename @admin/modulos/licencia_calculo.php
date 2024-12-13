<?php
	
	require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');
	// require_once(PATH_INCLUDES . 'redm.php');
	
	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }
    // if ($usrID != "edmonsalve") { header("Location:index.php"); }

	$DB_ADMIN = DB_ADMIN;
	$conexionReD = new DB_MySQLi;
	$conexionReD->conectar($DB_ADMIN, DB_SERVER, DB_USER, DB_PASSWD );
	

    include("../includes/crypt.php"); 
	$EnDecryptText = new EnDecryptText(); 
    
	$IdRegistro = $_GET['idCliente'];
	
	// ::::::::::::::::::::::::::::::::: DB  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$DB_ADMIN = DB_ADMIN;
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_SISTEMA, DB_SERVER, DB_USER, DB_PASSWD );
	
	$consulta = "SELECT *   FROM  `adm_clientes`
							WHERE  idCliente = $IdRegistro";

	$salida = $conexionDB->consulta($consulta);
	$row    = mysqli_fetch_array($salida);

	$clienteId   	= $row['idCliente'];
	$cliente        = $row['cliente'];
	$rut            = $row['rut'];

	$licencia        = $row['licencia'];
	$vcto			 = $row['vencimientoLic'];
	$sistemas        = $row['modulos'];
	$estado    		 = $row['estadoLic'];
	$tipo        	 = $row['tipoLic'];
	

    // ======     Genera numero de licencia a partir del Nombre de Cliente codificado   =====  //
	$_clienteMD5 = md5($cliente);

	
	$largo = strlen($_clienteMD5);
	$aux = 0;
	$key = '';
	for($i=$largo; $i > 0; $i--) { 
		if ($aux < 26) { $key .= strtoupper(substr($_clienteMD5,$i,1)); }
		if (($aux % 5) == 0 and $aux > 0 and $aux < 25) { $key .= '-'; }
		$aux++;
	}
    
	$_licencia = $key;
	$_sistemas = $sistemas;

	$eval = round(substr($rut,2,6)/10,0);
	$ok   = round(substr($rut,2,6)/1,0);
		
	// codifica nombre cliente
	$sTemp = $EnDecryptText->Encrypt_Text($cliente);
	$rText = $EnDecryptText->Decrypt_Text($sTemp);
	$_clienteHTML = "<textarea class='span-20' cols='160' rows='1' readonly='readonly' style='float:none; font-size:12px; height:60px; '>$sTemp</textarea>";
	

	// codifica nombre rut
	$sTemp = $EnDecryptText->Encrypt_Text($rut);
	$rText = $EnDecryptText->Decrypt_Text($sTemp);
	$_rutHTML .= "<textarea class='span-20' cols='160' rows='1' readonly='readonly' style='float:none; font-size:12px; height:60px; '>$sTemp</textarea>";

    
	// codifica nombre licencia
	$sTemp = $EnDecryptText->Encrypt_Text($key);
	$rText = $EnDecryptText->Decrypt_Text($sTemp);
	$_licHTML .= "<textarea class='span-20' cols='160' rows='1' readonly='readonly' style='float:none; font-size:12px; height:60px; '>$sTemp</textarea>";
   	
    // codifica sistemas
	$sTemp = $EnDecryptText->Encrypt_Text($sistemas);
	$rText = $EnDecryptText->Decrypt_Text($sTemp);
	$_sistHTML .= "<textarea class='span-20' cols='160' rows='1' readonly='readonly' style='float:none; font-size:12px; height:60px; '>$sTemp</textarea>";
	
	// codifica estado/tipo
	$sTemp = $EnDecryptText->Encrypt_Text("$estado/$tipo");
	$rText = $EnDecryptText->Decrypt_Text($sTemp);
	$_estadoHTML .= "<textarea class='span-20' cols='160' rows='1' readonly='readonly' style='float:none; font-size:12px; height:60px; '>$sTemp</textarea>";
	
	// codifica vencimiento
	$sTemp = $EnDecryptText->Encrypt_Text($vcto);
	$rText = $EnDecryptText->Decrypt_Text($sTemp);
	$_vctoHTML .= "<textarea class='span-20' cols='160' rows='1' readonly='readonly' style='float:none; font-size:12px; height:60px; '>$sTemp</textarea>";
	

	
	$contenido=new plantilla("licencia_calculo");
	$contenido->asigna_variables(
			array(
			"lang"  				=> $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  	=> date('d-m-Y'),
            "topbar"		      	=> $topbar,
			"lateral"			  	=> $lateral,
			"H2Sistema"			  	=> $sistema_txt->GetDefinition('H2Sistema'),
			"H2Modulo"	          	=> 'Licencias de Cliente',
			
			"cerrarSesion"        	=> $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  	=> $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  	=> $sistema_txt->GetDefinition('borrar'),
            "salir"        	      	=> $dg_txt->GetDefinition('salir'),
            
			"IdRegistro"			=> $IdRegistro,
			"cliente"				=> $cliente,
			"rut"					=> $rut,
			"estado"				=> "$estado/$tipo",
			"vcto"					=> $vcto,
			
			"sistemas"				=> $sistemas,
				
			"_licencia"				=> $_licencia,
			
			
			"_clienteHTML"     		=> $_clienteHTML,
			"_rutHTML"				=> $_rutHTML,
			"_licHTML"				=> $_licHTML,
			"_sistHTML"				=> $_sistHTML,
			"_estadoHTML"			=> $_estadoHTML,
			"_vctoHTML"				=> $_vctoHTML,
			));
			
	echo $contenido->muestra();
	/*	echo "hola! -- $DB_ADMIN $rut"; */
?>
