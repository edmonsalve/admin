<?php
    require('../classes/Class.DB_MySQLi.php');

    define('DB_SERVER', 'localhost');
    define('DB_USER',   'root');
    define('DB_PASSWD', 'kcm64%VI-9');
    define('DB_COMUN_IND', 'des_@comunIndicadores');	



    require('funciones.php');
    $ipcAcu = calcularIpcGeneral(2023,12);
    echo "IPC: $ipcAcu";
?>