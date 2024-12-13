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
		<div id="divCont" class="span-20" style="height:50px; margin-bottom:10px; background: url(/images/tit_tablas.png) no-repeat;">	
			<h2 class="titulosBarra" >{H2Titulo}</h2>
		</div>
		
        <!-- :::::::::::::::::::::: INICIO CAMPOS BUSQUEDA :::::::::::::::::::::: -->
		<div class="span-20" style="background-color:#fafafa; padding-top:5px; padding-bottom:5px;" >
			<div class="span-3" style="text-align:right;"><h3 style="margin-top:12px;">{buscarpor}</h3></div>

			
			<div class="span-4" >
				<select name="campo" id="campo" size="0" style="width:150px;  padding:3px 0 3px 0; ">
					<option value="{campoOrd}" >{cargo}</option>
					<option value="{IdCampo}" >{IdCargo}</option>
				</select>
			</div>
			<div class="span-1" style="text-align:center;"> <h3 style="margin-top:12px;">=</h3></div>
			<div class="span-5">
				<input class="text span-5" name="buscar" id="buscar"  type="text"  value="" />
			</div>
			<div class="span-2" >		
				<input onclick="buscarTab('{phpFile}','{tablaDB}')"   type="button" class="enviar" name="bBuscar" value="buscar" style="width:70px; margin-top:8px; "/>
			</div>
            
            <div class="span-2 " >		
				<input onclick="imprimirTabla('{dBase}','{tablaDB}','{campoOrd}','{TInforme}')"   type="button" class="enviar" name="bBuscar" value="imprimir" style="width:70px; margin-top:8px; "/>
			</div>

            <div class="span-2 last" >
                <input onclick="salir('../index_main')" class="enviar" type="button" value="{salir}" style="width:70px; margin-top:8px; " />
		    </div>  
        </div>
		<!-- :::::::::::::::::::::: FINAL CAMPOS BUSQUEDA :::::::::::::::::::::: -->
        	
		<div id="divCont" class="span-20" >	
			<!-- :::::::::::::::::::::::::: INICIO SALIDA TABLA :::::::::::::::::::::::: -->
			<div class="span-10" >
				{tablaHTML}
			</div>
			<!-- :::::::::::::::::::::: FINAL SALIDA TABLA :::::::::::::::::::::: -->
			
			<form id="ftabla" name="ftabla" method="post" action="{phpFile}.php" >
				<!-- :::::::::::::::::::::: CAMPOS OCULTOS  :::::::::::::::::::::: -->	
				<input id="{IdCampo}" name="{IdCampo}" type="hidden" value="{campoId}" />  
				
				<!-- ::::::::::::::::::::::::: INICIO TABLA  :::::::::::::::::::::::: -->
				<div id="divTabla"  class="span-9 last"  style="border-left:10px solid #eeeeee; padding-left:15px" >
					<div class="span-10 datos">
						<label id="cargo">{cargo}<br/><input class="text sombra" id="1cargo" name="cargo" type="text" value="{_cargo}" /></label>
					</div>
                    
                                       
					<div class="span-10 "><br /><br /><br /></div>
					<div class="span-10 " >
						<p>
						<input onclick="validar('ftabla')" class="enviar" type="button" value="{enviar}" />
						<input onclick="salir('{phpFile}')" class="enviar" type="button" value="{limpiar}" style="width:140px; " />
						</p>
					</div>
				</div>
				<!-- :::::::::::::::::::::::::  FINAL TABLA  :::::::::::::::::::::::: -->
			</form>
			
		</div>
		

		
		<div class="span-20 fondo_inv">
			{barra_pie_mensajes}
		</div>
	</div>
	<!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>

</body>
</html>
