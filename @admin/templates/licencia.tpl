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
		
		<form id="form1" name="form1" target="_top" method="post" action="licencia_calculo.php">
			<div class="span-20">
                <div class="span-12 datos">
                	<label id="cliente">Cliente<br/>
						<select name="clienteId" class="span-12"   size="0" style="padding-top:2px; padding-bottom:2px;">
							{_htmlClientes}
						</select>
                	</label>
                </div>
			</div>
			<div class="span-20">
                <div class="span-4 datos">
                	<label id="fecha">Vencimiento<br/>
                    <input class="span-4 text sombra " id="vcto" name="vcto" type="date"   />
                	</label>
                </div>
                <div class="span-4 datos">
                	<label class="span-4  sombra" >Estado licencia<br/>
                	<select name="estado" class="span-4"   size="0" style="padding-top:2px; padding-bottom:2px;">
                		<option value="A" >Activado</option>
                		<option value="D" >Desactivado</option>
                	</select>
                    </label>
                </div>
                <div class="span-4 datos">
                	<label class="span-4  sombra last" >Tipo licencia<br/>
                	<select name="tipo" size="0" class="span-4" style="padding-top:2px; padding-bottom:2px;">
                		<option value="C" >Compra</option>
                		<option value="A" >Arriendo</option>
                	</select>
                    </label>
                </div>
			</div>
			
			<div class="span-20">
				<div class="span-10">
					<div class="span-10 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>Sistemas área municipal</h3>
					</div>
					<div class="span-10">
						{_htmlSistMuni}
					</div>
				</div>
				
				<div class="span-10 last">
					<div class="span-10 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>Sistemas área salud</h3>
					</div>
					<div class="span-10">
						{_htmlSistSalud}
					</div>
					
					<div class="span-10 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>Sistemas área educación</h3>
					</div>
					<div class="span-10">
						{_htmlSistEduc}
					</div>
					
					<div class="span-10 enc_mensajes prepend-top"  style="margin-top:10px;">
						<h3>Administración plataforma</h3>
					</div>
					<div class="span-10">
						{_htmlSistAdmin}
					</div>
				</div>
			</div>
			<div class="span-6 prepend-14 last">
    			<input  class="enviarGrande span-3" type="submit" value="enviar" />
    			<input  onclick="salir('../index')" class="enviarSalir span-3 last" type="button" value="{salir}"  />
			</div>
		</form>
	</div>
</div>	
