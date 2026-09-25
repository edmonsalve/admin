<?php
    // $keynumber    = $_SESSION['keynumber'];
    $VERSION_SIS  = VERSION_SIS;
	$nombreUSR	  = $_SESSION['nomUsuario'];
	
	$_user  	  = $_SESSION['idUser'];
	$_prefijo     = $_SESSION['prefijo']; 

	$idsistema    = (isset($_SESSION['idsistema'])) ? $_SESSION['idsistema'] : '';
	$rutaSistema  = (isset($rutaSistema)) ? $rutaSistema : '';

	$LINK_SOPORTE = LINK_SOPORTE;
	$LOGO_CLIENTE = LOGO_CLIENTE;	

	$index_main = $raizSistemas.$rutaSistema."index_main.php";

	$barraLateral  = "<section class='barraLateral'>"; 
	
    // echo "verLogoAux: " . $verLogoAux . ' - ' . LOGO_AUX;

	if (LOGO_AUX == 'S') {
		$barraLateral .= "<div style='padding-top:10px; padding-bottom: 20px;'><a href='https://www.facebook.com/share/v/16DB6We5AP/' target='_blank'><img src='/imagesCli/$logoAux' height='90' alt='Logo Auxiliar' /></a></div>";
	}

	$barraLateral .= "
	<div>
		<a href='/index_main.php'><img class='barraLateral__iconos barraLateral__iconos--inicio' src='/images/indexMain.png' /></a>
	</div>";
	$barraLateral .= "</section>";
?>