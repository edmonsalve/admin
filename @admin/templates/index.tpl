<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="es-ES">
<head>
	<meta name="author" content="Edmundo Monsalve" />
    <meta http-equiv="refresh" content="30" />
    
	<title>{tituloNav}</title>
	<link rel="shortcut icon" href="images/m.ico" />
	
	<link rel="stylesheet" href="/{PATH_ROOT}styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/{PATH_ROOT}styles/print.css"  type="text/css" media="print" />
	<link rel="stylesheet" href="/{PATH_ROOT}styles/red-m.css" type="text/css" media="screen, projection" />
	
	<script src="js/red-m.js" type="text/javascript"></script>
    
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

		<div id="divCont" class="span-20" >	
			<div class="span-10" style="margin-top:10px;" >
				<div class="span-10 centro">
					<h3 style="float:left; padding-top:15px; " >Área de Gest&iacute;on RED-M</h3>
				</div>
                <div class="span-12 prepend-top" style="border-radius: 20px; background-color:#FAEBD7; padding-top:15px;" >
				    {menuTrabajoHTML}
                </div>
			</div>
            


		</div>
		
		
	</div>
	<!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>
</body>
</html>
