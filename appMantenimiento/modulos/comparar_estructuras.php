<?php
if (isset($_GET['mod'])) { $idModuloIco = $_GET['mod']; } else { $idModuloIco = 0; }
require_once('../includes/initSistema.php');

/*
 * Herramienta de diagnóstico: sólo ejecuta consultas sobre INFORMATION_SCHEMA.
 * La conexión reutiliza la configuración y los túneles SSH de adm_servidores.
 */
$DB_DCODE = 'adm_dCode';
$conexionDB = new DB_MySQLi;
$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);
$pdoMaestra = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=latin1', DB_USER, DB_PASSWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));

$html = static function ($valor): string { return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$esHostValido = static function (string $host): bool { return filter_var($host, FILTER_VALIDATE_IP) !== false || (bool) preg_match('/^[A-Za-z0-9.-]+$/', $host); };
$valorComparable = static function ($valor): string { return $valor === null ? 'NULL' : (string) $valor; };
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
$conectarServidor = static function (array $servidor) use ($abrirTunelSSH, $cerrarTunelSSH, $esHostValido): array {
    $hostSQL = trim((string) (($servidor['ipLocal'] ?? '') ?: ($servidor['ipPublica'] ?? '')));
    $puertoSQL = (int) ($servidor['puertoSQL'] ?: 3306);
    $usuarioSQL = (string) ($servidor['usrSQL'] ?? '');
    $claveSQL = (string) ($servidor['passSQL'] ?? '');
    if ($hostSQL === '' || !$esHostValido($hostSQL) || $usuarioSQL === '' || $puertoSQL < 1 || $puertoSQL > 65535) { throw new RuntimeException('El servidor seleccionado no tiene datos SQL suficientes o válidos.'); }
    $procesoTunel = null; $tubosTunel = array(); $archivoKnownHosts = '';
    if (($servidor['tunelSSH'] ?? 'N') === 'S') {
        $destinoTunelSQL = trim((string) (($servidor['ipLocal'] ?? '') ?: '127.0.0.1'));
        list($procesoTunel, $tubosTunel, $archivoKnownHosts, $puertoLocal) = $abrirTunelSSH($servidor, $destinoTunelSQL, $puertoSQL);
        try {
            $pdo = new PDO("mysql:host=127.0.0.1;port=$puertoLocal;charset=utf8mb4", $usuarioSQL, $claveSQL, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false));
        } catch (PDOException $e) {
            $cerrarTunelSSH($procesoTunel, $tubosTunel, $archivoKnownHosts);
            throw new RuntimeException('El túnel SSH fue establecido, pero las credenciales SQL configuradas para este servidor fueron rechazadas.');
        }
        return array($pdo, 'Conectado mediante túnel SSH.', $procesoTunel, $tubosTunel, $archivoKnownHosts);
    }
    $pdo = new PDO("mysql:host=$hostSQL;port=$puertoSQL;charset=utf8mb4", $usuarioSQL, $claveSQL, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false));
    return array($pdo, "Conectado a $hostSQL:$puertoSQL.", $procesoTunel, $tubosTunel, $archivoKnownHosts);
};

$servidores = array();
$consultaServidores = "SELECT s.idServidor, s.nombreServidor, s.ipLocal, s.ipPublica, s.prefijoBD, s.puertoSQL, s.usrSQL, s.passSQL, s.tunelSSH, s.usrSSH, s.puertoSSH, s.llavePrivada, c.cliente FROM `$DB_DCODE`.adm_servidores s LEFT JOIN `$DB_DCODE`.adm_clientes c ON c.idCliente = s.idCliente ORDER BY c.cliente, s.nombreServidor";
foreach ($pdoMaestra->query($consultaServidores) as $servidor) { $servidores[(string) $servidor['idServidor']] = $servidor; }

$idServidorBase = (string) ($_POST['idServidorBase'] ?? '');
$idServidorHV = (string) ($_POST['idServidorHV'] ?? '');
$buscarPredeterminado = static function (array $lista, string $texto): string {
    foreach ($lista as $id => $servidor) { if (stripos((string) ($servidor['nombreServidor'] ?? ''), $texto) !== false) { return (string) $id; } }
    return '';
};
if ($idServidorBase === '') { $idServidorBase = $buscarPredeterminado($servidores, 'T450'); }
if ($idServidorHV === '') { $idServidorHV = $buscarPredeterminado($servidores, 'HV'); }
if ($idServidorHV === '') { $idServidorHV = $buscarPredeterminado($servidores, 'HierroViejo'); }
$prefijoBase = trim((string) ($_POST['prefijoBase'] ?? (($servidores[$idServidorBase]['prefijoBD'] ?? ''))));
$prefijoHV = trim((string) ($_POST['prefijoHV'] ?? (($servidores[$idServidorHV]['prefijoBD'] ?? ''))));
$baseSeleccionada = trim((string) ($_POST['baseSeleccionada'] ?? ''));
$accion = (string) ($_POST['accion'] ?? '');
$mensajeError = ''; $estadoConexion = ''; $resultadoComparacion = ''; $detalleServidores = ''; $basesDisponibles = array();
$estadosConexionServidores = array(
    'base' => array('tipo' => 'pendiente', 'mensaje' => 'Pendiente de comprobación.'),
    'analizado' => array('tipo' => 'pendiente', 'mensaje' => 'Pendiente de comprobación.'),
);
$resumenConexion = static function (array $servidor, string $rol, array $estado) use ($html): string {
    $nombre = trim((string) ($servidor['nombreServidor'] ?? 'Sin nombre'));
    $host = trim((string) (($servidor['ipLocal'] ?? '') ?: ($servidor['ipPublica'] ?? 'Sin configurar')));
    $puerto = (int) ($servidor['puertoSQL'] ?: 3306);
    $usuario = trim((string) ($servidor['usrSQL'] ?? 'Sin configurar'));
    $tunel = (($servidor['tunelSSH'] ?? 'N') === 'S') ? 'Sí' : 'No';
    $clave = trim((string) ($servidor['passSQL'] ?? '')) === '' ? 'No configurada' : 'Configurada (oculta)';
    return '<div class="comparacion__credencial"><strong>' . $html($rol) . ':</strong> ' . $html($nombre) . '<br><span>SQL: ' . $html($host) . ':' . $html($puerto) . ' · usuario: ' . $html($usuario) . ' · contraseña: ' . $html($clave) . ' · túnel SSH: ' . $html($tunel) . '</span><p class="comparacion__estado comparacion__estado--' . $html($estado['tipo'] ?? 'pendiente') . '"><strong>Conexión:</strong> ' . $html($estado['mensaje'] ?? 'Pendiente de comprobación.') . '</p></div>';
};
$actualizarDetalleServidores = static function () use (&$detalleServidores, $servidores, $idServidorBase, $idServidorHV, &$estadosConexionServidores, $resumenConexion): void {
    $detalleServidores = '';
    if (isset($servidores[$idServidorBase])) { $detalleServidores .= $resumenConexion($servidores[$idServidorBase], 'Servidor base', $estadosConexionServidores['base']); }
    if (isset($servidores[$idServidorHV])) { $detalleServidores .= $resumenConexion($servidores[$idServidorHV], 'Servidor analizado', $estadosConexionServidores['analizado']); }
};
$actualizarDetalleServidores();

$leerBases = static function (PDO $conexion, string $prefijo): array {
    $bases = array();
    foreach ($conexion->query('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME') as $fila) {
        $base = (string) $fila['SCHEMA_NAME'];
        /* Algunos servidores guardan el prefijo sin el guion bajo separador (p. ej. v1hij). */
        if (str_starts_with($base, $prefijo)) { $bases[ltrim(substr($base, strlen($prefijo)), '_')] = $base; }
    }
    ksort($bases, SORT_NATURAL | SORT_FLAG_CASE);
    return $bases;
};
$leerTablas = static function (PDO $conexion, string $base): array {
    $consulta = $conexion->prepare('SELECT TABLE_NAME, TABLE_TYPE, ENGINE, TABLE_COLLATION, CREATE_OPTIONS, TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME');
    $consulta->execute(array($base));
    return $consulta->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
};
$leerCampos = static function (PDO $conexion, string $base): array {
    $consulta = $conexion->prepare('SELECT TABLE_NAME, COLUMN_NAME, ORDINAL_POSITION, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, CHARACTER_SET_NAME, COLLATION_NAME, COLUMN_KEY, EXTRA, COLUMN_COMMENT, GENERATION_EXPRESSION FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, ORDINAL_POSITION');
    $consulta->execute(array($base));
    $campos = array();
    foreach ($consulta as $fila) { $campos[$fila['TABLE_NAME']][$fila['COLUMN_NAME']] = $fila; }
    return $campos;
};
$leerIndices = static function (PDO $conexion, string $base): array {
    $consulta = $conexion->prepare('SELECT TABLE_NAME, INDEX_NAME, NON_UNIQUE, SEQ_IN_INDEX, COLUMN_NAME, COLLATION, INDEX_TYPE, SUB_PART FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX');
    $consulta->execute(array($base));
    $indices = array();
    foreach ($consulta as $fila) {
        $tabla = $fila['TABLE_NAME']; $indice = $fila['INDEX_NAME'];
        if (!isset($indices[$tabla][$indice])) { $indices[$tabla][$indice] = array('NON_UNIQUE' => $fila['NON_UNIQUE'], 'INDEX_TYPE' => $fila['INDEX_TYPE'], 'COLUMNAS' => array()); }
        $indices[$tabla][$indice]['COLUMNAS'][] = $fila['COLUMN_NAME'] . ($fila['SUB_PART'] !== null ? '(' . $fila['SUB_PART'] . ')' : '') . ($fila['COLLATION'] === 'D' ? ' DESC' : '');
    }
    foreach ($indices as &$porTabla) { foreach ($porTabla as &$indice) { $indice = ($indice['NON_UNIQUE'] ? 'No único' : 'Único') . ' · ' . $indice['INDEX_TYPE'] . ' · ' . implode(', ', $indice['COLUMNAS']); } } unset($porTabla, $indice);
    return $indices;
};
$leerDefinicionesIndices = static function (PDO $conexion, string $base): array {
    $consulta = $conexion->prepare('SELECT TABLE_NAME, INDEX_NAME, NON_UNIQUE, SEQ_IN_INDEX, COLUMN_NAME, COLLATION, INDEX_TYPE, SUB_PART FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX');
    $consulta->execute(array($base));
    $indices = array();
    foreach ($consulta as $fila) {
        $tabla = $fila['TABLE_NAME']; $indice = $fila['INDEX_NAME'];
        if (!isset($indices[$tabla][$indice])) { $indices[$tabla][$indice] = array('unico' => !(bool) $fila['NON_UNIQUE'], 'tipo' => strtoupper((string) $fila['INDEX_TYPE']), 'columnas' => array()); }
        $indices[$tabla][$indice]['columnas'][] = array('nombre' => $fila['COLUMN_NAME'], 'subParte' => $fila['SUB_PART'], 'orden' => $fila['COLLATION']);
    }
    return $indices;
};
$definicionCampoSQL = static function (array $campo) use ($identificadorSQL): string {
    $escaparTexto = static function ($texto): string { return "'" . str_replace("'", "''", (string) $texto) . "'"; };
    $definicion = $identificadorSQL((string) $campo['COLUMN_NAME']) . ' ' . $campo['COLUMN_TYPE'];
    if (($campo['GENERATION_EXPRESSION'] ?? '') !== '') {
        $definicion .= ' GENERATED ALWAYS AS (' . $campo['GENERATION_EXPRESSION'] . ')' . (stripos((string) ($campo['EXTRA'] ?? ''), 'STORED') !== false ? ' STORED' : ' VIRTUAL');
    } else {
        if (($campo['CHARACTER_SET_NAME'] ?? null) !== null) { $definicion .= ' CHARACTER SET ' . $campo['CHARACTER_SET_NAME']; }
        if (($campo['COLLATION_NAME'] ?? null) !== null) { $definicion .= ' COLLATE ' . $campo['COLLATION_NAME']; }
        $definicion .= ($campo['IS_NULLABLE'] ?? 'YES') === 'NO' ? ' NOT NULL' : ' NULL';
        if (($campo['COLUMN_DEFAULT'] ?? null) !== null) {
            $valor = (string) $campo['COLUMN_DEFAULT'];
            /* MariaDB puede devolver defaults de ENUM/SET ya como literal: 'I'. */
            $esExpresion = preg_match('/^(CURRENT_TIMESTAMP(?:\\([0-9]+\\))?|NULL)$/i', $valor);
            $esLiteralSQL = strlen($valor) >= 2 && $valor[0] === "'" && substr($valor, -1) === "'";
            $definicion .= ' DEFAULT ' . (($esExpresion || $esLiteralSQL) ? $valor : $escaparTexto($valor));
        } elseif (($campo['IS_NULLABLE'] ?? 'YES') === 'YES') {
            $definicion .= ' DEFAULT NULL';
        }
        $extra = trim(str_ireplace('DEFAULT_GENERATED', '', (string) ($campo['EXTRA'] ?? '')));
        if ($extra !== '') { $definicion .= ' ' . $extra; }
    }
    if (($campo['COLUMN_COMMENT'] ?? '') !== '') { $definicion .= ' COMMENT ' . $escaparTexto($campo['COLUMN_COMMENT']); }
    return $definicion;
};
$definicionIndiceSQL = static function (string $base, string $tabla, string $nombreIndice, array $indice) use ($identificadorSQL): string {
    $columnas = array();
    foreach ($indice['columnas'] as $columna) { $columnas[] = $identificadorSQL((string) $columna['nombre']) . ($columna['subParte'] !== null ? '(' . (int) $columna['subParte'] . ')' : '') . (($columna['orden'] ?? '') === 'D' ? ' DESC' : ''); }
    if ($nombreIndice === 'PRIMARY') { $tipo = 'PRIMARY KEY'; }
    elseif (($indice['tipo'] ?? '') === 'FULLTEXT') { $tipo = 'FULLTEXT INDEX ' . $identificadorSQL($nombreIndice); }
    elseif (($indice['tipo'] ?? '') === 'SPATIAL') { $tipo = 'SPATIAL INDEX ' . $identificadorSQL($nombreIndice); }
    else { $tipo = (($indice['unico'] ?? false) ? 'UNIQUE INDEX ' : 'INDEX ') . $identificadorSQL($nombreIndice) . (($indice['tipo'] ?? '') !== '' ? ' USING ' . $indice['tipo'] : ''); }
    return 'ALTER TABLE ' . $identificadorSQL($base) . '.' . $identificadorSQL($tabla) . ' ADD ' . $tipo . ' (' . implode(', ', $columnas) . ');';
};
$diferenciasCampos = static function (array $base, array $destino) use ($valorComparable): array {
    $etiquetas = array('COLUMN_TYPE' => 'tipo/tamaño', 'IS_NULLABLE' => 'nulo', 'COLUMN_DEFAULT' => 'predeterminado', 'CHARACTER_SET_NAME' => 'charset', 'COLLATION_NAME' => 'collation', 'COLUMN_KEY' => 'clave', 'EXTRA' => 'extra', 'GENERATION_EXPRESSION' => 'generación', 'ORDINAL_POSITION' => 'posición', 'COLUMN_COMMENT' => 'comentario');
    $diferencias = array();
    foreach ($etiquetas as $campo => $etiqueta) { if ($valorComparable($base[$campo] ?? null) !== $valorComparable($destino[$campo] ?? null)) { $diferencias[$etiqueta] = array($valorComparable($base[$campo] ?? null), $valorComparable($destino[$campo] ?? null)); } }
    return $diferencias;
};

if (in_array($accion, array('cargar_bases', 'comparar', 'generar_scripts'), true)) {
    $tunelBaseListado = array();
    try {
        if (!isset($servidores[$idServidorBase])) { throw new RuntimeException('Seleccione un servidor base válido.'); }
        if (!preg_match('/^[A-Za-z0-9_]{1,50}$/', $prefijoBase)) { throw new RuntimeException('El prefijo del servidor base sólo puede contener letras, números y guion bajo.'); }
        list($pdoListado, $estadoListado, $procesoListado, $tubosListado, $knownHostsListado) = $conectarServidor($servidores[$idServidorBase]);
        $tunelBaseListado = array($procesoListado, $tubosListado, $knownHostsListado);
        $estadosConexionServidores['base'] = array('tipo' => 'ok', 'mensaje' => $estadoListado);
        $basesDisponibles = $leerBases($pdoListado, $prefijoBase);
        if (!$basesDisponibles) { throw new RuntimeException('No se encontraron bases con el prefijo ' . $prefijoBase . ' en el servidor base.'); }
        if ($accion === 'cargar_bases') { $estadoConexion = $estadoListado . ' Se encontraron ' . count($basesDisponibles) . ' base(s) disponibles para seleccionar.'; }
    } catch (Throwable $e) {
        $estadosConexionServidores['base'] = array('tipo' => 'error', 'mensaje' => $e->getMessage());
        $mensajeError = 'No fue posible cargar las bases del servidor base (' . trim((string) ($servidores[$idServidorBase]['nombreServidor'] ?? 'sin seleccionar')) . '): ' . $e->getMessage();
    } finally {
        if ($tunelBaseListado) { $cerrarTunelSSH($tunelBaseListado[0], $tunelBaseListado[1], $tunelBaseListado[2]); }
    }
}

if (in_array($accion, array('comparar', 'generar_scripts'), true) && $mensajeError === '') {
    $tuneles = array();
    try {
        if (!isset($servidores[$idServidorBase], $servidores[$idServidorHV])) { throw new RuntimeException('Seleccione los dos servidores a comparar.'); }
        if ($idServidorBase === $idServidorHV) { throw new RuntimeException('El servidor base y el servidor analizado deben ser distintos.'); }
        if (!preg_match('/^[A-Za-z0-9_]{1,50}$/', $prefijoBase) || !preg_match('/^[A-Za-z0-9_]{1,50}$/', $prefijoHV)) { throw new RuntimeException('Los prefijos sólo pueden contener letras, números y guion bajo.'); }
        $erroresConexion = array();
        try {
            list($pdoBase, $estadoBase, $procesoBase, $tubosBase, $knownHostsBase) = $conectarServidor($servidores[$idServidorBase]);
            $tuneles[] = array($procesoBase, $tubosBase, $knownHostsBase);
            $estadosConexionServidores['base'] = array('tipo' => 'ok', 'mensaje' => $estadoBase);
        } catch (Throwable $e) {
            $estadosConexionServidores['base'] = array('tipo' => 'error', 'mensaje' => $e->getMessage());
            $erroresConexion[] = 'Servidor base (' . trim((string) $servidores[$idServidorBase]['nombreServidor']) . '): ' . $e->getMessage();
        }
        try {
            list($pdoHV, $estadoHV, $procesoHV, $tubosHV, $knownHostsHV) = $conectarServidor($servidores[$idServidorHV]);
            $tuneles[] = array($procesoHV, $tubosHV, $knownHostsHV);
            $estadosConexionServidores['analizado'] = array('tipo' => 'ok', 'mensaje' => $estadoHV);
        } catch (Throwable $e) {
            $estadosConexionServidores['analizado'] = array('tipo' => 'error', 'mensaje' => $e->getMessage());
            $erroresConexion[] = 'Servidor analizado (' . trim((string) $servidores[$idServidorHV]['nombreServidor']) . '): ' . $e->getMessage();
        }
        if ($erroresConexion) { throw new RuntimeException('No se pudo iniciar la comparación. ' . implode(' | ', $erroresConexion)); }
        $basesBase = $leerBases($pdoBase, $prefijoBase);
        $basesHV = $leerBases($pdoHV, $prefijoHV);
        if ($baseSeleccionada === '' || !in_array($baseSeleccionada, $basesBase, true)) { throw new RuntimeException('Seleccione una base válida de la lista del servidor base.'); }
        $sufijoSeleccionado = ltrim(substr($baseSeleccionada, strlen($prefijoBase)), '_');
        $basesBase = array($sufijoSeleccionado => $baseSeleccionada);
        $basesHV = isset($basesHV[$sufijoSeleccionado]) ? array($sufijoSeleccionado => $basesHV[$sufijoSeleccionado]) : array();
        $soloBase = array_diff_key($basesBase, $basesHV);
        $soloHV = array_diff_key($basesHV, $basesBase);
        $basesComunes = array_intersect_key($basesBase, $basesHV);
        $analisisBases = array();
        $scriptsAgregarCampos = array(); $scriptsEliminarCampos = array(); $scriptsAgregarIndices = array();
        foreach ($basesComunes as $sufijo => $baseOrigen) {
            $baseDestino = $basesHV[$sufijo];
            $tablasBase = $leerTablas($pdoBase, $baseOrigen); $tablasHV = $leerTablas($pdoHV, $baseDestino);
            $camposBase = $leerCampos($pdoBase, $baseOrigen); $camposHV = $leerCampos($pdoHV, $baseDestino);
            $indicesBase = $leerIndices($pdoBase, $baseOrigen); $indicesHV = $leerIndices($pdoHV, $baseDestino);
            $indicesDefinicionesBase = $accion === 'generar_scripts' ? $leerDefinicionesIndices($pdoBase, $baseOrigen) : array();
            $tablasSoloBase = array_diff_key($tablasBase, $tablasHV); $tablasSoloHV = array_diff_key($tablasHV, $tablasBase);
            $tablas = array();
            foreach (array_intersect_key($tablasBase, $tablasHV) as $tabla => $estructuraBase) {
                $atributos = array();
                foreach (array('TABLE_TYPE' => 'tipo', 'ENGINE' => 'motor', 'TABLE_COLLATION' => 'collation', 'CREATE_OPTIONS' => 'opciones', 'TABLE_COMMENT' => 'comentario') as $campo => $etiqueta) { if ($valorComparable($estructuraBase[$campo] ?? null) !== $valorComparable($tablasHV[$tabla][$campo] ?? null)) { $atributos[$etiqueta] = array($valorComparable($estructuraBase[$campo] ?? null), $valorComparable($tablasHV[$tabla][$campo] ?? null)); } }
                $soloCamposBase = array_diff_key($camposBase[$tabla] ?? array(), $camposHV[$tabla] ?? array());
                $soloCamposHV = array_diff_key($camposHV[$tabla] ?? array(), $camposBase[$tabla] ?? array());
                $camposDiferentes = array();
                foreach (array_intersect_key($camposBase[$tabla] ?? array(), $camposHV[$tabla] ?? array()) as $campo => $definicionBase) { $diferencia = $diferenciasCampos($definicionBase, $camposHV[$tabla][$campo]); if ($diferencia) { $camposDiferentes[$campo] = $diferencia; } }
                $indicesSoloBase = array_diff_key($indicesBase[$tabla] ?? array(), $indicesHV[$tabla] ?? array());
                $indicesSoloHV = array_diff_key($indicesHV[$tabla] ?? array(), $indicesBase[$tabla] ?? array());
                $indicesDiferentes = array();
                foreach (array_intersect_key($indicesBase[$tabla] ?? array(), $indicesHV[$tabla] ?? array()) as $indice => $firmaBase) { if ($firmaBase !== $indicesHV[$tabla][$indice]) { $indicesDiferentes[$indice] = array($firmaBase, $indicesHV[$tabla][$indice]); } }
                if ($accion === 'generar_scripts') {
                    /*
                     * Se recorren todos los campos de origen en su orden físico. Así, si
                     * faltan varios consecutivos, el segundo queda AFTER del primero que
                     * acaba de agregarse mediante la sentencia anterior del script.
                     */
                    $campoAnterior = null;
                    foreach (($camposBase[$tabla] ?? array()) as $nombreCampo => $campo) {
                        if (isset($soloCamposBase[$nombreCampo])) {
                            $posicion = $campoAnterior === null ? ' FIRST' : ' AFTER ' . $identificadorSQL((string) $campoAnterior);
                            $scriptsAgregarCampos[] = 'ALTER TABLE ' . $identificadorSQL($baseDestino) . '.' . $identificadorSQL($tabla) . ' ADD COLUMN ' . $definicionCampoSQL($campo) . $posicion . ';';
                        }
                        $campoAnterior = $nombreCampo;
                    }
                    foreach ($soloCamposHV as $campo) { $scriptsEliminarCampos[] = 'ALTER TABLE ' . $identificadorSQL($baseDestino) . '.' . $identificadorSQL($tabla) . ' DROP COLUMN ' . $identificadorSQL((string) $campo['COLUMN_NAME']) . ';'; }
                    foreach (array_keys($indicesSoloBase) as $nombreIndice) {
                        if (isset($indicesDefinicionesBase[$tabla][$nombreIndice])) { $scriptsAgregarIndices[] = $definicionIndiceSQL($baseDestino, $tabla, $nombreIndice, $indicesDefinicionesBase[$tabla][$nombreIndice]); }
                    }
                }
                if ($atributos || $soloCamposBase || $soloCamposHV || $camposDiferentes || $indicesSoloBase || $indicesSoloHV || $indicesDiferentes) { $tablas[$tabla] = compact('atributos', 'soloCamposBase', 'soloCamposHV', 'camposDiferentes', 'indicesSoloBase', 'indicesSoloHV', 'indicesDiferentes'); }
            }
            if ($tablasSoloBase || $tablasSoloHV || $tablas) { $analisisBases[$sufijo] = compact('baseOrigen', 'baseDestino', 'tablasSoloBase', 'tablasSoloHV', 'tablas'); }
        }
        /* El estado de cada conexión se informa en las tarjetas de los servidores. */
        $estadoConexion = '';

        $lista = static function (array $elementos, string $vacio) use ($html): string {
            if (!$elementos) { return '<p class="comparacion__ok">' . $html($vacio) . '</p>'; }
            $filas = ''; foreach ($elementos as $nombre => $detalle) { $filas .= '<tr><td>' . $html(is_string($nombre) ? $nombre : $detalle) . '</td><td>' . $html(is_array($detalle) ? (($detalle['TABLE_TYPE'] ?? '') . ' ' . ($detalle['ENGINE'] ?? '')) : '') . '</td></tr>'; }
            return '<div class="comparacion__tabla-wrap"><table class="tablaComparacion"><thead><tr><th>Nombre</th><th>Detalle</th></tr></thead><tbody>' . $filas . '</tbody></table></div>';
        };
        $totalTablas = 0; $totalCampos = 0; $totalIndices = 0;
        $detalleBases = '';
        foreach ($analisisBases as $sufijo => $analisis) {
            $totalTablas += count($analisis['tablasSoloBase']) + count($analisis['tablasSoloHV']);
            $detalleTablas = '<h5>Tablas faltantes en servidor analizado (' . count($analisis['tablasSoloBase']) . ')</h5>' . $lista($analisis['tablasSoloBase'], 'No hay tablas faltantes.')
                . '<h5>Tablas sobrantes en servidor analizado (' . count($analisis['tablasSoloHV']) . ')</h5>' . $lista($analisis['tablasSoloHV'], 'No hay tablas sobrantes.');
            foreach ($analisis['tablas'] as $tabla => $detalle) {
                $totalTablas++;
                $filasCampos = '';
                $totalCampos += count($detalle['soloCamposBase']) + count($detalle['soloCamposHV']);
                foreach ($detalle['camposDiferentes'] as $campo => $diferencias) { $totalCampos++; foreach ($diferencias as $atributo => $valores) { $filasCampos .= '<tr><td>' . $html($campo) . '</td><td>' . $html($atributo) . '</td><td>' . $html($valores[0]) . '</td><td>' . $html($valores[1]) . '</td></tr>'; } }
                $filasAtributos = ''; foreach ($detalle['atributos'] as $atributo => $valores) { $filasAtributos .= '<tr><td>' . $html($atributo) . '</td><td>' . $html($valores[0]) . '</td><td>' . $html($valores[1]) . '</td></tr>'; }
                $detalleCampo = '<h5>Tabla ' . $html($tabla) . '</h5>'
                    . ($filasAtributos ? '<div class="comparacion__tabla-wrap"><table class="tablaComparacion"><thead><tr><th>Propiedad</th><th>Base</th><th>Analizado</th></tr></thead><tbody>' . $filasAtributos . '</tbody></table></div>' : '')
                    . '<p><strong>Campos faltantes en servidor analizado:</strong> ' . $html(implode(', ', array_keys($detalle['soloCamposBase'])) ?: 'ninguno') . '<br><strong>Campos sobrantes en servidor analizado:</strong> ' . $html(implode(', ', array_keys($detalle['soloCamposHV'])) ?: 'ninguno') . '</p>'
                    . ($filasCampos ? '<div class="comparacion__tabla-wrap"><table class="tablaComparacion"><thead><tr><th>Campo</th><th>Diferencia</th><th>Base</th><th>Analizado</th></tr></thead><tbody>' . $filasCampos . '</tbody></table></div>' : '')
                    . '<p><strong>Índices faltantes en servidor analizado:</strong> ' . $html(implode(', ', array_keys($detalle['indicesSoloBase'])) ?: 'ninguno') . '<br><strong>Índices sobrantes en servidor analizado:</strong> ' . $html(implode(', ', array_keys($detalle['indicesSoloHV'])) ?: 'ninguno') . '</p>';
                $totalIndices += count($detalle['indicesSoloBase']) + count($detalle['indicesSoloHV']);
                foreach ($detalle['indicesDiferentes'] as $indice => $firma) { $totalIndices++; $detalleCampo .= '<p class="comparacion__aviso"><strong>Índice distinto ' . $html($indice) . ':</strong> Base: ' . $html($firma[0]) . ' / Analizado: ' . $html($firma[1]) . '</p>'; }
                $detalleTablas .= '<details class="comparacion__detalle"><summary>' . $html($tabla) . ' — ' . (count($detalle['soloCamposBase']) + count($detalle['soloCamposHV']) + count($detalle['camposDiferentes'])) . ' diferencia(s) de campo</summary>' . $detalleCampo . '</details>';
            }
            $detalleBases .= '<details class="comparacion__base" open><summary>' . $html($analisis['baseOrigen']) . ' ↔ ' . $html($analisis['baseDestino']) . '</summary>' . $detalleTablas . '</details>';
        }
        $camposOcultosScripts = '<input type="hidden" name="idServidorBase" value="' . $html($idServidorBase) . '"><input type="hidden" name="idServidorHV" value="' . $html($idServidorHV) . '"><input type="hidden" name="prefijoBase" value="' . $html($prefijoBase) . '"><input type="hidden" name="prefijoHV" value="' . $html($prefijoHV) . '"><input type="hidden" name="baseSeleccionada" value="' . $html($baseSeleccionada) . '">';
        $accionScripts = $basesComunes && $accion !== 'generar_scripts' ? '<form method="post" action="comparar_estructuras.php" class="comparacion__acciones-script">' . $camposOcultosScripts . '<button class="boton boton--guardar" type="submit" name="accion" value="generar_scripts">Generar scripts SQL manuales</button></form>' : '';
        $scriptGenerado = '';
        if ($accion === 'generar_scripts') {
            $seccionesScript = array();
            if ($scriptsAgregarCampos) { $seccionesScript[] = '-- Campos faltantes en el servidor remoto' . "\n" . implode("\n", $scriptsAgregarCampos); }
            if ($scriptsEliminarCampos) { $seccionesScript[] = '-- Campos sobrantes en el servidor remoto: revisar respaldo antes de ejecutar' . "\n" . implode("\n", $scriptsEliminarCampos); }
            if ($scriptsAgregarIndices) { $seccionesScript[] = '-- Índices faltantes en el servidor remoto' . "\n" . implode("\n", $scriptsAgregarIndices); }
            $sql = $seccionesScript ? '-- Script generado para revisión manual. No fue ejecutado por el sistema.' . "\n-- Base destino: " . ($basesHV ? reset($basesHV) : 'no disponible') . "\n\n" . implode("\n\n", $seccionesScript) : '-- No hay campos ni índices faltantes/sobrantes para generar scripts en la base seleccionada.';
            $scriptGenerado = '<section class="comparacion__script"><h4>Script SQL para ejecución manual</h4><p>Revise el contenido, especialmente los <code>DROP COLUMN</code>, y ejecútelo manualmente en el servidor remoto. Esta pantalla no ejecuta sentencias SQL.</p><textarea readonly spellcheck="false" aria-label="Script SQL generado">' . $html($sql) . '</textarea></section>';
        }
        $resultadoComparacion = '<div class="comparacion__resumen"><strong>Tablas con diferencias:</strong> ' . $totalTablas . ' · <strong>Diferencias de campos:</strong> ' . $totalCampos . ' · <strong>Índices distintos:</strong> ' . $totalIndices . '</div>'
            . ($soloBase ? '<section class="comparacion__bloque"><h4>Base no encontrada en servidor analizado</h4>' . $lista($soloBase, '') . '</section>' : '')
            . ($detalleBases ?: ($soloBase ? '' : '<p class="comparacion__ok comparacion__ok--grande">Las bases coincidentes no presentan diferencias de tablas, campos, propiedades ni índices.</p>'))
            . $accionScripts . $scriptGenerado;
    } catch (RuntimeException $e) {
        $mensajeError = $e->getMessage();
    } catch (Throwable $e) {
        $mensajeError = 'No fue posible comparar las estructuras. Verifique los permisos SQL, la conectividad y los prefijos indicados.';
    } finally {
        foreach ($tuneles as $tunel) { $cerrarTunelSSH($tunel[0], $tunel[1], $tunel[2]); }
    }
}

$actualizarDetalleServidores();

$opcionesBase = '<option value="">Seleccione un servidor</option>'; $opcionesHV = '<option value="">Seleccione un servidor</option>';
foreach ($servidores as $id => $servidor) {
    $nombre = trim(($servidor['cliente'] ?? '') . ' — ' . ($servidor['nombreServidor'] ?? ''));
    $opcion = '<option value="' . $html($id) . '" data-prefijo="' . $html(trim((string) ($servidor['prefijoBD'] ?? ''))) . '"';
    $opcionesBase .= $opcion . ((string) $id === $idServidorBase ? ' selected' : '') . '>' . $html($nombre) . '</option>';
    $opcionesHV .= $opcion . ((string) $id === $idServidorHV ? ' selected' : '') . '>' . $html($nombre) . '</option>';
}
$opcionesBases = '<option value="">Primero cargue las bases del servidor base</option>';
if ($basesDisponibles) {
    $opcionesBases = '<option value="">Seleccione una base</option>';
    foreach ($basesDisponibles as $base) { $opcionesBases .= '<option value="' . $html($base) . '"' . ($base === $baseSeleccionada ? ' selected' : '') . '>' . $html($base) . '</option>'; }
}
$contenido = new plantilla('comparar_estructuras');
$contenido->asigna_variables(array('lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR, 'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu, 'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'opcionesBase' => $opcionesBase, 'opcionesHV' => $opcionesHV, 'opcionesBases' => $opcionesBases, 'prefijoBase' => $html($prefijoBase), 'prefijoHV' => $html($prefijoHV), 'mensajeError' => $html($mensajeError), 'estadoConexion' => $html($estadoConexion), 'detalleServidores' => $detalleServidores, 'resultadoComparacion' => $resultadoComparacion));
echo $contenido->muestra();
?>
