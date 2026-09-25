<?php
	if (isset($_SESSION['idModuloIco']) || isset($_GET['mod'])) {
		$idModuloIco = $_SESSION['idModuloIco'] ?? $_GET['mod'];
		// echo "<br><br><br>idModuloIco: $idModuloIco ";
	} 

	$HTTP_HOST = RUTA_ABSOLUTA;

    $DB_CLIENTE = DB_CLIENTE;
    $DB_ADMIN   = DB_ADMIN;

	$conexionDB = new DB_MySQLi; 
	$conexionDB->conectar(DB_ADMIN, DB_SERVER, DB_USER, DB_PASSWD );

	if (isset($_GET['grp'])) { $seleccionado = $_GET['grp']; } else { $seleccionado = ""; } 
	if (isset($_GET['mod'])) { $modSeleccionado = $_GET['mod']; } else { $modSeleccionado = ""; } 

	$headerMenu = "";
    $fila_Menu  = "";

	$arrayTipoMenuBD = array();

	if (!isset($historico)) { $historico = false; }
	if ($historico) { $AND_HIS = "AND `historico` = 'S' "; } else { $AND_HIS = ""; }
	if (!isset($AND)) { $AND = ""; }	
	
	$estoyenmenu = true;
	$primero  	 = true;

    $usuarioId   = $_SESSION['idUser'];
    $tipoUser    = $_SESSION['tipo'];
	$usrAdmin    = $_SESSION['usrAdmin'];
	$SISTEMA_ID  = $_SESSION['idsistema'];

	$consulta 	= "SELECT * FROM `$DB_ADMIN`.`adm_sistemas` WHERE id = $SISTEMA_ID "; 
	$salida 	= $conexionDB->consulta($consulta);
	$row 	  	= mysqli_fetch_array($salida);
	mysqli_free_result($salida);

	// echo "<br>$consulta ";
	$nsistema  	  = $row["nombre"];
    $iconosist    = $row["icono"];
	$area         = $row["area"];
	$rutaSistema  = $row["ruta"].'/';	
	
	$nsistema  	= $row["nombre"];
    $iconosist  = '/'.$row["icono"];

    $_SESSION['iconosis'] = $iconosist;
	$_SESSION['nsistema'] = $nsistema;
	
	switch ($area) {
		case 'C': $raizSistemas = 'appContabilidad/'; break;
		case 'M': $raizSistemas = 'appMuni/'; break;
		case 'S': $raizSistemas = 'appSalud/';break;
		case 'E': $raizSistemas = 'appEduc/';break;    
		case 'A': $raizSistemas = ''; break;   
		default:  $raizSistemas = ''; break; 
	}


	// ::: LEE MODULOS 
	if ($usrAdmin == 'D') {
		$consulta = "SELECT * 	FROM   `$DB_ADMIN`.`adm_modulos`, `$DB_ADMIN`.adm_estrucMenu
								WHERE  `idsistema` = $SISTEMA_ID 
								AND     adm_modulos.tipo = adm_estrucMenu.tipoGrupo
								AND     estado = 1
                                AND     adm_estrucMenu.tipoMenu   = 'B'
								ORDER BY `tipo`,`ordenICO` ";  
		} else {
		// Nueva consulta lee solo los modulos con autorización
		$consulta = "SELECT *   FROM    `$DB_ADMIN`.adm_modulos, `$DB_ADMIN`.adm_estrucMenu, `$DB_CLIENTE`.adm_accesos
								WHERE   adm_modulos.idsistema = $SISTEMA_ID 
								AND     adm_modulos.tipo = adm_estrucMenu.tipoGrupo
								AND     adm_accesos.idmodulo     = adm_modulos.id
								AND     adm_accesos.iduser       = '$usuarioId'
								AND     adm_accesos.acceso       = 'S'
								AND     (adm_modulos.estado       =  1 or adm_modulos.estado = 9)
                                AND     adm_estrucMenu.tipoMenu  = 'B'
								$AND
								$AND_HIS
								ORDER BY `tipo`,`ordenICO` ";
	}

    $tipoGrupoAux  = "";
    $fila_Menu     = "";
	$fila_subMenu  = "";
	$primero       = false;
    $divSubMenus   = "";    
	$gruposMenu    = "";

	$cont = 1;

    $salida 	= $conexionDB->consulta($consulta);
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
		$txtIco		= $row['txtIco'];
		$icon	    = $row['icon'];

		$estado	 	= $row['estado'];


		switch ($target) {
			case 'B':
				$_target = "_blank";
				break;
			case 'T':
				$_target = "_top";
                break;
			default:
				$_target = "_top";
				break;
		}

		if (trim($get) != '') { $get = "&".$get; }

        $claseActivo = ((int) $idModuloIco === (int) $idmodulo) ? ' menuSuperior__enlace--activo' : '';
        $enlace = "$HTTP_HOST$raizSistemas$rutaSistema$raiz$php?grp=$tipoGrupo&mod=$idmodulo$get";
		
		switch ($estado) {
			case '1':
				// Si es el primer modulo del grupo, crea la fila de grupo
				$fila_Menu .=  "<li class='fila1__pestana mano menuSuperior__item'>
									<a class='menuSuperior__enlace$claseActivo' href='$enlace' target='$_target'>
										<img class='celdaGrilla--img mano menuSuperior__icono' src='/btns/$icon' height='45'>
										<span class='peque centrado menuSuperior__texto'>$txtIco</span>
									</a>
								</li>";
								break;
			case '9':
				// Si es el primer modulo del grupo, crea la fila de grupo
				$fila_Menu .=  "<li class='fila1__pestana mano menuSuperior__item'>
									<button type='button' onclick='mostrarMantenimiento()' class='menuSuperior__enlace'>
										<img class='celdaGrilla--img mano menuSuperior__icono' src='/btns/$icon' height='45'>
										<span class='peque centrado menuSuperior__texto'>$txtIco</span>
									</button>
								</li>";
				break;
			default:  $tipoGrupoAux = ''; break;
		}	
	}

    // ::
	$estiloListaMenu = '';
	if ((int) $SISTEMA_ID === 185) {
		// OMIL: cada módulo ocupa una columna fija, independiente del ancho del texto activo.
		$estiloListaMenu = "display:grid; grid-auto-flow:column; grid-auto-columns:60px; gap:0; margin:0; padding:0; list-style:none; align-items:start;";
	}

	$headerMenu =  "<div class='menuSuperior'>
						<ul class='listaPlana menuSuperior__lista' style='$estiloListaMenu'>
							$fila_Menu
						</ul>
					</div>"; 
?>
