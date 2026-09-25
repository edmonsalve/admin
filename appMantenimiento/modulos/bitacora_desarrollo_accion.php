<?php
require_once('../includes/initSistema.php');

header('Content-Type: application/json; charset=UTF-8');

$responder = static function (bool $ok, string $mensaje): void {
    echo json_encode(array('ok' => $ok, 'mensaje' => $mensaje));
    exit;
};

$idAccion = $_POST['idAccion'] ?? '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || ($_POST['accion'] ?? '') !== 'eliminar'
    || !ctype_digit((string) $idAccion)) {
    $responder(false, 'La acción indicada no es válida.');
}

$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=adm_dCode;charset=utf8mb4',
    DB_USER,
    DB_PASSWD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);

$consultaAccion = $pdo->prepare('SELECT idAccion FROM adm_bitacora_desarrollo_acciones WHERE idAccion = ?');
$consultaAccion->execute(array((int) $idAccion));
if (!$consultaAccion->fetchColumn()) {
    $responder(false, 'La acción ya no está disponible.');
}

$consultaArchivos = $pdo->prepare('SELECT archivo FROM adm_bitacora_desarrollo_acciones_archivos WHERE idAccion = ?');
$consultaArchivos->execute(array((int) $idAccion));
$archivos = $consultaArchivos->fetchAll(PDO::FETCH_COLUMN);

try {
    $pdo->beginTransaction();
    $eliminarAccion = $pdo->prepare('DELETE FROM adm_bitacora_desarrollo_acciones WHERE idAccion = ?');
    $eliminarAccion->execute(array((int) $idAccion));
    $pdo->commit();
} catch (Throwable $error) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $responder(false, 'No fue posible eliminar la acción.');
}

$carpeta = rtrim(ALMACEN_AUX, '/') . '/bitacora_desarrollo/';
foreach ($archivos as $archivo) {
    $ruta = $carpeta . basename($archivo);
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}

$responder(true, 'Acción eliminada junto con sus adjuntos.');
?>
