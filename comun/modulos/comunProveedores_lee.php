<?php
	/* :::::::::::::::::::::::::::::::::: Lee parametros $_GET para Busqueda :::::::::::::::::::::::::::::::::: */
	$where		  = '';
	$criterio     = '';

	if (isset($_GET['buscarpor'])) { 
		$criterio  	= trim($_GET['iguala']);
		$buscarpor 	= $_GET['buscarpor'];
		
		if (trim($criterio) != '') { $where = " `$buscarpor`  like  '$criterio%' "; }
		
		
		if (trim($where) != '') { $where = "WHERE ".$where;  }
	} 
    
    if (isset($_GET['ope'])) {
        if ($_GET['ope'] == 'Tras') {
             
            $criterio  	= $IdNewProvee;
    		$buscarpor 	= 'IdProvee';
    		
    		if (trim($criterio) != '') { $where = " `$buscarpor`  like  '$criterio%' "; }
    		if (trim($where) != '') { $where = "WHERE ".$where;  }   
        }
    }
    
	/* :::::::::::::::::::::::::::::::::::::: Determina Paginación  :::::::::::::::::::::::::::::::::::: */
	$PagTabla    = 'comun_tabProveedores';
	$moduloPHP   = 'comunProveedores';
	include(PATH_INCLUDES.'paginacion.php');
	
	
	/* :::::::::::::::::::::::::::::::::::::::: Lee Proveedores  ::::::::::::::::::::::::::::::::::::: */
	$contador = 1;
	$consulta = "SELECT *   FROM `comun_tabProveedores`
							$where
							ORDER BY razonSocial 
							LIMIT $RegInicial,".PAGINACION;
				
	$salida   = $conexionDB->consulta($consulta);
	
	$borrar      = $sistema_txt->GetDefinition('borrar');
	//$ver         = $sistema_txt->GetDefinition('proveedores');
    $comuProveedores = $sistema_txt->GetDefinition('vercomunProveedores');
    
	$grillaHTML  = '';
	while ($row = mysql_fetch_array($salida)) {
		if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; } 
		$contador++;
		
		$_IdProvee	 = $row['idProvee'];
		$_razonSocial = trim($row['razonSocial']);
		$_email    	 = $row['email'];
		$_telefono1 	 = $row['telefono1'];
		
		$js = "'comunProveedores',".$_IdProvee;
		// 
		$grillaHTML .= '
		<div class="span-20">
			<div onclick="ficha('.$js.') "class="span-12 cursorPoint '.$clase.'" >
				<div class="span-2" >
				'.$_IdProvee.'
				</div>
				<div class="span-10 last" >
				'.$_razonSocial.'
				</div>
			</div>
            <div class="span-2 last cursorPoint fila_impar"  ><input type="button" class="enviarpeq" onclick="borrarReg('.$js.')" value="'.$borrar.'"/></div>
		</div>';
	}


?>
