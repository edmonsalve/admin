<?php
$htmlCriterios = '';
    
if (isset($arrayCriterios)) {  
    foreach($arrayCriterios as $orden => $arrayC) {
        $campo      = $arrayC['campo'];
        $campoTxt   = $arrayC['descripcion'];
            
        if ($buscarpor == "$campo") { 
            $selected = " selected='selected' "; 
        } else { 
            $selected = ""; 
        }  
        $htmlCriterios .= "<option value='$campo' $selected>$campoTxt</option>";   
    }
} 
?>