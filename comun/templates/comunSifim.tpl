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
        
        <div class="span-20 enc_mensajes" style="margin-top:30px;">
                <h3>Datos Generales</h3>
        </div>
        
        <form id="ftabla" name="ftabla" method="post" action="comunSifim.php" >	
            <div id="divCont" class="span-20" >			
                <input name="Id" type="hidden" value="1" />  
                
                               
                <div class="span-3 datos ">
                    <label id="CodCliente">{CodCliente}<br/>
                    <input class="text span-2 sombra" id="1CodCliente" name="CodCliente" type="text" value="{_CodCliente}"  />
                    </label>
                </div>
                
                <div class="span-2  datos">
                    <label id="CodArea">{CodArea}<br/>
                    <input class="text span-1 sombra" id="1CodArea" name="CodArea" type="text" value="{_CodArea}"  />
                    </label>
                </div>
                
                <div class="span-5 datos ">
                    <label id="ip">{ip}<br/>
                    <input class="text span-3 sombra" id="1ip" name="ip" type="text" value="{_ip}"  />
                    </label>
                </div>
                
                <div class="span-3 datos">
                    <label id="codComuna">{codComuna}<br/>
                    <input class="text span-2 sombra" id="1codComuna" name="codComuna" type="text" value="{_codComuna}"  />
                    </label>
                </div>
                
                <div class="span-4 last datos ">
                    <label id="webServiceUserDB">{webServiceUserDB}<br/>
                    <input class="text span-3 sombra" id="1webServiceUserDB" name="webServiceUserDB" type="text" value="{_webServiceUserDB}"  />
                    </label>
                </div>
                
                <div class="span-5 datos ">
                    <label id="webServicePassDB">{webServicePassDB}<br/>
                    <input class="text span-4 sombra" id="1webServicePassDB" name="webServicePassDB" type="text" value="{_webServicePassDB}"  />
                    </label>
                </div>
                
                <div class="span-5 datos ">
                    <label id="webServiceDB">{webServiceDB}<br/>
                    <input class="text span-4 sombra" id="1webServiceDB" name="webServiceDB" type="text" value="{_webServiceDB}"  />
                    </label>
                </div>
                
                <div class="span-8 last datos ">
                    <label id="comunDB">{comunDB}<br/>
                    <input class="text span-4 sombra" id="1comunDB" name="comunDB" type="text" value="{_comunDB}"  />
                    </label>
                </div>
                
                <div class="span-5 datos ">
                    <label id="comunUser">{comunUser}<br/>
                    <input class="text span-4 sombra" id="1comunUser" name="comunUser" type="text" value="{_comunUser}"  />
                    </label>
                </div>
                
                <div class="span-8 last datos ">
                    <label id="comunPass">{comunPass}<br/>
                    <input class="text span-4 sombra" id="1comunPass" name="comunPass" type="text" value="{_comunPass}"  />
                    </label>
                </div>
                
            </div>
           
            
             <div class="span-20" style="margin-top:15px;">
                <div class="span-4">
                    <input onclick="validar('ftabla')" class="enviar" type="button" value="{enviar}" style="width:140px; " />
                </div>
                <div class="span-4">
                    <input onclick="salir('../index_main')" class="enviar" type="button" value="{salir}" style="width:140px; " />
                </div>
            </div>
            
            <div class="span-20 fondo_inv">
                {barra_pie_mensajes}
            </div>
        </div>
        
        <!-- ::::::::::::::::::::::::: INICIO TABLA  :::::::::::::::::::::::: -->
       
        <!-- :::::::::::::::::::::::::  FINAL TABLA  :::::::::::::::::::::::: -->
    </form>

    
    
    <!-- ::::::::::::::::: Final Area de Trabajo :::::::::::::::::::: -->
</div>

</body>
</html>
