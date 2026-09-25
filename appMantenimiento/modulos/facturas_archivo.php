<?php
require_once('../includes/initSistema.php');

$idArchivo = $_GET['id'] ?? '';
if (!ctype_digit((string) $idArchivo)) {
    http_response_code(404);
    exit;
}

$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=adm_dCode;charset=utf8mb4',
    DB_USER,
    DB_PASSWD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);
$consulta = $pdo->prepare('SELECT archivo, mimeTipo FROM adm_facturas_archivos WHERE idArchivoFactura = ?');
$consulta->execute(array((int) $idArchivo));
$archivo = $consulta->fetch(PDO::FETCH_ASSOC);
$ruta = $archivo ? rtrim(ALMACEN_AUX, '/') . '/facturas/' . basename($archivo['archivo']) : '';
if (!$archivo || !is_file($ruta)) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $archivo['mimeTipo']);
header('Content-Length: ' . filesize($ruta));
header('Cache-Control: private, max-age=86400');
header('X-Content-Type-Options: nosniff');
readfile($ruta);
?>
