<?php
    // $keynumber    = $_SESSION['keynumber'];
    $VERSION_SIS  = VERSION_SIS;
	$nombreUSR	  = $_SESSION['nomUsuario'];
	
	$_user  	  = $_SESSION['idUser'];
	$_usrAdmin    = $_SESSION['usrAdmin'];
	$_prefijo     = $_SESSION['prefijo']; 

	$idsistema    = $_SESSION['idsistema'];

	$LINK_SOPORTE = LINK_SOPORTE;
	$LOGO_CLIENTE = LOGO_CLIENTE;	
	$DB_CLIENTE   = DB_CLIENTE;

	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_ADMIN, DB_SERVER, DB_USER, DB_PASSWD );

	$consulta 	= "SELECT * FROM `adm_sistemas` WHERE id = $idsistema ";  
	$salida 	= $conexionDB->consulta($consulta);
	$row 	  	= mysqli_fetch_array($salida);
    mysqli_free_result($salida);

	$nsistema  	  = $row["nombre"];
	$nsistemaHtml = htmlspecialchars($nsistema, ENT_QUOTES, 'UTF-8');
    $btn          = $row["btn"];
	$area         = $row["area"];
	$rutaSistema  = $row["ruta"].'/';	

	switch ($area) {
		case 'M': $raizSistemas = 'appMuni/'; break;
		case 'S': $raizSistemas = 'appSalud/';break;
		case 'E': $raizSistemas = 'appEduc/';break;    
		case 'A': $raizSistemas = ''; break;   
		default:  $raizSistemas = ''; break; 
	}

	$consultaMod = "SELECT e.tipoMenu, COUNT(*) AS nroModulos
							FROM	adm_estrucMenu e, adm_modulos m, `$DB_CLIENTE`.adm_accesos a
							WHERE 	m.idsistema = $idsistema
							AND   	m.idModulo 	= a.idmodulo
							AND		m.idsistema = $idsistema
							AND		m.tipo 		= e.tipoGrupo
							AND   	m.estado    = 1
							AND   	a.iduser    = '$_user'
							AND   	a.acceso    = 'S'
							GROUP BY e.tipoMenu";
	
	$salida 	= $conexionDB->consulta($consultaMod);

	while ($row = mysqli_fetch_array($salida)) {
		$modulos[$row['tipoMenu']] = $row['nroModulos'];
	}

	$index_main = "/$raizSistemas$rutaSistema"."index_main.php";  // ! en desuso  href='/$index_main' linea 60
	// ? echo "<br><br><br><br>$index_main";

	$barraLateral  = "<nav class='barraLateral' aria-label='Navegación del sistema'>";
    // :::::::::::::::::::::::::::::::::::::  Barra superior ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	if (trim($btn) != "") {
		$barraLateral .= "
			<div>
				<a class='barraLateral__enlace' href='$index_main' title='inicio'>
					<img class='barraLateral__iconos barraLateral__iconos--modulo' src='/btns/$btn'>
				</a>
			</div>";
	}

	if (trim($idsistema) != 900) { 
		if (isset($modulos['I']) or $_usrAdmin == 'D') {
			$barraLateral .= "
				<div>
					<a class='barraLateral__enlace' href='/appUtilidades/modulos/appModulosInf.php?idsistema=$idsistema&idTipo=I' title='Informes'>
						<img class='barraLateral__iconos' src='/btns/btn_informes.png' />
					</a>
				</div>";

		}
		if (isset($modulos['M']) or $_usrAdmin == 'D') {
			$barraLateral .= "
			<div>
				<a class='barraLateral__enlace' href='/appUtilidades/modulos/appModulosLateral.php?idsistema=$idsistema&idTipo=M' title='Movimientos'>
					<img class='barraLateral__iconos' src='/btns/btn_update.png' />
				</a>
			</div>";
		}
		}
		if (isset($modulos['E']) or $_usrAdmin == 'D') {
			$barraLateral .= "
			<div>
				<a class='barraLateral__enlace' href='/appUtilidades/modulos/appModulosLateral.php?idsistema=$idsistema&idTipo=E' title='Exportar'>
					<img class='barraLateral__iconos' src='/btns/btn_export.png' />
				</a>
			</div>";
		}
		if (isset($modulos['T']) or $_usrAdmin == 'D') {
			$barraLateral .= "
			<div>
				<a class='barraLateral__enlace' href='/appUtilidades/modulos/appModulosLateral.php?idsistema=$idsistema&idTipo=T' title='Tablas ajuste sistema'>
					<img class='barraLateral__iconos' src='/btns/btn_conf.png' />
				</a>
			</div>";
		}
		if (isset($modulos['A']) or $_usrAdmin == 'D') {
			$barraLateral .= "
			<div>
				<a class='barraLateral__enlace' href='/appUtilidades/modulos/appModulosLateral.php?idsistema=$idsistema&idTipo=A' title='AdminModulo'>
					<img class='barraLateral__iconos' src='/btns/btn_cand.png' />
				</a>
			</div>
		";
		$barraLateral .= "<div class='barraLateral__sistema'>
			<img class='barraLateral__iconos' src='/images/$idsistema.png' alt=''>
		</div>";
	}

	$barraLateral .= "</nav>";
?>
