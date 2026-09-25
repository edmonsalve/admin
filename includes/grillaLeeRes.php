<?php
    // FIUNCIONA CORRECTAMENTE A FECHA DE 2024-12-10 
    // SE DEJA EN MODO DE PRUEBA LA NUEVA VERSION OPTIMIZADA
    
        /* 
        Script que genera grilla de salida a partir de una consulta, requiere de:

        $consultaGrilla =   Variable que contiene la consulta, si no existe "$consultaGrilla", se genera consulta
                            dinamicamente --> $consulta = "SELECT *   FROM `$tablaDB`   ORDER BY $orderBy";

        Variables necesarias si no se pasa la consulta:
            - $tablaDB  
            - $orderBy

        $grillaPeq       = true/false
        $backgroundColor = '#000000';

		$verConsulta  = true o false;

        $conEdit      = true o false;  con o sin link a ficha mantenedora funcion editarFila() por defecto, si se especifica una funcion tomara esta ultima
        
        $funcionEdit     = Si no se especifica una FUNCION,  la funsión por defecto será la función "editarFila()";
        $funcionBusqueda = Si no se especifica una FUNCION,  la funsión por defecto será la función"filtrar()";  // funcion de busqueda en la paginacion

        $soloConFiltro  = true o false realiza la busqueda solo si hay filtros
        $conBorrar      = true o false;  con o sin boton de borrado de registro

        ===========================================================================================================================================================
        ACCIONES:

        $acciones       = true/false   "si existen acciones, por defecto true"
        $anchoAcciones  = 1

        Acciones predifinidas
        $conEdit    = true;    $funcionEdit = "editar";      $iconEdit = "btn_editar.png";  $titEdit   = "Editar" ; $parametroEdit="";
        $conBorrar  = false;   $funcionDel  = "borrarFila";  $iconDel  = "btn_borrar.png";  $titBorrar = "Borrar" ; 
        
        Acciones opcionales
        $conBotAux1 = true;    $funcionAux1 = "verDoc";	     $iconAux1 = "btn_ver.png";	  $titIcon1='ver'  	$parametroAux1 = "casmpo_1,campo_2,campo_n";  $parEstaticos1 = "valor_1,valor_2,valor_n";

                **    SE PUEDEN USAR HASTA 5 ACCIONES OPCIONALES MODIFICANDO EL NUMERO DE LAS VARIABLES (1,2,3,4)  **
        
        ===========================================================================================================================================================
        FILAS o CELDAS CON COLOR:

        El color afecta a linea de forma individual por lo cual este debe venir desde la consulta.
         
        Ejem. pcir --> modulos/vehiculos.php
        SELECT 	IdVehiculo, placa, marca, modelo, version, aaaa_vehiculo, codigo_siiNew, rut, CONCAT(rut,'-',rut_dv) AS RutPro, multas,
    			IF(multas > 0, 'yellow', '') AS colorFila   ......
    			IF(multas > 0, 'yellow', '') AS colorCelda  ......
                IF(multas > 0, 'black',  '') AS colorFont   ......
        
        El campo debe ser 'colorFila' o 'colorCelda'  en el indice 0

        $arrayGrilla[0] = array("campo" => 'colorFila' );
        $arrayGrilla[0] = array("campo" => 'colorCelda');

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

        "img"       => true/false   "el contenido del campo apunta a una imagen"
        "rutaImg"   => Ruta donde se encuentra la imagen

        

        $ignorarWhere = true o false "solamente se usa en el caso de consulas con UNION o subconsultas con 'WHERE' intermedios valor por defecto false
                        Ejemplo: consulta movVacaciones.php  sistema personal

    */
    //! :: Para ayuda en el desarrollo, ECHO que muestra consulta que se envía al servidor
    if (!isset($verConsulta))    { $verConsulta   = false; }

    if (!isset($moduloPHP)) {
        $moduloPHP = '';
    }

    // :: cambia backgroundColor de titulos
    if (isset($backgroundColor)) { $bgColor = "background-color: $backgroundColor;"; } else { $bgColor = ""; }


    $bg_fila = "";

    // :: Activa grilla tamaño pequeño cuando es true
    if (!isset($grillaPeq))  { $grillaPeq = false; }
    if ($grillaPeq) { $claseCeldaiPec = "celdaGrilla--peq"; $tamaIco = 20; } else { $claseCeldaiPec = ""; $tamaIco = 25; } 

    // :: Acciones
    if (!isset($acciones))  { $acciones = true;  }  
    if (!isset($titAcciones)) { $titAcciones = 'Acc.';  }  

    if (!isset($acciones2)) { $acciones2 = false;  }  
    if (!isset($titAcciones2)) { $titAcciones2 = 'Acc. 2';  }  

    if (isset($anchoAcciones))  { $anchoAcciones  = $anchoAcciones."fr";  }   else {  $anchoAcciones  = " 3fr"; }
    if (isset($anchoAcciones2)) { $anchoAcciones2 = $anchoAcciones2."fr";  }  else {  $anchoAcciones2 = " 2fr"; }
    
    if (!isset($ignorarWhere)) { $ignorarWhere = false;  }  

    // :: valor por defecto es CON PAGINACION true
    if (!isset($conPaginacion)) { $conPaginacion = true; } 
    if (!isset($conLimite)) { $conLimite = false; } 
    

    // :: cuenta cuantas colummas vienen en el array para la grilla
    $numeroColumnas = count($arrayGrilla); 

    // :: Si es true no muestra ninguna fila mientras no se seleccione un criterio de filtrado
    if (!isset($soloConFiltro))  { $soloConFiltro = false;  } 

    // :: variable que contiene la consulta que se ejecutara, si no exixte la consulta de construye con las variables ($tablaDB - $orderBy)    
    if (isset($consultaGrilla))  { $conConsulta   = true; } else { $conConsulta = false; }


    // :: Botones en acciones
    if (!isset($conEdit))    { $conEdit    = false; }
    if (!isset($conBorrar))  { $conBorrar  = false; }    

    if (!isset($funcionEdit) or trim($funcionEdit) == '')  { $funcionEdit = "editarFila"; } 
    if (!isset($funcionDel)  or trim($funcionDel) == '')   { $funcionDel  = "borrarFila"; } 

    

    // ::: determina Paginación
	$where		  = '';
	$criterio     = '';
    
    if (isset($_GET['buscarpor'])) { $buscarpor    = $_GET['buscarpor']; } else  { $buscarpor    = ''; }

    $puedeLeer   = true; 

   if (isset($conFiltro)) { $conFiltro = $conFiltro; } else { $conFiltro = true; }

    if (!isset($confiltro0)) { $confiltro0 = false; }
    if (!isset($confiltro1)) { $confiltro1 = false; }
    if (!isset($confiltro2)) { $confiltro2 = false; }
    if (!isset($confiltro3)) { $confiltro3 = false; }
    if (!isset($confiltro4)) { $confiltro4 = false; }
    if (!isset($confiltro5)) { $confiltro5 = false; }

    if (!isset($conBotAux1)) { $conBotAux1 = false; }
    if (!isset($conBotAux2)) { $conBotAux2 = false; }
    if (!isset($conBotAux3)) { $conBotAux3 = false; }
    if (!isset($conBotAux4)) { $conBotAux4 = false; }
    if (!isset($conBotAux5)) { $conBotAux5 = false; }

    if (!isset($parEstaticos1)) { $parEstaticos1 = ""; }
    if (!isset($parEstaticos2)) { $parEstaticos2 = ""; }
    if (!isset($parEstaticos3)) { $parEstaticos3 = ""; }
    if (!isset($parEstaticos4)) { $parEstaticos4 = ""; }
    if (!isset($parEstaticos5)) { $parEstaticos5 = ""; }
    
    if (!isset($titEdit )) { $titEdit  = ""; }
    if (!isset($titBorrar )) { $titBorrar  = ""; }
    if (!isset($titIcon1)) { $titIcon1 = ""; }
    if (!isset($titIcon2)) { $titIcon2 = ""; }  
    if (!isset($titIcon3)) { $titIcon3 = ""; }
    if (!isset($titIcon4)) { $titIcon4 = ""; }
    if (!isset($titIcon5)) { $titIcon5 = ""; }

    // :: CRITERIOS DE BUSQUEDA Filtro 0
    $htmlCriterios = '';
    
    if (isset($arrayCriterios)) {  
        foreach($arrayCriterios as $orden => $arrayC) {
            $campo      = $arrayC['campo'];
            $campoTxt   = $arrayC['descripcion'];
                
            if ($buscarpor == "$campo") { 
                $selected = " selected='selected' "; 
            } else { 
                $selected = ""; 
            }  
            $htmlCriterios .= "<option value='$campo' $selected>$campoTxt</option>";   
        }
    }
     
	if (isset($_GET['buscarpor'])) {  
        $criterio  	= trim($_GET['iguala']);
		$buscarpor 	= $_GET['buscarpor'];   

        if ($conFiltro) {
            if (trim($criterio) != '') { 
                $buscarporArray  = explode(",",$buscarpor);
                $nroElemtBuscar  = count($buscarporArray);
                
                if ($nroElemtBuscar == 2) {
                    $modificador = $buscarporArray[0];
                    $buscarpor   = $buscarporArray[1];
                    
                    if ($modificador == 'L') {
                        $filtro0    = " $buscarpor  like  '%$criterio%' "; 
                        $confiltro0 = true;
                    }
                } else {
                    $filtro0    = " $buscarpor  like  '$criterio%' "; 
                    $confiltro0 = true;
                } 
                if (isset($filtroAux0BD)) {  $filtro0 = "`$filtroAux0BD`.".$filtro0; }
            }
        }


        // ::: busqueda con campos auxiliares
        if ((isset($_GET['filtroAux1Campo']) || isset($_SESSION['filtroAux1Campo'])) && $conFiltro) {
            if (trim($_GET['filtroAux1Valor']) != '') { //  || isset($_SESSION['filtroAux1Valor']) != ''

                if (isset($_GET['filtroAux1Campo'])) {
                    $filtroAux1Campo = $_GET['filtroAux1Campo'];
                    $filtroAux1Valor = $_GET['filtroAux1Valor'];
                    // echo "<br>Filtro GET";
                } else {
                    $filtroAux1Campo = $_SESSION['filtroAux1Campo'];
                    $filtroAux1Valor = $_SESSION['filtroAux1Valor'];
                    // echo "Filtro SESSION";
                }

                $valoresAux1    = explode(",",$filtroAux1Valor);
                $nroElemt1      = count($valoresAux1);

                if ($nroElemt1 == 2) {
                    $modificador        = $valoresAux1[0];
                    $filtroAux1Valor    = $valoresAux1[1];
                    switch ($modificador) {
                        case 'D':
                            $filtro1 = " `$filtroAux1Campo` >= '$filtroAux1Valor' ";
                            break;
                        case 'H':
                            $filtro1 = " `$filtroAux1Campo` <= '$filtroAux1Valor' ";
                            break;
                        case 'L':
                            $filtro1 = " `$filtroAux1Campo` LIKE '%$filtroAux1Valor%' ";
                            break;
                        case 'Y':
                            $filtro1 = " YEAR(`$filtroAux1Campo`) = '$filtroAux1Valor' ";
                            break;
                    }

                } else {
                    $filtro1         = " `$filtroAux1Campo` = '$filtroAux1Valor' ";
                }
                if (isset($filtroAux1BD)) {  $filtro1 = "`$filtroAux1BD`.".$filtro1; }
                $confiltro1      = true;
            }
        }

 
        if (isset($_GET['filtroAux2Campo'])) {
            if (trim($_GET['filtroAux2Valor']) != '' ) {
                $filtroAux2Campo = $_GET['filtroAux2Campo'];
                $filtroAux2Valor = $_GET['filtroAux2Valor'];

                $valoresAux2    = explode(",",$filtroAux2Valor);
                $nroElemt2      = count($valoresAux2);

                if ($nroElemt2 == 2) {
                    $modificador        = $valoresAux2[0];
                    $filtroAux2Valor    = $valoresAux2[1];
                    switch ($modificador) {
                        case 'D':
                            $filtro2 = " `$filtroAux2Campo` >= '$filtroAux2Valor' ";
                            break;
                        case 'H':
                            $filtro2 = " `$filtroAux2Campo` <= '$filtroAux2Valor' ";
                            break;
                        case 'L':
                            $filtro2 = " `$filtroAux2Campo` LIKE '%$filtroAux2Valor%' ";
                            break;
                    }

                } else {
                    $filtro2         = " `$filtroAux2Campo` = '$filtroAux2Valor' ";
                }
                if (isset($filtroAux2BD)) {  $filtro2 = "`$filtroAux2BD`.".$filtro2; }
                $confiltro2      = true;
            }
        }

        if (isset($_GET['filtroAux3Campo'])) { 
            if (trim($_GET['filtroAux3Valor']) != '' ) {
                $filtroAux3Campo = $_GET['filtroAux3Campo'];
                $filtroAux3Valor = $_GET['filtroAux3Valor'];
                
                $valoresAux3    = explode(",",$filtroAux3Valor);
                $nroElemt3      = count($valoresAux3);

                if ($nroElemt3 == 2) {
                    $modificador        = $valoresAux3[0];
                    $filtroAux3Valor    = $valoresAux3[1];
                    switch ($modificador) {
                        case 'D':
                            $filtro3 = " `$filtroAux3Campo` >= '$filtroAux3Valor' ";
                            break;
                        case 'H':
                            $filtro3 = " `$filtroAux3Campo` <= '$filtroAux3Valor' ";
                            break;
                        case 'L':
                            $filtro3 = " `$filtroAux3Campo` LIKE '%$filtroAux3Valor%' ";
                            break;
                    }
                } else {
                    $filtro3         = " `$filtroAux3Campo` = '$filtroAux3Valor' ";
                }
                if (isset($filtroAux3BD)) {  $filtro3 = "`$filtroAux3BD`.".$filtro3; }
                $confiltro3      = true;
            }
        }

        if (isset($_GET['filtroAux4Campo'])) { 
            if (trim($_GET['filtroAux4Valor']) != '' ) {
                $filtroAux4Campo = $_GET['filtroAux4Campo'];
                $filtroAux4Valor = $_GET['filtroAux4Valor'];
                
                $valoresAux4    = explode(",",$filtroAux4Valor);
                $nroElemt4      = count($valoresAux4);

                if ($nroElemt4 == 2) {
                    $modificador        = $valoresAux4[0];
                    $filtroAux4Valor    = $valoresAux4[1];
                    switch ($modificador) {
                        case 'D':
                            $filtro4 = " `$filtroAux4Campo` >= '$filtroAux4Valor' ";
                            break;
                        case 'H':
                            $filtro4 = " `$filtroAux4Campo` <= '$filtroAux4Valor' ";
                            break;
                        case 'L':
                            $filtro4 = " `$filtroAux4Campo` LIKE '%$filtroAux4Valor%' ";
                            break;
                    }
                } else {
                    $filtro4         = " `$filtroAux4Campo` = '$filtroAux4Valor' ";
                }
                if (isset($filtroAux4BD)) {  $filtro4 = "`$filtroAux4BD`.".$filtro4; }
                $confiltro4      = true;
            }
        }
        
        if (isset($_GET['filtroAux5Campo'])) { 
            if (trim($_GET['filtroAux5Valor']) != '' ) {
                $filtroAux5Campo = $_GET['filtroAux5Campo'];
                $filtroAux5Valor = $_GET['filtroAux5Valor'];
                
                $valoresAux5    = explode(",",$filtroAux5Valor);
                $nroElemt5      = count($valoresAux5);

                if ($nroElemt5 == 2) {
                    $modificador        = $valoresAux5[0];
                    $filtroAux5Valor    = $valoresAux5[1];
                    switch ($modificador) {
                        case 'D':
                            $filtro5 = " `$filtroAux5Campo` >= '$filtroAux5Valor' ";
                            break;
                        case 'H':
                            $filtro5 = " `$filtroAux5Campo` <= '$filtroAux5Valor' ";
                            break;
                        case 'L':
                            $filtro5 = " `$filtroAux5Campo` LIKE '%$filtroAux5Valor%' ";
                            break;
                    }
                } else {
                    $filtro5         = " `$filtroAux5Campo` = '$filtroAux5Valor' ";
                }
                if (isset($filtroAux5BD)) {  $filtro5 = "`$filtroAux5BD`.".$filtro5; }
                $confiltro5      = true;
            }
        }
	}

    // :: Si el valor es true solo ejecuta consulta si existen criterios de filtrado
    $puedeLeer = true; 
    if ($soloConFiltro ) { 
        if ($confiltro0 OR $confiltro1 OR $confiltro2 OR $confiltro3) { $puedeLeer = true; } else { $puedeLeer = false; } 
    } 
    
    $grillaHTML     = '';
    $paginacionHTML = '';
    $selected       = ""; 

    
    if ($puedeLeer) {
        if ($conConsulta) {
            $consulta = $consultaGrilla;   
        } else {
            $consulta = "SELECT * FROM `$tablaDB` ";     
        }

        $pos1 = strpos($consulta, 'WHERE');
        $pos2 = strpos($consulta, 'Where');
        $pos3 = strpos($consulta, 'where');
   
        if (!$ignorarWhere) {
            if ($pos1 === false AND $pos2 === false AND $pos3 === false) { $tieneWhere = false;  } else { $tieneWhere = true; } 
        } else { 
            $tieneWhere = false;
        }

        // :: Agrega filtros a la consulta
        if ($confiltro0) {
            if ($tieneWhere) { $conector = " AND "; } else { $conector = " WHERE "; $tieneWhere = true; }
            $consulta .= " $conector $filtro0  ";
        }
        if ($confiltro1) {
            if ($tieneWhere) { $conector = " AND "; } else { $conector = " WHERE "; $tieneWhere = true; }
            $consulta .= " $conector $filtro1  ";
        }
        if ($confiltro2) {
            if ($tieneWhere) { $conector = " AND "; } else { $conector = " WHERE "; $tieneWhere = true; }
            $consulta .= " $conector $filtro2  ";
        }
        if ($confiltro3) {
            if ($tieneWhere) { $conector = " AND "; } else { $conector = " WHERE "; $tieneWhere = true; }
            $consulta .= " $conector $filtro3  ";
        }
        if ($confiltro4) {
            if ($tieneWhere) { $conector = " AND "; } else { $conector = " WHERE "; $tieneWhere = true; }
            $consulta .= " $conector $filtro4  ";
        }
        if ($confiltro5) {
            if ($tieneWhere) { $conector = " AND "; } else { $conector = " WHERE "; $tieneWhere = true; }
            $consulta .= " $conector $filtro5  ";
        }   

        // :: Agrega Orden
        if(isset($orderBy)) { $consulta .= " ORDER BY  $orderBy"; } 
    
        // :: Paginación
		if ($conPaginacion) {
            include(PATH_INCLUDES.'grillaPaginacion.php');
            $contador = 1;
            $consulta .= " LIMIT $RegInicial,".PAGINACION;
        } 
         
        if ($conLimite) {
            $consulta .= " LIMIT 1,".REGSINPAGINACION;
        }
        
        if ($verConsulta) { echo "Qry Lim: <pre>$consulta</pre>"; }
 
        $salida    = $conexionDB->consulta($consulta);
           
       
        // :::::::::::::::::::::::::::::::::::::: Titulos Grilla  :::::::::::::::::::::::::::::::::::: //
        $grillaFilTitHTML  = ""; $grillaFilasHTML = "";
         
        $columnasTit       = "padding-top:10; row-gap:1px; column-gap:1px; grid-template-columns: ";
        $columnasFilas     = "padding-top:0;  row-gap:1px; column-gap:1px; grid-template-columns: ";

        foreach ($arrayGrilla as $linea => $valoresLin) {
            if ($linea > 0) {
                $titulo    = $valoresLin['titulo'];
                $ancho     = $valoresLin['ancho'];
                $columnasTit   .= $ancho."fr ";
                $columnasFilas .= $ancho."fr ";
                $grillaFilTitHTML .= "<div class='celdaGrilla--peq  fondoTitulos' style='$bgColor'>$titulo</div>";
            }
        }  

        // :: acciones 1
        if ($acciones) {
            if ($conBotAux1 OR $conBotAux2 OR $conBotAux3  OR $conBotAux4 OR $conEdit OR $conBorrar) {
                $columnasTit   .= " $anchoAcciones";
                $columnasFilas .= " $anchoAcciones";
                $grillaFilTitHTML .= "<div class='celdaGrilla--peq fondoTitulos' >$titAcciones</div>";
            }
        }

        // :: acciones 2
        if ($acciones2) {
            if ($conBotAux21 OR $conBotAux22 OR $conBotAux23 ) {
                $columnasTit   .= " $anchoAcciones2";
                $columnasFilas .= " $anchoAcciones2";
                $grillaFilTitHTML .= "<div class='celdaGrilla--peq fondoTitulos' >$titAcciones2</div>";
            }
        }

        $grillaHTMLTit    = "<div class='tablero--col' style='$columnasFilas; '>$grillaFilTitHTML</div>";
    
        // :::::::::::::::::::::::::::::::::::::: Datos Grilla  :::::::::::::::::::::::::::::::::::: //
        $borrar           = $dg_txt->GetDefinition('borrar');
        $grillaHTML       = "";

        $contador         = 1; 
    	while ($rowGrilla = mysqli_fetch_array($salida)) { 
            if (($contador % 2) == 1) { $clase='filaImpar'; } else { $clase='filaPar'; }
                
            $idReg  	= $rowGrilla[$IdCampo];
    		$js     	= "'$moduloPHP','$idReg'";
            if (isset($parametroEdit)) { 
                if (trim($parametroEdit) != '') {
                    $parametros 	= explode(",",$parametroEdit);
                    foreach($parametros as $ind => $campo)  { 
                        if (isset($rowGrilla[$campo])) { 
                            $campoAux  = trim($rowGrilla[$campo]); 
                            $js       .= ",'$campoAux'"; 
                        }
                    } 
                } // echo " $js -- ";
			}	
           
			
			$jsAux1     = '\''.$idReg.'\'';
			if (isset($parametroAux1)) {  
                if (trim($parametroAux1) != '') { 
                    $parametros1 	= explode(",",$parametroAux1);
                    foreach($parametros1 as $ind => $campoAux1)  { 
                        if (isset($rowGrilla[$campoAux1])) {
                            $datoAux1	= trim($rowGrilla[$campoAux1]); 
                            $jsAux1    .= ',\''.$datoAux1.'\'';
                        }
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
                        if (isset($rowGrilla[$campoAux2])) {
                            $datoAux2	= trim($rowGrilla[$campoAux2]); 
                            $jsAux2   .= ',\''.$datoAux2.'\'';
                        }
                    } 
                }
                if (trim($parEstaticos2) != '') { 
                    $estaticos2 	= explode(",",$parEstaticos2);
                    foreach($estaticos2 as $ind => $campoAux2)  { 
                        $datoAux2	= trim($campoAux2); 
                        $jsAux2    .= ',\''.$datoAux2.'\'';
                    } 
                }
			}	
			
			$jsAux3     = '\''.$idReg.'\'';
			if (isset($parametroAux3)) {  
                if (trim($parametroAux3) != '') { 
                    $parametros3 	= explode(",",$parametroAux3);
                    foreach($parametros3 as $ind => $campoAux3)  { 
                        if (isset($rowGrilla[$campoAux3])) {
                            $datoAux3	= trim($rowGrilla[$campoAux3]); 
                            $jsAux3   .= ',\''.$datoAux3.'\'';
                        }
                    } 
                }
                if (trim($parEstaticos3) != '') { 
                    $estaticos3 	= explode(",",$parEstaticos3);
                    foreach($estaticos3 as $ind => $campoAux3)  { 
                        $datoAux3	= trim($campoAux3); 
                        $jsAux3    .= ',\''.$datoAux3.'\'';
                    } 
                }
			}	
			
			$jsAux4     = '\''.$idReg.'\'';
			if (isset($parametroAux4)) {  
                if (trim($parametroAux4) != '') { 
                    $parametros4 	= explode(",",$parametroAux4);
                    foreach($parametros4 as $ind => $campoAux4)  { 
                        $datoAux4	= trim($rowGrilla[$campoAux4]); 
                        $jsAux4   .= ',\''.$datoAux4.'\'';
                    } 
                }
                if (trim($parEstaticos4) != '') { 
                    $estaticos4 	= explode(",",$parEstaticos4);
                    foreach($estaticos4 as $ind => $campoAux4)  { 
                        $datoAux4	= trim($campoAux4); 
                        $jsAux4    .= ',\''.$datoAux4.'\'';
                    } 
                }
			}	
			
            $jsAux5     = '\''.$idReg.'\'';
            if (isset($parametroAux5)) {  
                if (trim($parametroAux5) != '') { 
                    $parametros5 	= explode(",",$parametroAux5);
                    foreach($parametros5 as $ind => $campoAux5)  { 
                        $datoAux5	= trim($rowGrilla[$campoAux5]); 
                        $jsAux5    .= ',\''.$datoAux5.'\'';
                    } 
                }
                if (trim($parEstaticos5) != '') { 
                    $estaticos5 	= explode(",",$parEstaticos5);
                    foreach($estaticos5 as $ind => $campoAux5)  { 
                        $datoAux5	= trim($campoAux5); 
                        $jsAux5    .= ',\''.$datoAux5.'\'';
                    } 
                }
            }	

         
            // :: auxiliares 2
            $jsAux21     = '\''.$idReg.'\'';
            if (isset($parametroAux21)) {  
                if (trim($parametroAux21) != '') { 
                    $parametros21 	= explode(",",$parametroAux21);
                    foreach($parametros21 as $ind => $campoAux21)  { 
                        $datoAux21	= trim($rowGrilla[$campoAux21]); 
                        $jsAux21   .= ',\''.$datoAux21.'\'';
                    } 
                }
                if (trim($parEstaticos21) != '') { 
                    $estaticos21 	= explode(",",$parEstaticos21);
                    foreach($estaticos21 as $ind => $campoAux21)  { 
                        $datoAux21	= trim($campoAux21); 
                        $jsAux21    .= ',\''.$datoAux21.'\'';
                    } 
                }
            }	
            
            $jsAux22     = '\''.$idReg.'\'';
            if (isset($parametroAux22)) {  
                if (trim($parametroAux22) != '') { 
                    $parametros22 	= explode(",",$parametroAux22);
                    foreach($parametros22 as $ind => $campoAux22)  { 
                        $datoAux22	= trim($rowGrilla[$campoAux22]); 
                        $jsAux22   .= ',\''.$datoAux22.'\'';
                    } 
                }
            }	
            
            $jsAux23     = '\''.$idReg.'\'';
            if (isset($parametroAux23)) {  
                if (trim($parametroAux23) != '') { 
                    $parametros23 	= explode(",",$parametroAux23);
                    foreach($parametros23 as $ind => $campoAux23)  { 
                        $datoAux23	= trim($rowGrilla[$campoAux23]); 
                        $jsAux23   .= ',\''.$datoAux23.'\'';
                    } 
                }
            }	
            
            $jsAux24     = '\''.$idReg.'\'';
            if (isset($parametroAux24)) {  
                if (trim($parametroAux24) != '') { 
                    $parametros24 	= explode(",",$parametroAux24);
                    foreach($parametros24 as $ind => $campoAux24)  { 
                        $datoAux24	= trim($rowGrilla[$campoAux24]); 
                        $jsAux24   .= ',\''.$datoAux24.'\'';
                    } 
                }
            }	
            
            $colorFil        = "";
            $grillaFilasHTML = ""; 
            foreach ($arrayGrilla as $linea => $valoresLin) { 
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

                    $grillaFilasHTML .= "<div class='celdaGrilla  $claseCeldaiPec  $clase' style='$bg_fila; border-bottom:1px solid white; $justify' >$dato</div>"; 
                    if ($colorCelda) { $bg_fila = $backgroundAux; }
                } else {
                    $colorFila = trim($rowGrilla['colorFila']);  
                    if (trim($colorFila) == "") { $bg_fila = ""; } else { $bg_fila = " background-color:$colorFila; border-bottom:1px solid white;"; }
                }
            }

            if ($acciones) {
                if ($conEdit OR $conBotAux1 OR $conBotAux2 OR $conBotAux3 OR $conBotAux4 OR $conBorrar) {
                    $grillaFilasHTML .= "<div class='cursorPoint celdaGrilla $claseCeldaiPec centrado $clase' style='$bg_fila; border-bottom:1px solid white; paddinng-top:0; display: block;'>";
                    if ($conEdit)    { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionEdit($js)\"     src='/btns/$iconEdit'  height='$tamaIco' title='$titEdit' > "; }
                    if ($conBorrar)  { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionDel($js)\"      src='/btns/$iconDel'   height='$tamaIco' title='$titBorrar'> "; }
                    if ($conBotAux1) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux1($jsAux1)\" src='/btns/$iconAux1'  height='$tamaIco' title='$titIcon1'> "; }
                    if ($conBotAux2) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux2($jsAux2)\" src='/btns/$iconAux2'  height='$tamaIco' title='$titIcon2'> "; }
                    if ($conBotAux3) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux3($jsAux3)\" src='/btns/$iconAux3'  height='$tamaIco' title='$titIcon3'> "; }
                    if ($conBotAux4) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux4($jsAux4)\" src='/btns/$iconAux4'  height='$tamaIco' title='$titIcon4'> "; }
                    if ($conBotAux5) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux5($jsAux5)\" src='/btns/$iconAux5'  height='$tamaIco' title='$titIcon5'> "; }
                    $grillaFilasHTML .= "</div>";  
                }
            } 

            if ($acciones2) {
                $grillaFilasHTML .= "<div class='cursorPoint celdaGrilla $claseCeldaiPec centrado $clase' style='$bg_fila'>";
                if ($conBotAux21) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux21($jsAux21)\" src='/btns/$iconAux21'  height='$tamaIco' title='$titIcon21'> "; }
                if ($conBotAux22) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux22($jsAux22)\" src='/btns/$iconAux22'  height='$tamaIco' title='$titIcon22'> "; }
                if ($conBotAux23) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux23($jsAux23)\" src='/btns/$iconAux23'  height='$tamaIco' title='$titIcon23'> "; }
                if ($conBotAux24) { $grillaFilasHTML .= "<img class='celdaGrilla--img mano' onclick=\"$funcionAux24($jsAux24)\" src='/btns/$iconAux24'  height='$tamaIco' title='$titIcon24'> "; }
                $grillaFilasHTML .= "</div>";  
            }

            $grillaHTML .= "<div class='tablero--col' style=' $columnasFilas; min-height: 30px; '>$grillaFilasHTML</div>";
            $contador++;
        }
    }

    $nroFilas = $salida->num_rows;
    mysqli_free_result($salida);
    unset($backgroundColor);
    unset($conFiltro);
  ?>