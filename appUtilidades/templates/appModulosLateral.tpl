<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="{lang}">
<head>
	<title>{H2Sistema}</title>
	<link rel="icon" type="image/png" href="/images/icoDC.png">

	<!-- Fuente -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="/styles/normalize.css" 	  type="text/css"media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_globales.css" 	  type="text/css"media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_topbar.css" 	  type="text/css"media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_contenedores.css" type="text/css"media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_formularios.css"  type="text/css"media="screen, projection" />
    
	<script src="/js/dcode.js" type="text/javascript"></script>
    <script type="text/javascript">
    </script>
</head>

<body>
	<div id="toolsbar" style="display: block;">{topbar}</div>
	
	<!-- INICIO MAIN -->
	<main class="principal">
		{barraLateral}
	
		<section class="tablero">
			<div class='tablero__header'>
				<div class='filaGeneral filageneral--menu' style="width: 100%;">
					{headerMenu}
				</div>
			</div>
			<div> 
				{_modulosHTML}
			</div>
		</section>
	</main> 
</body>
</html>