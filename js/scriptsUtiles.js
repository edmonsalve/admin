/* Scripts de Utilidad */

/* Función: size()
 * 
 * Determina tamaño de la pantalla en la que se carga la web 
 * y posiciona la barra de Pie de Página para que siempre este visible
 * 
 * Requiere como constantes: 	- Altura DIV del pie de página
 * 								- Id del DIV del pie de página
 * 
 * */

function size(divId, AlturaDiv) {
	var AlturaDiv = 120;
	var divId     = 'piePagina';
	
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
	if (posiPiePagina < 450) { posiPiePagina = 450; }
	document.getElementById(divId).style.top=posiPiePagina+'px';
}
	
window.onresize = function() {
  size();
}



/* Función: scrollBg()
 * 
 * Mueve el dondo de pantalla de un DIV o BODY
 * Generando el efecto de una pelicula sin necesidad de usar Flash
 * 
 * */

var idContenedorFondo = 'container';
var imgFondo01        = 'url("fondos/P5.jpg")';
var imgFondo02        = 'url("fondos/P8.jpg")';
var imgFondo03        = 'url("fondos/P9.jpg")';
var imgFondo04        = 'url("fondos/P5.jpg")';
var imgFondo05        = 'url("fondos/P5.jpg")';
var imgFondo06        = 'url("fondos/P5.jpg")';

var scrollSpeed = 50;
var step = 1;
var current = 0;
var imageWidth  = 1600;
var headerWidth = screen.width;          

var restartPosition = -(imageWidth - headerWidth);
var contadorImagen = 1;
		
function scrollBg(){
		current -= step;
		if (current == restartPosition){
				current = 0;
				contadorImagen++;
				if (contadorImagen == 2) { document.getElementById(idContenedorFondo).style.backgroundImage = imgFondo01; }
				if (contadorImagen == 3) { document.getElementById(idContenedorFondo).style.backgroundImage = imgFondo02; }
				if (contadorImagen == 4) { document.getElementById(idContenedorFondo).style.backgroundImage = imgFondo03; contadorImagen = 1; }
			   /* if (contadorImagen == 5) { document.getElementById(idContenedorFondo).style.backgroundImage ='url("fondos/04.jpg")'; contadorImagen = 1; } */
		}

		contenedor = '#'+idContenedorFondo;
		$(contenedor).css("background-position",current+"px 0");
}

function scroolStart()	{
	var init = setInterval("scrollBg()", scrollSpeed);
}



