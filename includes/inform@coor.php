<?php
    $dbname = $_GET['dbname'];
    
    require_once('init.php');
    require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');
    
    define('DB_Sistema', $dbname);
    $conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD );
    
    
    require_once(PATH_EXTERNOS . 'fpdf/fpdf.php');
    /* ::::::::::::::::::::::::::::::: Formato de la Página :::::::::::::::::::::::::::: */
    $consultaInf = "SELECT * FROM `pcir_setxy` WHERE `id` = 999";
    $salida      = $conexionDB->consulta($consultaInf);
    $rowXY       = mysqli_fetch_array($salida);
    $x           = $rowXY['set_x'];
    $y           = $rowXY['set_y'];
    
    
    $pdf = new FPDF('P','mm',array($x,$y));
    $pdf->AddPage();
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Arial','B',6);
  
    $consultaInf = "SELECT * FROM `pcir_setxy` WHERE `id` < 999 ORDER BY set_y, set_x";
    $salida      = $conexionDB->consulta($consultaInf);
    
    while ($rowXY = mysqli_fetch_array($salida)) { 
        $x = $rowXY['set_x'];
        $y = $rowXY['set_y'];
        $t = $rowXY['titulo'].' '.$x.'-'.$y;
        $pdf->SetXY($x,$y);
        $pdf->Cell(10,2,$t,'LB',0,'L',0);
    }
        
    $pdf->Output();
	
?>