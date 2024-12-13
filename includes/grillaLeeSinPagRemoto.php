<?php
    /*
        $conBotAux1 = TRUE o FALSE;  $funcionAux1 => Funcion javascript    $txtBtn1 => texto del boton
        $conBotAux2 = TRUE o FALSE;  $funcionAux1 => Funcion javascript    $txtBtn2 => texto del boton
        
        
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

    
    // :::::::::::::::::::::::::::::::::::::: Titulos Grilla  :::::::::::::::::::::::::::::::::::: //
    $grillaTitHTMLRemoto  = '';
    $anchoLin       = 0;

    foreach ($arrayGrilla as $linea => $valoresLin) {    
        $titulo = $valoresLin['titulo'];
        $ancho  = $valoresLin['ancho'];
        $anchoLin += $ancho; 
		
		$last = "";
		if (isset($valoresLin['last'])) {
			if ($valoresLin['last']) { $last = "last"; }
		}
		
        $grillaTitHTMLRemoto .= "
            <div class='span-$ancho enc_mensajes $last' >
				 <h3>$titulo</h3>
			</div>";
    } 
    
    $anchoDiv = $anchoLin; 
    if ($conBotAux1){ $anchoDiv += 2; }
    if ($conBotAux2){ $anchoDiv += 2; }

    // :::::::::::::::::::::::::::::::::::::: Datos Grilla  :::::::::::::::::::::::::::::::::::: //
    $borrar      = $dg_txt->GetDefinition('borrar');

    $contador    = 1;
	$grillaHTMLRemoto  = '';
    
    $salida     = $conexionRemota->consulta($consulta);   
	while ($row = mysqli_fetch_array($salida)) {
		if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; }
		$contador++;

        $idReg  = $row[$IdCampo];
		$js     = "'$moduloPHP','$idReg'";

        $grillaHTMLRemoto .= "
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
            $grillaHTMLRemoto .= "<div class='span-$ancho $alinea $last' >$dato</div>";
        }
        $grillaHTMLRemoto .= "
            </div>";

        if ($conBotAux1) {
            $grillaHTMLRemoto .= "
                <div class='span-2 cursorPoint fila_impar last'  ><input type='button' class='enviarpeq' onclick=$funcionAux1($idReg)  value='$txtBtn1'/></div>
                ";
        }

        if ($conBotAux2) {
            $grillaHTMLRemoto .= "
                <div class='span-2 cursorPoint fila_impar'  ><input type='button' class='enviarpeq' onclick=$funcionAux2($idReg)  value='$txtBtn2'/></div>
                ";
        }
        
        
        $grillaHTMLRemoto .= "
        </div>";
    }
  ?>
