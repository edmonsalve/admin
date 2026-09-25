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

    if (isset($_GET['idsistema'])) { 
        $idsistema = $_GET['idsistema']; 
        $idTipo    = $_GET['idTipo']; 
        $_SESSION['lateralSistema'] = $idsistema;
        $_SESSION['lateralTipo']    = $idTipo; 
    } else { 
        header("Location:/index_main.php"); 
    }   

    //:: BD
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_ADMIN, DB_SERVER, DB_USER, DB_PASSWD );
	

    if (isset($_GET['idsistema'])) { $idsistema = $_GET['idsistema']; } else { header("Location:/index_main.php"); }   
		
	
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
		$consulta = "SELECT * 	FROM   `$DB_ADMIN`.`adm_modulos`, `$DB_ADMIN`.adm_estrucMenu
								WHERE  `idsistema`  = $SISTEMA_ID 
								AND     adm_modulos.tipo = adm_estrucMenu.tipoGrupo
								AND     estado = 1
                                AND     adm_estrucMenu.tipoMenu   = 'I'
								ORDER BY `tipo`,`modulo`";  
    } else {
        $consulta = "SELECT *,  CASE   target  WHEN 'T' THEN '_top' WHEN 'B' THEN '_blank'  END AS targerFile
                                FROM   `$DB_ADMIN`.`adm_modulos` m, `$DB_ADMIN`.adm_estrucMenu e, `$DB_CLIENTE`.adm_accesos a
                                WHERE   m.`idsistema` = $idsistema 
                                AND     m.tipo = e.tipoGrupo
                                AND     m.idModulo = a.idmodulo
                                AND     a.iduser = $idUser
                                AND		a.acceso = 'S'
                                AND     estado = 1
                                AND     e.tipoMenu = 'I'
                                ORDER BY tipoGrupo asc, `modulo`"; 
    }

    $salida = $conexionDB->consulta($consulta);

    $_titGr         = "";
    $_informesHTML  = "";
    $_primero       = true;

    while ($row = mysqli_fetch_array($salida)) {
		$_idModulo		= $row['idModulo'];
        $_idsistema 	= $row['idsistema'];
		$_modulo        = $row['modulo'];
        $_detalle       = $row['detalle'];
        $_target        = $row['target'];
		$_php   	    = $row['php'];
		$_parametros    = $row['parametros'];
        $_direcGrupo    = $row['direcGrupo'];
        $_tituloGrupo   = $row['tituloGrupo'];
 
        $tituloJS = str_replace(" ","_",$_modulo); 

        switch ($_target) {
            case 'T':
                $_target    = "_top";
                $funsionJS  = 'leeFiltros';  
                break;
            case 'B':
                $_target    = "_blank";
                $funsionJS  = 'imprime'; 
                break;
            default:
                $_target    = "_top";
                $funsionJS  = 'leeFiltros';  
                break;
        }

      
        $ruta = $rootSistema.$_direcGrupo.$_php."?area=$_area&ruta=$_ruta&direcGrupo=$_direcGrupo";
        if (trim($_parametros) != "") { 
            $ruta .= "&$_parametros"; 
            $_informePHP    = $_parametros;

            parse_str($_parametros, $_salida);
            $_informePHP = $_salida['php'];
            $_informePHP    = str_replace('.php','',$_informePHP);
        } else {
            $_informePHP    = str_replace('.php','',$_php);
        }

        $js = "'$ruta','$tituloJS'";

   
        if ($_titGr != $_tituloGrupo) {
            $_titGr = $_tituloGrupo;
            if ($_primero) { $_primero = false; } else { $_informesHTML  .= "</div>"; }
            $_informesHTML .= "<div class='tablero__fila'><p>$_tituloGrupo</p></div> 
                               <div class='tablero--2col'>"; 
        }
        
        
        $_informesHTML .=  "<a href='#toolsbar' style='margin-bottom:5px;'>
                                <div onclick=$funsionJS($js) class='tablero__tarjeta--horizontal'>
                                    <img class='tarjeta__img--horizontal' src='/fondos/fondoImpr.png'>
                                    <div class='tHorizontal__body'>
                                        <p class='tarjeta__texto izquierda' style='color:black;'>$_modulo <br><span style='font-size: 9px; color: black; font-style: italic;'>($_informePHP)</span></p>
                                        <p class='tarjeta__texto tarjeta__texto--peq izquierda'>$_detalle</p>
                                    </div>
                                </div>
                            </a>";

	}
    $_informesHTML  .= "</div>";

    include(PATH_BASE . ROOT_UTIL . 'modulos/appModulosIco.php'); 

	// :::::::::::::::::::::::::::::::::::::::::::: Carga Plantilla ::::::::::::::::::::::::::::: //
	$contenido=new plantilla("appModulosInf");
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

					"tituloForm"		=> $tituloForm,

					'headerMenu'		=> $headerMenu,

                    "_informesHTML"     => $_informesHTML,
                    "dispFormulario"    => $dispFormulario,
				));
				
	echo $contenido->muestra();
?>