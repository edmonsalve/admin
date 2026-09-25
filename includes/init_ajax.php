<?php
/**
 * Inicialización simplificada para endpoints AJAX
 * No requiere sesión activa - solo configuración básica
 */

// Deshabilitar output buffering para endpoints
if (ob_get_level()) {
    ob_end_clean();
}

// Configuración de errores (comentar en producción)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Cargar configuraciones básicas
require_once(__DIR__ . '/../defines/variables_path.php');
require_once(PATH_DEFINES . 'variables.php');

// Cargar clases necesarias
require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

// Opcional: cargar contexto si es necesario
// require_once(PATH_CLASSES . 'Class.Context.php');

// No requerir sesión activa para endpoints AJAX
// Los endpoints manejan su propia autenticación si es necesario

?>