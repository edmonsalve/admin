<?php
date_default_timezone_set('America/Santiago');
session_start();

require_once('defines/variables_path.php');
require_once(PATH_DEFINES . 'variables.php');
require_once(PATH_CLASSES . 'Class.Plantilla.php');

$usuario = htmlspecialchars(trim($_GET['usr'] ?? ''), ENT_QUOTES, 'UTF-8');
$errores = array(
    '1' => 'Usuario o contraseña incorrectos.',
    '90' => 'No fue posible validar el acceso. Intente nuevamente.',
    '94' => 'Ingrese usuario y contraseña para continuar.'
);
$mensajeEstado = $errores[$_GET['err'] ?? ''] ?? '';

$contenido = new plantilla('index');
$contenido->asigna_variables(array(
    'titulo' => 'dCode Administración',
    'icono' => PATH_ICO . ICONO_NAVEGADOR,
    'logo' => 'dAdminLog.png',
    'enviar' => 'Ingresar',
    'msgEstado' => $mensajeEstado,
    'usr' => $usuario,
));

echo $contenido->muestra();
?>
