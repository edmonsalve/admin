<?php 
	require_once('includes/initIndex.php');
   
    // ::  Determina Nombre de Sript en Ejecucion 
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);
	
    // :: BD
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_ADMIN, DB_SERVER, DB_USER, DB_PASSWD );


    if (isset($_GET['idsistema'])) { $idsistema = $_GET['idsistema']; } else { header("Location:/index_main.php"); }   
		

	// ::: Lee Sistema 
    $consulta = "SELECT *   FROM  `adm_sistemas` WHERE  id = $idsistema";                                         
	$salida   = $conexionDB->consulta($consulta);
    $row      = mysqli_fetch_array($salida);

    $_area  = $row['area'];
    $_ruta  = $row['ruta'];
    $_icono = $row['icono'];


	$consulta = "SELECT *   FROM  `adm_modulos`
							WHERE  idsistema = $idsistema AND  tipo = 'ADM' AND estado = 1";
                                           

	$salida = $conexionDB->consulta($consulta);

    while ($row = mysqli_fetch_array($salida)) {
		$_idModulo		= $row['idModulo'];
        $_idsistema 	= $row['idsistema'];
		$_modulo        = $row['modulo'];
		$_detalle       = $row['detalle'];
        $_target        = $row['target'];
		$_php   	    = $row['php'];
		$_parametros    = $row['accionPparametrosHP'];

        $href = $rootSistema."modulos/$_php";

        $_divAcciones .=  "<a class='enlace__tarjeta' href='$href'>
                                <div class='tablero__tarjeta'>
                                    <img class='tarjeta__img' src='/fondos/fondo_export.png'>
                                    <p class='tarjeta__texto centrado'>$_modulo</p>
									<p class='tarjeta__texto tarjeta__texto--peq centrado'>$_detalle</p>
                                </div>
                            </a> ";
	}

	// :::::::::::::::::::::::::::::::::::::::::::: Carga Plantilla ::::::::::::::::::::::::::::: //
	$contenido=new plantilla("index_main");
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

					"enviar"     	=> 'enviar',
					"serie"			=> $serie,

					"tituloForm"		=> $tituloForm,

					// "_divAccionesFila"	=> $_divAccionesFila,
                    "_divAcciones"      => $_divAcciones,
                    "dispFormulario"    => $dispFormulario,
					"placeholder"		=> $placeholder,
					"acc"				=> $acc,
				));
				
	echo $contenido->muestra();
?>