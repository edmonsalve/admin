<?php
	/* :::::::::::::::::::::::::::::::::::::::::::::::::::: PAGINACION :::::::::::::::::::::::::::::::::::::::::::::::::::::: */
	/*
	 * Requiere el nombre de la tabla en la variables: 	$contarTabla  
	 * Se muestran como maximo 12 paginas
	 */

    if (!isset($funcionBusqueda)) { $funcionBusqueda = "buscar"; }  
    
	if (isset($_GET['pag'])) { 
		$pagAct	       = $_GET['pag'];
		$RegInicial    = (($_GET['pag'] - 1) * PAGINACION); 
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

	$NroPaginas       = intval($NroFilas / PAGINACION);
	if (($NroFilas % PAGINACION) != 0) { $NroPaginas++; }
	
	echo '<input id="paginaAct" type="hidden" value="'.$pagAct.'" />';
	echo '<input id="paginaFin" type="hidden" value="'.$NroPaginas.'" />';

	$paginacionHTML = '';
	if  ($NroPaginas > 1) { 
        $style = "style='font-size:11px; color:silver; font-weight:bold; '";
        $paginacionHTML .= "<li><a $style onclick=\"$funcionBusqueda('$moduloPHP','$NroPaginas','I')\" >INICIAL</li>";
		$paginacionHTML .= "<li><a $style onclick=\"$funcionBusqueda('$moduloPHP','$tablaDB','A')\" >Anterior</li>";
        
        if ($NroPaginas > NRO_PAGINAS) { $paginaHasta = $paginaInicial + NRO_PAGINAS -1; } else { $paginaHasta = NRO_PAGINAS; }
        if ($paginaHasta > $NroPaginas) { $paginaHasta = $NroPaginas; }
        
		for($i = $paginaInicial; $i <= $paginaHasta; $i++) {
			if ($pagAct == $i) { $style = "style='font-size:12px; color:#0000ff; font-weight:bold; '"; } else  { $style = "style='font-size:11px; color:silver; font-weight:bold; '"; }
			$paginacionHTML .= "<li><a  $style onclick=\"$funcionBusqueda('$moduloPHP','$tablaDB',$i)\" >$i</a></li>";
		}
        if  ($pagAct < $NroPaginas) {
            $paginacionHTML .= "<li><a $style onclick=\"$funcionBusqueda('$moduloPHP','$tablaDB','S')\" >Siguiente</li>";   
        }
		
        if  ($NroPaginas > 12 and $paginaHasta < $NroPaginas) { 
            $paginacionHTML .= "<li><a $style onclick=\"$funcionBusqueda('$moduloPHP','$NroPaginas','U')\" >ULTIMA</li>";
        }
	}
?>
