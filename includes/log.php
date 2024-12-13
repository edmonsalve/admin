<?php
    date_default_timezone_set('America/Santiago');
    
    $dbCliente  = DB_CLIENTE;
   
    $fecha      = date("Y-m-d");
    $hora       = date("H:i:s");
    $usuario    = $_SESSION['idUser']; 
    $idsistema  = 0;
    $desde      = $_SERVER['REMOTE_ADDR'];
    
     
    if (!isset($accion)) {
        switch (substr($detalle,0,1)) {
            case 'R': $accion = 'ADD'; break;
            case 'U': $accion = 'UPD'; break;
        }
    }
    
   
    
    if(!isset($tablaDB)) { $tabla = ''; }
    
	$consultaLog  = "INSERT INTO `$dbCliente`.`adm_log` 
                                (  `sistema`,   `fecha`, `hora`, `usuario`, `desde`,  `tabla`,    `accion`,  `detalle`,    `objetivo`, `objetivoTXT`) 
                        VALUES  ('$idsistema', '$fecha','$hora','$usuario','$desde', '$tablaDB', '$accion', \"$detalle\", '$objetivo', '$objetivoTXT')";


    $conexionDB->consulta($consultaLog); 
?>