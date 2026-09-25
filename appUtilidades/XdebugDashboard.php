<?php
/**
 * Script de depuración para el dashboard CME
 * Verificar configuración y estado de carga
 */

require_once('../includes/init.php');

// Verificar si el usuario tiene acceso al CME
if (!isset($_SESSION['usrCME']) || $_SESSION['usrCME'] != 'S') {
    die("Acceso denegado. Usuario no tiene permisos de CME.");
}

echo "<h1>Debug Dashboard CME</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .section { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px; }
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
    .warning { background-color: #fff3cd; color: #856404; }
    .info { background-color: #cce7ff; color: #004085; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

$conexionDB = new DB_MySQLi;
$conexionDB->conectar(DB_ADMIN, DB_SERVER, DB_USER, DB_PASSWD);

// 1. Verificar tabla config_dashboard
echo "<div class='section'>";
echo "<h2>1. Verificación de Tabla config_dashboard</h2>";

try {
    $consulta = "SELECT * FROM `" . DB_CLIENTE . "`.`config_dashboard` WHERE id = 1";
    $resultado = $conexionDB->consulta($consulta);
    
    if (mysqli_num_rows($resultado) > 0) {
        $config = mysqli_fetch_array($resultado);
        echo "<div class='success'>✓ Tabla config_dashboard encontrada</div>";
        echo "<table>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        echo "<tr><td>async_enabled</td><td>" . ($config['async_enabled'] ? 'Habilitado (1)' : 'Deshabilitado (0)') . "</td></tr>";
        echo "<tr><td>cache_timeout</td><td>" . $config['cache_timeout'] . " segundos</td></tr>";
        echo "<tr><td>api_timeout</td><td>" . $config['api_timeout'] . " segundos</td></tr>";
        echo "<tr><td>created_at</td><td>" . $config['created_at'] . "</td></tr>";
        echo "<tr><td>updated_at</td><td>" . $config['updated_at'] . "</td></tr>";
        echo "</table>";
        
        $asyncEnabled = $config['async_enabled'];
    } else {
        echo "<div class='error'>✗ No se encontró configuración en config_dashboard</div>";
        $asyncEnabled = null;
    }
    mysqli_free_result($resultado);
} catch (Exception $e) {
    echo "<div class='error'>✗ Error accediendo a config_dashboard: " . $e->getMessage() . "</div>";
    $asyncEnabled = null;
}
echo "</div>";

// 2. Verificar permisos de usuario CME
echo "<div class='section'>";
echo "<h2>2. Verificación de Permisos CME</h2>";

$userDB = $_SESSION['idUser'];
$consulta = "SELECT * FROM `" . DB_CLIENTE . "`.adm_userDashBoad WHERE userId = '$userDB'";
$salida = $conexionDB->consulta($consulta);

if (mysqli_num_rows($salida) == 1) {
    $row = mysqli_fetch_array($salida);
    echo "<div class='success'>✓ Usuario encontrado en adm_userDashBoad</div>";
    echo "<table>";
    echo "<tr><th>Módulo</th><th>Estado</th></tr>";
    echo "<tr><td>pCircu</td><td>" . ($row['pCircu'] == 'S' ? '✓ Habilitado' : '✗ Deshabilitado') . "</td></tr>";
    echo "<tr><td>patComer</td><td>" . ($row['patComer'] == 'S' ? '✓ Habilitado' : '✗ Deshabilitado') . "</td></tr>";
    echo "<tr><td>ayudSocial</td><td>" . ($row['ayudSocial'] == 'S' ? '✓ Habilitado' : '✗ Deshabilitado') . "</td></tr>";
    echo "<tr><td>personal</td><td>" . ($row['personal'] == 'S' ? '✓ Habilitado' : '✗ Deshabilitado') . "</td></tr>";
    echo "<tr><td>remune</td><td>" . ($row['remune'] == 'S' ? '✓ Habilitado' : '✗ Deshabilitado') . "</td></tr>";
    echo "</table>";
    
    $pCircu = $row['pCircu'];
    $patComer = $row['patComer'];
    $ayudSocial = $row['ayudSocial'];
    $personal = $row['personal'];
    $remune = $row['remune'];
} else {
    echo "<div class='warning'>⚠ Usuario no encontrado, usando configuración por defecto</div>";
    $pCircu = 'S';
    $patComer = 'S';
    $ayudSocial = 'N';
    $personal = 'N';
    $remune = 'N';
}
mysqli_free_result($salida);
echo "</div>";

// 3. Verificar variables de API
echo "<div class='section'>";
echo "<h2>3. Verificación de Variables de API</h2>";
echo "<table>";
echo "<tr><th>API</th><th>URL</th><th>Estado</th></tr>";

$apis = [
    'AYUDSOC' => API_AYUDSOC,
    'PERSONAL' => API_PERSONAL,
    'PECIR' => API_PECIR,
    'PATCOM' => API_PATCOM
];

foreach ($apis as $nombre => $url) {
    if (defined("API_$nombre")) {
        echo "<tr><td>$nombre</td><td>$url</td><td>✓ Definida</td></tr>";
    } else {
        echo "<tr><td>$nombre</td><td>-</td><td>✗ No definida</td></tr>";
    }
}
echo "</table>";
echo "</div>";

// 4. Verificar archivos necesarios
echo "<div class='section'>";
echo "<h2>4. Verificación de Archivos</h2>";

$archivos = [
    '/var/www/html/dmuni/js/dashboard-async.js' => 'JavaScript asíncrono',
    '/var/www/html/dmuni/styles/dashboard-async.css' => 'CSS del dashboard',
    '/var/www/html/dmuni/appCme/ajax/ayudSocialData.php' => 'Endpoint Ayuda Social',
    '/var/www/html/dmuni/appCme/ajax/personalAusenteData.php' => 'Endpoint Personal',
    '/var/www/html/dmuni/appCme/ajax/pcirData.php' => 'Endpoint PCIR',
    '/var/www/html/dmuni/appCme/ajax/patentesData.php' => 'Endpoint Patentes',
    '/var/www/html/dmuni/appCme/index_cme_async.php' => 'CME Asíncrono',
];

echo "<table>";
echo "<tr><th>Archivo</th><th>Descripción</th><th>Estado</th></tr>";

foreach ($archivos as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        echo "<tr><td>$archivo</td><td>$descripcion</td><td>✓ Existe</td></tr>";
    } else {
        echo "<tr><td>$archivo</td><td>$descripcion</td><td>✗ No existe</td></tr>";
    }
}
echo "</table>";
echo "</div>";

// 5. Test de endpoints
echo "<div class='section'>";
echo "<h2>5. Test de Endpoints (si carga asíncrona está habilitada)</h2>";

if ($asyncEnabled) {
    $endpoints = [
        'Ayuda Social' => '/appCme/ajax/ayudSocialData.php',
        'Personal' => '/appCme/ajax/personalAusenteData.php',
        'PCIR' => '/appCme/ajax/pcirData.php',
        'Patentes' => '/appCme/ajax/patentesData.php'
    ];
    
    foreach ($endpoints as $nombre => $endpoint) {
        $fullUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $endpoint;
        echo "<p><strong>$nombre:</strong> <a href='$fullUrl' target='_blank'>$fullUrl</a></p>";
    }
} else {
    echo "<div class='info'>ℹ Carga asíncrona deshabilitada, endpoints no se utilizan</div>";
}
echo "</div>";

// 6. Configuración JavaScript esperada
echo "<div class='section'>";
echo "<h2>6. Configuración JavaScript</h2>";

$dashboardConfig = [
    'asyncEnabled' => $asyncEnabled == 1,
    'modules' => [
        'ayudSocial' => $ayudSocial == 'S',
        'personal' => $personal == 'S', 
        'pCircu' => $pCircu == 'S',
        'patComer' => $patComer == 'S'
    ],
    'endpoints' => [
        'ayudSocial' => '/appCme/ajax/ayudSocialData.php',
        'personal' => '/appCme/ajax/personalAusenteData.php',
        'pcir' => '/appCme/ajax/pcirData.php',
        'patentes' => '/appCme/ajax/patentesData.php'
    ]
];

echo "<pre>";
echo "window.dashboardConfig = " . json_encode($dashboardConfig, JSON_PRETTY_PRINT);
echo "</pre>";
echo "</div>";

// 7. Verificar cache
echo "<div class='section'>";
echo "<h2>7. Verificación de Cache</h2>";

$cacheDir = sys_get_temp_dir();
echo "<p><strong>Directorio de cache:</strong> $cacheDir</p>";

$cacheFiles = [
    'ayud_social_cache_*.json',
    'personal_ausente_cache_*.json', 
    'pcir_cache_*.json',
    'patentes_cache_*.json'
];

echo "<table>";
echo "<tr><th>Patrón</th><th>Archivos encontrados</th></tr>";

foreach ($cacheFiles as $pattern) {
    $files = glob($cacheDir . '/' . $pattern);
    $count = count($files);
    echo "<tr><td>$pattern</td><td>$count archivo(s)</td></tr>";
    
    if ($count > 0) {
        foreach ($files as $file) {
            $age = time() - filemtime($file);
            echo "<tr><td>└ " . basename($file) . "</td><td>Antigüedad: {$age}s</td></tr>";
        }
    }
}
echo "</table>";
echo "</div>";

echo "<div class='section info'>";
echo "<h2>Próximos Pasos</h2>";
echo "<ol>";
echo "<li>Si async_enabled = 1, los gráficos deberían cargarse vía AJAX</li>";
echo "<li>Si async_enabled = 0, los gráficos se cargan sincrónicamente</li>";
echo "<li>Verificar en la consola del navegador si hay errores JavaScript</li>";
echo "<li>Probar los endpoints individualmente haciendo clic en los enlaces</li>";
echo "<li>Si hay problemas, deshabilitar temporalmente async_enabled</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='/appUtilidades/configDashboard.php'>Ir a Configuración Dashboard</a> | ";
echo "<a href='/index_main.php'>Volver al Dashboard</a></p>";
?>