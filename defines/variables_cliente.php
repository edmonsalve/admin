<?php 
    if (!defined('DB_SERVER')) {  
		$iniFile = "/etc/dMuniIni/dMuniCli.ini";
		if (!file_exists($iniFile) || !is_readable($iniFile)) {
			die("Error: El archivo de configuración '$iniFile' no existe o no es accesible.");
		}
		$variablesCli = parse_ini_file($iniFile);

		// ::: INI CLIENTE
		define('COD_CLIENTE',  		$variablesCli['codCliente']);
        define('DB_PREFIJO', 		$variablesCli['prefijo']);

        // :: CONEXION BD
    	define('DB_SERVER',  		$variablesCli['server']); 
        define('DB_USER',    		$variablesCli['usuario']);
    	define('DB_PASSWD',  		$variablesCli['clave']);

		// :: COMUNICADOS
		define('COMUNICADO', 		$variablesCli['comunicado']);
		define('FILE_COMUNICADO', 	$variablesCli['fileComunicado']);

		// :: INTENTOS FALLIDOS
		define('INTENTOS_FALLIDOS', $variablesCli['intentosFallidos']);

		// :: API LABS MOBILE
		define('USER_LABS_MOBIL',	$variablesCli['userLabsMobile']);
		define('TOKEN_LABS_MOBIL',	$variablesCli['tokenLabsMobile']);

		// :: 2FA
		define('REQUIERE_2FA',		$variablesCli['requiere2FA']);
		define('TIEMPO_2FA',		$variablesCli['tiempo2FA']);
		define('SMS_2FA',			$variablesCli['sms2FA']);
		define('EMAIL_2FA',			$variablesCli['email2FA']);
		define('URL_REDIRECCION',	$variablesCli['urlRedirecc']);

        // :: logo cliente
        define('LOGO_CLIENTE' ,     $variablesCli['logoCli']);
        define('LOGO_DOC' ,         $variablesCli['logoDoc']);
		define('CON_LOGO_AUX' ,     $variablesCli['conLogoAux']);
        define('LOGO_AUX' ,         $variablesCli['logoAux']);

		// :: notificaciones intranet
		define('NOTIFICACION_EMAIL', $variablesCli['notificacionEmail']);
		define('NOTIFICACION_SMS',   $variablesCli['notificacionSMS']);

		//:: doc.digital
		define('API_URL',           $variablesCli['urlDocDigital']);
		define('API_USER',          $variablesCli['useDocDigital']);
		define('API_PASSWD',        $variablesCli['passDocDigital']);
		define('API_ENTIDAD_ID',    $variablesCli['entidadID']);
		
		//:: Pisee
		define('NODO_V2',           $variablesCli['urlNodoV2']);
		
		// :: AACH AG
		define('URL_AACH',          $variablesCli['urlAACH']);
		define('USER_AACH',         $variablesCli['userAACH']);
		define('PASSWD_AACH',       $variablesCli['passwdAACH']);
		define('MUNI_AACH',         $variablesCli['muniAACH']);
		
		// :: PRT
		define('URL_PRT',           $variablesCli['urlPRT']);
		define('USER_PRT',          $variablesCli['userPRT']);
		define('PASSWD_PRT',        $variablesCli['passwdPRT']);

		// :: extranet
		define('SERVER_EXTNET' ,	$variablesCli['extraNet']);
		define('PUERTO_EXTNET' ,	$variablesCli['puertoEx']);
		define('USER_EXTNET' ,      $variablesCli['userExt']);
		define('PASWD_EXTNET' ,     $variablesCli['claveExt']);
		define('ALMACEN_DECEXT' ,   $variablesCli['urlExtra']);

		// :: SIFIM
		define('S_SERVER',				$variablesCli['serverSifim']);
		define('S_PORT',				$variablesCli['puertoSifim']);  
 		define('S_USER',				$variablesCli['userSifim']);  
		define('S_PASSWORD',			$variablesCli['passSifim']);  
		define('S_DATABASE_COMUN',		$variablesCli['sifimComun']); 	    // C
		define('S_DATABASE_COMUN_X',	$variablesCli['sifimComunX']);      // D 
		define('S_DATABASE_FINANZAS_X',	$variablesCli['sifimFinanzas']);  	// F
		define('S_TDS_VERSION',			$variablesCli['tdsVersion']);
		}
		// echo "<br><br><br> ------------".S_SERVER.' '.S_PORT.' '.S_USER.' '.S_PASSWORD.' '.S_DATABASE_COMUN.' '.S_DATABASE_COMUN_X.' '.S_DATABASE_FINANZAS_X;
	
    if (!defined('E_HOST')) { 
		//::::::::::::::::::::: CUENTA E-MAIL ::::::::::::::::::::::: //
		define('E_HOST',			$variablesCli['mailServer']);
		
		define('E_MAILER',			$variablesCli['mailer']); 
		define('E_PORT',			$variablesCli['mailPort']);
 		define('E_SMTP_SECURE',		$variablesCli['mailSecure']);
		define('E_SMTP_AUTH',		$variablesCli['smtpAuth']);
 
		define('E_USER_NAME',		$variablesCli['mailUser']);	 
		define('E_PASSWORD',		$variablesCli['mailPasswd']);
		define('E_FROM',			$variablesCli['mailFrom']);  
		define('E_FROM_NAME',		$variablesCli['mailFromNam']);
	
		define('E_PIEFIRMA',"<br><br><p3>Este mensaje y sus posibles documentos adjuntos son confidenciales y est&acute;n dirigidos exclusivamente a sus destinatarios.
							 <br>Por favor, si Ud. no es uno de ellos, notifíquenoslo y elimine el mensaje de su sistema.<p3>
							 <hr><img src='".RUTA_ABSOLUTA."images/pieTicket1.png'><br><img src='".RUTA_ABSOLUTA."images/pieTicket2.png'><br>"); 
    } 
?>