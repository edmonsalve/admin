<?php
    /**  Para llamar a log
        $accion 	= 'VER';
        $tablaDB    = 'adm_xxx';
        $rutLog		= $_rut;
        $observLog	= "Ver ayuda social: $_folioSolAyuda";
        $consultaLog = "SELECT * FROM soc_xxx WHERE folioSolAyuda = '$_folioSolAyuda'";

        require_once( PATH_INCLUDES . 'log.php');
    */

    date_default_timezone_set('America/Santiago');
    
    $dbCliente  = DB_CLIENTE;
    
    $fecha      = date("Y-m-d");
    $hora       = date("H:i:s");
    $usuario    = $_SESSION['idUser'];
    $desde      = $_SERVER['REMOTE_ADDR'];
    
    if (isset($_SESSION['idsistema'])) { $idsistema = $_SESSION['idsistema']; } else { $idsistema = 0; }
    
    if (!isset($accion)) {
        switch (substr($detalle,0,1)) {
            case 'R': $accion = 'ADD'; break;
            case 'U': $accion = 'UPD'; break;
        }
    }   

    if (!isset($consultaLog)) { $consultaLog = ''; }
    if (!isset($observLog))   { $observLog = ''; }
    if (!isset($rutLog))      { $rutLog = 0; }
    
    if (!isset($tablaDB)) { $tablaDB = ''; }

    $qryLog = "INSERT INTO `$dbCliente`.`adm_log`
                        (`sistema`, `fecha`, `hora`, `usuario`, `desde`, `tabla`, `accion`, `consulta`, `observaciones`, `rut`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmtLog = mysqli_prepare($conexionDB->IdConexion, $qryLog);
    if ($stmtLog) {
        mysqli_stmt_bind_param(
            $stmtLog,
            'isssssssss',
            $idsistema,
            $fecha,
            $hora,
            $usuario,
            $desde,
            $tablaDB,
            $accion,
            $consultaLog,
            $observLog,
            $rutLog
        );
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
?>