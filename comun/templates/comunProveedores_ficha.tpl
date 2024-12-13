<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{lang}">
<head>
	<title>{H2Sistema}</title>

	<link rel="shortcut icon" href="m.ico" />
	
	<link rel="stylesheet" href="/styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/print.css"  type="text/css" media="print" />
	<link rel="stylesheet" href="/styles/red-m.css" type="text/css"  media="screen, projection" />

	<script src="/js/script_utiles.js" type="text/javascript" ></script>
	<script src="/js/red-m.js" type="text/javascript" ></script>
	<script src="/js/loadAjax.js" type="text/javascript" ></script>
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
			<h2 class="titulosBarra" >{H2Titulo}</h2>
		</div>
		
		<form id="formulario" name="formulario" method="post" action="comunProveedores_ficha.php"  >
		<!-- ::::::::::::::::::::::::: CAMPOS OCULTOS  ::::::::::::::::::::::::: -->	
		<input id="IdProvee" name="IdProvee" type="hidden" value="{_Idprovee}" >     
		
		<!-- :::::::::::::::::::::: INICIO CAMPOS FORMULARIO :::::::::::::::::::::: -->	
		<div id="divContenido" class="span-20" >	
			<div class="span-20 last datos">
                <div class="span-16 last">
    				<div class="span-2 datos">
    					<label id="rut">{rut}<br/>
                        <input {readonly} onchange="valrut('1rut','1dv')" class="text sombra" id="1rut" name="rut" type="text" value="{_rut}" style="width:65px; text-align:right; " />
    					</label>
                    </div>
                    <div class="span-2 append-12 last datos">
                        <label id="dv" style="">{dv}<br />
                        <input readonly="readonly" class="text sombra" id="1dv" name="dv" type="text" value="{_dv}" style="width:22px; " />
    				    </label>
                    </div>
				</div>
                
				<div class="span-8 append-8 last datos">
					<label id="razonSocial">{razonSocial}<br/><input class="text sombra" id="1razonSocial" name="razonSocial" type="text" value="{_razonSocial}" /></label>
				</div>
				
                
                <div class="span-8 append-8 last datos" >
					<label id="direccion">{direccion}<br/><input class="text sombra" id="1direccion" name="direccion" type="text" value="{_direccion}" style="width:335px; "/></label>
                </div>
                
                <div class="span-5 datos">
					<label id="sector">{sector}<br/><input class="span-4 text sombra" id="0sector" name="sector" type="text" value="{_sector}" /></label>
				</div>
                 
                 
                <div class="span-6 datos">
                        <label id="labUn" style="">{comunaId}<br />
                        <select class="span-5 text sombra" id="comunaId" name="comunaId" size="0" >
                            {OptionComunaHTML}
                        </select>
            		    </label>
                </div> 
                              
                <div class="span-5  datos">
					<label id="telefono1">{telefono1}<br/><input class="span-4 text sombra" id="0telefono1" name="telefono1" type="text" value="{_telefono1}" /></label>
				</div> 
                
                <div class="span-5  datos">
					<label id="telefono2">{telefono2}<br/><input class="span-4 text sombra" id="0telefono2" name="telefono2" type="text" value="{_telefono2}" /></label>
				</div> 
                
                <div class="span-6  datos">
					<label id="fax">{fax}<br/><input class="span-6 text sombra" id="0fax" name="fax" type="text" value="{_fax}" /></label>
				</div> 
                
				<div class="span-10 datos">
					<label id="email">{email}<br/><input class="span-6 text sombra" id="0email" name="email" type="text" value="{_email}" /></label>
				</div>
                
                <!-- Datos RL -->
                <div class="span-8 append-8 last datos"><h3 style="color:#346FAA;margin-top:3px;font-weight:bold;">Datos Contacto</h3> </div>  
                
                <div class="span-16 last">
    				<div class="span-10 datos">
    					<label id="contacto">{contacto}<br/>
                        <input class="span-9 text sombra" id="0contacto" name="contacto" type="text" value="{_contacto}"  />
    					</label>
                    </div>
                 
                <div class="span-6 datos">
                        <label id="labUn" style="">{condPago}<br />
                        <select class="span-5 text sombra" id="condPago" name="condPago" size="0" >
                            {OptionCpagoHTML}
                        </select>
            		    </label>
                </div> 
                
                
                <div class="span-6 datos">
				 	<label id="limiteCredito">{limiteCredito}<br/><input class="span-6 text sombra" id="0limiteCredito" name="limiteCredito" type="text" value="{_limiteCredito}" /></label>
				</div>
                
                <div class="span-8  last datos">
					<label id="giroComercial">{giroComercial}<br/><input class="span-8 text sombra" id="0giroComercial" name="giroComercial" type="text" value="{_giroComercial}" /></label>
				</div>
			</div>
		</div>
		<!-- ::::::::::::::::::::::::: FINAL CAMPOS FORMULARIO :::::::::::::::::::::::::::
            "campoRutRL"            
    		"campoDVRL"             
            "campoNombreRL"         
            "campoDireccionRL"      
            "campoTelefonoRL"       
            "campoEmailRL"          
         -->	
	
		
		<div class="span-10 " >
            <br />
			<p>
			<input onclick="validar('formulario')" class="enviar" type="button" value="{enviar}" />
			<input onclick="ficha('comunProveedores','new')" type="button" class="enviar" name="bNuevo" value="{nuevo}" />
			<input onclick="salir('comunProveedores')" class="enviar" type="button" value="{salir}" style="width:140px; " />
			</p>
		</div>
		</form>
		
		<div class="span-20 fondo_inv">
			{barra_pie_mensajes}
		</div>
	</div>
	<!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>

</body>
</html>
