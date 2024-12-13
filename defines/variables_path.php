<?php
    date_default_timezone_set('America/Santiago');
    $root_sistema = "sistemas/";

    if (!defined('VERSION')) {
    	define('VERSION', '0.1');

    	/* :::::::::::::::::::::: PATHS ::::::::::::::::::::::: */
        if ($_SERVER['DOCUMENT_ROOT'] == '/var/www' or  $_SERVER['DOCUMENT_ROOT'] == '/var/www/html') {
            // Para que funcione entrando por IP Local
            define('PATH_ROOT', $root_sistema);
            define('PATH_BASE', "{$_SERVER['DOCUMENT_ROOT']}/" . PATH_ROOT);
            } else {
            define('PATH_ROOT', '');
            define('PATH_BASE', "{$_SERVER['DOCUMENT_ROOT']}/");
        }
       
        define('LINK_SOPORTE',  'http://ayuda.red-m.cl/');
        define('RUTA_ABSOLUTA', 'http://admin.red-m.cl/');

    	define('PATH_DEFINES',   PATH_BASE . 'defines/');
        define('PATH_CRT',       PATH_BASE . 'crt/');
    	define('PATH_INCLUDES',  PATH_BASE . 'includes/');
    	define('PATH_CLASSES',   PATH_BASE . 'classes/');
    	define('PATH_TEMPLATES', PATH_BASE . 'templates/');
    	define('PATH_LANGUAGE',  PATH_BASE . 'language/');
    	define('PATH_STYLES',    PATH_BASE . 'styles/');
    	define('PATH_SCRIPTS',   PATH_BASE . 'js/');
    	define('PATH_EXTERNOS',  PATH_BASE . 'externos/');
    	define('PATH_IMAGES',    PATH_BASE . 'images/');
		define('PATH_ICONOS',    PATH_BASE . 'iconos/');
        define('PATH_FOTOS',     PATH_BASE . 'fotosUsr/');
        define('PATH_PLANOSTXT', PATH_BASE . 'planosTxt/');
        define('PATH_FONTS',     PATH_BASE . 'fonts/');
        
        define('PATH_FIRMAS',    PATH_BASE . 'firmas/');
        define('PATH_FIRMASWEB', RUTA_ABSOLUTA . 'firmas/');
              
        define('PATH_MENU',         'modulos/');
        define('PATH_MENU_CON',     'consultas/');
        define('PATH_MENU_INF',     'informes/');
        define('PATH_MENU_TAB',     'tablas/');
        define('PATH_MENU_FILE',    'files/');
        define('PATH_MENU_ANTIGUO', '');  
    } 
?>
