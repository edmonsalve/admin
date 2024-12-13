<?php
    /*
        :::::::::::::::::::::::::::::::::: VARIABLES UTILIZADAS  ::::::::::::::::::::::::::::::::::
        $phpFile: nombre del programa.php que se esta ejecutando 
        
        $pre  : Valor constante que pe agrega delante del valor del campo
        $post : Valor constante que pe agrega al final del valor del campo
        $utf8 : TRUE o FALSE
    */
    
    /* :::::::::::::::::::::::::::::::::: Lee parametros $_GET para Busqueda :::::::::::::::::::::::::::::::::: */
	$whereGET     = '';
	$criterio     = '';
	$centroId     = 0;

    if(!isset($DESC)) { $DESC = ''; }

	if (isset($_GET['buscarpor'])) {
		$criterio  	= trim($_GET['iguala']);
		$buscarpor 	= $_GET['buscarpor'];

		if (trim($criterio) != '') { $whereGET = " `$buscarpor`  like  '$criterio%' "; }

		if (trim($whereGET) != '') { $whereGET = "WHERE ".$whereGET;  }
	}


    $tablaDBLee = "`$tablaDB`";

    /* Cuando las tablas tienen un campo relacionado en tabla madre */
    /* Ejemplo: Familia - SebFamilia                                */

    if (isset($tablaDBRel) and isset($relacion)) {

        if (trim($tablaDBRel) != '' and  trim($relacion) != '' ) {

            $tablaDBLee .= ", `$tablaDBRel`";
            if (trim($criterio) == '') { $where = "WHERE $relacion"; } else { $where = "AND $relacion"; }
        }
    }

    if (!isset($where))  { $where = ''; }
	/* :::::::::::::::::::::::::::::::::::::::::::: Lee Tabla :::::::::::::::::::::::::::::::::::::::: */
	$contador = 1;
	$consulta = "SELECT *   FROM $tablaDBLee
                            $whereGET
                            $where
							ORDER BY $orderBy $DESC";

	$salida   = $conexionDB->consulta($consulta);

	$borrar = $dg_txt->GetDefinition('borrar');
	$tablaHTML  = '';

	/* :::::::::::::::::::::::::::::::::::::::::: Titulos Grilla :::::::::::::::::::::::::::::::::::::  */
	foreach ($titulos as $ind => $val) {
		$titulo = $val['tit'];
		$clase  = $val['ancho'];
		$tablaHTML .= "<div class='$clase' ><h3 style='font-size:10px;'>$titulo</h3></div>";
	}

    if (!isset($conBorrar)) { $conBorrar = false; }
    if ($conBorrar) { $anchoDiv = 8; } else { $anchoDiv = 9; }
    
	/* :::::::::::::::::::::::::::::::::::::::::: Datos Grilla :::::::::::::::::::::::::::::::::::::  */
	$tablaHTML .= '<div class="span-10" style="float:none; height:435px; overflow:auto; ">';
	while ($row = mysqli_fetch_array($salida)) {
		if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; }
		$contador++;

		$id = $row[$IdCampo];
		$jsdel   = "'$phpFile',".$id;
		$jsedit  = "'$phpFile.php',".$id;

		$tablaHTML .= '<div class="span-9" >';
			$tablaHTML .= "<div class='span-$anchoDiv cursorPoint $clase' onclick=editarTabla($jsedit) >";
				foreach ($fila as $ind => $val) {
					$campo = $val['campo'];
					$clase = $val['ancho'];

                    if (isset( $val['pre']))   { $pre   = $val['pre'];  } else { $pre  = ""; }
                    if (isset( $val['$post'])) { $post  = $val['post']; } else { $post = ""; }
				    if (isset( $val['utf8']))  { $utf8  = $val['utf8']; } else { $utf8 = FALSE; }

					$valorRow = $row[$campo];
					if (substr($campo,0,5) == 'fecha') { $valorRow = substr($row[$campo],8,2).'-'.substr($row[$campo],5,2).'-'.substr($row[$campo],0,4); }

                    if(trim($pre) != '')  { $valorRow  = $pre.$valorRow; }
                    if(trim($post) != '') { $valorRow = $valorRow.$post; }

                    if ($utf8) { $valorRow  = utf8_decode($valorRow);  }

                    $tablaHTML .= "<div class='$clase' >$valorRow</div>";
				}
			$tablaHTML .= '</div>';
            
            
            if ($conBorrar) {
                $tablaHTML .= '<div class="span-1 last cursorPoint fila_impar"  ><input type="button" class="enviarpeq" onclick="borrarReg('.$jsdel.')" value="'.$borrar.'" style="width:50px; "/></div>';
            }
        $tablaHTML .= '</div>';
	}
	$tablaHTML .= '</div>';


?>