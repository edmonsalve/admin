<?php
    /**
     * Script de grilla optimizado (versión 2025-10-03)
     * 
     * Mejoras:
     *  - Botones auxiliares dinámicos en $accionesAux[]
     *  - Filtros auxiliares dinámicos en $filtrosAux[]
     *  - Menos repetición en inicialización de variables
     *  - Construcción más clara de la consulta SQL
     */

    /* 
    foreach ($_SESSION as $key => $val) {
        if (preg_match('/^filtroAux(\d+)Campo$/', $key, $m)) {
            echo "<br>Sesión: $key = $val, Valor = " . ($_SESSION["filtroAux{$m[1]}Valor"] ?? '') . "<br>";
        }
    }
    */

    // ::: guarda filtros
    for ($i = 1; $i <= 5; $i++) {
        $campoKey = "filtroAux{$i}Campo";
        $valorKey = "filtroAux{$i}Valor";

        if (isset($_GET[$campoKey])) {
            $_SESSION[$campoKey] = $_GET[$campoKey];
            $_SESSION[$valorKey] = $_GET[$valorKey] ?? '';
        } else {
            unset($_SESSION[$campoKey], $_SESSION[$valorKey]);
        }
    }

    // inicialización de variables con valores por defecto
    $accionesGrupo1  = $tablaDatos['setupTabla']['accionesGrupo1'] ?? false;    
    $anchoAccGrupo1  = $tablaDatos['setupTabla']['anchoAccGrupo1']."fr" ?? "2fr";
    $tituloAccGrupo1 = (isset($tablaDatos['setupTabla']['tituloAccGrupo1']) && trim($tablaDatos['setupTabla']['tituloAccGrupo1']) !== '') 
        ? $tablaDatos['setupTabla']['tituloAccGrupo1'] 
        : 'Acc.';

    $accionesGrupo2  = (isset($tablaDatos['setupTabla']['accionesGrupo2']) && $tablaDatos['setupTabla']['accionesGrupo2'] !== '') 
        ? $tablaDatos['setupTabla']['accionesGrupo2'] : false;
    $anchoAccGrupo2  = (isset($tablaDatos['setupTabla']['anchoAccGrupo2']) && $tablaDatos['setupTabla']['anchoAccGrupo2'] !== '') 
        ? $tablaDatos['setupTabla']['anchoAccGrupo2']."fr" : "2fr";
    $tituloAccGrupo2 = (isset($tablaDatos['setupTabla']['tituloAccGrupo2']) && trim($tablaDatos['setupTabla']['tituloAccGrupo2']) !== '') 
        ? $tablaDatos['setupTabla']['tituloAccGrupo2'] 
        : 'Acc.';

    $conLimite       = $tablaDatos['setupTabla']['conLimite'] ?? 0; 
    $conPaginacion   = $tablaDatos['setupTabla']['conPaginacion'] ?? true;
    $lineasPorPagina = $tablaDatos['setupTabla']['lineasPorPagina'] ?? PAGINACION;   // esta linea se repite temporalmente en grillaPaginacion.php
    $grillaPeq       = $tablaDatos['setupTabla']['grillaPeq'] ?? false;
    $manuscrito      = $tablaDatos['setupTabla']['manuscrito'] ?? false;
    $soloConFiltro   = $tablaDatos['setupTabla']['soloConFiltro'] ?? false;

    $conFiltro       = isset($tablaDatos['setupTabla']['conFiltro']) ? $tablaDatos['setupTabla']['conFiltro'] : true;

    $ignorarWhere    = $tablaDatos['setupTabla']['ignorarWhere'] ?? false;
    $sinFiltros      = $tablaDatos['setupTabla']['sinFiltros'] ?? false;
    $funcionBusqueda = $tablaDatos['setupTabla']['funcionBusqueda'] ?? ''; 
    $colorEncabezado = $tablaDatos['setupTabla']['colorEncabezado'] ?? '';

    $verConsulta     = $tablaDatos['setupTabla']['verConsulta'] ?? false;

    // :: criterios de búsqueda
    $htmlCriterios = '';

    if (isset($tablaDatos['criterios']) && is_array($tablaDatos['criterios']) && count($tablaDatos['criterios']) > 0) {
       foreach($tablaDatos['criterios'] as $orden => $arrayC) {
            $campo      = $arrayC['campo'];
            $campoTxt   = $arrayC['descripcion'];

            $arrayCampo = explode(",", $campo);
            if (count($arrayCampo) == 2) {
                $campo = $arrayCampo[1];
            }
     
            if ($buscarpor == "$campo") { 
                $selected = " selected='selected' "; 
            } else { 
                $selected = ""; 
            }  
            $htmlCriterios .= "<option value='$campo' $selected>$campoTxt</option>";   
        }
    }
    $htmlCriterios = '';
    
    if (isset($arrayCriterios)) {  
        foreach($arrayCriterios as $orden => $arrayC) {
            $campo      = $arrayC['campo'];
            $campoTxt   = $arrayC['descripcion'];

            $arrayCampo = explode(",", $campo);
            if (count($arrayCampo) == 2) {
                $campo = $arrayCampo[1];
            }
              
            if ($buscarpor == "$campo") { 
                $selected = " selected='selected' "; 
            } else { 
                $selected = ""; 
            }  
            $htmlCriterios .= "<option value='$campo' $selected>$campoTxt</option>";   
        }
    }

    // fuente manuscrita
    if (isset($manuscrito) && $manuscrito) { $claseManuscrito = "celdaGrilla--manuscrito"; } else { $claseManuscrito = ""; }

    // ajuste de iconos
    if ($grillaPeq) { $claseCeldaiPec = "celdaGrilla--peq"; $tamaIco = 20; } else { $claseCeldaiPec = ""; $tamaIco = 25; }

    // Fondo encabezado
    if (trim($colorEncabezado) != "") { $bgColor = "background-color:$colorEncabezado"; } else { $bgColor = ""; }


    $confiltros = [];
    if ($conFiltro) {
        // Procesar criterios de búsqueda
        $criterio  = $_GET['iguala'] ?? '';
        $buscarpor = $_GET['buscarpor'] ?? '';
     
        if ($criterio !== '' && $buscarpor !== '') {
            $buscarporArray = explode(",", $buscarpor);
            if (count($buscarporArray) == 2 && $buscarporArray[0] == 'L') {
                $campo = $buscarporArray[1];
                $confiltros[] = "$campo LIKE '%$criterio%'";
            } else {
                $campoCom = explode(".", $buscarpor);
                $buscarpor    = (count($campoCom) == 2) ? "`{$campoCom[0]}`.`{$campoCom[1]}`" : "`$buscarpor`";
                $confiltros[] = "$buscarpor LIKE '%$criterio%'";    // se asume que el criterio de búsqueda es siempre un LIKE para permitir búsquedas parciales
            }
        }
    }

    // Filtros auxiliares dinámicos (se llenan desde $_GET)
    $filtrosAux = [];
    foreach ($_GET as $key => $val) {
        if (preg_match('/^filtroAux(\d+)Campo$/', $key, $m)) {
            $i = $m[1];
            $campo = $_GET["filtroAux{$i}Campo"] ?? '';
            $valor = $_GET["filtroAux{$i}Valor"] ?? '';

            
            $arrayCampo = explode(".", $campo);
            $campo   = (count($arrayCampo) == 2) ? "`{$arrayCampo[0]}`.`{$arrayCampo[1]}`" : "`$arrayCampo[0]`";
            
            if ($campo && $valor !== '') { 
                $filtrosAux[] = [
                    "campo" => $campo,
                    "valor" => $valor,
                ];
            }
        }
    }

    // echo "++++ ".count($filtrosAux)." filtrosAux <br>";
    if ($soloConFiltro) { 
        if (count($filtrosAux) == 0) { 
            $encabezadoHTML = '';
            $filasHTML      = '';
            $paginacionHTML = '';
            return; 
        } 
    } 
   
    /*
    foreach ($filtrosAux as $key => $value) {
        echo "* $key = $value <br>";  
        foreach ($value as $key2 => $value2) {
            echo ">>> $key2 = $value2 <br>";  
        }
    }
    */  

    // Filtros auxiliares
    foreach ($filtrosAux as $f) {
        $arrayCampo = explode(",", $f['campo']);
        
        if (count($arrayCampo) == 2) {
            $campo = str_replace("`","",$arrayCampo[1]);
            $mod   = str_replace("`","",$arrayCampo[0]);
        } else {
            $campo = $f['campo'];
            $mod   = '';
        }
     
        $valorFiltro = $f['valor'];
        $auxSinCAmpo = str_replace("`","",$campo);
        
        if (trim($auxSinCAmpo) != '') {  
            if (count($arrayCampo) == 2) {
                switch ($mod) {
                    case 'D': $confiltros[] = "$campo >= '$valorFiltro'"; break;
                    case 'H': $confiltros[] = "$campo <= '$valorFiltro'"; break;
                    case 'L': $confiltros[] = "$campo LIKE '%$valorFiltro%'"; break;
                    case 'Y': $confiltros[] = "YEAR($campo) = '$valorFiltro'";  break;
                }
            } else {
                $confiltros[] = "$campo = '$valorFiltro'";
            }
        }
    }
 

    // consulta
    $consulta     = $tablaDatos['consulta'];

    // insertar WHERE
    if ((!$ignorarWhere && $confiltros) && !$sinFiltros) { 
        $consulta .= (stripos($consulta, 'WHERE') === false ? " WHERE " : " AND ");
        $consulta .= implode(" AND ", $confiltros);
    }

    // ordenar por
    if (isset($tablaDatos['ordenarPor']) && trim($tablaDatos['ordenarPor']) !== '' ) {
        $consulta .= " ORDER BY {$tablaDatos['ordenarPor']}";
    }
        
    
    if ($conLimite > 0) {
        $consulta .= " LIMIT $conLimite";
    }

    // :: Muestra consulta en proceso de desarrollo
    if ($verConsulta) { echo "<pre>$consulta</pre>";   }

    // :: paginación
    if ($conPaginacion) {
        // echo "<pre>$consulta</pre>";
        include(PATH_INCLUDES.'grillaPaginacion2.php');
        $contador = 1;
        $consulta .= " LIMIT $RegInicial,".$lineasPorPagina;
    } 

 
    // :: ejecución consulta
    $salida    = $conexionDB->consulta($consulta);

    // titulos tabla
    $filaEncabezados  = ""; $fila = "";
         
    $columnasTit       = "padding-top:10; row-gap:1px; column-gap:1px; grid-template-columns: ";
    $columnasFilas     = "padding-top:0;  row-gap:1px; column-gap:1px; grid-template-columns: ";

    foreach ($tablaDatos['columnas'] as $linea => $valoresLin) {
        if ($linea > 0) {
            $titulo    = $valoresLin['titulo'];
            $ancho     = $valoresLin['ancho'];
            $columnasTit     .= $ancho."fr ";
            $columnasFilas   .= $ancho."fr ";
            $filaEncabezados .= "<div class='celdaGrilla--peq  fondoTitulos ' style='$bgColor'>$titulo</div>";
        }
    }  

    if ($accionesGrupo1) { 
        $columnasTit     .= " $anchoAccGrupo1";
        $columnasFilas   .= " $anchoAccGrupo1";
        $filaEncabezados .= "<div class='celdaGrilla--peq fondoTitulos' style='$bgColor' >$tituloAccGrupo1</div>";
    }

    if ($accionesGrupo2) { 
        $columnasTit     .= " $anchoAccGrupo2";
        $columnasFilas   .= " $anchoAccGrupo2";
        $filaEncabezados .= "<div class='celdaGrilla--peq fondoTitulos' style='$bgColor' >$tituloAccGrupo2</div>";
    }

    $encabezadoHTML    = "<div class='tablero--col' style='$columnasFilas; '>$filaEncabezados</div>";


    // :: datos grilla 
    $filasHTML       = "";
    $contador         = 1; 
    while ($rowGrilla = mysqli_fetch_array($salida)) { 
        if (($contador % 2) == 1) { $clase='filaImpar'; } else { $clase='filaPar'; }
            
        $idReg  	= $rowGrilla[$IdCampo];
 
        // :: datos filas
        $colorFil   = "";
        $fila       = ""; 
        foreach ($tablaDatos['columnas'] as $linea => $valoresLin) { 
            if ($linea > 0) {
                $campo  = $valoresLin['campo'];
                $ancho  = $valoresLin['ancho'];
                $dato   = trim($rowGrilla[$campo]);

                if (isset($valoresLin['colorCelda'])) { $colorCelda = $valoresLin['colorCelda']; } else { $colorCelda = false; }
                if (isset($valoresLin['utf'])) {
                    if ($valoresLin['utf']) { $dato = utf8_decode($dato); }
                }
                        
                if (isset($valoresLin['substr'])) {
                    $substr = $valoresLin['substr'];
                    $dato   = substr($dato,0,$substr);
                }
                
                if (isset($valoresLin['numform'])) { $numfor  = $valoresLin['numform']; } else { $numfor  = false; }
                if ($numfor) {  
                    $dato   = number_format($dato,0,',','.');
                }
        
                if (isset($valoresLin['date'])) { $datefor = $valoresLin['date']; } else { $datefor  = false; }
                if ($datefor) {  
                    $dato   = $dato = toFecDMA($dato);
                }

                if (isset($valoresLin['img'])) { $imagen = $valoresLin['img']; } else { $imagen  = false; }
                if ($imagen) {
                    if (isset($valoresLin['rutaImg'])) { $rutaImg = $valoresLin['rutaImg']; } else { $rutaImg  = "/images"; }
                    $dato = "<img src='$rutaImg/$dato' width='24' height='24' />";
                }
        
                $alinea = ""; $justify = '';
                if (isset($valoresLin['alin'])) {
                    $alin = $valoresLin['alin'];
                    switch($alin) {
                        case 'L': $alinea = ""; $justify = ''; break;
                        case 'C': $alinea = "centrado"; $justify = 'justify-content:center;';  break;
                        case 'R': $alinea = "derecha";  $justify = 'justify-content:flex-end;'; break;
                        default : $alinea = "";  $justify = ''; break;
                    }
                }
                
                if ($colorCelda) { 
                    $backgroundAux   = $bg_fila;
                    $backgroundCelda = trim($rowGrilla['colorCelda']); 
                    $bg_fila = "background-color:$backgroundCelda;";
                    } else { 
                    $colorFil = "";
                }

                $fila .= "<div class='celdaGrilla $claseManuscrito $claseCeldaiPec  $clase' style='$bg_fila; border-bottom:1px solid white; $justify' >$dato</div>"; 
                if ($colorCelda) { $bg_fila = $backgroundAux; }
            } else {
                $campo  = $valoresLin['campo'];
                $dato   = trim($rowGrilla[$campo]);
                
                if ($campo == 'colorFila' && trim($dato) != '') { 
                    $colorFila = trim($rowGrilla['colorFila']);  
                    if (trim($colorFila) == "") { $bg_fila = ""; } else { $bg_fila = " background-color:$colorFila; border-bottom:1px solid white;"; }
                } else {
                    $bg_fila = ""; 
                }
                // echo "<br>Campo: ".$valoresLin['campo']." - Dato: $dato -- bgFila: $bg_fila ";
            }
        }

      
        // :: acciones grupo 1
        if ($accionesGrupo1) {
            $fila .= "<div class='cursorPoint celdaGrilla $claseCeldaiPec centrado $clase' style='$bg_fila; border-bottom:1px solid white; paddinng-top:0; display: block;'>";
            foreach ($tablaDatos['accionesG1'] as $linea => $valoresLin) { 
                $jsAux = '';
                $funcionAux   = $valoresLin['funcion'] ?? '';
                $iconAux      = $valoresLin['icono'] ?? ''; 
                $titIcon      = $valoresLin['titulo'] ?? '';
                $parametroAux = $valoresLin['parametros'] ?? '';
                $parEstaticos = $valoresLin['paramEstaticos'] ?? '';
                $modPHP       = $valoresLin['modPHP'] ?? '';
    
                if (trim($modPHP) != '') {
                    // $funcionAux = "editar";
                    if (trim($jsAux) != '') { $jsAux .= ','; }
                    $jsAux .= "'$modPHP'";
                } else {
                   $jsAux = ''; 
                }

                if (trim($parametroAux) != '') { 
                    $parametros 	= explode(",",$parametroAux);
                    foreach($parametros as $ind => $camposParametros)  { 
                        if (isset($rowGrilla[$camposParametros])) {
                            $coma = (trim($jsAux) == '') ? '' : ',';
                            $datoAux = trim($rowGrilla[$camposParametros]); 
                            $jsAux  .= $coma . '\''.$datoAux.'\'';
                        } 
                    } 
                }

                if (trim($parEstaticos) != '') { 
                    $estaticos 	= explode(",",$parEstaticos);
                    foreach($estaticos as $ind => $constante)  { 
                        $coma = (trim($jsAux) == '') ? '' : ',';
                        $datoAux = trim($constante); 
                        $jsAux  .= $coma.'\''.$datoAux.'\'';
                    } 
                }
   
                $fila .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux($jsAux)\" src='/btns/$iconAux'  height='$tamaIco' title='$titIcon'> ";
            }
            $fila .= "</div>";  
        }
 
       
        // :: acciones2
        if ($accionesGrupo2) {
            $fila .= "<div class='cursorPoint celdaGrilla $claseCeldaiPec centrado $clase' style='$bg_fila; border-bottom:1px solid white; paddinng-top:0; display: block;'>";
            foreach ($tablaDatos['accionesG2'] as $linea => $valoresLin) { 
                $jsAux = '';
                $funcionAux   = $valoresLin['funcion'] ?? '';
                $iconAux      = $valoresLin['icono'] ?? ''; 
                $titIcon      = $valoresLin['titulo'] ?? '';
                $parametroAux = $valoresLin['parametros'] ?? '';
                $parEstaticos = $valoresLin['paramEstaticos'] ?? '';
                
              
                if (trim($parametroAux) != '') { 
                    $parametros 	= explode(",",$parametroAux);
                    foreach($parametros as $ind => $camposParametros)  { 
                        if (isset($rowGrilla[$camposParametros])) {
                            $coma = ($ind == 0) ? '' : ',';
                            $datoAux = trim($rowGrilla[$camposParametros]); 
                            $jsAux  .= $coma . '\''.$datoAux.'\'';
                        } else {
                            $jsAux  .= '';
                        }
                    } 
                }
    
                if (trim($parEstaticos) != '') { 
                    $estaticos 	= explode(",",$parEstaticos);
                    foreach($estaticos as $ind => $constante)  { 
                        $coma = (trim($jsAux) == '') ? '' : ',';
                        $datoAux = trim($constante); 
                        $jsAux  .= $coma.'\''.$datoAux.'\'';
                    } 
                }
    
           
                $fila .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux($jsAux)\" src='/btns/$iconAux'  height='$tamaIco' title='$titIcon'> ";
            }
            $fila .= "</div>";  
        }
   
        $filasHTML .= "<div class='tablero--col' style=' $columnasFilas;'>$fila</div>";
        $contador++;
    }
    
    unset($tablaDatos);

    
    // unset($filtrosAux);
?>