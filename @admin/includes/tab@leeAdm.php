<?php
    /* :::::::::::::::::::::::::::::::::: Lee parametros $_GET para Busqueda :::::::::::::::::::::::::::::::::: */
	$where		  = '';
	$criterio     = '';
	$centroId     = 0;
    
    if(!isset($DESC)) { $DESC = ''; }
    
	if (isset($_GET['buscarpor'])) { 
		$criterio  	= trim($_GET['iguala']);
		$buscarpor 	= $_GET['buscarpor'];
		
		if (trim($criterio) != '') { $where = " `$buscarpor`  like  '$criterio%' "; }
				
		if (trim($where) != '') { $where = "WHERE ".$where;  }
	} 
    
	/* :::::::::::::::::::::::::::::::::::::::::::: Lee Tabla :::::::::::::::::::::::::::::::::::::::: */
	$contador = 1;
	$consulta = "SELECT *   FROM $tablaDB
                            $where
							ORDER BY $orderBy $DESC";
				
	$salida   = $conexionDB->consulta($consulta);
	
	$borrar = $sistema_txt->GetDefinition('borrar');
	$tablaHTML  = '';
	
	/* :::::::::::::::::::::::::::::::::::::::::: Titulos Grilla :::::::::::::::::::::::::::::::::::::  */
	foreach ($titulos as $ind => $val) { 
		$titulo = $val['tit'];
		$clase  = $val['ancho'];	
		$tablaHTML .= "<div class='$clase' ><h3>$titulo</h3></div>";
	}
	
	/* :::::::::::::::::::::::::::::::::::::::::: Datos Grilla :::::::::::::::::::::::::::::::::::::  */
	$tablaHTML .= '<div class="span-10" style="float:none; height:435px; overflow:auto; ">';
	while ($row = mysqli_fetch_array($salida)) {
		if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; } 
		$contador++;
		
		$id = $row[$IdCampo];
		$jsdel   = "'$phpFile',".$id;
		$jsedit  = "'$phpFile.php',".$id;
        
		$tablaHTML .= '<div class="span-9" >';
			$tablaHTML .= '<div class="span-8 cursorPoint '.$clase.'" onclick="editarTabla('.$jsedit.')" >';
				foreach ($fila as $ind => $val) { 
					$campo = $val['campo'];
					$clase = $val['ancho'];
					
					$valorRow = $row[$campo];
					if (substr($campo,0,5) == 'fecha') { $valorRow = substr($row[$campo],8,2).'-'.substr($row[$campo],5,2).'-'.substr($row[$campo],0,4); }
					
                    $tablaHTML .= "<div class='$clase' >$valorRow</div>";
				}
			$tablaHTML .= '</div>';
			$tablaHTML .= '<div class="span-1 last cursorPoint fila_impar"  ><input type="button" class="enviarpeq" onclick="borrarReg('.$jsdel.')" value="'.$borrar.'" style="width:50px; "/></div>';
		$tablaHTML .= '</div>';
	}
	$tablaHTML .= '</div>';

    // echo "<br> ** $consulta  **<br>";
?>
