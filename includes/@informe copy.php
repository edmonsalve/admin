<?php
    /*
    *   El ancho de los campos se expresa en milimetros
    *   Orientacion: Vertical:P   Horizontal:L
    *
    *
    *   $dbname        = '';   // nombre base de datos.
    *   $consultaInf   = '';   // consulta MySQL.        
    *   $ordenarpor    = '';   // campos por los que se ordena. simple: $ordenarpor = 'rut'  multiple: $ordenarpor = 'apellido1  DESC,apellido2' 
    *
    *   $cortarpor     = '';   // campo para corte de pagina.  $cortarpor = 'depto'
    *   $corteconsalto = '';   // true/false     true = Salta pagina al corte; false = Salta lineas en el corte
    *   $cortarcontit  = '';   // true/false 
    *
    *   $infGrande     = '';   // true/false   
    *
    *   $titulo        = '';   $sizeTit   // titulo del informe  
    *   $titulo2       = '';   $sizeTit2  // titulo del informe
    *   $titulo3       = '';   $sizeTit3  // titulo del informe
    *   
    *   $orientacion   = '';   // Vertical: 'P',  Horizontal: 'L'
    *   $lineasXpagina = 55;   // Filas impresas por pagina, no considera los titulos.
    *   $fontSize      = 8;    // Tamaño de la letra (6 - 8 - 10 - 12).
    *
    *   $columnnasL1[1] = array("col" => "Id",     "ancho" => 10, "tit" => "Id.",          "alin" => "L", "tot" => false,  "cons" => false);
    *   $columnnasL1[2] = array("col" => "nombre", "ancho" => 50, "tit" => "Nombre Trab.", "alin" => "L", "tot" => false,  "cons" => false);
    *   $columnnasL1[3] = array("col" => "depto",  "ancho" => 10, "tit" => "Depart.",      "alin" => "L", "tot" => false,  "cons" => false);
    *   $columnnasL1[4] = array("col" => "monto",  "ancho" => 20, "tit" => "Id.",          "alin" => "L", "tot" => true,   "cons" => false);
    *
    *   "col"       => "campo"        Nombre del campo en la tabla
    *   "oculto"    => true/false     Oculta campo usado en corte de control
    *
    *   "ancho"     => 15             Ancho en milimetros de la colummna
    *   "alin"      => "L"            Alineación L - R - C
    *   "alinT"     => "L"            Alineación L - R - C
    *   "tit"       => "titulo"       Titulo de la columna
    *   "tot"       => true/false     Totaliza o no totaliza la columna, debe ser numerica.
    *   "cons"      => true/false     Cosolida lineas iguales
    *   "numform"   => true/false     Formatea numeros
    *   "date"      => true/false     Formatea fecha    
    *   "multiCell" => true/false     
    *   "negrita"   => true/false     
    *   "utf8"      => true/false     Decodifica utf8
    *   "substr"    => 18             Largo de la cadena antes de cortar
    *   "rut"       => true/false     Formatea RUT
    *
    *   "zeroNo"    => true/false     No imprime si ceros si es true
    *   "col"       => 'FILL'  campo relleno.
    *   "txt"       => 'texto' campo relleno.
    *   
    *   "pief1"     => Pie de Firma1   
    *   "pief2"     => Pie de Firma2
    *   "pief3"     => Pie de Firma3
    *   
    *   "cargo1"    => Cargo Pie de Firma1   
    *   "cargo2"    => Cargo Pie de Firma2 
    *   "cargo3"    => Cargo Pie de Firma3
	*
	*   "docPDF"    => nombre del PDF 	
    */
    /* LEFT(`glosa`, 256) */

    ini_set("display_errors","on");    
    if (trim(session_id()) == '') { session_start(); }
    
    if (isset($infGrande)) {
        if ($infGrande) { ini_set("memory_limit","512M"); }
    }

    date_default_timezone_set('America/Santiago');
    if ($orientacion == 'P') { 
        $anchoPagina = 190; 
        $tercio      = 50;
        $separador   = 10;
        } else { 
        $anchoPagina = 280; 
        $tercio      = 50; 
        $separador   = 25;
    }
    
   
    $fechaHoy = date("d-m-Y");
    $cliente  = $_SESSION['licencia'];
    
    $conexionReporte = new DB_MySQLi;
	$conexionReporte->conectar(DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD);
  
    $encontroRegistros = false;
    
    $titulos  = 0;
    $lineas   = 0;
    $dato     = 0;
    $nrorow   = 0;
    $paginas  = 0;
    
    $muestraTotales = false;
    $hacerSalto     = false;
    $cortecontrol   = false;
    
    if (!isset($sizeTit))  { $sizeTit  = 12; }
    if (!isset($sizeTit2)) { $sizeTit2 = 12; }
    if (!isset($sizeTit3)) { $sizeTit3 = 12; }
    
    if (!isset($titulo))  { $titulo  = ''; }
    if (!isset($titulo2)) { $titulo2 = ''; }
    if (!isset($titulo3)) { $titulo3 = ''; } 
	
	if (!isset($docPDF))  { $docPDF = 'doc'; }		//****nuevo
    
	if (isset($prefijoTitulo)) { $titulo = "$prefijoTitulo $titulo"; }
	
    $datoCorte = '';
    if (isset($cortarpor)) {
        if (trim($cortarpor) != '') { $cortecontrol = true; } 
    }
    if (!isset($corteconsalto)) { $corteconsalto = false; }
    
    header("Content-Type: text/html; charset=UTF-8");
    // if (IsSet($_SESSION['usuario'])) { $_username = $_SESSION['usuario']; } else { header("Location: /index.php"); }


    require_once(PATH_EXTERNOS . 'fpdf/fpdf.php');
    /* ::::::::::::::::::::::::::::::: Formato de la Página :::::::::::::::::::::::::::: */
    $pdf = new FPDF($orientacion, 'mm','A4');
    
    if(!isset($DESC)) { $DESC = ''; }
    if (trim($ordenarpor) != '') { $consultaInf .= " ORDER BY $ordenarpor  $DESC"; }
    
    $salida   = $conexionReporte->consulta($consultaInf);
	
	if (mysqli_num_rows($salida) == 0) {
		/* ::::::::::::::::::::::::::::::::: Encabezado Informe :::::::::::::::::::::::::::: */
        if ($titulos == 0) {
            $titulos = 1;
            $paginas++;
        	$pdf->AddPage();
        	$pdf->Image(PATH_IMAGES_CLI.LOGO_DOC,5,2,20,22,'PNG');
            
        	$pdf->SetFont('Arial','B',$sizeTit);
        	$pdf->SetTextColor(150,150,150);
        	$pdf->Cell($anchoPagina,5,utf8_decode($titulo),0,0,'R',0);
        	$pdf->ln();
            
            if (trim($titulo2 != '')) {
                $pdf->SetFont('Arial','B',$sizeTit2);
                $pdf->Cell($anchoPagina,5,utf8_decode($titulo2),0,0,'R',0);   
                $pdf->ln(); 
            }
            if (trim($titulo3 != '')) {
                $pdf->SetFont('Arial','B',$sizeTit3);
                $pdf->Cell($anchoPagina,5,utf8_decode($titulo3),0,0,'R',0);   
                $pdf->ln(); 
            }
            
        	$pdf->SetFillColor(150,150,150);
        	$pdf->SetFont('Arial','B',8);
        	$pdf->Cell(20,3,'',0,0,'R',0);
        	$pdf->Cell(120,3,'',0,0,'L',0);
            $pdf->Cell($anchoPagina-140,3,utf8_decode('fecha impresión: ').$fechaHoy,0,0,'R',0);
        	$pdf->ln();
        	$pdf->Cell($anchoPagina,8,'Pagina: '.$paginas,0,0,'R',0);
        	$pdf->ln();
        	$pdf->SetFont('Arial','B',10);
            
            if (isset($desde)) {
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(1,4,'',0,0,0,0);
                $pdf->Cell(10,4,'Desde: ',0,0,0,0); 
                $pdf->Cell(15,4,toFecDMA($desde),0,0,0,0); 
                $pdf->Cell(12,4,'Hasta: ',0,0,0,0); 
                $pdf->Cell(15,4,toFecDMA($hasta),0,0,0,0); 
                if (isset($_GET['usuario'])) {
                    $pdf->Cell(15,4,'Usuario: ',0,0,'R',0); 
                    $pdf->Cell(25,4,$_GET['usuario'],0,0,'L',0); 
                }
                $pdf->ln();
            }
            
          
        	$pdf->SetFont('Arial','',7);
        	$pdf->SetTextColor(255,255,255);
            
            $hayTotales = false;
            $anchoTotalL1 = 0;
            foreach($columnnasL1 as $linea  => $valor) {
                $col    = $valor['col'];
                $ancho  = $valor['ancho'];
                $tit    = $valor['tit'];
                
                if (isset($valor['alinT']))    { $alinT     = $valor['alinT'];     } else { $alinT     = 'C'; }
                if (isset($valor['alin']))     { $alin      = $valor['alin'];      } else { $alin      = false; }
                if (isset($valor['tot']))      { $tot       = $valor['tot'];       } else { $tot       = false; }
                if (isset($valor['cons']))     { $cons      = $valor['cons'];      } else { $cons      = false; }
                if (isset($valor['numform']))  { $numfor    = $valor['numform'];   } else { $numfor    = false; }
                if (isset($valor['utf8']))     { $utf8      = $valor['utf8'];      } else { $utf8      = false; }
                if (isset($valor['oculto']))   { $oculto    = $valor['oculto'];    } else { $oculto    = false; }
                
                if($tot) {
                    $hayTotales = true;
                    if (!isset($totales[$col]))  {  $totales[$col]  = 0; } 
                    if (!isset($totCorte[$col])) {  $totCorte[$col] = 0; } 
                }
                
                if($cons) {
                    $hayConsolidacion = true;
                    $datoCons[$col]   = '';
                }

                if($utf8) {
                    $tit = utf8_decode($tit);
                }
                
                if (!$oculto) { 
                    $pdf->Cell($ancho,4,$tit,0,0,$alinT,1); 
                    $anchoTotalL1 += $ancho;
                }
            }  
            
            
            // $pdf->Cell($anchoPagina-$anchoTotalL1,4,'',0,0,$alin,1);   
            $pdf->ln();
            
            if (isset($columnnasL2)) {
                $hayTotales = false;
                $anchoTotalL2 = 0;
                foreach($columnnasL2 as $linea  => $valor) {
                    $col    = $valor['col'];
                    $ancho  = $valor['ancho'];
                    $tit    = $valor['tit'];
                    
                    if (isset($valor['alinT']))    { $alinT     = $valor['alinT'];   } else { $alinT   = 'C'; }
                    if (isset($valor['alin']))     { $alin      = $valor['alin'];    } else { $alin    = false; }
                    if (isset($valor['tot']))      { $tot       = $valor['tot'];     } else { $tot     = false; }
                    if (isset($valor['cons']))     { $cons      = $valor['cons'];    } else { $cons    = false; }
                    if (isset($valor['numform']))  { $numfor    = $valor['numform']; } else { $numfor  = false; }
                    if (isset($valor['utf8']))     { $utf8      = $valor['utf8'];    } else { $utf8    = false; }
                    if (isset($valor['zeroNo']))   { $zeroNo    = $valor['zeroNo'];  } else { $zeroNo  = false; }
                    if (isset($valor['oculto']))   { $oculto    = $valor['oculto'];  } else { $oculto  = false; }
                     
                    if($tot) {
                        $hayTotales = true;
                        if (!isset($totales[$col]))  {  $totales[$col]  = 0; }
                        if (!isset($totCorte[$col])) {  $totCorte[$col] = 0; } 
                    }
                    
                    if($utf8) {
                        $tit = utf8_decode($tit);
                    }
                    
                    $pdf->Cell($ancho,4,$tit,0,0,$alinT,1);
                    $anchoTotalL2 += $ancho;
                }  
                
                if (!$oculto) { 
                    $pdf->Cell($ancho,4,$tit,0,0,$alinT,1); 
                    $anchoTotalL2 += $ancho;
                }
                
                $pdf->ln();
            }
        }
        
        $pdf->SetFillColor(240,240,240);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',8);
        /* ===============================   FIN TITULOS  ======================================  */
	}
		
    while ($row = mysqli_fetch_array($salida)) {
        $encontroRegistros = true;
        
        /* ::::::::::::::::::::::    Corte de Control   :::::::::::::::::::::::::: */
        if ($cortecontrol) {
            if ($datoCorte != $row[$cortarpor]) { 
                if ($datoCorte != '') { $hacerSalto = true; }
                $datoCorte = $row[$cortarpor]; 
                $sizeTit2 = 8;
                if (!isset($cortarcontit)) { $cortarcontit = false; }
                if ($cortarcontit) { $titulo2 = "$datoCorte"; }
            }
        }
        
        if ($hacerSalto) {
            /* :::::::::::::::::::::  Imprime Totales Corte ::::::::::::::::::::  */
            if ($encontroRegistros) {
                foreach($columnnasL1 as $linea  => $valor) {
                    $col    = $valor['col'];
                    $ancho  = $valor['ancho'];
                    $tit    = $valor['tit'];
                    
                    if (isset($valor['alin']))     { $alin    = $valor['alin'];     } else { $alin     = false; }
                    if (isset($valor['tot']))      { $tot     = $valor['tot'];      } else { $tot      = false; }
                    if (isset($valor['cons']))     { $cons    = $valor['cons'];     } else { $cons     = false; }
                    if (isset($valor['numform']))  { $numfor  = $valor['numform'];  } else { $numfor   = false; }
                    if (isset($valor['oculto']))   { $oculto  = $valor['oculto'];   } else { $oculto   = false; }
                                         
                    if($tot) {
                        $montoTotalizado = $totCorte[$col];
                        $totCorte[$col]  = 0;
                        if ($numfor) { $montoTotalizado = number_format($montoTotalizado,0,',','.'); }
                        } else {
                        $montoTotalizado = '';    
                    }
                         
                    $largoStr = strlen($dato);   
                    $pdf->SetFont('Arial','B',$fontSize);
                    if (!$oculto) { $pdf->Cell($ancho,4,$montoTotalizado,'T',0,$alin,1); }   
                } 
            }
            if ($corteconsalto) { $lineas = 0; $titulos = 0; } else { $pdf->ln();$pdf->ln();$pdf->ln(); }
            
            $hacerSalto = false;
            $lineas +=3; 
        }
        
        /* ::::::::::::::::::::::::::::::::: Encabezado Informe :::::::::::::::::::::::::::: */
        if ($titulos == 0) {
            $titulos = 1;
            $paginas++;
        	$pdf->AddPage();
        	$pdf->Image(PATH_IMAGES_CLI.LOGO_DOC,5,2,20,22,'PNG');
            
        	$pdf->SetFont('Arial','B',$sizeTit);
        	$pdf->SetTextColor(150,150,150);
        	$pdf->Cell($anchoPagina,5,utf8_decode($titulo),0,0,'R',0);
        	$pdf->ln();
            
            if (trim($titulo2 != '')) {
                $pdf->SetFont('Arial','B',$sizeTit2);
                $pdf->Cell($anchoPagina,5,utf8_decode($titulo2),0,0,'R',0);   
                $pdf->ln(); 
            }
            if (trim($titulo3 != '')) {
                $pdf->SetFont('Arial','B',$sizeTit3);
                $pdf->Cell($anchoPagina,5,utf8_decode($titulo3),0,0,'R',0);   
                $pdf->ln(); 
            }
            
        	$pdf->SetFillColor(150,150,150);
        	$pdf->SetFont('Arial','B',8);
        	$pdf->Cell(20,3,'',0,0,'R',0);
        	$pdf->Cell(120,3,'',0,0,'L',0);
            $pdf->Cell($anchoPagina-140,3,utf8_decode('fecha impresión: ').$fechaHoy,0,0,'R',0);
        	$pdf->ln();
        	$pdf->Cell($anchoPagina,8,'Pagina: '.$paginas,0,0,'R',0);
        	$pdf->ln();
        	$pdf->SetFont('Arial','B',10);
            
            if (isset($desde)) {
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(1,4,'',0,0,0,0);
                $pdf->Cell(10,4,'Desde: ',0,0,0,0); 
                $pdf->Cell(15,4,toFecDMA($desde),0,0,0,0); 
                $pdf->Cell(12,4,'Hasta: ',0,0,0,0); 
                $pdf->Cell(15,4,toFecDMA($hasta),0,0,0,0); 
                if (isset($_GET['usuario'])) {
                    $pdf->Cell(15,4,'Usuario: ',0,0,'R',0); 
                    $pdf->Cell(25,4,$_GET['usuario'],0,0,'L',0); 
                }
                $pdf->ln();
            }
            
          
        	$pdf->SetFont('Arial','',7);
        	$pdf->SetTextColor(255,255,255);
            
            $hayTotales = false;
            $anchoTotalL1 = 0;
            foreach($columnnasL1 as $linea  => $valor) {
                $col    = $valor['col'];
                $ancho  = $valor['ancho'];
                $tit    = $valor['tit'];
                
                if (isset($valor['alinT']))    { $alinT     = $valor['alinT'];     } else { $alinT     = 'C'; }
                if (isset($valor['alin']))     { $alin      = $valor['alin'];      } else { $alin      = false; }
                if (isset($valor['tot']))      { $tot       = $valor['tot'];       } else { $tot       = false; }
                if (isset($valor['cons']))     { $cons      = $valor['cons'];      } else { $cons      = false; }
                if (isset($valor['numform']))  { $numfor    = $valor['numform'];   } else { $numfor    = false; }
                if (isset($valor['utf8']))     { $utf8      = $valor['utf8'];      } else { $utf8      = false; }
                if (isset($valor['oculto']))   { $oculto    = $valor['oculto'];    } else { $oculto    = false; }
                
                if($tot) {
                    $hayTotales = true;
                    if (!isset($totales[$col]))  {  $totales[$col]  = 0; } 
                    if (!isset($totCorte[$col])) {  $totCorte[$col] = 0; } 
                }
                
                if($cons) {
                    $hayConsolidacion = true;
                    $datoCons[$col]   = '';
                }

                if($utf8) {
                    $tit = utf8_decode($tit);
                }
                
                if (!$oculto) { 
                    $pdf->Cell($ancho,4,$tit,0,0,$alinT,1); 
                    $anchoTotalL1 += $ancho;
                }
            }  
            
            
            // $pdf->Cell($anchoPagina-$anchoTotalL1,4,'',0,0,$alin,1);   
            $pdf->ln();
            
            if (isset($columnnasL2)) {
                $hayTotales = false;
                $anchoTotalL2 = 0;
                foreach($columnnasL2 as $linea  => $valor) {
                    $col    = $valor['col'];
                    $ancho  = $valor['ancho'];
                    $tit    = $valor['tit'];
                    
                    if (isset($valor['alinT']))    { $alinT     = $valor['alinT'];   } else { $alinT   = 'C'; }
                    if (isset($valor['alin']))     { $alin      = $valor['alin'];    } else { $alin    = false; }
                    if (isset($valor['tot']))      { $tot       = $valor['tot'];     } else { $tot     = false; }
                    if (isset($valor['cons']))     { $cons      = $valor['cons'];    } else { $cons    = false; }
                    if (isset($valor['numform']))  { $numfor    = $valor['numform']; } else { $numfor  = false; }
                    if (isset($valor['utf8']))     { $utf8      = $valor['utf8'];    } else { $utf8    = false; }
                    if (isset($valor['zeroNo']))   { $zeroNo    = $valor['zeroNo'];  } else { $zeroNo  = false; }
                    if (isset($valor['oculto']))   { $oculto    = $valor['oculto'];  } else { $oculto  = false; }
                     
                    if($tot) {
                        $hayTotales = true;
                        if (!isset($totales[$col]))  {  $totales[$col]  = 0; }
                        if (!isset($totCorte[$col])) {  $totCorte[$col] = 0; } 
                    }
                    
                    if($utf8) {
                        $tit = utf8_decode($tit);
                    }
                    
                    $pdf->Cell($ancho,4,$tit,0,0,$alinT,1);
                    $anchoTotalL2 += $ancho;
                }  
                
                if (!$oculto) { 
                    $pdf->Cell($ancho,4,$tit,0,0,$alinT,1); 
                    $anchoTotalL2 += $ancho;
                }
                
                $pdf->ln();
            }
        }
        
        $pdf->SetFillColor(240,240,240);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',8);
        /* ===============================   FIN TITULOS  ======================================  */
        
        if ((($lineas+1) % 2) == 0) { $bg = 1; } else { $bg = 0; }
 
        /* ::::: compone linea :::::::::::::: */
        foreach($columnnasL1 as $linea  => $valor) {
                $col    = $valor['col'];
                $ancho  = $valor['ancho'];
                $tit    = $valor['tit'];
                
                if (isset($valor['alin']))     { $alin      = $valor['alin'];     }  else { $alin      = false; }
                if (isset($valor['tot']))      { $tot       = $valor['tot'];      }  else { $tot       = false; }
                if (isset($valor['cons']))     { $cons      = $valor['cons'];     }  else { $cons      = false; }
                if (isset($valor['numform']))  { $numfor    = $valor['numform'];  }  else { $numfor    = false; }
                if (isset($valor['date']))     { $datefor   = $valor['date'];     }  else { $datefor   = false; }
                if (isset($valor['multiCell'])){ $multiCell = $valor['multiCell'];}  else { $multiCell = false; }
                if (isset($valor['utf8']))     { $utf8      = $valor['utf8'];     }  else { $utf8      = false; }
                if (isset($valor['substr']))   { $substr    = true;               }  else { $substr    = false; }
                if (isset($valor['rut']))      { $rut       = $valor['rut'];      }  else { $rut       = false; }
                if (isset($valor['txt']))      { $txt       = $valor['txt'];      }  else { $txt       = ''; }
                if (isset($valor['zeroNo']))   { $zeroNo    = $valor['zeroNo'];   }  else { $zeroNo    = false; }
                if (isset($valor['negrita']))  { $negrita   = $valor['negrita'];  }  else { $negrita   = false; }
                if (isset($valor['oculto']))   { $oculto    = $valor['oculto'];  }   else { $oculto    = false; }
            
                if ($col == 'FILL') { 
                    $dato = ''; 
                    } else { 
                    if (trim($txt) != '') {
                      $dato  = $txt; 
                    } else {
                      $dato  = $row[$col];   
                    }
                }
                
                if ($substr) {
                    $largoStr = $valor['substr'];
                    $dato     = substr($dato,0,$largoStr);
                }
                
                if($tot) {
                    $muestraTotales = true; 
                    $totales[$col]  += $dato;
                    $totCorte[$col] += $dato;
                }
                
                if ($cons) { 
                    if ($datoCons[$col] == $dato) { $dato = ''; } else { $datoCons[$col] = $dato; }
                }
                
                if ($numfor)  { $dato = number_format($dato,0,',','.'); }
                
                if ($rut)     { 
                    $largo = (strlen($dato));
                    $dv   = substr($dato,$largo-1,1);
                    $dato = intval(substr($dato,0,$largo-1));
                    $dato = number_format($dato,0,',','.');
                    $dato = str_pad($dato,10,"0", STR_PAD_LEFT)."-".$dv;   
                }
                
                if ($datefor) { $dato = toFecDMA($dato); }
                if ($utf8)    { $dato = utf8_decode($dato); }
                if ($zeroNo and $dato == 0)  { $dato = ''; }
                
                $largoStr = strlen($dato);   
                
                if ($negrita) {  $pdf->SetFont('Arial','B',$fontSize); } else { $pdf->SetFont('Arial','',$fontSize); }
                
                if (!$multiCell) {
                    if (!$oculto) { $pdf->Cell($ancho,4,substr($dato,0,$largoStr),0,0,$alin,$bg);  }
                    } else {
                    $pdf->MultiCell($ancho,4,trim($dato),0,'',$bg);     
                } 
        } 
        $pdf->Cell($anchoPagina-$anchoTotalL1,4,'',0,0,'L',$bg);
        $pdf->ln();
        

        if (isset($columnnasL2)) {
            foreach($columnnasL2 as $linea  => $valor) {
                $col    = $valor['col'];
                $ancho  = $valor['ancho'];
                $tit    = $valor['tit'];
                
                if (isset($valor['alin']))     { $alin    = $valor['alin'];     } else { $alin    = false; }
                if (isset($valor['tot']))      { $tot     = $valor['tot'];      } else { $tot     = false; }
                if (isset($valor['cons']))     { $cons    = $valor['cons'];     } else { $cons    = false; }
                if (isset($valor['numform']))  { $numfor  = $valor['numform'];  } else { $numfor  = false; }
                if (isset($valor['date']))     { $datefor = $valor['dateform']; } else { $datefor = false; }
                if (isset($valor['utf8']))     { $utf8    = $valor['utf8'];     } else { $utf8    = false; }
                if (isset($valor['substr']))   { $substr    = true;             } else { $substr  = false; }
                if (isset($valor['rut']))      { $rut       = $valor['rut'];    } else { $rut     = false; }
                if (isset($valor['txt']))      { $txt       = $valor['txt'];    } else { $txt     = ''; }
                if (isset($valor['zeroNo']))   { $zeroNo    = $valor['zeroNo']; } else { $zeroNo  = false; }
                
                if ($col == 'FILL') { 
                    $dato = ''; 
                    } else { 
                    if (trim($txt) != '') {
                      $dato  = $txt; 
                    } else {
                      $dato  = $row[$col];   
                    }
                }
                
                if ($substr) {
                    $largoStr = $valor['substr'];
                    $dato     = substr($dato,0,$largoStr);
                }
                
                if($tot) {
                    $totales[$col]  += $dato;
                    $totCorte[$col] += $dato;
                }
                
                if ($cons) { 
                    if ($datoCons[$col] == $dato) { $dato = ''; } else { $datoCons[$col] = $dato; }
                }
                
                if ($numfor) { $dato = number_format($dato,0,',','.'); }
                
                if ($datefor) { $dato = toFecDMA($dato); }
                if ($utf8)    { $dato = utf8_decode($dato); }
                
                if ($zeroNo and $dato == 0)  { $dato = ''; }
                
                $largoStr = strlen($dato);   
                $pdf->SetFont('Arial','',$fontSize);
                $pdf->Cell($ancho,4,substr($dato,0,$largoStr),0,0,$alin,$bg);   
            } 
            $pdf->Cell($anchoPagina-$anchoTotalL2,4,'',0,0,'L',$bg);
            $pdf->ln();
        }
         
        $lineas++;
        $nrorow++;
        if ($lineas == $lineasXpagina) { $lineas = 0; $titulos = 0;}
    }
    
    /* :::::::::::::::::::::  Imprime Totales Corte ::::::::::::::::::::  */
    if ($encontroRegistros and  $cortecontrol) {
        foreach($columnnasL1 as $linea  => $valor) {
                $col    = $valor['col'];
                $ancho  = $valor['ancho'];
                $tit    = $valor['tit'];
                
                if (isset($valor['alin']))     { $alin   = $valor['alin'];    } else { $alin    = false; }
                if (isset($valor['tot']))      { $tot    = $valor['tot'];     } else { $tot     = false; }
                if (isset($valor['cons']))     { $cons   = $valor['cons'];    } else { $cons    = false; }
                if (isset($valor['numform']))  { $numfor = $valor['numform']; } else { $numfor  = false; }
                if (isset($valor['oculto']))   { $oculto = $valor['oculto']; }  else { $oculto  = false; }
                    
                if($tot) {
                    $montoTotalizado = $totCorte[$col];
                    $totCorte[$col]  = 0;
                    if ($numfor) { $montoTotalizado = number_format($montoTotalizado,0,',','.'); }
                    } else {
                    $montoTotalizado = '';    
                }
                     
                $largoStr = strlen($dato);   
                $pdf->SetFont('Arial','B',$fontSize);
                if (!$oculto) { $pdf->Cell($ancho,4,$montoTotalizado,'T',0,$alin,1); }   
        } 
        $pdf->ln();$pdf->ln();$pdf->ln();
    }
    
    $hacerSalto = false;
    $lineas +=3;
            
    /* :::::::::::::::::::::  Imprime Totales Generales ::::::::::::::::::::  */
    if ($encontroRegistros) {
        $pdf->ln();
        foreach($columnnasL1 as $linea  => $valor) {
                $col    = $valor['col'];
                $ancho  = $valor['ancho'];
                $tit    = $valor['tit'];
                
                if (isset($valor['alin']))     { $alin   = $valor['alin'];    } else { $alin    = false; }
                if (isset($valor['tot']))      { $tot    = $valor['tot'];     } else { $tot     = false; }
                if (isset($valor['cons']))     { $cons   = $valor['cons'];    } else { $cons    = false; }
                if (isset($valor['numform']))  { $numfor = $valor['numform']; } else { $numfor  = false; }
                if (isset($valor['oculto']))   { $oculto = $valor['oculto']; }  else { $oculto  = false; }
                    
                if($tot) {
                    $montoTotalizado = $totales[$col];
                    if ($numfor) { $montoTotalizado = number_format($montoTotalizado,0,',','.'); }
                    } else {
                    $montoTotalizado = '';    
                }
                     
                $largoStr = strlen($dato);   
                $pdf->SetFont('Arial','B',$fontSize);
                if (!$oculto) { $pdf->Cell($ancho,4,$montoTotalizado,'T',0,$alin,1); }   
        } 
    }
    
    if(isset($anchoTotalL1)) {
       $pdf->Cell($anchoPagina-$anchoTotalL1,4,'','T',0,$alinT,1); 
    }
    
    $pdf->ln();$pdf->ln();
    $pdf->Cell(50,4,'Numero de Registros '.$nrorow,0,0,'L',1); 
    $pdf->Cell(2,4,'',0,0,'L',0); 
    $pdf->Cell(50,4,'Numero de Pag. Informe '.$paginas,0,0,'L',1); 
    
    
    $pdf->ln();$pdf->ln();$pdf->ln();$pdf->ln();$pdf->ln();$pdf->ln();
    
    if (isset($pief1)) {
        $pdf->Cell($separador,4,'',0,0,'C',0);
        $pdf->Cell($tercio,4,$pief1,'T',0,'C',0); 
    }
    if (isset($pief2)) {
        $pdf->Cell($separador,4,'',0,0,'C',0); 
        $pdf->Cell($tercio,4,$pief2,'T',0,'C',0); 
    }
    
    if (isset($pief3)) {
        $pdf->Cell($separador,4,'',0,0,'C',0); 
        $pdf->Cell($tercio,4,$pief3,'T',0,'C',0); 
    }
    
    $pdf->ln();
    if (isset($cargo1)) {
        $pdf->Cell($separador,4,'',0,0,'C',0);
        $pdf->Cell($tercio,4,$cargo1,0,0,'C',0); 
    }
    if (isset($cargo2)) {
        $pdf->Cell($separador,4,'',0,0,'C',0); 
        $pdf->Cell($tercio,4,$cargo2,0,0,'C',0); 
    }
    if (isset($cargo3)) {
        $pdf->Cell($separador,4,'',0,0,'C',0); 
        $pdf->Cell($tercio,4,$cargo3,0,0,'C',0); 
    }

    $pdf->Output('dCODE: '.$docPDF.'.pdf', 'I');		//****nuevo
?>
