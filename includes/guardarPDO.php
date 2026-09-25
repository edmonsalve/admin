<?php
    // ::: lee post
    $fecha = date('Y-m-d H:i:s');
    $countfiles = $_POST['countfiles'];

    foreach ($_POST as $key => $value) {
        if ($key != 'countfiles') {
            $columns[] = $key;
            $values[] = ":$key";
            $params[":$key"] = $value;
        }
    }

    $columns[] = 'fecha';
    $values[] = ':fecha';
    $params[':fecha'] = $fecha;

    $columns_str = implode(', ', $columns);
    $values_str  = implode(', ', $values);

    include('conexionDB.php');
    // :::: guardar datos
    $consulta = "REPLACE INTO `$DB_CC`.`cc_solicitudes` ($columns_str) VALUES ($values_str)";
    $statement = $pdo->prepare($consulta);

    foreach ($params as $key => $value) {
        $statement->bindParam($key, $value);
    }

    $statement->execute();
?>