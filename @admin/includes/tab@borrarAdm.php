<?php 
	$error = '';
	if ($permiteBorrar) {
		$IdRegistro = $_GET['IdRegistro'];
		$consulta   = "DELETE FROM `$tablaDB` WHERE `id` = $IdRegistro";
		if ($conexionDB->consulta($consulta)) { header("Location:$locationFile"); }  
		
		if (mysql_errno() == 1451) {
			echo $consulta.'<br>';
			$error = $red_text->Dictionary['errorNoDel'];
			?>
					<script language="javascript" type="text/javascript">
						alert('<?php echo $error; ?>');
					</script>
			<?php
			
		}
	} else {
		$error .= $red_text->Dictionary['errorNoAutDel'];
		header("Location:$locationFile");
	}
?>