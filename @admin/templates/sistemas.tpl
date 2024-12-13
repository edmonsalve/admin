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
		function buscarSistema(modulo,db,pagina) {
        	campo     = document.getElementById('campo').value;
        	criterio  = document.getElementById('buscar').value;
			
			area		= document.getElementById('area').value;
			
        	pagAct   	= document.getElementById('paginaAct').value;
        	pagFin   	= document.getElementById('paginaFin').value;
        	pag      	= pagina;

        	if (pagina == 'A') { pag = parseInt(pagAct) - 1; if (pag < 1) { pag = 1; }}
        	if (pagina == 'S') { pag = parseInt(pagAct) + 1; if (pag > pagFin) { pag = pagFin; }}
            if (pagina == 'U') { pag = pagFin; }
            if (pagina == 'I') { pag = 1; }
        
            url  = "sistemas.php?buscarpor="+campo+'&iguala='+criterio+'&pag='+pag+"&area="+area;
			
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
			<div class="span-9" >
				<label>Criterio de busqueda<br></label> 
				<div class="span-3" >
					<select class="span-3" name="campo" id="campo" size="0" style="padding:3px 0 3px 0; ">
						{htmlCriterios}
					</select>
				</div>
				<div class="span-4">
					<input class="text span-4" name="buscar" id="buscar"  type="text"  value="{iguala}" />
				</div>
				<div class="span-2 last" >
					<select class="span-2" name="area" id="area" size="0" style="padding:3px 0 3px 0; ">
						<option value="T" {st}>Todos</option>
						<option value="M" {sm}>Municipal</option>
						<option value="S" {ss}>Salud</option>
						<option value="E" {se}>Educ.</option>
						<option value="C" {sc}>Comer.</option>
						<option value="A" {sa}>Admin</option>
					</select>
				</div>
			</div>
			
			<div class="span-3" style="margin-top:8px;" >		
				<input onclick="buscarSistema('sistemas','sistemas',1)" 	type="button" class="enviarBuscar span-3" name="bBuscar" value="Buscar" />
			</div>
			<div class="span-3" style="margin-top:8px;" >		
				<input onclick="ficha('{moduloPHP}','new')" 			type="button" class="enviarGrande span-3" name="bBuscar" value="nuevo" />
			</div>
            <div class="span-3 last" style="margin-top:8px;" >
                <input onclick="salir('/index_main')" class="enviarSalir  span-3" type="button" value="{salir}" />
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
