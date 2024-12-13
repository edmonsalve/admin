<?php
    /*
        Script que genera grilla de salida a partir de una consulta, requiere de:
        $conFicha      = TRUE o FALSE;  con o sin linnk a ficha mantenedora funcion ficha() por defecto, si se especifica una funcion tomara esta ultima
        $funcionGrilla = funcion a ejecutar, si no se especifica ejecuta ficha();

        $soloBusquedaConFiltro  = TRUE o FALSE realiza la busqueda solo si hay filtros
        $conBorrar              = TRUE o FALSE;  con o sin boton de borrado de registro

        $conBotAux1 = TRUE o FALSE;  $funcionAux1 => Funcion javascript    $txtBtn1 => texto del boton
        $conBotAux2 = TRUE o FALSE;  $funcionAux1 => Funcion javascript    $txtBtn2 => texto del boton

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
        titulo: Ancho de la columan 1,2 3 (trabaja con clase 960 span-n)
        utf:    TRUE o FALSE
        last:   TRUE o FALSE
        substr: 18             Largo de la cadena antes de cortar
		
		rowColor: red   Color de la dila de la grilla cuando cumpla una caracteristica
		valorCol: 'A'   Valor que debe cumplir para el color selecciodo en rowColor
		oculta:   TRUE o FALSE  si no queremos mostrar el campo que se esta evaluando para el color
        
        "date"      => TRUE/FALSE     Formatea fecha
        
        "numform"   => TRUE/FALSE     Formatea numeros
        "decimales" => nroDecimales
    */

    if (!isset($soloBusquedaConFiltro))  { $soloBusquedaConFiltro = FALSE;  } 
    
    if (!isset($funcionGrilla))  { $funcionGrilla = "ficha"; }
    if (!isset($conBotAux1))     { $conBotAux1 = FALSE; }
    if (!isset($conBotAux2))     { $conBotAux2 = FALSE; }

    /* :::::::::::::::::::::::::::::::::::::: Determina Paginación  :::::::::::::::::::::::::::::::::::: */
	$where		  = '';
	$criterio     = '';

	if (isset($_GET['buscarpor'])) {
		$criterio  	= trim($_GET['iguala']);
		$buscarpor 	= $_GET['buscarpor'];
        
		if (trim($criterio) != '') { $where = " `$buscarpor`  like  '$criterio%' "; }
		if (trim($where) != '') { $where = "WHERE ".$where;  }

        /* =========== BUSQUEDA CON CAMPOS AUXILIARES ================ */
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

    if ($soloBusquedaConFiltro) { if (trim($where) == '') { $puedeLeer = FALSE; } else { $puedeLeer = TRUE; } } else { $puedeLeer = TRUE; } 
    
    $grillaHTML     = '';
    $grillaTitHTML  = '';
    $paginacionHTML = '';
    $selected       = ""; 
    
    /* :::::::::::::::::::::::::::::::::::::::::::::::::::: CRITERIOS DE BUSQUEDA :::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    $htmlCriterios = '';

    //***********************************************************************
    foreach($arrayCriterios as $orden => $arrayC) {
        $campo    = $arrayC['campo'];
        $campoTxt = $arrayC['descripcion'];
        
        if (isset($arrayC['quecontenga'])) { 
            $queContenga = $arrayC['quecontenga']; 
            } else {  
            $queContenga = FALSE; 
        }
        $arrayQueContega[$campo] = $queContenga;    
        
        
        if (isset($buscarpor)) {
           if ($buscarpor == $campo) { $selected = " selected='selected' "; } else { $selected = ""; } 
        }
        
        $htmlCriterios .= "<option value='$campo' $selected>$campoTxt</option>";   //   
    }
    //***********************************************************************
  
    if ($puedeLeer) { 
        $consulta = "SELECT *   FROM `$tablaDB`  $where  ORDER BY $orderBy ";
 
    	include(PATH_INCLUDES.'paginacion.php');
 
        $contador = 1;
    	$consulta .= "LIMIT $RegInicial,".PAGINACION;   
        $salida     = $conexionDB->consulta($consulta);
  
        /* :::::::::::::::::::::::::::::::::::::: Titulos Grilla  :::::::::::::::::::::::::::::::::::: */
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
    
        /* :::::::::::::::::::::::::::::::::::::: Datos Grilla  :::::::::::::::::::::::::::::::::::: */
        $borrar      = $dg_txt->GetDefinition('borrar');
    
        $contador    = 1;
		
    	while ($row = mysqli_fetch_array($salida)) {
    		if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; }

    		$contador++;
    
            $idReg  = $row[$IdCampo];
    		$js     = "'$moduloPHP','$idReg'";
    
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
						if ($datoC == $valorCol) { $backGroun = " background-color:$rowColor; "; }	
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
            $grillaHTML .= "
                </div>";
    
            if ($conBotAux1) {
                $grillaHTML .= "
                    <div class='span-2 cursorPoint fila_impar last'  ><input type='button' class='enviarpeq' onclick=$funcionAux1($idReg)  value='$txtBtn1'/></div>
                    ";
            }
    
            if ($conBotAux2) {
                $grillaHTML .= "
                    <div class='span-2 cursorPoint fila_impar'  ><input type='button' class='enviarpeq' onclick=$funcionAux2($idReg)  value='$txtBtn2'/></div>
                    ";
            }
    
            if ($conBorrar) {
                $grillaHTML .= "
                    <div class='span-2 cursorPoint fila_impar last'  ><input type='button' class='enviarpeq' onclick=borrarReg($js)  value='$borrar'/></div>
                    ";
            }
            $grillaHTML .= "
            </div>";
        }
    }      
  ?>
