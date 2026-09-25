<?php
use setasign\Fpdi\Fpdi;

require_once(PATH_EXTERNOS . 'fpdf/fpdf.php');
require_once(PATH_EXTERNOS . 'fpdi/src/autoload.php');


$pdf = new Fpdi();
$pageCount = $pdf->setSourceFile($filename);

for ($n = 1; $n <= $pageCount; $n++) {
	$pdf->AddPage('P', 'Legal');
	$tplIdx = $pdf->importPage($n);
	$pdf->useTemplate($tplIdx, 0,0);
	
    if($n == 1) {
		$pdf->SetFont('Helvetica','B',10);
		$pdf->SetTextColor(0, 102, 153);
		if (isset($addImg)) {
			if (trim($addImg) != '') { $pdf->Image($addImg, $ximg, $yimg, $ancho, $alto); }
		}
		
		if (isset($addTexto1)) {
			if (trim($addTexto1) != '') { 
				$pdf->SetXY($x1, $y1);
				$pdf->Write(0, "$addTexto1");
			}
		}
		if (isset($addTexto2)) {
			if (trim($addTexto2) != '') { 
				$pdf->SetXY($x2, $y2);
				$pdf->Write(0, "$addTexto2");
			}
		}
		if (isset($addTexto3)) {
			if (trim($addTexto3) != '') { 
				$pdf->SetXY($x3, $y3);
				$pdf->Write(0, "$addTexto3");
			}
		}
		
	}
}

if (isset($rutaDoc) && file_exists($rutaDoc)) {
	// echo "<br><br><br>Borra rutaDoc: $rutaDoc<br>";
	unlink($rutaDoc);
}

$pdf->Output($rutaDoc, 'F');
// $pdf->Output('/var/www/html/dcode/appMuni/sis_ayudSoc/almacenAux/aux.pdf', 'F');
?>