<?php
    /*
        $conBotAux1 = TRUE o FALSE;  $funcionAux1 => Funcion javascript  $parametroAux1 = "campo_1,..., campo_N"    $constanteAux1="cons1=val1,cons2=val2,...,consN=valN"  $txtBtn1 => texto del boton;
        $conBotAux2 = TRUE o FALSE;  $funcionAux1 => Funcion javascript  $parametroAux2 = "campo_1,..., campo_N"    $constanteAux2="cons1=val1,cons2=val2,...,consN=valN"  $txtBtn2 => texto del boton;
		
		$btnClassBorra = "";
		$btnClassAux1  = "";
		$btnClassAux2  = "";
        
        
        ====================================================================================================
        $arrayGrilla[1] = array("campo" => 'nombreCampo', "alin" => 'L', "titulo" => 'TituloCol', "ancho" => 'ancho', "utf" => true, "last" => true );
        campo:  Nombre de campo
        alin:   Alineacion del dato L,C,R
        titulo: Titulo de la columna
        titulo: Ancho de la columan 1,2 3 (trabaca con clase 960 span-n)
        utf:    TRUE o FALSE
        lasr:   TRUE o FALSE
        substr: 18             Largo de la cadena antes de cortar
    */

    
    if (!isset($conBotAux1))     { $conBotAux1 = FALSE; }
    if (!isset($conBotAux2))     { $conBotAux2 = FALSE; }
	if (!isset($conBotAux3))     { $conBotAux3 = FALSE; }

	if (!isset($btnClassBorra))    { $btnClassBorra = 'enviarpeq'; } 
	if (!isset($btnClassAux1))     { $btnClassAux1  = 'enviarpeq'; }
	if (!isset($btnClassAux2))     { $btnClassAux2  = 'enviarpeq'; }
	if (!isset($btnClassAux3))     { $btnClassAux3  = 'enviarpeq'; }    

    
    /* :::::::::::::::::::::::::::::::::::::: Titulos Grilla  :::::::::::::::::::::::::::::::::::: */
    $grillaTitHTML  = '';
    $anchoLin       = 0;

    foreach ($arrayGrilla as $linea => $valoresLin) {    
        $titulo = $valoresLin['titulo'];
        $ancho  = $valoresLin['ancho'];
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
    
    $anchoDiv = $anchoLin; 
	
    if ($conBorrar) { $anchoDiv = $anchoLin + 2; } else { $anchoDiv = $anchoLin; }
    if ($conBotAux1){ $anchoDiv += 2; }
    if ($conBotAux2){ $anchoDiv += 2; }
	if ($conBotAux3){ $anchoDiv += 2; }

    /* :::::::::::::::::::::::::::::::::::::: Datos Grilla  :::::::::::::::::::::::::::::::::::: */
    $borrar      = $dg_txt->GetDefinition('borrar');

    $contador    = 1;
	$grillaHTML  = '';
    
    $salida     = $conexionDB->consulta($consulta);   
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
		

        $grillaHTML .= "
		<div class='span-$anchoDiv'>
			<div  class='$clase  span-$anchoLin' >";

        foreach ($arrayGrilla as $linea => $valoresLin) {
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

			if (trim($dato) == '') { $dato = ' s/i'; } 
            $grillaHTML .= "<div class='span-$ancho $alinea $last' >$dato</div>";
        }
        $grillaHTML .= "
            </div>";

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
        
        $grillaHTML .= "
        </div>";
    }
  ?>
