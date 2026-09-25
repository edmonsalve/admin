<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="{lang}">
<head>
	<title>{H2Sistema}</title>
	<link rel="icon" type="image/png" href="/images/icoDC.png">

	<!-- Fuente -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="/styles/normalize.css" 	  type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_globales.css" 	  type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_topbar.css" 	  type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_contenedores.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="/styles/st_formularios.css"  type="text/css" media="screen, projection" />
    
	<script src="/js/dcode.js"    type="text/javascript"></script>
	<script src="/js/loadAjax.js" type="text/javascript"></script>

    <script type="text/javascript">
		function leeFiltros(modulo,titulo) {
			const reg = /_/g  /* crea expreción regular, con bandera g:Global */
			const titInforme = titulo.replace(reg," ");
			document.getElementById('titReport').innerHTML = titInforme;

			document.getElementById('divInformes').style.display = 'none';
			document.getElementById('divFiltroInformes').style.display = 'block';
			// alert (modulo);
			llamarasincrono(modulo,'divFiltroInformes','')
		}

		function imprime(informe,titulo) {
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('divFiltroInformes').style.display = 'none';

			const reg = /_/g  /* crea expreción regular, con bandera g:Global */
			const titInforme = titulo.replace(reg," ");

			// alert(informe);
			document.getElementById('titReport').innerHTML = titInforme;
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosAAAA(modulo) {
			aaaaPago = document.getElementById('aaaaPago').value;

			informe = modulo+'?aaaa='+aaaaPago;  
			
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosFecha(modulo) {
			desde = document.getElementById('desde').value;
			hasta = document.getElementById('hasta').value;

			informe = modulo+'?desde='+desde+'&hasta='+hasta;  
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosFechaAAAA(modulo) {
			desde = document.getElementById('desde').value;
			hasta = document.getElementById('hasta').value;
			aaaa  = document.getElementById('aaaa').value;

			informe = modulo+'?desde='+desde+'&hasta='+hasta+'&aaaa='+aaaa;
			
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosFechaPeriodo(modulo) {
			desde = document.getElementById('desde').value;
			hasta = document.getElementById('hasta').value;
			ppago = document.getElementById('ppago').value;

			informe = modulo+'?desde='+desde+'&hasta='+hasta+'&ppago='+ppago;
			
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosFechaUsuario(modulo) {
			desde 	 = document.getElementById('desde').value;
			hasta 	 = document.getElementById('hasta').value;
			usuario  = document.getElementById('usuario').value;

			informe = modulo+'?desde='+desde+'&hasta='+hasta+'&usuario='+usuario;
			
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosFechaDeptoTC(modulo) {
			desde    = document.getElementById('desde').value;
			hasta    = document.getElementById('hasta').value;
			contrato = document.getElementById('contrato').value;
			deptos   = document.getElementById('deptos').value;
	
			informe  = modulo+'?desde='+desde+'&hasta='+hasta+'&contrato='+contrato+'&deptos='+deptos;  
			// alert(informe);
			document.getElementById('divFiltroInformes').style.display = 'none';
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosAAAARutTC(modulo) {
			var aaaa  = document.getElementById('aaaa').value;
			var tc 	  = document.getElementById('contrato').value;
			var rut	  = document.getElementById('rut').value

			informe  = modulo+'?aaaa='+aaaa+'&rut='+rut+"&tc="+tc;
			// alert(informe);
			document.getElementById('divFiltroInformes').style.display = 'none';
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosRutFecha(modulo) {
			var desde = document.getElementById('desde').value;
			var hasta = document.getElementById('hasta').value;
			var rut	  = document.getElementById('rut').value
			var tc 	  = document.getElementById('contrato').value;

			informe  = modulo+'?desde='+desde+'&hasta='+hasta+'&rut='+rut+'&tc='+tc;
			// alert(informe);
			document.getElementById('divFiltroInformes').style.display = 'none';
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeDecreto(modulo) {
			var nroDcto   = document.getElementById('nroDcto').value;
			var iniciales = document.getElementById('iniciales').value;
            var fecDcto   = document.getElementById('fecDcto').value;
			var tipo   	  = document.getElementById('tipo').value;

            // url       = informe+'?nroDcto='+nroDcto+'&iniciales='+iniciales+'&fecDcto='+fecDcto+'&tipo='+tipo;

			informe  = modulo+'?nroDcto='+nroDcto+'&iniciales='+iniciales+'&fecDcto='+fecDcto+'&tipo='+tipo; 
			// alert(informe);
			document.getElementById('divFiltroInformes').style.display = 'none';
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosAAAA(modulo) {
			var aaaa  = document.getElementById('aaaa').value;

			informe  = modulo+'?aaaa='+aaaa;
			// alert(informe);
			document.getElementById('divFiltroInformes').style.display = 'none';
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosModalidad(modulo) {
			desde = document.getElementById('desde').value;
			hasta = document.getElementById('hasta').value;
			modalidad = document.getElementById('modalidad').value;

			informe = modulo+'?desde='+desde+'&hasta='+hasta+'&modalidad='+modalidad;
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}

		function imprimeConFiltrosNivelEstado(modulo) {
			desde = document.getElementById('desde').value;
			hasta = document.getElementById('hasta').value;
			modalidad = document.getElementById('modalidad').value;
			estado = document.getElementById('estado').value;
			nivel = document.getElementById('nivel').value;	

			informe = modulo+'?desde='+desde+'&hasta='+hasta+'&modalidad='+modalidad+'&estado='+estado+'&nivel='+nivel;
			document.getElementById('divInformes').style.display = 'block';
			document.getElementById('objReport').data = informe;
		}
    </script>
</head>

<body>
	<div id="toolsbar" style="display: block;">{topbar}</div>
	
	<main class="principal">
		{barraLateral}
		<section class="tablero">
			<div class='filaGeneral filageneral--menu'>
				{headerMenu}
			</div>
				
			<div class='col2_46' style="height: 100%; ">
				<div class="col1">
					{_informesHTML}
				</div>

				<div class="tablero__reportes col2">
					<div class="header__fila1">
						<img class='barraLateral__iconos' src='/btns/btn_informes.png' height="30" style="margin-top:10px; margin-left:20px;" />
						<h3 id="titReport"></h3>
					</div>
					<div id="divFiltroInformes"></div>
					<div id="divInformes" style="display: none; height: 100%; ">
						<object id="objReport" data="" type="application/pdf" width="100%" height="100%"></object>
					</div>
				</div>
			</div>
		</section> 
	</main> 
</body>
</html>