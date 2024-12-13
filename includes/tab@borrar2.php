<?php 
	$error = '';
	if (PERMITE_BORRAR) {   
	    $IdRegistro = $_GET['IdRegistro'];

	    $consulta = "SELECT * FROM  $tabMaster
                              WHERE $indMaster = $IdRegistro";

        $salida = $conexionDB->consulta($consulta);
        $numero = mysqli_num_rows($salida);

        If ($numero == 0) {
    		$consulta   = "DELETE FROM `$tablaDB` WHERE `$IdCampo` = $IdRegistro"; 
    		if ($conexionDB->consulta($consulta)) { header("Location:$locationFile"); }

            /* :::::::: Datos para LOG ::::::::::: */
            $detalle = $consulta;
            $accion  = 'DEL';
            require_once(PATH_INCLUDES . '/log.php');

    		if (mysqli_errno() == 1451) {
    			echo $consulta.'<br>';
    			$error = $red_text->Dictionary['errorNoDel'];
    			?>
    					<script language="javascript" type="text/javascript">
    						alert('<?php echo $error; ?>');
    					</script>
    			<?php

    		}
        } else { $error .= 'Registro no puede ser eliminado, Existen dependencias.';
                ?>
                     <script language="javascript" type="text/javascript">
                            alert('Error, Registro no puede ser eliminado existen dependencias ');
                     </script>
                <?php
        }

	} else {
		$error .= $dg_txt->Dictionary['errorNoAutDel'];
		header("Location:$locationFile");
	}
?>