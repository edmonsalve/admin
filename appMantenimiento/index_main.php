<?php
session_start();

if (!isset($_SESSION['idUser'])) {
    header('Location: /index.php');
    exit;
}

$_SESSION['idsistema'] = 950;
$_SESSION['areasistema'] = 'A';

$tipoUsuario = strtoupper(trim((string) ($_SESSION['tipo'] ?? '')));
$modulos = array(
    'clientes' => array('url' => '/appMantenimiento/modulos/clientes.php', 'icono' => 'fa-building', 'titulo' => 'Clientes', 'detalle' => 'Contratos, sistemas contratados y licencias.'),
    'facturas' => array('url' => '/appMantenimiento/modulos/facturas.php', 'icono' => 'fa-file-invoice-dollar', 'titulo' => 'Facturas', 'detalle' => 'Consulta y seguimiento de facturación.'),
    'cobranzas' => array('url' => '/appMantenimiento/modulos/cobranzas.php', 'icono' => 'fa-hand-holding-dollar', 'titulo' => 'Cobranzas', 'detalle' => 'Facturas, compromisos y seguimiento de cobro.'),
    'usuarios' => array('url' => '/appMantenimiento/modulos/usuarios.php', 'icono' => 'fa-users-gear', 'titulo' => 'Usuarios', 'detalle' => 'Accesos de administración.'),
    'sistemas' => array('url' => '/appMantenimiento/modulos/sistemas.php', 'icono' => 'fa-diagram-project', 'titulo' => 'Sistemas', 'detalle' => 'Gestión de sistemas y sus módulos.'),
    'servidores' => array('url' => '/appMantenimiento/modulos/servidores.php', 'icono' => 'fa-server', 'titulo' => 'Servidores', 'detalle' => 'Datos de conexión y acceso de clientes.'),
    'sincronizar' => array('url' => '/appMantenimiento/modulos/sincronizar.php', 'icono' => 'fa-arrows-rotate', 'titulo' => 'Sincronización', 'detalle' => 'Comparación de módulos con cada cliente.'),
    'comparar_estructuras' => array('url' => '/appMantenimiento/modulos/comparar_estructuras.php', 'icono' => 'fa-code-compare', 'titulo' => 'Comparar estructuras', 'detalle' => 'Diferencias de bases de datos entre servidores.'),
    'bitacora_desarrollo' => array('url' => '/appMantenimiento/modulos/bitacora_desarrollo.php', 'icono' => 'fa-book-journal-whills', 'titulo' => 'Bitácora de desarrollo', 'detalle' => 'Historial de mejoras, correcciones y cambios del proyecto municipal.'),
);
$modulosPorPerfil = array(
    'D' => array('clientes', 'facturas', 'cobranzas', 'usuarios', 'sistemas', 'servidores', 'sincronizar', 'comparar_estructuras', 'bitacora_desarrollo'),
    'T' => array('sistemas', 'servidores', 'sincronizar', 'comparar_estructuras', 'bitacora_desarrollo'),
    'A' => array('clientes', 'facturas', 'cobranzas'),
);
$modulosHtml = '';
foreach ($modulosPorPerfil[$tipoUsuario] ?? array() as $idModulo) {
    $modulo = $modulos[$idModulo];
    $modulosHtml .= '<a class="modern-module-card" href="' . $modulo['url'] . '"><div><i class="fa-solid ' . $modulo['icono'] . '" aria-hidden="true"></i><span>' . $modulo['titulo'] . '</span><small>' . $modulo['detalle'] . '</small></div></a>';
}

require_once(__DIR__ . '/../defines/variables_path.php');
require_once(PATH_DEFINES . 'variables.php');
require_once(__DIR__ . '/includes/variablesSistema.php');
require_once(PATH_CLASSES_SISTEMA . 'Class.Plantilla.php');

$contenido = new plantilla('index_main');
$contenido->asigna_variables(array(
    'titulo' => 'dCode Administración',
    'icono' => PATH_ICO . ICONO_NAVEGADOR,
    'usuario' => htmlspecialchars($_SESSION['nomUsuario'] ?? $_SESSION['idUser'], ENT_QUOTES, 'UTF-8'),
    'modulos' => $modulosHtml,
));

echo $contenido->muestra();
?>
