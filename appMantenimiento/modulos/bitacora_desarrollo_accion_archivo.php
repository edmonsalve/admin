<?php
require_once('../includes/initSistema.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=UTF-8');

    $responder = static function (bool $ok, string $mensaje): void {
        echo json_encode(array('ok' => $ok, 'mensaje' => $mensaje));
        exit;
    };
    $idArchivo = $_POST['idArchivo'] ?? '';
    if (($_POST['accion'] ?? '') !== 'eliminar' || !ctype_digit((string) $idArchivo)) {
        $responder(false, 'El adjunto indicado no es válido.');
    }

    $pdo = new PDO(
        'mysql:host=' . DB_SERVER . ';dbname=adm_dCode;charset=utf8mb4',
        DB_USER,
        DB_PASSWD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    $consulta = $pdo->prepare('SELECT archivo FROM adm_bitacora_desarrollo_acciones_archivos WHERE idArchivo = ?');
    $consulta->execute(array((int) $idArchivo));
    $archivo = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$archivo) {
        $responder(false, 'El adjunto ya no está disponible.');
    }

    $eliminar = $pdo->prepare('DELETE FROM adm_bitacora_desarrollo_acciones_archivos WHERE idArchivo = ?');
    $eliminar->execute(array((int) $idArchivo));
    $ruta = rtrim(ALMACEN_AUX, '/') . '/bitacora_desarrollo/' . basename($archivo['archivo']);
    if (is_file($ruta)) {
        @unlink($ruta);
    }
    $responder(true, 'Adjunto de la acción eliminado.');
}

$idArchivo = $_GET['id'] ?? '';
if (!ctype_digit((string) $idArchivo)) {
    http_response_code(404);
    exit;
}
$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=adm_dCode;charset=utf8mb4',
    DB_USER,
    DB_PASSWD
);
$stmt = $pdo->prepare('SELECT archivo, mimeTipo FROM adm_bitacora_desarrollo_acciones_archivos WHERE idArchivo = ?');
$stmt->execute(array((int) $idArchivo));
$archivo = $stmt->fetch(PDO::FETCH_ASSOC);
$ruta = $archivo ? rtrim(ALMACEN_AUX, '/') . '/bitacora_desarrollo/' . basename($archivo['archivo']) : '';
if (!$archivo || !is_file($ruta)) {
    http_response_code(404);
    exit;
}
header('Content-Type: ' . $archivo['mimeTipo']);
header('Content-Length: ' . filesize($ruta));
header('X-Content-Type-Options: nosniff');
readfile($ruta);
?>
