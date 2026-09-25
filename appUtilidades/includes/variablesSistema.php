<?php
	/* ::::::::::::::::::::::::::::::::: DB :::::::::::::::::::::::::::::: */
	define('DB_Sistema', DB_CLIENTE); 
    define('ROOT_SISTEMA', 'appUtilidades');
    define('TAB_MASTER', '');

    /* :::::::::::::::::::::::: VARIABLES SISTEMA :::::::::::::::::::::::: */
	define('SISTEMA_ID', '000');
    
    define('PASSWD_INI', '123456');
    
	define('PATH_INCLUDES_SISTEMA',  PATH_BASE . ROOT_SISTEMA .'/includes/');
	define('PATH_CLASSES_SISTEMA',   PATH_BASE . ROOT_SISTEMA .'/classes/');
	define('PATH_TEMPLATES_SISTEMA', PATH_BASE . ROOT_SISTEMA .'/templates/');
	define('PATH_LANGUAGE_SISTEMA',  PATH_BASE . ROOT_SISTEMA .'/language/');
	define('PATH_STYLES_SISTEMA',    PATH_BASE . ROOT_SISTEMA .'/styles/');
	define('PATH_SCRIPTS_SISTEMA',   PATH_BASE . ROOT_SISTEMA .'/js/');
	define('PATH_MODULOS_SISTEMA',   PATH_BASE . ROOT_SISTEMA .'/modulos/');
	define('PATH_EXTERNOS_SISTEMA',  PATH_BASE . ROOT_SISTEMA .'/externos/');
	define('PATH_FOTOS_SISTEMA',     PATH_BASE . ROOT_SISTEMA .'/fotos/');
	define('PATH_IMAGES_SISTEMA',    PATH_BASE . ROOT_SISTEMA .'/images/');
    define('PATH_UPLOADDOC_SISTEMA', PATH_BASE . ROOT_SISTEMA .'/upLoad/');
    define('PATH_PLANOS_SISTEMA',    PATH_BASE . ROOT_SISTEMA .'/planos/');

	/* ::::::::::::::::::::: UTILIDAD :::::::::::::::::::::: */
    define('TITULO_NAV', 'Modulo Administraci&oacute;n');
?>