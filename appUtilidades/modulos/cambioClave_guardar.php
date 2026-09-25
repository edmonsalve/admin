<?php
    session_start();

    // :::::::::::::::::::::: VARIABLES GENERALES  ::::::::::::::::::::::: //
    require_once('../../defines/variables_path.php');
    require_once(PATH_DEFINES . '/variables.php');

    // ::::::::::::::::::::::: VARIABLES SISTEMA  :::::::::::::::::::::::: //
    require_once('../includes/variablesSistema.php');
    require_once(PATH_CLASSES . 'Class.Context.php');
    require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

    $DB_PERSON   = DB_PERSON;
    $DB_COMUN    = DB_COMUN;
    $DB_ADMIN    = DB_ADMIN;  
    $DB_CLIENTE  = DB_CLIENTE; 
    $USER_CADUC  = USER_CADUC;  

    $usrID = $_SESSION['idUser']; 

	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar($DB_COMUN, DB_SERVER, DB_USER, DB_PASSWD );

  
    $_pass1    = sha1($_POST['pass1']);

    $hoy   = date('Y-m-d');
    $nuevaCadu = strtotime($USER_CADUC , strtotime($hoy));
    $nuevaCadu = date('Y-m-d',$nuevaCadu);
   
  
    $mensajeClave   = "<b>Su clave de usuario ha sido actualizada.</b><br>Esta nueva clave tiene vigencia hasta el $toFecCadu";
    $consultaUpd    = "UPDATE `$DB_CLIENTE`.`adm_users` SET `password` = '$_pass1', `caducidad` = '$nuevaCadu' WHERE username = '$usrID'";

    $conexionDB->consulta($consultaUpd); 

    // :::::::: Datos para LOG
    $detalle = "$mensajeClave, usuario: '$usrID'";
    $tablaDB = 'adm_users';
    $_SESSION['modulo'] = 0;
    $accion = 'UPD';
    require_once(PATH_INCLUDES . '/log.php');
?>