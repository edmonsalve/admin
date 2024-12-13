<?php 
	require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');
	// require_once(PATH_INCLUDES . 'redm.php');
	
	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }
	
	
    // ::::::::: Determina Nombre de Sript en Ejecucion ::::::::: //
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);


    // ::::::::::::::::::::::::::::::::: DB  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$DB_ADMIN = DB_ADMIN;
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_SISTEMA, DB_SERVER, DB_USER, DB_PASSWD );
	
	$_dispCont = "none"; 

	
    // :::::::::::::::::::::: Lee $_GETS si no existe regresa a pagina de busqueda :::::::::::::::::::::: /
    if (isset($_GET['IdRegistro'])) {
        $IdRegistro = $_GET['IdRegistro'];
    }
	
	if (isset($_POST['IdRegistro'])) {
        $IdRegistro = $_POST['IdRegistro'];
    }

    // ::::::::::::::::::::::::::::::: Lee parametros $_POST  para guardar  Contrato ::::::::::::::::::::::: //
    if (isset($_POST['rut'])) {
        $guardarTabla = 'adm_clientes';
        $IdCampo      = 'idCliente';
 
		$muestraConsulta = false;
		require_once('clientesGuardar.php');
    }

	// :::::::::::::::::::::::::::::::::::::::::: Lee registro ::::::::::::::::::::::::::::::::::::::: //
	if ( $IdRegistro != "new" ) {
	    $consulta = "SELECT *   FROM  `adm_clientes`
							    WHERE  idCliente = $IdRegistro";

		$salida = $conexionDB->consulta($consulta);
		$row    = mysqli_fetch_array($salida);

		$id		      	= $row['idCliente'];
        $cliente        = $row['cliente'];
        $rut            = $row['rut'];
		
		$administrador1	= $row['administrador1'];
		$emailAdm1	    = $row['emailAdm1'];
		$administrador2	= $row['administrador2'];
		$emailAdm2	    = $row['emailAdm2'];
		$administrador3	= $row['administrador3'];
		$emailAdm3	    = $row['emailAdm3'];

		$url			= $row['url'];
		$phpVer			= $row['phpVer'];
		
	    $prefijoBD      = $row['prefijoBD'];
        $servidorBD 	= $row['servidorBD'];	
        $puertoSQL      = $row['puertoSQL'];
		$usrSQL      	= $row['usrSQL'];
        $passSQL       	= $row['passSQL'];
		
		$servidorProduc = $row['servidorProduc'];
        $puertoSSH      = $row['puertoSSH'];  
		$usrSSH      	= $row['usrSSH'];
        $passSSH        = $row['passSSH'];
        $puertoHTTP     = $row['puertoHTTP'];
		
        $licencia       = $row['licencia'];
        $vencimientoLic	= $row['vencimientoLic'];
        $modulos        = $row['modulos'];
        $estadoLic      = $row['estadoLic'];
		$tipoLic        = $row['tipoLic'];
		
        $m          	= $row['m'];
        $e         		= $row['e'];
        $s				= $row['s'];
        $a       		= $row['c'];
		
		$inicioContrato = $row['inicioContrato'];
		$venctoContrato = $row['venctoContrato'];
		$montoMensual   = $row['montoMensual'];
		$contactoAdmin  = $row['contactoAdmin'];
		$observContrato = $row['observContrato'];

		$readonly       = 'readonly';
		} else {
        $id             = 'new';
        $cliente        = '';
        $rut            = '';
		
		$administrador1	= '';
		$emailAdm1	    = '';
		$administrador2	= '';
		$emailAdm2	    = '';
		$administrador3	= '';
		$emailAdm3	    = '';
		
		$url			= '';
		$phpVer			= '';
		
	    $prefijoBD      = '';
        $servidorBD     = '';
        $puertoSQL      = '';
		$usrSQL			= '';
        $passSQL       	= '';
		
		$servidorProduc = '';
        $puertoSSH      = '';
		$usrSSH			= '';
        $passSSH        = '';
        $puertoHTTP     = '';
		
        $licencia       = '';
        $vencimientoLic	= '';
        $modulos        = '';
        $estadoLic      = '';
		$tipoLic        = '';
		
        $m          	= 'S';
        $e         		= 'N';
        $s				= 'N';
        $a       		= 'S';
		
		$inicioContrato = "";
		$venctoContrato = "";
		$montoMensual   = "";
		$contactoAdmin  = "";
		$observContrato = "";

        $readonly         = '';
        $disabledBtn      = ' disabled="disabled" ';
	}

	// ::::::::::::::::::::::::::::::::: LEE SISTEMAS CONTRATADOS :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$nroModulos = strlen($modulos) / 3;
    
    for ($i = 0; $i < $nroModulos; $i++) {
        $pos = $i * 3;
        $mod = substr($modulos,$pos,3);
        $arrayModulos[$mod] = TRUE;
		echo  " $mod | ";
    }

	$areasSistemas = ['M' => '_htmlSistMuni', 'S' => '_htmlSistSalud', 'E' => '_htmlSistEduc', 'A' => '_htmlSistAdmin' ];
	
	
	// :::::::::::::::::::::::::::::::::  LEE SISTEMAS  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	foreach ($areasSistemas as $area => $html) { 
		
		$consulta = "SELECT *  	FROM `adm_sistemas`
								WHERE area = '$area'
								ORDER BY area, nombre ";

		$salida   = $conexionDB->consulta($consulta);	

		$w 			   = 1;
		$$html = "";	
		while ($row = mysqli_fetch_array($salida)) {
			if	(($w % 2) == 0) { $st = "";  $last = ''; } else { $st = "background-color:#EEF4F9; ";  $last = ' last';}

			$_idSistema		= $row['id'];
			$_nombreSistema	= $row['nombre'];
			
			if (isset($arrayModulos[$_idSistema])) { 
				$chkBox = " checked='checked' "; 
				$modulo	= "<a href='clientesActaRecepcion.php?IdSistema=$_idSistema&IdCliente=$IdRegistro' target='_blank' ><div class='span-8 last' style='$st' >$_nombreSistema</div></a>";
			} else { 
				$chkBox 	= "0"; 
				$modulo	= "<div class='span-8 last' style='$st' >$_nombreSistema</div>";
			}
				
			$$html .= "
			<div class='span-10 $last' style='$st'>
				<div class='span-2 centro'  ><input name='$_idSistema' type='checkbox'  $chkBox /></div>
				$modulo	
			</div>";
			$w++;
		}
		mysqli_free_result($salida);
	}
	
	if ($m == 'S') { $selectedMS = " selected='selected' "; $selectedMN = ""; } else { $selectedMS = ""; $selectedMN = " selected='selected' "; }
	if ($e == 'S') { $selectedES = " selected='selected' "; $selectedEN = ""; } else { $selectedES = ""; $selectedEN = " selected='selected' "; }
	if ($s == 'S') { $selectedSS = " selected='selected' "; $selectedSN = ""; } else { $selectedSS = ""; $selectedSN = " selected='selected' "; }
	if ($a == 'S') { $selectedAS = " selected='selected' "; $selectedAN = ""; } else { $selectedAS = ""; $selectedSN = " selected='selected' "; }
	
	
	// :::::::::::::::::::::::::::::::::::::::::::: Carga Plantilla ::::::::::::::::::::::::::::: //
	$contenido=new plantilla("clientes_ficha");
	$contenido->asigna_variables(
			array(
			"lang"  				=> $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  	=> date('d-m-Y'),
            "topbar"		      	=> $topbar,
			"lateral"			  	=> $lateral,
			"H2Sistema"			  	=> 'Mantenedor de Clientes',
			"H2Titulo"	          	=> 'Mantenedor Cliente & Licencia',
			
			"cerrarSesion"        	=> $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  	=> $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  	=> $sistema_txt->GetDefinition('borrar'),
            "salir"        	      	=> $dg_txt->GetDefinition('salir'),
            
			"id"				    => $id,
			"cliente"        		=> $cliente,
			"rut"        			=> $rut,
			
			"administrador1" 		=> $administrador1,
			"emailAdm1" 			=> $emailAdm1,
			"administrador2" 		=> $administrador2,
			"emailAdm2" 			=> $emailAdm2,
			"administrador3" 		=> $administrador3,
			"emailAdm3" 			=> $emailAdm3,
		
			"prefijoBD" 			=> $prefijoBD,
			"servidorBD" 			=> $servidorBD,
			"puertoSQL" 			=> $puertoSQL,
			"usrSQL"				=> $usrSQL,
			"passSQL" 				=> $passSQL,
			
			"url"					=> $url,
			"phpVer"				=> $phpVer,
			
			"servidorProduc" 		=> $servidorProduc,
			"puertoSSH" 			=> $puertoSSH,
			"usrSSH"				=> $usrSSH,
			"passSSH" 				=> $passSSH,
			"puertoHTTP" 			=> $puertoHTTP,
			
			"licencia" 				=> $licencia,
			"vencimientoLic"		=> $vencimientoLic,
			"modulos"				=> $modulos,
			"estadoLic"				=> $estadoLic,
			
			"_htmlSistMuni"     	=> $_htmlSistMuni,
			"_htmlSistSalud"     	=> $_htmlSistSalud,
			"_htmlSistEduc"     	=> $_htmlSistEduc,
			"_htmlSistAdmin"		=> $_htmlSistAdmin,
			"_htmlClientes"			=> $_htmlClientes,
			
			"selectedMS"			=> $selectedMS,
			"selectedMN"			=> $selectedMN,
			"selectedSS"			=> $selectedSS,
			"selectedSN"			=> $selectedSN,
			"selectedSN"			=> $selectedSN,
			"selectedEN"			=> $selectedEN,
			"selectedAS"			=> $selectedAS,
			"selectedAN"			=> $selectedAN,
			
			"inicioContrato"		=> $inicioContrato,
			"venctoContrato"		=> $venctoContrato,
			"montoMensual" 			=> $montoMensual,
			"contactoAdmin"			=> $contactoAdmin,
			"observContrato"		=> $observContrato,
			
			"dispCont"				=> $_dispCont,
			));
			
	echo $contenido->muestra();
?>
