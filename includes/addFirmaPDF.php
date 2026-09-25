<?php
use setasign\Fpdi\Fpdi;

require_once(PATH_EXTERNOS . 'fpdf/fpdf.php');
// require_once(PATH_EXTERNOS . 'tcpdf/tcpdf.php');
require_once(PATH_EXTERNOS . 'fpdi/src/autoload.php');

IF (!isset($tamFuente)) { $tamFuente = 10; }
	
$pdf = new Fpdi();
$pageCount = $pdf->setSourceFile($filename);

// Configurar el certificado
$certificate = 'file://'.realpath('/home/varios/cert.crt');   //0
$clavePri    = 'file://'.realpath('/home/varios/privkey.pem');
$certificate = 'file://'.realpath('/home/varios/ems.crt');    //1
$clavePri    = 'file://'.realpath('/home/varios/privkey.pem');

$info = array(
	'Name'		=> 'Edmundo Monsalvve',
	'Location' 	=> 'dCode Limitada',
	'Reason' 	=> 'Acta de Entrega ayuda social',
);
// $pdf->setSignature($certificate, $clavePri, 'kcm88ix3', '', 2, $info);


for ($n = 1; $n <= $pageCount; $n++) {
	$pdf->AddPage();
	$tplIdx = $pdf->importPage($n);
	$pdf->useTemplate($tplIdx, 0,0);
	
  
	$pdf->SetFont('Helvetica','B',$tamFuente);
	$pdf->SetTextColor(0, 102, 153);
	$pdf->SetXY($x1, $y1);
	$pdf->Write(0, "Hola!");
	
	
		

}

$pdf->Output($rutaDoc, 'F');
?>