<?php
	$fotousuario = 'pregunta.png';
	$href = '../index.php';
	
    // ::::::: Agrega Foto de Usuario  :::::::::: //
	$lateral = "	
		<div style='width:100px; height:120px; background:#dcdcdc; margin: 35px 0px 0 25px; border:3px solid #666666;'>        
			<img src='/".PATH_ROOT."fotosUsr/$fotousuario' width='100' height='100' alt='foto usuario' style='margin-top:10px; margin-left:0px'/>	    
		</div>
	";

    // ::::::: Agrega Icono de la Plataforma o del sistema a LATERAL  :::::::::: //   
	$lateral .= "
		<div class='span-4' style='text-align:center; '>
			<h3>$usrID</h3>
			<br>			
			<a href='$href'>
				<img src='/".PATH_ROOT."images/". LOGO_CLIENTE2 ."'  width='110' height='70' />	
			</a><br><br>
			<a href='$href'>
				<img src='/".PATH_ROOT."iconos-ok/".LOGO_SISTEMA."' width='110' height='110' />
			</a>
		</div>
	";
?>