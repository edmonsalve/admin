<?php
    $menuContHTML   = "";
	$menuMunHTML    = '';
    $menuEducHTML   = '';
    $menuSaludHTML  = "";
    $menuAdminHTML  = '';
    $tieneContabilidad = false;
    $tieneTesoreria = false;

    $modulos    = $_SESSION['modulos'];
    $nroModulos = intval(strlen($modulos) / 3);
    $usrAdmin   = $_SESSION['usrAdmin'];
    

    for ($i = 0; $i < $nroModulos; $i++) {
        $pos = $i * 3;
        $mod = substr($modulos,$pos,3);
        $arrayModulos[$mod] = true; 
    }
    
	$consulta 	= "SELECT * FROM `adm_sistemas` 
                            WHERE estado = 'S'
                            ORDER BY nombre";
                            
	$salida 	= $conexionDB->consulta($consulta);

	while ($rowsis = mysqli_fetch_array($salida)) {
		$id     = $rowsis['id'];
        $ruta   = $rowsis['ruta'];
        $area   = $rowsis['area'];
        $nombre = $rowsis['nombre'];
       
		$rutaIndex = "$ruta/index_main.php";
		
        if (isset($arrayModulos[$id]) OR $area == 'A') {    
            if ($usrAdmin == 'D') {
                $link       = "$raizSistemas/$rutaIndex?areasistema=M&idsistema=$id"; 
                $icono      = $rowsis['icono'];
                $accesoSist = 'S';
            } else { 
                $consulta  = "SELECT * FROM `$DB_CLIENTE`.`adm_accesoSist` WHERE `idUser` = '$user' AND `idSistema` = $id";
                $salidaSis = $conexionDB->consulta($consulta);
                $row       = mysqli_fetch_array($salidaSis);
                
                $link       = "#";    
                $icono      = $rowsis['iconoDesh'];
                
                if (isset($row['acceso'])) {  $accesoSist = $row['acceso']; } else { $accesoSist = 'N'; }
            }
            
            if ($accesoSist == 'S')  {
                switch ($area) {
                    case 'C': $raizSistemas = 'appContabilidad'; break;
                    case 'M': $raizSistemas = 'appMuni'; break;
                    case 'S': $raizSistemas = 'appSalud';break;
                    case 'E': $raizSistemas = 'appEduc';break;    
                    case 'A': $raizSistemas = ''; break;   
                    default:  $raizSistemas = ''; break; 
                }

                if ($area === 'C' && in_array($ruta, ['sis_contabilidad', 'sis_tesoreria'], true)) {
                    $raizSistemas = 'appMuni';
                }

                $link = "$raizSistemas/$rutaIndex?areasistema=M&idsistema=$id"; 
                $icono = $rowsis['icono'];

                $menuHTML = "
                <a href='$link' target='_top' style='text-decoration:none; '>
                    <img style='float:left; padding-left:12px; padding-right:2px; padding-bottom:8px;' src='iconos/$icono' width='72' height='86' alt='$nombre'/>
                </a>";

                switch ($area) {
                    case 'C':
                        if ($ruta === 'sis_contabilidad') {
                            $tieneContabilidad = true;
                        }
                        if ($ruta === 'sis_tesoreria') {
                            $tieneTesoreria = true;
                        }
                        $menuContHTML  .= $menuHTML;
                        break;
                    case 'M': $menuMunHTML   .= $menuHTML; break;
                    case 'S': $menuSaludHTML .= $menuHTML; break;
                    case 'E': $menuEducHTML  .= $menuHTML; break;    
                    case 'A': $menuAdminHTML .= $menuHTML; break;   
                    default: break; 
                }
            }
        } 	
	}

    if (($usrAdmin === 'D' || $tieneContabilidad) && !$tieneTesoreria) {
        $menuContHTML .= "
            <a href='/appMuni/sis_tesoreria/index_main.php?areasistema=M&idsistema=195' target='_top' style='text-decoration:none; '>
                <img style='float:left; padding-left:12px; padding-right:2px; padding-bottom:8px;' src='iconos/ico_tesor.png' width='72' height='86' alt='Tesoreria'/>
            </a>";
        $tieneTesoreria = true;
    }

    // $_SESSION['conSistemas'] = false;

    if (trim($menuContHTML) != "") { 
        $_SESSION['dispConta']    = 'block'; 
        $_SESSION['conSistemas'] = true;
    } else { 
        $_SESSION['dispConta'] = 'none'; 
    }
    
    if (trim($menuMunHTML) != "") { 
        $_SESSION['dispMun']       = 'block'; 
        $_SESSION['conSistemas'] = true;
    } else { 
        $_SESSION['dispMun'] = 'none'; 
    }

    if (trim($menuSaludHTML) != "") { 
        $_SESSION['dispSal'] = 'block';
        $_SESSION['conSistemas'] = true; 
    } else { 
        $_SESSION['dispSal'] = 'none'; 
    }

    if (trim($menuEducHTML) != "") { 
        $_SESSION['dispEduc'] = 'block';
        $_SESSION['conSistemas'] = true; 
    } else { 
        $_SESSION['dispEduc'] = 'none'; 
    }

    if (trim($menuAdminHTML) != "") { 
        $_SESSION['dispAdmin'] = 'block';
        $_SESSION['conSistemas'] = true; 
    } else { 
        $_SESSION['dispAdmin'] = 'none'; 
    }

	mysqli_free_result($salida);
?>
