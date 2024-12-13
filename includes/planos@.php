<?php
    /*
    *   $filename      = '';   // nombre de archivo de salida
    *   $typeFile      = 'F' - 'V' - 'M' :: F - Largo Fijo, V Largo Variable con separador, M Largo Fijo con separador
    *   $separador     = '|' ó ',' ó ';'
    *   $lineaEnc     = '';
    *   $lineaPie      = '';
    *   $impEnc        = TRUE/FALSE
    *   $impPie        = TRUE/FALSE
    *
    *   $dbname        = '';   // nombre base de datos.
    *   $consultaInf   = '';   // consulta MySQL.        
    *   $ordenarpor    = '';   // campos por los que se ordena. simple: $ordenarpor = 'rut'  multiple: $ordenarpor = 'apellido1  DESC,apellido2' 
    *
    *   $columnnasL1[1] = array("col" => "Id", "ancho" => 10, "zerofill" => true, "utf8" => true);
    *
    *   "col"       => "campo"        Nombre del campo en la tabla
    *   "ancho"     => 15             Ancho en milimetros de la colummna   
    *   "utf8"      => TRUE/FALSE     Decodifica utf8
    *   "rut"       => TRUE/FALSE     Formatea RUT
    *   "zerofill"  => TRUE/FALSE     Rellena con ceros a la izquierda
    *   "tipo"      => 'C' ó 'D' ó 'N' Ó 'E'  ENTEROS
    *   "last"      => TRUE/FALSE
    *   "date8"     => TRUE/FALSE
    *   "txt"       => 'texto' campo relleno.
    *   "fill"      => campo relleno.
    */
    
                                
    ini_set("display_errors","on");
    // session_start();
    
    date_default_timezone_set('America/Santiago');
    
    // require_once('../defines/variables_path.php');

    require_once(PATH_DEFINES  . 'variables.php');
    require_once(PATH_INCLUDES . 'funciones.php');
	require_once(PATH_CLASSES  . 'Class.DB_MySQLi.php');
    
    // Asegurarse primero de que el archivo existe y puede escribirse sobre el.
    //if 	(!file_exists("$ruta/$filename")) {
        if (!$gestor = fopen(PATH_PLANOSTXT . "/$filename", 'w')) {
            echo "No se puede abrir el archivo ($nombre_archivo)";
            exit;
        } else {
            chmod(PATH_PLANOSTXT ."/$filename", 0666);  
        }	
    //}
    
    $fechaHoy = date("d-m-Y");
    $cliente  = $_SESSION['licencia'];

    $conexionDB = new DB_MySQLi;
	$conexionDB->conectar($dbname, DB_SERVER, DB_USER, DB_PASSWD );
  
    $encontroRegistros = FALSE;
    $nrorow            = 0;
    
    header("Content-Type: text/html; charset=UTF-8");
    if (IsSet($_SESSION['usuario'])) { $_username = $_SESSION['usuario']; } else { header("Location: /index.php"); }
    
    if ($impEnc) {
        $lineaEnc .= "\r\n";
        if (fwrite($gestor, $lineaEnc) === FALSE) {
        	echo "No se puede escribir al archivo ($nombre_archivo)";
        	exit;
        }
    }  
      //echo "*** $consultaFile ***";
    $salida   = $conexionDB->consulta($consultaFile);
    while ($row = mysqli_fetch_array($salida)) {
        $encontroRegistros = TRUE;

        /* ::::: compone linea :::::::::::::: */
        $lineaTxt = "";
        // echo "*** <br>";
        
        foreach($campos as $linea  => $valor) {
            $txt      = ''; 
            $col      = $valor['col'];
            $ancho    = $valor['ancho'];
            $tipo     = $valor['tipo'];
        
            if (isset($valor['zerofill'])) { $zerofill  = $valor['zerofill']; } else { $zerofill  = FALSE; }
            if (isset($valor['utf8']))     { $utf8      = $valor['utf8'];     } else { $utf8      = FALSE; }
            if (isset($valor['date8']))    { $date8     = $valor['date8'];    } else { $date8     = FALSE; }
            if (isset($valor['rut']))      { $rut       = $valor['rut'];      } else { $rut       = FALSE; }
            if (isset($valor['last']))     { $last      = $valor['last'];     } else { $last      = FALSE; }
            if (isset($valor['fill']))     { $fill      = $valor['fill'];     } else { $fill      = FALSE; }
            
            if ($fill) {
                $dato  = $valor['txt']; 
                } else {
                $dato  = $row[$col]; // echo "$col - ";
            }
                
            if ($rut)     { 
                $largo = (strlen($dato));
                $dv   = substr($dato,$largo-1,1);
                $dato = substr($dato,0,$largo-1);
                $dato = number_format($dato,0,',','.');
                $dato = str_pad($dato,9,"0", STR_PAD_LEFT).$dv;   
            }
                     
            
            
            if ($utf8) { $dato = utf8_encode($dato); }
            
            if ($tipo == 'D' AND $date8) {
                $dato = substr($dato,0,4).substr($dato,5,2).substr($dato,8,2);
            }
                
            if ($tipo == 'E') { $dato = round($dato,0); }
            if ($tipo == 'N') { $dato = round($dato,2); }
            
            if ($zerofill) {
                $dato = str_pad(trim($dato),$ancho,"0", STR_PAD_LEFT);
            }
                
            if ($typeFile == 'F' or $typeFile == 'M') {
                $largoDato = (strlen(trim($dato)));
                if ($largoDato < $ancho) { 
                    switch($tipo) {
                       case 'C': $dato = str_pad($dato,$ancho," ", STR_PAD_RIGHT); break;
                       case 'N': $dato = str_pad($dato,$ancho," ", STR_PAD_LEFT);  break; 
                       case 'E': $dato = str_pad($dato,$ancho," ", STR_PAD_LEFT);  break;
                       case 'D': $dato = substr($dato,0,10);  break;
                    } 
                }
                if ($largoDato > $ancho) { $dato = substr($dato,0,$ancho); }
            } else {
               $dato = trim($dato); 
            }
            
            if (($typeFile == 'V' OR $typeFile == 'M') AND  !$last) {
                $dato .= $separador;
            }
              
            $lineaTxt .= "$dato";
        } 
        
        $lineaTxt .= "\r\n";
        
        if (fwrite($gestor, $lineaTxt) === FALSE) {
        	echo "No se puede escribir al archivo ($nombre_archivo)";
        	exit;
        }
        

        $nrorow++;
    }
    
    if ($impPie) {
        $lineaPie .= "\r\n";
        if (fwrite($gestor, $lineaPie) === FALSE) {
        	echo "No se puede escribir al archivo ($nombre_archivo)";
        	exit;
        }
    } 
    fclose($gestor);
    $enlace = "/planosTxt/$filename";   
?>