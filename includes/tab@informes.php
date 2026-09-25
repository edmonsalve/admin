<?php
    session_start();
	if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }

    date_default_timezone_set('America/Santiago');

    $dbname   = $_GET['dbname'];
    $dbtabla  = $_GET['dbtabla'];
    $orderBy  = $_GET['orderBy'];
    $titulo   = $_GET['titulo'];
    $fechaHoy = date("d-m-Y");

    require_once('../defines/variables_path.php');
	require_once(PATH_DEFINES . 'variables.php');
    require_once('controlAcceso.php');

    // require_once('init.php');

    $cliente  = $_SESSION['licencia'];

    require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

    define('DB_Sistema', $dbname);


    $conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD );


    /* ::::::::::::::::::::::::::::::::   lee Campos de la tabla ::::::::::::::::::::::::::::: */
    $indCampos = 1;
    $query  = "SHOW FULL COLUMNS FROM $dbtabla"; // echo "** $query ** $dbname **";

    $campos = $conexionDB->consulta($query);
    while ($row = mysqli_fetch_array($campos)) {
        $ini = strrpos($row['Type'],'(');
        $fin = strrpos($row['Type'],')');
        $lar = ($fin - $ini)-1;

        $largoCampo = substr($row['Type'],$ini+1,$lar);
        if ($largoCampo < 10) { $largoCampo = 10; }

        if ($row['Type'] == 'date') { $largoCampo = 15; }

        $arrayCampos[$indCampos]['titulo'] = $row['Comment'];
        $arrayCampos[$indCampos]['nombre'] = $row['Field'];
        $arrayCampos[$indCampos]['tipo']   = $row['Type'];
        $arrayCampos[$indCampos]['largo']  = $largoCampo;
        $indCampos++;
    }


	require_once(PATH_EXTERNOS . 'fpdf/fpdf.php');

    $lineasXpagina = 55;
    $lineas        = 0;
    $nrorow        = 0;
    $paginas       = 0;

    header("Content-Type: text/html; charset=UTF-8");
    if (IsSet($_SESSION['usuario'])) { $_username = $_SESSION['usuario']; } else { header("Location: ../index.php"); }

    /* ::::::::::::::::::::::::::::::: Formato de la Página :::::::::::::::::::::::::::: */
    $pdf = new FPDF('P','mm','A4');

    $consulta = "SELECT * FROM $dbtabla ORDER BY $orderBy";   // echo "**** $consulta ***<br>";
    $salida   = $conexionDB->consulta($consulta);
    $titulos  = 0;
    while ($row = mysqli_fetch_array($salida)) {
        /* ::::::::::::::::::::::::::::::::: Encabezado Informe :::::::::::::::::::::::::::: */
        if ($titulos == 0) {
            $titulos = 1;
            $paginas++;
        	$pdf->AddPage();
        	$pdf->Image(PATH_IMAGES.'escudo.jpg',5,5,20,22);
        	$pdf->SetFont('Arial','B',16);
        	$pdf->SetTextColor(150,150,150);
        	$pdf->Cell(190,8,$titulo,0,0,'R',0);
        	$pdf->ln();
        	$pdf->SetFillColor(150,150,150);
        	$pdf->SetFont('Arial','B',8);
        	$pdf->Cell(20,3,'',0,0,'R',0);
            $pdf->Cell(120,3,$cliente,0,0,'L',0);
            $pdf->Cell(50,3,$fechaHoy,0,0,'R',0);
        	$pdf->ln();
        	$pdf->Cell(190,8,'Pagina: '.$paginas,0,0,'R',0);
        	$pdf->ln();

        	$pdf->SetFont('Arial','',7);
        	$pdf->SetTextColor(255,255,255);

            $largoTotal = 0;
            foreach($arrayCampos as $linea  => $valor) {
                $nombre = $valor['titulo'];
                $largo  = $valor['largo']+5;

                if (substr($valor['tipo'],0,3) == 'int' or substr($valor['tipo'],0,7) == 'decimal') { $alinear = 'R'; } else { $alinear = 'L'; }

                $pdf->Cell($largo,4,$nombre,0,0,$alinear,1);
                $largoTotal += $largo;
            }
            $pdf->Cell(190-$largoTotal,4,'',0,0,$alinear,1);
            $pdf->ln();
        }

        $pdf->SetFillColor(240,240,240);
        $pdf->SetTextColor(100,100,100);
        $pdf->SetFont('Arial','',8);
        /* ===============================   FIN TITULOS  ======================================  */

        if (($lineas % 2) == 0) { $bg = 1; } else { $bg = 0; }

        /*::::: compone linea ::: */

        foreach($arrayCampos as $linea  => $valor) {
                $nombre = $valor['nombre'];
                $largo  = $valor['largo']+5;
                if ($largo > 70) {        // if (strlen($row[$nombre]) > 70) {
                    $pdf->SetFont('Arial','',6);
                    $largoStr = 95;
                } else {
                    $pdf->SetFont('Arial','',8);
                    $largoStr = strlen($row[$nombre]);
                }
                if (substr($valor['tipo'],0,3) == 'int' or substr($valor['tipo'],0,7) == 'decimal') { $alinear = 'R'; } else { $alinear = 'L'; }
                $pdf->Cell($largo,4,substr($row[$nombre],0,$largoStr),0,0,$alinear,$bg);
        }
        $pdf->Cell(190-$largoTotal,4,'',0,0,'L',$bg);
        $pdf->ln();

        $lineas++;
        $nrorow++;
        if ($lineas == $lineasXpagina) { $lineas = 0; $titulos = 0;}

    }

    $pdf->ln();$pdf->ln();
    $pdf->Cell(50,4,'Numero de registros: '.$nrorow,0,0,'L',1);
    $pdf->Cell(2,4,'',0,0,'L',0);
    $pdf->Cell(50,4,'Numero de paginas Informe: '.$paginas,0,0,'L',1);

    $pdf->Output();
?>