<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<script type="text/javascript">
    
    document.addEventListener("DOMContentLoaded", function() {
        const openDivs = [];
        document.querySelectorAll('div').forEach(function(div) {
            const style = window.getComputedStyle(div);
            if (style.display !== 'none' && div.offsetParent !== null) {
                openDivs.push(div.id || '(sin id)');
            }
        });
        console.log('Divs visibles:', openDivs);

        const areaSelect = document.getElementById('areaId');
        if (areaSelect) {
            try {
                const savedArea = localStorage.getItem('dmuni_areaId');
                const valueExists = Array.from(areaSelect.options).some(option => option.value === savedArea);

                if (savedArea && valueExists) {
                    areaSelect.value = savedArea;
                }

                areaSelect.addEventListener('change', function() {
                    localStorage.setItem('dmuni_areaId', this.value);
                });
            } catch (e) {
                console.warn('No se pudo acceder a localStorage para guardar el area:', e);
            }
        }
    });
    
    document.querySelectorAll("div").forEach(div => {
        if (!div.id && !div.className) {
        div.remove(); // Elimina los div que no tienen id ni class
        }
    });

    // Redirigir a la ventana principal si está en un iframe
    if (window.top !== window.self) {
        window.top.location = window.location.href;
    }
</script>
<html lang="es-ES">
<head>
	<meta name="author" content="Edmundo Monsalve" />
	<title>{titulo}</title>
	<link rel="icon" type="image/png" href="/images/icoDC.png">


	<!-- Fuente -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="/styles/normalize.css" 	  			type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_globales.css" 	  			type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_topbar.css" 	  			type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_contenedores.css" 			type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_formularios.css"  			type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_formulariosFlotantes.css"  	type="text/css" media="screen, projection" />
    
	<!-- sweet alert2 -->
	<link rel="stylesheet" href="/styles/swal2/sweetalert2.min.css">	
	<script src="/js/sweetalert2.all.min.js"></script>
	
	<!-- chartJS -->
	<script src="/js/chart.js"							type="text/javascript"></script>
	<script src="/js/chartjs-plugin-datalabels.min.js" 	type="text/javascript"></script>

	<script src="/js/dcode.js"    			type="text/javascript"></script>
	<script src="/js/loadAjax.js" 			type="text/javascript"></script>
	<script src="/js/jquery-3.6.3.min.js"	type="text/javascript"></script>

	<script src="js/scriptsUtiles.js" type="text/javascript"></script>

    <script type="text/javascript">
        function checkKey(key) {
            var unicode
            if (key.charCode)
            {unicode=key.charCode;}
            else
            {unicode=key.keyCode;}

            if (unicode == 13){
                document.getElementById('flogin').submit();
            }
        }

        function validaIP() {
            const params = new URLSearchParams(window.location.search);
            const errLogin  = params.get('err');
            
            let titulo      = '';
            let texto       = '';
            let icon        = '';
            let textBoton   = '';
            let displayCod2FA  = 'none'

            switch (errLogin) {
                case '90':
                    titulo      = 'Error de Acceso';
                    texto       = 'Su usuario no registrado en el sistema, contacte al administrador';
                    icon        = 'error';
                    textBoton   = 'OK';
                    displayCod2FA  = 'none';
                    break;
                case '91':
                    titulo      = 'Error de Acceso';
                    texto       = 'Su usuario se encuentra temporalmente inactivo, contacte al administrador';
                    icon        = 'error';
                    textBoton   = 'OK';
                    displayCod2FA  = 'none';
                    break;
                case '92':
                    titulo      = 'Error de Acceso';
                    texto       = 'Su usuario a sido bloqueado, contacte al administrador';
                    icon        = 'error';
                    textBoton   = 'OK';
                    displayCod2FA  = 'none';
                    break;
                case '93':
                    titulo      = 'Error de Acceso';
                    texto       = 'Su usuario a sido bloqueado, por superar el número de intentos fallidos permitidos';
                    icon        = 'error';
                    textBoton   = 'OK';
                    displayCod2FA  = 'none';
                    break;
                case '94':
                    titulo      = 'Error de Acceso';
                    texto       = 'Usuario o clave en blanco, por favor verifique';
                    icon        = 'error';
                    textBoton   = 'OK';
                    displayCod2FA  = 'none';
                    break;
                case '12':
                    titulo      = 'Error de Acceso';
                    texto       = 'No tiene permitido acceder desde este dispositivo o IP';
                    icon        = 'error';
                    textBoton   = 'OK';
                    displayCod2FA  = 'none';
                    break;
                case '11':
                    titulo      = 'Error de Acceso';
                    texto       = 'Codigo 2FA incorrecto';
                    icon        = 'error';
                    textBoton   = 'OK';
                    displayCod2FA  = 'block';
                    break;
                case '10':
                    titulo      = 'Requiere 2FA';
                    texto       = 'Se esta conectado de una IP no autorizada, se ha envíado un codigo a su email o SMS a su teléfono';
                    icon        = 'warning';
                    textBoton   = 'OK';
                    displayCod2FA  = 'block';
                    break;
                case '1': 
                    titulo      = 'Error de Acceso';
                    texto       = 'Clave de usuario incorrecta';
                    icon        = 'error';
                    textBoton   = 'OK';
                    displayCod2FA  = 'none';
                    break;
            }

            document.getElementById('divCod2FA').style.display = displayCod2FA;

            if (parseInt(errLogin) > 0) {
                Swal.fire({
                    title: titulo,
                    text: texto,
                    icon: icon,
                    confirmButtonText: textBoton
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = 'logout.php'; 
                    }
                });
            }
        }        
    </script>	
</head>
<body onload="cierreSes()" >
    <main class="portal" style="background-image: url('/fondos/fondo{_nroFondo}.jpg'); align-items: flex-end; padding-right: 100px; box-sizing: border-box;">
        <div id="divContenido" class="contenido" >
            <form class="formulario-flotante" name="flogin" action="login.php" method="post"  style="background-color: rgba(193, 233, 252, 0.95); padding-bottom: 30px; ">
                <div id="divLogo" class="formularioFL-img" >
                    <img class="formularioFL-logo" src="images/dMuni.png" alt="Logo"/>
                </div>

                <div id="divLogoCliente" class="col2_11" >
                    <div class="col1"><img src="imagesCli/{logoC}"   height="90" alt="Logo Cliente" /></div>
                    <div class="col2" style="display: {verLogoAux};"><img src="imagesCli/{logoAux}" height="90" alt="Logo Auxiliar" /></div>
                </div>
                
                <div id="divUser" class="formularioFL-campo" >
                    <label for="user-id" style="color: #124F87;">Área</label>
                    <select class="FLcampo-input" id="areaId" name="areaId" style="font-size: 10px; font-weight: 700;">
                        <option value="1">Área municipal</option>
                        <option value="2">Área salud</option>
                        <option value="0">Usuarios externos</option>
                    </select>
                </div>
                <div id="divUser" class="formularioFL-campo">
                    <label for="user-id" style="color: #124F87;">Usuario</label>
                    <input class="FLcampo-input" id="user-id" name="user-id" type="text" placeholder="rut sin digito verificador" value="{usr}"  onkeypress="return event.charCode >= 48 && event.charCode <= 57"/>
                </div>

                <div id="divUserPw" class="formularioFL-campo">
                    <label for="user-id" style="color: #124F87;">Contraseña</label>
                    <input class="FLcampo-input" id="user-pw" name="user-pw" type="password" placeholder="clave de acceso" />
                </div>

                <div id="divCod2FA" class="formularioFL-campo" style="display:none;">
                    <label for="user-id" style="color: #124F87;">Codigo 2FA</label>
                    <input class="FLcampo-input" id="cod2FA" name="cod2FA" type="text" placeholder="Codigo 2FA"/>
                </div>
                
                <input id="enviar-login" name="enviar-login" class="boton boton--buscar"  type="submit" value="{login}" />

                <div id="IPadd" style="margin-top: 20px; text-align: center;">
                    <label  style="color: #124F87; font-weight: 600;">IP: {_ipAddre}</label>
                </div>
			</form>
        </div>
        <div id="divAjax" style="display: none; background-color: yellow;">Ajax</div>
    </main>

    <script type="text/javascript">
        window.onload = validaIP;
        /*
        var totalTime = 15;

        function updateClock() {
            document.getElementById('countdown').innerHTML = totalTime;
            if(totalTime==0){
                document.getElementById('divRetardo').style.display='none';
                document.getElementById('divLog').style.display='block';
                }else{
                document.getElementById('divLog').style.display='none';
                document.getElementById('divRetardo').style.display='block';
                totalTime-=1;
                setTimeout("updateClock()",1000);
            }
        }
        */
    </script>
</html>