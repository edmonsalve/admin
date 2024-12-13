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

    <!-- :::::::::::::::::::::::::::::::::::::::::::: CALENDARIO DE ENTRADA ::::::::::::::::::::::::::::::::::::::: -->
    <link type="text/css" rel="stylesheet" href="/externos/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
    <script type="text/javascript" src="/externos/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20120609"></script>

    <script type="text/javascript">
        function verSistemas() {
           divTram = document.getElementById('divSistemas').style.display;
           if (divTram == 'none') {
               document.getElementById('divSistemas').style.display = 'block';
               document.getElementById('btnSistemas').value="Ocultar Sistemas";
               } else {
               document.getElementById('divSistemas').style.display = 'none';
               document.getElementById('btnSistemas').value="Ver Sistemas";
           }
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
    <form id="formularioFicha" name="formularioFicha" method="post" action="clientes_ficha.php">
        <input type="hidden" name="idCliente" id="idCliente" value="{id}" />

	<div class="span-20 enc_mensajes"  style="margin-top:10px;">
	   <h3>datos cliente</h3>
	</div>
    <div class="span-20" >
		<div class="span-3 datos" >
			<label>rut [99999999-9]<br/>
			<input onblur="valrut('rut','dv')" class="text sombra span-3" id="rut" name="rut" type="text" value="{rut}"  style="" /></label>
		</div>

		<div class="span-12 datos " >
			<label>cliente<br/>
			<input class="text sombra span-12"  id="cliente" name="cliente" value="{cliente}"   /></label>
		</div>
		<div class="span-12 datos " >
			<label>observaciones<br/>
			<input class="text sombra span-12"  id="cliente" name="cliente" value="{cliente}"   /></label>
		</div>
	</div>
	
	<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px;">
	   <h3>datos contrato</h3>
	</div>
	
	<div class="span-20" >
		<div class="span-4 datos " >
			<label>fecha contrato<br/>
			<input class="text sombra span-4" id="inicioContrato" name="inicioContrato" type="date" value="{inicioContrato}"  /></label>
		</div>
		<div class="span-4 datos " >
			<label>fecha vcto. cont.<br/>
			<input class="text sombra span-4" id="venctoContrato" name="venctoContrato" type="date" value="{venctoContrato}"  /></label>
		</div>
		<div class="span-3 datos " >
			<label>monto mensual<br/>
			<input class="text sombra span-3"  id="montoMensual" name="montoMensual"  type="text"value="{montoMensual}"   /></label>
		</div>
		<div class="span-9 last datos " >
			<label>contacto adm.<br/>
			<input class="text sombra span-9"  id="contactoAdmin" name="contactoAdmin" type="text" value="{contactoAdmin}"  /></label>
		</div>
		<div class="span-20 last datos " >
			<label>observaciones<br/>
			<textarea name="observContrato"     class="span-20 " wrap="virtual" style="height:60px;">{observContrato}</textarea>
		</div>
	</div>
	
	<div class="span-20 enc_mensajes prepend-top"  style="margin-top:10px;">
	   <h3>administracion sistema cliente</h3>
	</div>
	
	<div class="span-20" >
		<div class="span-10 datos " >
			<label>administrador sistemas área municipal<br/>
			<input class="text sombra span-10" id="administrador1" name="administrador1" type="text" value="{administrador1}"  /></label>
		</div>
		<div class="span-10 datos last" >
			<label>email<br/>
			<input class="text sombra span-10" id="emailAdm1" 	name="emailAdm1" type="text" value="{emailAdm1}"  /></label>
		</div>
		<div class="span-10 datos " >
			<label>administrador sistemas área salud<br/>
			<input class="text sombra span-10" id="administrador2" name="administrador2" type="text" value="{administrador2}"  /></label>
		</div>
		<div class="span-10 datos last" >
			<label>email<br/>
			<input class="text sombra span-10" id="emailAdm2" 	name="emailAdm2" type="text" value="{emailAdm2}"  /></label>
		</div>
		<div class="span-10 datos " >
			<label>administrador sistemas área educación<br/>
			<input class="text sombra span-10" id="administrador3" name="administrador3" type="text" value="{administrador3}"  /></label>
		</div>
		<div class="span-10 datos last" >
			<label>email<br/>
			<input class="text sombra span-10" id="emailAdm3" 	name="emailAdm3" type="text" value="{emailAdm3}"  /></label>
		</div>
	</div>
	
	<div class="span-20 enc_mensajes"  style="margin-top:10px;">
	   <h3>datos conexi&oacute;n servidor cliente</h3>
	</div>
    <div class="span-20" >
        <div class="span-2 datos " >
            <label>preFijo <br/>
            <input class="text span-2" id="prefijoBD" name="prefijoBD" type="text"  value="{prefijoBD}" /></label>
        </div>

        <div class="span-4 datos " >
            <label>servidor BD<br/>
            <input class="text span-4" id="servidorBD" name="servidorBD" type="text"  value="{servidorBD}"  /></label>
        </div>
		
		<div class="span-2 datos " >
			<label>puerto BD<br/>
			<input class="text span-2"  id="puertoSQL" name="puertoSQL" type="text"  value="{puertoSQL}"   /></label>
		</div>
		
		<div class="span-3 datos " >
			<label>user BD<br/>
			<input class="text span-3"  id="usrSQL" name="usrSQL" type="text"  value="{usrSQL}"   /></label>
		</div>
		
		<div class="span-4 datos " >
			<label>pass BD<br/>
			<input class="text span-4"  id="passSQL" name="passSQL" type="text"  value="{passSQL}"   /></label>
		</div>
		
		<div class="span-4 prepend-1 datos last" >
			<label>ver PHP<br/>
			<input class="text span-4"  id="phpVer"  name="phpVer"  type="text"  value="{phpVer}" style="background-color:lime;"   /></label>
		</div>
		
		<div class="span-4 datos  prepend-2" >
            <label>servidor producci&oacute;n<br/>
            <input class="text span-4" id="servidorBD" name="servidorBD" type="text"  value="{servidorBD}"  /></label>
        </div>
		
		<div class="span-2 datos" >
			<label>puerto SSH<br/>
			<input class="text span-2"  id="puertoSSH" name="puertoSSH" type="text"  value="{puertoSSH}"   /></label>
		</div>
		
		<div class="span-3 datos" >
			<label>user SSH<br/>
			<input class="text sombra span-3"  id="usrSSH" name="usrSSH" type="text"  value="{usrSSH}"   /></label>
		</div>
		
		<div class="span-3 datos " >
			<label>pass SSH<br/>
			<input class="text sombra span-3"  id="passSSH" name="passSSH" type="text"  value="{passSSH}"   /></label>
		</div>
		
		<div class="span-3 prepend-2 datos last" >
			<label>puerto HTTP<br/>
			<input class="text sombra span-2"  id="puertoHTTP" name="puertoHTTP" type="text"  value="{puertoHTTP}"   /></label>
		</div>
		
		<div class="span-16 datos  prepend-2" >
            <label>url cliente<br/>
            <input class="text span-9" id="url" name="url" type="text"  value="{url}"  /></label>
        </div>
		
		<div class="span-20 enc_mensajes"  style="margin-top:10px;">
		   <h3>datos licencia</h3>
		</div>
		<div class="span-20" >
			<div class="span-7 datos " >
				<label>licencia<br/>
				<input class="text sombra span-7" id="licencia" name="licencia" type="text"  value="{licencia}"  /></label>
			</div>
			<div class="span-4 datos " >
				<label>fecha vcto.<br/>
				<input class="text sombra span-4" id="vencimientoLic" name="vencimientoLic" type="date" value="{vencimientoLic}"  /></label>
			</div>
			
			<div class="span-2 datos " >
				<label>Muni<br/>
				<select name="m" class="span-2"   size="0" style="padding-top:2px; padding-bottom:2px;">
                		<option value="S" {selectedMS} >Si</option>
                		<option value="N" {selectedMN} >No</option>
                	</select>
				</label>
			</div>
			<div class="span-2 datos " >
				<label>Salud<br/>
				<select name="s" class="span-2"   size="0" style="padding-top:2px; padding-bottom:2px;">
                		<option value="S" {selectedSS} >Si</option>
                		<option value="N" {selectedSN} >No</option>
                	</select>
				</label>
			</div>
			<div class="span-2 datos " >
				<label>Educ.<br/>
				<select name="e" class="span-2"   size="0" style="padding-top:2px; padding-bottom:2px;">
                		<option value="S" {selectedES} >Si</option>
                		<option value="N" {selectedEN} >No</option>
                	</select>
				</label>
			</div>
			<div class="span-2 datos " >
				<label>Admin.<br/>
				<select name="a" class="span-2"   size="0" style="padding-top:2px; padding-bottom:2px;">
                		<option value="S" {selectedAS} >Si</option>
                		<option value="N" {selectedAN} >No</option>
                	</select>
				</label>
			</div>
        </div>
    </div>
	


    <div class="span-20 prepend-top" >
		<div class="span-16 prepend-top">
			<br />
			<p>
			<input class="enviarGrande  span-4" type="submit" value="Enviar" />
			<input onclick="salir('clientes')"          class="enviarSalir   span-4" type="button" value="{salir}"     />
			<input onclick="verSistemas()"              class="botonAuxiliar span-4" type="button" value="Sistemas Cliente"  id="btnSistemas"   />
			</p>
		</div>
		<div class="span-4 last">
			<a href="licencia_calculo.php?idCliente={id}" target="_top" style="text-decoration:none; ">
				<img style="float:left; padding-left:12px; padding-right:2px; padding-bottom:12px; " src="../../images/icon-licencias.png" width="80" height="96" alt="Licencias"/>
			</a>
		</div>
    </div>

    
    <!-- ::::::::::::::::::::::::: DIV sistemas ::::::::::::::::::::::::::: -->
    <div id="divSistemas" class="span-21 prepend-top " style="display:{dispCont}; height:600px; overflow:auto; ">
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
	</form>

    <div class="span-20 fondo_inv prepend-top" >
        {barra_pie_mensajes}
    </div>

    <!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
    </div>
</div>

</body>
</html>