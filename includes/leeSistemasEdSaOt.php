<?php
	$menuEducHTML = '';
    $raizSistemas = 'sistemasEducSalOtr';
    
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
                            WHERE estado = 'S' 
                            AND   (area ='E' OR area ='S' OR area ='O')
                            $moviles
                            ORDER BY nombre";
                            
	$salida 	= $conexionReD->consulta($consulta);
	
	while ($rowsis = mysql_fetch_array($salida,MYSQL_ASSOC)) {
		$id   = $rowsis['id'];
        $ruta = $rowsis['ruta'];
        
        if (isset($arrayModulos[$id])) {              
            if ($tipoUser == 7)  { $link = "$raizSistemas/$ruta?areasistema=E&idsistema=$id"; } 
                else { 
                $consulta  = "SELECT * FROM `adm_accesoSist` WHERE `idUser` = '$user' AND `idSistema` = $id";
                $salidaSis = $conexionReD->consulta($consulta);
                $row       = mysql_fetch_array($salidaSis,MYSQL_ASSOC);
                
                $link = "#";
                if (isset($row['acceso'])) { 
                    if ($row['acceso'] == 'S') { $link = "$raizSistemas/$ruta?areasistema=E&idsistema=$id"; }
                }
            }
            
    		$menuEducHTML .= '
              <a href="'.$link.'" target="_top" style="text-decoration:none; ">
    		  <img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="'.$rowsis['icono'].'" width="80" height="96" alt="'.$rowsis['nombre'].'"/>
              </a>';
        } 	
	}
    
	mysql_free_result($salida);
?>
