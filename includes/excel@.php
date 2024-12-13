<?php
    /*
    *   $filename       = '';   // nombre de archivo de salida
    *   $tituloPlanilla = '';
    *
    *   $dbname        = '';   // nombre base de datos.
    *   $consultaFile  = '';   // consulta MySQL.        
    *
    *   $contitulo  => TRUE/FALSE  Si emprime o no los encabezados
    *   $conbordes  => TRUE/FALSE  Con bordes
    *
    *   $columnnasL1[1] = array("col" => "Id", "ancho" => 10,  "utf8" => true);
    *
    *   "col"       => "campo"        Nombre del campo en la tabla
    *   "ancho"     => 15             Ancho en milimetros de la colummna  
    *   "tit"       => "titilo col"   Titulo Columna
    *   "utf8"      => TRUE/FALSE     Decodifica utf8
    *   "rut"       => TRUE/FALSE     Formatea RUT
    *   "zerofill"  => TRUE/FALSE     Rellena con ceros a la izquierda
    *   "tipo"      => 'C' ó 'D' ó 'N' Ó 'E'  ENTEROS
    *   "date8"     => TRUE/FALSE
    *   "formato"   => 'mso-number-format:\@'  para textos;  'mso-number-format:00'  para numeros
    */
	
	/*  Si se requiere INICIALIZAR UNA VARIABLE EN MySQL
		$consultaAuxiliar = TRUE;
		$consultaAux      = "SET @i = $serieSUBDERE;";
		
		SELECT *, @i := @i + 1 AS contador FROM ......
	*/
    
	if (!isset($xpantalla))  { $xpantalla  = FALSE; }
    if (!isset($contitulo))  { $contitulo  = FALSE; }
    if (!isset($conbordes))  { $conbordes  = FALSE; }
	if (!isset($conformato)) { $conformato = FALSE; }
	
 
    header("Content-Type: text/html; charset=UTF-8");
	if (!$xpantalla) { 
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-Transfer-Encoding: Binary");
		header("Content-Disposition: attachment; filename=$filename");
	}
                                  
    ini_set("display_errors","on");
    
    date_default_timezone_set('America/Santiago');

    require_once(PATH_DEFINES  . 'variables.php');
    require_once(PATH_INCLUDES . 'funciones.php');
	require_once(PATH_CLASSES  . 'Class.DB_MySQLi.php');
    
    $fechaHoy = date("d-m-Y");

    $conexionDB = new DB_MySQLi;
	$conexionDB->conectar($dbname, DB_SERVER, DB_USER, DB_PASSWD );
	
	if (!isset($consultaAuxiliar)) { $consultaAuxiliar = FALSE; }
	if ($consultaAuxiliar) {
		$conexionDB->consulta($consultaAux);
	}
	
    $salida   = $conexionDB->consulta($consultaFile);
    
	
    $encontroRegistros = FALSE;
    $nrorow            = 0;
    
    
    $excel  = "<table>";
    
    if ($contitulo) {  
        $excel .= "
                <thead>
                    <tr style='font-size:16px; font-weight:bold;'>$tituloPlanilla</tr>
                </thead>
                <tbody style='border:#cccccc 1px solid; ' >";
    }  
    
    while ($row = mysqli_fetch_array($salida)) {
        $encontroRegistros = TRUE;

        /* ::::: compone linea :::::::::::::: */
		if ($nrorow == 0) {
			$excel .= "<tr>";
			foreach($campos as $linea  => $valor) { 
				$col      = $valor['col'];
				$ancho    = $valor['ancho'];
				$tit      = $valor['tit'];
				$excel .= "<td style='width:$ancho px; font-size:10px; text-align:center; font-weight:bold; background-color:#cccccc; '>$tit</td>";
			}
			$excel .= "</tr>";
		}
       
        
        $excel .= "<tr>";
        foreach($campos as $linea  => $valor) {
            $txt      = ''; 
            $col      = $valor['col'];
            $ancho    = $valor['ancho'];
            $tipo     = $valor['tipo'];
        

            if (isset($valor['utf8']))       { $utf8       = $valor['utf8'];       } else { $utf8       = FALSE; }
            if (isset($valor['date8']))      { $date8      = $valor['date8'];      } else { $date8      = FALSE; }
			if (isset($valor['dma']))        { $dma        = $valor['dma'];        } else { $dma        = FALSE; }
            if (isset($valor['rut']))        { $rut        = $valor['rut'];        } else { $rut        = FALSE; }
            if (isset($valor['last']))       { $last       = $valor['last'];       } else { $last       = FALSE; }
			if (isset($valor['sumar']))      { $sumar      = $valor['sumar'];      } else { $sumar      = FALSE; }
			if (isset($valor['conformato'])) { $conformato = $valor['conformato']; } else { $conformato = FALSE; }
			
			$formato   = "";
            if ($conformato)  { 
				$conformato = $valor['conformato'];
				if ($conformato) { $formato   = "mso-number-format:'#\,##0';";  }
			}
			
            $dato  = $row[$col];
			
			if ($sumar)  { 
				if(!isset($total[$col])) { $total[$col] = 0; }
				$total[$col]  += intval($dato);  
			}
                
            if ($rut)     { 
                $largo = (strlen($dato));
                $dv   = substr($dato,$largo-1,1);
                $dato = intval(substr($dato,0,$largo-1));
                $dato = number_format($dato,0,',','.');
                $dato = str_pad($dato,9,"0", STR_PAD_LEFT).'-'.$dv;   
            }
          
            if ($utf8) { $dato = utf8_encode($dato); }
            
            if ($tipo == 'D' AND $date8) {
                $dato = substr($dato,0,4).substr($dato,5,2).substr($dato,8,2);
            }
            
			if ($tipo == 'D' AND $dma) {
                $dato = toFecDMA($dato);
            }
			
            if ($tipo == 'E') { $dato = round($dato,0); }
			if ($tipo == 'N') { $dato = round($dato,2); }  
            if ($tipo == 'T') { $dato = " $dato"; }
			
            $dato = trim($dato);   
            
            if ($conbordes)  { $borde = "border-left:#cccccc 1px solid;"; } else { $borde = ""; }
			
            $excel .= '<td style="'.$formato.' width:'.$ancho.'px; '.$borde.'">'.$dato.'</td>'; 
        } 
        $excel .= "</tr>";
        $nrorow++;
    }
	
	if (isset($total)) {
		foreach($campos as $linea  => $valor) { 
			$col      = $valor['col'];
		
			$formato   = "mso-number-format:'#\,##0';"; 
			if (isset($total[$col])) { $totalFor = $total[$col]; } else  { $totalFor = ""; }
			$excel .= '<td style="'.$formato.' width:'.$ancho.'px; '.$borde.'  font-weight:bold; background-color:#cccccc; ">'.$totalFor.'</td>';
		}
	}
    
    $excel .= "</tbody>
               </table>";
               
    echo "$excel";
?>