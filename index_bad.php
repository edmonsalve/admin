<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ReD-M Error</title>
</head>

<?php 
	echo "hola";
    $estado    = 'O';
    if (isset($_GET['estado'])) { $estado = $_GET['estado']; }
    
    switch($estado) {
        case 'L': $msg = 'Licencia INVALIDA, es posible que usted este usando una copia no autorizada.'; break;
        case 'A': $msg = 'Aplicaciones desactivadas'; break;
        case 'V': $msg = 'Pagos atrasados'; break;
        case 'O': $msg = 'Problemas t&eacute;cnicos'; break;
    }
?>
<body>
<h3>Su instalaci&oacute;n presenta el siguiente problema: <?php echo $msg; ?></h3>
<b>Pongase en contacto con ReD-M, para corregir esta situaci&oacute;n</b>
<br />
<br />
<b>Telefono:(09) 820 20 353</b>
<br />
<b>email: <a href="mailto:atencionalcliente@red-m.cl">atencionalcliente@red-m.cl</a></b>
<br />
<br />
<a href="http://www.red-m.cl" target="_top" >Sitio web ReD-M</a>
</body>
</html>