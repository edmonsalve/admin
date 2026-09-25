<?php 
    date_default_timezone_set('America/Santiago');

    if (!defined('VERSION')) {
    	define('VERSION', '2.0');
        
        $variablesCli = parse_ini_file("/etc/dMuniIni/dMuniCli.ini");
        $variablesIni = parse_ini_file("/etc/dMuniIni/dMuniIni.ini");

		// ::: URL
		define('RUTA_FISICA',    $variablesCli['ruraFisica']); 
		define('RUTA_ABSOLUTA',  $variablesCli['rutaAbs']); 

        // ::: Soporte
        define('LINK_SOPORTE',   $variablesIni['urlSoporte']);

        // ::: APIS
        define('API_PECIR',      $variablesIni['apiPerCir']); 
        define('API_PATCOM',     $variablesIni['apiPatCom']);
        define('API_AYUDSOC',    $variablesIni['apiAyuSoc']);
        define('API_PERSONAL',   $variablesIni['apiPersonal']);
        define('API_SOC',        $variablesIni['apiSOC']);

    	// ::: PATHS 
        define('PATH_ROOT', '');
        define('PATH_BASE', "{$_SERVER['DOCUMENT_ROOT']}/");

        define('PATH_BOTONES',   PATH_BASE . 'btns/');
        define('PATH_CLASSES',   PATH_BASE . 'classes/');
        define('PATH_COMUN',     PATH_BASE . 'comun/');
        define('PATH_DEFINES',   PATH_BASE . 'defines/');
        define('PATH_EXTERNOS',  PATH_BASE . 'externos/');
        define('PATH_FONTS',     PATH_BASE . 'fonts/');
        define('PATH_IMAGES_CLI',PATH_BASE . 'imagesCli/');
        define('PATH_IMAGES',    PATH_BASE . 'images/');
        define('PATH_INCLUDES',  PATH_BASE . 'includes/');
        define('PATH_LANGUAGE',  PATH_BASE . 'language/');
        define('PATH_PLANOSTXT', PATH_BASE . 'planosTxt/');
        define('PATH_SCRIPTS',   PATH_BASE . 'js/');
        define('PATH_STYLES',    PATH_BASE . 'styles/');
        define('PATH_TEMPLATES', PATH_BASE . 'templates/');
        define('PATH_VENDOR',    PATH_BASE . 'vendor/');
        
        // ::: Directorios
        define('ROOT_CME',     'appCme/' );
        define('ROOT_COMUN',   'appComun/' );
        define('ROOT_CONTA',   'appContabilidad/' );
        define('ROOT_EDUC',    'appEduc/' );
        define('ROOT_HTAS',    'appHtas/' );
        define('ROOT_INTRA',   'appIntranet/' );
        define('ROOT_MUNI',    'appMuni/' );
        define('ROOT_SAI',     'appSai/' );
        define('ROOT_SALUD',   'appSalud/' );
        define('ROOT_UTIL',    'appUtilidades/' );
        
        define('PATH_MENU',         'modulos/');
        define('PATH_MENU_CON',     'consultas/');
        define('PATH_MENU_INF',     'informes/');
        define('PATH_MENU_TAB',     'tablas/');
        define('PATH_MENU_FILE',    'files/');
        

        // :: almacenes & path sistema
        define('ALMACEN_AUX',       $variablesIni['almacenAux'] );
        define('ALMACEN_CERTS',     $variablesIni['almacenCerts'] );
        define('ALMACEN_FIRMAS',    $variablesIni['almacenFirmas'] );
        define('ALMACEN_FOTOS',     $variablesIni['almacenFotos'] );

        define('PATH_AUX',          '/almacenAux/');
        define('PATH_CERTS',        '/almacenCerts/');
        define('PATH_FIRMAS',       '/almacenFirmas/');
        define('PATH_FOTOS',        '/almacenFotos/');

        // :: clave maestra
        define('CLAVE_MAESTRA',     trim(sha1($variablesIni['claveMaestra'])) );
    }
?>