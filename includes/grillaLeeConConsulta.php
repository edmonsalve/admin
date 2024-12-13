<?php
    /* 
        Script que genera grilla de salida a partir de una consulta, requiere de:
		
		$verConsulta    = TRUE o FALSE;
		$consultaGrilla = Variable que contiene la consulta
		
        $conFicha      = TRUE o FALSE;  con o sin linnk a ficha mantenedora funcion ficha() por defecto, si se especifica una funcion tomara esta ultima
		
        $funcionGrilla = funcion a ejecutar, si no se especifica ejecuta ficha();  $parametrosFicha = (campoAuxiliar_1, campo_n);  

        $conBorrar = TRUE o FALSE;  con o sin boton de borrado de registro

        $conBotAux1 = TRUE o FALSE;  $funcionAux1 => Funcion javascript  $parametroAux1 = "campo_1,..., campo_N"    $constanteAux1="cons1=val1,cons2=val2,...,consN=valN"  $txtBtn1 => texto del boton;
        $conBotAux2 = TRUE o FALSE;  $funcionAux1 => Funcion javascript  $parametroAux2 = "campo_1,..., campo_N"    $constanteAux2="cons1=val1,cons2=val2,...,consN=valN"  $txtBtn2 => texto del boton;
		
		$btnClassBorra = "";
		$btnClassAux1  = "";
		$btnClassAux2  = "";

        ==================== Busqueda con campos Auxiliares ================================================
        $filtroAux1Campo  = Nombre de campo por el cual se filtrara la base de datos
        $filtroAux1Valor  = Valor del campo por el cual se filtrara;

        $filtroAux2Campo = Nombre de campo por el cual se filtrara la base de datos
        $filtroAux2Valor = Valor del campo por el cual se filtrara;

        $filtroAux3Campo = Nombre de campo por el cual se filtrara la base de datos
        $filtroAux3Valor = Valor del campo por el cual se filtrara;
        ====================================================================================================

        $arrayGrilla[1] = array("campo" => 'nombreCampo', "alin" => 'L', "titulo" => 'TituloCol', "ancho" => 'ancho', "utf" => true, 
								"last" => true, "rowColor" => 'red', "valorCol" => 'A',"oculta" => true );
								
        campo:  Nombre de campo
        alin:   Alineacion del dato L,C,R
        titulo: Titulo de la columna
        titulo: Ancho de la columan 1,2 3 (trabaca con clase 960 span-n)
        utf:    TRUE o FALSE
        lasr:   TRUE o FALSE
        substr: 18             Largo de la cadena antes de cortar
        fecha:  TRUE o FALSE
		img:    TRUE o FALSE
		
		rowColor: red   Color de la dila de la grilla cuando cumpla una caracteristica
		valorCol: 'A'   Valor que debe cumplir para el color selecciodo en rowColor
		oculta:   TRUE o FALSE  si no queremos mostrar el campo que se esta evaluando para el color
        
        "numform"   => TRUE/FALSE     Formatea numeros
        "decimales" => nroDecimales
    */

    // :::::::::::::::::::::::::::::::::::::::::::::::::::: CRITERIOS DE BUSQUEDA :::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	if (!isset($verConsulta))    { $verConsulta = FALSE; }
	
    $htmlCriterios = '';
    $selected      = '';
    
    foreach($arrayCriterios as $inf => $criterio) { 
        $campo       = $criterio['campo'];
        $descripcion = $criterio['descripcion'];
        
        if (isset($_GET['buscarpor'])){ 
            $bx = $_GET['buscarpor']; 
            if ($campo == $bx) { $selected = "selected='selected'"; } else { $selected = ""; }
        }        
        $htmlCriterios .= "<option value='$campo' $selected>$descripcion</option>";
    }	
	
	if (!isset($verConsulta))    { $verConsulta = FALSE; }
    if (!isset($funcionGrilla))  { $funcionGrilla = "ficha"; }
    if (!isset($conBotAux1))     { $conBotAux1 = FALSE; }
    if (!isset($conBotAux2))     { $conBotAux2 = FALSE; }
	if (!isset($conBotAux3))     { $conBotAux3 = FALSE; }

	if (!isset($btnClassBorra))    { $btnClassBorra = 'enviarpeq'; } 
	if (!isset($btnClassAux1))     { $btnClassAux1  = 'enviarpeq'; }
	if (!isset($btnClassAux2))     { $btnClassAux2  = 'enviarpeq'; }
	if (!isset($btnClassAux3))     { $btnClassAux3  = 'enviarpeq'; }     
    
    // :::::::::::::::::::::::::::::::::::::: Determina Paginación  :::::::::::::::::::::::::::::::::::: //
	if (!isset($where)) { $where		  = ''; } 
	$criterio     = '';

	if (isset($_GET['buscarpor'])) {
		$criterio  	= trim($_GET['iguala']);
		$buscarpor 	= $_GET['buscarpor'];

		if (trim($criterio) != '') { 
			if (trim($where) != '' ) { $where .= " AND  `$buscarpor`  like  '$criterio%' "; } else { $where .= " WHERE  `$buscarpor`  like  '$criterio%' "; }
		}

        // =========== BUSQUEDA CON CAMPOS AUXILIARES ================ //
        if (isset($_GET['filtroAux1Campo'])) {
            if (trim($_GET['filtroAux1Valor']) != '' ) {
                $filtroAux1Campo = $_GET['filtroAux1Campo'];
                $filtroAux1Valor = $_GET['filtroAux1Valor'];
                if (trim($where) == '') { $where = "WHERE `$filtroAux1Campo` = '$filtroAux1Valor'";  } else { $where .= " AND `$filtroAux1Campo` = '$filtroAux1Valor'"; }
            }
        }

        if (isset($_GET['filtroAux2Campo'])) {
            if (trim($_GET['filtroAux2Valor']) != '' ) {
                $filtroAux2Campo = $_GET['filtroAux2Campo'];
                $filtroAux2Valor = $_GET['filtroAux2Valor'];
                if (trim($where) == '') { $where = "WHERE `$filtroAux2Campo` = '$filtroAux2Valor'";  } else { $where .= " AND `$filtroAux2Campo` = '$filtroAux2Valor'"; }
            }
        }

        if (isset($_GET['filtroAux3Campo'])) {
            if (trim($_GET['filtroAux3Valor']) != '' ) {
                $filtroAux3Campo = $_GET['filtroAux3Campo'];
                $filtroAux3Valor = $_GET['filtroAux3Valor'];
                if (trim($where) == '') { $where = "WHERE `$filtroAux3Campo` = '$filtroAux3Valor'";  } else { $where .= " AND `$filtroAux3Campo` = '$filtroAux3Valor'"; }
            }
        }
	}


	if (isset($orden)) { $orden = "$orden"; } else { $orden = ""; }
	$consulta = "$consultaGrilla  $where  $orden";

	include(PATH_INCLUDES.'paginacion.php');

    $contador = 1;
	$consulta .= "LIMIT $RegInicial,".PAGINACION; 
	
	if ($verConsulta) { echo "Qry: $consulta</br>";	}
    $salida     = $conexionDB->consulta($consulta);       

    // :::::::::::::::::::::::::::::::::::::: Titulos Grilla  :::::::::::::::::::::::::::::::::::: //
    $grillaTitHTML  = '';
    $anchoLin       = 0;

    foreach ($arrayGrilla as $linea => $valoresLin) {
        
		$titulo = $valoresLin['titulo'];
		$ancho  = $valoresLin['ancho'];
		if (isset($valoresLin['oculta'])) { $oculta = $valoresLin['oculta']; } else { $oculta = false; }
		
		if (!$oculta) { 
			$anchoLin += $ancho;
		
			$last = "";
			if (isset($valoresLin['last'])) {
				if ($valoresLin['last']) { $last = "last"; }
			}
		
			$grillaTitHTML .= "
				<div class='span-$ancho enc_mensajes $last' >
					 <h3>$titulo</h3>
				</div>"; 
		}
    }

    if ($conBorrar) { $anchoDiv = $anchoLin + 2; } else { $anchoDiv = $anchoLin; }
    if ($conBotAux1){ $anchoDiv += 2; }
    if ($conBotAux2){ $anchoDiv += 2; }
	if ($conBotAux3){ $anchoDiv += 2; }

    // :::::::::::::::::::::::::::::::::::::: Datos Grilla  :::::::::::::::::::::::::::::::::::: //
    $borrar      = $dg_txt->GetDefinition('borrar');
    $contador    = 1;
	$grillaHTML  = '';
	while ($row = mysqli_fetch_array($salida)) {
		if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; }
		$contador++;

        $idReg  = $row[$IdCampo];  
		$js     = "'$moduloPHP','$idReg'";
		
		// boton Aux1 :::
		$jsAux1     = "'$idReg'";
		if (isset($parametroAux1) AND trim($parametroAux1) != '') { 
			$parametros1 	= explode(",",$parametroAux1);
			foreach($parametros1 as $ind => $campo)  { 
				$campoAux1	= $row[$campo];  
				$jsAux1    .= ",'$campoAux1'"; 
			} 
		}
		
		if (isset($constanteAux1) AND trim($constanteAux1) != '') { 
			$constante1 	= explode(",",$constanteAux1);
			foreach($constante1 as $ind => $constante)  { 
				$constAux1	= $constante;  
				$jsAux1    .= ",'$constAux1'"; 
			} 
		}
		
		// boton Aux1 :::
		$jsAux2     = "'$idReg'";
		if (isset($parametroAux2) AND trim($parametroAux2) != '') { 
			$parametros2 	= explode(",",$parametroAux2);
			foreach($parametros2 as $ind => $campo)  { 
				$campoAux2	= $row[$campo]; 
				$jsAux2    .= ",'$campoAux2'"; 
			} 
		}
		
		if (isset($constanteAux2) AND trim($constanteAux2) != '') { 
			$constante2 	= explode(",",$constanteAux2);
			foreach($constante2 as $ind => $constante)  { 
				$constAux2	= $constante;  
				$jsAux1    .= ",'$constAux2'"; 
			} 
		}
		
		// boton Aux1 :::
		$jsAux3     = "'$idReg'";
		if (isset($parametroAux3)) { 
			$parametros3 	= explode(",",$parametroAux3);
			foreach($parametros3 as $ind => $campo)  { 
				$campoAux3	= $row[$campo]; 
				$jsAux3    .= ",'$campoAux3'"; 
			} 
		}
		
		if (isset($constanteAux3) AND trim($constanteAux3) != '') { 
			$constante3 	= explode(",",$constanteAux3);
			foreach($constante3 as $ind => $constante)  { 
				$constAux3	= $constante;  
				$jsAux1    .= ",'$constAux3'"; 
			} 
		}
		
		
		
		if (isset($parametrosFicha) AND trim($parametrosFicha) != '') { 
			$parametrosF 	= explode(",",$parametrosFicha);
			foreach($parametrosF as $ind => $campo)  { 
				$campoF		= $row[$campo]; 
				$js   	   .= ",'$campoF'";      
			} 
		}
        if ($conFicha) { $link = "onclick=$funcionGrilla($js)"; $cursorPoint = " cursorPoint"; } else { $link = ""; $cursorPoint = "";}

        $grillaHTML .= "
		<div class='span-$anchoDiv'>
			<div  $link class='$clase $cursorPoint span-$anchoLin' >";

		$backGroun   = "";
        foreach ($arrayGrilla as $linea => $valoresLin) {
			// cambia color de fondo
			if (isset($valoresLin['oculta'])) { $oculta = $valoresLin['oculta']; } else { $oculta = false; }
			
			if (isset($valoresLin['rowColor'])) { 
				if (isset($valoresLin['rowColor'])) {
					$rowColor  	= $valoresLin['rowColor'];
					$valorCol	= $valoresLin['valorCol']; 
					$datoC   	= $row[$valoresLin['campo']]; 
					
					$colores 	= explode(",",$rowColor);
					$valores    = explode(",",$valorCol);
					
					foreach($valores as $ind => $val) { 
						$arrayColor[$val] = $colores[$ind];
					}
					
					if (isset($arrayColor[$datoC])) { $rowColor = $arrayColor[$datoC]; $backGroun = " background-color:$rowColor; "; }	
				}
			}
				
            if (!$oculta) { 
				$campo  = $valoresLin['campo'];
				$ancho  = $valoresLin['ancho'];
				
				$dato   = $row[$campo];
	
				if (isset($valoresLin['utf'])) {
					if ($valoresLin['utf']) { $dato = utf8_decode($dato); }
				}
	
				$last = "";
				if (isset($valoresLin['last'])) {
					if ($valoresLin['last']) { $last = "last"; }
				}
	
				if (isset($valoresLin['substr'])) {
					$substr = $valoresLin['substr'];
					$dato   = substr($dato,0,$substr);
				}
				
				if (isset($valoresLin['numform'])) { $numfor  = $valoresLin['numform']; } else { $numfor  = FALSE; }
				if ($numfor) {  
					$dato   = number_format($dato,0,',','.');
				}
				
				if (isset($valoresLin['date'])) { $datefor = $valoresLin['date']; } else { $datefor  = FALSE; }
				if ($datefor) {  
					$dato   = $dato = toFecDMA($dato);
				}
	
				$alinea = "";
				if (isset($valoresLin['alin'])) {
					$alin = $valoresLin['alin'];
					switch($alin) {
						case 'L': $alinea = ""; break;
						case 'C': $alinea = "centro"; break;
						case 'R': $alinea = "derecha"; break;
						default : $alinea = ""; break;
					}
				}
	
				$grillaHTML .= "<div class='span-$ancho $alinea $last' style='$backGroun' >$dato</div>";
			}
        }
        $grillaHTML .= "</div>";

        if ($conBotAux1){ 
			$grillaHTML .= "<div class='span-2 cursorPoint fila_impar last'><input type='button' class='$btnClassAux1' onclick=$funcionAux1($jsAux1) value='$txtBtn1'/></div>"; 
		}
        if ($conBotAux2){
            $grillaHTML .= "<div class='span-2 cursorPoint fila_impar last'><input type='button' class='$btnClassAux2' onclick=$funcionAux2($jsAux2) value='$txtBtn2'/></div>";
        }
		if ($conBotAux3){ 
			$grillaHTML .= "<div class='span-2 cursorPoint fila_impar last'><input type='button' class='$btnClassAux3' onclick=$funcionAux3($jsAux3) value='$txtBtn3'/></div>";
		}			
        if ($conBorrar){
            $grillaHTML .= "<div class='span-2 cursorPoint fila_impar last' ><input type='button' class='$btnClassBorra' onclick=borrarReg($js) value='$borrar'/></div>";
        }
        $grillaHTML .= "</div>";
    }
  ?>