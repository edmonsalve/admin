/* Funciones */

function editar(php,id,aux1,aux2) {
	if (typeof aux1 !== 'undefined') { parAux1 = '&aux1='+aux1; } else { parAux1 = ''; }
	if (typeof aux2 !== 'undefined') { parAux2 = '&aux2='+aux2; } else { parAux2 = ''; }
	campo    = document.getElementById('campo').value;
	criterio = document.getElementById('buscar').value;
	pagina   = document.getElementById('pagina').value;
	url 	 = php+'_ficha.php?IdRegistro='+id+'&ope=Update&buscarpor='+campo+'&iguala='+criterio+'&pag='+pagina+parAux1+parAux2;
	location.href=url;
}

function verMenu(divActivo,grupos) {
	liSelec = "LI_"+divActivo;

	document.getElementById(liSelec).classList.add('seleccionado');
	document.getElementById(divActivo).style.display = 'block';
	gruposMenu = grupos.split(',');
	for (i = 0; i < gruposMenu.length; i++) { 
		ocultarDiv = gruposMenu[i];
		liSelec = "LI_"+ocultarDiv;
		
		if (divActivo != ocultarDiv) { 
			document.getElementById(liSelec).classList.remove('seleccionado');
			document.getElementById(ocultarDiv).style.display = 'none'; 
		}
	} 
}

function ocultarMenu(menu) {
	document.getElementById(menu).style.display = 'none';
}

function onoff(div) {
    estado = document.getElementById(div).style.display;
    if (estado == 'none')  { document.getElementById(div).style.display = 'block'; }
    if (estado == 'block') { document.getElementById(div).style.display = 'none'; }
}


/* combos */
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



 
function imprimirTabla(dbname,dbtabla,orderBy,titulo){
    url = '../../../includes/tab@informes.php?dbname='+dbname+'&dbtabla='+dbtabla+'&orderBy='+orderBy+'&titulo='+titulo;
    window.open(url);
}

function imprimirSetXY(dbname){
    url = '../../../includes/inform@coor.php?dbname='+dbname;
    window.open(url);
}

function buscarTab(modulo,db) {
	campo    = document.getElementById('campo').value;
	criterio = document.getElementById('buscar').value;

	url = modulo+'.php?buscarpor='+campo+'&iguala='+criterio;
	location.href=url;
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
	}
    
	if (error.length > 0) { alert(error); } else { document.getElementById(formulario).submit(); }
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



