/* Funsiones */

function ficha(modulo,id) {
	url = modulo+'_ficha.php?IdRegistro='+id;
	location.href=url;
}

function editarTabla(db,id) {
	url = db+'?IdRegistro='+id+'&ope=Update';
	location.href=url;
}

function imprimirTabla(dbname,dbtabla,orderBy,titulo){
    url = '../../../includes/tab@informes.php?dbname='+dbname+'&dbtabla='+dbtabla+'&orderBy='+orderBy+'&titulo='+titulo;
    window.open(url);
}

function imprimirSetXY(dbname){
    url = '../../../includes/inform@coor.php?dbname='+dbname;
    window.open(url);
}

function borrarReg(db,id) {
    if (confirm('Esta seguro de Eliminar?')) {
        url = db+'.php?IdRegistro='+id+'&ope=Delete';
	    location.href=url;
    }
}

function buscar(modulo,db,pagina) {
	campo    = document.getElementById('campo').value;
	criterio = document.getElementById('buscar').value;
	pagAct   = document.getElementById('paginaAct').value;
	pagFin   = document.getElementById('paginaFin').value;
	pag      = pagina;
	
	if (pagina == 'A') { pag = parseInt(pagAct) - 1; if (pag < 1) { pag = 1; }}
	if (pagina == 'S') { pag = parseInt(pagAct) + 1; if (pag > pagFin) { pag = pagFin; }}
    if (pagina == 'U') { pag = pagFin; }
    if (pagina == 'I') { pag = 1; }
	
	url = modulo+'.php?buscarpor='+campo+'&iguala='+criterio+'&pag='+pag;
	location.href=url;
}

function buscarTab(modulo,db) {
	campo    = document.getElementById('campo').value;
	criterio = document.getElementById('buscar').value;
	
	url = modulo+'.php?buscarpor='+campo+'&iguala='+criterio;
	location.href=url;
}

function salir(db) {
	url = db+'.php';
	location.href=url;
}

function onoff(div) {
    estado = document.getElementById(div).style.display;
    if (estado == 'none')  { document.getElementById(div).style.display = 'block'; }
    if (estado == 'block') { document.getElementById(div).style.display = 'none'; }
}

function validar(formulario) {
	/* Validación de Formulario	usando el primer carcater del atributo ID
	 * 0: Campo no obligatorio
	 * 1: Campo obligatorio
	 * 2: email 
	 * 3: Fecha valida
	 */
	 
	// document.getElementById('mensaje').style.display="block";
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
                    alert(nombreValue);
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
        
        // Valida si el campo es Fecha Valida
		if (campoNombre.substring(0,1) == '3') {
		  
		}
	}

	if (error.length > 0) { alert(error); } else { document.getElementById(formulario).submit(); }
}

/* Mover de un combo a otro  */
function asignar(desde,destino) {
	nroElementosDesde = document.getElementById(desde).length;
	comboDesde        = document.getElementById(desde);
	
	nroElementosDestino = document.getElementById(destino).length;
	comboDestino        = document.getElementById(destino);
	
	for (i=0; i < nroElementosDesde; i++) {
		if (comboDesde[i].selected) {
			variable = new Option(comboDesde[i].text, comboDesde[i].value)
			comboDestino[nroElementosDestino] = variable;
			comboDestino[nroElementosDestino].style.color="green";
			comboDestino[nroElementosDestino].selected="selected";
			nroElementosDestino++;
		}
	}
}

/* Eliminar elementos de un combo */
function borrar(borrar) {
	nroElementosCombo  = document.getElementById(borrar).length;
	comboBorrar        = document.getElementById(borrar);
	
	for (i=0; i < nroElementosCombo; i++) {
		if (comboBorrar[i].selected) {
			comboBorrar[i] = null;
		}
	}
}

function marcaSelectMultipleyGuarda(combo,formulario) {
	nroElementosCombo = document.getElementById(combo).length;
	combo             = document.getElementById(combo);
	
	for (i=0; i < nroElementosCombo; i++) {
		combo[i].selected="selected";
	}
	
	validar(formulario);
}

function sizeScreen() {
	var AlturaDiv = 25;
	var divId     = 'barraInf';
    
    var posi = document.getElementById(divId).style.top;
    
	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
	//Non-IE
	myWidth = window.innerWidth;
	myHeight = window.innerHeight;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
	//IE 6+ in 'standards compliant mode'
	myWidth = document.documentElement.clientWidth;
	myHeight = document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
	//IE 4 compatible
	myWidth  = document.body.clientWidth;
	myHeight = document.body.clientHeight;
	}
	
    
	posiPiePagina = myHeight - AlturaDiv;
	if (posiPiePagina < 700) { posiPiePagina = 700; }
	document.getElementById(divId).style.top=posiPiePagina+'px';
}

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
