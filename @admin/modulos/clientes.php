<?php  
	require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');
	

	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }
	
	
    /* ::::::::: Determina Nombre de Sript en Ejecucion ::::::::: */
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);


    // ::::::::::::::::::::::::::::::::: DB  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$DB_ADMIN = DB_ADMIN;
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_SISTEMA, DB_SERVER, DB_USER, DB_PASSWD );

    /* :::::::::::::: Nombre de Tabla y Campo Indice :::::::::::: */
    $tablaDB      = "adm_clientes";
	$IdCampo      = "idCliente";
    $orderBy      = "cliente";


    /* :::::::::::::::::::::::::::::::::::::: Criterios de Busqueda  :::::::::::::::::::::::::::::::::::: */
    $arrayCriterios[1] = array("campo" => "rut",      "descripcion" => "Rut" );
    $arrayCriterios[2] = array("campo" => "cliente",  "descripcion" => "Cliente" );
   
    /* :::::::::::::::::::::::::::::::::: Lee parametros $_GET  para eliminar  :::::::::::::::::::::::::: */
	if (isset($_GET['ope'])) {
		if ($_GET['ope'] == 'DEL') {
			$locationFile = $_SERVER['SCRIPT_NAME'];
			$errorMensaje = "ERROR_borrar";
			include(PATH_INCLUDES.'grillaEliminacion.php');
		}
	}

    /* :::::::::::::::::::::::::::::::::::::::: Campos para Grilla  :::::::::::::::::::::::::::::::::::::: */
    $conFicha   = TRUE;
    $conBorrar  = FALSE;
    $conBotAux1 = FALSE;  $funcionAux1 = "";  $txtBtn1 = "";
    $conBotAux2 = FALSE;  $funcionAux2 = "";  $txtBtn2 = "";

    $arrayGrilla[1] = array("campo" => 'rut',      			"titulo" => 'RUT',      	"ancho" => '2', "alin" => 'R' );
    $arrayGrilla[2] = array("campo" => 'cliente',  			"titulo" => 'Cliente',  	"ancho" => '8');
	$arrayGrilla[3] = array("campo" => 'vencimientoLic',	"titulo" => 'Vencimiento',  "ancho" => '3', "alin" => 'C');
	$arrayGrilla[4] = array("campo" => 'prefijoBD',			"titulo" => 'Prefijo',  	"ancho" => '2', "last" => true);
	/* :::::::::::::::::::::::::::::::::::::::::::: Lee Grilla :::::::::::::::::::::::::::::::::::::::::::: */
    include(PATH_INCLUDES.'grillaLee.php'); 


	/* ::::::::::::::::::::::::::::::::::::::: Carga Plantilla :::::::::::::::::::::::::::::::::::::::::::: */
	$contenido=new plantilla("clientes");
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
			"H2Titulo"	          => 'Mantenedor de Clientes',

			"grillaTitHTML"       => $grillaTitHTML,
            "grillaHTML"          => $grillaHTML,

			"paginacionHTML"      => $paginacionHTML,
            "htmlCriterios"       => $htmlCriterios,

            "moduloPHP"           => $moduloPHP,
			"iguala"		      => $criterio,
		));

	echo $contenido->muestra();
?>
