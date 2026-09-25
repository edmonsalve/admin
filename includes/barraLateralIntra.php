<?php
    $keynumber    = $_SESSION['keynumber'];
    $VERSION_SIS  = VERSION_SIS;
	$nombreUSR	  = $_SESSION['usuario'];
	
	$_user  	  = $_SESSION['idUser'];
	$_prefijo     = $_SESSION['prefijo']; 

	$idsistema    = $_SESSION['idsistema'];

	$LINK_SOPORTE = LINK_SOPORTE;
	$LOGO_CLIENTE = LOGO_CLIENTE;	

	$index_main = $raizSistemas.$rutaSistema."index_main.php";

	$barraLateral  = "<section class='barraLateral'>"; 
	if (isset($ico)) {
		$muestraIco = "<a href='/index_main.php'><img class='barraLateral__iconos' src='/iconos/$ico' /></a>";
	} else {
		$muestraIco = "";
	}
	$barraLateral .= "
	<div>
		$muestraIco 
		<img class='barraLateral__iconos' src='/images/intranet.png' />
	</div>";
	$barraLateral .= "</section>";
?>