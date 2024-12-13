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
        function patentes(IdProvee) {
        	url = 'comunProveedores.php?IdRegistro='+IdIdProvee;
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
			<div class="span-3" style="text-align:right;"><h3 style="margin-top:12px;">{buscarpor}</h3></div>

			
			<div class="span-4" >
				<select name="campo" id="campo" size="0" style="width:150px;  padding:3px 0 3px 0; ">
					<option value="IdProvee" >{id}</option>
                    <option value="razonSocial" >{razonSocial}</option>
				</select>
			</div>
			<div class="span-1" style="text-align:center;"> <h3 style="margin-top:12px;">=</h3></div>
			<div class="span-5">
				<input class="text" name="buscar" id="buscar"  type="text"  style="width:160px;" value="{iguala}" />
			</div>
			<div class="span-2" >		
				<input onclick="buscar('comunProveedores','comun_tabProveedores',1)"      type="button" class="enviar" name="bBuscar" value="Buscar" style="width:70px; margin-top:8px; "/>
			</div>
			<div class="span-2" >		
				<input onclick="ficha('comunProveedores','new')" type="button" class="enviar" name="bNuevo" value="Nuevo" style="width:70px; margin-top:8px; "/>
			</div>
            <div class="span-2 last" >
                <input onclick="salir('../index_main')" class="enviar" type="button" value="{salir}" style="width:70px; margin-top:8px; " />
		    </div>  
        </div>
		<!-- :::::::::::::::::::::: FINAL CAMPOS BUSQUEDA :::::::::::::::::::::: -->
	
			
		<div id="divContDocente" class="span-20" >	
			<!-- :::::::::::::::::::::: INICIO ENCABEZADO BUSQUEDA :::::::::::::::::::::: -->
			<div class="span-20 append-2 last" >
				<div class="span-2 enc_mensajes" >
					 <h3>{id}</h3>
				</div>
				<div class="span-18 last enc_mensajes" >
					 <h3>{razonSocial}</h3>
				</div>
			</div>
			<!-- :::::::::::::::::::::: FINAL ENCABEZADO BUSQUEDA :::::::::::::::::::::: -->
			
			<!-- :::::::::::::::::::::: INICIO SALIDA BUSQUEDA :::::::::::::::::::::: -->
			<div id="divLeeDocentes"  class="span-18 append-2 last" >
				{grillaHTML}
			</div>
			<!-- :::::::::::::::::::::: INICIO SALIDA BUSQUEDA :::::::::::::::::::::: -->
		</div>
		
		<div class="span-20" style="text-align:center; ">
			<ul class="paginacion">
				{paginacionHTML}
			</ul>
		</div>
		
		<div class="span-20 fondo_inv">
			{barra_pie_mensajes}
		</div>
	</div>
	<!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>

</body>
</html>
