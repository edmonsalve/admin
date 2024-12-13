<?php
function impSelect($type="", $options=""){
	$html_return ="<img src=\"barcode/test.php5?value=$options\">";
	echo $html_return;
	}

$html_text=impSelect($_GET["status"], $_GET["barcode"]);
?>
