<?php
    /*
        Script que genera grilla de salida a partir de una consulta, requiere de:

        $consultaGrilla =   Variable que contiene la consulta, si no existe "$consultaGrilla", se genera consulta
                            dinamicamente --> $consulta = "SELECT *   FROM `$tablaDB`   ORDER BY $orderBy";

        Variables necesarias si no se pasa la consulta:
            - $tablaDB  
            - $orderBy

		$verConsulta  = true o false;

        $conEdit      = true o false;  con o sin link a ficha mantenedora funcion editarFila() por defecto, si se especifica una funcion tomara esta ultima
        
        $funcionEdit  = Si no se especifica una FUNCION,  la funsión por defecto será la función "editarFila()";

        $soloConFiltro  = true o false realiza la busqueda solo si hay filtros
        $conBorrar      = true o false;  con o sin boton de borrado de registro

        ===========================================================================================================================================================
        ACCIONES:

        Acciones predifinidas
        $conEdit    = true;    $funcionEdit = "editar";      $iconEdit = "btn_editar.png";      $parametroEdit="";
        $conBorrar  = false;   $funcionDel  = "borrarFila";  $iconDel  = "btn_borrar.png";
        
        Acciones opcionales
        $conBotAux1 = true;    $funcionAux1 = "verDoc";	     $iconAux1 = "btn_ver.png";	    	$parametroAux1 = "casmpo_1,campo_2,campo_n";  $parEstaticos1 = "valor_1,valor_2,valor_n";

                **    SE PUEDEN USAR HASTA 4 ACCIONES OPCIONALES MODIFICANDO EL NUMERO DE LAS VARIABLES (1,2,3,4)  **
        
        ===========================================================================================================================================================
        FILAS DE COLOR:

        El color afecta a linea de forma individual por lo cual este debe venir desde la consulta.
         
        Ejem. pcir --> modulos/vehiculos.php
        SELECT 	IdVehiculo, placa, marca, modelo, version, aaaa_vehiculo, codigo_siiNew, rut, CONCAT(rut,'-',rut_dv) AS RutPro, multas,
    			IF(multas > 0, 'yellow', '') AS colorFila  ......
                IF(multas > 0, 'black',  '') AS colorFont  ......
        
        El campo debe ser 'colorFila' y debe ser en el indice 0
        $arrayGrilla[0] = array("campo" => 'colorFila' );

        ===========================================================================================================================================================
        FILTRADO CON CAMPOS AUXILIARES

        Requiere de una funcion JS de busqueda modificada, no sirve la funcion buscar().

        $filtroAux1BD     ='nombre o alias de la base de datos' es opcional, se puede necesitar en determinadas consulta para evitar el error de "Campo Ambiguo"
                            ejemplo: gdoc --> modulos/misDocumentos.php 

        $filtroAux1Campo  = Nombre de campo por el cual se filtrara la base de datos
        
        
        $filtroAux1Valor  = Valor del campo por el cual se filtrara;

                            modificadores de busqueda del filtro
                            -------------------------------------
                            D: D,0000-00-00 -> solo para campos fechas, Filtra desde indicada
                            H: H,0000-00-00 -> solo para campos fechas, Filtra hasta indicada
                            Y: Y,2023       -> solo para campos fechas, por el año del campo indicado
                            L: L,valor      -> modifica el operador  LIKE  %valor% busca el valor solicitado en cualquier parte del campo, si no se especifica
                                               el filtro por defecto es LIKE %valor

                            esto requiere de una funcion de busqueda modificada, 
                            
                            ej buscarDoc() en decretos.tpl sistema gestion documental
                            if (aaaaFind   == 'T') { filtroAux1 = ''; } else { filtroAux1 = '&filtroAux1Campo=fecDecreto'+'&filtroAux1Valor=Y,'+aaaaFind; }

                            ej buscarDoc() en recepcion.tpl sistema gestion documental
                            if (desde == '') { filtroAux2 = ''; } else { filtroAux2 = '&filtroAux2Campo=fechaIngreso'+'&filtroAux2Valor=D,'+desde; }
			                if (hasta == '') { filtroAux3 = ''; } else { filtroAux3 = '&filtroAux3Campo=fechaIngreso'+'&filtroAux3Valor=H,'+hasta; }

                **  SE PUEDEN UTILIZAR HASTA 4 FILTROS (1, 2, 3, 4), CAMBIANDO EL NUMERO EN LA VARIABLE QUE LOS IDENTIFICA **

        ===========================================================================================================================================================
        CAMPOS GRILLA:

        $arrayGrilla[1] = array("campo" => 'nombreCampo', "alin" => 'L', "titulo" => 'TituloCol', "ancho" => 'ancho', "utf" => true, "last" => true );
        campo:  Nombre de campo
        alin:   Alineacion del dato L,C,R
        titulo: Titulo de la columna
        titulo: Ancho de la columan 1,2 3 (trabaja con clase 960 span-n)
        utf:    true o false
        last:   true o false
        substr: 18             Largo de la cadena antes de cortar
        
        "date"      => true/false     Formatea fecha
        "numform"   => true/false     Formatea numeros
        "decimales" => nroDecimales

        img         => true/false   "el contenido del campo apunta a una imagen"
        rutaImg     => Ruta donde se encuentra la imagen

        $acciones   => true/false   "si existen acciones, por defecto true"

        $ignorarWhere = true o false "solamente se usa en el caso de consulas con UNION o subconsultas con 'WHERE' intermedios valor por defecto false
                        Ejemplo: consulta movVacaciones.php  sistema personal
    */

    $bg_fila = "";

    if (!isset($acciones)) { $acciones = true;  }  
    if (isset($anchoAcciones)) { $anchoAcciones = $anchoAcciones."fr";  }  else {  $anchoAcciones = " 3fr"; }
    
    if (!isset($ignorarWhere)) { $ignorarWhere = false;  }  

    // :: valor por defecto es CON PAGINACION true
    if (!isset($conPaginacion)) { $conPaginacion = true; } 
    if (!isset($conLimite)) { $conLimite = false; } 

    // :: ordena el array por campo enviado en $orderBy
    if (isset($orderBy)) {
        $ordenarPor = array_column($arrayLee, $orderBy);

        $sortFlag = SORT_REGULAR;
        $sortFlag = is_numeric($arrayLee[0][$orderBy]) ? SORT_NUMERIC : SORT_STRING;

        array_multisort($ordenarPor, $sortFlag | SORT_DESC, $arrayLee); 
    }

    /*
    foreach ($arrayLee as $key => $row) {
        echo "<br> $key => $row  *** " . $row[$orderBy] ;
    }
    */

    // :: cuenta cuantas colummas vienen en el array para la grilla
    $numeroColumnas = count($arrayGrilla); 

    // :: Si es true no muestra ninguna fila mientras no se seleccione un criterio de filtrado
    if (!isset($soloConFiltro))  { $soloConFiltro = false;  } 

    // :: Botones en acciones
    if (!isset($conEdit))    { $conEdit    = false; }
    if (!isset($conBorrar))  { $conBorrar  = false; }    

    if (!isset($funcionEdit) or trim($funcionEdit) == '')  { $funcionEdit = "editarFila"; } 
    if (!isset($funcionDel)  or trim($funcionDel) == '')   { $funcionDel  = "borrarFila"; } 

    // :: Activa grilla tamaño pequeño cuando es true
    if (!isset($grillaPeq))  { $grillaPeq = false; }
    
    if ($grillaPeq) { $claseCeldaiPec = "celdaGrilla--peq"; $tamaIco = 18; } else { $claseCeldaiPec = ""; $tamaIco = 25; } 

    $grillaHTML     = '';
    $paginacionHTML = '';
    $selected       = ""; 

    // :::::::::::::::::::::::::::::::::::::: Titulos Grilla  :::::::::::::::::::::::::::::::::::: //
    $grillaFilTitHTML  = ""; $grillaFilasHTML = "";
        
    $columnasTit       = "padding-top:10; row-gap:1px; column-gap:1px; grid-template-columns: ";
    $columnasFilas     = "padding-top:0;  row-gap:1px; column-gap:1px; grid-template-columns: ";

    foreach ($arrayGrilla as $linea => $valoresLin) {
        $titulo    = $valoresLin['titulo'];
        $ancho     = $valoresLin['ancho'];

        $columnasTit   .= $ancho."fr ";
        $columnasFilas .= $ancho."fr ";

        $grillaFilTitHTML .= "<div class='celdaGrilla--peq  fondoTitulos'>$titulo</div>";
    }  

    if ($acciones) {
        if ($conBotAux1 OR $conBotAux2 OR $conBotAux3  OR $conBotAux4 OR $conEdit OR $conBorrar) {
            $columnasTit   .= " $anchoAcciones";
            $columnasFilas .= " $anchoAcciones";
            $grillaFilTitHTML .= "<div class='celdaGrilla--peq fondoTitulos' >Acc.</div>";
        }
    }
    $grillaHTMLTit    = "<div class='tablero--col' style='$columnasTit; '>$grillaFilTitHTML</div>";

    // :::::::::::::::::::::::::::::::::::::: Datos Grilla  :::::::::::::::::::::::::::::::::::: //
    $borrar           = 'borrar';
    $colorFil         = "";
    $grillaFilasHTML  = ""; 
       
    $contador         = 1; 
    foreach($arrayLee as $ind => $row) { 
        if (($contador % 2) == 1) { $clase='filaImpar'; } else { $clase='filaPar'; }

        $idReg  	= $row[$IdCampo]; 
        $js     	= "'$moduloPHP','$idReg'";
        if (isset($parametroEdit)) { 
            if (trim($parametroEdit) != '') {
                $parametros 	= explode(",",$parametroEdit);
                foreach($parametros as $ind => $campo)  { 
                    $campoAux  = trim($row[$campo]); 
                    $js       .= ",'$campoAux'"; 
                } 
            } 
        }	
        
        if (isset($parametroAux1)) {   
            $jsAux1     = '\''.$idReg.'\'';
            if (trim($parametroAux1) != '') { 
                $parametros1 	= explode(",",$parametroAux1);
                foreach($parametros1 as $ind => $campoAux1)  { 
                    $datoAux1	= trim($row[$campoAux1]); 
                    $jsAux1    .= ',\''.$datoAux1.'\''; 
                } 
            }
            if (trim($parEstaticos1) != '') { 
                $estaticos1 	= explode(",",$parEstaticos1);
                foreach($estaticos1 as $ind => $campoAux1)  { 
                    $datoAux1	= trim($campoAux1); 
                    $jsAux1    .= ',\''.$datoAux1.'\'';
                } 
            }
        }		
    
        $jsAux2     = '\''.$idReg.'\'';
        if (isset($parametroAux2)) {  
            if (trim($parametroAux2) != '') { 
                $parametros2 	= explode(",",$parametroAux2);
                foreach($parametros2 as $ind => $campoAux2)  { 
                    $datoAux2	= trim($row[$campoAux2]); 
                    $jsAux2   .= ',\''.$datoAux2.'\'';
                } 
            }
        }	
        
        $jsAux3     = '\''.$idReg.'\'';
        if (isset($parametroAux3)) {  
            if (trim($parametroAux3) != '') { 
                $parametros3 	= explode(",",$parametroAux3);
                foreach($parametros3 as $ind => $campoAux3)  { 
                    $datoAux3	= trim($row[$campoAux3]); 
                    $jsAux3   .= ',\''.$datoAux3.'\'';
                } 
            }
        }	
        
        $jsAux4     = '\''.$idReg.'\'';
        if (isset($parametroAux4)) {  
            if (trim($parametroAux4) != '') { 
                $parametros4 	= explode(",",$parametroAux4);
                foreach($parametros4 as $ind => $campoAux4)  { 
                    $datoAux4	= trim($row[$campoAux4]); 
                    $jsAux4   .= ',\''.$datoAux4.'\'';
                } 
            }
        }	
 
        $grillaFilasHTML = "";
        foreach ($arrayGrilla as $linea => $valoresLin) { 
            $campo  = $valoresLin['campo'];
            $ancho  = $valoresLin['ancho'];
            $dato   = trim($row[$campo]);
     
            if (isset($valoresLin['utf'])) {
                if ($valoresLin['utf']) { $dato = utf8_decode($dato); }
            }

            if (isset($valoresLin['substr'])) {
                $substr = $valoresLin['substr'];
                $dato   = substr($dato,0,$substr);
            }
            
            if (isset($valoresLin['numform'])) { $numfor  = $valoresLin['numform']; } else { $numfor  = false; }
            if ($numfor && trim($dato) != '') {  
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

            $grillaFilasHTML .= "<div class='celdaGrilla  $claseCeldaiPec $alinea $clase' style='$bg_fila  $justify' >$dato</div>";  
        }

        if ($acciones) {
            if ($conEdit OR $conBotAux1 OR $conBotAux2 OR $conBotAux3 OR $conBotAux4 OR $conBorrar) {
                $grillaFilasHTML .= "<div class='cursorPoint celdaGrilla $claseCeldaiPec centrado $clase' style='$bg_fila'>";
                    if ($conEdit)    { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionEdit($js)\" src='/btns/$iconEdit' height='$tamaIco' >"; }
                    if ($conBorrar)  { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionDel($js)\"  src='/btns/$iconDel'  height='$tamaIco' >"; }
                    if ($conBotAux1) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux1($jsAux1)\" src='/btns/$iconAux1' height='$tamaIco'>"; }
                    if ($conBotAux2) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux2($jsAux2)\" src='/btns/$iconAux2' height='$tamaIco'>"; }
                    if ($conBotAux3) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux3($jsAux3)\" src='/btns/$iconAux3' height='$tamaIco'>"; }
                    if ($conBotAux4) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux4($jsAux4)\" src='/btns/$iconAux4' height='$tamaIco'>"; }
                $grillaFilasHTML .= "</div>";  
            }
        } 

        $grillaHTML .= "<div class='tablero--col' style=' $columnasFilas;'>$grillaFilasHTML</div>";
        $contador++;
    }
  ?>