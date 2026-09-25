<?php
require_once('../includes/initSistema.php');

header('Content-Type: application/json; charset=UTF-8');

$respuesta = static function (bool $ok, string $mensaje, array $datos = array()): void {
    echo json_encode(array_merge(array('ok' => $ok, 'mensaje' => $mensaje), $datos));
    exit;
};
$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=adm_dCode;charset=utf8mb4',
    DB_USER,
    DB_PASSWD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);
$carpeta = rtrim(ALMACEN_AUX, '/') . '/bitacora_desarrollo/';

if (($_POST['accion'] ?? '') === 'eliminar') {
    $idImagen = $_POST['idImagen'] ?? '';
    if (!ctype_digit((string) $idImagen)) {
        $respuesta(false, 'La imagen indicada no es válida.');
    }
    $consulta = $pdo->prepare('SELECT archivo FROM adm_bitacora_desarrollo_imagenes WHERE idImagen = ?');
    $consulta->execute(array((int) $idImagen));
    $imagen = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$imagen) {
        $respuesta(false, 'La imagen ya no está disponible.');
    }
    $eliminar = $pdo->prepare('DELETE FROM adm_bitacora_desarrollo_imagenes WHERE idImagen = ?');
    $eliminar->execute(array((int) $idImagen));
    $rutaArchivo = $carpeta . basename($imagen['archivo']);
    if (is_file($rutaArchivo)) {
        @unlink($rutaArchivo);
    }
    $respuesta(true, 'Captura eliminada.');
}

$idBitacora = $_POST['idBitacora'] ?? '';
$archivo = $_FILES['file'] ?? null;
if (!ctype_digit((string) $idBitacora) || (int) $idBitacora < 1) {
    $respuesta(false, 'Guarde primero el registro de bitácora.');
}
if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 8 * 1024 * 1024) {
    $respuesta(false, 'Seleccione un PDF, imagen, TXT o SQL válido de hasta 8 MB.');
}

$mimeTipo = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
$tiposPermitidos = array(
    'application/pdf' => 'pdf',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'text/plain' => 'txt',
    'application/sql' => 'sql',
    'text/x-sql' => 'sql',
    'application/x-sql' => 'sql',
);
if (!isset($tiposPermitidos[$mimeTipo])) {
    $respuesta(false, 'El adjunto debe ser PDF, imagen, TXT o SQL.');
}
if (str_starts_with($mimeTipo, 'image/')) {
    $datosImagen = @getimagesize($archivo['tmp_name']);
    if (($datosImagen[0] ?? 0) > 8000 || ($datosImagen[1] ?? 0) > 8000) {
        $respuesta(false, 'La imagen no puede superar 8.000 px por lado.');
    }
}
$existe = $pdo->prepare('SELECT 1 FROM adm_bitacora_desarrollo WHERE idBitacora = ?');
$existe->execute(array((int) $idBitacora));
if (!$existe->fetchColumn()) {
    $respuesta(false, 'El registro de bitácora indicado no existe.');
}
if (!is_dir($carpeta) || !is_writable($carpeta)) {
    $respuesta(false, 'La carpeta para capturas no está disponible.');
}

try {
    $nombreArchivo = 'bitacora_' . (int) $idBitacora . '_' . bin2hex(random_bytes(12)) . '.' . $tiposPermitidos[$mimeTipo];
} catch (Throwable $e) {
    $respuesta(false, 'No fue posible preparar el nombre del adjunto.');
}
if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo)) {
    $respuesta(false, 'No fue posible almacenar el adjunto.');
}

try {
    $nombreOriginal = mb_substr(basename((string) $archivo['name']), 0, 255);
    $insertar = $pdo->prepare('INSERT INTO adm_bitacora_desarrollo_imagenes (idBitacora, archivo, nombreOriginal, mimeTipo, tamano) VALUES (?, ?, ?, ?, ?)');
    $insertar->execute(array((int) $idBitacora, $nombreArchivo, $nombreOriginal, $mimeTipo, (int) $archivo['size']));
} catch (Throwable $e) {
    @unlink($carpeta . $nombreArchivo);
    $respuesta(false, 'No fue posible asociar el adjunto al registro.');
}

$idImagen = (int) $pdo->lastInsertId();
$respuesta(true, 'Adjunto cargado correctamente.', array(
    'idImagen' => $idImagen,
    'url' => 'bitacora_desarrollo_imagen.php?id=' . $idImagen,
));
?>
