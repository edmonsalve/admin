<?php
/**
 * Debug dashboard sin sesión - Solo verificar configuración de BD
 */

echo "<h1>Debug Dashboard CME - Configuración BD</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .section { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px; }
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
    .warning { background-color: #fff3cd; color: #856404; }
    .info { background-color: #cce7ff; color: #004085; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

// Cargar configuraciones básicas
require_once('../defines/variables_path.php');
require_once(PATH_DEFINES . 'variables.php');
require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

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
        echo "<tr><th>Campo</th><th>Valor</th><th>Estado</th></tr>";
        echo "<tr><td>async_enabled</td><td>" . $config['async_enabled'] . "</td><td>" . ($config['async_enabled'] ? '<span style="color:green">✓ HABILITADO</span>' : '<span style="color:red">✗ DESHABILITADO</span>') . "</td></tr>";
        echo "<tr><td>cache_timeout</td><td>" . $config['cache_timeout'] . " segundos</td><td>" . round($config['cache_timeout']/60, 1) . " minutos</td></tr>";
        echo "<tr><td>api_timeout</td><td>" . $config['api_timeout'] . " segundos</td><td>-</td></tr>";
        echo "<tr><td>updated_at</td><td>" . $config['updated_at'] . "</td><td>-</td></tr>";
        echo "</table>";
        
        $asyncEnabled = $config['async_enabled'];
        
        if ($asyncEnabled) {
            echo "<div class='info'>ℹ <strong>Carga asíncrona HABILITADA</strong> - Los gráficos deberían cargarse vía AJAX</div>";
        } else {
            echo "<div class='warning'>⚠ <strong>Carga asíncrona DESHABILITADA</strong> - Los gráficos se cargan sincrónicamente</div>";
        }
        
    } else {
        echo "<div class='error'>✗ No se encontró configuración en config_dashboard</div>";
        echo "<p>Ejecuta este SQL para crear la configuración:</p>";
        echo "<pre>INSERT INTO `" . DB_CLIENTE . "`.`config_dashboard` (id, async_enabled, cache_timeout, api_timeout) VALUES (1, 1, 3600, 8);</pre>";
        $asyncEnabled = null;
    }
    mysqli_free_result($resultado);
} catch (Exception $e) {
    echo "<div class='error'>✗ Error accediendo a config_dashboard: " . $e->getMessage() . "</div>";
    $asyncEnabled = null;
}
echo "</div>";

// 2. Verificar variables de API
echo "<div class='section'>";
echo "<h2>2. Variables de API Configuradas</h2>";
echo "<table>";
echo "<tr><th>API</th><th>URL</th><th>Estado</th></tr>";

$apis = [
    'AYUDSOC' => defined('API_AYUDSOC') ? API_AYUDSOC : 'NO DEFINIDA',
    'PERSONAL' => defined('API_PERSONAL') ? API_PERSONAL : 'NO DEFINIDA',
    'PECIR' => defined('API_PECIR') ? API_PECIR : 'NO DEFINIDA',
    'PATCOM' => defined('API_PATCOM') ? API_PATCOM : 'NO DEFINIDA'
];

foreach ($apis as $nombre => $url) {
    $status = ($url != 'NO DEFINIDA') ? '✓ Definida' : '✗ No definida';
    echo "<tr><td>$nombre</td><td>$url</td><td>$status</td></tr>";
}
echo "</table>";
echo "</div>";

// 3. Test rápido de conectividad (solo si async está habilitado)
if ($asyncEnabled) {
    echo "<div class='section'>";
    echo "<h2>3. Test de Endpoints AJAX</h2>";
    
    $endpoints = [
        'Test' => '/appCme/ajax/test.php',
        'Ayuda Social' => '/appCme/ajax/ayudSocialData.php',
        'Personal' => '/appCme/ajax/personalAusenteData.php',
        'PCIR' => '/appCme/ajax/pcirData.php',
        'Patentes' => '/appCme/ajax/patentesData.php'
    ];
    
    echo "<table>";
    echo "<tr><th>Endpoint</th><th>URL</th><th>Acción</th></tr>";
    
    foreach ($endpoints as $nombre => $endpoint) {
        $fullUrl = "https://dev.dcode.cl" . $endpoint;
        echo "<tr><td>$nombre</td><td>$endpoint</td><td><a href='$fullUrl' target='_blank'>Probar</a></td></tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Instrucciones:</strong></p>";
    echo "<ol>";
    echo "<li>Haz clic en 'Probar' para cada endpoint</li>";
    echo "<li>Deberías ver JSON válido en cada uno</li>";
    echo "<li>Si alguno da error, ese es el problema</li>";
    echo "</ol>";
    echo "</div>";
}

// 4. Verificación de JavaScript
echo "<div class='section'>";
echo "<h2>4. Verificación JavaScript</h2>";
echo "<p>Para verificar si JavaScript está funcionando correctamente:</p>";
echo "<ol>";
echo "<li>Ve a tu página principal de dMuni</li>";
echo "<li>Abre las Herramientas de Desarrollador (F12)</li>";
echo "<li>Ve a la pestaña 'Consola'</li>";
echo "<li>Busca estos mensajes:</li>";
echo "</ol>";

echo "<table>";
echo "<tr><th>Mensaje esperado</th><th>Significado</th></tr>";
echo "<tr><td><code>DOMContentLoaded ejecutado</code></td><td>El sistema se está inicializando</td></tr>";
echo "<tr><td><code>dashboardConfig: {...}</code></td><td>La configuración se cargó correctamente</td></tr>";

if ($asyncEnabled) {
    echo "<tr><td><code>Modo asíncrono - los gráficos se cargarán vía AJAX</code></td><td>Carga asíncrona activada</td></tr>";
    echo "<tr><td><code>dashboard-async.js DOMContentLoaded ejecutado</code></td><td>Script asíncrono funcionando</td></tr>";
    echo "<tr><td><code>Haciendo request a: /appCme/ajax/...</code></td><td>Las peticiones AJAX se están haciendo</td></tr>";
} else {
    echo "<tr><td><code>Modo síncrono - creando gráficos con datos PHP</code></td><td>Carga síncrona activada</td></tr>";
    echo "<tr><td><code>Creando gráfico de ...</code></td><td>Los gráficos se están creando</td></tr>";
}

echo "</table>";
echo "</div>";

// 5. Acciones recomendadas
echo "<div class='section'>";
echo "<h2>5. Próximos Pasos</h2>";

if ($asyncEnabled === null) {
    echo "<div class='error'>";
    echo "<h3>PROBLEMA: Configuración no encontrada</h3>";
    echo "<p>Ejecuta este comando SQL en tu base de datos cliente:</p>";
    echo "<pre>INSERT INTO `" . DB_CLIENTE . "`.`config_dashboard` (id, async_enabled, cache_timeout, api_timeout) VALUES (1, 1, 3600, 8);</pre>";
    echo "</div>";
} elseif ($asyncEnabled) {
    echo "<div class='info'>";
    echo "<h3>Carga Asíncrona Habilitada</h3>";
    echo "<ol>";
    echo "<li>Ve a tu dashboard principal y abre la consola del navegador (F12)</li>";
    echo "<li>Verifica que NO hay errores JavaScript en rojo</li>";
    echo "<li>Busca los mensajes listados arriba</li>";
    echo "<li>Si no ves los mensajes o hay errores, comparte la captura de pantalla de la consola</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div class='warning'>";
    echo "<h3>Carga Síncrona (Tradicional)</h3>";
    echo "<p>Los gráficos deberían aparecer inmediatamente usando el método tradicional.</p>";
    echo "<p>Si quieres habilitar la carga asíncrona para mejor rendimiento:</p>";
    echo "<pre>UPDATE `" . DB_CLIENTE . "`.`config_dashboard` SET async_enabled = 1 WHERE id = 1;</pre>";
    echo "</div>";
}

echo "</div>";

echo "<hr>";
echo "<p><strong>URLs útiles:</strong></p>";
echo "<ul>";
echo "<li><a href='/index_main.php' target='_blank'>Ir al Dashboard Principal</a></li>";
echo "<li><a href='/appUtilidades/configDashboard.php' target='_blank'>Panel de Configuración</a> (requiere login)</li>";
echo "<li><a href='debugSimple.php' target='_blank'>Debug Simple</a></li>";
echo "</ul>";
?>