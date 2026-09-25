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

    // ::: guarda filtros que vienen por json para ser apliccados en la consulta
    for ($i = 1; $i <= 5; $i++) {
        $campoKey = "filtroAux{$i}Campo";
        $valorKey = "filtroAux{$i}Valor";

        $campoKey = "aux{$i}";
        $valorKey = "aux{$i}Valor";
        
        if (isset($arrayCampos[$campoKey]) && trim($arrayCampos[$campoKey]) != '') {
            $_SESSION[$campoKey] = $arrayCampos[$campoKey] ?? '';
            $_SESSION[$valorKey] = $arrayValores[$campoKey] ?? '';
        } else {
            unset($_SESSION[$campoKey], $_SESSION[$valorKey]);
        }
    }

    //! foreach ($_SESSION as $key => $val) { echo "<br>Sesion Key: $key - Val: $val "; }   

    // inicialización de variables con valores por defecto
    $accionesGrupo1  = $tablaDatos['setupTabla']['accionesGrupo1'] ?? FALSE;    
    $anchoAccGrupo1  = $tablaDatos['setupTabla']['anchoAccGrupo1']."fr" ?? "2fr";
    $tituloAccGrupo1 = (isset($tablaDatos['setupTabla']['tituloAccGrupo1']) && trim($tablaDatos['setupTabla']['tituloAccGrupo1']) !== '') 
        ? $tablaDatos['setupTabla']['tituloAccGrupo1'] 
        : 'Acc.';

    $accionesGrupo2  = (isset($tablaDatos['setupTabla']['accionesGrupo2']) && $tablaDatos['setupTabla']['accionesGrupo2'] !== '') 
        ? $tablaDatos['setupTabla']['accionesGrupo2'] : FALSE;
    $anchoAccGrupo2  = (isset($tablaDatos['setupTabla']['anchoAccGrupo2']) && $tablaDatos['setupTabla']['anchoAccGrupo2'] !== '') 
        ? $tablaDatos['setupTabla']['anchoAccGrupo2']."fr" : "2fr";
    $tituloAccGrupo2 = (isset($tablaDatos['setupTabla']['tituloAccGrupo2']) && trim($tablaDatos['setupTabla']['tituloAccGrupo2']) !== '') 
        ? $tablaDatos['setupTabla']['tituloAccGrupo2'] 
        : 'Acc.';

    $conLimite       = $tablaDatos['setupTabla']['conLimite'] ?? 0; 
    $conPaginacion   = $tablaDatos['setupTabla']['conPaginacion'] ?? true;
    $lineasPorPagina = $tablaDatos['setupTabla']['lineasPorPagina'] ?? PAGINACION;   
    $grillaPeq       = $tablaDatos['setupTabla']['grillaPeq'] ?? FALSE;
    $manuscrito      = $tablaDatos['setupTabla']['manuscrito'] ?? FALSE;
    $soloConFiltro   = $tablaDatos['setupTabla']['soloConFiltro'] ?? FALSE;

    $conFiltro       = isset($tablaDatos['setupTabla']['conFiltro']) ? $tablaDatos['setupTabla']['conFiltro'] : true;

    $ignorarWhere    = $tablaDatos['setupTabla']['ignorarWhere'] ?? FALSE;
    $conSubConsulta  = $tablaDatos['setupTabla']['conSubConsulta'] ?? FALSE;
    $funcionBusqueda = $tablaDatos['setupTabla']['funcionBusqueda'] ?? ''; 
    $colorEncabezado = $tablaDatos['setupTabla']['colorEncabezado'] ?? '';

    $verConsulta     = $tablaDatos['setupTabla']['verConsulta'] ?? FALSE;
    

    // fuente manuscrita
    if (isset($manuscrito) && $manuscrito) { $claseManuscrito = "celdaGrilla--manuscrito"; } else { $claseManuscrito = ""; }

    // ajuste de iconos
    if ($grillaPeq) { $claseCeldaiPec = "celdaGrilla--peq"; $tamaIco = 20; } else { $claseCeldaiPec = ""; $tamaIco = 25; }

    // Fondo encabezado
    if (trim($colorEncabezado) != "") { $bgColor = "background-color:$colorEncabezado"; } else { $bgColor = ""; }


    $filtros = [];
    if ($conFiltro) {
        // Procesar criterios de búsqueda
        $criterio  = trim($input['iguala'] ?? '');
        $buscarpor = $input['buscarpor'] ?? '';
        $camposBusquedaCalculada = $tablaDatos['setupTabla']['camposBusquedaCalculada'] ?? array();
        $criterio = $conexionDB->escapaDatos(substr($criterio, 0, 160));
        if ($criterio !== '' && $buscarpor !== '') {
            // Cuando un módulo define una búsqueda calculada, sólo se aceptan
            // sus claves declaradas; nunca una expresión enviada por el navegador.
            if (!empty($camposBusquedaCalculada)) {
                if (array_key_exists($buscarpor, $camposBusquedaCalculada)) {
                    $campo = $camposBusquedaCalculada[$buscarpor];
                    $filtros[] = "($campo) LIKE '%$criterio%'";
                }
            } else {
                $buscarporArray = explode(",", $buscarpor);
                if (count($buscarporArray) == 2 && $buscarporArray[0] == 'L') {
                    $campo = $buscarporArray[1];
                    $filtros[] = "$campo LIKE '%$criterio%'";
                } else {
                    $campoCom   = explode(".", $buscarpor);
                    $buscarpor  = (count($campoCom) == 2) ? "`{$campoCom[0]}`.`{$campoCom[1]}`" : "`$buscarpor`";
                    $filtros[]  = "$buscarpor LIKE '$criterio%'";
                }
            }
        }
    }

    // Filtros auxiliares dinámicos
    $filtrosAux = [];
    foreach ($arrayCampos as $key => $val) {
        $campo = $arrayCampos[$key] ?? '';
        $valor = $arrayValores[$key] ?? '';

        if (trim($campo) == '' && isset($_SESSION[$key])) { 
            $campo = $_SESSION[$key]; 
            $valor = $_SESSION[$key . 'Valor'] ?? '';
        }
       
        $arrayCampo = explode(".", $campo);
        $campo   = (count($arrayCampo) == 2) ? "`{$arrayCampo[0]}`.`{$arrayCampo[1]}`" : "`$arrayCampo[0]`";
        
        if ($campo && $valor !== '') { 
            $filtrosAux[] = [
                "campo" => $campo,
                "valor" => $valor,
            ];
        }
    }

    if ($soloConFiltro) { 
        if (count($filtrosAux) == 0 && count($filtros) == 0) { 
            $encabezadoHTML = '';
            $filasHTML      = '';
            $paginacionHTML = '';
            return; 
        } 
    } 

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
                    case 'D': $filtros[] = "$campo >= '$valorFiltro'"; break;
                    case 'H': $filtros[] = "$campo <= '$valorFiltro'"; break;
                    case 'L': $filtros[] = "$campo LIKE '%$valorFiltro%'"; break;
                    case 'Y': $filtros[] = "YEAR($campo) = '$valorFiltro'";  break;
                }
            } else {
                $filtros[] = "$campo = '$valorFiltro'";
            }
        }
    }
 
    // consulta
    $consulta     = $tablaDatos['consulta'];

  

    // insertar WHERE
    if (!$ignorarWhere && count($filtros) > 0 ) {
        if ($conSubConsulta) {
            $consulta .= " WHERE ";
        } else {
            $consulta .= (stripos($consulta, 'WHERE') === false ? " WHERE " : " AND ");
        }
        $consulta .= implode(" AND ", $filtros);
    } 

    // ordenar por
    if (isset($tablaDatos['ordenarPor']) && trim($tablaDatos['ordenarPor']) !== '' ) {
        $consulta .= " ORDER BY {$tablaDatos['ordenarPor']}";
    }
    
    // :: Muestra consulta en proceso de desarrollo
    if ($verConsulta) { echo "<pre>$consulta</pre>";   }


    if ($conLimite > 0) {
        $consulta .= " LIMIT $conLimite";
    }


    // :: paginación
    if ($conPaginacion) {
        include(PATH_INCLUDES.'grillaPaginacion3.php');
        $contador = 1;
        $consulta .= " LIMIT $RegInicial,".$lineasPorPagina;
        // echo "<br>Ln 194 pagina: $_pag  LIMIT $RegInicial, $lineasPorPagina<br>";
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
            $filaEncabezados .= "<div class='celdaGrilla--peq fondoTitulos grillaResultados__encabezado' style='$bgColor'>$titulo</div>";
        }
    }  

    if ($accionesGrupo1) { 
        $columnasTit     .= " $anchoAccGrupo1";
        $columnasFilas   .= " $anchoAccGrupo1";
        $filaEncabezados .= "<div class='celdaGrilla--peq fondoTitulos grillaResultados__encabezado' style='$bgColor' >$tituloAccGrupo1</div>";
    }

    if ($accionesGrupo2) { 
        $columnasTit     .= " $anchoAccGrupo2";
        $columnasFilas   .= " $anchoAccGrupo2";
        $filaEncabezados .= "<div class='celdaGrilla--peq fondoTitulos grillaResultados__encabezado' style='$bgColor' >$tituloAccGrupo2</div>";
    }

    $estilosGrillaModerna = '<style>.grillaResultados{border-left:1px solid #e4ebf1;border-right:1px solid #e4ebf1}.grillaResultados--encabezado{position:sticky;top:0;z-index:2;border-radius:9px 9px 0 0;overflow:hidden;box-shadow:0 2px 5px rgba(32,61,83,.09)}.grillaResultados--fila{min-height:42px;transition:transform .12s ease,box-shadow .12s ease}.grillaResultados--fila:hover{position:relative;z-index:1;transform:translateY(-1px);box-shadow:0 3px 10px rgba(32,61,83,.12)}.grillaResultados__encabezado{display:flex!important;align-items:center;min-height:38px;padding:8px 10px!important;letter-spacing:.025em;font-size:.76rem;text-transform:uppercase}.grillaResultados__celda{display:flex!important;align-items:center;padding:9px 10px!important;border-color:#eef2f5!important;line-height:1.3}.grillaResultados__celda .celdaGrilla--img{transition:transform .12s ease,filter .12s ease}.grillaResultados__celda .celdaGrilla--img:hover{transform:scale(1.12);filter:drop-shadow(0 2px 2px rgba(32,61,83,.2))}.grillaEstado{display:inline-flex;align-items:center;padding:7px 16px;border-radius:999px;font-size:1rem;line-height:1;white-space:nowrap}.grillaEstado--recibida{background:#e6f6f5;color:#00777c}.grillaEstado--proceso{background:#fff3d6;color:#966000}.grillaEstado--finalizada{background:#e7f6eb;color:#207540}.grillaEstado--alerta{background:#fde9e8;color:#b1342d}.grillaEstado--neutro{background:#edf1f4;color:#455a64}</style>';
    $encabezadoHTML = $estilosGrillaModerna . "<div class='tablero--col grillaResultados grillaResultados--encabezado' style='$columnasFilas; '>$filaEncabezados</div>";

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

                if (isset($valoresLin['colorCelda'])) { $colorCelda = $valoresLin['colorCelda']; } else { $colorCelda = FALSE; }
                if (isset($valoresLin['utf'])) {
                    if ($valoresLin['utf']) { $dato = utf8_decode($dato); }
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
                    if (trim($dato) == '' || $dato == '0000-00-00 00:00:00') {
                        $dato = '00-00-0000';
                    } else {
                        $dato = toFecDMA($dato);
                    }
                }
                
                if (isset($valoresLin['dateTime'])) { $dateTimefor = $valoresLin['dateTime']; } else { $dateTimefor  = FALSE; }
                if ($dateTimefor) {  
                    if (trim($dato) == '' || $dato == '0000-00-00 00:00:00') {
                        $dato = '00-00-0000 00:00';
                    } else {
                        $dato = date("d-m-Y H:i", strtotime($dato));
                    }
                }

                if (isset($valoresLin['img'])) { $imagen = $valoresLin['img']; } else { $imagen  = FALSE; }
                if ($imagen) {
                    if (isset($valoresLin['rutaImg'])) { $rutaImg = $valoresLin['rutaImg']; } else { $rutaImg  = "/images"; }
                    $dato = "<img src='$rutaImg/$dato' width='24' height='24' />";
                }

                // Formato opcional de estado; el comportamiento de las
                // columnas existentes no cambia si no declaran estado=true.
                if (!empty($valoresLin['estado'])) {
                    $estadoTexto = trim((string) $dato);
                    $estadoClave = strtoupper(str_replace([' ', '-'], '_', $estadoTexto));
                    if (in_array($estadoClave, ['RECIBIDA', 'INGRESADA', 'ABIERTA', 'ABIERTO', 'ACTIVA', 'ACTIVO', 'VIGENTE'], true)) {
                        $estadoClase = 'recibida';
                    } elseif (in_array($estadoClave, ['PENDIENTE', 'EN_GESTION', 'EN_PROCESO', 'DERIVADA'], true)) {
                        $estadoClase = 'proceso';
                    } elseif (in_array($estadoClave, ['CERRADA', 'CERRADO', 'FINALIZADA', 'FINALIZADO', 'RESUELTA', 'APROBADA'], true)) {
                        $estadoClase = 'finalizada';
                    } elseif (in_array($estadoClave, ['ANULADA', 'ANULADO', 'RECHAZADA', 'RECHAZADO', 'DESCARTADA', 'DESCARTADO', 'INACTIVA', 'INACTIVO'], true)) {
                        $estadoClase = 'alerta';
                    } else {
                        $estadoClase = 'neutro';
                    }
                    $estiloEstado = '';
                    if (!empty($valoresLin['colorEstado'])) {
                        $colorConfigurado = $valoresLin['colorEstado'];
                        $campoColorEstado = $colorConfigurado === true ? 'colorEstado' : trim((string) $colorConfigurado);
                        $colorEstado = array_key_exists($campoColorEstado, $rowGrilla)
                            ? trim((string) $rowGrilla[$campoColorEstado])
                            : trim((string) $colorConfigurado);
                        if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $colorEstado)) {
                            $estiloEstado = ' style="background-color:' . $colorEstado . '"';
                        }
                    }
                    $dato = '<span class="grillaEstado grillaEstado--' . $estadoClase . '"' . $estiloEstado . '>' . htmlspecialchars($estadoTexto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</span>';
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

                $fila .= "<div class='celdaGrilla grillaResultados__celda $claseManuscrito $claseCeldaiPec $clase' style='$bg_fila; border-bottom:1px solid white; $justify' >$dato</div>"; 
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
            $fila .= "<div class='cursorPoint celdaGrilla grillaResultados__celda $claseCeldaiPec centrado $clase' style='$bg_fila; border-bottom:1px solid white; paddinng-top:0; display: block;'>";
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
            $fila .= "<div class='cursorPoint celdaGrilla grillaResultados__celda $claseCeldaiPec centrado $clase' style='$bg_fila; border-bottom:1px solid white; paddinng-top:0; display: block;'>";
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
   
        $filasHTML .= "<div class='tablero--col grillaResultados grillaResultados--fila' style=' $columnasFilas;'>$fila</div>";
        $contador++;
    }
    unset($tablaDatos);
    unset($auxiliares);


    // :: si conexión asíncrona, devuelve solo el contenido
    if ($_async == 1) {
        echo $encabezadoHTML;
        echo $filasHTML;
        echo "<div class='tablero__footer'><ul class='paginacion'>$paginacionHTML</ul></div>";
    } 
    /*  echo "<br>grillaLeeRes3.php - fin <br> $consulta<br>";  */
?>
