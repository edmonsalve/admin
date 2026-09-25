function leeFiltros(modulo,titulo) {
    const reg = /_/g  /* crea expreción regular, con bandera g:Global */
    const titInforme = titulo.replace(reg," ");
    document.getElementById('titReport').innerHTML = titInforme;

    document.getElementById('divInformes').style.display = 'none';
    document.getElementById('divFiltroInformes').style.display = 'block';
    llamarasincrono(modulo,'divFiltroInformes','')
}

function imprime(informe,titulo) {
    document.getElementById('divInformes').style.display = 'block';
    document.getElementById('divFiltroInformes').style.display = 'none';

    const reg = /_/g  /* crea expreción regular, con bandera g:Global */
    const titInforme = titulo.replace(reg," ");
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
    alert(informe);
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