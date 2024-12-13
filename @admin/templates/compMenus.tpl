<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="{lang}">
<head>
	<title>{H2Sistema}</title>

	<link rel="shortcut icon" href="m.ico" />
	
	<link rel="stylesheet" href="/styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/print.css"  type="text/css" media="print" />
	<link rel="stylesheet" href="/styles/red-m.css" type="text/css" media="screen, projection" />

	<script src="/js/script_utiles.js" type="text/javascript" ></script>
	<script src="/js/red-m.js" type="text/javascript" ></script>
	<script src="/js/loadAjax.js" type="text/javascript" ></script>
    
    <script type="text/javascript">
        function comparar() {
			sisId  = document.getElementById('sistema').value;
			cliId  = document.getElementById('cliente').value;
        	url = 'compMenus.php?sistema='+sisId+'&cliente='+cliId+"&Ope=comp";
        	location.href=url;
        }
		
		function leeSistCliente() {
			
			cliId  = document.getElementById('cliente').value;
        	url = 'compMenus.php?cliente='+cliId+"&Ope=leeSist";
        	location.href=url;
        }
    </script>
</head>

<body>

<div id="toolsbar">
	{topbar}
</div>

<div class="container" style="float:left;" >
	<!-- ::::::::::::::::: Inicio Area de Lateral :::::::::::::::::::: -->
	<div class="span-4 ">
		{lateral}
	</div>
	<!-- ::::::::::::::::: Final Area de Lateral ::::::::::::::::::::: -->
	
	<!-- ::::::::::::::::: Inicio Area de Trabajo :::::::::::::::::::: -->
	<div class="span-20 last ">
		
		<div id="divCont" class="span-20" style="height:50px; margin-bottom:10px; background: url(/images/tit_docentes.png) no-repeat;">	
			<h2 class="titulosBarra" >{H2Modulo}</h2>
		</div>
		
		<form id="form1" name="form1" target="_top" method="get" action="clientes_ficha.php">
			<input type="hidden" name="IdRegistro" value="{IdRegistro}" />
			<div class="span-20"> 
				<div class="span-10 datos">
					<select onchange="leeSistCliente()" id="cliente" class="span-10"  size="0" style="padding-top:2px; padding-bottom:2px;">
						{optionClientes}
					</select>
                </div>
				<div id="divSistCli" class="span-10 datos last" style="display:{displaySistemas};">
					<select id="sistema" class="span-10"  size="0" style="padding-top:2px; padding-bottom:2px;">
						{optionSistemas}
					</select>
                </div>
			</div>
		
			<div class="span-10 prepend-10 last prepend-top">
    			<input onclick="comparar()"  class="enviarBuscar span-7" type="button" value="comparar con bd cliente" />
    			<input onclick="salir('/index_main')" class="enviarSalir span-3 last" type="button" value="{salir}"  />
			</div>
			
			<div class="span-20 enc_mensajes prepend-top"  style="display:{displayModulos};">
			   <h3>datos conexi&oacute;n</h3>
			</div>
			<div id="divConexion" class="span-20" style="display:{displayModulos};">
				<div class="span-2 datos " >
					<label>preFijo <br/>
					<input class="text sombra span-2" id="prefijoBD" name="prefijoBD" type="text"  value="{prefijoBD}" /></label>
				</div>

				<div class="span-4 datos " >
					<label>servidor<br/>
					<input class="text sombra span-4" id="url" name="url" type="text"  value="{url}"  /></label>
				</div>
				
				<div class="span-2 datos " >
					<label>puerto BD<br/>
					<input class="text sombra span-2"  id="puertoSQL" name="puertoSQL" type="text"  value="{puertoSQL}"   /></label>
				</div>
				
				<div class="span-3 datos " >
					<label>user BD<br/>
					<input class="text sombra span-3"  id="usrSQL" name="usrSQL" type="text"  value="{usrSQL}"   /></label>
				</div>
				
				<div class="span-3 datos " >
					<label>pass BD<br/>
					<input class="text sombra span-3"  id="passSQL" name="passSQL" type="text"  value="{passSQL}"   /></label>
				</div>
			</div>
			
			<div id="divModulosRed" class="span-20" style="display:{displayModulos};">
				<div class="span-10 prepend-top" style="height:300px; overflow:auto;">
					<h3>Servidor ReD-M</h3>
					{modulosREDM}
				</div>
				<div class="span-10 prepend-top last" style="height:300px; overflow:auto;">
					<h3>Servidor Cliente</h3>
					{modulosCliente}
				</div>
			</div>
			
			<div id="divModulosFaltantes" class="span-20" style="display:{displayModulos};">
				<div class="span-10 prepend-top" style="height:160px; overflow:auto; background-color:#FFDA91;">
					<div class="span-10" style="background-color:#ffffff;"><h3>Modulos Faltantes en Cliente</h3></div>
					{modFaltantesCliente}
				</div>
				<div class="span-10 prepend-top last" style="height:160px; overflow:auto; background-color:Pink;">
					<div class="span-10" style="background-color:#ffffff;"><h3>Modulos en Cliente no instalados en RED-M</h3></div>
					{modSoloInstEnCliente}
				</div>
			</div>
		</form>
	</div>
</div>	
<!--  -->