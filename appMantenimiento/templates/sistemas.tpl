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
    
	<script src="/js/dcode.js" 		type="text/javascript"></script>
	<script src="/js/loadAjax.js" 	type="text/javascript" ></script>
    
	<script type="text/javascript" >
		function modulosSistemas(idSistema){
			location.href = 'sistemas_modulos.php?idSistema=' + encodeURIComponent(idSistema);
		}
	</script>

</head>
<body onload="cargaInicial('{moduloPHP}',{_async},{pagina})">
	<div id="toolsbar" style="display: block;">{topbar}</div>
	
	<main class="principal">
		{barraLateral}
	
		<section class="tablero">
			<div class='filaGeneral filageneral--menu'>
				{headerMenu}
			</div>
			
						
			<div class='tablero__header'>
				<div class="tHeader__derecha">
					<div class='header__fila1'>
						<h3>{H2Titulo}</h3>
					</div>
				</div>
			</div>
			
			<div class="filaGeneral">
				<input id="pagina"  	type="hidden" value="{pagina}" />
				<input id="mod"  		type="hidden" value="{mod}" />
				<input id="origen"  	type="hidden" value="{origen}" />
				
				<div class="formulario__campo">
					<label class="campo__label">{filtrarpor}</label>
					<select class="campo__input" name="campo" id="campo" size="0">
						{htmlCriterios} 
					</select>
				</div>
				<div class="formulario__campo">
					<label class="campo__label"><br></label>
					<input class="campo__input" name="buscar" id="buscar"  type="text"  value="{iguala}" ondblclick="this.value=''" />
				</div>
				<div class="formulario__campo formulario__campo--sin">
					<label class="campo__label" ><br></label>
					<input onclick="filtrarJSON('{moduloPHP}',1)" type="button" class="boton boton--buscar" name="bBuscar" value="Filtrar"/>
				</div>
				<div class="formulario__campo formulario__campo--sin">
					<label class="campo__label" ><br></label>
					<input onclick="editarPost('sistemas','new')" type="button" class="boton boton--nuevo" name="bNuevo" value="Nuevo"/>
				</div>

				<div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo  formulario__campo--filtro" style="margin-left: 50px;">
					<label  class="campo__label">Estado sistema</label>
					<select class="campo__input" id="aux1" name="s.estado" size="0">
						<option value="S" {estadoA}>Activo</option>
						<option value="N" {estadoI}>Inactivo</option>
						<option value=""  {estadoT}>Todos</option>
					</select>
				</div>	
				<div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo  formulario__campo--filtro">
					<label  class="campo__label">Área</label>
					<select class="campo__input" id="aux2" name="s.area" size="0">
						<option value="" >Todos</option>
						{optionAreas}
					</select>
				</div>	
				
				<div class="formulario__campo formulario__campo--orden">
					<label class="campo__label">Ordenar por<br></label>
					<select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="ordenarPor" name="" size="0" style="padding:3px 0 3px 0; ">
						<option value='s.nombre' 		    {_ord0}>Nombre</option>
						<option value='a.area' 		    	{_ord1}>Área</option>
					</select>
				</div>
				<div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo formulario__campo--orden">
					<label class="campo__label">Sentido <br></label>
					<select class="campo__input" id="sentido" name="" size="0" style="padding:3px 0 3px 0; ">
						<option value='DESC' {sentDESC}>▼ Descendente </option>
						<option value='ASC'  {sentASC}>▲ Ascendente</option>
					</select>
				</div>
				<div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo formulario__campo--orden">
					<label class="campo__label">Filas <br></label>
					<select class="campo__input" id="filas" name="" size="0" style="padding:3px 0 3px 0; ">
						<option value='8'  {fl8}>8</option>
						<option value='10' {fl10}>10</option>
						<option value='12' {fl12}>12</option>
						<option value='15' {fl15}>15</option>
						<option value='20' {fl20}>20</option>
					</select>
				</div>	
			</div> 
			
			<div id="contenedorResultados">
				{grillaHTMLTit}
				{grillaHTML}
				<div class="tablero__footer"><ul class="paginacion">{paginacionHTML}</ul></div>
			</div>
		</section>
	</main>
</body>
</html>
