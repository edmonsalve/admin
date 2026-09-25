function LoadBarcode(){
	var barcode,contenedor;
	contenedor = document.getElementById('ctBarcode');
	barcode    = document.getElementById('id_bien').value;
	ajax=nuevoAjax();
	ajax.open("GET", "barcode/procesos.php5?status=barcode&barcode="+barcode,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
		   contenedor.innerHTML = ajax.responseText
		}
	}
	ajax.send(null)
}