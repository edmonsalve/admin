<?php
	/* :::::::::::::::::::::::: DB :::::::::::::::::::::::: */
    if (!defined('DB_SERVER')) {
    	define('DB_SERVER', 'localhost');
    	define('DB_NAME',   '@admin');
        define('DB_USER',   'root');
    	define('DB_PASSWD', 'kcm64%VI-9');

        /* :::::::::::::::::::::::::::::: DB :::::::::::::::::::::::: */
        define('DB_PREFIJO', 'adm_');
        define('DB_COMUN',   DB_PREFIJO.'@comun');
        define('DB_CLIENTE', DB_PREFIJO.'dCode');
        define('DB_ADMIN',   DB_PREFIJO.'dCode');
		
		/* ::::::::::::::::::::::::: VARIOS ::::::::::::::::::::::::: */
        define('TITULO_NAVEGADOR',  'dCode: Administración y Soporte');
		define('TITULO_ADMIN',  	'dCode: Administración');
		define('TITULO_SOPORTE',  	'dCode: Soporte');			
			
        define('LOGO_CLIENTE' ,     'dCode4x4.png');
		define('LOGO_SISTEMA' ,     'ico_sopor.png');
        define('LOGO_DOC' ,         'dMuniLog.png');
		define('VERSION_SIS' ,      '3.A 2023');
		
		define('ICONO_NAVEGADOR',	'icoDC.png');
		define('LOGOH',             'dAdmin.png'); 
		define('PATH_ICO',			'images/');
		define('EMAIL_RT',			'r.torres@red-m.cl');
		define('EMAIL_EM',			'e.monsalve@red-m.cl');
     }	 

     if (!defined('PAGINACION')) { define('PAGINACION',  '25'); }  // Numero de lineas mostradas por pagina
     if (!defined('NRO_PAGINAS')){ define('NRO_PAGINAS', '10'); }  // Numero de paginas mostradas en indice inferior
?>