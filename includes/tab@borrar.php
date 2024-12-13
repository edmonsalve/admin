<?php 
	$error = '';
	if (PERMITE_BORRAR) {
		$IdRegistro = $_GET['IdRegistro'];
		$consulta   = "DELETE FROM `$tablaDB` WHERE `$IdCampo` = $IdRegistro";
		if ($conexionDB->consulta($consulta)) { header("Location:$locationFile"); }

		if (mysqli_errno() == 1451) {
			echo $consulta.'<br>';
			$error = $dg_txt->Dictionary['errorNoDel'];
			?>
					<script language="javascript" type="text/javascript">
						alert('<?php echo $error; ?>');
					</script>
			<?php

		}
	} else {
		$error .= $dg_txt->Dictionary['errorNoAutDel'];
		header("Location:$locationFile");
	}
?>