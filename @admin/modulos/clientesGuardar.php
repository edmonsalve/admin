<?php 
   
    if (!isset($mayusculas)) { $mayusculas = false; }
	

    $error     = '';
    $j = count($_POST); 
    

	$i = 1;
    $j = $j; 
    
    $field   = '(';
    $valores = '(';
    $set     = '';
    
    $Id = $_POST['idCliente'];  
    
    
    if  ($Id == 'new') {
        if (isset($reemplazarpor)) {
            if (trim($reemplazarpor) != '' ) {
                $field   .= "`$IdCampo`,";
                $valor    = $_POST[$reemplazarpor];
                $valores .= "'$valor',";   
            }
        } 
    }
	
	$modulos = "";
    foreach ($_POST as $campo => $valor) {
	    // valida que el nombre del campo no sea entero --> como es el caso de los checkbox
		if (is_int($campo)) { $modulos .= $campo; $saltar = TRUE; } else { $saltar = FALSE; }
		
        if  ($campo != 'radio-set' and $campo != 'registroHijo' and $campo != "$IdCampo" and !$saltar) {     
             
        	if ($valor != 'new') {
                $field   .= "`$campo`";
                if (substr($campo,0,5) == 'fecha') { 
					if (trim($valor) == '' or trim($valor) == '--') { $valor = "0000-00-00"; }
				}
                
                if ($mayusculas) { $valor    = strtoupper($valor); }
                
				
                $valor    = str_replace(",","",$valor);
                $valores .= "'$valor'";
                
                if ($valor == 'ON' or $valor == 'on'  or $valor == 'on') { $valor = 1; }
                
                $set .= "`$campo` = '$valor'";
                
                $i++;
                
                if ($i < $j) { 
                    $field   .= ','; 
                    $valores .= ','; 
                    $set     .= ','; 
                } 
             }  
         }
    }
    $field   .= "`modulos`)";
    $valores .= "'$modulos')";
	$set 	 .= "`modulos` = '$modulos'";
   
    $consultaNEW = "REPLACE INTO `$guardarTabla`  $field values $valores";
    $consultaUPD = "UPDATE `$guardarTabla` SET $set WHERE `$IdCampo` = '$Id'";
	
    if ($Id == 'new') { $consulta = $consultaNEW; $accion  = "ADD";  } else { $consulta = $consultaUPD; $accion  = "UPD"; }

	if (!$conexionDB->consulta($consulta)) { 
	    $error .= mysqli_error()."<br />Error al guardar datos tabla:  $guardarTabla<br />$consulta *****<br />"; echo $error;
        } else { 
    		if ($Id == 'new' ) { 
                if ($registroHijo == 0 ) { $IdRegistro = $conexionDB->ultimoId(); } else { $IdRegistro = $idPadre; }
            } else { 
                $IdRegistro = $Id; 
            }
	}
    
    if (isset($muestraConsulta)) {
        if ($muestraConsulta) {  echo "</br>Qry: $consulta  **ID: $IdRegistro **</br>";	}
    }

?>