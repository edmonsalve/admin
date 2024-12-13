<?php
    /*
    *   PARAMETROS PARA GRILLA DE BUSQUEDA
    *   ----------------------------------
    *
    *   "date"      => TRUE/FALSE     Formatea fecha
    *   "numform"   => TRUE/FALSE     Formatea numeros
    *   "date"      => TRUE/FALSE     Formatea fecha
    *   "substr"    => 18             Largo de la cadena antes de cortar
    *   "alin"      => "L"            Alineación L - R - C
    */

	if (!isset($conBorrar)) {  $conBorrar = false; }
	
    $conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD );

    /* :::::::::::::::::::::::::::::::: Lee parametros $_GET para Busqueda ::::::::::::::::::::::::::::::: */
	$where		  = '';
	$criterio     = '';
    $selected     = "";

	if (isset($_GET['buscarpor'])) {
		$criterio  	= trim($_GET['iguala']);
		$buscarpor 	= $_GET['buscarpor'];

		if (trim($criterio) != '') { $where = " `$buscarpor`  like  '$criterio%' "; }

        if (trim($cuando) != '') {
            if (trim($where) != '') { $where .= "AND $cuando"; } else { $where .= $cuando; }
        }

		if (trim($where) != '') { $where = "WHERE $where";  }
	} else {
        if (trim($cuando) != '') {
            $where .= "WHERE ".$cuando;
        }
	}

    $consulta .= $where;


    /* :::::::::::::::::::::::::::::::: Option Criterios de Busqueda ::::::::::::::::::::::::::::::: */
    $htmlCriterios = "";
    foreach($arrayCriterios as $orden => $Criterio) {
        $campo    = $Criterio['campo'];
        $campoTxt = $Criterio['descripcion'];

        if (isset($buscarpor)) {
           if ($buscarpor == $campo) { $selected = " selected='selected' "; } else { $selected = ""; }
        }
        $htmlCriterios .= "<option value='$campo' $selected>$campoTxt</option>";
    }


	/* :::::::::::::::::::::::::::::::::::::: Determina Paginación  ::::::::::::::::::::::::::::::::::::
	$PagTabla    = 'pcir_propietarios';
	$moduloPHP   = 'propietarios';
	include(PATH_INCLUDES.'paginacion.php');

    LIMIT $RegInicial," .PAGINACION
	*/
	$paginacionHTML = "";

    $lineaTitulos = "<div class='span-16 enc_mensajes last' >";
    /* ::::::::::::::::::::::::::::::::: Encabezado Grilla ::::::::::::::::::::::::::::::::::::: */
     foreach($columna as $nroCol => $datosCol) {
        $titColum = $datosCol['descripcion'];
        $span     = $datosCol['span'];

        $lineaTitulos .= "<div class='span-$span enc_mensajes' ><h3>$titColum</h3></div>";
     }
     $lineaTitulos .= "</div>";

    /* :::::::::::::::::::::::::::::::::::::::: Lee Grilla ::::::::::::::::::::::::::::::::::::: */
	$contador = 1;

	$salida    = $conexionDB->consulta($consulta);

	$borrar    = $sistema_txt->GetDefinition('borrar');
	$ver       = $sistema_txt->GetDefinition('ficha');

	$grillaHTML  = "$lineaTitulos"; 
	while ($row = mysqli_fetch_array($salida)) {
		if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; }
		$contador++;

        $nameFicha = str_replace(".php","",$name)."_ficha.php";
        //"Ficha.php";

        $idReg = $row[$pasarId];
        $js    = "\"$nameFicha\",$idReg";
        $jsDel = "\"$name\",$idReg";

        $lineaGrilla = "";
        foreach($columna as $nroCol => $datosCol) {
            $campo = $datosCol['campo'];
            $span  = $datosCol['span'];

            $dato  = $row[$campo];


            if (isset($datosCol['date']))    { $datefor = $datosCol['date'];   } else { $datefor = FALSE; }
            if (isset($datosCol['utf8']))    { $utf8    = $datosCol['utf8'];   } else { $utf8    = FALSE; }
            if (isset($datosCol['numform'])) { $numfor  = $datosCol['numform'];} else { $numfor  = FALSE; }
            if (isset($datosCol['substr']))  { $substr  = TRUE;                } else { $substr  = FALSE; }
            if (isset($datosCol['alin']))    { $alin     = $datosCol['alin'];  } else { $alin    = FALSE; }


            if ($substr) {
                $largoStr = $datosCol['substr'];
                $dato     = substr($dato,0,$largoStr);
            }

            if ($datefor) { $dato = toFecDMA($dato); }
            if ($numfor)  { $dato = number_format($dato,0,',','.'); }
            if ($utf8)    { $dato = utf8_decode($dato); }

            if (trim($dato) == '') { $dato = ' '; }

            if ($utf8)    { $dato = utf8_decode($dato); }

            $alineacion = '';

            switch ($alin) {
                case 'L': $alineacion = 'IZQUIERDA'; break;
                case 'C': $alineacion = 'CENTRO';    break;
                case 'R': $alineacion = 'DERECHA';   break;
            }

            $lineaGrilla .= "<div class='span-$span $alineacion'>$dato</div>";
        }

		$grillaHTML .= "
		<div class='span-20'>
			<div onclick='' class='span-16 cursorPoint $clase'>
				$lineaGrilla
			</div>
            <div class='span-2'><input onclick='verfichaGr($js)' class='enviarpeq' type='button' value='ver' /></div>";
		
		
		if ($conBorrar) { $grillaHTML .= "<div class='span-2 last'><input onclick='eliminarGr($jsDel)' class='enviarpeq' type='button' value='borrar' /></div>"; }
		
        $grillaHTML .= "</div>";   
	}
?>