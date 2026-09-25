/* funciones generales */
if ( window.location.pathname != '/index.php' ) {
	let tiempoMaximo = 20 * 60 * 1000; // 10 valor recomendado por la ANCI
	let avisoTiempo  =  2 * 60 * 1000;  // Avisar 2 min antes
	
	let temporizador, temporizadorAviso;
	
	// Reinicia los contadores en cada evento de actividad
	function reiniciarTemporizador() {
		clearTimeout(temporizador);
		clearTimeout(temporizadorAviso);
	
		temporizadorAviso = setTimeout(() => {
			Swal.fire({
				title: 'Inactividad detectada',
				text: 'Por motivos de seguridad y en cumplimiento de la normativa de la Agencia Nacional de Ciberseguridad, su sesión se cerrará automáticamente en 1 minuto si no se detecta actividad.',
				icon: 'warning',
				showCancelButton: false,
				// confirmButtonText: 'Seguir conectado',
				}).then((result) => {
					// if (result.isConfirmed) reiniciarTemporizador();
				});
		}, tiempoMaximo - avisoTiempo);
	
		temporizador = setTimeout(() => {	
			window.location.href = "/logout.php";	
		}, tiempoMaximo);
	}
	
	// Eventos que reinician el temporizador
	['keydown', 'click'].forEach(evt => {
		document.addEventListener(evt, reiniciarTemporizador);
	});
	
	// Inicia al cargar
	reiniciarTemporizador();
} else {
	clearTimeout(temporizador);
    clearTimeout(temporizadorAviso);
}

function endesarrollo() {
	Swal.fire({
		icon: 'info',
		title: 'En desarrollo',
		text: 'Esta funcionalidad aún no está disponible.',
		confirmButtonText: 'Aceptar'
	});
}

// inicializa la pagina cargando los datos si es carga inicial sincronica
function cargaInicial(moduloPHP,_async,pagAct){
	if (_async == 0){
		filtrarJSON(moduloPHP,pagAct);
	}
}

function irAPaginaURL(modulo,aux0,aux1,aux2,aux3,aux4,aux5,aux6,aux7,aux8,aux9) {
	const params = {
		aux0: aux0,
		aux1: aux1,
		aux2: aux2,
		aux3: aux3,
		aux4: aux4,
		aux5: aux5,
		aux6: aux6,
		aux7: aux7,
		aux8: aux8,
		aux9: aux9
	};

	// Agregar a la query sólo parámetros con valor definido y no vacío
	const qp = new URLSearchParams();
	Object.entries(params).forEach(([key, value]) => {
		if (value !== undefined && value !== null && value !== '') {
			qp.append(key, value);
		}
	});

	const qs = qp.toString();
	// alert ( `${modulo}${qs ? '?' + qs : ''}` );
	location.href = `${modulo}${qs ? '?' + qs : ''}`;
}

function irAPaginaJSON(modulo,aux0,aux1,aux2,aux3,aux4,aux5,aux6,aux7,aux8,aux9) {
	const params = {
		aux0: aux0,
		aux1: aux1,
		aux2: aux2,
		aux3: aux3,
		aux4: aux4,
		aux5: aux5,
		aux6: aux6,
		aux7: aux7,
		aux8: aux8,
		aux9: aux9
	};

	fetch(`${modulo}.php`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(params)
	})
	.then(res => res.text())
	.then(html => {
		document.open();
		document.write(html);
		document.close();
	})
	.catch(err => console.error(err));
}

function mostrarMantenimiento() {
	Swal.fire({
		icon: 'info',
		title: 'En Mantenimiento',
		text: 'Esta sección se encuentra en mantenimiento. Por favor, inténtelo más tarde.',
		confirmButtonText: 'Aceptar'
	});
}

function editar(php,id,aux1,aux2,aux3,aux4,aux5) {
	if (typeof aux1 !== 'undefined') { parAux1 = '&aux1='+aux1; } else { parAux1 = ''; }
	if (typeof aux2 !== 'undefined') { parAux2 = '&aux2='+aux2; } else { parAux2 = ''; }
	if (typeof aux3 !== 'undefined') { parAux3 = '&aux3='+aux3; } else { parAux3 = ''; }
	if (typeof aux4 !== 'undefined') { parAux4 = '&aux4='+aux4; } else { parAux4 = ''; }
	if (typeof aux5 !== 'undefined') { parAux5 = '&aux5='+aux5; } else { parAux5 = ''; }
	campo    = document.getElementById('campo').value;
	criterio = document.getElementById('buscar').value;
	pagina   = document.getElementById('pagina').value;
	url 	 = php+'_ficha.php?IdRegistro='+id+'&ope=Update&buscarpor='+campo+'&iguala='+criterio+'&pag='+pagina+parAux1+parAux2+parAux3+parAux4+parAux5;
	location.href=url;
}

function editar2(php,id) {
	url 	 = php+'_ficha.php?IdRegistro='+id+'&ope=Update';
	location.href=url;
}

function editarPost(php, id, aux1, aux2, aux3, aux4, aux5) {

    const campo    = document.getElementById('campo')?.value ?? '';
    const criterio = document.getElementById('buscar')?.value ?? '';
    const pagina   = document.getElementById('pagina')?.value ?? 1;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = php + '_ficha.php';

    const add = (name, value) => {
        if (typeof value !== 'undefined' && value !== null) {
            const i = document.createElement('input');
            i.type  = 'hidden';
            i.name  = name;
            i.value = value;
            form.appendChild(i);
        }
    };

    add('IdRegistro', id);
    add('ope', 'Update');

    add('buscarpor', campo);
    add('iguala', criterio);
    add('pag', pagina);

    add('aux1', aux1);
    add('aux2', aux2);
    add('aux3', aux3);
    add('aux4', aux4);
    add('aux5', aux5);

    document.body.appendChild(form);
    form.submit();
}

function editarJSON(modulo,aux0,aux1,aux2,aux3,aux4,aux5,aux6,aux7,aux8,aux9) {
	const params = {
		aux0: aux0,
		aux1: aux1,
		aux2: aux2,
		aux3: aux3,
		aux4: aux4,
		aux5: aux5,
		aux6: aux6,
		aux7: aux7,
		aux8: aux8,
		aux9: aux9
	};
	
	fetch(`${modulo}.php`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(params)
	})
	.then(res => res.text())
	.then(html => {
		document.open();
		document.write(html);
		document.close();
	})
	.catch(err => console.error(err));
}

//! deprecated, use editar() instead
function ficha(modulo,id,mod) {
	url = modulo+'_ficha.php?IdRegistro='+id+'&mod='+mod;
	location.href=url;
}

function borrarReg(php,id) { 
    if (confirm('Esta seguro de Eliminar el registro?')) {
        url = php+'.php?IdRegistro='+id+'&ope=Delete'; 
	    location.href=url;
    }
}

function filtrar2(modulo, pagina) {
	const getValue = id => {
		const el = document.getElementById(id);
		return el ? el.value : '';
	};

	const getAuxParam = num => {
		const el = document.getElementById(`aux${num}`);
		if (el && el.value !== '0') {  // el.value !== '' && 
			return `&filtroAux${num}Campo=${encodeURIComponent(el.name)}&filtroAux${num}Valor=${encodeURIComponent(el.value)}`;
		}
		return '';
	};

	const campo    = getValue('campo');
	const criterio = getValue('buscar');
	const pagAct   = parseInt(getValue('paginaAct')) || 1;
	const pagFin   = parseInt(getValue('paginaFin')) || 1;

	let pag = pagina;
	if (pagina === 'A') pag = Math.max(pagAct - 1, 1);
	else if (pagina === 'S') pag = Math.min(pagAct + 1, pagFin);
	else if (pagina === 'U') pag = pagFin;
	else if (pagina === 'I') pag = 1;

	// agregar ordenarPor y sentido si existen en el formulario
	const getOrderParam = () => {
		const ordenarPor = getValue('ordenarPor');
		const sentido    = getValue('sentido');
		let s = '';
		if (ordenarPor !== '') s += `&ordenarPor=${encodeURIComponent(ordenarPor)}`;
		if (sentido !== '') s += `&sentido=${encodeURIComponent(sentido)}`;
		return s;
	};

	// agregar numero de filas por pagina si existe en el formulario
	const getFilasParam = () => {
		const filas = getValue('filas');
		return filas !== '' ? `&filas=${encodeURIComponent(filas)}` : '';
	};

	const params = [
		`buscarpor=${encodeURIComponent(campo)}`,
		`&iguala=${encodeURIComponent(criterio)}`,
		`&pag=${pag}`,
		getAuxParam(1),
		getAuxParam(2),
		getAuxParam(3),
		getAuxParam(4),
		getAuxParam(5),
		getOrderParam(),
		getFilasParam()
	].join('');


	location.href = `${modulo}.php?${params}`;
}

// Versión mejorada que usa fetch y actualiza contenido dinámicamente
async function filtrarJSON(modulo, pagina) { 
	const getValue = id => {
		const el = document.getElementById(id);
		return el ? el.value.trim() : '';
	};

	const getAux = num => {
		const el = document.getElementById(`aux${num}`);
		if (el && el.value !== '' && el.value !== '0') {
			return {idaux: el.id, campo: el.name, valor: el.value };
		}
		return null;
	};

	// :: Capturar parámetros base
	const campo    = getValue('campo');
	const criterio = getValue('buscar');
	const pagAct   = parseInt(getValue('paginaAct')) || 1;
	const pagFin   = parseInt(getValue('paginaFin')) || 1;
	
	// :: Calcular página final
	let pag = pagina;
	if (pagina === 'A') pag = Math.max(pagAct - 1, 1);
	else if (pagina === 'S') pag = Math.min(pagAct + 1, pagFin);
	else if (pagina === 'U') pag = pagFin;
	else if (pagina === 'I') pag = 1;

	// recopilar todos los <option> del select 'ordenarPor' y convertirlos a un array para enviar en el JSON
	const ordenarSelect = document.getElementById('ordenarPor');
	const ordenarPorOptions = [];
	if (ordenarSelect) {
		Array.from(ordenarSelect.options).forEach(opt => {
			ordenarPorOptions.push(opt.value);
		});
		// Si necesito mandar el array con más detalles, usar este código
		/*
		Array.from(ordenarSelect.options).forEach(opt => {
			ordenarPorOptions.push({
				value: opt.value,
				text: opt.text,
				disabled: opt.disabled,
				selected: opt.selected
			});
		});
		*/
	}

	// :: Preparar JSON con filtros
	const filtros = {
		buscarpor: campo,
		iguala: criterio,
		pag: pag,
		auxiliares: [getAux(1), getAux(2), getAux(3), getAux(4), getAux(5)].filter(Boolean),
		ordenarPorOptions: ordenarPorOptions,
		ordenarPor: getValue('ordenarPor'),
		sentido: getValue('sentido'),
		filas: getValue('filas')
	};

	try { 
		const res = await fetch(`${modulo}.php`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(filtros)
		});

		if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
		const data = await res.text(); // o .json() si devuelves JSON
		// :: Actualizar contenido dinámicamente (sin recargar)
		const contenedor = document.getElementById('contenedorResultados');
		
		if (contenedor) contenedor.innerHTML = data;
		else 
			console.log('Respuesta del servidor:', data);
	} catch (err) {
		console.error('Error en filtrarJSON:', err);
	}
}

// Versión simplificada que recarga toda la página
async function filtrarJSONOK(modulo, pagina) { 
	const getValue = id => {
		const el = document.getElementById(id);
		return el ? el.value.trim() : '';
	};

	const getAux = num => {
		const el = document.getElementById(`aux${num}`);
		if (el && el.value !== '' && el.value !== '0') {
			return {idaux: el.id, campo: el.name, valor: el.value };
		}
		return null;
	};

	// :: Capturar parámetros base
	const campo    = getValue('campo');
	const criterio = getValue('buscar');
	const pagAct   = parseInt(getValue('paginaAct')) || 1;
	const pagFin   = parseInt(getValue('paginaFin')) || 1;

	// :: Calcular página final
	let pag = pagina;
	if (pagina === 'A') pag = Math.max(pagAct - 1, 1);
	else if (pagina === 'S') pag = Math.min(pagAct + 1, pagFin);
	else if (pagina === 'U') pag = pagFin;
	else if (pagina === 'I') pag = 1;

	// :: Preparar JSON con filtros
	const filtros = {
		buscarpor: campo,
		iguala: criterio,
		pag: pag,
		auxiliares: [getAux(1), getAux(2), getAux(3), getAux(4), getAux(5)].filter(Boolean),
		ordenarPor: getValue('ordenarPor'),
		sentido: getValue('sentido'),
		filas: getValue('filas')
	};

	fetch(`${modulo}.php`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(filtros)
	})
	.then(res => res.text())
	.then(html => {
		document.open();
		document.write(html);
		document.close();
	})
	.catch(err => console.error(err));
}


function filtrar(modulo,pagina) {
	campo    = document.getElementById('campo').value; 
	criterio = document.getElementById('buscar').value;

	if ( document.getElementById('aux1') && document.getElementById('aux1').value != '' & document.getElementById('aux1').value != '0' ) { 
		var name = document.getElementById('aux1').name;
		var valor = document.getElementById('aux1').value;
		var campoAdicional1 = '&filtroAux1Campo='+name+'&filtroAux1Valor='+valor;
	} else {
		var campoAdicional1 = '';
	}
	
	if ( document.getElementById('aux2') && document.getElementById('aux2').value != '' & document.getElementById('aux2').value != '0' ) { 
		var name = document.getElementById('aux2').name;
		var valor = document.getElementById('aux2').value;
		var campoAdicional2 = '&filtroAux2Campo='+name+'&filtroAux2Valor='+valor;
	} else {
		var campoAdicional2 = '';
	}
	
	if ( document.getElementById('aux3') && document.getElementById('aux3').value != '' & document.getElementById('aux3').value != '0' ) { 
		var name = document.getElementById('aux3').name;
		var valor = document.getElementById('aux3').value;
		var campoAdicional3 = '&filtroAux3Campo='+name+'&filtroAux3Valor='+valor;
	} else {
		var campoAdicional3 = '';
	}
	
	if (document.getElementById('paginaAct')) { pagAct = document.getElementById('paginaAct').value; } else { pagAct = 1; }
	if (document.getElementById('paginaFin')) { pagFin = document.getElementById('paginaFin').value; } else { pagFin = 1; }
	
	pag      = pagina;

	if (pagina == 'A') { pag = parseInt(pagAct) - 1; if (pag < 1) { pag = 1; }}
	if (pagina == 'S') { pag = parseInt(pagAct) + 1; if (pag > pagFin) { pag = pagFin; }}
    if (pagina == 'U') { pag = pagFin; }
    if (pagina == 'I') { pag = 1; }

	url = modulo+'.php?buscarpor='+campo+'&iguala='+criterio+'&pag='+pag+campoAdicional1+campoAdicional2+campoAdicional3;
	location.href=url;
}

function ocultarDiv(div, btn, txt) {
	divOcul = document.getElementById(div).style.display;
	if (divOcul == 'none') {
	   document.getElementById(div).style.display = 'flex';
	   document.getElementById(btn).value="Ocultar "+txt;               
	   } else {
	   document.getElementById(div).style.display = 'none';
	   document.getElementById(btn).value="Ver "+txt;
	}
}

function salir(php,parametro) {
    /* 	Se agrega la posibilidad de pasar parametros al boton de salida por medio de get 
		ejemplo salir('propietarios','nombre=tapia&comuna=123')  */
    
    if (parametro != '') { parametro = '?'+parametro; } else { parametro = ''; }
	url = php+'.php'+parametro;
	location.href=url;
}

/* funciones grilla */
function addFila(modulo) {
	url = modulo+'.php?IdRegistro=new&ope=Add';
	location.href=url;
}

function editarFila(php,id) {
	campo    = document.getElementById('campo').value;
	criterio = document.getElementById('buscar').value;
	pagina   = document.getElementById('pagina').value;
	url 	 = php+'.php?IdRegistro='+id+'&ope=Update&buscarpor='+campo+'&iguala='+criterio+'&pag='+pagina;
	location.href=url;
}

function editarFilaRes2(id,php) {
	campo    = document.getElementById('campo').value;
	criterio = document.getElementById('buscar').value;
	pagina   = document.getElementById('pagina').value;
	url 	 = php+'.php?IdRegistro='+id+'&ope=Update&buscarpor='+campo+'&iguala='+criterio+'&pag='+pagina;
	location.href=url;
}

function borrarFila(php,id) { 
	Swal.fire({
		title: "Esta seguro de Eliminar el registro?",
		text: '',
		showDenyButton: false,
		showCancelButton: true,
		icon: 'question',
		width:'30em',
		color:'darkblue',
		confirmButtonText: "Eliminar",
		confirmButtonColor: "red",
		denyButtonText: "No guardar",
		cancelButtonText: "Cancelar"
		}).then((result) => {
		
		if (result.isConfirmed) {
			url = php+'.php?IdRegistro='+id+'&ope=Delete'; 
	    	location.href=url;
		} else if (result.isDenied) {	
			return;
		}
	});
}

function guadarFila(formulario) {
	document.getElementById(formulario).submit(); 
}

/* velidacion */
// ingresa automaticamente el digito verificador 
function valrut(campoRUT,campoDV) {
	rut      = new String(document.getElementById(campoRUT).value);
	largo    = rut.length;
	largoaux = largo - 1;
	contador = 2;
	acumula  = 0;
	resto    = 0;

	while (largoaux >= 0) {
		numero = rut.substr(largoaux,1);
		acumula += numero * contador;
		contador++;
		largoaux--;
		if (contador > 7) { contador = 2; }
		}

	modulo = acumula % 11;
	digito = 11 - modulo;

	if (digito == 10) { digito = 'K'; }
	if (digito == 11) { digito = 0; }

	document.getElementById(campoDV).value = digito;
}

// digito verificador se ingresa manualmente y valida que sea correcto
function valrutError(campoRUT,campoDV) {
	rut = new String(document.getElementById(campoRUT).value);
	dv	= new String(document.getElementById(campoDV).value);

	largo    = rut.length;
	largoaux = largo - 1;
	contador = 2;
	acumula  = 0;
	resto    = 0;

	while (largoaux >= 0) {
		numero = rut.substr(largoaux,1);
		acumula += numero * contador;
		contador++;
		largoaux--;
		if (contador > 7) { contador = 2; }
		}

	modulo = acumula % 11;
	digito = 11 - modulo;

	if (digito == 10) { digito = 'K'; }
	if (digito == 11) { digito = 0; }

	if (dv != digito) {  return 0; } else {  return 1; }
}

function validar(formulario) {
	/* Validación de Formulario	usando el primer carcater del atributo ID
	 * 0: Campo no obligatorio
	 * 1: Campo obligatorio
	 * 2: email
	 * 3: Fecha valida
	 */

	//document.getElementById('mensaje').style.display="block"; 

	camposFormulario = document.getElementById(formulario).elements.length;

	error = '';
	for (i=0; i < camposFormulario; i++) {
		campoForm    = document.getElementById(formulario).elements[i];
		campoNombre  = campoForm.id;

		// Valida si el campo es Obligatorio
		if (campoNombre.substring(0,1) == '1') {
			if (campoForm.type == 'text') {

				valor = campoForm.value;
				valor = valor.replace(/^\s*|\s*$/g,"");

				if (valor.length == 0) {
					nombreValue = campoNombre.substring(1);
                    // alert(nombreValue);
					textoValue  = document.getElementById(nombreValue).firstChild.nodeValue;
					document.getElementById(nombreValue).style.color='red';
					document.getElementById(nombreValue).firstChild.nodeValue = textoValue +' es Campo Obligatorio';
					error = 'Formulario Incompleto. \n\n Los campos marcados con Rojo son Obligatorios';
				}
			}
		}

        // Valida si el campo es eMail
		if (campoNombre.substring(0,1) == '2') {
    		  valor = campoForm.value;
    		  re=/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/
              if(!re.exec(valor)) {
                nombreValue = campoNombre.substring(1);
    			textoValue  = document.getElementById(nombreValue).firstChild.nodeValue;
    			document.getElementById(nombreValue).style.color='red';
    			document.getElementById(nombreValue).firstChild.nodeValue = textoValue+' no es una direccion valida';
                error += 'Formulario Incompleto. \n\n';
              }
		}
	}
    
	if (error.length > 0) { alert(error); } else { document.getElementById(formulario).submit(); }

	//alert(formulario);
}

function verMasMenos(idImagen, idDiv) {
	let src = document.getElementById(idImagen).src;
	let fileName = src.substring(src.lastIndexOf('/') + 1);
	
	if (fileName == 'btn_mas.png') {
		document.getElementById(idDiv).style.display = 'block';
		document.getElementById(idImagen).src = '/btns/btn_menos.png';
	} else {
		document.getElementById(idDiv).style.display = 'none';
		document.getElementById(idImagen).src = '/btns/btn_mas.png';
	}
}