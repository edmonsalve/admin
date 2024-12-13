<?php
	$tipoUsuario = 'E';    // $_SESSION['tipoUsuario'];
	
	switch ($tipoUsuario) {
		case 'E':
			$menuReD = '
			<ul id="sm" class="sm" style="width:368px;">
				<li><img src="/images/m03_evaluaciones.png" alt="" /></li>
				<li><img src="/images/m04_deberes.png" alt="" /></li>
				<li><img src="/images/m05_material.png" alt="" /></li>
				<li><img src="/images/m05_material.png" alt="" /></li>
			</ul>
			'; break;
	
		default:
			$menuReD = '
			<ul id="sm" class="sm">
				<li><img src="/images/m01_centros.png" alt="" /></li>
				<li><img src="/images/m02_herramientas.png" alt="" /></li>
				<li><img src="/images/m03_evaluaciones.png" alt="" /></li>
				<li><img src="/images/m04_deberes.png" alt="" /></li>
				<li><img src="/images/m05_material.png" alt="" /></li>
			</ul>
			';
	}
	$menuReD = '<br />';
?>
