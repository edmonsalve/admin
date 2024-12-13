<?php
    require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');
	// require_once(PATH_INCLUDES . 'redm.php');

	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }
   // if ($usrID != "edmonsalve") { header("Location:index.php"); }

	// ::::::::::::::::::::::::::::::::: DB  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$DB_ADMIN = DB_ADMIN; 
	$conexionReD = new DB_MySQLi;
	$conexionReD->conectar(DB_CLIENTE, DB_SERVER, DB_USER, DB_PASSWD );

	// ::::::::::::::::::::::::::::::::: Clientes :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
    $consulta = "SELECT *  	FROM `adm_clientes`
							ORDER BY cliente ";

    $salida   = $conexionReD->consulta($consulta);	

	$w 			   = 1;
	$_htmlClientes = "";	
	while ($row = mysqli_fetch_array($salida)) {
		$_idCliente		= $row['idCliente'];
		$_cliente		= $row['cliente'];
		
		$_htmlClientes .= "<option value='$_idCliente'>$_cliente</option>";
	}
	mysqli_free_result($salida);

	// ::::::::::::::::::::::::::::::::: SISTEMAS MUNI  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
    $consulta = "SELECT *  	FROM `adm_sistemas`
							WHERE area = 'M'
							ORDER BY area, nombre ";

    $salida   = $conexionReD->consulta($consulta);	

	$w 			   = 1;
	$_htmlSistMuni = "";	
	while ($row = mysqli_fetch_array($salida)) {
		if	(($w % 2) == 0) { $st = "";  $last = ''; } else { $st = "background-color:#EEF4F9; ";  $last = ' last';}

		$_idSistema		= $row['id'];
		$_nombreSistema	= $row['nombre'];
		
		$_htmlSistMuni .= "
		<div class='span-10 $last' style='$st'>
			<div class='span-2 centro'  ><input name='$_idSistema' type='checkbox' /></div>
			<div class='span-8 last' style='$st' >$_nombreSistema</div>
		</div>";
		$w++;
	}
	mysqli_free_result($salida);
	
	
	// ::::::::::::::::::::::::::::::::: SISTEMAS SALUD :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
    $consulta = "SELECT *  	FROM `adm_sistemas`
							WHERE area = 'S'
							ORDER BY area, nombre ";

    $salida   = $conexionReD->consulta($consulta);	

	$w 			   = 1;
	$_htmlSistSalud = "";
	while ($row = mysqli_fetch_array($salida)) {
		if	(($w % 2) == 0) { $st = "";  $last = ''; } else { $st = "background-color:#EEF4F9; ";  $last = ' last';}

		$_idSistema		= $row['id'];
		$_nombreSistema	= $row['nombre'];
		
		$_htmlSistSalud .= "
		<div class='span-10 $last' style='$st'>
			<div class='span-2 centro'  ><input name='$_idSistema' type='checkbox' /></div>
			<div class='span-8 last' style='$st' >$_nombreSistema</div>
		</div>";
		$w++;
	}
	mysqli_free_result($salida);
	
	// ::::::::::::::::::::::::::::::::: SISTEMAS EDUC :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
    $consulta = "SELECT *  	FROM `adm_sistemas`
							WHERE area = 'E'
							ORDER BY area, nombre ";

    $salida   = $conexionReD->consulta($consulta);	

	$w 			   = 1;
	$_htmlSistEduc = "";
	while ($row = mysqli_fetch_array($salida)) {
		if	(($w % 2) == 0) { $st = "";  $last = ''; } else { $st = "background-color:#EEF4F9; ";  $last = ' last';}

		$_idSistema		= $row['id'];
		$_nombreSistema	= $row['nombre'];
		
		$_htmlSistEduc .= "
		<div class='span-10 $last' style='$st'>
			<div class='span-2 centro'  ><input name='$_idSistema' type='checkbox' /></div>
			<div class='span-8 last' style='$st' >$_nombreSistema</div>
		</div>";
		$w++;
	}
	mysqli_free_result($salida);
	
	// ::::::::::::::::::::::::::::::::: SISTEMAS ADMIN :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
    $consulta = "SELECT *  	FROM `adm_sistemas`
							WHERE area = 'A'
							ORDER BY area, nombre ";

    $salida   = $conexionReD->consulta($consulta);	

	$w 			   = 1;
	$_htmlSistAdmin = "";
	while ($row = mysqli_fetch_array($salida)) {
		if	(($w % 2) == 0) { $st = "";  $last = ''; } else { $st = "background-color:#EEF4F9; ";  $last = ' last';}

		$_idSistema		= $row['id'];
		$_nombreSistema	= $row['nombre'];
		
		$_htmlSistAdmin .= "
		<div class='span-10 $last' style='$st'>
			<div class='span-2 centro'  ><input name='$_idSistema' type='checkbox' /></div>
			<div class='span-8 last' style='$st' >$_nombreSistema</div>
		</div>";
		$w++;
	}
	mysqli_free_result($salida);
      
	
	$contenido=new plantilla("licencia");
	$contenido->asigna_variables(
			array(
			"lang"  				=> $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  	=> date('d-m-Y'),
            "topbar"		      	=> $topbar,
			"lateral"			  	=> $lateral,
			"H2Sistema"			  	=> $sistema_txt->GetDefinition('H2Sistema'),
			"H2Modulo"	          	=> 'Mantenedor de Licencias Clientes',
			
			"cerrarSesion"        	=> $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  	=> $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  	=> $sistema_txt->GetDefinition('borrar'),
            "salir"        	      	=> $dg_txt->GetDefinition('salir'),
            
			"_htmlSistMuni"     	=> $_htmlSistMuni,
			"_htmlSistSalud"     	=> $_htmlSistSalud,
			"_htmlSistEduc"     	=> $_htmlSistEduc,
			"_htmlSistAdmin"		=> $_htmlSistAdmin,
			"_htmlClientes"			=> $_htmlClientes,
			));
			
	echo $contenido->muestra();
 ?>