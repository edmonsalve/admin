<?php
    /*
		Version: 10.01
		
		NOTA IMPORTANTE: no de pueden usar campos 'radio-set', no deran guardados se usan para fichas ejempro el sistema ayudas sociales ficha:ficha.tpl
		
		// Cuando se usen campos type "checkbox" se reeplazara el valor 
		$camposCheckbox = ('campochk1','campochk2','campochk3',......);
		
		// Cuando el Id no es auto incremental se reemplaza el valor por el campo indicado
		$reemplazarpor: [NombreCampo], cuando Id no es incremental
		$mayusculas:    TRUE / FALSE
		$objetivo :		Indicar NOMBREdeCAMPO identificatorio para log, como placa, rut, rol, etc
		
		$muestraPost: 		TRUE / FALSE  muestra consulta ejecutada 
		$muestraConsulta: 	TRUE / FALSE  muestra consulta ejecutada 
		
		$ejecutaConsulta:   TRUE / FALSE
    */
	
	if (!isset($ejecutaConsulta)) { $ejecutaConsulta = TRUE; }
	if (!isset($muestraConsulta)) { $muestraConsulta = FALSE; }
	if (!isset($muestraPost)) { $muestraPost = FALSE; }
	
	if ($muestraPost) { foreach($_POST as $campo => $valor) { echo "$campo => $valor<br>"; } }
	
	//:: postIgnorar 
	$ignorar = FALSE;
	if (isset($ignorarPost)) {
		$ignorar = TRUE;
		$arrayIgnPost 	= explode(",",$ignorarPost);
		foreach($arrayIgnPost as $ind => $campo)  { 
			$arrayIgnorar[trim($campo)] = $campo;
		} 
	}
	
	$reemplazarCheckbox = false;
	if (isset($camposCheckbox)) { 
		$reemplazarCheckbox = true;
		$arrayCheckbox 	= explode(",",$camposCheckbox);
		foreach($arrayCheckbox as $ind => $campo)  { 
			$arrayChkbx[trim($campo)] = $campo;
		} 
	}	
	
    if (!isset($mayusculas)) { $mayusculas = true; }
	

    $error      = '';
    $camposPost = count($_POST); 
    
    
    
    /* ::: CUENTA CAMPOS RADIO-SET UTILIZADOS EN LAS PESTAÑAS DENTRO DE LAS PLANTILLAS Y REGISTROS HIJOS ::: */
    if (isset($_POST['registroHijo'])) { 
        $registroHijo = $_POST['registroHijo']; 
        $camposPost--; 
        } else { 
        $registroHijo = 0; 
    } 
    
    if (isset($IdPadre)) {  
        if (isset($_POST[$IdPadre])) {
            $idPadre = $_POST[$IdPadre];
        }
    }
    
    
    $camposProgramacion = 0; 
    foreach ($_POST as $campo => $valor) {  if  ($campo == 'radio-set' ) { $camposProgramacion++; } }     // echo "<br>$cont:$campo => $valor"; $cont++;
        
	$i 			= 1;
    $camposPost = $camposPost - $camposProgramacion;    // Descuenta $camposProgramacion pestañas   *";

    $field   = '(';
    $valores = '(';
    $set     = '';
    
    $Id = $_POST[$IdCampo];  
    
    
    if  ($Id == 'new') {
        if (isset($reemplazarpor)) {
            if (trim($reemplazarpor) != '' ) {
                $field   .= "`$IdCampo`,";
                $valor    = $_POST[$reemplazarpor];
                $valores .= "'$valor',";   
            }
        }
    }
	
    foreach ($_POST as $campo => $valor) {
        if  ($campo != 'radio-set' AND $campo != 'registroHijo' AND $campo != "$IdCampo" ) {    
		
			if (gettype($valor) != 'array') {
				if ($valor != 'new') {
					if (substr($campo,0,5) == 'fecha') { 
						if (trim($valor) == '' or trim($valor) == '--') { $valor = "0000-00-00"; }
					}
					
					if ($mayusculas) { $valor    = strtoupper($valor); }
					
					// Reemplaza valor ON por 1 de los campos checkbox especificados en la variable $camposCheckbox
					// esto ya esta descontinuado --- if ($valor == 'ON' or $valor == 'on'  or $valor == 'on') { $valor = 1; }
					if ($reemplazarCheckbox) { if (isset($arrayChkbx[$campo])) { $valor = 1;  } }
					
					$valor    = str_replace(",","",$valor);
					
					$saltar = FALSE;
					if ($ignorar) {
						// echo " ** paso **";
						if (isset($arrayIgnorar[$campo])) { $saltar = TRUE; }
					}
				
					if (trim($valor) != '' AND !$saltar) { 
						if ($i < $camposPost AND $i > 1) { 
							if (trim($field) != "") { $field .= ','; }
							if (trim($valores) != "") { $valores .= ','; }
							if (trim($set) != "") { $set .= ','; }
						} 
						
						$field   .= "`$campo`";
						$valores .= "'$valor'";
						$set 	 .= "`$campo` = '$valor'";
					} 
					$i++; 
				}  
			} else {
				$i++; 
			}
        }
    }
	
	// recorre campos checkbox que no tengan valor
	if (isset($camposCheckbox)) { 
		$addCampos = 0;
		foreach($arrayChkbx AS $campoArray => $datoArray) {
			if (!isset($_POST[$campoArray])) { $addCampos++; }
		}
		
		if ($addCampos > 0) {
			$addCampos--;
			
			$field   .= ','; 
			$valores .= ','; 
			$set     .= ','; 
					
			$i = 1;
			foreach($arrayChkbx AS $campoArray => $datoArray) {
				if (!isset($_POST[$campoArray])) { 
					$field   .= "`$campoArray`"; 
					$valores .= "'0'"; 
					$set 	.= "`$campoArray` = '0'";
				
				
					if ($i <= $addCampos) { 
						$field   .= ','; 
						$valores .= ','; 
						$set     .= ','; 
					} 
					$i++;
				}
			}
		}
	}

    $field   .= ')';
    $valores .= ')';
    
    $consultaNEW = "REPLACE INTO `$guardarTabla`  $field values $valores";
    $consultaUPD = "UPDATE `$guardarTabla` SET $set WHERE `$IdCampo` = '$Id'";
	
    if ($Id == 'new') { $consulta = $consultaNEW; $accion  = "ADD";  } else { $consulta = $consultaUPD; $accion  = "UPD"; }

	if (isset($muestraConsulta)) {
        if ($muestraConsulta) {  echo "</br>Qry: $consulta   **</br>";	}
    }
	
	if ($ejecutaConsulta) {
		if (!$conexionDB->consulta($consulta)) { 
			$error .= mysqli_error()."<br />Error al guardar datos tabla:  $guardarTabla<br />$consulta *****<br />"; 
			} else { 
				if ($Id == 'new' ) { 
					if ($registroHijo == 0 ) { $IdRegistro = $conexionDB->ultimoId(); } else { $IdRegistro = $idPadre; }
				} else { 
					$IdRegistro = $Id; 
				}
			
			if ($muestraConsulta) {  echo "</br>IdRegistro: $IdRegistro   ID:  $Id  ** RHijo: $registroHijo </br>";	}
		}
	} else { $IdRegistro = $Id;  }
?>