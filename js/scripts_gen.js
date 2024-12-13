/* Funsiones */

function buscarGr(name,pagina) {
	campo    = document.getElementById('campo').value;
	criterio = document.getElementById('buscar').value;
    
    /*
	pagAct   = document.getElementById('paginaAct').value;
	pagFin   = document.getElementById('paginaFin').value;
	pag      = pagina;
	
    
	if (pagina == 'A') { pag = parseInt(pagAct) - 1; if (pag < 1) { pag = 1; }}
	if (pagina == 'S') { pag = parseInt(pagAct) + 1; if (pag > pagFin) { pag = pagFin; }}
    if (pagina == 'U') { pag = pagFin; }
    if (pagina == 'I') { pag = 1; }
	*/
    
	url = name+'?buscarpor='+campo+'&iguala='+criterio;
	location.href=url;
}

function verfichaGr(nombrephp,idreg){
    url = nombrephp+'?IdRegistro='+idreg;
	location.href=url;
}

function eliminarGr(nombrephp,idreg) {
    if (confirm('Esta seguro de eliminar el Registro')) {
        url = name+'?ope=del&idReg='+idreg;
        location.href=url; 
    }
}