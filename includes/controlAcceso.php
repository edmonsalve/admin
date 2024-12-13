<?php

    /* ::::::::::::::: DEFINE CONSTANTES  DE ACCESO, BORRAR, GUARDAR  :::::::::::::::::::::. */
    define('PERMITE_ACCESO',  $_SESSION['login']);
    if (!PERMITE_ACCESO) { header("Location:index.php"); }

    define('PERMITE_BORRAR',  $_SESSION['delete']);
    define('PERMITE_GUARDAR', $_SESSION['save']);


    /* :::::::::::::::::::::::::::::::: DEFINE VARIABLES  ::::::::::::::::::::::::::::::::: */
    $licencia 	= $_SESSION['licencia'];
	$rut      	= $_SESSION['rut'];
	$serie    	= $_SESSION['keynumber'];
?>