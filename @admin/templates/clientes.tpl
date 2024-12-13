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
		
		<div id="divCont" class="span-20" style="height:50px; margin-bottom:10px; ">	
			<h2 class="titulosBarra" >{H2Titulo}</h2>
		</div>
		
		<!-- :::::::::::::::::::::: INICIO CAMPOS BUSQUEDA :::::::::::::::::::::: -->
		<div class="span-20" style="background-color:#fafafa; padding-top:5px; padding-bottom:5px;" >
			<div class="span-6" >
				<label>Criterio de busqueda<br></label> 
				<div class="span-2" >
					<select class="span-2" name="campo" id="campo" size="0" style="padding:3px 0 3px 0; ">
						<option value="entidad" >Cliente</option>
						<option value="keynumber" >Serie</option>
					</select>
				</div>
				<div class="span-4 last">
					<input class="text span-4" name="buscar" id="buscar"  type="text"  value="{iguala}" />
				</div>
			</div>
			
			<div class="span-3" >		
				<input onclick="ficha('{moduloPHP}','new')" 			type="button" class="enviarGrande span-3" name="bBuscar" value="nuevo" style="margin-top:8px;"/>
			</div>
			<div class="span-3" >		
				<input onclick="buscarTab('clientes','licencias',1)" 	type="button" class="enviarBuscar span-3" name="bBuscar" value="Buscar" style="margin-top:8px;"/>
			</div>
            <div class="span-3 last" >
                <input onclick="salir('/index_main')" class="enviarSalir  span-3" type="button" value="{salir}" style="margin-top:8px;"/>
		    </div>  
        </div>
		<!-- :::::::::::::::::::::: FINAL CAMPOS BUSQUEDA :::::::::::::::::::::: -->
	
			
		<div id="divGrilla" class="span-20 prepend-top" >	
			<!-- :::::::::::::::::::::: INICIO ENCABEZADO BUSQUEDA :::::::::::::::::::::: -->
			<div class="span-20 append-2 last" >
				{grillaTitHTML}
			</div>
			<!-- :::::::::::::::::::::: FINAL ENCABEZADO BUSQUEDA :::::::::::::::::::::: -->
			
			<!-- :::::::::::::::::::::: INICIO SALIDA BUSQUEDA :::::::::::::::::::::: -->
			<div id="divLeeDocentes"  class="span-18 append-2 last" >
				{grillaHTML}
			</div>
			<!-- :::::::::::::::::::::: INICIO SALIDA BUSQUEDA :::::::::::::::::::::: -->
		</div>
		

		
		<div class="span-20 fondo_inv">
			{barra_pie_mensajes}
		</div>
	</div>
	<!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>

</body>
</html>
