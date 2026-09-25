<?php    
    // :: campos que deben recibirse obligatoriamente:
    // :: $dataBase; $tablaDB, $IdCampo, $idReg (identificador del registro a eliminar)

    $error = '';
    $dataBase = $_GET['dataBase']   ?? null;
    $tablaDB  = $_GET['tablaDB']    ?? null;
    $IdCampo  = $_GET['IdCampo']    ?? null;
    $idReg    = $_GET['IdRegistro'] ?? null;
    
    if (PERMITE_BORRAR) {
        $consultaDel = "DELETE FROM `$dataBase`.`$tablaDB` WHERE `$IdCampo` = '$idReg'";  
        $conexionDB->consulta($consultaDel);
       
        $accion       = "DEL";
        $observLog    = $observLog ?? "Elimina registro: $idReg";
        $consultaLog  = $consultaDel;
        require_once('log.php');         
  }
?>