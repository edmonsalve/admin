<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{lang}">
<head>
	<title>{H2Sistema}</title>
	<link rel="shortcut icon" href="m.ico" />
	<link rel="stylesheet" href="/styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/print.css"  type="text/css" media="print" />
	<link rel="stylesheet" href="/styles/red-m.css" type="text/css"  media="screen, projection" />

	<script src="/js/red-m.js"    type="text/javascript" ></script>
	<script src="/js/loadAjax.js" type="text/javascript" ></script>

    <script type="text/javascript">
		function editarMod(modulo,sistema) {
			url = 'sistemas_modulos.php?idSistema='+sistema+'&modulo='+modulo;
        	location.href=url;
		}
		
		function nuevo(modulo,sistema) {
			url = 'sistemas_modulos.php?idSistema='+sistema+'&modulo='+modulo;
        	location.href=url;
		}
    </script>
</head>
<body>

<div id="toolsbar">{topbar}</div>
<div class="container" style="float:left;" >

<!-- ::::::::::::::::: Inicio Area de Lateral :::::::::::::::::::: -->
<div class="span-4 ">{lateral}</div>
<!-- ::::::::::::::::: Final Area de Lateral ::::::::::::::::::::: -->

<!-- ::::::::::::::::: Inicio Area de Trabajo :::::::::::::::::::: -->
<div class="span-20 last ">
    <div id="divCont" class="span-20" style="height:50px; margin-bottom:10px; ">	
		<h2 class="titulosBarra" >{H2Titulo}</h2>
	</div>

	<div class="span-20 enc_mensajes"  style="margin-top:10px;">
	   <h3>identificaci&oacute;n sistema</h3>
	</div>
	
	<div class="span-20" >
		<div class="span-3 datos" >
			<label>Id [999]<br/>
			<input {ronly} class="text sombra span-3" id="id" name="id" type="text" value="{id}"  style="" /></label>
		</div>

		<div class="span-12 datos " >
			<label>nombre sistema<br/>
			<input {ronly} class="text sombra span-12"  id="nombre" name="nombre" value="{nombre}"   /></label>
		</div>
	</div>
	
	<div class="span-20 prepend-top">
	   {grillaTitHTML}
	</div>
	<div class="span-20"  style="height:350px; overflow:auto;">
	   {grillaHTML}
	</div>
		
	
	<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px; display:{dispEdit}">
	   <h3>datos Modulos</h3>
	</div>
	
	<form id="formularioFicha" name="formularioFicha" method="post" action="sistemas_modulos.php?idSistema={id}&modulo={idModulo}"  >
		<input type="hidden" name="idsistema" value="{id}" />
		<input type="hidden" name="id" value="{idModulo}" />
		<!-- ::::::::::::::::::::::::: EDICION DE MODULOS  :::::::::::::::::::::::: -->
		<div id="divTabla"  class="span-20 last"  style="display:{dispEdit}" >
			<div class="span-2 datos">
				<label id="idmod">Id<br/><input {rIdModu} class="span-2 text sombra" id="idModulo" name="idModulo" type="text" value="{idModulo}" /></label>
			</div>
			<div class="span-9 datos">
				<label id="sistema">Modulo<br/><input class="span-9 text sombra" id="modulo" name="modulo" type="text" value="{modulo}" /></label>
			</div>
			<div class="span-7 datos">
				<label id="ruta">Tipo<br/>
				<select  class="span-7" name="tipo" id="tipo" size="0" style="padding:3px 0 3px 0; ">
					{optionEstMenu}
				</select>
				</label>
			</div>
			
			<div class="span-2 datos last">
				<label id="ruta">Target<br/>
				<select class="span-2" name="target" id="target" size="0" style="padding:3px 0 3px 0; ">
					<option value="T" {targetT}>Top</option>
					<option value="B" {targetB}>Blank</option>
				</select>
				</label>
			</div>
			
			<div class="span-6 datos">
				<label id="ruta">.php<br/><input class="span-6 text sombra" id="php" name="php" type="text" value="{php}" /></label>
			</div>
			<div class="span-14 last datos">
				<label id="ruta">Parametros<br/><input class="span-14 text sombra" id="parametros" name="parametros" type="text" value="{parametros}" /></label>
			</div>
			
		
			
			<div class="span-4 datos">
				<label>Versi&oacute;n Sistema<br/>
				<select class="span-4" name="sistemaVer" id="sistemaVer" size="0" style="padding:3px 0 3px 0; ">
					<option value="1" {sistemaVer1}>Sistemas Antiguos</option>
					<option value="2" {sistemaVer2}>Sistemas Nuevos</option>
				</select>
				</label>
			</div>
			<div class="span-4 datos last">
				<label id="ruta">Estado<br/>
				<select class="span-4" name="estado" id="estado" size="0" style="padding:3px 0 3px 0; ">
					<option value="1" {estado1}>Habilitado</option>
					<option value="0" {estado0}>Deshabilatado</option>
				</select>
				</label>
			</div>

			<div class="span-4 prepend-8 last">
				</br>
				<input class="enviarBuscar span-4" 		type="submit" value="Enviar" />
			</div>
		</div>
	</form>

	<div class="span-20 prepend-top">
		<input onclick="nuevo('new',{id})"	class="enviarGrande span-4" type="button" value="Nuevo" />
		<input onclick="salir('sistemas')"  class="enviarSalir  span-4" type="button" value="{salir}"     />
	</div>

		
	<div class="span-20 fondo_inv prepend-top" >
        {barra_pie_mensajes}
    </div>	
		
</div>
</body>
</html>