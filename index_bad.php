<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <link rel="icon" type="image/png" href="/images/icoDC.png">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>dCode Error</title>
    </head>
    
    <?php
        $estado    = 'O';
        if (isset($_GET['estado'])) { $estado = $_GET['estado']; }

		$msg = 'Problemas t&eacute;cnicos';
    ?>
    <body>
        <h3>Su instalaci&oacute;n presenta el siguiente problema: <?php echo $msg; ?></h3>
        <b>Pongase en contacto con Servicio al Cliente dCode, para corregir esta situaci&oacute;n</b>

        <br />
        <br />
        <a href="http://www.dcode.cl" target="_top" >Sitio web dCode</a>
    </body>
</html>