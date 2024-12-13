<?php
	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }
	
	$sesionID	  = session_id();
	$cerrarSesion = $red_text->GetDefinition('cerrarSesion');
	
    $fotousuario = $_SESSION['foto'];
    
    if (isset($estoyenmenu)) { $href = "../../index_main.php"; } else { $href = "../index_main.php"; }
    if (isset($antiguo)) { $href = "index_main.php"; }
    
	$lateral = "
	<div style='width:95px; height:125px; background:#dcdcdc; margin: 15px 0 0 35px; '>
        <a href='/includes/userdata.php' target='_top'>
		<img src='/".PATH_ROOT."images/$fotousuario' width='100' height='120' alt='foto usuario' style='border:3px solid #666666; margin-top:-5px; margin-left:-5px ' />
	    </a>
    </div>

	<div class='span-4' style='text-align:center; '>
		<h3>$usrID</h3>
		<br /><br /><br />
		<img src='/".PATH_ROOT."images/escudo.png' alt='Anagrama' width='110' height='120' />
        <br /><br />";
        if (isset($_SESSION['iconosis'])) { $iconosist = $_SESSION['iconosis'];}
        if (isset($iconosist)) { 
            $lateral .= "
                <a href='$href'>
                <img src='$iconosist' alt='Anagrama' width='90' height='108' /> 
                </a>
            "; 
        }              
	$lateral .= "
    </div>
	";
    // echo "$lateral";
?>
