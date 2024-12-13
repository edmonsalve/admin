function llamarasincrono(url, id_contenedor, post){
	var pagina_requerida = false
	if (window.XMLHttpRequest) {// Si es Mozilla, Safari etc
		pagina_requerida = new XMLHttpRequest()
		} else if (window.ActiveXObject){ // pero si es IE
		try {
		pagina_requerida = new ActiveXObject("Msxml2.XMLHTTP")
		} 
		catch (e){ // en caso que sea una versión antigua
		try{
		pagina_requerida = new ActiveXObject("Microsoft.XMLHTTP")
		}
		catch (e){}
		}
		}
		else
		return false
		pagina_requerida.onreadystatechange=function(){ // función de respuesta
		cargarpagina(pagina_requerida, id_contenedor)
	}
	
	if (post != '') {
		pagina_requerida.open('POST', url, true) // asignamos los métodos open y send
		pagina_requerida.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		pagina_requerida.send(post)
		} else {
		pagina_requerida.open('GET', url, true) // asignamos los métodos open y send
		pagina_requerida.send(null)
		}
}

function cargarpagina(pagina_requerida, id_contenedor){
	if (pagina_requerida.readyState == 4 && (pagina_requerida.status==200 || window.location.href.indexOf("http")==-1))
		document.getElementById(id_contenedor).innerHTML=pagina_requerida.responseText
}
	
function oculta(div) {
	document.getElementById(div).style.display='none';
}
	
function muestra(div) {
	document.getElementById(div).style.display='block';
}