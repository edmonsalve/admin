<?php 
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_ADMIN, DB_SERVER, DB_USER, DB_PASSWD );
    
    $DB_CLIENTE   = DB_CLIENTE;
	$DB_COMUN_IND = DB_COMUN_IND;
	
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_CLIENTE, DB_SERVER, DB_USER, DB_PASSWD );
	
	/* :::::::::::::::::::::::::::::::::: SETUP :::::::::::::::::::::::::::::::::::::::: */
    $consulta = "SELECT *   FROM  `$DB_CLIENTE`.`adm_estadoIndicadores` WHERE id = 1";
    $salida   = $conexionDB->consulta($consulta);
	$row      = mysqli_fetch_array($salida);
    
    $fechaActualizacion = $row['fechaActualizacion'];
	
	$hoy  = date('Y-m-d');
	$year = date('Y');

	
	if ($fechaActualizacion < $hoy) {
		$dbIndicadores1 = ['comun_tabAfp','comun_tabApv','comun_tabCajas','comun_tabImpuesto','comun_tabIsapre','comun_tabTramosCargasFam','comun_tabUf','comun_tabUtm'];
		
		$conexionIND = new DB_MySQLi;
		$conexionIND->conectar(DB_INDICADORES, INDICADORES_SERV, USUARIO_SERV, CLAVE_SERV);
		
		$DB_INDICADORES = DB_INDICADORES;
		
		
		/* ::::::::::::::::::::  tablas con registros multiples :::::::::::::::::::::::::::::::::::::::: */
		foreach($dbIndicadores1 as $ind => $tablaIND) {
			$consultaIND = "SELECT * FROM  `$DB_INDICADORES`.`$tablaIND` ORDER BY id"; 
			$salidaIND   = $conexionIND->consulta($consultaIND);
			
			while ($rowIND = mysqli_fetch_array($salidaIND,MYSQLI_ASSOC)) {                              
				$nroElementos = count($rowIND);
				$contador = 1;
				$campos   = "(";
				$valores  = "(";

				foreach($rowIND  as $ind => $val) { 
					if ($contador < $nroElementos) { $coma = ","; } else { $coma = ""; }
					$campos  .= "`$ind`".$coma;
					$valores .= "'$val'".$coma;
					$contador++;
				} 
				$campos   .= ")";
				$valores  .= ")";
				
				$consultaUPD = "REPLACE INTO `$DB_COMUN_IND`.`$tablaIND` $campos VALUE $valores";
				$conexionDB->consulta($consultaUPD);
			}
		}
		
		/* ::::::::::::::::: Guarda Fecha de Actualizacion::::::::::::::::::: */
		$consulta = "UPDATE `$DB_CLIENTE`.`adm_estadoIndicadores` SET `fechaActualizacion` = '$hoy' WHERE id = 1";
        $conexionDB->consulta($consulta);
	}
?>