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
		function hojaSist(sisId) {
            url = "sistemas_hoja.php?sisId="+sisId;
            window.open(url);
		}
		
		function hojaSistT(sisId) {
            url = "sistemas_hojaTec.php?sisId="+sisId;
            window.open(url);
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

    <!-- :::::::::::::::::::::: INICIO CAMPOS FORMULARIO :::::::::::::::::::::: -->
    <form id="formularioFicha1" name="formularioFicha1" method="post" action="sistemas_ficha.php"  enctype="multipart/form-data">
		<input type="hidden" name="bd" 		value="adm_sistemas" />
		<input type="hidden" name="idTab" 	value="id" />
		<div class="span-20 enc_mensajes"  style="margin-top:10px;">
		   <h3>identificaci&oacute;n sistema</h3>
		</div>
		

		<div class="span-20" >
			<div class="span-14 datos " >
				<div class="span-3 datos" >
					<label>Id [999]<br/>
					<input class="text sombra span-3" id="id" name="id" type="text" value="{id}"  style="" /></label>
				</div>

				<div class="span-11 last datos" >
					<label>nombre sistema<br/>
					<input class="text sombra span-10"  id="nombre" name="nombre" value="{nombre}"   /></label>
				</div>
				
				<div class="span-3 datos" >
					<label>&aacute;rea<br/>
					<select name="area" class="span-3"   size="0" style="padding-top:2px; padding-bottom:2px;">
						<option value="M" {selectedAM} >Municipal</option>
						<option value="S" {selectedAS} >Salud</option>
						<option value="E" {selectedAE} >Educaci&oacute;n</option>
						<option value="C" {selectedAC} >Sistema comercial</option>
						<option value="A" {selectedAA} >Administrador Sistemas</option>
					</select>
					</label>
				</div>

				<div class="span-4 datos " >
					<label>directorio<br/>
					<input class="text span-4" id="ruta" name="ruta" type="text"  value="{ruta}" /></label>
				</div>

				<div class="span-3 datos" >
					<label>base datos<br/>
					<input class="text span-3" id="dbase" name="dbase" type="text"  value="{dbase}"  /></label>
				</div>
				
				<div class="span-3 datos  last" >
					<label>prefijo tablas<br/>
					<input class="text sombra span-3"  id="prefijoTablas" name="prefijoTablas" value="{prefijoTablas}"   /></label>
				</div>
				
				<div class="span-6 datos " >
					<label>icono<br/>
					<input readonly="readonly" class="text span-6" id="icono"  type="text"  value="{icono}"  /></label>
				</div>
				
				<div class="span-6 prepend-1 datos last" >
					<label>icono desactivado<br/>
					<input readonly="readonly" class="text span-6" id="iconoDesh"  type="text"  value="{iconoDesh}"  /></label>
				</div>
				
				
			</div>
			
			<div class="span-6 last" >
				<div class="span-3 prepend-top"><img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " 		src="../../iconos/{icono}" width="80" height="96" /></div>
				<div class="span-3 prepend-top last"><img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " 	src="../../iconos/{iconoDesh}" width="80" height="96" /></div>
				<div class="span-4 prepend-1"><input class="enviarGrande  span-4"	type="submit"   value="Enviar" /></div>
			</div>

			<div class="span-10 prepend-top">
				<div class="span-10 enc_mensajes"  style="margin-top:10px;"><h3>icono</h3></div>
				<div class="span-10" style="margin-top:5px"><input type="file" name="iconoUpLoad" id="iconoUpLoad" ></div>
			</div>
			<div class="span-10 prepend-top last">
				<div class="span-10 enc_mensajes" style="margin-top:10px;"><h3>icono desactivado</h3></div>
				<div class="span-10" style="margin-top:5px"><input type="file" name="iconodesUpLoad" id="iconodesUpLoad" ></div>
			</div>
		</div>
	</form>
	
	<form id="formularioFicha2" name="formularioFicha2" method="post" action="sistemas_ficha.php"  enctype="multipart/form-data">	
		<input type="hidden" name="bd" 		value="adm_sistemasAdmin" />
		<input type="hidden" name="idTab" 	value="idAdmin" />
		<input type="hidden" name="idAdmin" type="text" value="{id}"  />
		
		<div class="span-20 enc_mensajes prepend-top" >
		   <h3>datos sistema</h3>
		</div>
		
		<div class="span-20 last datos " >
			<label>descripción<br/>
			<textarea name="descripcion"     class="span-20 " wrap="virtual" style="height:90px;">{descripcion}</textarea>
		</div>
		
		<div class="span-20 prepend-top" >		
			<div class="span-7" >
				<img src="../pantallas/{pantalla1}" width="{ancho1}" height="{alto1}" />
			</div>
			<div class="span-7" >
				<img src="../pantallas/{pantalla2}" width="{ancho2}" height="{alto2}" />
			</div>
			<div class="span-6 last" >
				<img src="../pantallas/{pantalla3}" width="{ancho3}" height="{alto3}" />
			</div>
		</div>
		<div class="span-20 ">
			<div class="span-7">
				<div class="span-6 enc_mensajes"  style="margin-top:10px;"><h3>pantalla 1</h3></div>
				<div class="span-6" style="margin-top:5px"><input class="span-6"  type="file" name="pant1UpLoad" id="pant1UpLoad" ></div>
			</div>
			<div class="span-7">
				<div class="span-6 enc_mensajes"  style="margin-top:10px;"><h3>pantalla 2</h3></div>
				<div class="span-6" style="margin-top:5px"><input class="span-6"  type="file" name="pant2UpLoad" id="pant2UpLoad" ></div>
			</div>
			<div class="span-6 last">
				<div class="span-6 enc_mensajes"  style="margin-top:10px;"><h3>pantalla 3</h3></div>
				<div class="span-6" style="margin-top:5px"><input class="span-6"  type="file" name="pant3UpLoad" id="pant3UpLoad" ></div>
			</div>
		</div>
		
		<div class="span-20 ">
			<div class="span-7 datos" >
				<label>desripci&oacute;n pantalla 1<br/>
				<input class="text sombra span-6" id="descPant1" name="descPant1" type="text"  value="{descPant1}" /></label>
			</div>
			<div class="span-7 datos " >
				<label>desripci&oacute;n pantalla 2<br/>
				<input class="text sombra span-6" id="descPant2" name="descPant2" type="text"  value="{descPant2}" /></label>
			</div>
			<div class="span-6 datos last" >
				<label>desripci&oacute;n pantalla 1<br/>
				<input class="text sombra span-6" id="descPant3" name="descPant3" type="text"  value="{descPant3}" /></label>
			</div>
		</div>
		
		<div class="span-20 enc_mensajes prepend-top" >
		   <h3>datos desarrollo</h3>
		</div>
		
		<div class="span-20">
			<div class="span-8 datos" >
				<label>situaci&oacute;n<br/>
				<select name="situacion" class="span-6"   size="0" style="padding-top:2px; padding-bottom:2px;">
					<option value="O" {selectedSO} >Operativo</option>
					<option value="A" {selectedSA} >Requiere actualizaci&oacute;n</option>
					<option value="D" {selectedSD} >En desarrollo</option>
					<option value="B" {selectedSB} >Baja</option>
				</select>
				</label>
			</div>
			<div class="span-5 datos last" >
				<label>version<br/>
				<input class="text sombra span-3"  id="version" name="version" value="{version}"   /></label>
			</div>
		</div>
			
		<div class="span-20 last datos " >
			<label>observaciones<br/>
			<textarea name="observaciones"     class="span-20 " wrap="virtual" style="height:90px;">{observaciones}</textarea>
		</div>
			
		<div class="span-20 prepend-top" >
			<div class="span-16 prepend-top">
				<p>
				<input class="enviarGrande  span-4"	type="submit"   value="Enviar" />
				<!-- <input onclick="hojaSist('{id}')"   class="enviarKhaki   span-4" 		type="button"   value="Ficha Sistema" /> -->
				<input onclick="hojaSistT('{id}')"   class="enviarYellow  span-4" 		type="button"   value="Ficha Tecnica" />
				<input onclick="salir('sistemas')"  class="enviarSalir   span-4 last" 	type="button" value="{salir}"     />
				</p>
			</div>
			<div class="span-4 last">
				<a href="sistemas_modulos.php?idSistema={id}" target="_top" style="text-decoration:none; ">
					<img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="../../images/icon-modulos.png" width="80" height="96" alt="Licencias"/>
				</a>
			</div>
		</div>
	</form>

    <div class="span-20 fondo_inv prepend-top" >
        {barra_pie_mensajes}
    </div>

</div>
</body>
</html>