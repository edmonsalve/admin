<?php 
	if (isset($_GET['mod'])) { $idModuloIco = $_GET['mod']; } else { $idModuloIco = 0; }
	require_once('../includes/initSistema.php');

    unset($_SESSION['usuarios_ficha_id'], $_SESSION['usuarios_ficha_area']);

    $userId     = $_SESSION['idUser'];
    $nomUsuario = $_SESSION['nomUsuario'];
	
    // ::: script en ejecución
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);
	
    // :: Leer datos JSON desde el cuerpo
    $input = json_decode(file_get_contents('php://input'), true);
    // ! foreach ($input as $key => $value) { echo "<br>$key : $value";} //exit; 
	
    $_pag       = $input['pag'] ?? 1;   
    $auxiliares = $input['auxiliares'] ?? [];
    $optionsTPL = $input['ordenarPorOptions'] ?? [];
    $arrayCampos = [];
    $arrayValores = [];
	
    $_ordenarPor = isset($input['ordenarPor']) ? $input['ordenarPor'] : 's.nombre';
    $_sentido    = (isset($input['sentido']))    ? $input['sentido']  : 'ASC';
    $_filas      = (isset($input['filas']))      ? $input['filas']    : PAGINACION;
    if (!isset($_SESSION['filas'])) { $_SESSION['filas'] = 15; }
	
    // :: auxiliares recibidos por JSON
    foreach ($auxiliares as $filtros) {
		$arrayCampos[$filtros['idaux']]  = $filtros['campo'];
        $arrayValores[$filtros['idaux']] = $filtros['valor'];
        // ! echo "<br>Auxiliar: ".$filtros['idaux']." Campo: ".$filtros['campo']." Valor: ".$filtros['valor'];
    }
	
	$criterio  = $input['iguala'] ?? ($input['aux0'] ?? '');
    $buscarpor = $input['buscarpor'] ?? ($input['aux1'] ?? '');

	if (trim($criterio) == '') {
		$criterio = (isset($_SESSION['iguala'])) ? $_SESSION['iguala'] : '';
	}


    // ** ➡️  debug aux arrays; solo para desarrollo envia un error muestra un mensaje si hay diferencia entre los 
    // **     campos de la plantilla y los del array usados para la primera carga, si hay diferencia detiene la ejecución
    // **     no puedo usar el mismo array que viene por json porque no esta disponible en la primera carga, 
    // **     la ejecución posterior es asoncrónica y esta contenida en el despliegue de la grilla
    $ordenarPorOptions    = array('s.nombre', 'a.area');
    foreach ($ordenarPorOptions as $key => $value) {
        if ($value == ($_SESSION['ordenarPor'] ?? $_ordenarPor)) { 
            $_ordSel[$key] = 'selected';
        } else {
            $_ordSel[$key] = '';
        }
    }  

    if (count($optionsTPL) != 0) {
        for ($i = 0; $i < count($ordenarPorOptions); $i++) { 
            if (($optionsTPL[$i] ?? null) != $ordenarPorOptions[$i]) {
                echo "<br>❌ $i TPL: ".$optionsTPL[$i]." Array: ".$ordenarPorOptions[$i];
                exit;
            }
        }
    }
    
    // ::: filtros auxiliares
    $estadoA = '';
    $estadoI = '';
    $estadoB = '';
    $estadoT = '';
	if (isset($arrayCampos['aux1']) || isset($_SESSION['aux1'])) { 
        // estado
		if (isset($arrayCampos['aux1'])) {
			$_filtroEstado = $arrayValores['aux1']; 
            $_SESSION['aux1'] = $_filtroEstado;
		} else {
			$_filtroEstado = $_SESSION['aux1'];
		}

		if ($_filtroEstado == 'S') { $estadoA = "selected"; $estadoI = ""; $estadoB = ""; $estadoT = ""; }
		if ($_filtroEstado == 'N') { $estadoA = ""; $estadoI = "selected"; $estadoB = ""; $estadoT = ""; }
		if ($_filtroEstado == '')  { $estadoA = ""; $estadoI = ""; $estadoB = ""; $estadoT = "selected"; }
	}

    if (!isset($_filtroEstado)) {
        $_filtroEstado = 'S';
        $_SESSION['aux1'] = $_filtroEstado;
        $estadoA = "selected";
    }

    $_filtroArea = '';
    if (isset($arrayCampos['aux2'])) {
        $_filtroArea = $arrayValores['aux2'];
        $_SESSION['aux2'] = $_filtroArea;
    } elseif (isset($_SESSION['aux2'])) {
        $_filtroArea = $_SESSION['aux2'];
    }
     
    // ::: conexión a la BD
    $DB_DCODE    = 'adm_dCode';
    $DB_ADMIN    = DB_ADMIN;
    $DB_CLIENTE  = DB_CLIENTE;
    $conexionDB = new DB_MySQLi;
	$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD );    
    
    $optionAreas = '';
    $consultaAreas = "SELECT idArea, area
                             FROM `$DB_DCODE`.adm_areas
                             ORDER BY area";
                             
                             
    $salidaAreas = $conexionDB->consulta($consultaAreas);
    while ($rowArea = mysqli_fetch_array($salidaAreas)) {
        $idArea = $rowArea['idArea'];
        $area = $rowArea['area'];
        $selected = ((string) $_filtroArea === (string) $idArea) ? 'selected="selected"' : '';
        $optionAreas .= "<option value='$idArea' $selected>$area</option>";
    }

    if (!isset($pagina)) { $pagina = 1; } 

    // :: criterios de busqueda
    $arrayCriterios[1] = array('campo' => 'L,s.nombre',  	'descripcion' => 'Nombre' );
	$arrayCriterios[2] = array('campo' => 'L,s.ruta',     	'descripcion' => 'Ruta' );

    // ::: incluir nro filas por pagina, sentido
    include(PATH_INCLUDES . '@grillaSentidoFilas.php');
    include(PATH_INCLUDES . '@grillaCriterios.php'); 
    // ::: BD
    $tablaDB      = "adm_sistemas";
	$IdCampo      = "id";
    
    
    // :: tabla de datos
    $tablaDatos = [
        "consulta" => "SELECT  
								s.id, 
								s.ruta, 
								s.nombre,
								s.estado,
                                s.icono,
                                prefijoTablas,
                                dbase,
								if (s.estado = 'N', 'yelow', '') AS colorFila,
								COALESCE(a.area, 'S/I') AS area
							FROM `$DB_DCODE`.adm_sistemas s
							LEFT JOIN `$DB_DCODE`.adm_areas a ON   a.idArea = s.area",
                        
        "ordenarPor" => "$_ordenarPor $_sentido",

        "columnas" => [
                        ["campo" => 'colorFila'],
                        ["campo" => 'nombre',      		"ancho" => '8', "titulo" => 'Sistema',             "alin" => 'L'],
                        ["campo" => 'area',        		"ancho" => '4',  "titulo" => 'Área',                "alin" => 'L'],
                        ["campo" => 'prefijoTablas',    "ancho" => '1',  "titulo" => 'Prefijo',             "alin" => 'L'],
                        ["campo" => 'dbase',       		"ancho" => '3',  "titulo" => 'Base de Datos',       "alin" => 'L'],
                        ["campo" => 'icono',       		"ancho" => '5',  "titulo" => 'Icono',               "alin" => 'L'],
        ],

        "accionesG1" => [
                        ["funcion" => "editarPost",      "icono"=> "btn_editar.png",     "titulo" => "editar",        "parametros" => "id",   "paramEstaticos"  => "",   "modPHP"  => 'sistemas'  ],     
                        ["funcion" => "modulosSistemas", "icono"=> "btn_modulos2.png",   "titulo" => "modulos",       "parametros" => "id",   "paramEstaticos"  => "",   "modPHP"  => ''  ],     
        ],    

        "setupTabla"  => [
            "funcionBusqueda" => "filtrarJSON",
            "conPaginacion"   => true,
            "lineasPorPagina" => $_filas,
            "soloConFiltro"   => false,
            "conFiltro"       => true,
            "ignorarWhere"    => false,
            "accionesGrupo1"  => true,
            "anchoAccGrupo1"  => "2",
            "tituloAccGrupo1" => "Acc.",
            "accionesGrupo2"  => false,
            "anchoAccGrupo2"  => "0",
            "tituloAccGrupo2" => "",
            "grillaPeq"       => false,
            "colorEncabezado" => "", 
            "manuscrito"      => false,
            "verConsulta"     => false,
        ],
    ];
    
    // Detectar si la petición viene por JSON/AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));
    
    if ($isAjax) { 
        // echo "<br>AJAX Request Detected";
        header('Content-Type: text/html; charset=UTF-8');
        
        // solo grilla
        ob_start();
        $_async = 1;
        
        include(PATH_INCLUDES.'grillaLeeRes3.php');
        $html = ob_get_clean();

        echo $html;
        exit; // evita seguir hasta la plantilla completa   
    } else {
        // página completa
        $_async = 0;
        // echo "<br>Normal Request Detected";

    }

	// :::::::::::::::::::::::::::::::::::::::::::: Campos Plantilla :::::::::::::::::::::::::::::::::::::::::::: //
	$contenido=new plantilla('sistemas');
	$contenido->asigna_variables(
		array(
			'lang'  			  	=> $sistema_txt->GetDefinition('XMLLang'),
			"icono"	  		      	=> PATH_ICO.ICONO_NAVEGADOR, 
            "moduloPHP"				=> $moduloPHP,
			
			"topbar"		      	=> $topbar,
			"barraLateral"		  	=> $barraLateral,
            'headerMenu'		  	=> $headerMenu,

            "dispFormPerson"        => $dispFormPerson,
            "origen"                => $origen,
            "mod"                   => $mod,
		
			"H2Sistema"			  	=> $sistema_txt->GetDefinition('H2Sistema'),
            "H2Titulo"		        => 'Mantenedor sistemas',
						
            "salir"		          	=> $dg_txt->GetDefinition('salir'),
            "filtrarpor"		  	=> $dg_txt->GetDefinition('filtrarpor'),
			'cerrarSesion'        	=> $dg_txt->GetDefinition('cerrarSesion'),
			'guardar'        	  	=> $dg_txt->GetDefinition('guardar'),
			'borrar'        	  	=> $dg_txt->GetDefinition('borrar'),
            'salir'        	      	=> $dg_txt->GetDefinition('salir'),
            
            "datosPers"           	=> $dg_txt->GetDefinition('datosPers'),
			"rut"            		=> $dg_txt->GetDefinition('rut'),
            "dv"             		=> $dg_txt->GetDefinition('dv'),
            "paterno"     	 		=> $dg_txt->GetDefinition('paterno'),
            "materno"     	 		=> $dg_txt->GetDefinition('materno'),
            "nombre"     	 		=> $dg_txt->GetDefinition('nombre'),
            "estado_civ"     		=> $dg_txt->GetDefinition('estado_civ'),
            "sexo"           		=> $dg_txt->GetDefinition('sexo'),
            "fecha_nac"      		=> $dg_txt->GetDefinition('fecha_nac'),
            "direccion"      		=> $dg_txt->GetDefinition('direccion'),
            "ciudad"         		=> $dg_txt->GetDefinition('comuna'),
			"telefono"       		=> $dg_txt->GetDefinition('telefono'),
            "movil"          		=> $dg_txt->GetDefinition('movil'),
            "email"          		=> $dg_txt->GetDefinition('email'),

            
			'grillaHTMLTit'		  	=> $encabezadoHTML,
			'filasHTML'     	  	=> $filasHTML,
            'htmlCriterios'  	  	=> $htmlCriterios,
			'paginacionHTML' 	  	=> $paginacionHTML,
			'pagina'		 	  	=> $pagina,
			'iguala'		 	  	=> $criterio,
			'buscarpor'		 	  	=> $buscarpor,

            'optionAreas'           => $optionAreas,
            'estadoA'               => $estadoA,
            'estadoI'               => $estadoI, 
            'estadoB'               => $estadoB,
            'estadoT'               => $estadoT,

            // ordenarPor options
            'sentASC'               => $sentASC,
            'sentDESC'              => $sentDESC,

            '_ord0'                 => $_ordSel[0],
            '_ord1'                 => $_ordSel[1],

            'fl8'                   => $fl8,
            'fl10'                  => $fl10,
            'fl12'                  => $fl12,
            'fl15'                  => $fl15,
            'fl20'                  => $fl20,

            "_async"                => $_async,
		));
			
	echo $contenido->muestra();
?>
