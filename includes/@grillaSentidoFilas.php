<?php
    // ::: número de filas por página
    if (isset($input['filas']) || isset($_SESSION['filas'])) { 
        if (isset($input['filas']) && $input['filas'] != '') {
            $_filas = $input['filas'];  
        } else {
            $_filas = $_SESSION['filas'];  
        }
        
        if ($_filas == 8)	{ $fl8 = 'selected'; $fl10 = ''; $fl12 = ''; $fl15 = ''; $fl20 = ''; }
        if ($_filas == 10)	{ $fl8 = ''; $fl10 = 'selected'; $fl12 = ''; $fl15 = ''; $fl20 = ''; }
        if ($_filas == 12)	{ $fl8 = ''; $fl10 = ''; $fl12 = 'selected'; $fl15 = ''; $fl20 = ''; }
        if ($_filas == 15)	{ $fl8 = ''; $fl10 = ''; $fl12 = ''; $fl15 = 'selected'; $fl20 = ''; }
        if ($_filas == 20)	{ $fl8 = ''; $fl10 = ''; $fl12 = ''; $fl15 = ''; $fl20 = 'selected'; }
    }
   
    // ::: sentido de ordenamiento
    if (isset($input['sentido']) || isset($_SESSION['sentido'])) { 
        if (isset($input['sentido'])) {
            $_sentido = $input['sentido']; 
        } else {
            $_sentido = $_SESSION['sentido'];
        }
    }
    if ($_sentido == 'ASC') { $sentASC = 'selected'; $sentDESC = ''; } else { $sentASC = ''; $sentDESC = 'selected'; }

    // :: Cargar variables en la sesion desde el array recibido por JSON
    $_SESSION['pag']        = $_pag;
    $_SESSION['ordenarPor'] = $_ordenarPor;
    $_SESSION['sentido']    = $_sentido;
    $_SESSION['filas']      = $_filas;
?>