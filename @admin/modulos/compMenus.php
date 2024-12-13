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
	
	$displaySistemas = "none";
	$displayModulos  = "none";
	$optionSistemas  = "";
	
	
	// ::::::::::::::::::::::::::::::::: DB  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$DB_ADMIN   = DB_ADMIN;
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_SISTEMA, DB_SERVER, DB_USER, DB_PASSWD );
	

	// Selecciona Clientes
	$idCliente 		 = 0;
	$puedeLeerRemoto = FALSE;
	
	$rut            = '';
	$cliente        = '';
	$modulos        = '';
	
	$prefijoBD      = '';
	$url        	= '';
	$puertoSQL    	= '';
	$usrSQL         = '';
	$passSQL        = '';

	if (isset($_GET['Ope'])) {
		if ($_GET['Ope'] == 'leeSist' OR $_GET['Ope'] == 'comp') {
			$displaySistemas = "block";
			$idCliente		 = $_GET['cliente'];
			
			// ::::::::::::::::::::::::::::::::::::::::::  Lee Cliente
			$consulta = "SELECT *   FROM  `adm_clientes`
									WHERE  idCliente = $idCliente";

			$salida = $conexionDB->consulta($consulta);
			$row    = mysqli_fetch_array($salida);

			$rut            = $row['rut'];
			$cliente        = $row['cliente'];
			$modulos        = $row['modulos'];
			
			$prefijoBD      = $row['prefijoBD'];
			$servidorBD  	= $row['servidorBD'];
			$puertoSQL    	= $row['puertoSQL'];
			$usrSQL         = $row['usrSQL'];
			$passSQL        = $row['passSQL'];

			
			$nroModulos = strlen($modulos) / 3;
			
			for ($i = 0; $i < $nroModulos; $i++) {
				$pos = $i * 3;
				$mod = substr($modulos,$pos,3);
				$arrayModulos[$mod] = TRUE;
			}
			
	
			if (isset($_GET['sistema'])) { $sistemaId = $_GET['sistema']; } else { $sistemaId = 0; }
			
			$optionSistemas = "<option value='0'>Todos</option>";
			$consulta = "SELECT *   FROM  `adm_sistemas` ORDER BY area, nombre";
			$salida = $conexionDB->consulta($consulta);
			
			while ($row = mysqli_fetch_array($salida)) {
				$id   		= $row['id'];
				$nombre     = $row['nombre'];

				if ($id == $sistemaId) { $selected = " selected='selected' "; } else { $selected = ""; }
				if ($arrayModulos[$id]) { $optionSistemas.= "<option value='$id' $selected>$nombre</option>"; }
			}
			
			$displaySistemas = "block";
			
			// ::::::::::::::::::::::::::::::::: DB  REMOTA::::::::::::::::::::::::::::::::::::::::::::::::: //
			define('CLI_SERVER',  "$servidorBD".":$puertoSQL");
			define('CLI_USER',    "$usrSQL");
			define('CLI_PASSWD',  "$passSQL");
			define('CLI_SISTEMA', "$prefijoBD@admin");
			
			$conexionRemota = new DB_MySQLi;
			$conexionRemota->conectar(CLI_SISTEMA, CLI_SERVER, CLI_USER, CLI_PASSWD );
			
			$puedeLeerRemoto = TRUE;
		}
	}
	
	// ::::::::::::::::::::::::::::::::::::::::::::::::  Comparacion de modulos
	$modulosREDM = ""; $modulosCliente = "";
	if (isset($_GET['Ope'])) {
		if ($_GET['Ope'] == 'comp') {
			$displaySistemas = "block";
			$displayModulos  = "block";
			
			$sistema	= $_GET['sistema'];
			$idCliente	= $_GET['cliente'];
			
			if ($sistema != 0) { $WHERE = "WHERE idsistema = '$sistema' "; } else { $WHERE = ""; }
			
			
			// ::::::::::::::::::::::::::::::::::::::::  Grilla sin paginación :::::::::::::::::::::::::::::::::::::: //
			$consulta = "SELECT *   FROM  `adm_modulos` 
									$WHERE
									ORDER BY tipo, modulo";				
			
			$conFicha   = FALSE;
			$conBorrar  = FALSE;
			$conBotAux1 = FALSE;  $funcionAux1 = "";  $txtBtn1 = "";
			$conBotAux2 = FALSE;  $funcionAux2 = "";  $txtBtn2 = "";

			$arrayGrilla[1] = array("campo" => 'id',      			"titulo" => 'ID',      	"ancho" => '2', "alin" => 'R' );
			$arrayGrilla[2] = array("campo" => 'modulo',  			"titulo" => 'modulo',  	"ancho" => '7');
			$arrayGrilla[3] = array("campo" => 'tipo',				"titulo" => 'tipo',  	"ancho" => '1', "alin" => 'C', "last" => true);

			include(PATH_INCLUDES.'grillaLeeSinPag.php'); 
			
			$modulosREDM = $grillaTitHTML.$grillaHTML;
			
			// carga array con consulta de  grilla
			$salida = $conexionDB->consulta($consulta);
	
			while ($row = mysqli_fetch_array($salida)) {
				$idMod		= $row['id'];
				$modulo     = $row['modulo'];

				$arrayMod[$idMod] = $modulo; 
			}
			
			// ::::::::::::::::::::::::::::::::::::::::  Grilla sin paginación remota :::::::::::::::::::::::::::::: //
			$consulta = "SELECT *   FROM  `adm_modulos` 
									$WHERE
									ORDER BY tipo, modulo";				
			
			$conFicha   = FALSE;
			$conBorrar  = FALSE;
			$conBotAux1 = FALSE;  $funcionAux1 = "";  $txtBtn1 = "";
			$conBotAux2 = FALSE;  $funcionAux2 = "";  $txtBtn2 = "";

			$arrayGrilla[1] = array("campo" => 'id',      			"titulo" => 'ID',      	"ancho" => '2', "alin" => 'R' );
			$arrayGrilla[2] = array("campo" => 'modulo',  			"titulo" => 'modulo',  	"ancho" => '7');
			$arrayGrilla[3] = array("campo" => 'tipo',				"titulo" => 'tipo',  	"ancho" => '1', "alin" => 'C', "last" => true);
			
			if ($puedeLeerRemoto) { include(PATH_INCLUDES.'grillaLeeSinPagRemoto.php'); }
			
			$modulosCliente = $grillaTitHTMLRemoto . $grillaHTMLRemoto; 
			
			// carga array con consulta de  grilla Remota
			$salida = $conexionRemota->consulta($consulta);
	
			while ($row = mysqli_fetch_array($salida)) {
				$idMod		= $row['id'];
				$modulo     = $row['modulo'];

				$arrayModRemoto[$idMod] = $modulo; 
			}
			
			// busca modulos faltantes en cliente
			$modFaltantesCliente = "";
			$arrayNoRemoto = array_diff_key($arrayMod, $arrayModRemoto);
			foreach($arrayNoRemoto as $codMod => $desc) { 
				$modFaltantesCliente .= "<div class='span-2'>$codMod</div><div class='span-8 last'>$desc</div>";
			}
			
			// busca modulos instalados en cliente y instalados en servidor RED
			$modSoloInstEnCliente = "";
			$arrayNoReDM   = array_diff_key($arrayModRemoto, $arrayMod);
			foreach($arrayNoReDM as $codMod => $desc) { 
				$modSoloInstEnCliente .= "<div class='span-2'>$codMod</div><div class='span-8 last'>$desc</div>";
			}
		}
	}
	

	$optionClientes = "<option value='0'>Seleccione Cliente</option>";
	$consulta = "SELECT *   FROM  `adm_clientes`";
	$salida = $conexionDB->consulta($consulta);
	
	while ($row = mysqli_fetch_array($salida)) {
		$clienteId   	= $row['idCliente'];
		$cliente        = $row['cliente'];

		if ($clienteId == $idCliente) { $selectd = " selected='selected' "; } else { $selectd = ""; }
		$optionClientes.= "<option value='$clienteId' $selectd>$cliente</option>";
	}
	
	
	$contenido=new plantilla("compMenus");
	$contenido->asigna_variables(
			array(
			"lang"  				=> $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  	=> date('d-m-Y'),
            "topbar"		      	=> $topbar,
			"lateral"			  	=> $lateral,
			"H2Sistema"			  	=> $sistema_txt->GetDefinition('H2Sistema'),
			"H2Modulo"	          	=> 'Comparador de Menus',
			
			"cerrarSesion"        	=> $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  	=> $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  	=> $sistema_txt->GetDefinition('borrar'),
            "salir"        	      	=> $dg_txt->GetDefinition('salir'),
           
		    "optionClientes"		=> $optionClientes,
			"optionSistemas"		=> $optionSistemas,
			
			"modulosREDM"       	=> $modulosREDM,
			"modulosCliente"		=> $modulosCliente,
			
			"cliente"        		=> $cliente,
			"rut"        			=> $rut,
			
			"prefijoBD" 			=> $prefijoBD,
			"url" 					=> $url,
			"puertoSQL" 			=> $puertoSQL,
			"usrSQL"				=> $usrSQL,
			"passSQL" 				=> $passSQL,
			
			"displaySistemas"		=> $displaySistemas,
			"displayModulos"		=> $displayModulos,
			"modFaltantesCliente"	=> $modFaltantesCliente,
			"modSoloInstEnCliente"	=> $modSoloInstEnCliente,
		));
			
	echo $contenido->muestra();
	
?>
