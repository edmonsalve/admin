<?php
require_once('../includes/initSistema.php');
require_once(__DIR__ . '/../../includes/crypt.php');

$idCliente = $_GET['idCliente'] ?? '';
if (!ctype_digit((string) $idCliente)) {
    header('Location: clientes.php');
    exit;
}

$DB_DCODE = 'adm_dCode';
$conexionDB = new DB_MySQLi;
$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);
$salida = $conexionDB->consulta("SELECT * FROM `$DB_DCODE`.adm_clientes WHERE idCliente = " . (int) $idCliente . ' LIMIT 1');
$cliente = mysqli_fetch_assoc($salida);
if (!$cliente) {
    header('Location: clientes.php');
    exit;
}

// Algoritmo histórico: se mantiene para que las licencias generadas sean compatibles.
$nombreCliente = (string) ($cliente['cliente'] ?? '');
$hashCliente = md5($nombreCliente);
$licencia = '';
$auxiliar = 0;
for ($i = strlen($hashCliente); $i > 0; $i--) {
    if ($auxiliar < 26) {
        $licencia .= strtoupper(substr($hashCliente, $i, 1));
    }
    if (($auxiliar % 5) === 0 && $auxiliar > 0 && $auxiliar < 25) {
        $licencia .= '-';
    }
    $auxiliar++;
}

$estado = (string) ($cliente['estadoLic'] ?? '');
$tipo = (string) ($cliente['tipoLic'] ?? '');
$datosLicencia = array(
    'Cliente' => $nombreCliente,
    'RUT' => (string) ($cliente['rut'] ?? ''),
    'Licencia' => $licencia,
    'Sistemas contratados' => (string) ($cliente['modulos'] ?? ''),
    'Estado / tipo' => $estado . '/' . $tipo,
    'Vencimiento' => (string) ($cliente['vencimientoLic'] ?? '')
);

$encriptador = new EnDecryptText();
$html = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$filasLicencia = '';
foreach ($datosLicencia as $etiqueta => $valor) {
    $cifrado = $encriptador->Encrypt_Text($valor);
    $filasLicencia .= '<section class="licencia-dato"><h4>' . $html($etiqueta) . '</h4><p class="licencia-dato__valor">' . $html($valor) . '</p><textarea readonly rows="3">' . $html($cifrado) . '</textarea></section>';
}

$contenido = new plantilla('licencia_calculo');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'),
    'icono' => PATH_ICO . ICONO_NAVEGADOR,
    'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'),
    'H2Titulo' => 'Generación de licencia',
    'idCliente' => $html($idCliente),
    'cliente' => $html($nombreCliente), 'rut' => $html($datosLicencia['RUT']),
    'licencia' => $html($licencia), 'filasLicencia' => $filasLicencia
));
echo $contenido->muestra();
?>
