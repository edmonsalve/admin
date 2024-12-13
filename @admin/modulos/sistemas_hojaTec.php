<?php  
    require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');
	
	if (IsSet($_SESSION['usuario'])) { $usrID = $_SESSION['idUser']; } else { header("Location:/index.php"); }

	
    // ::::::::: Determina Nombre de Sript en Ejecucion ::::::::: //
    $filename    = str_replace(__DIR__.'/','',__FILE__);
    $moduloPHP   = str_replace('.php','',$filename);


    // ::::::::::::::::::::::::::::::::: DB  :::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	$DB_ADMIN = DB_ADMIN;
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_SISTEMA, DB_SERVER, DB_USER, DB_PASSWD );

    $fechaImpresion = date('d-m-Y');
   
	require_once('../includes/variablesSistema.php');  
    
    if (isset($_GET['sisId'])) {  $sisId = $_GET['sisId']; } else { $sisId = 0;  }  

    require_once(PATH_EXTERNOS . 'fpdf/fpdf.php');
    $pdf = new FPDF('P','mm','A4');
    

    //------------------------------------------------------------------  LECTURA DE SISTEMAS 
	$consulta 	= 	"SELECT   *	FROM 	`adm_sistemas`,`adm_sistemasAdmin`
								WHERE 	`adm_sistemas`.id = `adm_sistemasAdmin`.idAdmin
								AND		`adm_sistemas`.`id` = $sisId"; 

    $salida 	= $conexionDB->consulta($consulta);
    $row  		= mysqli_fetch_array($salida);
	
	$id		    	= $row['id'];
	$nombre     	= $row['nombre'];
	$descripcion	= $row['descripcion'];
	$estado			= $row['estado'];
	$area       	= $row['area'];
	$ruta      		= $row['ruta'];
	$dbase      	= $row['dbase'];
	$icono      	= $row['icono'];
	$iconoDesh  	= $row['iconoDesh'];
	$situacion  	= $row['situacion'];
	$version    	= $row['version'];
	$prefijoTablas  = $row['prefijoTablas'];
	$fechaDesa		= $row['fechaDesa'];
	$observaciones	= $row['observaciones'];
	
	$pantalla1	    = $row['pantalla1'];
	$pantalla2	    = $row['pantalla2'];
	$pantalla3	    = $row['pantalla3'];
	
	$descPant1	    = $row['descPant1'];
	$descPant2	    = $row['descPant2'];
	$descPant3	    = $row['descPant3'];
		

	$areaTXT = "";
	switch($area) {
		case 'M': $areaTXT = "Municipal"; break;
		case 'S': $areaTXT = "Salud"; break;
		case 'E': $areaTXT = "Educación"; break;
		case 'C': $areaTXT = "Comercial"; break;
		case 'A': $areaTXT = "Administración"; break;
	}
	
	$situacionTXT = ""; 
	switch($situacion) {
		case 'O': $situacionTXT = "OPERATIVO"; $filColor = "140,204,113"; break;  
		case 'A': $situacionTXT = "REQUIERE ACTUALIZACION"; $filColor = "244,208,63"; break;
		case 'B': $situacionTXT = "BAJA"; $filColor = "231,76,60"; break;
	}
		

	$pdf->AddPage();
	
	$looDoc 	= PATH_IMAGES.LOGO_DOC;
	$icoSist	= PATH_ICONOS. $icono;
	$icoSistDes	= PATH_ICONOS. $iconoDesh; 
  
	$pdf->Image($looDoc,30,10,30);
	$pdf->SetFont('Arial','',16);
	$pdf->SetTextColor(0,0,0);

	$pdf->Cell(190,10,utf8_decode("Ficha Técnica"),0,0,'R',0);
	$pdf->ln();

	$pdf->SetFont('Arial','B',10);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(18,79,135);
	$pdf->Cell(20,5,'',0,0,'R',0);
	$pdf->Cell(170,5,'',0,0,'L',1);
	$pdf->ln(); $pdf->ln();
			
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Arial','',9);
	

	$pdf->SetTextColor(255,255,255);
	$pdf->Cell(20,5,'',0,0,'R',0);
	$pdf->Cell(30,5,utf8_decode('Nombre Módulo'),0,0,'L',1);
	$pdf->SetDrawColor(150,150,150);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(50,5,utf8_decode($nombre),1,0,'L',0);
	$pdf->Cell(2,5,'',0,0,'L',0);
	$pdf->ln();
	
	$pdf->SetTextColor(255,255,255);
	$pdf->Cell(20,5,'',0,0,'R',0);
	$pdf->Cell(30,5,utf8_decode('Versión'),0,0,'L',1);
	$pdf->SetDrawColor(150,150,150);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(50,5,$version,1,0,'L',0);
	$pdf->Cell(2,5,'',0,0,'L',0);
	$pdf->ln();

	$pdf->SetTextColor(255,255,255);
	$pdf->Cell(20,5,'',0,0,'R',0);
	$pdf->Cell(30,5,'Directorio',0,0,'L',1);
	$pdf->SetDrawColor(150,150,150);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(50,5,'/'.utf8_decode($ruta),1,0,'L',0);
	$pdf->Cell(2,5,'',0,0,'L',0);
	$pdf->ln();
	
	$pdf->SetTextColor(255,255,255);
	$pdf->Cell(20,5,'',0,0,'R',0);
	$pdf->Cell(30,5,'Area',0,0,'L',1);
	$pdf->SetDrawColor(150,150,150);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(50,5,utf8_decode($areaTXT),1,0,'L',0);
	$pdf->Cell(2,5,'',0,0,'L',0);
	$pdf->ln();
	
	$pdf->Image($icoSist,130,30,22,22);
	$pdf->Image($icoSistDes,170,30,22,22);
	
	$pdf->ln();$pdf->ln();$pdf->ln();
	$pdf->SetFont('Arial','B',8);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(20,5,'',0,0,'R',0);
	$pdf->Cell(160,5,"Descripcion",'B',0,'L',0);
	$pdf->ln();
    
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(20,5,'',0,0,'R',0);
    $pdf->MultiCell(160,5,utf8_decode($descripcion),0);
	

	//--------------------------------------------------  lee Tablas
	$consultaTablas = "SELECT * FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA='des_$dbase' ORDER BY TABLE_NAME";

	$primero  	 	= TRUE;
	
	$pdf->AddPage();
	$looDoc = PATH_IMAGES.LOGO_DOC;
	$pdf->Image($looDoc,30,10,30);
	
	$pdf->ln();$pdf->ln();$pdf->ln();
	$pdf->SetFont('Arial','B',9);
	$pdf->SetFillColor(0,102,153);
	
	$pdf->ln();
	$pdf->SetFont('Arial','B',10);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(20,5,'',0,0,'R',0);
	$pdf->Cell(170,5,utf8_decode("Información Técnica"),0,0,'L',0);
	$pdf->ln();
	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetTextColor(255,255,255); 
	$pdf->Cell(20,5,'',0,0,'L',0);
	$pdf->Cell(50,5,utf8_decode("S.O. (recomendado):"),0,0,'L',1);
	$pdf->SetTextColor(0,0,0); 	
	$pdf->Cell(50,5,"Ubuntu Server 22.04 LTS",1,0,'L',0); 
	$pdf->Cell(50,5,"Licencia GLP",1,0,'L',0); 
	$pdf->ln();
	
	
	$pdf->SetTextColor(255,255,255); 
	$pdf->Cell(20,5,'',0,0,'L',0);
	$pdf->Cell(50,5,utf8_decode("Servidor HTTP:"),0,0,'L',1);
	$pdf->SetTextColor(0,0,0); 	
	$pdf->Cell(50,5,"Apache 2",1,0,'L',0); 
	$pdf->Cell(50,5,"Licencia Apache 2",1,0,'L',0); 
	$pdf->ln();
	
	$pdf->SetTextColor(255,255,255); 
	$pdf->Cell(20,5,'',0,0,'L',0);
	$pdf->Cell(50,5,utf8_decode("Lenguaje Programación:"),0,0,'L',1);
	$pdf->SetTextColor(0,0,0); 	
	$pdf->Cell(50,5,"PHP 8.1",1,0,'L',0); 
	$pdf->Cell(50,5,"Licencia PHP",1,0,'L',0); 
	$pdf->ln();
	
	$pdf->SetTextColor(255,255,255); 
	$pdf->Cell(20,5,'',0,0,'L',0);
	$pdf->Cell(50,5,"Gestor BD:",0,0,'L',1);
	$pdf->SetTextColor(0,0,0); 	
	$pdf->Cell(50,5,"MariaDB",1,0,'L',0); 
	$pdf->Cell(50,5,"GNU General Public License",1,0,'L',0); 
	$pdf->ln();$pdf->ln();
	
	$pdf->ln();$pdf->SetTextColor(255,255,255); 
	$pdf->Cell(20,5,'',0,0,'L',0);
	$pdf->Cell(30,5,"Base Datos Sistema:",0,0,'L',1);
	$pdf->SetTextColor(0,0,0); 	
	$pdf->Cell(45,5,"des_$dbase",1,0,'L',0);
	$pdf->SetTextColor(255,255,255); 
	$pdf->Cell(25,5,"Prefijo Tablas:",0,0,'L',1);
	$pdf->SetTextColor(0,0,0); 	
	$pdf->Cell(20,5,"$prefijoTablas",1,0,'L',0);
	$pdf->ln();
	$pdf->ln();

	$pdf->SetTextColor(255,255,255); 
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(20,4,"",0,0,'L',0);
	$pdf->Cell(60,4,"TABLA",'L',0,'L',1);
	$pdf->Cell(30,4,"ENGINE",0,0,'L',1);
	$pdf->Cell(30,4,"ROW_FORMAT",0,0,'L',1);
	$pdf->ln();
	
	$pdf->SetTextColor(0,0,0); 	
	$salidaT 	= $conexionDB->consulta($consultaTablas);
	while ($rowT = mysqli_fetch_array($salidaT)) { 	
		$TABLE_NAME = $rowT['TABLE_NAME'];
		$ENGINE 	= $rowT['ENGINE'];
		$ROW_FORMAT = $rowT['ROW_FORMAT'];
		
		$imprime = TRUE;
		
		if (trim($prefijoTablas) != '') {
			$largo   = strlen($prefijoTablas);
			$prefTab = substr($TABLE_NAME,0,$largo);
			if ($prefijoTablas != $prefTab) { $imprime = FALSE; }
		}
			
		if ($imprime) {
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(20,5,'',0,0,'R',0);
			$pdf->Cell(60,4,utf8_decode($TABLE_NAME),0,0,'L',0);	
			$pdf->Cell(30,4,$ENGINE,0,0,'L',0);
			$pdf->Cell(30,4,$ROW_FORMAT,0,0,'L',0);
			
			$pdf->ln();
		}
	}


	//--------------------------------------------------  lee Modulos
	$primero  	 	= TRUE;
	$titSeccionMenu = ''; 
	$iIzq			= 1;
	$iDer			= 1;
	
	$pdf->AddPage();
	$looDoc = PATH_IMAGES.LOGO_DOC;
	$pdf->Image($looDoc,30,10,30);
	
	$pdf->ln();$pdf->ln();$pdf->ln();
	$pdf->SetFont('Arial','',14);
	$pdf->SetFillColor(0,102,153);
	$pdf->SetTextColor(0,0,0); 
	
	$pdf->Cell(20,5,'',0,0,'L',0);
	$pdf->Cell(170,10,"Menu Sistema",0,0,'L',0);
	$pdf->ln();


	$pdf->SetFont('Arial','',7);
	
	
	$consulta = "SELECT * 	FROM   adm_modulos, adm_estrucMenu
							WHERE  idsistema  = $sisId
							AND    adm_modulos.tipo = adm_estrucMenu.tipoGrupo
							AND    estado = 1
							ORDER BY `columna`,`ordenColumna`,`modulo`";
	

	$salida 	= $conexionDB->consulta($consulta);
	while ($row = mysqli_fetch_array($salida)) { 
	    $idmodulo   = $row['id'];  
		$tipoGrupo 	= $row['tipoGrupo'];

		$Dir 		= $row['direcGrupo'];
		$titGrupo 	= $row['tituloGrupo'];
		$colGrupo   = $row['columna'];

		$ind 		= $row['id'];
		$modulo	  	= $row['modulo'];
		$tipo		= $row['tipo'];
		$php	    = $row['php'];
		$get        = $row['parametros'];
		$target	    = $row['target'];
		
		if ($titSeccionMenu != $titGrupo) {
			switch($colGrupo) {
				case 'I': 	$izq[$iIzq] = $titGrupo; $izqT[$iIzq] = '1'; $iIzq++; break;			
				case 'D': 	$der[$iDer] = $titGrupo; $derT[$iDer] = '1'; $iDer++; break;
			}
			$titSeccionMenu = $titGrupo;
		}
		
		switch($colGrupo) {
			case 'I':	$izq[$iIzq] = $modulo; $izqT[$iIzq] = '0'; $iIzq++; break;		
			case 'D':	$der[$iDer] = $modulo; $derT[$iDer] = '0'; $iDer++; break;
		}
	}

	if ($iIzq > $iDer) { $ind = $iIzq; } else { $ind = $iDer; } 
	
	for($i= 1; $i <= $ind; $i++) {
		$tipoI   = 0;
		$moduloI = "";
		if ($i <= $iIzq) { $tipoI 	 = $izqT[$i]; $moduloI = $izq[$i]; } 
			 
		$tipoD   = 0;
		$moduloD = "";
		if ($i <= $iDer) { $tipoD 	 = $derT[$i]; $moduloD = $der[$i]; } 	
		

		$pdf->Cell(20,5,'',0,0,'L',0);
		if ($tipoI == 1) { $pdf->SetTextColor(255,255,255); } else { $pdf->SetTextColor(0,0,0); }
		$pdf->Cell(70,5,utf8_decode($moduloI),0,0,'L',$tipoI);
		$pdf->Cell(10,5,'',0,0,'L',0);
		if ($tipoD == 1) { $pdf->SetTextColor(255,255,255); } else { $pdf->SetTextColor(0,0,0); }
		$pdf->Cell(70,5,utf8_decode($moduloD),0,0,'L',$tipoD);
		
		$pdf->ln();
	}

	// ::::::::::: PANTALLAS ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: //
	
	if (trim($pantalla1) != '') {
		$imagePant1 = getimagesize("../pantallas/$pantalla1");
		$ancho1     = intval($imagePant1[0] / 5);
		$alto1		= intval($imagePant1[1] / 5);
		
		// descPant1
		$pdf->AddPage();
		$looDoc = PATH_IMAGES.LOGO_DOC;
		$pdf->Image($looDoc,30,10,30);
		
		$pdf->ln();$pdf->ln();$pdf->ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->SetFillColor(0,102,153);
		$pdf->SetTextColor(0,0,0); 
		
		$pdf->Cell(20,5,'',0,0,'L',0);
		$pdf->Cell(170,10,utf8_decode("Pantalla: $descPant1"),0,0,'L',0);
		$pdf->ln(); $pdf->ln();

		$y = $pdf->GetY() +3;
		$pdf->Image("../pantallas/$pantalla1",30,$y,170);
	}
	
	if (trim($pantalla2) != '') {
		$imagePant2 = getimagesize("../pantallas/$pantalla2");
		$ancho2     = intval($imagePant2[0] / 6);
		$alto2		= intval($imagePant2[1] / 6);
		
		// descPant2
		$pdf->AddPage();
		$looDoc = PATH_IMAGES.LOGO_DOC;
		$pdf->Image($looDoc,30,10,30);
		
		$pdf->ln();$pdf->ln();$pdf->ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->SetFillColor(0,102,153);
		$pdf->SetTextColor(0,0,0); 
		
		$pdf->Cell(20,5,'',0,0,'L',0);
		$pdf->Cell(170,10,utf8_decode("Pantalla: $descPant2"),0,0,'L',0);
		$pdf->ln(); $pdf->ln();
		
		$y = $pdf->GetY() +3;
		$pdf->Image("../pantallas/$pantalla2",30,$y,170);
	}
	
	if (trim($pantalla3) != '') {
		$imagePant3 = getimagesize("../pantallas/$pantalla3");
		$ancho3     = intval($imagePant3[0] / 6);
		$alto3		= intval($imagePant3[1] / 6);
		
		// descPant3
		$pdf->AddPage();
		$looDoc = PATH_IMAGES.LOGO_DOC;
		$pdf->Image($looDoc,30,10,30);
		
		$pdf->ln();$pdf->ln();$pdf->ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->SetFillColor(0,102,153);
		$pdf->SetTextColor(0,0,0); 
		
		$pdf->Cell(20,5,'',0,0,'L',0);
		$pdf->Cell(170,10,utf8_decode("Pantalla: $descPant3"),0,0,'L',0);
		$pdf->ln(); $pdf->ln();
		
		$y = $pdf->GetY() +3;
		$pdf->Image("../pantallas/$pantalla3",30,$y,170);
	}
	
    $nombreDoc = $sisId."_".$nombre.".pdf";
	$pdf->Output("$nombreDoc", 'I');

?>