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
        function modulos(serial) {
        	url = 'clientes_modulos.php?serial='+serial;
        	location.href=url;
        }
        
        function detalles(serial) {
        	url = 'clientes_ficha.php?serial='+serial;
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
                <div class="span-3 datos">
                	<label>rut (99999999K)<br/>
                    <input eadonly="readonly"  class="span-3 text sombra" type="text"  style="text-align:right; "value="{rut}" />
                	</label>
                </div>

                <div class="span-8 datos">
                	<label Nombre Organizaci&oacute;n<br/>
                    <input readonly="readonly" class="span-8 text sombra"  type="text"  style="text-align:left; " value="{cliente}" />
                	</label>
                </div>
			</div>
		
			
			<div class="span-20">
				<div class="span-20">
					<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>CLIENTE: {cliente}</h3>
					</div>
					<div class="span-20">
						{_clienteHTML}
					</div>
				</div>
				
				<div class="span-20">
					<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>RUT: {rut}</h3>
					</div>
					<div class="span-20">
						{_rutHTML}
					</div>
				</div>
				
				<div class="span-20">
					<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>Licencia: {_licencia}</h3>
					</div>
					<div class="span-20">
						{_licHTML}
					</div>
				</div>
				
				<div class="span-20">
					<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>Sistemas: {sistemas}</h3>
					</div>
					<div class="span-20">
						{_sistHTML}
					</div>
				</div>
				
				<div class="span-20">
					<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>Estado: {estado}</h3>
					</div>
					<div class="span-20">
						{_estadoHTML}
					</div>
				</div>
				
				<div class="span-20">
					<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>Vencimiento: {vcto}</h3>
					</div>
					<div class="span-20">
						{_vctoHTML}
					</div>
				</div>
				
			</div>
			<div class="span-10 prepend-10 last">
    			<input  class="enviarBuscar span-7" type="button" value="mover licencia a bd cliente" />
    			<input  class="enviarSalir span-3 last" type="submit" value="{salir}"  />
			</div>
		</form>
	</div>
</div>	
?>