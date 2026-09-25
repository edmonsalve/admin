<?php
require_once('../includes/initSistema.php');

$idImagen = $_GET['id'] ?? '';
if (!ctype_digit((string) $idImagen)) {
    http_response_code(404);
    exit;
}

$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=adm_dCode;charset=utf8mb4',
    DB_USER,
    DB_PASSWD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);
$consulta = $pdo->prepare('SELECT archivo, mimeTipo FROM adm_bitacora_desarrollo_imagenes WHERE idImagen = ?');
$consulta->execute(array((int) $idImagen));
$imagen = $consulta->fetch(PDO::FETCH_ASSOC);
$rutaArchivo = $imagen ? rtrim(ALMACEN_AUX, '/') . '/bitacora_desarrollo/' . basename($imagen['archivo']) : '';
if (!$imagen || !is_file($rutaArchivo)) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $imagen['mimeTipo']);
header('Content-Length: ' . filesize($rutaArchivo));
header('Cache-Control: private, max-age=86400');
header('X-Content-Type-Options: nosniff');
readfile($rutaArchivo);
?>
