<?php
    require_once('../includes/initSistema.php');

    header('Content-Type: application/json; charset=UTF-8');

    $respuesta = static function (bool $success, string $mensaje, array $extra = []): void {
        echo json_encode(array_merge(['success' => $success, 'message' => $mensaje], $extra));
        exit;
    };

    $id = $_POST['id'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $archivo = $_FILES['file'] ?? null;

    if (!ctype_digit((string) $id) || (int) $id < 1 || !in_array($tipo, ['icono', 'btn'], true)) {
        $respuesta(false, 'Solicitud de carga inválida.');
    }

    if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 2 * 1024 * 1024) {
        $respuesta(false, 'Seleccione una imagen válida de hasta 2 MB.');
    }

    $tiposPermitidos = [
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_GIF  => 'gif',
    ];
    if (defined('IMAGETYPE_WEBP')) {
        $tiposPermitidos[IMAGETYPE_WEBP] = 'webp';
    }

    if (function_exists('exif_imagetype')) {
        $tipoImagen = @exif_imagetype($archivo['tmp_name']);
    } else {
        $datosImagen = @getimagesize($archivo['tmp_name']);
        $tipoImagen = $datosImagen[2] ?? null;
    }
    if (!isset($tiposPermitidos[$tipoImagen])) {
        $respuesta(false, 'El archivo debe ser una imagen PNG, JPG, GIF o WEBP.');
    }

    $conexionDB = new DB_MySQLi;
    $conexionDB->conectar('adm_dCode', DB_SERVER, DB_USER, DB_PASSWD);
    $existe = $conexionDB->consulta('SELECT id FROM adm_sistemas WHERE id = ' . (int) $id . ' LIMIT 1');
    if (!mysqli_fetch_assoc($existe)) {
        $respuesta(false, 'El sistema indicado no existe.');
    }

    $extension = $tiposPermitidos[$tipoImagen];
    $nombreArchivo = 'sistema_' . (int) $id . '_' . $tipo . '.' . $extension;
    $carpeta = ($tipo === 'icono') ? PATH_BASE . 'iconos/' : PATH_BOTONES;
    $campo = ($tipo === 'icono') ? 'icono' : 'btn';

    if (!is_dir($carpeta) || !is_writable($carpeta)) {
        $respuesta(false, 'La carpeta de destino no está disponible para cargas.');
    }

    if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo)) {
        $respuesta(false, 'No fue posible almacenar el archivo.');
    }

    try {
        $pdo = new PDO(
            'mysql:host=' . DB_SERVER . ';dbname=adm_dCode;charset=utf8mb4',
            DB_USER,
            DB_PASSWD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $stmt = $pdo->prepare("UPDATE adm_sistemas SET $campo = ? WHERE id = ?");
        $stmt->execute([$nombreArchivo, (int) $id]);
    } catch (PDOException $e) {
        @unlink($carpeta . $nombreArchivo);
        $respuesta(false, 'No fue posible asociar el archivo al sistema.');
    }

    $url = ($tipo === 'icono' ? '/iconos/' : '/btns/') . rawurlencode($nombreArchivo);
    $respuesta(true, 'Archivo cargado correctamente.', ['filename' => $nombreArchivo, 'url' => $url]);
?>
