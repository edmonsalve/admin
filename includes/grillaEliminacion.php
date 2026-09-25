<?php    
    $error = '';
    $IdRegistro = $_GET['IdRegistro'] ?? null;
    
    if (PERMITE_BORRAR) {
        $consultaDel = "DELETE FROM `$tablaDB` WHERE `$IdCampo` = '$IdRegistro'";  
        $conexionDB->consulta($consultaDel);
       
        $accion = "DEL";
        $observLog = $observLog ?? "Elimina registro: $IdRegistro";
        $consultaLog = $consultaDel;
        require_once('log.php');         
  }
?>