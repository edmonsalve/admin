<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="es-ES">
<head>
	<meta name="author" content="Edmundo Monsalve" />

	<title>{tituloNav}</title>
	<link rel="shortcut icon" href="../../images/m.ico" />
	
	<link rel="stylesheet" href="../../styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="../../styles/print.css"  type="text/css" media="print" />
	<link rel="stylesheet" href="../../styles/red-m.css"  type="text/css" media="screen, projection" />
    
    <script src="../../js/red-m.js" type="text/javascript"></script>
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

		<h2 style="display:none">{H2titulo}</h2>
		

		<div id="divCont" class="span-20" style="margin-top:10px;">	
			<div class="span-20">					
				<h2>{sistema}</h2>
			</div>
			{menuSistema}
		</div>

		<!-- :::::::::::::::::::: barra de pie de pagina  ::::::::::::::::::::::::::::::::::  --> 
        <div id="barraInf" class="span-20  barra" style="position:absolute; top:450px;">
			{bottombar}
		</div>
	</div>
	<!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>
</body>
</html>
