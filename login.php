<?php
date_default_timezone_set('America/Santiago');
session_start();

require_once('defines/variables_path.php');
require_once(PATH_DEFINES . 'variables.php');
require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

$usuario = trim($_POST['user-id'] ?? '');
$clave = (string) ($_POST['user-pw'] ?? '');

if ($usuario === '' || $clave === '') {
    header('Location: index.php?usr=' . rawurlencode($usuario) . '&err=94');
    exit;
}

// Esta instalación administra la base maestra dCode, igual que adminORI.
$conexion = new DB_MySQLi();
if (!$conexion->conectar('adm_dCode', DB_SERVER, DB_USER, DB_PASSWD)) {
    header('Location: index.php?usr=' . rawurlencode($usuario) . '&err=90');
    exit;
}

$usuarioSql = $conexion->escapaDatos($usuario);
$consulta = "SELECT username, password, nombre, tipoUser, foto, estado
             FROM `adm_dCode`.`adm_users`
             WHERE username = '$usuarioSql'
             LIMIT 1";
$resultado = $conexion->consulta($consulta);
$registro = $resultado ? mysqli_fetch_assoc($resultado) : null;

if (!$registro || trim((string) $registro['estado']) !== 'A' ||
    !in_array(trim((string) $registro['tipoUser']), array('D', 'T', 'A'), true) ||
    !hash_equals(trim((string) $registro['password']), sha1($clave))) {
    header('Location: index.php?usr=' . rawurlencode($usuario) . '&err=1');
    exit;
}

session_regenerate_id(true);

$_SESSION['idUser'] = $registro['username'];
$_SESSION['nomUsuario'] = $registro['nombre'];
$_SESSION['usuario'] = $registro['nombre'];
$_SESSION['tipo'] = $registro['tipoUser'];
$_SESSION['foto'] = $registro['foto'];
$_SESSION['usrAdmin'] = $registro['tipoUser'] === 'D' ? 'D' : 'N';
$_SESSION['permiteAcceso'] = true;
$_SESSION['delete'] = $_SESSION['usrAdmin'] === 'D';
$_SESSION['save'] = true;
$_SESSION['prefijo'] = 'adm_';
$_SESSION['idsistema'] = 950;
$_SESSION['areasistema'] = 'A';

session_write_close();
// Redirección relativa: RUTA_ABSOLUTA pertenece a la instalación T450.
header('Location: appMantenimiento/index_main.php');
exit;
?>
