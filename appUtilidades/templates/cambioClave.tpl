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
	<link rel="stylesheet" href="/styles/onoff.css"  type="text/css"media="screen, projection" />

    <!-- sweet alert2 -->
	<link rel="stylesheet" href="/styles/swal2/sweetalert2.min.css">	
	<script src="/js/sweetalert2.all.min.js"></script>
    
	<script src="/js/dcode.js" type="text/javascript"></script>
    <script type="text/javascript">
		function togglePassword() {
            var passwordField  = document.getElementById("pass1");
            var passwordFieldR = document.getElementById("passR");
            var checkbox = document.getElementById("showPassword");
            if (checkbox.checked) {
                passwordField.type  = "text";
                passwordFieldR.type = "text";
            } else {
                passwordField.type  = "password";
                passwordFieldR.type = "password";
            }
        }
	</script>
</head>

<body>
	<div id="toolsbar" style="display: block;">{topbar}</div>

	<main class="principal">
        <section class="tablero">
            <div class="col2_64">
                <div>
                    <form class="formularioGrande" id="passForm" name="passForm" method="post" action="cambioClave.php">
                        <input type="hidden" name="departAsoc" id="departAsoc" value="{_departAsoc}" />
                        
                        <div class="filaGeneral filaGeneral__seccion">Debe actualizar su clave de acceso</div>
                        <fieldset>
                            <legend>Identificación</legend>
                            <div class="col2_28">
                                <div class="formulario__campo" >
                                    <label class="campo__label" id="placa">{username}</label>
                                    <input class="campo__input"  type="text"  value="{_user}" readonly />
                                </div>
        
                                <div class="formulario__campo" >
                                    <label class="campo__label" id="nombrelb">{nombre}</label>
                                    <input class="campo__input"  type="text" value="{_name}" readonly/>
                                </div>
        
                            </div>
                            
                            <div class="formulario__campo" >
                                <label class="campo__label" id="emaillb">{email}</label>
                                <input class="campo__input" type="text" value="{_mail}" readonly/>
                            </div>

                            <div class="col3_111" >
                                <div class="formulario__campo col1" >
                                    <label class="campo__label">Nueva clave</label>
                                    <input class="campo__input negrita peque" onblur="validarPass()" name="pass1" id="pass1" type="password" placeholder="ingresar nueva clave" value="" />
                                </div>
                                
                                <div class="formulario__campo col2" >
                                    <label class="campo__label">Repetir nueva contraseña</label>
                                    <input class="campo__input negrita peque" onblur="validarPass()" name="passR" id="passR" type="password" placeholder="reingresar nueva clave" value="" />
                                </div>
                                <div class="col3" style="padding-top: 15px;">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="showPassword" tabindex="0" onclick="togglePassword()" >
                                        <label class="onoffswitch-label" for="showPassword">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col1_1"  >
                                <button  class="boton  boton--guardar" id="btnEnviar" type="submit">cambiar clave de acceso</button>				
                            </div>
                        </fieldset>
                        <div class="col1_1"  >
                            <input type="button" class="boton boton--salir"     style="margin-top:7px;" onclick="salir('/index_main')" value="{salir}"  />		
                        </div>
                        <div id="divAjax"></div>
                    </form>
                </div>
                <div>
                    <div class="col2_64">
                        <div class="col1" style="position: relative; width: 400px; height: 300px;">
                            <video id="video" width="400" height="300" autoplay style="display:block;"></video>
                            <!-- Círculo amarillo superpuesto -->
                            <div style="
                                position: absolute;
                                top: 0; left: 50%;
                                width: 300px; height: 300px;
                                margin-left: -150px;
                                border: 4px solid rgb(255, 255, 255);
                                border-radius: 50%;
                                pointer-events: none;
                                box-sizing: border-box;
                                z-index: 2;
                            "></div>
                            <canvas id="canvas" width="300" height="300" style="display:none;"></canvas>
                            <div class="col1_1" style="max-width: 400px;">
                                <button class="boton  boton--auxiliar" id="capturar">Capturar</button>
                            </div>
                        </div>
                        <div class="col2">
                            <img id="foto" src="/btns/btn_camara.png" height="300"/>
                            <div class="col1_1" style="max-width: 300px;">
                                <button class="boton  boton--auxiliar" id="guardar">Guardar</button>
                            </div>
                            <canvas id="canvas" width="300" height="300" style=""></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>       
    </main>
</body>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const foto = document.getElementById('foto');

    // Solicita permiso y accede a la cámara
    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
    })
    .catch(err => {
        alert("Error accediendo a la cámara: " + err);
    });



    // Envía la imagen al servidor en base64
    document.getElementById('capturar').addEventListener('click', () => {
        const contexto = canvas.getContext('2d');
        
        const videoWidth = video.videoWidth;
        const videoHeight = video.videoHeight;
        
        // Define el lado del cuadrado (el mínimo entre ancho y alto)
        const lado = Math.min(videoWidth, videoHeight);
        
        // Coordenadas para recortar el centro del video
        const sx = (videoWidth - lado) / 2;
        const sy = (videoHeight - lado) / 2;

        // Dibuja el recorte cuadrado centrado en el canvas
        contexto.drawImage(video, sx, sy, lado, lado, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/png');
        foto.src = dataUrl;
        foto.style.display = 'block';
    });

    // Envía la imagen al servidor en base64
    document.getElementById('guardar').addEventListener('click', () => {
        const dataUrl = canvas.toDataURL('image/png');
        fetch('fotoGuardar.php', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json'
            },
            body: JSON.stringify({ imagen: dataUrl })
        })
        .then(response => response.text())
        .then(data => alert(data))
        .catch(error => alert('Error al guardar la imagen: ' + error));
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script src="../js/pass.js"></script>
</html>