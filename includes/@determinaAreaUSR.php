<?php
    //* :: Llamado desde login.php, valida si el usuario es interno o externo, y obtiene datos de personal, departamento y direccion y si es Jefe o Director.

    $usrID = $_SESSION['idUser'];

    $_SESSION['esJefe']         = 'N';
    $_SESSION['deptoIdUsr']     = 0;
    $_SESSION['intraUsr']       = false;
    $_SESSION['usuarioIntra']   = false;
    $_SESSION['areaIntranet']   = '';

    switch($_areaId) {
        case '1': 
            $DB_PERSONAL    = DB_PERSONAL_MUNI;
            $DB_COMUN       = DB_COMUN;
            $TABLA_DEPTOS   = 'comun_tabDeptos';
            $_areaIntranet  = 'M';
            break;
        case '2': 
            $DB_PERSONAL    = DB_PERSONAL_SALUD;
            $DB_COMUN       = DB_COMUN;
            $TABLA_DEPTOS   = 'comun_tabDeptosSalud';
            $_areaIntranet  = 'S';
            break;
    }

    // :::: Valida que existe BD Personal 
    $consulta   = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$DB_PERSONAL'"; 
    $salida     = $conexionDB->consulta($consulta);	

    if (mysqli_num_rows($salida) == 1) {
        $consultaPer = "SELECT rut,departamentoId,jefeSiNo, cargo  
                            FROM  `$DB_PERSONAL`.`rem_personal` 
                            WHERE  rut = '$usrID' 
                            AND estadoFunc = 'V' "; 

        $salidaPer   = $conexionDB->consulta($consultaPer);

        if (mysqli_num_rows($salidaPer) == 1) { 
            $row  = mysqli_fetch_array($salidaPer);

            $_SESSION['esJefe']       = $row['jefeSiNo'];
            $_SESSION['deptoIdUsr']   = $row['departamentoId'];
            $_SESSION['cargo']        = $row['cargo'];
            $_SESSION['intraUsr']     = true;
            $_SESSION['usuarioIntra'] = true;
            $_SESSION['areaIntranet'] = $_areaIntranet;
        }
        mysqli_free_result($salidaPer);

        // :: honorarios
        $consultaPer = "SELECT rut,departamentoId, 'N' as jefeSiNo, cargo  
                            FROM  `$DB_PERSONAL`.`hon_personal` 
                            WHERE  rut = '$usrID' 
                            AND estadoFunc = 'V' "; 

        $salidaPer   = $conexionDB->consulta($consultaPer);

        if (mysqli_num_rows($salidaPer) == 1) { 
            $row  = mysqli_fetch_array($salidaPer);

            $_SESSION['esJefe']       = $row['jefeSiNo'];
            $_SESSION['deptoIdUsr']   = $row['departamentoId'];
            $_SESSION['cargo']        = $row['cargo'];
            $_SESSION['intraUsr']     = true;
            $_SESSION['usuarioIntra'] = true;
            $_SESSION['areaIntranet'] = 'HM';
        }
        mysqli_free_result($salidaPer);


        // :: directores
        $_SESSION['esDirector']     = 'N';
        $_SESSION['direccionId']    = 0; 

        $consultaDir = "SELECT *    
                            FROM  `$DB_COMUN`.`comun_tabDireccion` 
                            WHERE  firmaTitular = '$usrID' 
                            AND firmaActiva = 'T'";  
       
        $salidaDir   = $conexionDB->consulta($consultaDir);
        if (mysqli_num_rows($salidaDir) == 1) { 
            $row  = mysqli_fetch_array($salidaDir);

            $_SESSION['esDirector']              = 'S';
            $_SESSION['direccionId']            = $row['id']; 
            $_SESSION['autorizaDirectores']     = $row['autorizaDirectores'];
            $_SESSION['autorizadoPorAlcalde']   = $row['autorizadoPorAlcalde'];
            $_SESSION['esAlcalde']              = $row['esAlcalde'];
        }
        mysqli_free_result($salidaDir);


        // ::  direccion and departamento
        $deptoId = $_SESSION['deptoIdUsr'];
        $comsulta = "SELECT *   FROM `$DB_COMUN`.`$TABLA_DEPTOS` 
                                WHERE id = '$deptoId'";

        $salida   = $conexionDB->consulta($comsulta);
        $row = mysqli_fetch_array($salida);
        $_SESSION['deptoNombre']    = $row['departamento'];   
        $_SESSION['deptoDireccion'] = $row['direccionId'];
        $_SESSION['titular']        = $row['titular'];
        $_SESSION['suplente']       = $row['suplente'];
        mysqli_free_result($salida);
        // echo "<br><br><br>consulta depto: $comsulta<br>"; exit;
        
        $deptoDireccion = $_SESSION['deptoDireccion'];
        $consultaDir = "SELECT *  FROM  `$DB_COMUN`.`comun_tabDireccion` WHERE  id = '$deptoDireccion'";
        $salida      = $conexionDB->consulta($consultaDir);

        $row = mysqli_fetch_array($salida);
        $_SESSION['direccionNombre'] = $row['direccion'];
    }
?>