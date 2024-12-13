<?php
	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }
	
	$sesionID	 = session_id();
    $fotousuario = $_SESSION['foto'];

    if (isset($estoyenmenu)) { $href = "../../index_main.php"; } else { $href = "../index_main.php"; }
    if (isset($antiguo)) { $href = "index_main.php"; }

    // ::::::: Agrega Foto de Usuario  :::::::::: //
	$lateral = "</br>
	<div class='prepend-top' style='width:95px; height:125px; background:#dcdcdc; margin: 15px 0 0 35px; '>
        <a href='/includes/userdata.php' target='_top'>
		<img src='/".PATH_ROOT."fotosUsr/$fotousuario' width='100' height='120' alt='foto usuario' style='border:3px solid #666666; margin-top:-5px; margin-left:-5px ' />
	    </a>
    </div>";

    // ::::::: Agrega Icono de la Plataforma o del sistema a LATERAL  :::::::::: //
    $lateral .= "
	<div class='span-4' style='text-align:center; '>
		<h3>$usrID</h3>
		<br /><br /><br />
		<a href='$enlace'>
		<img src='/".PATH_ROOT."images/". LOGO_CLIENTE ."'  width='110' height='110' style='cursor:pointer;' />
		</a>
        <br /><br />";

        
        if (isset($_SESSION['iconosis'])) { $iconosist = $_SESSION['iconosis'];}
        

        

    // ::::::: Agrega Icono y Link de soporte a LATERAL  :::::::::: //
	$lateral .= "
    </div>";
?>
