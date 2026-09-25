<?php
    /*
    *   $filename       = '';   // nombre de archivo de salida
    *   $tituloPlanilla = '';
    *
    *   $dbname        = '';   // nombre base de datos.
    *   $consultaFile  = '';   // consulta MySQL.        
    *
    *   $contitulo  => true/false  Si emprime o no los encabezados
    *   $conbordes  => true/false  Con bordes
    *
    *   $columnnasL1[1] = array("col" => "Id", "ancho" => 10,  "utf8" => true);
    *
    *   "col"       => "campo"        Nombre del campo en la tabla
    *   "ancho"     => 15             Ancho en milimetros de la colummna  
    *   "tit"       => "titilo col"   Titulo Columna
    *   "rut"       => true/false     Formatea RUT
    *   "zerofill"  => true/false     Rellena con ceros a la izquierda
    *   "tipo"      => 'CAR'  'INT' 'INTSIN'  'DEC' 'DATE' 'DATE8'
    *   "alin"      => 'L' 'C' 'R'   Alineación
    */
	
	/*  Si se requiere INICIALIZAR UNA VARIABLE EN MySQL
		$consultaAuxiliar = true;
		$consultaAux      = "SET @i = $serieSUBDERE;";
		
		SELECT *, @i := @i + 1 AS contador FROM ......
    */

    require PATH_VENDOR . '/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    // Generar array de letras para columnas Excel (A-Z, AA-AZ, ..., ZZ)
    $alfabeto = [];
    for ($i = 1; $i <= 702; $i++) { // 702 = ZZ
        $alfabeto[$i] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    }

    $styleArray = [
        'font' => [
            'bold' => true,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => 'd6dbdf', 
            ],
        ],
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];

    

    require_once(PATH_DEFINES  . 'variables.php');
    require_once(PATH_INCLUDES . 'funciones.php');
	require_once(PATH_CLASSES  . 'Class.DB_MySQLi.php');
    
    if (!isset($contitulo))  { $contitulo  = false; }
    if (!isset($conbordes))  { $conbordes  = false; }

    $fechaHoy = date("d-m-Y");
    $conTotales  = false;


    // :: consulta sql
    $conexionDB = new DB_MySQLi;
	$conexionDB->conectar($dbname, DB_SERVER, DB_USER, DB_PASSWD );

    $salida   = $conexionDB->consulta($consultaFile);
    $encontroRegistros = false;
    $nrorow            = 0;

    // Crear una nueva hoja de cálculo
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

    $fila = 1;
    if ($contitulo) {
        $titulo = isset($tituloPlanilla) ? $tituloPlanilla : '';
        $sheet->mergeCells('A1:' . $alfabeto[count($campos)] . '1');
        $sheet->setCellValue('A1', $tituloPlanilla);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(22);
        $sheet->getRowDimension(1)->setRowHeight(32);
        $fila = 3;
    }
    
    // Título de la colummnas planilla
    $ind = 1; 
    foreach($campos as $linea  => $valor) { 
        $tit = $valor['tit'];

        if ($indsub > 0) { $base = $alfabeto[$indsub]; }
        $columna = $alfabeto[$ind];
        $celda   = $columna.$fila;

        $sheet->getColumnDimension($columna)->setAutoSize(true);
        $sheet->getStyle($celda)->applyFromArray($styleArray);
        $sheet->setCellValue($celda, $tit);
        $ind++;
    }        
    $fila++;

    while ($row = mysqli_fetch_array($salida)) {
        $ind = 1;
        foreach($campos as $linea  => $valor) {
            $columna = $alfabeto[$ind];
            $celda   = $columna.$fila;

            $txt      = ''; 
            $col      = $valor['col'];
            $ancho    = $valor['ancho'];
            $tipo     = $valor['tipo'];

            $dato  = $row[$col];
            
			if (isset($valor['conN']))       { $conN       = $valor['conN'];       } else { $conN       = false; }
            if (isset($valor['utf8']))       { $utf8       = $valor['utf8'];       } else { $utf8       = false; }
            if (isset($valor['date8']))      { $date8      = $valor['date8'];      } else { $date8      = false; }
			// if (isset($valor['dma']))        { $dma        = $valor['dma'];        } else { $dma        = false; }
            if (isset($valor['rut']))        { $rut        = $valor['rut'];        } else { $rut        = false; }
            if (isset($valor['last']))       { $last       = $valor['last'];       } else { $last       = false; }
			if (isset($valor['sumar']))      { $sumar      = $valor['sumar'];      } else { $sumar      = false; }
			if (isset($valor['conformato'])) { $conformato = $valor['conformato']; } else { $conformato = false; }
            if (isset($valor['fillColor']))  { $fillColor  = $valor['fillColor'];  } else { $fillColor = 'ffffff'; }
        
            $alin = $valor['alin'] ?? 'L';

            $styleArrayData = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => $fillColor, 
                    ],
                ],
            ];
			
   

			if ($sumar)  { 
				if(!isset($total[$col])) { $total[$col] = 0; }
				$total[$col]  += intval($dato);  
                $conTotales = true;
			}

            if ($rut)     { 
                $largo = (strlen($dato));
                $dv   = substr($dato,$largo-1,1);
                $dato = intval(substr($dato,0,$largo-1));
                $dato = number_format($dato,0,',','.');
                $dato = str_pad($dato,9,"0", STR_PAD_LEFT).'-'.$dv;   
            }

		
			if ($conN) {
				$dato = str_replace('Ñ', 'N', $dato);
				$dato = str_replace('ñ', 'n', $dato);
			}  

            switch($tipo) {
                case 'INT':
                    // Aplica formato de número sin decimales
                    $sheet->getStyle($celda)->getNumberFormat()->setFormatCode('#,##0');
                    break;
                case 'INTSIN':
                    // Aplica formato de número sin decimales
                    $sheet->getStyle($celda)->getNumberFormat()->setFormatCode('##0');
                    break;
                case 'DEC':
                    // Aplica formato de número con decimales
                    $sheet->getStyle($celda)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    break;
                case 'DATE':
                    $sheet->getStyle($celda)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                    break;
                case 'DATE8':
                    $dato = substr($dato,0,4).substr($dato,5,2).substr($dato,8,2);
                    break;
                default:
                    $sheet->getStyle($celda)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    break;
            }

            switch($alin) {
                case 'L':
                    $sheet->getStyle($celda)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    break;
                case 'C':
                    $sheet->getStyle($celda)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    break;
                case 'R':
                    $sheet->getStyle($celda)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    break;
                default:
                    $sheet->getStyle($celda)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    break;
            }
      
            $dato = trim($dato);   
            
            $sheet->getStyle($celda)->applyFromArray($styleArrayData);
            $sheet->setCellValue($celda, $dato);
            $ind++;
            
        } 
        $fila++;
    }

    if ($conTotales) {
        $sheet->getStyle('A'.$fila)->applyFromArray($styleArray);
        $sheet->setCellValue('A'.$fila, 'Totales:');
        foreach($total as $col => $valor) {
            $ind = 1;
            foreach($campos as $linea  => $valorCampo) {
                if ($valorCampo['col'] == $col) {
                    $columna = $alfabeto[$ind];
                    $celda   = $columna.$fila;

                    $sheet->getStyle($celda)->applyFromArray($styleArray);
                    $sheet->getStyle($celda)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->setCellValue($celda, $valor);
                }
                $ind++;
            }
        }
    }  

    // Crear el writer
    $writer = new Xlsx($spreadsheet);
    
    if (isset($saveFile) && $saveFile) {
        $writer->save( $filename . '.xlsx');
        echo "$filename.xlsx";
        exit;
    } else {
        // 🔹 Limpia cualquier salida previa (espacios, warnings, etc.)
        if (ob_get_length()) ob_end_clean();
    
        // 🔹 Cabeceras correctas para XLSX
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}.xlsx\"");
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');
    
        // Enviar al navegador
        $writer->save('php://output'); 
    }
?>