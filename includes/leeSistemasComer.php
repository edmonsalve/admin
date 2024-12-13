<?php
	$menuComerHTML = '';
    
    $movil   = $_SESSION['movil'];
    $modulos = $_SESSION['modulos'];
    
    $nroModulos = strlen($modulos) / 3;
    
    for ($i = 0; $i < $nroModulos; $i++) {
        $pos = $i * 3;
        $mod = substr($modulos,$pos,3);
        $arrayModulos[$mod] = TRUE;
    }
    
    if($movil == 1) { $moviles = "AND `movil` = 1 "; } else { $moviles = ""; }
	 
	$consulta 	= "SELECT * FROM `adm_sistemas` 
                            WHERE area ='C' 
                            AND   estado = 'S'
                            $moviles
                            ORDER BY nombre";
                            
	$salida 	= $conexionReD->consulta($consulta);
	
	while ($rowsis = mysql_fetch_array($salida,MYSQL_ASSOC)) {
		$id   = $rowsis['id'];
        $ruta = $rowsis['ruta'];
        
        if (isset($arrayModulos[$id])) {              
            if ($tipoUser == 7)  { $link = "sistemasComer/$ruta?areasistema=M&idsistema=$id"; } 
                else { 
                $consulta  = "SELECT * FROM `adm_accesoSist` WHERE `idUser` = '$user' AND `idSistema` = $id";
                $salidaSis = $conexionReD->consulta($consulta);
                $row       = mysql_fetch_array($salidaSis,MYSQL_ASSOC);
                
                $link = "#";
                if (isset($row['acceso'])) { 
                    if ($row['acceso'] == 'S') { $link = "sistemasComer/$ruta?areasistema=M&idsistema=$id"; }
                }
            }
            
    		$menuComerHTML .= '
              <a href="'.$link.'" target="_top" style="text-decoration:none; ">
    		  <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="'.$rowsis['icono'].'" width="80" height="96" alt="'.$rowsis['nombre'].'"/>
              </a>';
        } 	
	}
    
	mysql_free_result($salida);
?>
