<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{lang}">
<head>
	<title>&#123;ReD-M&#125; - {H2Sistema}</title>

	<link rel="shortcut icon" href="m.ico" />
	
	<link rel="stylesheet" href="/styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/print.css"  type="text/css" media="print" />
	<link rel="stylesheet" href="/styles/red-m.css" type="text/css"  media="screen, projection" />

	<script src="/js/script_utiles.js" type="text/javascript" ></script>
	<script src="/js/red-m.js" type="text/javascript" ></script>
	<script src="/js/loadAjax.js" type="text/javascript" ></script>
    
    <script type="text/javascript" >
        function editarModulo(modId) {

        }
        
        function leeModulos() {
            sisId  = document.getElementById('sistema').value;
            serial = document.getElementById('serial').value;
            url = "clientes_modulos.php?sisId="+sisId+"&serial="+serial;
            location.href=url;
        }
    </script>
</head>

<body>

<div id="toolsbar">
	{topbar}
</div>


<div class="container" style="float:left;" >
    <input type="hidden" id="serial" value="{serial}" />
    
	<!-- ::::::::::::::::: Inicio Area de Lateral :::::::::::::::::::: -->
	<div class="span-4 ">
		{lateral}
	</div>
	<!-- ::::::::::::::::: Final Area de Lateral ::::::::::::::::::::: -->
	
	<!-- ::::::::::::::::: Inicio Area de Trabajo :::::::::::::::::::: -->
	<div class="span-20 last ">
		<div id="divCont" class="span-20" style="height:50px; margin-bottom:10px; background: url(/images/tit_tablas.png) no-repeat;">	
			<h2 class="titulosBarra" >{H2Modulo}</h2>
		</div>
		
        <!-- :::::::::::::::::::::: INICIO CAMPOS BUSQUEDA :::::::::::::::::::::: -->
		<div class="span-20" style="background-color:#fafafa; padding-top:5px; padding-bottom:5px;" >
			<div class="span-8" >
				<select onchange="leeModulos()" class="span-8" name="sistema" id="sistema" size="0" style="padding:3px 0 3px 0; ">
                    <option value="0">Seleccione Sistema</option>
                    {sistemasHTML}
				</select>
			</div>
		

            <div class="span-2 last" >
                <input onclick="location.href='../index.php'" class="enviar" type="button" value="{salir}" style="width:70px; margin-top:8px; " />
		    </div>  
        </div>
		<!-- :::::::::::::::::::::: FINAL CAMPOS BUSQUEDA :::::::::::::::::::::: -->
        	
		<div id="divCont" class="span-24" >	
            
			<!-- :::::::::::::::::::::::::: IMODULOS BASE :::::::::::::::::::::::: -->
			<div class="span-11" >
                <div class="span-10 centro " ><h3>Server</h3></div>
                <div class="span-8 centro enc_mensajes" ><h3>PHP</h3></div>
                <div class="span-2 centro enc_mensajes" ><h3>ID</h3></div>
				{modulosHTML}
			</div>
            
            
			<!-- :::::::::::::::::::::::::: IMODULOS CLIENTE ::::::::::::::::::::: -->
			<div class="span-11 last" >
                <div class="span-10 centro" ><h3>Cliente</h3></div>
                <div class="span-8 centro enc_mensajes" ><h3>PHP</h3></div>
                <div class="span-2 centro enc_mensajes" ><h3>ID</h3></div>
				{modulosCliHTML}
			</div>
			
		</div>

		<div class="span-22 fondo_inv">
			{barra_pie_mensajes}
		</div>
	</div>
	<!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>

</body>
</html>
