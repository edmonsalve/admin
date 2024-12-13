<?php
    $keynumber    = $_SESSION['keynumber'];
    $VERSION_SIS  = VERSION_SIS;
	$nombreUSR	  = $_SESSION['usuario'];
	
	$_user  	  = $_SESSION['idUser'];
	$_prefijo     = $_SESSION['prefijo']; 
	
	$LINK_SOPORTE = LINK_SOPORTE;

	$topbar = "";
    /* :::::::::::::::::::::::::::::::::::::  Barra superior ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    $topbar = "
	<div style='float:left; height:40px; width:100%;'>
		<div class='span-4 ' style='height:40px;'>  
			<a href='/index_main.php'><img id='logotools' src='/".PATH_ROOT."images/dAdminLog.png' height='40' alt='Logo' style='float:left; margin-left:20px;'/></a>
		</div>
		<div class='span-4 prepend-20 last' style='height:40px; text-align:right; padding-top:5px'>
			<div class='span-4 last'>
				<a href='/".PATH_ROOT."logout.php' target='_top' style='text-decoration:none; ' >
					<button class='enviarSalir sombra span-4 last' type='button' />Cerrar Sesi&oacute;n</button>
				</a>
			</div>
		</div>
	<div>";  
          
	$bottombar = "";
    /* :::::::::::::::::::::::::::::::::::::  Barra inferior ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    $bottombar = "
        <div class='datos span-3' style='border-right:#FFFFFF 1px solid;' >Versi&oacute;n: $VERSION_SIS</div>
    	<div class='datos span-8' style='border-right:#FFFFFF 1px solid;' >N&deg; Serie: $keynumber </div>
    	<div class='datos span-8 last'  >Usuario: $nombreUSR</div>";	
?>