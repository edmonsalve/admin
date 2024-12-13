<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="es-ES">
<head>
	<meta name="author" content="Edmundo Monsalve" />

	<title>{titulo}</title>
	<link rel="shortcut icon" href="m.ico" />
	
	<link rel="stylesheet" href="../styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="../styles/print.css"  type="text/css" media="print" />
	<link rel="stylesheet" href="../styles/red-m.css" type="text/css" media="screen, projection" />

	<script src="/js/red-m.js" type="text/javascript"></script>
    
    <script type="text/javascript">
    <!--
    window.onresize = function() {
      sizeScreen();
    }	
    -->
    </script>
</head>

<body onload="sizeScreen()">

<div id="toolsbar">
	{topbar}
</div>

<div class="container" style="float:left;" >
	<!-- ::::::::::::::::: Inicio Area de Lateral :::::::::::::::::::: -->
	<div class="span-4" >
		{lateral}
	</div>
	<!-- ::::::::::::::::: Final Area de Lateral ::::::::::::::::::::: -->
	
	<!-- ::::::::::::::::: Inicio Area de Trabajo :::::::::::::::::::: -->
	<div class="span-20 last ">

		<div id="divCont" class="span-20" style="height:50px; margin-bottom:10px; background: url(/images/tit_tablas.png) no-repeat;">	
			<h2 class="titulosBarra" >Cambio de Contrase&ntilde;a y Fotograf&iacute;a</h2>
		</div>

        <form name="formularioFicha" method="post" action="userdata.php" enctype="multipart/form-data" >
		<div id="divCont" class="span-20" >	
			<div class="span-10">
				<div class="span-10 datos">
					<label id="categoria">Contrase&ntilde;a Actual<br/><input class="text span-5 sombra" name="antigua" type="password" value="" /></label>
				</div>
                <div class="span-10 datos">
					<label id="categoria">Nueva Contrase&ntilde;a<br/><input class="text span-5 sombra"  name="nueva" type="password" value="" /></label>
				</div>
                <div class="span-10 datos">
					<label id="categoria">Reingrese Nueva Contrase&ntilde;a<br/><input class="text span-5 sombra" name="renueva" type="password" value="" /></label>
				</div>
			</div>
	        <div class="span-10 last">
                <label  id="subirdoc">Mi fotograf&iacute;a<input class="text" name="mifoto" type="file" /></label>                        
            </div>
		</div>
		
        <div class="span-20" >  
			<br />
            <p style="color:red; font-weight:bold;">{error}</p>
            <br />
			<input class="enviar span-5" type="submit" value="{enviar}" />
            <input onclick="salir('/index_main')" class="enviar span-5" type="button" value="salir" />
		</div>
        </form>  
                 
		<div id="barraInf" class="span-20  barra" style="position:absolute; top:450px;">
			{bottombar}
		</div>
	</div>
	<!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>
</body>
</html>
