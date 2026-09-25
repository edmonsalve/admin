<?php
if (isset($_GET['mod'])) { $idModuloIco = $_GET['mod']; } else { $idModuloIco = 0; }
require_once('../includes/initSistema.php');

$DB_DCODE = 'adm_dCode';
$conexionDB = new DB_MySQLi;
$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);
$pdoMaestra = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=latin1', DB_USER, DB_PASSWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));

$html = static function ($valor): string { return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$esHostValido = static function (string $host): bool { return filter_var($host, FILTER_VALIDATE_IP) !== false || (bool) preg_match('/^[A-Za-z0-9.-]+$/', $host); };
$identificadorSQL = static function (string $nombre): string { return '`' . str_replace('`', '``', $nombre) . '`'; };

$abrirTunelSSH = static function (array $servidor, string $destinoSQL, int $puertoSQL) use ($esHostValido): array {
    $directorioLlaves = '/var/www/html/admin/llavePriv';
    $llave = trim((string) ($servidor['llavePrivada'] ?? ''));
    $hostSSH = trim((string) (($servidor['ipPublica'] ?? '') ?: ($servidor['ipLocal'] ?? '')));
    $usuarioSSH = trim((string) ($servidor['usrSSH'] ?? ''));
    $puertoSSH = (int) ($servidor['puertoSSH'] ?: 22);
    if ($llave === '' || !preg_match('/^[A-Za-z0-9._-]{1,50}$/', $llave)) { throw new RuntimeException('El servidor no tiene una llave privada SSH válida.'); }
    $baseLlaves = realpath($directorioLlaves);
    $rutaLlave = $baseLlaves === false ? false : realpath($baseLlaves . DIRECTORY_SEPARATOR . $llave);
    if ($rutaLlave === false || !str_starts_with($rutaLlave, $baseLlaves . DIRECTORY_SEPARATOR) || !is_file($rutaLlave) || !is_readable($rutaLlave)) { throw new RuntimeException('La llave privada configurada no está disponible para el servidor web.'); }
    if ($hostSSH === '' || !$esHostValido($hostSSH) || !$esHostValido($destinoSQL) || !preg_match('/^[A-Za-z0-9._-]{1,50}$/', $usuarioSSH) || $puertoSSH < 1 || $puertoSSH > 65535) { throw new RuntimeException('Los datos de conexión SSH del servidor no son válidos.'); }

    $puertoLocal = random_int(40000, 55000);
    $archivoKnownHosts = tempnam(sys_get_temp_dir(), 'dcode_ssh_');
    if ($archivoKnownHosts === false) { throw new RuntimeException('No fue posible preparar la conexión SSH.'); }
    $comando = array('/usr/bin/ssh', '-i', $rutaLlave, '-o', 'BatchMode=yes', '-o', 'IdentitiesOnly=yes', '-o', 'ConnectTimeout=10', '-o', 'ExitOnForwardFailure=yes', '-o', 'StrictHostKeyChecking=accept-new', '-o', 'UserKnownHostsFile=' . $archivoKnownHosts, '-N', '-L', '127.0.0.1:' . $puertoLocal . ':' . $destinoSQL . ':' . $puertoSQL, '-p', (string) $puertoSSH, $usuarioSSH . '@' . $hostSSH);
    $proceso = proc_open($comando, array(0 => array('file', '/dev/null', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $tubos, null, null, array('bypass_shell' => true));
    if (!is_resource($proceso)) { @unlink($archivoKnownHosts); throw new RuntimeException('No fue posible iniciar el túnel SSH.'); }
    stream_set_blocking($tubos[1], false); stream_set_blocking($tubos[2], false);
    $listo = false; $limite = microtime(true) + 12;
    do {
        $socket = @fsockopen('127.0.0.1', $puertoLocal, $codigoError, $detalleError, 0.2);
        if ($socket !== false) { fclose($socket); $listo = true; break; }
        $estado = proc_get_status($proceso);
        if (!$estado['running']) { break; }
        usleep(100000);
    } while (microtime(true) < $limite);
    if (!$listo) {
        foreach ($tubos as $tubo) { if (is_resource($tubo)) { fclose($tubo); } }
        @proc_terminate($proceso); @proc_close($proceso); @unlink($archivoKnownHosts);
        throw new RuntimeException('No fue posible establecer el túnel SSH con la llave privada configurada.');
    }
    return array($proceso, $tubos, $archivoKnownHosts, $puertoLocal);
};
$cerrarTunelSSH = static function ($proceso, array $tubos, string $archivoKnownHosts): void {
    foreach ($tubos as $tubo) { if (is_resource($tubo)) { fclose($tubo); } }
    if (is_resource($proceso)) { $estado = proc_get_status($proceso); if ($estado['running']) { @proc_terminate($proceso); } @proc_close($proceso); }
    if ($archivoKnownHosts !== '') { @unlink($archivoKnownHosts); }
};

$servidores = array();
$consultaServidores = "SELECT s.idServidor, s.nombreServidor, s.ipLocal, s.ipPublica, s.prefijoBD, s.puertoSQL, s.usrSQL, s.passSQL, s.tunelSSH, s.usrSSH, s.puertoSSH, s.llavePrivada, c.cliente FROM `$DB_DCODE`.adm_servidores s LEFT JOIN `$DB_DCODE`.adm_clientes c ON c.idCliente = s.idCliente WHERE TRIM(COALESCE(s.prefijoBD, '')) <> '' ORDER BY c.cliente, s.nombreServidor";
$salidaServidores = $conexionDB->consulta($consultaServidores);
while ($servidor = mysqli_fetch_assoc($salidaServidores)) { $servidores[(string) $servidor['idServidor']] = $servidor; }

$idServidor = (string) ($_POST['idServidor'] ?? $_GET['idServidor'] ?? '');
$accion = (string) ($_POST['accion'] ?? (isset($_POST['comparar']) ? 'comparar' : ''));
$mensajeError = '';
$mensajeExito = '';
$resultadoComparacion = '';
$estadoConexion = '';

$leerCampos = static function (PDO $conexion, string $base) use ($identificadorSQL): array {
    $campos = array();
    foreach ($conexion->query('SHOW COLUMNS FROM ' . $identificadorSQL($base) . '.`adm_modulos`') as $fila) { $campos[] = $fila['Field']; }
    return $campos;
};
$conectarCliente = static function (array $servidor, string $baseCliente) use ($abrirTunelSSH, $conexionDB): array {
    $hostCliente = trim((string) ($servidor['ipLocal'] ?: $servidor['ipPublica']));
    $puertoCliente = (int) ($servidor['puertoSQL'] ?: 3306);
    $usuarioCliente = (string) $servidor['usrSQL'];
    $claveCliente = (string) $servidor['passSQL'];
    if ($hostCliente === '' || $usuarioCliente === '' || $puertoCliente < 1 || $puertoCliente > 65535) { throw new RuntimeException('El servidor seleccionado no tiene datos SQL suficientes.'); }
    $procesoTunel = null; $tubosTunel = array(); $archivoKnownHosts = '';
    if (($servidor['tunelSSH'] ?? 'N') === 'S') {
        $destinoTunelSQL = trim((string) ($servidor['ipLocal'] ?: '127.0.0.1'));
        $puertoDestinoTunel = $servidor['ipLocal'] ? $puertoCliente : 3306;
        list($procesoTunel, $tubosTunel, $archivoKnownHosts, $puertoLocal) = $abrirTunelSSH($servidor, $destinoTunelSQL, $puertoDestinoTunel);
        try {
            $pdoCliente = new PDO("mysql:host=127.0.0.1;port=$puertoLocal;dbname=$baseCliente;charset=latin1", $usuarioCliente, $claveCliente, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new RuntimeException('El túnel SSH fue establecido, pero las credenciales SQL configuradas para este servidor fueron rechazadas.');
        }
        return array($pdoCliente, 'Conectado mediante túnel SSH a ' . $baseCliente . '.', $procesoTunel, $tubosTunel, $archivoKnownHosts);
    }
    try {
        $pdoCliente = new PDO("mysql:host=$hostCliente;port=$puertoCliente;dbname=$baseCliente;charset=latin1", $usuarioCliente, $claveCliente, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
        return array($pdoCliente, "Conectado a $hostCliente:$puertoCliente ($baseCliente).", $procesoTunel, $tubosTunel, $archivoKnownHosts);
    } catch (PDOException $errorConexion) {
        $baseEscapada = $conexionDB->escapaDatos($baseCliente);
        $salidaBaseLocal = $conexionDB->consulta("SHOW DATABASES LIKE '$baseEscapada'");
        if (!$salidaBaseLocal || mysqli_num_rows($salidaBaseLocal) === 0) { throw $errorConexion; }
        $pdoCliente = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . $baseCliente . ';charset=latin1', DB_USER, DB_PASSWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
        return array($pdoCliente, 'Usando la réplica local de ' . $baseCliente . '; la conexión configurada no estuvo disponible.', $procesoTunel, $tubosTunel, $archivoKnownHosts);
    }
};

if ($accion !== '') {
    if (!isset($servidores[$idServidor])) {
        $mensajeError = 'Seleccione un servidor válido.';
    } elseif (!in_array($accion, array('comparar', 'exportar', 'importar', 'eliminar'), true)) {
        $mensajeError = 'La acción solicitada no es válida.';
    } elseif ($accion !== 'comparar' && empty($_SESSION['save'])) {
        $mensajeError = 'No dispone de permisos para modificar módulos.';
    } else {
        $servidor = $servidores[$idServidor];
        $prefijo = trim((string) $servidor['prefijoBD']);
        if (!preg_match('/^[A-Za-z0-9_]+$/', $prefijo)) {
            $mensajeError = 'El prefijo de base de datos del servidor no es válido.';
        } else {
            $baseCliente = $prefijo . '@admin';
            $procesoTunel = null; $tubosTunel = array(); $archivoKnownHosts = '';
            try {
                list($pdoCliente, $estadoConexion, $procesoTunel, $tubosTunel, $archivoKnownHosts) = $conectarCliente($servidor, $baseCliente);
                $camposMaestros = $leerCampos($pdoMaestra, $DB_DCODE);
                $camposCliente = $leerCampos($pdoCliente, $baseCliente);
                $camposComunes = array_values(array_intersect($camposMaestros, $camposCliente));
                if (!in_array('id', $camposComunes, true)) { throw new RuntimeException('Las tablas adm_modulos no tienen un campo id compatible.'); }
                $camposSQL = implode(', ', array_map($identificadorSQL, $camposComunes));
                $modulosMaestros = $pdoMaestra->query("SELECT $camposSQL FROM `$DB_DCODE`.`adm_modulos` ORDER BY `id`")->fetchAll();
                $modulosCliente = $pdoCliente->query("SELECT $camposSQL FROM `$baseCliente`.`adm_modulos` ORDER BY `id`")->fetchAll();
                $maestroPorId = array(); foreach ($modulosMaestros as $modulo) { $maestroPorId[(string) $modulo['id']] = $modulo; }
                $clientePorId = array(); foreach ($modulosCliente as $modulo) { $clientePorId[(string) $modulo['id']] = $modulo; }
                $faltantesCliente = array_diff_key($maestroPorId, $clientePorId);
                $soloCliente = array_diff_key($clientePorId, $maestroPorId);

                $idsSeleccionados = static function ($origen): array {
                    $ids = is_array($origen) ? $origen : array();
                    $ids = array_filter($ids, static fn($id) => ctype_digit((string) $id));
                    return array_values(array_unique(array_map('strval', $ids)));
                };
                if ($accion === 'exportar') {
                    $ids = $idsSeleccionados($_POST['faltantes'] ?? array());
                    $seleccionados = array_intersect_key($faltantesCliente, array_flip($ids));
                    if (!$seleccionados) { throw new RuntimeException('Seleccione al menos un módulo faltante para exportar al cliente.'); }
                    $marcadores = implode(', ', array_fill(0, count($camposComunes), '?'));
                    $insertar = $pdoCliente->prepare("INSERT INTO `$baseCliente`.`adm_modulos` ($camposSQL) VALUES ($marcadores)");
                    $pdoCliente->beginTransaction();
                    foreach ($seleccionados as $modulo) { $insertar->execute(array_map(static fn($campo) => $modulo[$campo], $camposComunes)); }
                    $pdoCliente->commit();
                    $mensajeExito = count($seleccionados) . ' módulo(s) exportado(s) a ' . $baseCliente . '.';
                } elseif ($accion === 'importar') {
                    $ids = $idsSeleccionados($_POST['exclusivos'] ?? array());
                    $seleccionados = array_intersect_key($soloCliente, array_flip($ids));
                    if (!$seleccionados) { throw new RuntimeException('Seleccione al menos un módulo exclusivo para importar a la tabla maestra.'); }
                    $marcadores = implode(', ', array_fill(0, count($camposComunes), '?'));
                    $insertar = $pdoMaestra->prepare("INSERT INTO `$DB_DCODE`.`adm_modulos` ($camposSQL) VALUES ($marcadores)");
                    $pdoMaestra->beginTransaction();
                    foreach ($seleccionados as $modulo) { $insertar->execute(array_map(static fn($campo) => $modulo[$campo], $camposComunes)); }
                    $pdoMaestra->commit();
                    $mensajeExito = count($seleccionados) . ' módulo(s) importado(s) desde ' . $baseCliente . '.';
                } elseif ($accion === 'eliminar') {
                    $ids = $idsSeleccionados($_POST['exclusivos'] ?? array());
                    $seleccionados = array_intersect_key($soloCliente, array_flip($ids));
                    if (!$seleccionados) { throw new RuntimeException('Seleccione al menos un módulo exclusivo para eliminar del cliente.'); }
                    $marcadores = implode(', ', array_fill(0, count($seleccionados), '?'));
                    $eliminar = $pdoCliente->prepare("DELETE FROM `$baseCliente`.`adm_modulos` WHERE `id` IN ($marcadores)");
                    $eliminar->execute(array_map('intval', array_keys($seleccionados)));
                    $mensajeExito = count($seleccionados) . ' módulo(s) exclusivo(s) eliminado(s) de ' . $baseCliente . '.';
                }

                if ($accion !== 'comparar') {
                    $modulosMaestros = $pdoMaestra->query("SELECT $camposSQL FROM `$DB_DCODE`.`adm_modulos` ORDER BY `id`")->fetchAll();
                    $modulosCliente = $pdoCliente->query("SELECT $camposSQL FROM `$baseCliente`.`adm_modulos` ORDER BY `id`")->fetchAll();
                    $maestroPorId = array(); foreach ($modulosMaestros as $modulo) { $maestroPorId[(string) $modulo['id']] = $modulo; }
                    $clientePorId = array(); foreach ($modulosCliente as $modulo) { $clientePorId[(string) $modulo['id']] = $modulo; }
                    $faltantesCliente = array_diff_key($maestroPorId, $clientePorId);
                    $soloCliente = array_diff_key($clientePorId, $maestroPorId);
                }
                $camposVista = array_values(array_intersect(array('id', 'idModulo', 'idsistema', 'modulo', 'tipo', 'php', 'estado'), $camposComunes));
                $crearTabla = static function (array $modulos, string $vacio, string $nombreSeleccion) use ($html, $camposVista): string {
                    if (count($modulos) === 0) { return '<p style="margin:10px 0; color:#147a2d;">' . $html($vacio) . '</p>'; }
                    $titulos = array('id' => 'ID', 'idModulo' => 'ID módulo', 'idsistema' => 'Sistema', 'modulo' => 'Módulo', 'tipo' => 'Tipo', 'php' => 'Archivo PHP', 'estado' => 'Estado');
                    $encabezados = '<th><input type="checkbox" aria-label="Seleccionar todos" onclick="seleccionarModulos(\'' . $nombreSeleccion . '\', this.checked)"></th>';
                    foreach ($camposVista as $campo) { $encabezados .= '<th>' . $html($titulos[$campo] ?? $campo) . '</th>'; }
                    $filas = '';
                    foreach ($modulos as $modulo) {
                        $filas .= '<tr><td><input type="checkbox" name="' . $nombreSeleccion . '[]" value="' . $html($modulo['id']) . '"></td>';
                        foreach ($camposVista as $campo) { $filas .= '<td>' . $html($modulo[$campo] ?? '') . '</td>'; }
                        $filas .= '</tr>';
                    }
                    return '<div style="overflow-x:auto;"><table class="tablaComparacion"><thead><tr>' . $encabezados . '</tr></thead><tbody>' . $filas . '</tbody></table></div>';
                };
                $resultadoComparacion = '<form method="post" action="sincronizar.php" class="comparacion__acciones"><input type="hidden" name="idServidor" value="' . $html($idServidor) . '">'
                    . '<div class="comparacion__resumen"><strong>Base maestra:</strong> adm_dCode.adm_modulos (' . count($maestroPorId) . ' registros) &nbsp; <strong>Base cliente:</strong> ' . $html($baseCliente) . '.adm_modulos (' . count($clientePorId) . ' registros)</div>'
                    . '<h4 class="comparacion__titulo comparacion__titulo--faltantes">Faltantes en el cliente (' . count($faltantesCliente) . ')</h4>'
                    . $crearTabla($faltantesCliente, 'El cliente contiene todos los módulos de la tabla maestra.', 'faltantes')
                    . (count($faltantesCliente) ? '<div class="filaGeneral" style="margin:10px 0;"><button class="boton boton--guardar" type="submit" name="accion" value="exportar" onclick="return confirm(\'¿Exportar los módulos seleccionados al cliente?\')">Exportar seleccionados al cliente</button></div>' : '')
                    . '<h4 class="comparacion__titulo comparacion__titulo--exclusivos">Solo en el cliente (' . count($soloCliente) . ')</h4>'
                    . $crearTabla($soloCliente, 'No existen módulos exclusivos en el cliente.', 'exclusivos')
                    . (count($soloCliente) ? '<div class="filaGeneral" style="margin:10px 0; gap:8px;"><button class="boton boton--guardar" type="submit" name="accion" value="importar" onclick="return confirm(\'¿Importar los módulos seleccionados a la tabla maestra?\')">Importar seleccionados a la maestra</button><button class="boton boton--salir" type="submit" name="accion" value="eliminar" onclick="return confirm(\'¿Eliminar definitivamente del cliente los módulos seleccionados?\')">Eliminar seleccionados del cliente</button></div>' : '')
                    . '</form>';
            } catch (RuntimeException $e) {
                if ($pdoMaestra->inTransaction()) { $pdoMaestra->rollBack(); }
                if (isset($pdoCliente) && $pdoCliente->inTransaction()) { $pdoCliente->rollBack(); }
                $mensajeError = $e->getMessage();
            } catch (Throwable $e) {
                if ($pdoMaestra->inTransaction()) { $pdoMaestra->rollBack(); }
                if (isset($pdoCliente) && $pdoCliente->inTransaction()) { $pdoCliente->rollBack(); }
                $mensajeError = 'No fue posible completar la operación. Verifique la conexión SQL y la estructura de adm_modulos.';
            } finally {
                $cerrarTunelSSH($procesoTunel, $tubosTunel, $archivoKnownHosts);
            }
        }
    }
}

$opcionesServidores = '<option value="">Seleccione un servidor</option>';
foreach ($servidores as $id => $servidor) {
    $seleccionado = $id === $idServidor ? ' selected' : '';
    $nombre = trim(($servidor['cliente'] ?? '') . ' — ' . ($servidor['nombreServidor'] ?? ''));
    $opcionesServidores .= '<option value="' . $html($id) . '"' . $seleccionado . '>' . $html($nombre) . '</option>';
}

$contenido = new plantilla('sincronizar');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR, 'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'H2Titulo' => 'Comparar módulos de clientes', 'opcionesServidores' => $opcionesServidores,
    'mensajeError' => $html($mensajeError), 'mensajeExito' => $html($mensajeExito), 'estadoConexion' => $html($estadoConexion), 'resultadoComparacion' => $resultadoComparacion,
));
echo $contenido->muestra();
?>
