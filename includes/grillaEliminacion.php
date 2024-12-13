<?php    
    $error = '';

    $IdRegistro = $_GET['IdRegistro']; 

	$consultaDel = "DELETE FROM `$tablaDB` WHERE `$IdCampo` = '$IdRegistro'";
	$conexionDB->consulta($consultaDel);
	
	$accion  = "DEL";
	$detalle = "ELIMINA REGISTRO: $IdRegistro TABLA: $tablaDB";
?>