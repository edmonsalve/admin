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
	
	if (isset($_GET['ope'])) {
		$ope = $_GET['ope'];
		if ($ope == 'Delete') {
			$idModulo = $_GET['IdRegistro'];
			$consulta = "SELECT *	FROM 	`adm_modulos` 
									WHERE	 id = $idModulo";
		
			$salida	= $conexionDB->consulta($consulta);
			$row 	= mysqli_fetch_array($salida);
			$sisId  = $row['idsistema']; 
			
			$consulta = "DELETE	FROM 	`adm_modulos` 
								WHERE	 id = $idModulo";
								
			$conexionDB->consulta($consulta);
		}
	}
	
    if (isset($_GET['idSistema'])) {
        $sisId = $_GET['idSistema'];
    }
	
	$modulo     = "";
	$php        = "";
	$parametros = "";
	$tipo       = "";
	$target     = "";
	$estado     = "";
	$sistemaVer = "";
	$movil      = "";
	
	$dispEdit   = "none";
	$rIdModu  	= " readonly='readonly' ";
	
	
	
	
	if (isset($_GET['modulo'])) {
        $idModulo = $_GET['modulo'];
		
		if ($idModulo != 'new') {
			$consulta = "SELECT *	FROM 	`adm_modulos` 
									WHERE	 id = $idModulo
									AND 	 idsistema = '$sisId'";
	
			$salida   = $conexionDB->consulta($consulta);
			$row = mysqli_fetch_array($salida);
	
			$modulo     = $row['modulo'];
			$php        = $row['php'];
			$parametros = $row['parametros'];
			$tipo       = $row['tipo'];
			$target     = $row['target'];
			$estado     = $row['estado'];
			$sistemaVer = $row['sistemaVer'];
			$movil      = $row['movil'];		
			
			$targetT = ""; $targetB = "";
			switch($target) { 
				case 'T': $targetT = " selected='selected' "; $targetB = ""; break;
				case 'B': $targetT = ""; $targetB = " selected='selected' "; break;
			}
			
			$estado1 = ""; $estado0 = "";
			switch($estado) { 
				case '1': $estado1 = " selected='selected' "; $estado0 = ""; break;
				case '0': $estado1 = ""; $estado0 = " selected='selected' "; break;
			}
			
			$sistemaVer1 = ""; $sistemaVer2 = "";
			switch($sistemaVer) { 
				case '1': $sistemaVer1 = " selected='selected' "; $sistemaVer2 = ""; break;
				case '2': $sistemaVer1 = ""; $sistemaVer2 = " selected='selected' "; break;
			}
			
			$dispEdit   = "block";
			$rIdModu  	= " readonly='readonly' ";
		} else {
			$dispEdit   = "block";
			$rIdModu  	= "";
		}
    }
  
	// lee estructura de menus ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	$consulta = "SELECT * FROM `adm_estrucMenu` 
                                ORDER BY tipoGrupo";
    
    $salida   = $conexionDB->consulta($consulta);
    
    $optionEstMenu  = "<option value='0' >Seleccione grupo menu</option>";
	while ($row = mysqli_fetch_array($salida)) {
	   $tipoGrupo 	  = $row['tipoGrupo'];
       $tituloGrupo   = $row['tituloGrupo'];
	   
	   if ($tipo == $tipoGrupo) { $selected = " selected='selected' "; } else { $selected = ""; }
	   $optionEstMenu .= "<option value='$tipoGrupo' $selected>$tipoGrupo - $tituloGrupo</option>";
	}
	
	
    // Lee parametros $_POST  para guardar  Contrato :::::::::::::::::::::::::::::::::::::::::::::::::::::::
    if (isset($_POST['id'])) {
        $guardarTabla  = 'adm_modulos';
        $IdCampo       = 'id';
        $reemplazarpor = 'idModulo';
       
	    $muestraPost	 = false;
		$muestraConsulta = false;
		$mayusculas		 = false;
		require_once( PATH_INCLUDES . 'guardar.php');
    }


	// :::::::::::::::::::::::::::::::::::::::::: Lee Sistema ::::::::::::::::::::::::::::::::::::::: //
	$consulta = "SELECT *  	FROM `adm_sistemas`
							WHERE id = '$sisId'";

	$salida = $conexionDB->consulta($consulta);
	$row    = mysqli_fetch_array($salida);

	$id		    	= $row['id'];
	$nombre     	= $row['nombre'];

	$readonly       = " readonly='readonly' ";
	
	// ::::::::::::::::::::::::::::::::::::::::  Grilla sin paginación :::::::::::::::::::::::::::::::::::::: //
	$consulta = "SELECT * FROM `adm_modulos` 
                                WHERE idsistema = $sisId
                                ORDER BY tipo, modulo";		
	
	$IdCampo	= "id";
	$conFicha   = FALSE;
	$conBorrar  = true;   $btnClassBorra = "enviarpeqRojo";
	$conBotAux1 = true;   $funcionAux1   = "editarMod";  $txtBtn1 = "editar";  $constanteAux1 = "$sisId";  $btnClassAux1 = "enviarpeqKhaki";
	$conBotAux2 = FALSE;  $funcionAux2   = "";  $txtBtn2 = "";

	$arrayGrilla[1] = array("campo" => 'id',      			"titulo" => 'ID',      		"ancho" => '2', "alin" => 'R' );
	$arrayGrilla[2] = array("campo" => 'modulo',  			"titulo" => 'modulo',  		"ancho" => '7');
	$arrayGrilla[3] = array("campo" => 'tipo',				"titulo" => 'tipo',  		"ancho" => '1', "alin" => 'C');
	$arrayGrilla[4] = array("campo" => 'php',				"titulo" => 'php',  		"ancho" => '4', "alin" => 'L');
	$arrayGrilla[5] = array("campo" => 'target',			"titulo" => 'targ',			"ancho" => '1', "alin" => 'C', "last" => true);
	$arrayGrilla[6] = array("campo" => 'estado',			"titulo" => 'esta',			"ancho" => '1', "alin" => 'C');
	
	include(PATH_INCLUDES.'grillaLeeSinPag.php'); 


	// :::::::::::::::::::::::::::::::::::::::::::: Carga Plantilla ::::::::::::::::::::::::::::: //
	$contenido=new plantilla("sistemas_modulos");
	$contenido->asigna_variables(
			array(
			"lang"  				=> $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  	=> date('d-m-Y'),
            "topbar"		      	=> $topbar,
			"lateral"			  	=> $lateral,
			"H2Sistema"			  	=> 'Mantenedor Modulos Sistema',
			"H2Titulo"	          	=> 'Mantenedor Modulos Sistema',
			
			"cerrarSesion"        	=> $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  	=> $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  	=> $sistema_txt->GetDefinition('borrar'),
            "salir"        	      	=> $dg_txt->GetDefinition('salir'),
			
			"id"				    => $sisId,
			"nombre"				=> $nombre,
			
			"idModulo"				=> $idModulo,
			"modulo" 				=> $modulo,
			"php" 					=> $php,
			"parametros" 			=> $parametros,
			"tipo" 					=> $tipo,
			"target" 				=> $target,
			"estado" 				=> $estado,
			"sistemaVer" 			=> $sistemaVer,
			"movil" 				=> $movil,	
				

			"targetT"				=> $targetT,
			"targetB"				=> $targetB,
			
			"estado1"				=> $estado1,
			"estado0"				=> $estado0,
			
			"sistemaVer1"			=> $sistemaVer1,
			"sistemaVer2"			=> $sistemaVer2,
			
			"optionEstMenu"			=> $optionEstMenu,
			"grillaTitHTML"			=> $grillaTitHTML,
			"grillaHTML"			=> $grillaHTML,
			
			"dispEdit"				=> $dispEdit,
			"ronly"					=> $readonly,
			"rIdModu"				=> $rIdModu,
			));
			
	echo $contenido->muestra();
?>
