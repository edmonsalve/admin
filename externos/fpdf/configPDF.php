<?php
	require_once('fpdf.php');
	
	class PDF extends FPDF {
		protected $B = 0;
		protected $I = 0;
		protected $U = 0;
		protected $HREF = '';

		function WriteHTML($html) {
			// Intérprete de HTML
			$html = str_replace("\n",' ',$html);
			$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
			foreach($a as $i=>$e)
			{
				if($i%2==0)
				{
					// Text
					if($this->HREF)
						$this->PutLink($this->HREF,$e);
					else
						$this->Write(5,$e);
				}
				else
				{
					// Etiqueta
					if($e[0]=='/')
						$this->CloseTag(strtoupper(substr($e,1)));
					else
					{
						// Extraer atributos
						$a2 = explode(' ',$e);
						$tag = strtoupper(array_shift($a2));
						$attr = array();
						foreach($a2 as $v)
						{
							if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
								$attr[strtoupper($a3[1])] = $a3[2];
						}
						$this->OpenTag($tag,$attr);
					}
				}
			}
		}

		function OpenTag($tag, $attr) {
			// Etiqueta de apertura
			if($tag=='B' || $tag=='I' || $tag=='U')
				$this->SetStyle($tag,true);
			if($tag=='A')
				$this->HREF = $attr['HREF'];
			if($tag=='BR')
				$this->Ln(5);
		}

		function CloseTag($tag) {
			// Etiqueta de cierre
			if($tag=='B' || $tag=='I' || $tag=='U')
				$this->SetStyle($tag,false);
			if($tag=='A')
				$this->HREF = '';
		}

		function SetStyle($tag, $enable) {
			// Modificar estilo y escoger la fuente correspondiente
			$this->$tag += ($enable ? 1 : -1);
			$style = '';
			foreach(array('B', 'I', 'U') as $s)
			{
				if($this->$s>0)
					$style .= $s;
			}
			$this->SetFont('',$style);
		}

		function PutLink($URL, $txt) {
			// Escribir un hiper-enlace
			$this->SetTextColor(0,0,255);
			$this->SetStyle('U',true);
			$this->Write(5,$txt,$URL);
			$this->SetStyle('U',false);
			$this->SetTextColor(0);
		}
		
		// Tabla coloreada
		function tablaColores($header, $data, $width) {
			// Colores, ancho de línea y fuente en negrita
			$this->SetFillColor(160,160,160);
			$this->SetTextColor(255);
			$this->SetDrawColor(80,80,80);
			$this->SetLineWidth(.3);
			$this->SetFont('','B');
			// Cabecera			
			for($i=0;$i<count($header);$i++)
				$this->Cell($width,5,$header[$i],1,0,'C',true);
			$this->Ln();
			// Restauración de colores y fuentes
			$this->SetFillColor(255,255,255);
			$this->SetTextColor(0);
			$this->SetFont('');
			// Datos
			for($i=0;$i<count($data);$i++)
				$this->Cell($width,6,$data[$i],1,0,'C',true);
			$this->Ln();
			// Línea de cierre
			$this->Cell($width*$i,0,'','T');
		}		
	}
?>