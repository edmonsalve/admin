<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="es-ES">
<head>
	<meta name="author" content="Edmundo Monsalve" />
	<title>{titulo}</title>
	<link rel="shortcut icon" href="{icono}" />

	<link rel="stylesheet" href="styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="styles/print.css"  type="text/css" media="print" />
	<link rel="stylesheet" href="styles/red-m.css"  type="text/css" media="screen, projection" />
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>

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
    </script>	
</head>
<body onload="size()" >

<div class="container"  >
    <div class="span-24 last prepend-top append-bottom centro">
		<div id="encab" class="prepend-top" >	
			<img id="logo"  src="images/{logo}" alt="Logo" width="310" />	
			
			<form name="flogin" action="login.php" method="post" >				
				<div class="prepend-8 span-8 prepend-top centro" >					
					<div class="span-8 centro" style="border-radius: 20px; background-color:AliceBlue; height:270px;"> <!-- Gainsboro  WhiteSmoke -->
						<br><div style="text-align:center; font-weight:bold; font-size:18px;">Login Admin</div>
						
						<div class="prepend-1 span-6 centro">
							<div class="input-icons inline prepend-top">
								<!--<label>Usuario<br/></label>-->
								<i class="fa-solid fa-user fa-lg icon" style="margin-top:7px;"></i>
								<input class="input-field title centro" style="width:220px;" id="user-id" name="user-id" type="text" placeholder="Usuario"/>	
							</div>
							<div class="input-icons inline prepend-top">
								<!--<label>Contrase&ntilde;a<br/></label>-->
								<i class="fa-solid fa-unlock-keyhole fa-lg icon" style="margin-top:7px;"></i>
								<input class="input-field title centro" style="width:220px;" id="user-pw" name="user-pw" type="password" placeholder="Contraseña"/>
							</div>
							<div class="prepend-top">
								<input id="enviar-login" name="enviar-login" class="enviarGrande span-6 centro" style="width:220px;" type="submit" value="{enviar}" />
							</div>
						</div>
						
						<br><p class="peqRojo">{msgEstado}</p><br>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>