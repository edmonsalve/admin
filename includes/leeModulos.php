<?php
    $DB_CLIENTE = DB_CLIENTE;
	$arrayTipoMenuBD = array();
	
	if (!isset($historico)) { $historico = FALSE; }
	if ($historico) { $WHERE = "WHERE `historico` = 'S' "; } else { $WHERE = ""; }
	
	$estoyenmenu = TRUE;
	$primero  	 = TRUE;

    $usuarioId  = $_SESSION['idUser'];
    $tipoUser   = $_SESSION['tipo'];
	$movil 		= 0; // $_SESSION['movil'];
	
	$consulta 	= "SELECT * FROM `adm_sistemas` WHERE id = '".SISTEMA_ID."' ";
	$salida 	= $conexionReD->consulta($consulta);
	$row 	  	= mysqli_fetch_array($salida);
	mysqli_free_result($salida);
	
	$nsistema  	= $row["nombre"];
    $iconosist  = '/'.$row["icono"];

    $_SESSION['iconosis'] = $iconosist;

	$menusistemaHTML 	  = '<div class="span-20 last">';
	$menusistemaHTML_IZQ .= '<div class="span-9">'; 
	$menusistemaHTML_DER .= '<div class="span-9 last">'; 
	
	if($movil == 1) {
        $AND     = 'AND `movil` = 1 ';
        $style   = 'style = "width:100%; height:31px; padding-top:10px; text-align:center; background:url(/images/fondoBtn.png) repeat-x;"';
        $class   = '';
        } else {
        $AND     = '';
        $style   = 'style = "float:none; "';
        $class   = 'class = "span-8 last sombra"';
    }

	/* ======================================================= LEE MODULOS  =============================================== */
	if ($tipoUser < 7) {
		// Nueva consulta lee solo los modulos con autorización
		$consulta = "SELECT *   FROM    adm_modulos, adm_estrucMenu, `$DB_CLIENTE`.adm_accesos
								WHERE   adm_modulos.idsistema = '".SISTEMA_ID."'
								AND     adm_modulos.tipo = adm_estrucMenu.tipoGrupo
								AND     adm_accesos.idmodulo  = adm_modulos.id
								AND     adm_accesos.iduser    = '$usuarioId'
								AND     adm_accesos.acceso = 'S'
								AND     adm_modulos.estado =  1
								$AND
								ORDER BY `columna`,`ordenColumna`,`modulo`";
		} else {
		$consulta = "SELECT * 	FROM `adm_modulos`, adm_estrucMenu
								WHERE `idsistema`  = '".SISTEMA_ID."'
								AND     adm_modulos.tipo = adm_estrucMenu.tipoGrupo
								AND    estado = 1
								$AND
								ORDER BY `columna`,`ordenColumna`,`modulo`";
	}

	$titSeccionMenu = ''; 

    $salida 	= $conexionReD->consulta($consulta);
	while ($row = mysqli_fetch_array($salida)) { 
	    $idmodulo   = $row['id'];  
		$tipoGrupo 	= $row['tipoGrupo'];

		$His 		= $row['historico'];
		$Dir 		= $row['direcGrupo'];
		$titGrupo 	= $row['tituloGrupo'];
		$colGrupo   = $row['columna'];
		$raiz       = $row['direcGrupo'];

		$ind 		= $row['id'];
		$modulo	  	= $row['modulo'];
		$tipo		= $row['tipo'];
		$php	    = $row['php'];
		$get        = $row['parametros'];
		$target	    = $row['target'];
		
		$enlace = "";
		if ($php != '#') {
			if ($get != '') { $_para = '&'.$get;} else { $_para  = ''; } // echo " $_para  ** ";
			$enlace = $raiz.$php.'?mod='.$idmodulo.$_para;
			} else {
			$enlace = '';
		}

		if ($target == 'T') { $target = ''; } else { $target = ' target="_blank" '; }
		
		if ($titSeccionMenu != $titGrupo) {
			switch($colGrupo) {
				case 'I': 	if (!$primero) { $menusistemaHTML_IZQ .= '</ol>';  }
							$menusistemaHTML_IZQ .= "<div class='enc_mensajes prepend-top' style='padding:2px;'><h3>$titGrupo</h3></div>"; 
							$menusistemaHTML_IZQ .= "<ol>";
							break;
				case 'D': 	if (!$primero) { $menusistemaHTML_DER .= '</ol>';  }
							$menusistemaHTML_DER .= "<div class='enc_mensajes prepend-top' style='padding:2px;'><h3>$titGrupo</h3></div>"; 
							$menusistemaHTML_DER .= "<ol>";
							break;
			}
			$titSeccionMenu = $titGrupo;
			$aux++;
		}
		
		switch($colGrupo) {
			case 'I':	$menusistemaHTML_IZQ .= "<div $class $style >";	 	
						$menusistemaHTML_IZQ .= "<a href='$enlace' class='menuSistema' $target ><li>$modulo</li></a>";
						$menusistemaHTML_IZQ .= '</div>';
						break;
			case 'D':	$menusistemaHTML_DER .= "<div $class $style >";	 	
						$menusistemaHTML_DER .= "<a href='$enlace' class='menuSistema' $target ><li>$modulo</li></a>";
						$menusistemaHTML_DER .= '</div>';
						break;
		}

		if ($primero) { $primero = FALSE; }
	}
	
	$menusistemaHTML .= "$menusistemaHTML_IZQ</div>" . "$menusistemaHTML_DER</div>";
	$menusistemaHTML .= '</div>';
?>
