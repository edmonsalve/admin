<?php  
	require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');
	// require_once(PATH_INCLUDES . 'redm.php');

	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }

	$area = 'M';
	if (isset($_GET['area'])) { $area = $_GET['area']; }

	switch($area) {
		case 'T': $st = " selected='selected ' "; $sm = ""; $ss = ""; $se = ""; $sc = ""; $sa = ""; $filtroArea = ""; break;
		case 'M': $st = ""; $sm = " selected='selected ' "; $ss = ""; $se = ""; $sc = ""; $sa = ""; $filtroArea = " WHERE area = 'M' "; break;
		case 'S': $st = ""; $sm = ""; $ss = " selected='selected ' "; $se = ""; $sc = ""; $sa = ""; $filtroArea = " WHERE area = 'S' "; break;
		case 'E': $st = ""; $sm = ""; $ss = ""; $se = " selected='selected ' "; $sc = ""; $sa = ""; $filtroArea = " WHERE area = 'E' "; break;
		case 'C': $st = ""; $sm = ""; $ss = ""; $se = ""; $sc = " selected='selected ' "; $sa = ""; $filtroArea = " WHERE area = 'C' "; break;
		case 'A': $st = ""; $sm = ""; $ss = ""; $se = ""; $sc = ""; $sa = " selected='selected ' "; $filtroArea = " WHERE area = 'A' "; break;
	}
	
    // ::::::::: Determina Nombre de Sript en Ejecucion ::::::::: //
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);


    // ::::::::::::::::::::::::::::::::: DB  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$DB_ADMIN = DB_ADMIN;
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_SISTEMA, DB_SERVER, DB_USER, DB_PASSWD );

    // :::::::::::::: Nombre de Tabla y Campo Indice :::::::::::: //
    $tablaDB      = "adm_sistemas";
	$IdCampo      = "id";
    $orderBy      = "nombre";


    // :::::::::::::::::::::::::::::::::::::: Criterios de Busqueda  :::::::::::::::::::::::::::::::::::: //
    $arrayCriterios[1] = array("campo" => "nombre",      "descripcion" => "nombre" );
    $arrayCriterios[2] = array("campo" => "id",  		 "descripcion" => "id" );
   
    // :::::::::::::::::::::::::::::::::: Lee parametros $_GET  para eliminar  :::::::::::::::::::::::::: //
	
	if (isset($_GET['ope'])) { 
		if ($_GET['ope'] == 'Delete') {
			$locationFile = $_SERVER['SCRIPT_NAME'];
			$errorMensaje = "ERROR_borrar";
			include(PATH_INCLUDES.'grillaEliminacion.php');
		}
	}

    // :::::::::::::::::::::::::::::::::::::::: Campos para Grilla  :::::::::::::::::::::::::::::::::::::: //
    $conFicha   		= TRUE;
	$funcionBusqueda	= "buscarResol";
	
    $conBorrar  = FALSE;  $btnClassBorra = "enviarpeqRojo";
    $conBotAux1 = FALSE;  $funcionAux1 	 = "";  $txtBtn1 = "";
    $conBotAux2 = FALSE;  $funcionAux2 	 = "";  $txtBtn2 = "";

	$arrayGrilla[1] = array("campo" => 'situacion',			"titulo" => 'situacion',	"ancho" => '1', "alin" => 'C', "rowColor" => 'yellow,red,orange', "valorCol" => 'A,B,D', "oculta" => true);
    $arrayGrilla[2] = array("campo" => 'id',      			"titulo" => 'ID',      		"ancho" => '1', "alin" => 'R' );
    $arrayGrilla[3] = array("campo" => 'nombre',  			"titulo" => 'nombre',  		"ancho" => '7');
	$arrayGrilla[4] = array("campo" => 'area',				"titulo" => 'area',  		"ancho" => '1', "alin" => 'C');
	$arrayGrilla[5] = array("campo" => 'ruta',				"titulo" => 'ruta',  		"ancho" => '5' );
	$arrayGrilla[6] = array("campo" => 'icono',				"titulo" => 'icono',  		"ancho" => '5', "last" => true );
	
	// :::::::::::::::::::::::::::::::::::::::::::: Lee Grilla :::::::::::::::::::::::::::::::::::::::::::: //
	$consultaGrilla = "SELECT * FROM adm_sistemas ";
	$where = " $filtroArea ";
	$orden = " ORDER BY nombre "; 
	
    include(PATH_INCLUDES.'grillaLeeConConsulta.php'); 


	// ::::::::::::::::::::::::::::::::::::::: Carga Plantilla :::::::::::::::::::::::::::::::::::::::::::: //
	$contenido=new plantilla("sistemas");
	$contenido->asigna_variables(
			array(
			"lang"                => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
            "topbar"		      => $topbar,
			"lateral"			  => $lateral,
		
			"cerrarSesion"        => $dg_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  => $dg_txt->GetDefinition('buscarpor'),
			"borrar"        	  => $dg_txt->GetDefinition('borrar'),
            "salir"        	      => $dg_txt->GetDefinition('salir'),

			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
			"H2Titulo"	          => 'Mantenedor de Sistemas',

			"grillaTitHTML"       => $grillaTitHTML,
            "grillaHTML"          => $grillaHTML,

			"paginacionHTML"      => $paginacionHTML,
            "htmlCriterios"       => $htmlCriterios,

            "moduloPHP"           => $moduloPHP,
			"iguala"		      => $criterio,
			
			"st"	=> $st,
			"sm"	=> $sm,
			"ss"	=> $ss,
			"se"	=> $se,
			"sc"	=> $sc,
			"sa"	=> $sa,
		));

	echo $contenido->muestra();
?>
