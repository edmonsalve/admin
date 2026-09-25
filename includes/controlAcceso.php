<?php
    // ::: DEFINE CONSTANTES  DE ACCESO, BORRAR, GUARDAR  
    define('PERMITE_ACCESO',  $_SESSION['permiteAcceso']);
    
    if (PERMITE_ACCESO) {
        define('PERMITE_BORRAR',  $_SESSION['delete']);
        define('PERMITE_GUARDAR', $_SESSION['save']);
        

    } else {
        header("Location:/index.php");
    }
?>