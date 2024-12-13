<?php 	 
	require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');
	// require_once(PATH_INCLUDES . 'redm.php');
	
	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }


    // ::::::::: Determina Nombre de Sript en Ejecucion ::::::::: //
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);


    // ::::::::::::::::::::::::::::::::: DB  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$DB_ADMIN = DB_ADMIN;
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_SISTEMA, DB_SERVER, DB_USER, DB_PASSWD );
	
	$_dispCont = "none";

	
    if (isset($_GET['IdRegistro'])) {
        $IdRegistro = $_GET['IdRegistro'];
    }
 
	$carpeta 		= PATH_ICONOS;
    // :::::::::::::::::::::::::::: Lee parametros $_POST  para guardar  Contrato ::::::::::::::::::: //
    if (isset($_POST['bd'])) {
		$bd 	= $_POST['bd'];
		$idTab	= $_POST['idTab'];

        $guardarTabla = $bd;
        $IdCampo      = $idTab;
       
	    $muestraPost	 = false;
		$muestraConsulta = false;
		$mayusculas		 = false;
		$ignorarPost	 = "bd,idTab";
		require_once( PATH_INCLUDES . 'guardar.php');

		if ($bd == "adm_sistemas") {
			$ico_nombre     = $_FILES['iconoUpLoad']['name'];
			$ico_tipo       = $_FILES['iconoUpLoad']['type'];
			$ico_size       = $_FILES['iconoUpLoad']['size'];
			$ico_tmp        = $_FILES['iconoUpLoad']['tmp_name'];
			$ico_error      = $_FILES['iconoUpLoad']['error'];			

			if (trim($ico_nombre) != '') {
				move_uploaded_file($ico_tmp,  "$carpeta/$ico_nombre");
				$consulta = "UPDATE `$bd` SET  `icono` = '$ico_nombre' 		WHERE id = $IdRegistro";
				$conexionDB->consulta($consulta);
			}

			$icod_nombre    = $_FILES['iconodesUpLoad']['name'];
			$icod_tipo      = $_FILES['iconodesUpLoad']['type'];
			$icod_size      = $_FILES['iconodesUpLoad']['size'];
			$icod_tmp       = $_FILES['iconodesUpLoad']['tmp_name'];
			$icod_error     = $_FILES['iconodesUpLoad']['error'];

			if (trim($icod_nombre) != '') {
				move_uploaded_file($icod_tmp, "$carpeta/$icod_nombre");
				$consulta = "UPDATE `$bd` SET  `iconoDesh` = '$icod_nombre' 	WHERE id = $IdRegistro";
				$conexionDB->consulta($consulta);
			}
		}
		
		if ($bd == "adm_sistemasAdmin") { 
			$pan1_nombre     = $_FILES['pant1UpLoad']['name'];
			$pan1_tipo       = $_FILES['pant1UpLoad']['type'];
			$pan1_size       = $_FILES['pant1UpLoad']['size'];
			$pan1_tmp        = $_FILES['pant1UpLoad']['tmp_name'];
			$pan1_error      = $_FILES['pant1UpLoad']['error'];
			
			if (trim($pan1_nombre) != '') {
				move_uploaded_file($pan1_tmp, "../pantallas/$pan1_nombre");
				$consulta = "UPDATE `$bd` SET  `pantalla1` = '$pan1_nombre' 	WHERE idAdmin = $IdRegistro"; 
				$conexionDB->consulta($consulta);
			}
		
		
			$pan2_nombre     = $_FILES['pant2UpLoad']['name'];
			$pan2_tipo       = $_FILES['pant2UpLoad']['type'];
			$pan2_size       = $_FILES['pant2UpLoad']['size'];
			$pan2_tmp        = $_FILES['pant2UpLoad']['tmp_name'];
			$pan2_error      = $_FILES['pant2UpLoad']['error'];	
			
			if (trim($pan2_nombre) != '') {
				move_uploaded_file($pan2_tmp, "../pantallas/$pan2_nombre");
				$consulta = "UPDATE `$bd` SET  `pantalla2` = '$pan2_nombre' 	WHERE idAdmin = $IdRegistro";
				$conexionDB->consulta($consulta);
			}

			$pan3_nombre     = $_FILES['pant3UpLoad']['name'];
			$pan3_tipo       = $_FILES['pant3UpLoad']['type'];
			$pan3_size       = $_FILES['pant3UpLoad']['size'];
			$pan3_tmp        = $_FILES['pant3UpLoad']['tmp_name'];
			$pan3_error      = $_FILES['pant3UpLoad']['error'];	

			if (trim($pan3_nombre) != '') {
				move_uploaded_file($pan3_tmp, "../pantallas/$pan3_nombre");
				$consulta = "UPDATE `$bd` SET  `pantalla3` = '$pan3_nombre' 	WHERE idAdmin = $IdRegistro";
				$conexionDB->consulta($consulta);
			}
		}
		
		
    }

	// :::::::::::::::::::::::::::::::::::::::::: Lee registro ::::::::::::::::::::::::::::::::::::::: //
	if ( $IdRegistro != "new" ) {
		$consulta = "SELECT *  	FROM   `adm_sistemas`,`adm_sistemasAdmin` 
								WHERE  `adm_sistemas`.id = `adm_sistemasAdmin`.idAdmin
								AND		id = '$IdRegistro'";

		$salida = $conexionDB->consulta($consulta);
		$row    = mysqli_fetch_array($salida);

		$id		    	= $row['id'];
        $nombre     	= $row['nombre'];
		$descripcion  	= $row['descripcion'];
		$version		= $row['version'];
		$prefijoTablas	= $row['prefijoTablas'];
		$estado			= $row['estado'];
        $area       	= $row['area'];
	    $ruta      		= $row['ruta'];
		$dbase			= $row['dbase'];
        $icono      	= $row['icono'];
        $iconoDesh  	= $row['iconoDesh'];
        $situacion  	= $row['situacion'];
		$version    	= $row['version'];
        $fechaDesa		= $row['fechaDesa'];
		$observaciones	= $row['observaciones'];
		$pantalla1	    = $row['pantalla1'];
		$pantalla2	    = $row['pantalla2'];
		$pantalla3	    = $row['pantalla3'];
		$descPant1	    = $row['descPant1'];
		$descPant2	    = $row['descPant2'];
		$descPant3	    = $row['descPant3'];
		
        $readonly       = 'readonly';

		} else {
        $id		   		= '';
        $nombre     	= '';
		$descripcion  	= '';
		$version		= '';
		$prefijoTablas	= '';
		$estado			= '';
        $area       	= '';
	    $ruta      		= '';
		$dbase			= '';
        $icono      	= '';
        $iconoDesh  	= '';
        $situacion  	= '';
		$version    	= '';
        $fechaDesa		= '';
		$observaciones	= '';
		$pantalla1	    = '';
		$pantalla2	    = '';
		$pantalla3	    = '';
		$descPant1	    = '';
		$descPant2	    = '';
		$descPant3	    = '';
		
        $readonly         = '';
        $disabledBtn      = ' disabled="disabled" ';
	}

	if (trim($pantalla1) != '') {
		$imagePant1 = getimagesize("../pantallas/$pantalla1");
		$ancho1     = intval($imagePant1[0] / 6);
		$alto1		= intval($imagePant1[1] / 6);
	}
	
	if (trim($pantalla2) != '') {
		$imagePant2 = getimagesize("../pantallas/$pantalla2");
		$ancho2     = intval($imagePant2[0] / 6);
		$alto2		= intval($imagePant2[1] / 6);
	}
	
	if (trim($pantalla3) != '') {
		$imagePant3 = getimagesize("../pantallas/$pantalla3");
		$ancho3     = intval($imagePant3[0] / 6);
		$alto3		= intval($imagePant3[1] / 6);
	}
	
	
	$selectedAM = ""; $selectedAS = ""; $selectedAE = ""; $selectedAC = ""; $selectedAA = "";
	switch($area) {
		case 'M': $selectedAM = " selected='selected' "; $selectedAS = ""; $selectedAE = ""; $selectedAC = ""; $selectedAA = ""; break;
		case 'S': $selectedAM = ""; $selectedAS = " selected='selected' "; $selectedAE = ""; $selectedAC = ""; $selectedAA = ""; break;
		case 'E': $selectedAM = ""; $selectedAS = ""; $selectedAE = " selected='selected' "; $selectedAC = ""; $selectedAA = ""; break;
		case 'C': $selectedAM = ""; $selectedAS = ""; $selectedAE = ""; $selectedAC = " selected='selected' "; $selectedAA = ""; break;
		case 'A': $selectedAM = ""; $selectedAS = ""; $selectedAE = ""; $selectedAC = ""; $selectedAA = " selected='selected' "; break;
	}
	
	
	$selectedSO = ""; $selectedSA = ""; $selectedSD = ""; $selectedSB = ""; 
	switch($situacion) {
		case 'O': $selectedSO = " selected='selected' "; $selectedSA = ""; $selectedSD = ""; $selectedSB = ""; break;
		case 'A': $selectedSO = ""; $selectedSA = " selected='selected' "; $selectedSD = ""; $selectedSB = ""; break;
		case 'D': $selectedSO = ""; $selectedSA = ""; $selectedSD = "selected='selected' "; $selectedSB = " "; break;
		case 'B': $selectedSO = ""; $selectedSA = ""; $selectedSD = ""; $selectedSB = " selected='selected' "; break;
	}

	
	// :::::::::::::::::::::::::::::::::::::::::::: Carga Plantilla ::::::::::::::::::::::::::::: //
	$contenido=new plantilla("sistemas_ficha");
	$contenido->asigna_variables(
			array(
			"lang"  				=> $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  	=> date('d-m-Y'),
            "topbar"		      	=> $topbar,
			"lateral"			  	=> $lateral,
			"H2Sistema"			  	=> 'Mantenedor de Sistemas',
			"H2Titulo"	          	=> 'Mantenedor de Sistemas',
			
			"cerrarSesion"        	=> $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  	=> $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  	=> $sistema_txt->GetDefinition('borrar'),
            "salir"        	      	=> $dg_txt->GetDefinition('salir'),
			
			"id"				    => $id,
			"nombre" 				=> $nombre,
			"descripcion"			=> $descripcion,
			"area" 					=> $area,
			"ruta" 					=> $ruta,
			"dbase"					=> $dbase,
			"icono" 				=> $icono,
			"iconoDesh" 			=> $iconoDesh,
			"situacion" 			=> $situacion,
			"version" 				=> $version,
			"prefijoTablas"			=> $prefijoTablas,
			"fechaDesa" 			=> $fechaDesa,
			
			"selectedAM" 			=> $selectedAM, 
			"selectedAS" 			=> $selectedAS, 
			"selectedAE" 			=> $selectedAE, 
			"selectedAC" 			=> $selectedAC, 
			"selectedAA" 			=> $selectedAA,
			
			"selectedSO" 			=> $selectedSO, 
			"selectedSA" 			=> $selectedSA, 
			"selectedSD"			=> $selectedSD,
			"selectedSB" 			=> $selectedSB, 
			
			"observaciones"			=> $observaciones,
			
			"_htmlSistMuni"     	=> $_htmlSistMuni,
			"_htmlSistSalud"     	=> $_htmlSistSalud,
			"_htmlSistEduc"     	=> $_htmlSistEduc,
			"_htmlSistAdmin"		=> $_htmlSistAdmin,
			"_htmlClientes"			=> $_htmlClientes,
			
			"selectedMS"			=> $selectedMS,
			"selectedMN"			=> $selectedMN,
			"selectedSS"			=> $selectedSS,
			"selectedSN"			=> $selectedSN,
			"selectedSN"			=> $selectedSN,
			"selectedEN"			=> $selectedEN,
			"selectedAS"			=> $selectedAS,
			"selectedAN"			=> $selectedAN,
			
			"icono" 				=> $icono,
			"iconoDesh" 			=> $iconoDesh,
			
			"dispCont"				=> $_dispCont,
			
			"pantalla1"				=> $pantalla1,
			"ancho1"				=> $ancho1,
			"alto1"					=> $alto1,
			"descPant1"				=> $descPant1,
			
			"pantalla2"				=> $pantalla2,
			"ancho2"				=> $ancho2,
			"alto2"					=> $alto2,
			"descPant2"				=> $descPant2,
			
			"pantalla3"				=> $pantalla3,
			"ancho3"				=> $ancho3,
			"alto3"					=> $alto3,
			"descPant3"				=> $descPant3,
			));
			
	echo $contenido->muestra();

?>
