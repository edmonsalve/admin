<?php 
	require_once('../includes/initSistema.php');

    // ::  Determina Nombre de Sript en Ejecucion 
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);
    $DB_ADMIN    = DB_ADMIN;
    $DB_CLIENTE  = DB_CLIENTE;

    $idUser      = $_SESSION['idUser'];
    $tipoUser    = $_SESSION['tipo'];
	$usrAdmin    = $_SESSION['usrAdmin'];
	$SISTEMA_ID  = $_SESSION['idsistema'];

    // :: BD
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_ADMIN, DB_SERVER, DB_USER, DB_PASSWD );

    if (isset($_GET['idsistema'])) { 
        $idsistema = $_GET['idsistema']; 
        $idTipo    = $_GET['idTipo']; 
        $_SESSION['lateralSistema'] = $idsistema;
        $_SESSION['lateralTipo']    = $idTipo; 
    } else { 
        header("Location:/index_main.php"); 
    }   

	// ::: Lee Sistema 
    $consulta = "SELECT *   FROM  `adm_sistemas` WHERE  id = $idsistema ";                                         
	$salida   = $conexionDB->consulta($consulta);
    $row      = mysqli_fetch_array($salida);

    $_area  = $row['area'];
    $_ruta  = $row['ruta'];
    $_icono = $row['icono'];

    switch($_area) {
        case 'C':   $rootSistema = RUTA_ABSOLUTA."appContabilidad/$_ruta/";  break;        
        case 'M':   $rootSistema = RUTA_ABSOLUTA."appMuni/$_ruta/";  break;        
        case 'S':   $rootSistema = RUTA_ABSOLUTA."appSalud/$_ruta/"; break;         
        case 'E':   $rootSistema = RUTA_ABSOLUTA."appEduc/$_ruta/";  break;  
        case 'A':   $rootSistema = RUTA_ABSOLUTA.'appComun/'; break;  
    }
             
    if ($usrAdmin == 'D') {
		$consultaMod = "SELECT *, 	CASE   target  WHEN 'T' THEN '_top' WHEN 'B' THEN '_blank'  END AS targerFile
                                    FROM   `$DB_ADMIN`.`adm_modulos`, `$DB_ADMIN`.adm_estrucMenu
                                    WHERE  `idsistema`  = $SISTEMA_ID 
                                    AND     adm_modulos.tipo = adm_estrucMenu.tipoGrupo
                                    AND     estado = 1
                                    AND     adm_estrucMenu.tipoMenu   = '$idTipo'
                                    ORDER BY `tipo`, `ordenColumna`, `modulo`";  
    } else {
        $consultaMod = "SELECT *,   CASE   target  WHEN 'T' THEN '_top' WHEN 'B' THEN '_blank'  END AS targerFile
                                    FROM   `$DB_ADMIN`.`adm_modulos` m, `$DB_ADMIN`.adm_estrucMenu e, `$DB_CLIENTE`.adm_accesos a
                                    WHERE   m.`idsistema` = $idsistema 
                                    AND     m.tipo = e.tipoGrupo
                                    AND     m.idModulo = a.idmodulo
                                    AND     a.iduser = $idUser
                                    AND		a.acceso = 'S'
                                    AND     estado = 1
                                    AND     e.tipoMenu = '$idTipo'
                                    ORDER BY `tipo`, `ordenColumna`, `modulo`";  
    }

    $salida = $conexionDB->consulta($consultaMod);

    $_titGr       = "";
    $_modulosHTML = "";
    $_primero     = true;

    switch ($idTipo) {
        case 'A':
            $_fondoTarjeta = "fondoAdmin.png";
            break;
        case 'E':
            $_fondoTarjeta = "fondoExportar.png"; // "fondoExcel.png" "fondo_export.png"
            break;
        case 'T':
            $_fondoTarjeta = "fondoTablas.png";
            break;
        default:
            $_fondoTarjeta = "fondo_bd.png";
            break;
    }

    while ($row = mysqli_fetch_array($salida)) {
		$_idModulo		= $row['idModulo'];
        $_idsistema 	= $row['idsistema'];
		$_modulo        = $row['modulo'];
        $_detalle       = $row['detalle'];
        $_target        = $row['targerFile'];
		$_php   	    = $row['php'];
		$_parametros    = $row['parametros'];
        $_direcGrupo    = $row['direcGrupo'];
        $_tituloGrupo   = $row['tituloGrupo'];

        $ruta = $rootSistema.$_direcGrupo.$_php;   // "?area=$_area&ruta=$_ruta&direcGrupo=$_direcGrupo"  
        if (trim($_parametros) != "") { $ruta .= "?$_parametros"; } 
        $js = "'$ruta','$tituloJS'";
   
        if ($_titGr != $_tituloGrupo) {
            $_titGr = $_tituloGrupo;
            if ($_primero) { $_primero = false; $marginTop = " margin-top: 10px;"; } else { $_modulosHTML .= "</div>"; $marginTop = ""; }
            $_modulosHTML .= "<div class='tablero__fila' style='$marginTop'><p>$_tituloGrupo</p></div> 
                               <div class='tablero--col'>"; 
        }

        $_modulosHTML   .=  "<a class='enlace__tarjeta' href='$ruta' target='$_target'>
                                <div class='tablero__tarjeta tablero__tarjeta--peq'>
                                    <img class='tarjeta__img' src='/fondos/$_fondoTarjeta'>
                                    <p class='tarjeta__texto centrado'>$_modulo</p>
                                    <p class='tarjeta__texto tarjeta__texto--peq centrado'>$_detalle</p>
                                </div>
                            </a>";
	}
    $_modulosHTML  .= "</div>";

    include(PATH_BASE . ROOT_UTIL . 'modulos/appModulosIco.php'); 

	// :::::::::::::::::::::::::::::::::::::::::::: Carga Plantilla ::::::::::::::::::::::::::::: //
	$contenido=new plantilla("appModulosLateral");
	$contenido->asigna_variables(
				array(
                    "PATH_ROOT"         => PATH_ROOT,
                    "topbar"		    => $topbar,
                    "barraLateral"		=> $barraLateral,
					"titulo"  		    => TITULO_NAVEGADOR,

					"icono"	  		    => PATH_ICO.ICONO_NAVEGADOR, 
					"lateral"		    => $lateral,

					"cerrarSesion"        => $dg_txt->GetDefinition('cerrarSesion'),
                    "buscarpor"        	  => $dg_txt->GetDefinition('buscarpor'),
                    "borrar"        	  => $dg_txt->GetDefinition('borrar'),
                    "salir"        	      => $dg_txt->GetDefinition('salir'),
                    "enviar"        	  => $dg_txt->GetDefinition('enviar'),
                    "nuevo"        	      => $dg_txt->GetDefinition('nuevo'),

                    "nombre"        	  => $dg_txt->GetDefinition('nombre'),
                    "email"        	      => $dg_txt->GetDefinition('email'),
                    "telefono"            => $dg_txt->GetDefinition('telefono'),

					"enviar"     	      => 'enviar',
					"serie"		          => $serie,

					"tituloForm"		  => $tituloForm,
					'headerMenu'		  => $headerMenu,
                    "_modulosHTML"        => $_modulosHTML,
				));
				
	echo $contenido->muestra();
?>