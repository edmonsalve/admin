<?php
    date_default_timezone_set('America/Santiago');
    header("Content-Type: text/html; charset=UTF-8");

    $IdSistema = $_GET['IdSistema'];
    $IdCliente = $_GET['IdCliente'];

    require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');
    
    require_once(PATH_EXTERNOS . 'fpdf/fpdf.php');

    class MYPDF extends FPDF {
        public function saltoPag($encabezado,$nomSistema,$nomCliente,$nomResponsable) {
            $lineas = 1;
            $titulo   = 'ACTA RECEPCION CONFORME';
            $fechaHoy = date("d-m-Y");

            $this->AddPage();
           
            $looCli = PATH_IMAGES . LOGO_CLIENTE;
            $looDoc = PATH_IMAGES . LOGO_DOC;
   
            $this->Image($looCli,30,10,30);
            $this->Image($looDoc,182,280,18);

            $this->SetFont('arial','',16);

            $this->ln();
            $this->SetTextColor(0,0,0);
            $this->Cell(20,5,'',0,0,'R',0);
            $this->Cell(170,10,utf8_decode($titulo),0,0,'R',0);
            $this->ln();$this->ln();
            
            $this->SetFont('arial','',10);
            $this->SetTextColor(255,255,255);
            $this->SetFillColor(18,79,135);
            $this->Cell(20,5,'',0,0,'R',0);
            $this->Cell(170,5,$fechaHoy,0,0,'R',1);
            $this->ln(); $this->ln();
                
         
            $this->SetTextColor(0,0,0);
            $this->SetFont('arial','B',10);
            $this->Cell(20,5,'',0,0,'R',0);
            $this->Cell(30,8,'SISTEMA: ',0,0,'L',0);
            $this->Cell(130,8,$nomSistema,0,0,'L',0);
            $this->ln();

            if($encabezado == 1) {
                $this->SetFont('arial','B',10);
                $this->Cell(20,5,'',0,0,'R',0);
                $this->Cell(30,5,'CLIENTE: ',0,0,'L',0);
                $this->Cell(130,5,$nomCliente,0,0,'L',0);
                $this->ln();
                
                $this->SetFont('arial','B',10);
                $this->Cell(20,5,'',0,0,'R',0);
                $this->Cell(30,5,'ENCARGADO:',0,0,'L',0);
                $this->Cell(100,5,$nomResponsable,0,0,'L',0);
                $this->ln();
                $this->ln();
            }

            $this->SetTextColor(255,255,255);
            $this->SetFont('arial','',6);
        
            $this->Cell(20,5,'',0,0,'R',0);
            $this->Cell(90,4,'MODULO',0,0,'L',1);
            $this->Cell(1,4,'',0,0,'L',0);
            $this->Cell(12,4,'COD',0,0,'L',1);
            $this->Cell(1,4,'',0,0,'L',0);
            $this->Cell(50,4,utf8_decode('SECCIÓN MENÚ'),0,0,'C',1);
            $this->Cell(2,4,'',0,0,'L',0);
            $this->Cell(6,4,'S/O',0,0,'C',1);
            $this->Cell(2,4,'',0,0,'L',0);
            $this->Cell(6,4,'C/O',0,0,'C',1);
        
            $this->ln(); 
            $this->SetFont('arial','',1);
            $this->Cell(190,1,'',0,0,'L',0);
            $this->ln(); 
            
            $this->SetFillColor(240,240,240);
            $this->SetTextColor(0,0,0);
            $this->SetFont('arial','',8);
        }
    }

    $pdf = new MYPDF('P','mm','A4');
    $lineasXpagina = 40;
    $lineas        = 0;

	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar('adm_dCode', DB_SERVER, DB_USER, DB_PASSWD );

    $consulta = "SELECT *   FROM  `adm_clientes`
							WHERE  idCliente = $IdCliente";

    $salida = $conexionDB->consulta($consulta);
    $row    = mysqli_fetch_array($salida);

    $id		      	= $row['idCliente'];
    $cliente        = $row['cliente'];
    $rut            = $row['rut'];
    
    $administrador1	= $row['administrador1'];
    $administrador2	= $row['administrador2'];
    $administrador3	= $row['administrador3'];

    $dBase  	  		= 'adm_@admin';
	$tablaDB	  		= "adm_modulos";

	$IdCampo	  		= "id";
	$phpFile 			= "tab_sistemas";
	$orderBy			= "tipo , modulo";
	    
    
    $consulta = "SELECT *   FROM   `adm_sistemas`
                            WHERE  `id` = $IdSistema";
                            
    $salida   = $conexionDB->consulta($consulta);
    $row      = mysqli_fetch_array($salida);
    $sistema  = $row['nombre'];
    $area     = $row['area'];

    switch ($area) {
        case 'M': $administrador = $administrador1; break; 
        case 'S': $administrador = $administrador2; break; 
        case 'E': $administrador = $administrador3; break;
        
        default: $administrador = $administrador1; break;
    }

    // ===============================  Lee modulos   ====================================== //
    $consulta = "SELECT *   FROM   `$tablaDB`, adm_estrucMenu
                            WHERE  `idsistema` = $IdSistema
                            AND    `tipo` = adm_estrucMenu.`tipoGrupo`
                            AND    `estado` = 1
                            ORDER BY $orderBy  ";
    
    $salida   = $conexionDB->consulta($consulta);
 
    $lineas = 1; $primeraVez = 1;
    while ($row = mysqli_fetch_array($salida))  {
        if ($lineas ==  1) { $pdf->saltoPag($primeraVez,$sistema,$cliente,$administrador); }
        if (($lineas % 2) == 0) { $bg = 1; } else { $bg = 0; }
     
        $pdf->Cell(20,5,'',0,0,'R',0);
        $pdf->Cell(90,4,utf8_decode($row['modulo']),0,0,'L',$bg);
        $pdf->Cell(12,4,$row['id'],0,0,'R',$bg);
        $pdf->Cell(1,4,'',0,0,'L',0);
        $pdf->Cell(50,4,$row['tituloGrupo'],0,0,'L',$bg);
        $pdf->Cell(4,4,' ',0,0,'L',0);
        $pdf->Cell(4,4,'',1,0,'L',0);
        $pdf->Cell(4,4,' ',0,0,'L',0);
        $pdf->Cell(4,4,'',1,0,'L',0);

        $pdf->ln(); 
        $pdf->SetFont('arial','',1);
        $pdf->Cell(190,1,'',0,0,'L',0);
        $pdf->ln(); 
        $pdf->SetFont('arial','',8);

        $lineas++; 
        if ($lineas > $lineasXpagina) { $lineas = 1; $primeraVez = 0; }  
    } 

    
    if ($pdf->GetY() > 210) { $pdf->saltoPag($primeraVez,$sistema,$cliente,$administrador); }  
    $pdf->Cell(20,4,'',0,0,'L',0);
    $pdf->Cell(170,4,'',0,0,'L',1);
    $pdf->ln();
    $pdf->ln();
    
    $pdf->SetTextColor(90,90,90);
	$pdf->SetFont('arial','B',8);
    $pdf->Cell(20,4,'',0,0,'L',0);
    $pdf->Cell(52,4,'RECEPCION SIN OBSERVACIONES:',0,0,'L',0);
    $pdf->Cell(2,4,'',0,0,'L',0);
    $pdf->Cell(4,4,'',1,0,'L',0);
    $pdf->Cell(10,4,'',0,0,'L',0);
    $pdf->Cell(52,4,'RECEPCION CON OBSERVACIONES:',0,0,'L',0);
    $pdf->Cell(2,4,'',0,0,'L',0);
    $pdf->Cell(4,4,'',1,0,'L',0);
	$pdf->ln();
    
    $pdf->ln();
    $pdf->Cell(20,4,'',0,0,'L',0);
    $pdf->Cell(30,5,'OBSERVACIONES:',0,0,'L',0);
    $pdf->Cell(140,5,'','B',0,'L',0);
    $pdf->ln();
    $pdf->Cell(20,4,'',0,0,'L',0);
    $pdf->Cell(170,8,'','B',0,'L',0);
    $pdf->ln();
    $pdf->Cell(20,4,'',0,0,'L',0);
    $pdf->Cell(170,8,'','B',0,'L',0);
    
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    $pdf->Cell(20,4,'',0,0,'L',0);
    $pdf->Cell(40,4,'Fecha','T',0,'C',0);
    $pdf->Cell(5,4,'',0,0,'L',0);
    $pdf->Cell(60,4,'Nombre, Firma','T',0,'C',0);
    $pdf->Cell(5,4,'',0,0,'L',0);
    $pdf->Cell(60,4,'Nombre, Firma','T',0,'C',0);
    $pdf->ln();
    
    $pdf->SetFont('arial','',6);
    $pdf->Cell(20,3,'',0,0,'L',0);
    $pdf->Cell(40,3,'',0,0,'C',0);
    $pdf->Cell(5,3,'',0,0,'L',0);
    $pdf->Cell(60,3,'Cliente',0,0,'C',0);
    $pdf->Cell(5,3,'',0,0,'L',0);
    $pdf->Cell(60,3,'dCode Limitada',0,0,'C',0);

    $pdf->Output();
?>