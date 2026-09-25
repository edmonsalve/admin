<?php
    /*
        Versión segura (PDO)
        Compatibilidad: PHP 8.1 / MariaDB 10.x
    */

unset($campos);
unset($valores);
unset($set);   

$DB_SERVER   = DB_SERVER;
$DB_USER     = DB_USER;
$DB_PASSWD   = DB_PASSWD;

if (isset($guardarBD)) { $DB_SISTEMA  = $guardarBD; } else { $DB_SISTEMA  = DB_Sistema; }

try {
    // ::: Conexión PDO
    $dsn = "mysql:host=$DB_SERVER;dbname={$DB_SISTEMA};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASSWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

// ::::: VARIABLES POR DEFECTO ::::: 
$BD = isset($DB_SISTEMA) ? "`$DB_SISTEMA`." : "";

if (isset($ejecutaConsulta)) { $ejecutaConsulta = $ejecutaConsulta; } else { $ejecutaConsulta = true;}
if (!isset($muestraConsulta)) { $muestraConsulta = false;}
if (!isset($muestraConsulataJSON)) { $muestraConsulataJSON = false; }
if (!isset($muestraPost)) { $muestraPost = false;}
if (!isset($mayusculas)) { $mayusculas = true;}

// ::::: DEPURACIÓN POST ::::: 
if ($muestraPost) {
    echo "<p>➡️ POST recibido:</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}

// ::::: IGNORAR CAMPOS ::::: 
$arrayIgnorar = [];
if (!empty($ignorarPost)) {
    foreach (explode(',', $ignorarPost) as $campo) {
        $arrayIgnorar[trim($campo)] = true;
    }
}

// ::::: CAMPOS CHECKBOX ::::: 
$arrayChkbx = [];
if (!empty($camposCheckbox)) {
    foreach (explode(',', $camposCheckbox) as $campo) {
        $arrayChkbx[trim($campo)] = true;
    }
}


$campos = [];
$valores = [];
$set = [];

foreach ($_POST as $campo => $valor) {
    // echo "<br>$campo => $valor";  // Debug desactivado para respuestas JSON
    if (
        $campo === 'radio-set' ||
        $campo === 'registroHijo' ||
        ($campo === $IdCampo && empty($reemplazarpor)) ||
        isset($arrayIgnorar[$campo])
    ) {
        continue;
    }

    // Normalización de texto
    $valor = trim($valor);
    if ($mayusculas) {
        $valor = mb_strtoupper($valor, 'UTF-8');
    }

    if (substr($campo, 0, 5) === 'fecha' && ($valor === '' || $valor === '--')) {
        $valor = '0000-00-00';
    }

    // Valor checkbox
    if (isset($arrayChkbx[$campo])) {
        $valor = isset($_POST[$campo]) ? 1 : 0;
    }

    if ($IdCampo == $campo && !empty($reemplazarpor)) {
        $valor = $_POST[$reemplazarpor];
    } 

    $campos[]  = "`$campo`";
    $valores[] =   $valor;
    $set[]     = "`$campo` = ?";
}

// Debug opcional de campos procesados (comentado para JSON)
/*
$x=1;
foreach ($campos as $key => $value) {
    echo "<br>$x: $key => $value";
    $x++;
}
*/

// ::::: CHECKBOX AUSENTES ::::: 
foreach ($arrayChkbx as $campo => $x) {
    if (!isset($_POST[$campo])) {
        $campos[]  = "`$campo`";
        $valores[] = 0;
        $set[]     = "`$campo` = 0";
    }
}

// ::::: CONSTRUCCIÓN CONSULTA ::::: 
if ($_POST[$IdCampo] === 'new') {
    $placeholders = implode(',', array_fill(0, count($valores), '?'));
    $sql = "INSERT INTO {$BD}`{$guardarTabla}` (" . implode(',', $campos) . ") VALUES ($placeholders)";
    $accion = "ADD";
} else {
    $sql = "UPDATE {$BD}`{$guardarTabla}` SET " . implode(',', $set) . " 
                    WHERE `$IdCampo` = ?";
                    
    $valores[] = $_POST[$IdCampo];

    if (isset($IdCampo2) && !empty($IdCampo2)) {
        $sql .= " AND `$IdCampo2` = ?";
        $valores[] = $_POST[$IdCampo2];
    }

    $accion = "UPD";
}

if ($muestraConsulta) {
    echo "<p>➡️ Procesando formulario para tabla <strong>$guardarTabla</strong></p>";
    echo "<pre>SQL:\n$sql\n\nValores:\n";
    print_r($valores);
    echo "\n\nConsulta con datos reales:\n";
    $sqlReal = $sql;
    foreach ($valores as $valor) {
        $valorEscapado = is_null($valor) ? 'NULL' : "'" . addslashes($valor) . "'";
        $sqlReal = preg_replace('/\?/', $valorEscapado, $sqlReal, 1);
    }
    echo $sqlReal;
    echo "</pre>";
    echo "<script>console.log('".$sqlReal."');</script>";
}

if ($muestraConsultaJSON) {
    $qryJSON =  "<p>➡️ Procesando formulario para tabla <strong>$guardarTabla</strong></p>";
    $qryJSON .= "<pre>SQL1:\n$sql\n\nValores:\n<br>";
    $qryJSON .= print_r($valores, true);
    $qryJSON .= "</pre>";
}

// ::::: EJECUTAR ::::: 
if ($ejecutaConsulta) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        $IdRegistro = ($accion === "ADD") ? $pdo->lastInsertId() : $_POST[$IdCampo];

        if ($muestraConsulta) {
            echo "<pre>✅ Acción: $accion — ID: $IdRegistro</pre>";
        }

        // ::::: LOG opcional ::::: 
        $tablaDB = $guardarTabla;
        $consultaLog = $sql . " | Valores: " . implode(', ', $valores);
        require_once('log.php');

    } catch (PDOException $e) {
        if ($muestraConsulta) {
            echo "<pre>❌ Error al guardar: " . htmlspecialchars($e->getMessage()) . "</pre>";
        }  
        if ($muestraConsultaJSON) {
            $qryJSON .= "<pre>❌ Error al guardar: " . htmlspecialchars($e->getMessage()) . "</pre>";
        }       
        // echo "<pre>❌ Error al guardar: " . htmlspecialchars($e->getMessage()) . "</pre>";
    }
}
?>
