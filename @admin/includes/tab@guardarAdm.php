<?php 

    $error = '';
    if ($permiteGuardar) {
		$i = 1;
        $j = count($_POST); 
        
        $field   = '(';
        $valores = '(';
        $set     = '';
        
        $Id = $_POST[$IdCampo];
        
        foreach ($_POST as $campo => $valor) {
        	if ($valor != 'new') {
                
                $field   .= "`$campo`";
                if (substr($campo,0,5) == 'fecha') { $valor = toFecAMD($valor); }
                $valor    = str_replace(",","",$valor);
                $valores .= "'$valor'";
                
            
                $set .= "`$campo` = '$valor'";
                
                if ($i < $j) { 
                    $field   .= ','; 
                    $valores .= ','; 
                    $set     .= ','; 
                } 
            	$i++;
             } else { $i++; }
        }
        $field   .= ')';
        $valores .= ')';
        
        $consultaNEW = "REPLACE INTO `$tablaDB`  $field values $valores";
        $consultaUPD = "UPDATE `$tablaDB` SET $set WHERE `$IdCampo` = $Id";
		
        if ($Id == 'new') { $consulta = $consultaNEW; } else { $consulta = $consultaUPD; }

        $consulta = $consultaNEW;
		if (!$conexionDB->consulta($consulta)) { $error .= mysql_error()."Error al guardar datos tabla:  $tablaDB<br />";
            } 
            else {
			if ($Id == 'new' ) { $IdRegistro = $conexionDB->ultimoId(); } else { $IdRegistro = $IdCampo; }
		}
        
	}
	echo $error;
?>
