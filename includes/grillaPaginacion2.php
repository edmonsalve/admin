<?php
	// ::::  PAGINACION
	// Requiere el nombre de la tabla en la variables: 	$contarTabla  
	// Se muestran como maximo 12 paginas

	if (!isset($lineasPorPagina) || !is_numeric($lineasPorPagina) || $lineasPorPagina <= 0 ) {
		$lineasPorPagina = PAGINACION;
	}

    if (!isset($funcionBusqueda) OR trim($funcionBusqueda) == "") { 
		$funcionBusqueda = "filtrar2"; 
	}  else { 
		$funcionBusqueda = $funcionBusqueda; 
	}
    
	if (isset($_GET['pag'])) { 
		$pagAct	       = $_GET['pag']; 
		$RegInicial    = (($_GET['pag'] - 1) * $lineasPorPagina); 
        if (($pagAct % NRO_PAGINAS) == 1  AND $pagAct > NRO_PAGINAS) { 
            $paginaInicial = round(($pagAct / NRO_PAGINAS) * 10,0) ; 
            } else { 
            if ($pagAct <=  NRO_PAGINAS) {
                $paginaInicial = 1;
                } else {
                $paginaInicial = (intval($pagAct / NRO_PAGINAS) * 10) + 1;  
            } 
        }
        
		} else { 
		$pagAct	       = 1;
		$RegInicial    = 0; 
        $paginaInicial = 1;
	}	

	$SalidaNroFilas   = $conexionDB->consulta($consulta);
    $NroFilas         = mysqli_num_rows($SalidaNroFilas);	

	$NroPaginas       = intval($NroFilas / $lineasPorPagina);
	if (($NroFilas % $lineasPorPagina) != 0) { $NroPaginas++; }
	
	echo '<input id="paginaAct" type="hidden" value="'.$pagAct.'" />';
	echo '<input id="paginaFin" type="hidden" value="'.$NroPaginas.'" />';
  
	$paginacionHTML = '<div>';
	if  ($NroPaginas > 1) { 

		$jsIni = "'$moduloPHP','I'";
		$jsAnt = "'$moduloPHP','A'";
		$jsSig = "'$moduloPHP','S'";
		$jsUlt = "'$moduloPHP','U'";
		
        $style = "style='font-size:11px; color:silver; font-weight:bold; '";
        $paginacionHTML .= "<li><a $style onclick=\"$funcionBusqueda($jsIni)\" >INICIAL</a></li>";
		$paginacionHTML .= "<li><a $style onclick=\"$funcionBusqueda($jsAnt)\" >Anterior</a></li>";
        
        if ($NroPaginas > NRO_PAGINAS) { $paginaHasta = $paginaInicial + NRO_PAGINAS -1; } else { $paginaHasta = NRO_PAGINAS; }
        if ($paginaHasta > $NroPaginas) { $paginaHasta = $NroPaginas; }
        
		for($i = $paginaInicial; $i <= $paginaHasta; $i++) {
			$jsPag = "'$moduloPHP',$i"; 
			if ($pagAct == $i) { $style = "style='font-size:12px; color:#0000ff; font-weight:bold; '"; } else  { $style = "style='font-size:11px; color:silver; font-weight:bold; '"; }
			$paginacionHTML .= "<li><a  $style onclick=\"$funcionBusqueda($jsPag)\" >$i</a></li>";
		}
        if  ($pagAct < $NroPaginas) {
            $paginacionHTML .= "<li><a $style onclick=\"$funcionBusqueda($jsSig)\" >Siguiente</a></li>";   
        }
        if  ($NroPaginas > 12 and $paginaHasta < $NroPaginas) { 
            $paginacionHTML .= "<li><a $style onclick=\"$funcionBusqueda($jsUlt)\" >ULTIMA</a></li>";
        }
	} 
	$paginacionHTML .= '</div>';
?>
