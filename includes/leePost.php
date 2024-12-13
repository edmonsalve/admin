<?php
   /* Lee POST y genera REPLACE y UPDATE*/
    $update = '<BR>';
    $into   = '(';
    $values = '(';
    
    $i = 0;
    foreach($_POST AS $campo => $valor) { 
        if  ($i > 0) { 
            $into   .= ',';
            $values .= ',';
            $update .= ',';
        }
        
        echo '$_'."$campo = ".'$_POST['.$campo."];<br>"; 
        
        $into   .= "`$campo`";
        $values .= '\'$_'."$campo'";
        $update .= "<br>`$campo` = ".'\'$_'."$campo'";
        
        $i++;
    }
    $into   .= ')';
    $values .= ')';
    $update .= ')';
    
    echo "REPLACE INTO `` $into VALUES $values  <br><br>";
    echo "UPDATE `` SET  $update";
  
?>