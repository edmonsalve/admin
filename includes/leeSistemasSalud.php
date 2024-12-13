
<?php
	$menuSaludHTML = '';
    $raizSistemas = 'sistemasSalud';
    
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
                            AND   (area ='S')
                            $moviles
                            ORDER BY nombre";
                            
	$salida 	= $conexionDB->consulta($consulta);
	
	while ($rowsis = mysqli_fetch_array($salida)) {
		$id   = $rowsis['id'];
        $ruta = $rowsis['ruta'];
        $nombre = $rowsis['nombre'];
        
        if (isset($arrayModulos[$id])) {              
            if ($tipoUser == 7)  { 
                $link = "$raizSistemas/$ruta?areasistema=M&idsistema=$id"; 
                $icono = $rowsis['icono']; 
                $menuSaludHTML .= "
                  <a href='$link' target='_top' style='text-decoration:none; '>
        		     <img style='float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; ' src='$icono' width='80' height='96' alt='$nombre'/>
                  </a>"; 
                } else { 
                $consulta  = "SELECT * FROM `$DB_CLIENTE`.`adm_accesoSist` WHERE `idUser` = '$user' AND `idSistema` = $id";
                $salidaSis = $conexionDB->consulta($consulta);
                $row       = mysqli_fetch_array($salidaSis);
                
                $link = "#";    
                $icono = $rowsis['iconoDesh'];
                
                if (isset($row['acceso'])) { 
                    if ($row['acceso'] == 'S') { 
                        $link = "$raizSistemas/$ruta?areasistema=M&idsistema=$id"; 
                        $icono = $rowsis['icono'];
                        $menuSaludHTML .= "
                          <a href='$link' target='_top' style='text-decoration:none; '>
                		     <img style='float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; ' src='$icono' width='80' height='96' alt='$nombre'/>
                          </a>";
                    }
                }
            }
        } 	
	}
    
	mysqli_free_result($salida);
?>
