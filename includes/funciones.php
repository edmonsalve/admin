<?php
    function toFecDMA($fecha) {
        $fechToDMA = substr($fecha,8,2).'-'.substr($fecha,5,2).'-'.substr($fecha,0,4);
        return($fechToDMA);
    }
    
    function toFecAMD($fecha) {
        $fechToAMD = substr($fecha,6,4).'-'.substr($fecha,3,2).'-'.substr($fecha,0,2);
        return($fechToAMD);
    }
    
    function diferenciaMeses($fechaDesde,$fechaHasta) {
         
        $fechaIni = $fechaDesde;
        $date     = $fechaHasta;  // date("Y/n/d");
        $months   = 0;
        
        $activationdate = date("Y/n/d", strtotime ($fechaIni));
        $years          = date("Y", strtotime("now")) - date("Y", strtotime($activationdate));
        
        if (date ("Y", strtotime($date)) == date ("Y", strtotime($activationdate))){
            $months = date ("m", strtotime($date)) - date ("m", strtotime($activationdate));
            }
            elseif ($years == "1"){
                $months = (date ("m", strtotime("December")) - date ("m", strtotime($activationdate))) + (date ("m"));
            }
            elseif($years >= "2"){
                $months = ($years*12) + (date ("m", strtotime("now")) - date ("m", strtotime($activationdate)));
            }
        return($months);
    }
    
    /*
    function XcalcularIpc($aaaaCAL,$mmCAL) {
       $ipcAcumulado = 0;
       $aaaaAct      = date('Y');
       $mmAct        = date('m');
       
       $base2008 = 98.62;
       $base2009 = 99.51;
       $base2013 = 100;
       
       $mesIni = $mmCAL - 2;   // Nunca sera Negativo el Primer mes de pago es Marzo (3-2 = 1 Toma ENE)
       $mesFin = $mmAct - 2;   // Puede ser negativo o 0: -2 => OCT Año Anterior
                               //                         -1 => NOV Año Anterior
                               //                          0 => DIC Año Anterior
       
       $aaaaHasta = $aaaaAct;
       if ($mesFin < 1) {
            switch ($mesFin)  {
                case  0: $mesFin = 12; $aaaaHasta--; break;
                case -1: $mesFin = 11; $aaaaHasta--; break;
                case -2: $mesFin = 10; $aaaaHasta--; break;
            }
       }
       
       $conexionIPC = new DB_MySQL;
	   $conexionIPC->conectar(DB_COMUN_IND, DB_SERVER, DB_USER, DB_PASSWD ); 

       // :::::  lee Puntos Iniciales  :::::::::::::::::::::::::::::::::::::::::::::::: 
       $consultaIPC = "SELECT * FROM `comun_utm` WHERE `aaaa` = $aaaaCAL"; 
       $salida      = $conexionIPC->consulta($consultaIPC);
       $rowIPC      = mysqli_fetch_array($salida,MYSQLI_ASSOC);
       $puntosIni   = $rowIPC['ipc_'.$mesIni];
       mysqli_free_result($salida);

       // :::::  lee Puntos Finales  :::::::::::::::::::::::::::::::::::::::::::::::::: 
       // Lee Hasta 2013
       $consultaIPC = "SELECT * FROM `comun_utm` WHERE `aaaa` = 2013"; 
       $salida      = $conexionIPC->consulta($consultaIPC);
       $rowIPC      = mysqli_fetch_array($salida,MYSQLI_ASSOC);
       $puntosFin   = $rowIPC['ipc_12'];
       mysqli_free_result($salida);
       
       $ipcAcumulado = 0; 
       if ($puntosIni != 0) {
            // if ($aaaaCAL > 0) { $ipcAcumulado = round((($puntosFin/$puntosIni)-1)*100,1); }
            if ($aaaaCAL > 0) { $ipcAcumulado = round((($puntosFin - $puntosIni)/$puntosIni)*100,1); }
            if ($ipcAcumulado < 0) { $ipcAcumulado = 0; }
       }

       // Lee  2014 en Adelante
       $consultaIPC = "SELECT * FROM `comun_utm` WHERE `aaaa` = $aaaaHasta"; 
       $salida      = $conexionIPC->consulta($consultaIPC);
       $rowIPC      = mysqli_fetch_array($salida,MYSQLI_ASSOC);
       $puntosFin   = $rowIPC['ipc_'.$mesFin];
       mysqli_free_result($salida);
    
       if ($aaaaCAL > 0) { $ipcAcumulado += round((($puntosFin - 100)/100)*100,1); }
       if ($ipcAcumulado < 0) { $ipcAcumulado = 0; }
       
       return($ipcAcumulado);
    }
    */
    
    function calcularIpcGeneral($aaaaCAL,$mmCAL) { 
       
       $ipcAcumulado = 0;
       $aaaaAct      = date('Y');
       $mmAct        = date('m');
    
       
       $mesIni = $mmCAL -2;    // - 2;  Nunca sera Negativo el Primer mes de pago es Marzo (3-2 = 1 Toma ENE)
                               //       Puede ser negativo o 0: -2 => OCT Año Anterior
                               //                               -1 => NOV Año Anterior
                               //                                0 => DIC Año Anterior
       
	   if ($mesIni < 1) {
             switch ($mesIni)  {
                case  0: $mesIni = 12; $aaaaCAL--; break;
                case -1: $mesIni = 11; $aaaaCAL--; break;
                case -2: $mesIni = 10; $aaaaCAL--; break;
            }
       }

	   
       $mesFin = $mmAct - 2; 
       
       $aaaaHasta = $aaaaAct;
       if ($mesFin < 1) {
            switch ($mesFin)  {
                case  0: $mesFin = 12; $aaaaHasta--; break;
                case -1: $mesFin = 11; $aaaaHasta--; break;
                case -2: $mesFin = 10; $aaaaHasta--; break;
            }
       }
  
       // echo "MI $mesIni  MF  $mesFin   AF $aaaaHasta  <br>";
  
       if (strlen($mesIni) == 1) { $mesIniCampo = '0'.$mesIni; } else { $mesIniCampo = $mesIni; } 
       if (strlen($mesFin) == 1) { $mesFinCampo = '0'.$mesFin; } else { $mesFinCampo = $mesFin; }
       
       
       $conexionIPC = new DB_MySQLi;
	   $conexionIPC->conectar(DB_COMUN_IND, DB_SERVER, DB_USER, DB_PASSWD ); 

       
       
       $ipcAcumulado       = 0; 
       $ipcAcumulado_a2009 = 0;
       $puntosIniOld       = 0;
       $puntosFin2009      = 0;
   

       
       /* :::::  Lee Puntos Iniciales :::::::::::: */
       $mesIniv    = $mesIniCampo; 
       $aaaaNew    = $aaaaCAL;

       IF($aaaaCAL < 2010) {
            $consultaIPC = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = $aaaaNew"; 
            $salida      = $conexionIPC->consulta($consultaIPC);
            $rowIPC      = mysqli_fetch_array($salida,MYSQLI_ASSOC);
            $puntosIni   = $rowIPC['ipc_'.$mesIniCampo];
            mysqli_free_result($salida);  
            
            $consulta   = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = 2009"; 
            $salida     = $conexionIPC->consulta($consulta);
            $rowIPC     = mysqli_fetch_array($salida,MYSQLI_ASSOC);
            $puntosFin  = 98.62;
            mysqli_free_result($salida);     
            
            $ipcAcumulado_a2009 = (($puntosFin / $puntosIni) *100) - 100;
            
            if ($ipcAcumulado_a2009 < 0) { $ipcAcumulado_a2009 = 0; }
            
            // echo "$aaaaNew-$mesIniCampo -- <br>IPC 2009  $ipcAcumulado_a2009 = (($puntosFin / $puntosIni) *100) - 100;  <br>";
            
            $aaaaNew = 2010;
            
            // $puntosIni = 75.45;   
       }
       
       $consultaIPC = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = $aaaaNew"; 
       $salida      = $conexionIPC->consulta($consultaIPC);
       $rowIPC      = mysqli_fetch_array($salida,MYSQLI_ASSOC);
       $puntosIni   = $rowIPC['ipc_'.$mesIniCampo];
       mysqli_free_result($salida);
       
       /* :::::  Lee Puntos Finales :::::::::::: */
       $consulta   = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = $aaaaHasta"; 
       $salida     = $conexionIPC->consulta($consulta);
       $rowIPC     = mysqli_fetch_array($salida,MYSQLI_ASSOC);
       $puntosFin  = $rowIPC['ipc_'.$mesFinCampo];
       mysqli_free_result($salida);
       
       if ($aaaaCAL > 0) { $ipcAcumulado = (($puntosFin / $puntosIni) *100) - 100;  }
       
       $ipcAcumulado += $ipcAcumulado_a2009;
       
       if ($ipcAcumulado < 0) { $ipcAcumulado = 0; }
       
       
       $ipc = round($ipcAcumulado,1); 
           
       
       /*
       echo "$aaaaCAL,$mmCAL<br>  $ipcAcumulado = (($puntosFin / $puntosIni) *100) - 100;  <br>";
       echo "$aaaaCAL, $mmCAL ||** MesIni: $mesIni ** MesFin: $mesFin  *** <br>";
       echo "PI OLD: $puntosIniOld  -  PF OLD: $puntosFin2009  <br> ";
       echo "PI NEW: $puntosIni  -  PF NEW: $puntosFin  <br> ";
       echo "IPC Acum 2009: $ipcAcumulado_a2009  -- IPC2014 : $ipcAcumulado  -- IPC TOT: $ipc <br>";
       echo "======================================================================================<br>";
       */
       return($ipc);
    }
    
    //  ($aaaaCAL,$mmCAL,$mmSC)
    function calcularCM($aaaaCAL,$mmCAL,$mmSC) {
       
       $ipcAcumulado = 0;
       $aaaaAct      = date('Y');
       $mmAct        = date('m');
       
       $base2008 = 98.62;
       $base2009 = 90.28;
       
       $mesIni = $mmCAL -2;    // - 2;  Nunca sera Negativo el Primer mes de pago es Marzo (3-2 = 1 Toma ENE)
                               //       Puede ser negativo o 0: -2 => OCT Año Anterior
                               //                               -1 => NOV Año Anterior
                               //                                0 => DIC Año Anterior
                               
       $mesFin = $mmSC - 2; 
       
       if (strlen($mesIni) == 1) { $mesIniCampo = '0'.$mesIni; } else { $mesIniCampo = $mesIni; } 
       if (strlen($mesFin) == 1) { $mesFinCampo = '0'.$mesFin; } else { $mesFinCampo = $mesFin; }
 
       $aaaaHasta = $aaaaCAL;
       if ($mesFin < 1) {
            switch ($mesFin)  {
                case  0: $mesFin = 12; $aaaaHasta--; break;
                case -1: $mesFin = 11; $aaaaHasta--; break;
                case -2: $mesFin = 10; $aaaaHasta--; break;
            }
       }
       //  ECHO "$aaaaCAL,$mmCAL,$mmSC ** $aaaaHasta *** <BR>";
       
       $conexionIPC = new DB_MySQLI;
	   $conexionIPC->conectar(DB_COMUN_IND, DB_SERVER, DB_USER, DB_PASSWD ); 
    
       $ipcAcumulado       = 0; 
       $ipcAcumulado_a2009 = 0;
       $puntosIniOld       = 0;
       $puntosFin2009      = 0;
       
       if ($aaaaCAL < 2010) {
           $aaaaCALOld     = $aaaaCAL; 
           $mesIniCampoOld = $mesIniCampo;   //  -1;
           
           if ($mesIniCampoOld < 1) {
                switch ($mesIniCampoOld)  {
                    case  0: $mesIniCampoOld = 12; $aaaaCALOld--; break;
                    case -1: $mesIniCampoOld = 11; $aaaaCALOld--; break;
                    case -2: $mesIniCampoOld = 10; $aaaaCALOld--; break;
                }
           }
           
           /* :::::  lee Puntos Iniciales  :::::::::::::::::::::::::::::::::::::::::::::::: */
           $consultaIPC    = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = $aaaaCALOld"; 
           $salida         = $conexionIPC->consulta($consultaIPC);
           $rowIPC         = mysqli_fetch_array($salida,MYSQLI_ASSOC);
           $puntosIniOld   = $rowIPC['ipc_'.$mesIniCampoOld];
           mysqli_free_result($salida);
            echo "**$mesIniCampoOld **** $consultaIPC<br>";
           /* :::::  lee Puntos Finales  :::::::::::::::::::::::::::::::::::::::::::::::::: */
           // Lee Hasta 2009
           $consulta       = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = 2009"; 
           $salida         = $conexionIPC->consulta($consulta);
           $rowIPC         = mysqli_fetchi_array($salida,MYSQLI_ASSOC);
           $puntosFin2009  = $rowIPC['ipc_12'];
           mysqli_free_result($salida);
       
           if ($puntosIniOld != 0) {
                if ($aaaaCAL > 0) { $ipcAcumulado_a2009 = ((($puntosFin2009 / $puntosIniOld) * 100 ) - 100) +.3; }
                if ($ipcAcumulado_a2009 < 0) { $ipcAcumulado_a2009 = 0; }
                // echo "**** $ipcAcumulado_a2009<br>";
           }
       }
       
       /* :::::  Lee Puntos desde 2014 por cambio de Base :::::::::::: */
       
       /* :::::  Lee Puntos Iniciales :::::::::::: */
       if ($aaaaCAL < 2010) { 
            $mesIni2010 = '01'; 
            $aaaaNew    = 2010;
            } else { 
            $mesIni2010 = $mesIniCampo; 
            $aaaaNew    = $aaaaCAL;
       }
       
       $consultaIPC = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = $aaaaNew"; 
       $salida      = $conexionIPC->consulta($consultaIPC);
       $rowIPC      = mysqli_fetch_array($salida,MYSQLI_ASSOC);
       $puntosIni   = $rowIPC['ipc_'.$mesIni2010];
       mysqli_free_result($salida);
       
       
       /* :::::  Lee Puntos Finales :::::::::::: */
       $consulta   = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = $aaaaHasta"; 
       $salida     = $conexionIPC->consulta($consulta);
       $rowIPC     = mysqli_fetch_array($salida,MYSQLI_ASSOC);
       $puntosFin  = $rowIPC['ipc_'.$mesFinCampo];
       mysqli_free_result($salida);
       
       if ($aaaaCAL > 0) { $ipcAcumulado += (($puntosFin / $puntosIni) *100) - 100; }
       if ($ipcAcumulado < 0) { $ipcAcumulado = 0; }
       
       if ($aaaaCAL > 2013) { 
            $ipc = round($ipcAcumulado,1); 
            } else { 
            $ipcAcumulado_a2009 = $ipcAcumulado_a2009 + (($ipcAcumulado_a2009 * $ipcAcumulado ) / 100); 
            $ipc = round($ipcAcumulado_a2009,1) + round($ipcAcumulado,1); ;
       }
       
       
       /*
       echo "$aaaaCAL, $mmCAL ||** MesIni: $mesIni ** MesFin: $mesFin  *** <br>";
       echo "PI OLD: $puntosIniOld  -  PF OLD: $puntosFin2009  <br> ";
       echo "PI NEW: $puntosIni  -  PF NEW: $puntosFin  <br> ";
       echo "IPC Acum 2009: $ipcAcumulado_a2009  -- IPC2014 : $ipcAcumulado  -- IPC TOT: $ipc <br>";
       echo "======================================================================================<br>";
       */
       return($ipc);
    }
    
    function calcularCMOri ($aaaaCAL,$mmCAL,$mmSC) {
       echo "$aaaaCAL,$mmCAL,$mmSC";
       $ipcAcumulado = 0;
       $aaaaAct      = date('Y');
       $mmAct        = date('m');
       
       $base2008 = 98.62;
       $base2009 = 99.51;
       
       $mesIni = $mmCAL - 2;   // Nunca sera Negativo el Primer mes de pago es Marzo (3-1 = 1 Toma FEB)
       $mesFin = $mmSC  ;   // Puede ser negativo o 0: -2 => OCT Año Anterior
                               //                         -1 => NOV Año Anterior
                               //                          0 => DIC Año Anterior
       
       $aaaaHasta = $aaaaAct;
       /*
       if ($mesFin < 1) {
            switch ($mesFin)  {
                case  0: $mesFin = 12; $aaaaHasta--; break;
                case -1: $mesFin = 11; $aaaaHasta--; break;
                case -2: $mesFin = 10; $aaaaHasta--; break;
            }
       }
       */
       
       
       $conexionIPC = new DB_MySQLi;
       
       if (strlen($mesIni) == 1) { $mesIniCampo = '0'.$mesIni; } else { $mesIniCampo = $mesIni; } 
       if (strlen($mesFin) == 1) { $mesFinCampo = '0'.$mesFin; } else { $mesFinCampo = $mesFin; }
       
       $conexionIPC->conectar(DB_COMUN_IND, DB_SERVER, DB_USER, DB_PASSWD );   
       /* :::::  lee Puntos Iniciales  :::::::::::::::::::::::::::::::::::::::::::::::: */
       $consulta   = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = $aaaaCAL"; 
       $salida     = $conexionIPC->consulta($consulta);
       $rowIPC     = mysqli_fetch_array($salida,MYSQLi_ASSOC);
       $puntosIni  = $rowIPC['ipc_'.$mesIniCampo];
       mysqli_free_result($salida);

       /* :::::  lee Puntos Finales  :::::::::::::::::::::::::::::::::::::::::::::::::: */
       $consulta   = "SELECT * FROM `comun_tabUtm` WHERE `aaaa` = $aaaaCAL";   // $aaaaHasta
       $salida     = $conexionIPC->consulta($consulta);
       $rowIPC     = mysqli_fetch_array($salida,MYSQLi_ASSOC);
       $puntosFin  = $rowIPC['ipc_'.$mesFinCampo];
       mysqli_free_result($salida);
       
       $ipcAcumulado = 0; 
       if ($puntosIni != 0) {
            // se resta 0.05 para redondeo a solo un decimal
            if ($aaaaCAL > 0) { $ipcAcumulado = round(((($puntosFin - $puntosIni)/$puntosIni)*100)-.05,1); }
            if ($ipcAcumulado < 0) { $ipcAcumulado = 0; }
       }

       return($ipcAcumulado);
    }
    
    function numerotexto ($numero) {
        // Primero tomamos el numero y le quitamos los caracteres especiales y extras
        // Dejando solamente el punto "." que separa los decimales
        // Si encuentra mas de un punto, devuelve error.
        // NOTA: Para los paises en que el punto y la coma se usan de forma
        // inversa, solo hay que cambiar la coma por punto en el array de "extras"
        // y el punto por coma en el explode de $partes
        
        $extras= array("/[\$]/","/ /","/,/","/-/");
        $limpio=preg_replace($extras,"",$numero);
        $partes=explode(".",$limpio);
        if (count($partes)>2) {
            return "Error, el n&uacute;mero no es correcto";
            exit();
        }
        
        // Ahora explotamos la parte del numero en elementos de un array que
        // llamaremos $digitos, y contamos los grupos de tres digitos
        // resultantes
        
        $digitos_piezas=chunk_split ($partes[0],1,"#");
        $digitos_piezas=substr($digitos_piezas,0,strlen($digitos_piezas)-1);
        $digitos=explode("#",$digitos_piezas);
        $todos=count($digitos);
        $grupos=ceil (count($digitos)/3);
        
        // comenzamos a dar formato a cada grupo
        
        $unidad  = array ('un','dos','tres','cuatro','cinco','seis','siete','ocho','nueve');
        $decenas = array ('diez','once','doce', 'trece','catorce','quince');
        $decena  = array ('dieci','veinti','treinta','cuarenta','cincuenta','sesenta','setenta','ochenta','noventa');
        $centena = array ('ciento','doscientos','trescientos','cuatrocientos','quinientos','seiscientos','setecientos','ochocientos','novecientos');
        $resto=$todos;
        
        for ($i=1; $i<=$grupos; $i++) {
            
            // Hacemos el grupo
            if ($resto>=3) {
                $corte=3; } else {
                $corte=$resto;
            }
            $offset=(($i*3)-3)+$corte;
            $offset=$offset*(-1);
            
            // la siguiente seccion es una adaptacion de la contribucion de cofyman y JavierB
            
            $num=implode("",array_slice ($digitos,$offset,$corte));
            $resultado[$i] = "";
            $cen = (int) ($num / 100);              //Cifra de las centenas
            $doble = $num - ($cen*100);             //Cifras de las decenas y unidades
            $dec = (int)($num / 10) - ($cen*10);    //Cifra de las decenas
            $uni = $num - ($dec*10) - ($cen*100);   //Cifra de las unidades
            if ($cen > 0) {
               if ($num == 100) $resultado[$i] = "cien";
               else $resultado[$i] = $centena[$cen-1].' ';
            }//end if
            if ($doble>0) {
               if ($doble == 20) {
                  $resultado[$i] .= " veinte";
               }elseif (($doble < 16) and ($doble>9)) {
                  $resultado[$i] .= $decenas[$doble-10];
               }else {
                  $resultado[$i] .=' '. $decena[$dec-1];
               }//end if
               if ($dec>2 and $uni<>0) $resultado[$i] .=' y ';
               if (($uni>0) and ($doble>15) or ($dec==0)) {
                  if ($i==1 && $uni == 1) $resultado[$i].="uno";
                  elseif ($i==2 && $num == 1) $resultado[$i].="";
                  else $resultado[$i].=$unidad[$uni-1];
               }
            }
    
            // Le agregamos la terminacion del grupo
            switch ($i) {
                case 2:
                $resultado[$i].= ($resultado[$i]=="") ? "" : " mil ";
                break;
                case 3:
                $resultado[$i].= ($num==1) ? " mill&oacute;n " : " millones ";
                break;
            }
            $resto-=$corte;
        }
        
        // Sacamos el resultado (primero invertimos el array)
        $resultado_inv= array_reverse($resultado, TRUE);
        $final="";
        foreach ($resultado_inv as $parte){
            $final.=$parte;
        }
        return $final;
    } 
?>