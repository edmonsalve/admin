<?php 
    $error = '';
    if (PERMITE_GUARDAR) {
		$i = 1;
        $j = count($_POST);

        $field   = '(';
        $valores = '(';
        $set     = '';

        $Id = $_POST[$IdCampo];
        

        foreach ($_POST as $campo => $valor) {

        	if ($valor != 'new') {

                $field   .= "`$campo`";
                if (substr($campo,0,5) == 'fecha') { $valor = toFecAMD($valor); }

                $valor    = str_replace(",","",$valor);
				
				if (!isset($mayusculas)) { $mayusculas = false; }
				if ($mayusculas)  { $valor = strtoupper($valor); }
                
				
				$valores .= "'$valor'";
                $set .= "`$campo` = '$valor'";

                if ($i < $j) {
                    $field   .= ',';
                    $valores .= ',';
                    $set     .= ',';
                }
            	$i++;
             } else { $i++; }
        }
        $field   .= ')';
        $valores .= ')';

        $consultaNEW = "REPLACE INTO `$tablaDB`  $field values $valores";
        $consultaUPD = "UPDATE `$tablaDB` SET $set WHERE `$IdCampo` = '$Id'";

        if ($Id == 'new') { $consulta = $consultaNEW; $accion  = 'ADD'; } else { $consulta = $consultaUPD; $accion  = 'UPD'; }
        
		if (isset($muestraConsulta)) {
			if ($muestraConsulta) {  echo "</br>Qry: $consulta  **ID: $Id **</br>";	}
		}
		
        /* -------------------------------------------------------------------------------------------------- */

		if (!$conexionDB->consulta($consulta)) {
            $error .= mysqli_error()."Error al guardar datos tabla:  $tablaDB<br />";
            echo $error;
            } else {
			if ($Id == 'new' ) { $IdRegistro = $conexionDB->ultimoId(); } else { $IdRegistro = $Id; }

            // =====================================  Para documentos adjuntos de una tabla   =========================================== 
            // se requieren las siguientes Variables: $campoFile:  Contiene el nombre del campo de la Tabla que debe ser el mismo en la plantilla
            //                                        $directorio: Contiene la ruta donde se guardara el archivo adjunto
            //                                        $nameFile:   Contiene el prefijo del adjunto
            //
            if (isset($campoFile)) {
                if (isset($_FILES[$campoFile])) {
                    $doc_nombre     = $_FILES[$campoFile]['name'];
                    $doc_tipo       = $_FILES[$campoFile]['type'];
                    $doc_size       = $_FILES[$campoFile]['size'];
                    $doc_tmp        = $_FILES[$campoFile]['tmp_name'];
                    $doc_error      = $_FILES[$campoFile]['error'];
                    
                    $nombreDoc = "$nameFile"."_$IdRegistro.jpg";
                    
                    move_uploaded_file($doc_tmp, $directorio . "$nombreDoc"); 
                    $consultaDoc = "UPDATE `$tablaDB` SET $campoFile = '$nombreDoc' WHERE `$IdCampo` = '$IdRegistro'";
                    $conexionDB->consulta($consultaDoc);
                }
            }
        
            /* :::::::: Datos para LOG ::::::::::: */
            $detalle = $consulta;
			$idObjetivo	= "$Id";
            require_once(PATH_INCLUDES . '/log.php');

			require_once('log.php');
		}
	}

?>
