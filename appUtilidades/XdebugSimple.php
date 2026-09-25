<?php
// Debug simple para verificar configuración
echo "<h1>Debug Simple Dashboard</h1>";

// Verificar archivos
echo "<h2>Archivos JavaScript y CSS</h2>";
$files = [
    '/var/www/html/dmuni/js/dashboard-async.js',
    '/var/www/html/dmuni/styles/dashboard-async.css',
    '/var/www/html/dmuni/appCme/index_cme_async.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✓ $file EXISTS<br>";
    } else {
        echo "✗ $file MISSING<br>";
    }
}

echo "<h2>Verificar que el archivo esté incluido en index_main.php</h2>";
$indexMain = file_get_contents('/var/www/html/dmuni/index_main.php');
if (strpos($indexMain, 'index_cme_async.php') !== false) {
    echo "✓ index_main.php está usando index_cme_async.php<br>";
} else {
    echo "✗ index_main.php NO está usando index_cme_async.php<br>";
}

echo "<h2>Verificar que la plantilla tenga los scripts</h2>";
$template = file_get_contents('/var/www/html/dmuni/templates/index_main.tpl');
if (strpos($template, 'dashboard-async.js') !== false) {
    echo "✓ Plantilla incluye dashboard-async.js<br>";
} else {
    echo "✗ Plantilla NO incluye dashboard-async.js<br>";
}

if (strpos($template, 'dashboard-async.css') !== false) {
    echo "✓ Plantilla incluye dashboard-async.css<br>";
} else {
    echo "✗ Plantilla NO incluye dashboard-async.css<br>";
}

echo "<h2>Verificar IDs de contenedores en plantilla</h2>";
$ids = ['dashboard-ayudSocial', 'dashboard-personal', 'dashboard-pcir', 'dashboard-patentes'];
foreach ($ids as $id) {
    if (strpos($template, $id) !== false) {
        echo "✓ ID $id encontrado en plantilla<br>";
    } else {
        echo "✗ ID $id NO encontrado en plantilla<br>";
    }
}

echo "<br><p><strong>Si todos los elementos anteriores muestran ✓, el sistema debería funcionar.</strong></p>";
echo "<p>Si no hay gráficos, verificar:</p>";
echo "<ul>";
echo "<li>1. Consola del navegador para errores JavaScript</li>";
echo "<li>2. Red del navegador para ver si se hacen las llamadas AJAX</li>";
echo "<li>3. Que la configuración async_enabled esté en 1</li>";
echo "</ul>";

echo "<p><a href='debugDashboard.php'>Debug Completo</a> (requiere login)</p>";
?>